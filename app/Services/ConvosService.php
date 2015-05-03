<?php
namespace App\Services;

use App\Model\ConvosException;
use App\Repositories\ConvosRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Mockery\CountValidator\Exception;

class ConvosService implements ConvosServiceInterface
{
    function __construct(ConvosRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Creates a new conversation. Required data
     *
     * subject, string, max 140
     * recipient, integer
     * body, string
     *
     * @param $userId
     * @param array $data
     * @return mixed
     * @throws ConvosException
     */
    public function createConversation($userId, array $data)
    {
        $data['user_id'] = $userId;

        $this->_validate($data, [
            'user_id' => 'required|integer|min:1',
            'subject' => 'required|max:140',
            'recipient' => 'required|integer|min:1|different:user_id', // recipient must be different
            'body' => 'required'
        ]);

        // Create a new conversation
        $convo = $this->repository->createConversation($data['user_id'], $data['subject']);

        // Add participants
        $this->repository->addConversationParticipants($convo, $data['user_id'], array($data['recipient']));

        // Add message
        $this->repository->addConversationMessage($convo, $data['user_id'], $data['body']);

        $convo->messages;
        $convo->participants;

        return $convo;
    }

    /**
     * Validation helper
     *
     * @param $data
     * @param array $rules
     * @throws ConvosException
     */
    private function _validate($data, array $rules)
    {
        $v = Validator::make($data, $rules);
        if ($v->fails()) {
            throw new ConvosException($v->errors());
        }
    }

    /**
     * Adds a new message to a conversation
     *
     * @param $convoId
     * @param $userId
     * @param array $data
     * @return mixed
     * @throws ConvosException
     */
    public function addConversationMessage($convoId, $userId, array $data)
    {
        $data['conversation_id'] = $convoId;
        $data['user_id'] = $userId;

        $this->_validate($data, [
            'conversation_id' => 'required|integer|min:1',
            'user_id' => 'required|integer|min:1',
            'body' => 'required'
        ]);

        // get convo
        $convo = $this->repository->getConversation($convoId, $data['user_id']);

        // add message an return message details
        return $this->repository->addConversationMessage($convo, $data['user_id'], $data['body']);
    }


    /**
     * Returns a conversation details
     *
     * @param $convoId
     * @param $userId
     * @return mixed
     * @throws ConvosException
     */
    public function getConversation($convoId, $userId)
    {
        $this->_validate(
            ['userId' => $userId, 'convoId' => $convoId],
            [
                'convoId' => 'required|integer|min:1',
                'userId' => 'required|integer|min:1'
            ]
        );

        return $this->repository->getConversation($convoId, $userId);
    }

    /**
     * List of messages in a conversation
     *
     * @param $convoId
     * @param $userId
     * @param int $limit
     * @param int $page
     * @param null $until
     * @return mixed
     * @throws ConvosException
     */
    public function getConversationMessages($convoId, $userId, $limit = 25, $page = 1, $until = null)
    {
        $this->_validate(
            ['userId' => $userId, 'convoId' => $convoId],
            [
                'convoId' => 'required|integer|min:1',
                'userId' => 'required|integer|min:1'
            ]
        );

        $pagination = $this->_parsePaginationParams($limit, $page, $until);

        // get the convo... this will check the existence of the conversation
        $convo = $this->repository->getConversation($convoId, $userId);

        return $this->repository->getConversationMessages($convoId, $userId, $pagination);
    }

    /**
     * Helper to parse pagination parameters
     *
     * @param int $limit
     * @param int $page
     * @param null $until
     * @return array
     */
    private function _parsePaginationParams($limit = 25, $page = 1, $until = null)
    {
        // limit is default 25 / max 100
        $intLimit = $this->_tryParseInt($limit, 25);
        if ($intLimit > 100) $intLimit = 100;

        // page is > 0
        $intPage = $this->_tryParseInt($page, 1);

        $untilDate = null;
        if (!is_null($until)) {
            try {
                $untilDate = Carbon::parse($until);
            } catch (Exception $ex) {
                $untilDate = null;
            }
        }

        return [
            'limit' => $intLimit,
            'page' => $intPage,
            'until' => $untilDate
        ];
    }

    /**
     * Tries to parse and int and return the default on failure
     *
     * @param $val
     * @param $default
     * @return int|null
     */
    private function _tryParseInt($val, $default)
    {
        $intValue = ctype_digit((string)$val) ? intval($val) : null;
        if ($intValue === null) {
            $intValue = $default;
        }
        return $intValue;
    }

    /**
     * Returns a list of conversations
     *
     * @param $userId
     * @param int $limit
     * @param int $page
     * @param null $until
     * @return mixed
     * @throws ConvosException
     */
    public function getConversations($userId, $limit = 25, $page = 1, $until = null)
    {
        $this->_validate(
            ['userId' => $userId],
            ['userId' => 'required|integer|min:1']);
        $pagination = $this->_parsePaginationParams($limit, $page, $until);
        return $this->repository->getConversations($userId, $pagination);
    }

    /**
     * Deletes a message from a conversation
     *
     * @param $convoId
     * @param $userId
     * @param $messageId
     * @throws ConvosException
     */
    public function deleteConversationMessage($convoId, $userId, $messageId)
    {
        $this->_validate(
            ['userId' => $userId, 'convoId' => $convoId, 'messageId' => $messageId],
            [
                'convoId' => 'required|integer|min:1',
                'userId' => 'required|integer|min:1',
                'messageId' => 'required|integer|min:1'
            ]
        );
        return $this->repository->deleteConversationMessage($convoId, $userId, $messageId);
    }

    /**
     * Deletes a conversation from the specified user list
     *
     * @param $convoId
     * @param $userId
     * @return mixed
     * @throws ConvosException
     */
    public function deleteConversation($convoId, $userId)
    {
        $this->_validate(
            ['userId' => $userId, 'convoId' => $convoId],
            [
                'convoId' => 'required|integer|min:1',
                'userId' => 'required|integer|min:1'
            ]
        );
        return $this->repository->deleteConversation($convoId, $userId);
    }

    /**
     * Updates a conversation status.
     * At the moment this method only marks the conversation as read
     *
     * @param $convoId
     * @param $userId
     * @param array $data
     * @return mixed
     * @throws ConvosException
     */
    public function updateConversation($convoId, $userId, array $data)
    {
        $this->_validate(
            $data,
            [
                'is_read' => 'required|boolean'
            ]
        );
        return $this->repository->markConversationAsRead($convoId, $userId, $data['is_read']);
    }
}
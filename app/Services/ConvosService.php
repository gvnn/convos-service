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
            'recipient' => 'required|integer|min:1',
            'body' => 'required'
        ]);

        // Create a new conversation
        $convo = $this->repository->createConversation($data['user_id'], $data['subject']);

        // Add participants
        $this->repository->addConverstationParticipants($convo, $data['user_id'], array($data['recipient']));

        // Add message
        $this->repository->addConverstationMessage($convo, $data['user_id'], $data['body']);

        return $convo;
    }

    private function _validate($data, array $rules)
    {
        $v = Validator::make($data, $rules);
        if ($v->fails()) {
            throw new ConvosException($v->errors());
        }
    }

    public function addConverstationMessage($convoId, array $data)
    {
        $data['conversation_id'] = $convoId;

        $this->_validate($data, [
            'conversation_id' => 'required|integer|min:1',
            'user_id' => 'required|integer|min:1',
            'body' => 'required'
        ]);

        // get convo
        $convo = $this->repository->getConversation($convoId);

        // add message an return message details
        return $this->repository->addConverstationMessage($convo, $data['user_id'], $data['body']);
    }

    public function getConverstation($convoId)
    {
        $this->_validate([
            'convoId' => $convoId
        ], [
            'convoId' => 'required|integer|min:1'
        ]);

        return $this->repository->getConversation($convoId);
    }

    public function getConverstationMessages($convoId, $userId, $limit = 25, $page = 1, $until = null)
    {
        $this->_validate(
            ['userId' => $userId, 'convoId' => $convoId],
            [
                'convoId' => 'required|integer|min:1',
                'userId' => 'required|integer|min:1'
            ]
        );

        $pagination = $this->_parsePaginationParams($limit, $page, $until);
        return $this->repository->getConversationMessages($convoId, $userId, $pagination);
    }

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

    private function _tryParseInt($val, $default)
    {
        $intValue = ctype_digit((string)$val) ? intval($val) : null;
        if ($intValue === null) {
            $intValue = $default;
        }
        return $intValue;
    }

    public function getConversations($userId, $limit = 25, $page = 1, $until = null)
    {
        $this->_validate(
            ['userId' => $userId],
            ['userId' => 'required|integer|min:1']);
        $pagination = $this->_parsePaginationParams($limit, $page, $until);
        return $this->repository->getConversations($userId, $pagination);
    }

    public function markConversationAsRead($convoId, $userId)
    {
        $this->_validate(
            ['userId' => $userId, 'convoId' => $convoId],
            [
                'convoId' => 'required|integer|min:1',
                'userId' => 'required|integer|min:1'
            ]
        );
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
}
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

    public function create($userId, array $data)
    {
        $data['user_id'] = $userId;

        $this->_validate($data, [
            'user_id' => 'required|integer|min:1',
            'subject' => 'required|max:140',
            'recipient' => 'required|integer|min:1',
            'body' => 'required'
        ]);

        // Create a new conversation
        $convo = $this->repository->createConvo($data['user_id'], $data['subject']);

        // Add participants
        $this->repository->addConvoParticipants($convo, $data['user_id'], array($data['recipient']));

        // Add message
        $this->repository->addMessage($convo, $data['user_id'], $data['body']);

        return $convo;
    }

    private function _validate($data, array $rules)
    {
        $v = Validator::make($data, $rules);
        if ($v->fails()) {
            throw new ConvosException($v->errors());
        }
    }

    public function addMessage($convoId, array $data)
    {
        $data['conversation_id'] = $convoId;

        $this->_validate($data, [
            'conversation_id' => 'required|integer|min:1',
            'user_id' => 'required|integer|min:1',
            'body' => 'required'
        ]);

        // get convo
        $convo = $this->repository->getConvo($convoId);

        // add message an return message details
        return $this->repository->addMessage($convo, $data['user_id'], $data['body']);
    }

    public function getConvo($convoId)
    {
        $this->_validate([
            'conversation_id' => $convoId
        ], [
            'conversation_id' => 'required|integer|min:1'
        ]);

        return $this->repository->getConvo($convoId);
    }

    public function getConvoMessages($convoId, $userId, $limit = 25, $page = 1, $until = null)
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

        return $this->repository->getConvoMessages($convoId, $userId, $intLimit, $intPage, $untilDate);
    }

    private function _tryParseInt($val, $default)
    {
        $intValue = ctype_digit((string)$val) ? intval($val) : null;
        if ($intValue === null) {
            $intValue = $default;
        }
        return $intValue;
    }

    public function getConversations()
    {
    }

    public function markAsRead()
    {
    }

    public function deleteMessage()
    {
    }

    public function deleteConvo()
    {
    }
}
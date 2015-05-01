<?php
namespace App\Services;

use App\Model\ConvosException;
use App\Repositories\ConvosRepositoryInterface;
use Illuminate\Support\Facades\Validator;

class ConvosService implements ConvosServiceInterface
{
    function __construct(ConvosRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function create($userId, array $data)
    {
        $data['user_id'] = $userId;

        $v = Validator::make($data, [
            'user_id' => 'required|integer|min:1',
            'subject' => 'required|max:140',
            'recipient' => 'required|integer|min:1',
            'body' => 'required'
        ]);

        if ($v->fails()) {
            throw new ConvosException($v->errors());
        }

        // Create a new conversation
        $convo = $this->repository->create_convo($data['user_id'], $data['subject']);

        // Add participants
        $this->repository->add_convo_participants($convo, $data['user_id'], array($data['recipient']));

        // Add message
        $this->repository->add_message($convo, $data['user_id'], $data['body']);

        return $convo;
    }
}
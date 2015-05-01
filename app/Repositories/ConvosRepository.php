<?php namespace App\Repositories;


use App\Model\Convos\Conversation;
use App\Model\Convos\Message;
use App\Model\Convos\Participant;
use Carbon\Carbon;

class ConvosRepository implements ConvosRepositoryInterface
{

    public function createConvo($userId, $subject)
    {
        $convo = new Conversation;

        $convo->subject = $subject;
        $convo->created_by = $userId;

        $convo->save();

        return $convo;
    }

    public function addConvoParticipants(Conversation $convo, $creator, array $participants)
    {
        $participantsArray = [
            new Participant(['user_id' => $creator, 'is_creator' => true, 'is_read' => true, 'read_at' => Carbon::now()])
        ];

        foreach ($participants as $participantId) {
            $participantsArray[] =
                new Participant([
                    'user_id' => $participantId,
                    'is_creator' => false,
                    'is_read' => false,
                    'read_at' => null
                ]);
        }

        $convo->participants()->saveMany($participantsArray);

        return $convo;
    }

    public function addMessage(Conversation $convo, $userId, $body)
    {
        $message = new Message([
            'user_id' => $userId,
            'body' => $body
        ]);

        $convo->messages()->save($message);

        return $message;
    }

    public function getConvo($convoId)
    {
        return Conversation::find($convoId);
    }

}
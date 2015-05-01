<?php namespace App\Repositories;


use App\Model\Convos\Conversation;
use App\Model\Convos\Message;
use App\Model\Convos\Participant;
use Carbon\Carbon;

class ConvosRepository implements ConvosRepositoryInterface
{

    public function create_convo($user_id, $subject)
    {
        $convo = new Conversation;

        $convo->subject = $subject;
        $convo->created_by = $user_id;

        $convo->save();

        return $convo;
    }

    public function add_convo_participants(Conversation $convo, $creator, array $participants)
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

    public function add_message(Conversation $convo, $user_id, $body)
    {
        $convo->messages()->save(new Message([
            'user_id' => $user_id,
            'body' => $body
        ]));

        return $convo;
    }

}
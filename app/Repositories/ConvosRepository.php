<?php namespace App\Repositories;


use App\Model\Convos\Conversation;
use App\Model\Convos\Message;
use App\Model\Convos\Participant;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

        // update participants and set is_read to false and read_at to null
        // for everyone except the user that created the message
        $table = with(new Participant)->getTable();
        DB::table($table)
            ->where('conversation_id', $convo->id)
            ->where('user_id', '<>', $userId)
            ->whereNull('deleted_at')
            ->update(['read_at' => null, 'is_read' => 0]);

        // and for the current user I set is read and read at now
        DB::table($table)
            ->where('conversation_id', $convo->id)
            ->where('user_id', $userId)
            ->whereNull('deleted_at')
            ->update(['read_at' => Carbon::now(), 'is_read' => 1]);

        return $message;
    }

    public function getConvo($convoId)
    {
        return Conversation::find($convoId);
    }

    public function getConvoMessages($convoId, $userId, $intLimit, $intPage, $untilDate)
    {
        $messagesTable = with(new Message)->getTable();
        $participantsTable = with(new Participant)->getTable();
        $base_query = DB::table($messagesTable)
            ->join($participantsTable, $participantsTable . '.conversation_id', '=', $messagesTable . '.conversation_id')
            ->where($messagesTable . '.conversation_id', $convoId)
            ->where($participantsTable . '.user_id', $userId)
            ->whereNull($messagesTable . '.deleted_at')
            ->whereNull($participantsTable . '.deleted_at');

        if (!is_null($untilDate)) {
            $base_query = $base_query->where($messagesTable . '.created_at', '<=', $untilDate);
        }

        $result = [
            'pagination' => [
                'page' => $intPage,
                'limit' => $intLimit,
                'count' => $base_query->count()
            ],
            'messages' => $base_query->skip(($intPage - 1) * $intLimit)->take($intLimit)->get()
        ];

        return $result;
    }
}
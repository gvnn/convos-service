<?php namespace App\Repositories;


use App\Model\Convos\Conversation;
use App\Model\Convos\Message;
use App\Model\Convos\Participant;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ConvosRepository implements ConvosRepositoryInterface
{

    public function createConversation($userId, $subject)
    {
        $convo = new Conversation;

        $convo->subject = $subject;
        $convo->created_by = $userId;

        $convo->save();

        return $convo;
    }

    public function addConversationParticipants(Conversation $convo, $creator, array $participants)
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

    public function addConversationMessage(Conversation $convo, $userId, $body)
    {
        $message = new Message([
            'user_id' => $userId,
            'body' => $body
        ]);

        $convo->messages()->save($message);

        // update participants and set is_read to false and read_at to null
        // for everyone except the user that created the message
        // also if a participant deleted the conversation it comes back in list
        $table = with(new Participant)->getTable();
        DB::table($table)
            ->where('conversation_id', $convo->id)
            ->where('user_id', '<>', $userId)
            ->update(['read_at' => null, 'is_read' => 0, 'deleted_at' => null, 'updated_at' => Carbon::now()]);

        // and for the current user I set is read and read at now
        DB::table($table)
            ->where('conversation_id', $convo->id)
            ->where('user_id', $userId)
            ->whereNull('deleted_at')
            ->update(['read_at' => Carbon::now(), 'is_read' => 1, 'updated_at' => Carbon::now()]);

        return $message;
    }

    public function getConversation($convoId, $userId)
    {
        $conversation = Participant::where('conversation_id', $convoId)->where('user_id', $userId)->firstOrFail()->conversation;
        $conversation->participants;
        return $conversation;
    }

    public function getConversationMessages($convoId, $userId, array $pagination)
    {
        $messagesTable = with(new Message)->getTable();
        $participantsTable = with(new Participant)->getTable();
        $base_query = DB::table($messagesTable)
            ->join($participantsTable, $participantsTable . '.conversation_id', '=', $messagesTable . '.conversation_id')
            ->where($messagesTable . '.conversation_id', $convoId)
            ->where($participantsTable . '.user_id', $userId)
            ->whereNull($messagesTable . '.deleted_at')
            ->whereNull($participantsTable . '.deleted_at')
            ->select(
                $messagesTable . '.id',
                $messagesTable . '.body',
                $messagesTable . '.updated_at',
                $messagesTable . '.user_id'
            );

        if (!is_null($pagination['until'])) {
            $base_query = $base_query->where($messagesTable . '.created_at', '<=', $pagination['until']);
        }

        $result = [
            'pagination' => [
                'page' => $pagination['page'],
                'limit' => $pagination['limit'],
                'count' => $base_query->count()
            ],
            'messages' => $base_query
                ->orderBy($messagesTable . '.id', 'asc')
                ->skip(($pagination['page'] - 1) * $pagination['limit'])
                ->take($pagination['limit'])->get()
        ];

        return $result;
    }

    public function getConversations($userId, array $pagination)
    {
        $convosTable = with(new Conversation)->getTable();
        $participantsTable = with(new Participant)->getTable();

        $base_query = DB::table($convosTable)
            ->join($participantsTable, $participantsTable . '.conversation_id', '=', $convosTable . '.id')
            ->where($participantsTable . '.user_id', $userId)
            ->whereNull($convosTable . '.deleted_at')
            ->whereNull($participantsTable . '.deleted_at')
            ->select(
                $convosTable . '.id',
                $convosTable . '.subject',
                $participantsTable . '.updated_at',
                $participantsTable . '.is_read',
                $convosTable . '.created_by'
            );

        if (!is_null($pagination['until'])) {
            $base_query = $base_query->where($convosTable . '.created_at', '<=', $pagination['until']);
        }

        $result = [
            'pagination' => [
                'page' => $pagination['page'],
                'limit' => $pagination['limit'],
                'count' => $base_query->count()
            ],
            'conversations' => $base_query
                ->orderBy($participantsTable . '.updated_at', 'desc')
                ->orderBy($convosTable . '.updated_at', 'desc')
                ->orderBy($convosTable . '.id', 'desc')
                ->skip(($pagination['page'] - 1) * $pagination['limit'])
                ->take($pagination['limit'])->get()
        ];

        return $result;
    }

    public function deleteConversation($convoId, $userId)
    {
        //find a participation
        $participant = Participant::where(
            'conversation_id', $convoId
        )->where('user_id', $userId)->firstOrFail();

        $convo = $participant->conversation;

        $participant->delete();

        return $convo;
    }

    public function deleteConversationMessage($convoId, $userId, $messageId)
    {
        // find message, only the creator can delete that
        $message = Message::where('conversation_id', $convoId)
            ->where('user_id', $userId)
            ->where('id', $messageId)
            ->firstOrFail();

        $message->delete();

        return $message;
    }

    public function markConversationAsRead($convoId, $userId, $is_read)
    {
        // find the participant
        $participant = Participant::where(
            'conversation_id', $convoId
        )->where('user_id', $userId)->firstOrFail();

        $participant->is_read = $is_read;
        $participant->read_at = $is_read ? Carbon::now() : null;

        $participant->save();

        return $participant->conversation;
    }
}
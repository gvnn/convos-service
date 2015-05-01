<?php namespace App\Repositories;

use App\Model\Convos\Conversation;

interface ConvosRepositoryInterface
{
    public function createConversation($userId, $subject);

    public function addConverstationParticipants(Conversation $convo, $creator, array $participants);

    public function addConverstationMessage(Conversation $convo, $userId, $body);

    public function getConversation($convoId);

    public function getConversationMessages($convoId, $userId, array $pagination);

    public function getConversations($userId, array $pagination);

    public function deleteConversation($convoId, $userId);

    public function deleteConversationMessage($convoId, $userId, $messageId);
}
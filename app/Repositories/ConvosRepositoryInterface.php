<?php namespace App\Repositories;

use App\Model\Convos\Conversation;

interface ConvosRepositoryInterface
{
    public function createConversation($userId, $subject);

    public function addConversationParticipants(Conversation $convo, $creator, array $participants);

    public function addConversationMessage(Conversation $convo, $userId, $body);

    public function getConversation($convoId, $userId);

    public function getConversationMessages($convoId, $userId, array $pagination);

    public function getConversations($userId, array $pagination);

    public function deleteConversation($convoId, $userId);

    public function deleteConversationMessage($convoId, $userId, $messageId);

    public function markConversationAsRead($convoId, $userId, $is_read);

}
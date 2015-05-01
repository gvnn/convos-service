<?php namespace App\Services;

interface ConvosServiceInterface
{
    public function createConversation($userId, array $data);

    public function addConversationMessage($convoId, $userId, array $data);

    public function getConversation($convoId, $userId);

    public function getConversationMessages($convoId, $userId, $limit = 25, $page = 1, $until = null);

    public function getConversations($userId, $limit = 25, $page = 1, $until = null);

    public function deleteConversationMessage($convoId, $userId, $messageId);

    public function deleteConversation($convoId, $userId);

    public function updateConversation($convoId, $userId, array $data);
}
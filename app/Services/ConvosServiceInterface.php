<?php namespace App\Services;

interface ConvosServiceInterface
{
    public function createConversation($userId, array $data);

    public function addConverstationMessage($convoId, array $data);

    public function getConverstation($convoId, $userId);

    public function getConverstationMessages($convoId, $userId, $limit = 25, $page = 1, $until = null);

    public function getConversations($userId, $limit = 25, $page = 1, $until = null);

    public function markConversationAsRead($convoId, $userId);

    public function deleteConversationMessage($convoId, $userId, $messageId);

    public function deleteConversation($convoId, $userId);
}
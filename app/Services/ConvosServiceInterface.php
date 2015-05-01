<?php namespace App\Services;

interface ConvosServiceInterface
{
    public function create($userId, array $data);

    public function addMessage($convoId, array $data);

    public function getConvo($convoId);

    public function getConvoMessages($convoId, $userId, $limit = 25, $page = 1, $until = null);

    public function getConversations($userId, $limit = 25, $page = 1, $until = null);

    public function markAsRead($convoId, $userId);

    public function deleteMessage($convoId, $userId, $messageId);

    public function deleteConvo($convoId, $userId);
}
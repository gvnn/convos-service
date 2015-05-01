<?php namespace App\Repositories;

use App\Model\Convos\Conversation;

interface ConvosRepositoryInterface
{
    public function createConvo($userId, $subject);

    public function addConvoParticipants(Conversation $convo, $creator, array $participants);

    public function addMessage(Conversation $convo, $userId, $body);

    public function getConvo($convoId);

    public function getConvoMessages($convoId, $intLimit, $intPage, $untilDate);
}
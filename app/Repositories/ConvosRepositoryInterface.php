<?php namespace App\Repositories;

use App\Model\Convos\Conversation;

interface ConvosRepositoryInterface
{
    public function create_convo($user_id, $subject);

    public function add_convo_participants(Conversation $convo, $creator, array $participants);

    public function add_message(Conversation $convo, $user_id, $body);
}
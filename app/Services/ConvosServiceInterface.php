<?php namespace App\Services;

interface ConvosServiceInterface
{
    public function create($userId, array $data);

    public function addMessage($convoId, array $data);
}
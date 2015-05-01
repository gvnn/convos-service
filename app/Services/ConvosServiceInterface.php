<?php namespace App\Services;

use Illuminate\Contracts\Auth\Authenticatable;

interface ConvosServiceInterface
{
    public function create($userId, array $data);
}
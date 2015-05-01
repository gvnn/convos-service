<?php namespace App\Http\Controllers;

use App\Services\ConvosServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConvosController extends Controller
{
    public function __construct(
        ConvosServiceInterface $service
    )
    {
        $this->service = $service;
    }

    public function create(Request $request)
    {
        $convo = $this->service->create(Auth::user(), Request::all());
        return $convo->toJson();
    }

}
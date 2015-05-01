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

    public function  find(Request $request)
    {
    }

    public function get(Request $request, $id)
    {
        $convo = $this->service->getConverstation(
            $id,
            Auth::user()->id
        );
        return $convo->toJson();
    }

    public function  update(Request $request)
    {
    }

    public function  delete(Request $request)
    {
    }

    public function create(Request $request)
    {
        $convo = $this->service->createConversation(Auth::user(), Request::all());
        return $convo->toJson();
    }

}
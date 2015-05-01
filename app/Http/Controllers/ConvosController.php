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

    public function find(Request $request)
    {
        $convos = $this->service->getConversations(
            Auth::user()->id,
            $request->get('limit'),
            $request->get('page'),
            $request->get('until')
        );
        return response()->json($convos);
    }

    public function get(Request $request, $id)
    {
        $convo = $this->service->getConversation($id, Auth::user()->id);
        return response()->json($convo);
    }

    public function update(Request $request, $id)
    {
        $convo = $this->service->updateConversation($id, Auth::user()->id, $request->all());
        return response()->json($convo);
    }

    public function delete(Request $request, $id)
    {
        $convo = $this->service->deleteConversation($id, Auth::user()->id);
        return response()->json($convo);
    }

    public function create(Request $request)
    {
        $convo = $this->service->createConversation(Auth::user()->id, $request->all());
        return response()->json($convo);
    }
}
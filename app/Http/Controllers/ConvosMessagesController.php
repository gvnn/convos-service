<?php namespace App\Http\Controllers;

use App\Services\ConvosServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConvosMessagesController extends Controller
{
    public function __construct(
        ConvosServiceInterface $service
    )
    {
        $this->service = $service;
    }

    public function find(Request $request, $convoId)
    {
        $messages = $this->service->getConversationMessages(
            $convoId,
            Auth::user()->id,
            $request->get('limit'),
            $request->get('page'),
            $request->get('until')
        );
        return response()->json($messages);
    }

    public function delete($convoId, $id)
    {
        $message = $this->service->deleteConversationMessage($convoId, Auth::user()->id, $id);
        return response()->json($message);
    }

    public function create(Request $request, $convoId)
    {
        $message = $this->service->addConversationMessage($convoId, Auth::user()->id, $request->all());
        return response()->json($message);
    }

}
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
        $messages = $this->service->getConverstationMessages(
            $convoId,
            Auth::user()->id,
            $request->get('limit'),
            $request->get('page'),
            $request->get('until')
        );
        return  $messages;
    }

    public function delete($convoId, $id)
    {
        $message = $this->service->deleteConversationMessage($convoId, Auth::user()->id, $id);
        return $message->toJson();
    }

    public function create(Request $request, $convoId)
    {
        $message = $this->service->addConverstationMessage($convoId, Auth::user()->id, $request->all());
        return $message->toJson();
    }

}
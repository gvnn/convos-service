<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{
    public function create(Request $request)
    {
        $v = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($v->fails()) {
            return response($v->messages(), 400);
        }

        return response('ok');
    }

    public function delete(Request $request, $id)
    {
        return response('ok');
    }
}
<?php

namespace App\Auth\Http\Controllers;

use Illuminate\Http\Request;

use App\Base\Http\Controllers\Controller;
use App\Common\Response;
use App\User\User;

class AuthController extends Controller
{
    /**
     * Get current user status
     */
    public function getStatus()
    {
        $user = \Auth::user();
        if ( ! $user) {
            return [
                'id' => 0,
                'name' => 'guest'
            ];
        }
        return $user;
    }

    /**
     * Confirm a user account with a valid confirmation token
     */
    public function confirm(Request $request)
    {
        try {
            $this->validate($request, [
                'u' => 'required|numeric',
                't' => 'required|string'
            ]);
        } catch (\Exception $e) {
            return Response::error($e->errors());
        }
        $user = User::query()
            ->where('id', $request->get('u'))
            ->where('confirmation_code', $request->get('t'))
            ->first();
        if ( ! $user) {
            return Response::error([
                'confirm' => 'Invalid request'
            ]);
        }
        $user->active = true;
        $user->confirmation_code = null;
        $user->save();
        return [
            'status' => 'OK'
        ];
    }
}

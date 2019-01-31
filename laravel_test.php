<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;

/**
 * Class AdminRolesController
 */
class AdminController extends Controller
{
    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function banUser($id, Request $request)
    {
        $data = $request->validate([
            'reason'   => 'string',
        ]);

        $user = \User::find($id);

        //If user not found
        if (! $user) {
            throw new \Exception('User not found');
        }

        //If user not admin
        if ($user->role == 1) {
            throw new \Exception('Cannot ban an admin');
        }

        //Set their role and status to banned
        $user->role = 9;
        $user->status = 0;
        $user->save();

        //If there was a reason passed in
        if (isset($data['reason']) && $data['reason']) {
            \UserLog::create([
                'user_id' => $user->id,
                'action' => 'banned',
                'reason' => $data['reason'],
            ]);
        } else {
            \UserLog::create([
                'user_id' => $user->id,
                'action' => 'banned',
            ]);
        }

        //Go back with message
        return Redirect::back()->with('Message', 'User has been banned');
    }

}
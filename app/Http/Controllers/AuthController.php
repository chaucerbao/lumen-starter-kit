<?php

namespace App\Http\Controllers;

use App\PendingUpdate;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show the authentication form (login).
     *
     * @return \Illuminate\Http\Response
     */
    public function createSession()
    {
        return view('auth.form');
    }

    /**
     * Attempt to authenticate a user and create a session.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeSession(Request $request)
    {
        if (Auth::attempt($request->only('email', 'password'), true)) {
            return redirect('dashboard');
        }

        return redirect()->route('auth.createSession')->withInput()->withErrors(['auth' => trans('auth.failed')]);
    }

    /**
     * Show the form for recovering a password.
     *
     * @return \Illuminate\Http\Response
     */
    public function createRecoveryToken()
    {
        return view('auth.recover');
    }

    /**
     * Generate a password recovery token.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeRecoveryToken(Request $request)
    {
        if ($user = User::where('email', $request->email)->first()) {
            $pending = PendingUpdate::create([
                'model' => 'App\User',
                'id' => $user->id,
                'update' => ['password' => null],
            ]);

            return redirect('account/reset_requested');
        }

        return redirect()->route('auth.createRecoveryToken')->withInput();
    }

    /**
     * Show the form for resetting a password.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\Response
     */
    public function editPassword($token)
    {
        return view('auth.password', compact('token'));
    }

    /**
     * Update the password in storage.
     *
     * @param Request $request
     * @param string  $token
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request, $token)
    {
        if ($pending = PendingUpdate::apply($token, ['password' => $request->password])) {
            return redirect('account/password_set');
        }

        return redirect()->route('auth.editPassword', ['token' => $token])->withInput();
    }
}

<?php

namespace App\Http\Controllers;

use App\Jobs\SendPendingUpdateConfirmationRequestEmail;
use App\PendingUpdate;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show the registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function createUser()
    {
        return view('auth.register');
    }

    /**
     * Store a newly registered user in storage.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeUser(Request $request)
    {
        $this->validate($request, User::$rules);

        $user = User::create($request->all());

        $pending = PendingUpdate::create([
            'model' => $user,
            'update' => ['is_confirmed' => true],
        ]);
        $this->dispatch(new SendPendingUpdateConfirmationRequestEmail($user, $pending->token, 'Registration confirmation', 'auth.email.register_confirmation'));

        return redirect()->route('auth.registerConfirmation');
    }

    /**
     * Update a user's e-mail confirmation status.
     *
     * @return \Illuminate\Http\Response
     */
    public function emailConfirmed($token)
    {
        PendingUpdate::apply($token);

        return view('auth.email_confirmed');
    }

    /**
     * Show the authentication form (login).
     *
     * @return \Illuminate\Http\Response
     */
    public function createSession()
    {
        return view('auth.login');
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
        $this->validate($request, ['email' => 'required|email|exists:users']);

        $user = User::where('email', $request->email)->first();
        $pending = PendingUpdate::create([
            'model' => $user,
            'update' => ['password' => null],
        ]);

        return redirect()->route('auth.recoverInstructions');
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
        $this->validate($request, array_only(User::$rules, ['password']));

        if ($pending = PendingUpdate::apply($token, ['password' => $request->password])) {
            return redirect()->route('auth.passwordReset');
        }

        return redirect()->route('auth.editPassword', ['token' => $token])->withInput()->withErrors(['token' => trans('auth.bad_token')]);
    }
}

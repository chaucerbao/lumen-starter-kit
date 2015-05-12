<?php

namespace App\Http\Controllers;

use App\Jobs\RegistrationConfirmationEmail;
use App\PendingUpdate;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();

        return view('user.index', compact('users'));
    }

    /**
     * Display the specified user.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

        return view('user.show', compact('user'));
    }

    /**
     * Store a newly created user in storage.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, User::$rules);

        $user = User::create($request->all());

        $pending = PendingUpdate::create([
            'model' => 'App\User',
            'id' => $user->id,
            'update' => ['is_confirmed' => true],
        ]);
        $this->dispatch(new RegistrationConfirmationEmail($user, $pending->token));

        return redirect()->route('user.index');
    }

    /**
     * Update the specified user in storage.
     *
     * @param Request $request
     * @param int     $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $rules = User::$rules;
        if (empty($request->input('password'))) {
            unset($rules['password']);
        }
        $this->validate($request, $rules);

        $user = User::findOrFail($id);
        $user->update($request->all());

        return redirect()->route('user.index');
    }

    /**
     * Remove the specified user from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('user.index');
    }

    /**
     * Show the form for creating a user.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = new User();

        return view('user.form', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);

        return view('user.form', compact('user'));
    }
}

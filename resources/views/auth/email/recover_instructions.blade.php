@extends('layout.email')

@section('body')
Hello {{ $user->full_name }}

<a href="{{ route('auth.editPassword', ['token' => $token]) }}">Recover Account</a>
@stop

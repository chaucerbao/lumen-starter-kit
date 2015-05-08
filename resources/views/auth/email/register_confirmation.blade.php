@extends('layout.email')

@section('body')
Hello {{ $user->full_name }}

<a href="{{ route('auth.emailConfirmed', ['token' => $token]) }}">Confirm Registration</a>
@stop

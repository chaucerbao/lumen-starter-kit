@extends('layout.email')

@section('site-body')
Hello {{ $user->full_name }}

<a href="{{ route('auth.registrationConfirmed', ['token' => $token])  }}">Confirm Registration</a>
@stop

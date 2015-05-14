@extends('layout.email')

@section('body')
{{ $user->full_name }},

<a href="{{ route('auth.emailConfirmed', ['token' => $token]) }}">{{ trans('auth.confirm_registration') }}</a>
@stop

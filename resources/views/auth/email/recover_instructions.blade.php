@extends('layout.email')

@section('body')
{{ $user->full_name }},

<a href="{{ route('auth.editPassword', ['token' => $token]) }}">{{ trans('auth.recover_account') }}</a>
@stop

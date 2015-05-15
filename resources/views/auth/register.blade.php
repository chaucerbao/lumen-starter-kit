@extends('layout.default')

@section('site-body')
<form action="{{ route('auth.storeUser') }}" method="post">
    <input name="_token" value="{{ csrf_token() }}" type="hidden">

    <label for="first_name">{{ trans('user.first_name') }}</label>
    <input id="first_name" name="first_name" value="{{ old('first_name') }}" type="text">

    <label for="last_name">{{ trans('user.last_name') }}</label>
    <input id="last_name" name="last_name" value="{{ old('last_name') }}" type="text">

    <label for="email">{{ trans('user.email') }}</label>
    <input id="email" name="email" value="{{ old('email') }}" type="email">

    <label for="password">{{ trans('user.password') }}</label>
    <input id="password" name="password" type="password">

    <label for="password_confirmation">{{ trans('user.password_confirmation') }}</label>
    <input id="password_confirmation" name="password_confirmation" type="password">

    <input value="{{ trans('form.register') }}" type="submit">
</form>
@stop

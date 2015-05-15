@extends('layout.default')

@section('site-body')
<form action="{{ route('auth.storeSession') }}" method="post">
    <input name="_token" value="{{ csrf_token() }}" type="hidden">

    <label for="email">{{ trans('user.email') }}</label>
    <input id="email" name="email" value="{{ old('email') }}" type="email">

    <label for="password">{{ trans('user.password') }}</label>
    <input id="password" name="password" type="password">

    <input value="{{ trans('form.login') }}" type="submit">
</form>
@stop

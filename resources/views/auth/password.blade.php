@extends('layout.default')

@section('site-body')
<form action="{{ route('auth.updatePassword', ['token' => $token]) }}" method="post">
    <input name="_method" value="PUT" type="hidden">
    <input name="_token" value="{{ csrf_token() }}" type="hidden">

    <label for="password">{{ trans('user.password') }}</label>
    <input id="password" name="password" type="password">

    <input value="{{ trans('form.update') }}" type="submit">
</form>
@stop

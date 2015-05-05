@extends('layout.default')

@section('site-body')
<form action="{{ route('auth.storeRecoveryToken') }}" method="post">
    <input name="_token" value="{{ csrf_token() }}" type="hidden">

    <label for="email">{{ trans('user.email') }}</label>
    <input id="email" name="email" value="{{ old('email') }}" type="email">

    <input value="{{ trans('form.login') }}" type="submit">
</form>
@stop

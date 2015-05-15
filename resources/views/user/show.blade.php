@extends('layout.default')

@section('site-body')
<dl>
    <dt>Name</dt>
    <dd>{{ $user->first_name }} {{ $user->last_name }}</dd>

    <dt>E-mail</dt>
    <dd>{{ $user->email }}</dd>
</dl>
@stop

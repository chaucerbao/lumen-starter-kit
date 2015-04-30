@extends('layout.default')

@section('site-body')
<table>
    <thead>
        <tr>
            <th>{{ trans('user.first_name') }}</th>
            <th>{{ trans('user.last_name') }}</th>
            <th>{{ trans('user.email') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
        <tr>
            <td>{{ $user->first_name }}</td>
            <td>{{ $user->last_name }}</td>
            <td><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></td>
        </tr>
        @endforeach
    </tbody>
</table>
@stop

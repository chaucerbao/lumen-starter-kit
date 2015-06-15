@extends('layout.default')

@section('site-body')
<table>
    <thead>
        <tr>
            <th>{{ trans('tag.slug') }}</th>
            <th>{{ trans('tag.name') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($tags as $tag)
        <tr>
            <td>{{ $tag->slug }}</td>
            <td>{{ $tag->name }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@stop

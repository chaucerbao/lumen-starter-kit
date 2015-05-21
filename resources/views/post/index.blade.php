@extends('layout.default')

@section('site-body')
<table>
    <thead>
        <tr>
            <th>{{ trans('post.slug') }}</th>
            <th>{{ trans('post.title') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($posts as $post)
        <tr>
            <td>{{ $post->slug }}</td>
            <td>{{ $post->title }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@stop

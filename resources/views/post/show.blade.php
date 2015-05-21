@extends('layout.default')

@section('site-body')
<dl>
    <dt>Slug</dt>
    <dd>{{ $post->slug }}</dd>

    <dt>Title</dt>
    <dd>{{ $post->title }}</dd>

    <dt>Body</dt>
    <dd>{{ $post->body }}</dd>
</dl>
@stop

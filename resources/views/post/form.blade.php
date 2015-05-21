@extends('layout.default')

@section('site-body')
<form action="{{ $post->exists ? route('post.update', ['slug' => $post->slug]) : route('post.store') }}" method="post">
    @if ($post->exists)<input name="_method" value="PUT" type="hidden">@endif
    <input name="_token" value="{{ csrf_token() }}" type="hidden">

    <label for="slug">{{ trans('post.slug') }}</label>
    <input id="slug" name="slug" value="{{ old('slug', $post->slug) }}" type="text">

    <label for="title">{{ trans('post.title') }}</label>
    <input id="title" name="title" value="{{ old('title', $post->title) }}" type="text">

    <label for="body">{{ trans('post.body') }}</label>
    <textarea id="body" name="body">{{ old('body', $post->body) }}</textarea>

    <label for="published_at">{{ trans('post.published_at') }}</label>
    <input id="published_at" name="published_at" value="{{ old('published_at', $post->published_at) }}" type="datetime-local">

    <label for="is_active">{{ trans('post.active') }}</label>
    <input name="is_active" value="0" type="hidden">
    <input id="is_active" name="is_active" value="1" {{ old('is_active', $post->is_active) ? 'checked ' : '' }}type="checkbox">

    <input value="{{ trans($post->exists ? 'form.update' : 'form.save') }}" type="submit">
</form>
@stop

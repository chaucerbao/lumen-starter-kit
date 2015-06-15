@extends('layout.default')

@section('site-body')
<form action="{{ $tag->exists ? route('tag.update', ['id' => $tag->id]) : route('tag.store') }}" method="post">
    @if ($tag->exists)<input name="_method" value="PUT" type="hidden">@endif
    <input name="_token" value="{{ csrf_token() }}" type="hidden">

    <label for="slug">{{ trans('tag.slug') }}</label>
    <input id="slug" name="slug" value="{{ old('slug', $tag->slug) }}" type="text">

    <label for="name">{{ trans('tag.name') }}</label>
    <input id="name" name="name" value="{{ old('name', $tag->name) }}" type="text">

    <input value="{{ trans($tag->exists ? 'form.update' : 'form.save') }}" type="submit">
</form>
@stop

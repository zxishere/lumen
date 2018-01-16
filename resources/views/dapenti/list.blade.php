@extends('dapenti.index')

@section('title')
    {{ $title }}
@stop

@section('content')
	@foreach ($content as $single)
	    <p> <a href="{{ $single->link }}">{{ $single->title }}</a></p>
		<p><img src="{{ $single->imgurl }}" alt="{{ $single->title }}"></p>
		<hr>
	@endforeach
@stop
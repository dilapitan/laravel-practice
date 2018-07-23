@extends('layouts.app')

@section('content')
  <a href="/posts" class="btn btn-primary btn-lg">Go Back</a>
  
  <br><br>

  <h1>{{ $post->title }}</h1>
  <div>
    {!! $post->body !!}
  </div>
  <hr>
  <small>Written on {{ $post->created_at }}</small>

  <hr>

  <a href="/posts/{{$post->id}}/edit" class="btn btn-primary">Edit</a>
@endsection
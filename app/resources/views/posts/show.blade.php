@extends('layouts.app')

@section('content')
  <a href="/posts" class="btn btn-primary btn-lg">Go Back</a>
  
  <br><br>

  <h1>{{ $post->title }}</h1>
  <img src="/storage/cover_images/{{$post->cover_image}}" style="width: 100%; height: auto;">
  <br><br>
  <div>
    {!! $post->body !!}
  </div>
  <hr>
  <small>Written on {{ $post->created_at }} by {{ $post->user->name }} </small>

  <hr>

  @auth
    @if(Auth::user()->id == $post->user_id)
    <a href="/posts/{{$post->id}}/edit" class="btn btn-primary">Edit</a>

    
    {!! Form::open(['action' => ['PostsController@destroy', $post->id], 'method' => 'POST', 'class' => 'float-right']) !!}
        {{ Form::hidden('_method', 'DELETE') }}
        {{ Form::submit('Delete', ['class' => 'btn btn-danger']) }}
    {!! Form::close() !!}
    @endif
  @endauth
@endsection
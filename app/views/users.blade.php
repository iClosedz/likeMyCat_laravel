@extends('layout')

@section('title')
	<title>Users</title>
@stop

@section('content')
    @foreach($users as $user)
        <p>{{ $user->password }}</p>
    @endforeach
@stop
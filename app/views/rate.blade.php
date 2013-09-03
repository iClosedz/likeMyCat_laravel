@extends('layout')

@section('title')
	<title>Users</title>
@stop

@section('content')
        <div>
        <h1>{{ $user->email }}</h1>
        @foreach($roles as $role)
        	<span>{{ $role->role->role_name }}</span>
  	  	@endforeach
  	  	</div>
@stop
@extends('layout')

@section('title')
<title>Users</title>
@stop

@section('content')
<div>
	@if (Auth::check())
	<h1>{{{ $user->email }}} from {{ long2ip($user->ip_address) }}</h1>

		@foreach($roles as $role)
		<span>{{ $role->role->role_name }}</span>
		@endforeach
	@endif
</div>
@stop
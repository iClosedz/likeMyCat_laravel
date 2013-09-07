@extends('layout')

@section('title')
<title>Manage Roles for {{{ $userBeingManaged->email }}}</title>
@stop

@section('content')
<h1>Manage Roles for {{{ $userBeingManaged->email }}}</h1>
<hr/>
<table class="table table-condensed table-hover" id="sort">
	<thead>
		<tr>
			<th>Role</th>
			<th>Status</th>
		</tr>
	</thead>
	<tbody>
		@foreach(Role::all() as $role)
			<tr>
				<td>{{{ ucfirst($role->role_name) }}}</td>
				<td>
				@if($userBeingManaged->hasRole($role))
					<a href="/admin/users/{{ $userBeingManaged->id }}/roles/{{{ $role->id }}}/revoke">
						<button currentvalue="enabled" userid={{ $userBeingManaged->id }} class="btn btn-mini btn-info" type="button">
						Disable
						</button>
					</a>
				@else
					<a href="/admin/users/{{ $userBeingManaged->id }}/roles/{{{ $role->id }}}/grant">
						<button currentvalue="disabled" userid={{ $userBeingManaged->id }} class="btn btn-mini btn-warning" type="button">
						Enable
						</button>
					</a>
				@endif
				</td>
			</tr>
		@endforeach
	</tbody>
</table>
@stop

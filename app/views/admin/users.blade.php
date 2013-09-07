@extends('layout')

@section('title')
<title>Admin - Users</title>
@stop

@section('additionalScriptTags')
	<script type="text/javascript" src="/assets/js/jquery.tablesorter.min.js"></script> 
	<script type="text/javascript">
		$(document).ready(function () {
			$("#sort").tablesorter({widthFixed: false, sortList: [[0,1]]});
		});
	</script>
@stop

@section('customStyles')
	th.headerSortDown { 
		background-color: #3399FF; 
	} 

	th.headerSortUp { 
		background-color: #3399FF; 
	} 

	th.header { 
		cursor: pointer; 
		font-weight: bold; 
		background-repeat: no-repeat; 
		background-position: center left; 
		padding-left: 20px; 
		border-right: 1px solid #dad9c7; 
		margin-left: -1px; 
	} 
@stop

@section('content')
<h1>Manage Users</h1>
<hr/>
<table class="table table-condensed table-hover" id="sort">
	<thead>
		<tr>
			<th>id</th>
			<th>email/username</th>
			<th>last login</th>
			<th>signup date</th>
			<th>enable/disable</th>
			<th>manage roles</th>
		</tr>
	</thead>
	<tbody>
	@foreach($users as $user)
		<tr>
			<td>{{ $user->id }}</td>
			<td>{{{ $user->email }}}</td>
			<td>{{ $user->updated_at }}</td>
			<td>{{ $user->created_at }}</td>
			<td>
			@if(!empty($user->deleted_at))
				<a href="/admin/users/{{ $user->id }}/enable">
					<button currentvalue="disabled" userid={{ $user->id }} class="btn btn-mini btn-warning" type="button">
					Enable
					</button>
				</a>
			@else
				<a href="/admin/users/{{ $user->id }}/disable">
					<button currentvalue="enabled" userid={{ $user->id }} class="btn btn-mini btn-info" type="button">
					Disable
					</button>
			@endif
			</td>
			<td><a href="/admin/users/{{ $user->id }}/roles">Roles</a></td>
		</tr>
	@endforeach
	</tbody>
</table>

@stop

@extends('layouts.layout')

@section('content')

	<div class="container">

		<h1>Summarised login attempts</h1>

		<section>

			<h2>Last item</h2>

			<table class="table">
				<thead>
				<tr>
					<th>All</th>
					<th>Ip address</th>
					<th>Ip with 16 bit</th>
					<th>Ip with 24 bit</th>
					<th>Username</th>
				</tr>
				</thead>
				<tr>
					<td>{{$loginAttempts->all}}</td>
					<td>{{$loginAttempts->ip_address}}</td>
					<td>{{$loginAttempts->ip_16}}</td>
					<td>{{$loginAttempts->ip_24}}</td>
					<td>{{$loginAttempts->username}}</td>
				</tr>
			</table>

		</section>

	</div>

@stop
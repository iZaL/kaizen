@extends('layouts.scaffold')

@section('main')

<h1>Create Country</h1>
{{-- Edit Blog Form --}}
<!--<form class="form-horizontal" method="post" action="{{ URL::to('country/create') }}" autocomplete="off">-->
    {{ Form::open(array('route' => 'countries.create')) }}
    <!-- CSRF Token -->
	<ul>
        <li>
            {{ Form::label('name', 'Name:') }}
            {{ Form::text('name') }}
        </li>

		<li>
			{{ Form::submit('Submit', array('class' => 'btn btn-info')) }}
		</li>
	</ul>
    <!-- ./ form actions -->
</form>

@if ($errors->any())
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
@endif

@stop



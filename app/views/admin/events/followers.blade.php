@extends('admin.layouts.default')

{{-- Content --}}
@section('content')
<h1>Followers For {{ $event->title }}</h1>
@if(count($users))
<a href="{{ action('AdminEventsController@notifyFollowers',$event->id) }}"><button type="button" class="btn btn-default">Notify Followers</button></a>
<br>
@endif
@if(count($users))
<h3>Total {{count($users) }} Users Following This Event</h3>
@else
<h3>No Users are Following This Event Yet</h3>
@endif


<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <th>Username</th>
        <th>Email</th>
    </tr>
    </thead>

    <tbody>
    @foreach($users as $user)
    <tr>

        <td><a href="{{ action('UserController@getProfile',$user->id) }}">{{{ $user->username }}}</a></td>
        <td>{{{ $user->email }}} </td>

    </tr>
    @endforeach
    </tbody>
</table>

@stop
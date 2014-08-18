@extends('admin.master')

{{-- Web site Title --}}
@section('title')
Blog Post
@stop

{{-- Content --}}
@section('content')
	<div class="page-header">
		<h3>
			Add a Blog Post

			<div class="pull-right">
				<a href="{{ URL::action('AdminBlogsController@create') }}" class="btn btn-small btn-info iframe"><span class="glyphicon glyphicon-plus-sign"></span> Create</a>
			</div>
		</h3>
	</div>

    <table cellpadding="0" cellspacing="0" border="0" class="datatable table table-striped table-bordered">
        <thead>
			<tr>
				<th class="col-md-4">Blog Title</th>
				<th class="col-md-2">Created at</th>
				<th class="col-md-2">Actions</th>
			</tr>
		</thead>
		<tbody>
            @foreach($posts as $post)
            <tr>
                <td>{{ $post->title }}</td>
                <td>{{ $post->created_at }}</td>
                <td><a href="{{ URL::action('AdminBlogsController@edit', array($post->id)) }}" class="btn">Edit</a></td>
            </tr>
            @endforeach
		</tbody>
	</table>
@stop
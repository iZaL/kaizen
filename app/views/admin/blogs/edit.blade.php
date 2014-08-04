@extends('admin.master')

@section('content')
<h1>Edit Blog Post</h1>

{{ Form::model($post,array('method' => 'PATCH', 'action' => array('AdminBlogsController@update',$post->id), 'role'=>'form', 'files' => true)) }}
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('user_id', 'Author:',array('class'=>'control-label')) }}
            {{ Form::select('user_id', $author,NULL,array('class'=>'form-control')) }}
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('category_id', 'Category:') }}
            {{ Form::select('category_id', $category, NULL,array('class'=>'form-control')) }}
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <label class="control-label" for="title">Post Title in Arabic</label>
            {{ Form::text('title_ar', null, ['class' => 'form-control']) }}
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <label class="control-label" for="title">Post Title in English</label>
            {{ Form::text('title_en', null, ['class' => 'form-control']) }}
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <label class="control-label" for="content">Description in Arabic</label>
            {{ Form::textarea('description_ar', null, ['class' => 'form-control wysihtml5']) }}
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <label class="control-label" for="content">Description in English</label>
            {{ Form::textarea('description_en', null, ['class' => 'form-control wysihtml5']) }}
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <button type="submit" class="btn btn-success">Save</button>
        </div>
    </div>

</div>

{{ Form::close() }}
@if ($errors->any())
<div class="row">
    <div class="alert alert-danger">
        <ul>
            {{ implode('', $errors->all('<li class="error"> - :message</li>')) }}
        </ul>
    </div>
</div>
@endif
@stop

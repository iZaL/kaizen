@extends('site.layouts.home')
@section('maincontent')
<link rel="stylesheet" href="http://blueimp.github.io/Gallery/css/blueimp-gallery.min.css">
{{ HTML::style('css/bootstrap-image-gallery.min.css') }}
<div class="row">
    <div class="col-md-12">

    <!-- gallery Template Divisions that should be load each time we will use the gallery -->
        <div id="blueimp-gallery" class="blueimp-gallery">
            <!-- The container for the modal slides -->
            <div class="slides"></div>
            <!-- Controls for the borderless lightbox -->
            <h3 class="title"></h3>
            <a class="prev">‹</a>
            <a class="next">›</a>
            <a class="close">×</a>
            <a class="play-pause"></a>
            <ol class="indicator"></ol>
            <!-- The modal dialog, which will be used to wrap the lightbox content -->
            <div class="modal fade">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" aria-hidden="true">&times;</button>
                            <h4 class="modal-title"></h4>
                        </div>
                        <div class="modal-body next"></div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default pull-left prev">
                                <i class="glyphicon glyphicon-chevron-left"></i>
                                Previous
                            </button>
                            <button type="button" class="btn btn-primary next">
                                Next
                                <i class="glyphicon glyphicon-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- sidecontent division -->
<div class="row">
    <div class="col-md-7">
        <h1>
            @if ( LaravelLocalization::getCurrentLocaleName() == 'English')
            @if($event->title_en)
            {{ $event->title_en }}
            @else
            {{ $event->title }}
            @endif
            @else
            {{ $event->title }}
            @endif
        </h1>
    </div>

    <div class="col-md-5 {{ !Auth::user()? 'btns_disabled' :'' }}">
        <h1></h1>
        <button {{ !Auth::user()? 'disabled' :'' }} type="button" class="col-md-4 btn btn-default btn-sm events_btns favorite_btn"
        data-toggle="tooltip" data-placement="top" title="{{ Lang::get('site.event.favorite') }}">
        <i class="favorite glyphicon glyphicon-star {{ $favorited? 'active' :'' ;}}"></i></br>
        {{ Lang::get('site.general.fv_btn_desc')}}</button>

        <button
        {{ !Auth::user()? 'disabled' :'' }} type="button" class="col-md-4 events_btns btn btn-default btn-sm follow_btn"
        data-toggle="tooltip" data-placement="top" title="{{ Lang::get('site.event.follow') }}">
        <i class="follow glyphicon glyphicon-heart {{ $followed? 'active' :'' ;}}"></i> </br>
        {{ Lang::get('site.general.follow_btn_desc')}}</button>

        <button
        {{ !Auth::user()? 'disabled' :'' }} type="button" class="col-md-4 events_btns btn btn-default btn-sm subscribe_btn"
        data-toggle="tooltip" data-placement="top" title="{{ Lang::get('site.event.subscribe') }}">
        <i class="subscribe glyphicon glyphicon-check {{ $subscribed? 'active' :'' ;}}"></i>  </br>
        {{ Lang::get('site.general.subscribe_btn_desc')}}</button>
    </div>

</div>

<div class="row" id="event_images">
    <div id="links">
        @foreach($event->photos as $photo)
        <a href="{{ base_path().'/uploads/thumbnail/'.$photo->name }}" data-gallery>
            {{ HTML::image('uploads/thumbnail/'.$photo->name.'',$photo->name,array('class'=>'img-responsive img-thumbnail')) }}
        </a>
        @endforeach
    </div>
</div>
<br><br><br>

<div class="row">
    <div class="col-md-12">
        <table class="table table-striped">
            <tr>
                <h4>{{ Lang::get('site.event.summaryevent') }}</h4>
            </tr>
            <tr>
                <td><b>{{ Lang::get('site.event.totalseats') }}</b></td>
                <td> {{ $event->total_seats}}</td>
                <td> {{ Lang::get('site.event.seatsavail') }}</td>
                <td> {{ $event->available_seats}}</td>
            </tr>
            <tr>
                <td><b>{{ Lang::get('site.event.date_start') }}</b></td>
                <td> {{ $event->formatEventDate($event->date_start) }}</td>
                <td> {{ Lang::get('site.event.date_end') }}</td>
                <td> {{ $event->formatEventDate($event->date_end) }}</td>
            </tr>
            <tr>
                <td><b>{{ Lang::get('site.event.time_start') }}</b></td>
                <td> {{ $event->formatEventTime($event->time_start) }}</td>
                <td> {{ Lang::get('site.event.time_end') }}</td>
                <td> {{ $event->formatEventTime($event->time_end) }}</td>
            </tr>
        </table>
    </div>

    <div class="col-md-12">
        <p>
            @if ( LaravelLocalization::getCurrentLocaleName() == 'English')
            @if($event->description_en)
            {{ $event->description_en }}
            @else
            {{ $event->description }}
            @endif
            @else
            {{ $event->description }}
            @endif
        </p>
    </div>

    @if($event->latitude && $event->longitude)
    <div id="map_canvas"></div>
    @endif
    <div class="col-md-12">
        <option selected disabled>first option</option>
        <address>
            <strong> {{ $event->address}} </strong><br>
            795 Folsom Ave, Suite 600<br>
            San Francisco, CA 94107<br>
            <abbr title="Phone">P:</abbr> (123) 456-7890
        </address>
    </div>

    <div class="col-md-12 {{ !Auth::user()? 'btns_disabled' :'' }}">
        <button
        {{ !Auth::user()? 'disabled' :'' }} type="button" class="col-md-offset-3 col-md-2 col-sm-4 btn btn-default btn-sm events_btns favorite_btn"
        data-toggle="tooltip" data-placement="top" title="{{ Lang::get('site.event.favorite') }}">
        <i class="favorite glyphicon glyphicon-star {{ $favorited? 'active' :'' ;}}"></i></br>
        {{ Lang::get('site.general.fv_btn_desc')}}</button>

        <button
        {{ !Auth::user()? 'disabled' :'' }} type="button" class="col-md-2 col-sm-4 events_btns btn btn-default btn-sm follow_btn"
        data-toggle="tooltip" data-placement="top" title="{{ Lang::get('site.event.follow') }}">
        <i class="follow glyphicon glyphicon-heart {{ $followed? 'active' :'' ;}}"></i> </br>
        {{ Lang::get('site.general.follow_btn_desc')}}</button>

        <button
        {{ !Auth::user()? 'disabled' :'' }} type="button" class="col-md-2 col-sm-4 events_btns btn btn-default btn-sm subscribe_btn"
        data-toggle="tooltip" data-placement="top" title="{{ Lang::get('site.event.subscribe') }}">
        <i class="subscribe glyphicon glyphicon-check {{ $subscribed? 'active' :'' ;}}"></i>  </br>
        {{ Lang::get('site.general.subscribe_btn_desc')}}</button>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        @if(count($event->comments) > 0)
        <h3 class="comments_title"> {{Lang::get('site.event.comment') }}</h3>
        @foreach($event->comments as $comment)
        <div class="comments_dev">
            <p class="text-muted">
                {{ $comment->content }}
            </p>

            <p
            @if ( LaravelLocalization::getCurrentLocaleName() == 'English')
            class="text-right text-primary"
            @else
            class="text-left text-primary"
            @endif
            >{{ $comment->user->username}}</p>
        </div>
        @endforeach
        @endif
    </div>
    <div class="col-md-12">
        @if(Auth::User())
            {{ Form::open(array( 'action' => array('CommentsController@store', $event->id),'class'=>'row')) }}
                <div class="form-group">
                    <label for="comment"></label>
                    <textarea type="text" style="width: 97%;" class="form-control" id="content" name="content"
                              placeholder="{{ Lang::get('site.event.comment')}}"></textarea>
                </div>
                <button type="submit" class="btn btn-default"> {{ Lang::get('site.event.addcomment') }}</button>
            {{ Form::close() }}
        @endif
        @if ($errors->any())
            <ul> {{ implode('', $errors->all('  <li class="error">:message</li> ')) }} </ul>
        @endif
    </div>
</div>

<script src="http://blueimp.github.io/Gallery/js/jquery.blueimp-gallery.min.js"></script>
<script src="{{ asset('js/bootstrap-image-gallery.js') }}"></script>

<script>
    var id = '<?php echo $event->id; ?>';
</script>

@if($event->latitude && $event->longitude)
<script>
    function initialize() {
        var myLatlng = new google.maps.LatLng({
        {
            $event - > latitude
        }
    }
    ,
    {
        {
            $event - > longitude
        }
    }
    )
    ;
    var myOptions = {
        zoom: 10,
        center: myLatlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    }

    function loadScript() {
        var script = document.createElement("script");
        script.type = "text/javascript";
        script.src = "http://maps.google.com/maps/api/js?sensor=false&callback=initialize";
        document.body.appendChild(script);
    }

    window.onload = loadScript;

</script>
@endif
@stop
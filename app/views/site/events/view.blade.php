<!-- Extends From Two Column Layou -->
@extends('site.layouts._two_column')

@section('style')
    @parent
    {{ HTML::style('http://blueimp.github.io/Gallery/css/blueimp-gallery.min.css') }}
    {{ HTML::style('css/bootstrap-image-gallery.min.css') }}
@stop

@section('script')
    @parent
    {{ HTML::script('https://maps.googleapis.com/maps/api/js?key=AIzaSyAvY9Begj4WZQpP8b6IGFBACdnUhulMCok&sensor=false') }}
    {{ HTML::script('http://blueimp.github.io/Gallery/js/jquery.blueimp-gallery.min.js') }}
    {{ HTML::script('js/bootstrap-image-gallery.js') }}
    {{ HTML::script('js/app.js') }}

    <script>
        var id = '<?php echo $event->id; ?>';

        function toggleTooltip(action) {
            switch (action) {
                case 'favorite':
                    var ttip = '{{ trans('site.event.unfavorite ') }}'
                    $('.favorite_btn')
                        .attr('title', ttip)
                        .tooltip('fixTitle');
                    break;
                case 'unfavorite':
                    var ttip = '{{ trans('site.event.favorite') }}'
                    $('.favorite_btn')
                        .attr('title', ttip)
                        .tooltip('fixTitle');
                    break;
                case 'follow':
                    var ttip = '{{ trans('site.event.unfollow') }}'
                    $('.follow_btn')
                        .attr('title', ttip)
                        .tooltip('fixTitle');
                    break;
                case 'unfollow':
                    var ttip = '{{ trans('site.event.follow') }}'
                    $('.follow_btn')
                        .attr('title', ttip)
                        .tooltip('fixTitle');
                    break;
                case 'subscribe':
                    var ttip = '{{ trans('site.event.unsubscribe') }}'
                    $('.subscribe_btn')
                        .attr('title', ttip)
                        .tooltip('fixTitle');
                    break;
                case 'unsubscribe':
                    var ttip = '{{ trans('site.event.subscribe') }}'
                    $('.subsribe_btn')
                        .attr('title', ttip)
                        .tooltip('fixTitle');
                    break;
                default:
            }
        }

    </script>
    @if($event->latitude && $event->longitude)
        <script>
            var latitude = '<?php echo $event->latitude?>';
            var longitude = '<?php echo $event->longitude ?>';
            function initialize() {
                var myLatlng = new google.maps.LatLng(latitude, longitude);
                var mapOptions = {
                    zoom: 10,
                    center: myLatlng,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                }
                var map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
                var marker = new google.maps.Marker({
                    position: myLatlng,
                    map: map
                });

                // collapse the map div
                $('.collapse').collapse();
            }
            google.maps.event.addDomListener(window, 'load', initialize);
        </script>
    @endif

@stop

<!-- Content Section -->
@section('content')
    @parent

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
                {{ $event->title }}
            </h1>
        </div>

        <div class="col-md-5">

            <div class="row">

                <div class="col-md-12 col-sm-12 col-xs-12">

                    @if( !$subscribed)

                        <a href="{{  URL::action('EventsController@showSubscriptionOptions', array('id'=>$event->id)) }}"/>
                            <button type="submit" class=" col-md-12 col-sm-12 col-xs-12 events_btns btn btn-default btn-sm subscribe_btn bg-blue "
                                data-toggle="tooltip" data-placement="top" title="{{  trans('site.event.subscribe')  }}">
                                <i class="subscribe glyphicon glyphicon-check "></i>  </br>
                                <span class="buttonText">
                                {{ trans('site.event.subscribe')  }}
                                </span>
                            </button>
                        </a>
                    @else

                        {{-- If Subscribed --}}
                        @if($canWatchOnline)
                            {{-- If Can Watch Online --}}
                            <a href="{{  URL::action('EventsController@streamEvent', array('id'=>$event->id)) }}"/>
                                <button type="submit" class=" col-md-12 col-sm-12 col-xs-12 events_btns btn btn-default btn-sm subscribe_btn bg-green "
                                     data-toggle="tooltip" data-placement="top" title="{{ trans('site.event.online')  }}">
                                    <i class="subscribe glyphicon glyphicon-check"></i>  </br>
                                    <span class="buttonText">
                                       {{ trans('site.event.online')  }}
                                    </span>
                                </button>
                            </a>
                        {{-- If Event has started --}}
                            {{--  and  expired --}}
                        @elseif($eventExpired)
                            {{ Form::open(['class' => 'form', 'method' => 'post', 'action' => ['EventsController@reorganizeEvents', $event->id]]) }}
                                <button type="submit" class=" col-md-12 col-sm-12 col-xs-12 events_btns btn btn-default btn-sm subscribe_btn bg-blue "
                                    data-toggle="tooltip" data-placement="top" title="{{ trans('site.event.reorganize')  }}">
                                    <i class="subscribe glyphicon glyphicon-check"></i>  </br>
                                    <span class="buttonText">
                                       {{ trans('site.event.reorganize')  }}
                                    </span>
                                </button>
                            {{ Form::close() }}
                        @else
                            <a href="{{  URL::action('SubscriptionsController@unsubscribe', array('id'=>$event->id)) }}"/>
                                <button type="submit" class=" col-md-12 col-sm-12 col-xs-12 events_btns btn btn-default btn-sm subscribe_btn bg-blue "
                                    data-toggle="tooltip" data-placement="top" title="{{ trans('site.event.unsubscribe')  }}">
                                    <i class="subscribe glyphicon glyphicon-check active "></i>  </br>
                                    <span class="buttonText">
                                        {{  trans('site.event.unsubscribe_btn_desc')  }}
                                    </span>
                                </button>
                            </a>
                        @endif

                    @endif
                    {{--  Show Favorite, Subscription Buttons--}}
                    <button type="button" class="col-md-6 col-sm-6 col-xs-6 events_btns btn btn-default btn-sm follow_btn bg-blue top5"
                        data-toggle="tooltip" data-placement="top" title="{{ $followed? trans('site.event.unfollow') : trans('site.event.follow') }}">
                        <i class="follow glyphicon glyphicon-heart {{ $followed? 'active' :'' ;}}"></i> </br>
                        {{ trans('site.general.follow_btn_desc')}}
                    </button>

                    <button type="button" class="col-md-6 col-sm-6 col-xs-6 events_btns btn btn-default btn-sm favorite_btn bg-blue top5"
                        data-toggle="tooltip" data-placement="top" title="{{ $favorited? trans('site.event.unfavorite') : trans('site.event.favorite') }}">
                        <i class="favorite glyphicon glyphicon-star {{ $favorited? 'active' :'' ;}}"></i></br>
                        {{ trans('site.general.fv_btn_desc') }}
                    </button>

                </div>

            </div>
        </div>
    </div>

    @if(count($event->photos))
        <div class="row" id="event_images">
            <div id="links">
                @foreach($event->photos as $photo)
                    <a href="#" data-gallery>
                        {{ HTML::image('uploads/thumbnail/'.$photo->name.'',$photo->name,array('class'=>'img-responsive img-thumbnail')) }}
                    </a>
                @endforeach
            </div>
        </div>
        <br><br><br>
    @endif

    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped">
                <tr>
                    <h4>{{ trans('site.event.summaryevent') }}</h4>
                </tr>
                <tr>
                    <td><b>{{ trans('site.general.country') }} </b></td>
                    <td> {{ $event->location->country->name }} </td>
                    <td><b>{{ trans('site.general.location') }}</b></td>
                    <td> {{ $event->location->name }} </td>
                </tr>

                <tr>
                    <td><b>{{ trans('site.event.date_start') }}</b></td>
                    <td> {{ $event->formatEventDate($event->date_start) }}</td>
                    <td><b> {{ trans('site.event.date_end') }} </b></td>
                    <td> {{ $event->formatEventDate($event->date_end) }}</td>
                </tr>
                
                @if($event->phone || $event->email)
                    <tr>
                        @if($event->phone)
                            <td><b>{{ trans('site.general.phone') }}</b></td>
                            <td> {{ $event->phone }}</td>
                        @endif
                        @if($event->email)
                            <td><b>{{ trans('site.general.email') }}</b></td>
                            <td> {{ $event->email }}</td>
                        @endif
                    </tr>
                @endif
                <tr>
                    <td><b>{{ trans('site.event.price') }}</b></td>
                    @if($event->price)
                        <td>{{ $event->price }}</td>
                    @else
                        <td>{{ trans('site.event.free') }}</td>
                    @endif
                </tr>
            </table>
        </div>

        @if($event->latitude && $event->longitude)
            <div class="col-md-12">
                <button type="button" class="btn btn-default" data-toggle="collapse" data-target="#map">
                    <b><i class="glyphicon glyphicon-map-marker"></i> Show / Hide Map </b>
                </button>
                <div id="map" class="collapse in top10">
                    <div id="map_canvas"></div>
                </div>
            </div>
        @endif

        <div class="col-md-12 top15">
            <b>{{ trans('site.general.address') }} </b>
            <address>
                <strong>
                    {{ $event->address }}
                    -
                    {{ $event->street }}
                </strong>
                <br>
                @if($event->phone)
                    <abbr title="Phone">Phone:</abbr>
                    {{ $event->phone }}
                @endif
            </address>
        </div>

        <div class="col-md-12 col-sm-12 col-xs-12">
            <!-- Tags Element -->
            @if($tags)
                <div class="row" style="padding:20px;">
                    @for($i=0; $i < count($tags); $i++)
                        <a href="{{ action('TagsController@show', $tags[$i]->id) }}">
                            <button type="button" class="btn btn-default btn-sm">
                                <span class="glyphicon glyphicon-tags"></span>
                                {{ $tags[$i]->title}}
                            </button>
                            </a>
                    @endfor
                </div>
            @endif


            @if( !$subscribed)
                <a href="{{  URL::action('EventsController@showSubscriptionOptions', array('id'=>$event->id)) }}"/>
                    <button type="submit" class="col-md-12 col-sm-12 col-xs-12 btn btn-default btn-sm subscribe_btn "
                        data-toggle="tooltip" data-placement="top" title="{{  trans('site.event.subscribe')  }}">
                        <i class="subscribe glyphicon glyphicon-check "></i>  </br>
                        <span class="buttonText">
                        {{ trans('site.event.subscribe')  }}
                        </span>
                    </button>
                </a>
            @else
                {{-- If Subscribed --}}

                @if($canWatchOnline)
                    {{-- If Can Watch Online --}}
                    <a href="{{  URL::action('EventsController@streamEvent', array('id'=>$event->id)) }}"/>
                        <button type="submit" class="col-md-12 col-sm-12 col-xs-12 btn btn-default btn-sm subscribe_btn "
                             data-toggle="tooltip" data-placement="top" title="{{ trans('site.event.online')  }}">
                            <i class="subscribe glyphicon glyphicon-check"></i>  </br>
                            <span class="buttonText">
                               {{ trans('site.event.online')  }}
                            </span>
                        </button>
                    </a>
                @elseif($eventExpired)
                    {{-- If Event is Expired--}}

                    {{ Form::open(['class' => 'form', 'method' => 'post', 'action' => ['EventsController@reorganizeEvents', $event->id]]) }}
                        <button type="submit" class="col-md-12 col-sm-12 col-xs-12 btn btn-default btn-sm subscribe_btn "
                            data-toggle="tooltip" data-placement="top" title="{{ trans('site.event.reorganize')  }}">
                            <i class="subscribe glyphicon glyphicon-check"></i>  </br>
                            <span class="buttonText">
                               {{ trans('site.event.reorganize')  }}
                            </span>
                        </button>
                    {{ Form::close() }}
                @else
                    <a href="{{  URL::action('SubscriptionsController@unsubscribe', array('id'=>$event->id)) }}"/>
                        <button type="submit" class="col-md-12 col-sm-12 col-xs-12 btn btn-default btn-sm subscribe_btn "
                            data-toggle="tooltip" data-placement="top" title="{{ $subscribed? trans('site.event.unsubscribe') : trans('site.event.subscribe')  }}">
                            <i class="subscribe glyphicon glyphicon-check {{ $subscribed? 'active' :'' ;}}"></i>  </br>
                            <span class="buttonText">
                                {{ $subscribed? trans('site.event.unsubscribe_btn_desc') : trans('site.event.subscribe')  }}
                            </span>
                        </button>
                    </a>

                @endif

            @endif

        </div>
    </div>
    <hr>

    <div class="row">
        <div class="col-md-12">
            @if(count($event->comments) > 0)
                <h3><i class=" glyphicon glyphicon-comment"></i>&nbsp;{{trans('site.event.comment') }}</h3>
                @foreach($event->comments as $comment)
                    <div class="comments_dev">
                        <p>{{ $comment->content }}</p>
                        <p
                        @if ( App::getLocale() == 'en')
                            class="text-left text-primary"
                        @else
                            class="text-right text-primary"
                        @endif
                        >{{ $comment->user->username}}
                        <span class="text-muted"> - {{ $comment->created_at }} </span></p>
                    </div>
                @endforeach
            @endif
        </div>
        <div class="col-md-12">
            @if(Auth::User())
                {{ Form::open(array( 'action' => array('CommentsController@store', $event->id))) }}
                    {{ Form::hidden('commentable_id',$event->id)}}
                    {{ Form::hidden('commentable_type','EventModel')}}
                    <div class="form-group">
                        <label for="comment"></label>
                        <textarea type="text" class="form-control" id="content" name="content" placeholder="{{ trans('site.event.comment')}}"></textarea>
                    </div>
                    <button type="submit" class="btn btn-default"> {{ trans('site.event.addcomment') }}</button>
                {{ Form::close() }}
            @endif
            @if ($errors->any())
                <ul> {{ implode('', $errors->all('<li class="error">:message</li> ')) }} </ul>
            @endif
        </div>
    </div>

@stop
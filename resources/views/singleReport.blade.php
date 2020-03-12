@extends('spark::layouts.app')


@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    {{--<home :user="user" inline-template>--}}
    <div class="container-fluid">
        @include('widgets.sidebar')
        <div class="col-md-10">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="col-sm-6">
                        <h3 class="noMargin"><strong>{{$url['title']}}</strong></h3>
                        <br>
                        <p class="noMargin"><a href="{{$url['url']}}">{{$url['url']}}</a></p>
                        @if($url['url_android'] != '')
                            <p class="noMargin"><strong>For Android User : </strong><a
                                        href="{{$url['url_android']}}">{{$url['url_android']}}</a></p>
                        @endif
                        @if($url['url_ios'] != '')
                            <p class="noMargin"><strong>For iOS User : </strong><a
                                        href="{{$url['url_ios']}}">{{$url['url_ios']}}</a></p>
                        @endif
                        <a class="label label-info"
                           href="{{env('APP_URL').'/'.$url['short']}}">{{env('APP_URL').'/'.$url['short']}}</a>
                        <br><br>
                        <table class="table table-bordered table-striped">
                            <tr>
                                <td><strong>Today Visit </strong></td>
                                <td width="100px" class="text-center">{{$url['report'][6]}}</td>
                            </tr>
                            <tr>
                                <td><strong>Yesterday Visit </strong></td>
                                <td width="100px" class="text-center">{{$url['report'][5]}}</td>
                            </tr>
                            <tr>
                                <td><strong>Total Visit </strong></td>
                                <td width="100px" class="text-center">{{$url['visit']}}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-sm-6 noPadding">
                        <div id="thisChart" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-body">
                    <table class="table table-bordered table-stripped table-hover noMarginBottom">
                        <thead>
                        <tr class="active">
                            <th class="text-center">Source</th>
                            <th class="text-center">Today</th>
                            <th class="text-center">Yesterday</th>
                            <th class="text-center">Visit (Total)</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($sourceInfo as $source)
                            <tr>
                                <td class="text-sm text-center">{{$source['title']}}</td>
                                <td class="text-center">{{$source['report'][6]}}</td>
                                <td class="text-center">{{$source['report'][5]}}</td>
                                <td class="text-center">{{$source['visit']}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-body">
                    <table class="table table-bordered table-stripped table-hover noMarginBottom">
                        <thead>
                        <tr class="active">
                            <th>Shortened Url</th>
                            <th class="text-center">Source</th>
                            <th class="text-center">Medium</th>
                            <th class="text-center">content</th>
                            <th class="text-center">Today</th>
                            <th class="text-center">Yesterday</th>
                            <th class="text-center">Visit (Total)</th>
                            <th class="text-center">Active</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $repo = 1; ?>
                        @foreach($child as $urlC)
                            <tr>
                                <td class="text-sm">
                                    @if($urlC['domain_id'] > 0)
                                        <a class="urlWidth" target="_blank"
                                           href="{{$urlC['domain'].'/'.$urlC['short']}}">{{$urlC['domain'].'/'.$urlC['short']}}</a>
                                    @else
                                        <a class="urlWidth" target="_blank"
                                           href="{{env('APP_URL').'/'.$urlC['short']}}">{{env('APP_URL').'/'.$urlC['short']}}</a>
                                    @endif
                                </td>
                                <td class="text-sm text-center">{{substr($urlC['source'], 0, 20)}} @if(strlen($urlC['source']) > 20) ... @endif</td>
                                <td class="text-sm text-center">{{substr($urlC['media'], 0, 20)}} @if(strlen($urlC['media']) > 20) ... @endif</td>
                                <td class="text-sm text-center">{{substr($urlC['content'], 0, 20)}} @if(strlen($urlC['content']) > 20) ... @endif</td>
                                <td class="text-center">{{$urlC['report'][6]}}</td>
                                <td class="text-center">{{$urlC['report'][5]}}</td>
                                <td class="text-center">{{$urlC['visit']}}</td>
                                <td class="text-center">
                                    @if($repo > 1)
                                        <a class="btn btn-sm" onclick="openRemoveModal('{{$urlC['id']}}')"><i
                                                    class="fa fa-fw fa-trash text-danger"></i></a>
                                    @endif
                                </td>
                            </tr>
                            <?php $repo = $repo + 1; ?>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal fade" id="removeUrlModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form onsubmit="removeShortUrl(event)">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                            aria-hidden="true">&times;</span></button>
                                <h5 class="modal-title">Remove Url</h5>
                            </div>
                            <div class="modal-body">
                                <h4 class="text-center">
                                    Are you really want to remove this url?
                                </h4>
                            </div>
                            <div class="modal-footer">
                                {{ csrf_field() }}
                                <input type="hidden" name="id" value="">
                                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="application/javascript" src="{{env('APP_URL')}}/js/report.js?_token={{uniqid()}}"></script>
    <script type="application/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="application/javascript">
        $(function () {
            var v = '{{json_encode($url['report'], true)}}';
            v = JSON.parse(v);
            google.charts.load('current', {'packages': ['corechart']});
            google.charts.setOnLoadCallback(function () {
                var data = google.visualization.arrayToDataTable([
                    ['Day', 'visit'],
                    ['', v[0]],
                    ['', v[1]],
                    ['', v[2]],
                    ['', v[3]],
                    ['', v[4]],
                    ['', v[5]],
                    ['', v[6]]
                ]);
                var options = {
                    hAxis: {title: 'Last 7 Days', titleTextStyle: {color: '#333'}},
                    vAxis: {minValue: 0}
                };
                var chart = new google.visualization.AreaChart(document.querySelector('#thisChart'));
                chart.draw(data, options);
            });
        });
    </script>
    {{--</home>--}}
@endsection

@extends('spark::layouts.app')


@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    {{--<home :user="user" inline-template>--}}
        <div class="container-fluid">
            <!-- Application Dashboard -->
            <div>
{{--                @include('widgets.sidebar')--}}
                <div class="col-md-10 col-md-offset-1">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <form class="form" method="POST" onsubmit="generateShortUrl(event)">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="col-sm-12 noPadding text-sm">
                                            Destination Url
                                            <a class="pull-right" onclick="openCreateModal()"><i class="fa fa-fw fa-plus-circle fa-2x"></i></a>
                                        </label>
                                        <select name="url_id" required class="form-control input-sm urlDrop">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="col-sm-12 noPadding text-sm">
                                            Sources
                                            <a class="pull-right"><i class="fa fa-fw fa-plus-circle-0 fa-2x">&nbsp;</i></a>
                                        </label>
                                        <select name="source_id" onchange="changeSourceMedia(this)" required class="form-control input-sm sourceDrop">
                                            <option value="0">None</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="col-sm-12 noPadding text-sm">
                                            Medium
                                            <a class="pull-right"><i class="fa fa-fw fa-plus-circle-0 fa-2x">&nbsp;</i></a>
                                        </label>
                                        <select name="media_id" required class="form-control input-sm mediaDrop">
                                            <option value="0">None</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="col-sm-12 noPadding text-sm">
                                            Content
                                            <a class="pull-right" onclick="createContentModal(event)"><i class="fa fa-fw fa-plus-circle fa-2x"></i></a>
                                        </label>
                                        <select name="content_id" class="form-control input-sm contentDrop">
                                            <option value="0">None</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group text-center">
                                        <label class="col-sm-12 noPadding text-sm">
                                            &nbsp;
                                            <a class="pull-right" href="#"><i class="fa fa-fw fa-plus-circle-0 fa-2x">&nbsp;</i></a>
                                        </label>
                                        <button type="submit" class="btn btn-primary btn-block">Generate</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <table class="table table-bordered table-stripped table-hover noMarginBottom">
                                <thead>
                                <tr class="active">
                                    {{--<th class="text-center">#</th>--}}
                                    <th>Title</th>
                                    <th>Destination Url</th>
                                    {{--<th>Shortened Url</th>--}}
                                    {{--<th>Category</th>--}}
                                    <th class="text-center">Today</th>
                                    <th class="text-center">Yesterday</th>
                                    <th class="text-center">Visit (Total)</th>
                                    <th class="text-center">Graph</th>
                                    {{--<th class="text-center">Actions</th>--}}
                                </tr>
                                </thead>
                                <tbody id="app-url-preview">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="generateShortLinkModal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h5 class="modal-title">Generated Short Link</h5>
                            </div>
                            <div class="modal-body">
                                <div class="GCL" id="GCL">
                                    <div class="cssload-container">
                                        <div class="cssload-whirlpool"></div>
                                        <p class="text-center text-sm">Generating Shortened Link</p>
                                    </div>
                                </div>
                                <div class="GCLF">
                                    <div class="col-md-12">
                                        <form  method="POST" onsubmit="event.preventDefault()">
                                            <div class="form-group">
                                                <label class="text-sm">Set Domain</label>
                                                <select name="domain_id" onchange="generateDomainShortUrl()" required class="form-control input-sm domainDrop">
                                                    <option value="0">None</option>
                                                </select>
                                                <input id="popDomainShort" type="hidden" name="url_id" value="">
                                            </div>
                                        </form>
                                    </div>
                                    <input class="copyToClipInput genUri" id="genUri"/>
                                    <div class="margin-10"></div>
                                    <div class="text-center">
                                        <a class="btn btn-default copyToClip" onclick="copyToClip(event)">Copy Link</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="modal fade" id="createUrlModal">
                    <div class="modal-dialog modal-md">
                        <div class="modal-content">
                            <form class="form" method="POST" onsubmit="createShortUrl(event)">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                                aria-hidden="true">&times;</span></button>
                                    <h5 class="modal-title">Create Link</h5>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label class="text-sm">Destination Url <span class="text-danger">*</span></label>
                                        <input type="url" name="url" required class="form-control input-sm"
                                               placeholder="Destination Url">
                                    </div>
                                    <div class="form-group">
                                        <label class="text-sm">Android Destination Url <small>(optional)</small></label>
                                        <input type="url" name="url_android" class="form-control input-sm"
                                               placeholder="Destination Url (Android)">
                                    </div>
                                    <div class="form-group">
                                        <label class="text-sm">iOS Destination Url <small>(optional)</small></label>
                                        <input type="url" name="url_ios" class="form-control input-sm"
                                               placeholder="Destination Url (iOS)">
                                    </div>
                                    <div class="form-group">
                                        <label class="text-sm">Url Title <span class="text-danger">*</span></label>
                                        <input type="text" name="title" required class="form-control input-sm"
                                               placeholder="Url Title">
                                    </div>
                                    <div class="form-group">
                                        <label class="text-sm">Category <span class="text-danger">*</span></label>
                                        <select name="cat_id" required class="form-control catDrop input-sm">
                                        </select>
                                    </div>
                                    <div class="checkbox">
                                        <label for="customCheck">
                                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checkbox" value="1" onchange="changeClickPop()"> 
                                            Include Click Pop
                                        </label>
                                    </div>
                                    <div class="form-group" style="display:none">
                                        <label for="hide">Click Pop Script</label>
                                        <textarea class="form-control rounded-0" id="hide" rows="5" style="resize: vertical" name="write_script" placeholder="Click Pop Script"></textarea>
                                    </div>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-sm btn-primary">Create Link</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="createContentModal">
                    <div class="modal-dialog modal-md">
                        <div class="modal-content">
                            <form onsubmit="createQuickContent(event)">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                                aria-hidden="true">&times;</span></button>
                                    <h5 class="modal-title">Create New Content</h5>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Content Title</label>
                                        <input type="text" name="title" required class="form-control"
                                               placeholder="Content Title">
                                    </div>
                                    {{--<div class="form-group">
                                        <label>Select Platform</label>
                                        <select name="source_id" required class="form-control sourceDrop">
                                        </select>
                                    </div>--}}
                                </div>
                                <div class="modal-footer">
                                    {{ csrf_field() }}
                                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close
                                    </button>
                                    <button type="submit" class="btn btn-sm btn-primary">Create</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <script type="application/javascript" src="{{env('APP_URL')}}/js/dash.js?_token={{uniqid()}}"></script>
        </div>
    {{--</home>--}}
@endsection

@extends('spark::layouts.app')


@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    {{--<home :user="user" inline-template>--}}
    <div class="container-fluid">
        <!-- Application Dashboard -->
        <div>
            @include('widgets.sidebar')
            <div class="col-md-10">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="text-right">
                            <a class="btn btn-sm btn-info" href="{{env('APP_URL')}}/asset/category"><strong>Manage
                                    Your Url Category</strong></a>
                            <a class="btn btn-sm btn-primary" onclick="openCreateModal()"><strong>Create Shortened
                                    Url</strong></a>
                        </div>
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
                                <th class="text-center">Category</th>
                                {{--<th class="text-center">Today</th>--}}
                                {{--<th class="text-center">Yesterday</th>--}}
                                {{--<th class="text-center">Visit (Total)</th>--}}
                                <th class="text-center">Actions</th>
                            </tr>
                            </thead>
                            <tbody id="app-url-preview">
                            </tbody>
                        </table>
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
            <div class="modal fade" id="editUrlModal">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <form onsubmit="updateShortUrl(event)">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                            aria-hidden="true">&times;</span></button>
                                <h5 class="modal-title">Edit Url Information</h5>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label class="text-sm">Destination Url  <span class="text-danger">*</span></label>
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
                                    <label class="text-sm">Url Title  <span class="text-danger">*</span></label>
                                    <input type="text" name="title" required class="form-control input-sm"
                                           placeholder="Url Title">
                                </div>
                                <div class="form-group">
                                    <label class="text-sm">Category  <span class="text-danger">*</span></label>
                                    <select name="cat_id" required class="form-control catDrop input-sm">
                                    </select>
                                </div>
                                <div class="checkbox">
                                    <label for="customCheck">
                                        <input type="checkbox" class="custom-control-input" id="customCheck" name="checkbox" value="1" onchange="changeClickPopEdit()"> 
                                        Include Click Pop
                                    </label>
                                </div>
                                <div class="form-group" style="display:none">
                                    <label for="hide">Click Pop Script</label>
                                    <textarea class="form-control rounded-0" id="hide" rows="5" style="resize: vertical" name="write_script" placeholder="Click Pop Script"></textarea>
                                </div>
                                  
                            </div>
                            <div class="modal-footer">
                                {{ csrf_field() }}
                                <input type="hidden" name="id" value="">
                                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-sm btn-primary">Save changes</button>
                            </div>
                        </form>
                    </div>
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
        <script type="application/javascript" src="{{env('APP_URL')}}/js/url.js?_token={{uniqid()}}"></script>
    </div>
    {{--</home>--}}
@endsection

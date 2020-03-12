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
                        <h3 class="pull-left noMargin">Medium</h3>
                        <div class="text-right">
                            <a class="btn btn-primary" onclick="createMediaModal()"><i class="fa fa-fw fa-plus"></i>
                                Create New</a>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <table class="table table-bordered table-stripped table-hover noMarginBottom">
                            <thead>
                            <tr class="active">
                                <th class="text-center">#</th>
                                <th>Source</th>
                                <th>Medium</th>
                                <th class="text-center">Actions</th>
                            </tr>
                            </thead>
                            <tbody id="app-media-preview">
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal fade" id="createMediaModal">
                    <div class="modal-dialog modal-md">
                        <div class="modal-content">
                            <form onsubmit="createMedia(event)">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                                aria-hidden="true">&times;</span></button>
                                    <h5 class="modal-title">Create New Medium</h5>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Medium Title</label>
                                        <input type="text" name="title" required class="form-control"
                                               placeholder="Medium Title">
                                    </div>
                                    <div class="form-group">
                                        <label>Select Source</label>
                                        <select name="source_id" required class="form-control sourceDrop">
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close
                                    </button>
                                    <button type="submit" class="btn btn-sm btn-primary">Create</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="editMediaModal">
                    <div class="modal-dialog modal-md">
                        <div class="modal-content">
                            <form onsubmit="updateMedia(event)">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                                aria-hidden="true">&times;</span></button>
                                    <h5 class="modal-title">Edit Medium</h5>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Medium Title</label>
                                        <input type="text" name="title" required class="form-control"
                                               placeholder="Medium Title">
                                    </div>
                                    <div class="form-group">
                                        <label>Select Source</label>
                                        <select name="source_id" required class="form-control sourceDrop">
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="id" value="">
                                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close
                                    </button>
                                    <button type="submit" class="btn btn-sm btn-primary">Save changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="removeMediaModal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form onsubmit="removeMedia(event)">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                                aria-hidden="true">&times;</span></button>
                                    <h5 class="modal-title">Remove Medium</h5>
                                </div>
                                <div class="modal-body">
                                    <h4 class="text-center">
                                        Are you really want to remove this Medium?
                                    </h4>
                                </div>
                                <div class="modal-footer">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="id" value="">
                                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close
                                    </button>
                                    <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script type="application/javascript" src="{{env('APP_URL')}}/js/media.js?_token={{uniqid()}}"></script>
    </div>
    {{--</home>--}}
@endsection

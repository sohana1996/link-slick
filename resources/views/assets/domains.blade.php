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
                        <h3 class="pull-left noMargin">Domains</h3>
                        <div class="text-right">
                            <a class="btn btn-primary" onclick="createMediaModal()"><i class="fa fa-fw fa-plus"></i>
                                Add New</a>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <table class="table table-bordered table-stripped table-hover noMarginBottom">
                            <thead>
                            <tr class="active">
                                <th class="text-center">#</th>
                                <th>Domain Name</th>
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
                                    <h5 class="modal-title">Add New Domain</h5>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Domain</label>
                                        <input type="url" name="title" required class="form-control"
                                               placeholder="Domain">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close
                                    </button>
                                    <button type="submit" class="btn btn-sm btn-primary">Add Now</button>
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
                                    <h5 class="modal-title">Edit Domain</h5>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Domain</label>
                                        <input type="text" name="title" required class="form-control"
                                               placeholder="Domain">
                                    </div>
                                </div>
                                <div class="modal-footer">
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
                                    <h5 class="modal-title">Remove Domain</h5>
                                </div>
                                <div class="modal-body">
                                    <h4 class="text-center">
                                        Are you really want to remove this domain?
                                    </h4>
                                </div>
                                <div class="modal-footer">
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
        <script type="application/javascript" src="{{env('APP_URL')}}/js/domain.js?_token={{uniqid()}}"></script>
    </div>
    {{--</home>--}}
@endsection

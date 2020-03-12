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
                        <h3 class="pull-left noMargin">Source</h3>
                        <div class="text-right">
                            <a class="btn btn-primary" onclick="createSourceModal()"><i class="fa fa-fw fa-plus"></i> Create New</a>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <table class="table table-bordered table-stripped table-hover noMarginBottom">
                            <thead>
                            <tr class="active">
                                <th class="text-center">#</th>
                                <th>Source Title</th>
                                <th class="text-center">Actions</th>
                            </tr>
                            </thead>
                            <tbody id="app-source-preview">
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal fade" id="CreateSourceModal">
                    <div class="modal-dialog modal-md">
                        <div class="modal-content">
                            <form onsubmit="createSource(event)">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h5 class="modal-title">Create New Source</h5>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Source Title</label>
                                        <input type="text" name="title" required class="form-control"
                                               placeholder="Source Title">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-sm btn-primary">Create</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="editSourceModal">
                    <div class="modal-dialog modal-md">
                        <div class="modal-content">
                            <form onsubmit="updateSource(event)">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h5 class="modal-title">Edit Source</h5>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Source Title</label>
                                        <input type="text" name="title" required class="form-control"
                                               placeholder="Source Title">
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
                <div class="modal fade" id="removeSourceModal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form onsubmit="removeSource(event)">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h5 class="modal-title">Remove Source</h5>
                                </div>
                                <div class="modal-body">
                                    <h4 class="text-center">
                                        Are you really want to remove this Source?
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
        <script type="application/javascript" src="{{env('APP_URL')}}/js/source.js?_token={{uniqid()}}"></script>
    </div>
    {{--</home>--}}
@endsection

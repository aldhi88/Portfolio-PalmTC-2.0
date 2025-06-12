@extends('layouts.master')

@section('css')
    @include('modules.init.include.index_css')
@endsection

@section('js')
    @include('modules.init.include.index_js')
    @include('modules.init.include.delete_js')
    @include('modules.init.include.nonactive_js')
    @include('modules.init.include.active_js')
@endsection

@section('content')
    <div class="row">
        <div class="col">
            <span id="alert-area"></span>
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <a href="{{ route('inits.create') }}" class="btn btn-sm btn-primary"><i class="feather mr-2 icon-plus"></i>Add New</a>
                        </div>
                        <div class="card-header-right">
                            <div class="btn-group card-option">
                                <button type="button" class="btn dropdown-toggle btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="feather icon-more-horizontal"></i>
                                </button>
                                <ul class="list-unstyled card-option dropdown-menu dropdown-menu-right">
                                    <li class="dropdown-item"><a href="#!" data-toggle="modal" data-target="#importModal"><span><i class="feather icon-upload"></i> Upload Data Excel</span></a></li>
                                    <li class="dropdown-item"><a href="{{ route('import.initsExport') }}"><span><i class="feather icon-download"></i> Download Excel Template</span></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="dt-responsive table-responsive">

                        <table id="myTable" class="table table-striped table-bordered nowrap table-xs w-100">
                            <thead>
                                <tr class="text-center align-middle">
                                    <th>Initiation <br> Date</th>
                                    <th class="align-middle">Sample</th>
                                    <th>Selection <br> Number</th>
                                    <th>Planting <br> Year</th>
                                    <th class="align-middle">Type</th>
                                    <th class="align-middle">Program</th>
                                    <th>Total <br> Block</th>
                                    <th>Total <br> Bottle</th>
                                    <th>Total <br> Explant</th>
                                    <th class="align-middle">Room</th>
                                    <th class="align-middle">Stop</th>
                                    <th>IMPORT <br> ID</th>
                                </tr>
                            </thead>
                            <thead id="header-filter" class="bg-white">
                                <tr>
                                    <th class="bg-white" disable="true"></th>
                                    <th class="bg-white">Ex: 12</th>
                                    <th class="bg-white">....</th>
                                    <th class="bg-white">....</th>
                                    <th class="bg-white">....</th>
                                    <th class="bg-white">....</th>
                                    <th class="bg-white" disable="true"></th>
                                    <th class="bg-white">....</th>
                                    <th class="bg-white" disable="true"></th>
                                    <th class="bg-white">....</th>
                                    <th class="bg-white" disable="true"></th>
                                    <th class="bg-white" disable="true"></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('modules.init.delete')
    @include('modules.init.nonactive')
    @include('modules.init.active')
@endsection

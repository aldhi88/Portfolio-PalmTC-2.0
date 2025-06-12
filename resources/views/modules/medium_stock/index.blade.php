@extends('layouts.master')

@section('css')
    @include('modules.medium_stock.include.index_css')
@endsection

@section('js')
    @include('modules.medium_stock.include.index_js')
    @include('modules.medium_stock.include.delete_js')
    @include('modules.medium_stock.include.history_js')
@endsection

@section('content')
<div class="row">
    <div class="col">
        <span id="alert-area"></span>
        <div class="card">
            <div class="card-header">
                <a href="{{ route('medium-stocks.create') }}" class="btn btn-sm btn-primary d-none d-sm-inline"><i class="feather mr-2 icon-plus"></i> Add New</a>
                <a href="{{ route('mediums.index') }}" class="btn btn-sm btn-warning d-none d-sm-inline"><i class="feather mr-2 icon-arrow-left"></i> Back to Medium</a>

                <a href="{{ route('medium-stocks.create') }}" class="btn btn-sm btn-primary btn-block d-sm-none"><i class="feather mr-2 icon-plus"></i> Add New</a>
                <a href="{{ route('mediums.index') }}" class="btn btn-sm btn-warning btn-block d-sm-none mt-1"><i class="feather mr-2 icon-arrow-left"></i> Back to Medium</a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <select name="filter" class="form-control form-control-sm">
                            <option value="0">All Data</option>
                            @if (isset($data['filter']))
                                @foreach ($data['tc_mediums'] as $item)
                                    <option {{ $item['id']==$data['filter']?'selected':'' }} value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            @else
                                @foreach ($data['tc_mediums'] as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <hr>
                    <table id="myTable" class="table table-xs table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                {{-- <th>#</th> --}}
                                <th>Name</th>
                                <th>Created<br>Date</th>
                                <th>Bottles</th>
                                <th>Worker</th>
                                <th>Age(days)</th>
                                <th>Added<br>Stock</th>
                                <th>Last<br>Stock</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
            </div>
        </div>
    </div>
</div>
@include('modules.medium_stock.include.modal')
@endsection


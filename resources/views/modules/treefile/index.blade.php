@extends('layouts.master')

@section('css')
    @include('modules.treefile.include.index_css')
@endsection

@section('js')
    @include('modules.treefile.include.index_js')
@endsection

@section('content')
<span id="alert-area"></span>
<div class="card">
    <div class="card-header">
        @include('components.card_tool')
    </div>
    <div class="card-body">
        {{-- <div class="dt-responsive table-responsive"> --}}
            <table id="myTable" class="table table-striped table-bordered dt-responsive nowrap table-xs" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
            {{-- <table id="simpletable" class="table table-striped table-bordered nowrap"> --}}
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Selection Number</th>
                        <th>Family</th>
                        <th>Female Genitor</th>
                        <th>Male Genitor</th>
                        <th>Block</th>
                        <th>Row</th>
                        <th>Palm N°</th>
                        <th>Tahun Tanam</th>
                        <th>Tipe</th>
                    </tr>
                </thead>
                <thead id="header-filter" class="bg-white">
                    <tr>
                        <th class="bg-white"></th>
                        <th class="bg-white"></th>
                        <th class="bg-white"></th>
                        <th class="bg-white"></th>
                        <th class="bg-white"></th>
                        <th class="bg-white"></th>
                        <th class="bg-white"></th>
                        <th class="bg-white"></th>
                        <th class="bg-white"></th>
                        <th class="bg-white"></th>
                    </tr>
                </thead>

                <tbody></tbody>
            </table>
        {{-- </div> --}}
    </div>
</div>



@endsection


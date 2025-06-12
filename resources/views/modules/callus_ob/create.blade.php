@extends('layouts.master')
@section('css')
{{-- @include('modules.callus_observation.include.index_css') --}}
@endsection

@section('js')
{{-- @include('modules.callus_observation.include.index_js') --}}
@endsection

@section('content')

<div class="row">
    <div class="col">
        <span id="alert-area"></span>

        <div class="card">

            <div class="card-header">
                <div class="row">
                    <div class="col"><h5 style="top: 5px"><i class="feather icon-file-text"></i> All Observation Data</h5></div>
                    <div class="col text-right">
                        <a href="{{ route('callus-observations.index') }}" class="btn btn-warning btn-sm"><i class="fas fa-backward mr-2"></i>Back to All Data</a>
                    </div>
                </div>
            </div>
            <div class="card-body">

            </div>

        </div>

    </div>
</div>

@endsection


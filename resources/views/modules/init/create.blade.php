@extends('layouts.master')

@if ($data['sample_count'] == 0)
    @section('content')
        <div class="alert alert-danger" role="alert">
            Sample data is not found, please click the following <a href="{{ route('samples.create') }}" class="alert-link">link</a> to create sample data.
        </div>
    @endsection
@else

@section('css')
    @include('modules.init.include.create_css')
@endsection

@section('js')
    @include('modules.init.include.create_js')
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <a href="{{ route('inits.index') }}" class="btn btn-warning btn-sm d-none d-sm-inline"><i class="feather mr-2 icon-skip-back"></i>Back to All Initiation Data</a>
        <a href="{{ route('inits.index') }}" class="btn btn-warning btn-sm btn-block d-sm-none"><i class="feather mr-2 icon-skip-back"></i>Back to All Initiation Data</a>
    </div>

    <div class="card-body">
        <div id="step1"></div>
        <hr>
        <div id="step2"></div>
        <hr>
        <div id="step3"></div>
    </div>

    <div class="card-footer">
        <div class="row">
            <div class="col">
                <span id="alert-area"></span>
                <form id="finishInit">@csrf
                    <button type="submit" id="initiation-finish" class="btn btn-primary btn-block rounded-0">Initiation Complate</button>
                </form>
            </div>
        </div>
    </div>

</div>

@endsection

@endif

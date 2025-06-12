@extends('layouts.master')
@section('css')
@include('modules.rooting_transfer.include.create_css')
@endsection

@section('js')
@include('modules.rooting_transfer.include.create_js')
@endsection

@section('content')

<span id="alert-area"></span>

<div class="row">
    <div class="col">
        <div class="card border border-sm">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        <a href="{{ route('rooting-transfers.show',$data['initId']) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-angle-double-left"></i> Back
                        </a>
                    </div>
                    <div class="col text-right">
                        <h5>Sample:</h5>
                        <span class="badge-light text-dark font-weight-bold px-2 py-1 border">
                            <i class="fas fa-eye-dropper mr-2"></i> {{ $data['sampleNumber'] }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="card-body">

                <div id="step1"></div>
                <hr>
                <div id="step2"></div>
                <hr>
                <div id="step3"></div>
                <hr>
                <div id="step4"></div>

            </div>

            <div class="card-footer">
                <span id="alert-finish"></span>
                <div class="row">
                    <div class="col">
                        <form id="finishTransfer">@csrf
                            <input type="hidden" name="tc_init_id" value="{{ $data['initId'] }}">
                            <button type="submit" class="btn btn-primary btn-block">Finish Transfer</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection


@extends('layouts.master')


@section('content')
<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header">
                <h5>
                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#createModal"><i class="feather mr-2 icon-plus"></i>Add New</button>
                </h5>
                @include('components.card_tool')
            </div>
            <div class="card-body">
                @livewire('worker.worker-data')
            </div>
        </div>
    </div>
</div>
@endsection




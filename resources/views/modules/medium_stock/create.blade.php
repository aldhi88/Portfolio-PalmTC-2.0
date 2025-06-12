@extends('layouts.master')

@section('css')
    @include('modules.medium_stock.include.index_css')
@endsection

@section('js')
    @include('modules.medium_stock.include.create_js')
@endsection

@section('content')
<span id="alert-area"></span>
<div class="card">
    <div class="card-header">
        <a href="{{ route('medium-stocks.index') }}" class="btn btn-warning btn-sm d-none d-sm-inline"><i class="feather mr-2 icon-skip-back"></i>Back to Medium Stock</a>
        <a href="{{ route('medium-stocks.index') }}" class="btn btn-warning btn-sm btn-block d-sm-none"><i class="feather mr-2 icon-skip-back"></i>Back to Medium Stock</a>
        <a href="{{ route('mediums.index') }}" class="btn btn-warning btn-sm d-none d-sm-inline"><i class="feather mr-2 icon-corner-up-left"></i>Back to Medium</a>
        <a href="{{ route('mediums.index') }}" class="btn btn-warning btn-sm btn-block d-sm-none mt-1"><i class="feather mr-2 icon-corner-up-left"></i>Back to Medium</a>
    </div>
    <form id="formCreateModal"> @csrf
        <div class="card-body">
            
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label><strong>Medium</strong></label>
                        <select name="tc_medium_id" class="form-control form-control-sm">
                            @if (isset($data['selected']))
                                @foreach ($data['tc_mediums'] as $item)
                                    <option {{ $item['id']==$data['selected']?'selected':'' }} value="{{ $item->id }}">{{ $item->code }}</option>
                                @endforeach
                            @else
                                @foreach ($data['tc_mediums'] as $item)
                                    <option value="{{ $item->id }}">{{ $item->code }}</option>
                                @endforeach
                            @endif

                            {{-- @foreach ($data['tc_mediums'] as $item)
                                <option value="{{ $item['id'] }}">{{ $item['name'] }}</option>
                            @endforeach --}}
                        </select>
                    </div>
                    <div class="form-group">
                        <label><strong>Bottle</strong></label>
                        <select name="tc_bottle_id" class="form-control form-control-sm">
                            @foreach ($data['tc_vassels'] as $item)
                                <option value="{{ $item['id'] }}">{{ $item['code'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label><strong>Agar Rose</strong></label>
                        <select name="tc_agar_id" class="form-control form-control-sm">
                            @foreach ($data['tc_agars'] as $item)
                                <option value="{{ $item['id'] }}">{{ $item['code'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label><strong>Worker</strong></label>
                        <select name="tc_worker_id" class="form-control form-control-sm">
                            @foreach ($data['tc_workers'] as $item)
                                <option value="{{ $item['id'] }}">{{ $item['code'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label><strong>Stock</strong></label>
                        <input type="text" name="stock" class="form-control form-control-sm">
                        <small><span class="stock text-danger msg"></span></small>
                    </div>
                    <div class="form-group">
                        <label><strong>Created At</strong></label>
                        <input type="date" value="{{ date('Y-m-d') }}" name="created_at" class="form-control form-control-sm">
                        <small><span class="created_at text-danger msg"></span></small>
                    </div>
                </div>
            </div>
            
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-block btn-sm btn-primary"><i class="feather mr-2 icon-save"></i>Save New Stock</button>
        </div>
    </form>
</div>


@endsection


@extends('layouts.master',
    [
        "data" => [
            'title' => "Import Sample Data",
            'desc' => "import sample data from excel file.",
        ]
    ]
)
@section('js')
    @include('modules.import.inc.sample_js')
@endsection

@section('content')
<span id="alert-area"></span>
    
<div class="card">

    <div class="card-header">
        <h5>Import Sample Data</h5>
        <div class="card-header-right">
            <div class="btn-group card-option">
                {{-- right of card --}}
            </div>
        </div>
    </div>
    <div class="card-body">
        
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label>Download Form Format</label><br>
                    <a href="{{route('import.sampleExport')}}" class="btn btn-info">Download</a>
                </div>
            </div>
            <div class="col">
                <form id="form-import">@csrf
                <div class="form-group">
                    <label>Upload Data Import</label><br>
                    <input type="file" name="file">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-sm btn-danger">Import Now</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
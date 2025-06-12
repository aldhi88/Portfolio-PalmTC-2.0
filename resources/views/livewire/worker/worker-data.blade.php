<div>
    <div class="row">
        <div class="col">
            {{-- <span id="alert-area"></span> --}}
            <div class="card">
                <div class="card-header">
                    <h5>
                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#create">
                            <i class="feather mr-2 icon-plus"></i>Add New
                        </button>
                    </h5>
                    {{-- @include('components.card_tool') --}}
                </div>
                <div class="card-body">
                    <span id="alert-area"></span>
                    <table id="myTable" class="table table-striped table-bordered nowrap table-xs w-100">
                        <thead>
                            <tr>
                                <th style="width: 60px !important"><i class="fa fa-clock"></i></th>
                                <th>Employ No.</th>
                                <th>Name</th>
                                <th>Date of Birth</th>
                                <th>Code</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <thead id="header-filter" class="text-center">
                            <tr>
                                <th class="bg-white" off></th>
                                <th class="bg-white"></th>
                                <th class="bg-white"></th>
                                <th class="bg-white"></th>
                                <th class="bg-white"></th>
                                <th class="bg-white" off></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('components.modal',[
        'id' => 'create',
        'content' => 'livewire.worker.worker-create'
    ])

    @include('components.vendor', ['vendor' => ['vendor1', 'vendor2']])
</div>

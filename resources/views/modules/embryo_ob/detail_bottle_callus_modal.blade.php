<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title"><i class="feather icon-file-text"></i> Data Bottle Callus</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
    <div class="modal-body">
        <div class="table-responsive">
            <table class="table table-xs">
                <thead class="text-center">
                    <tr>
                        <th>#</th>
                        <th>Block</th>
                        <th>Bottle</th>
                        <th>Worker</th>
                        <th>Transfer Count</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data['listBottleCallusPerInit'] as $item)
                        <tr class="text-center">
                            <td>{{ $loop->index + 1 }}</td>
                            <td>{{ $item->tc_init_bottles->block_number }}</td>
                            <td>{{ $item->tc_init_bottles->bottle_number }}</td>
                            <td>{{ $item->tc_init_bottles->tc_workers->code }}</td>
                            <td>{{ '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
    </div>
</div>

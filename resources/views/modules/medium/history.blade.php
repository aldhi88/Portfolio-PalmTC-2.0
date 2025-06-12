{{-- @php
    dd($data['tc_medium_opname']);
@endphp --}}
<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" id="sampleModalLabel"><i class="feather icon-align-center"></i> Medium Stock History</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
    <div class="modal-body">
        <div class="table-responsive">
            <table class="table table-xs">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Description</th>
                        <th class="text-right">Stock In</th>
                        <th class="text-right">Stock Out</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($data['histories'])
                        @foreach ($data['histories'] as $item)
                            <tr>
                                <td>{{ $item['created_at'] }}</td>
                                <td>{!! $item['desc'] !!}</td>
                                <td class="text-right">{{ $item['stock_in'] }}</td>
                                <td class="text-right">{{ $item['stock_out'] }}</td>
                                <td class="text-right">{{ $item['total'] }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">Close</button>
    </div>
</div>


{{-- @php
    dd($data['tc_medium_opname']);
@endphp --}}
<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" id="sampleModalLabel"><i class="feather icon-align-center"></i> Validation Stock History</h5>
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
                    <tr>
                        <td>{{ $data['histories']['created_at_short_format'] }}</td>
                        <td>Added Stock</td>
                        <td class="text-right">{{ $data['histories']['stock'] }}</td>
                        <td class="text-right">{{ 0 }}</td>
                        <td class="text-right">{{ $data['histories']['stock'] }}</td>
                    </tr>
                    @if ($data['histories'] != null)

                        @foreach ($data['tc_medium_opname'] as $item)
                            <tr>
                                <td>{{ $item['created_at_short_format'] }}</td>
                                <td>Validation <br> <small>{{ $item['desc'] }}</small></td>
                                <td class="text-right">{{ $item['stock_in'] }}</td>
                                <td class="text-right">{{ $item['stock_out'] }}</td>
                                <td class="text-right">{{ $item['total'] }}</td>
                            </tr>
                        @endforeach
                        @foreach ($data['init_stocks'] as $item)
                            <tr>
                                <td>{{ $item['date_format'] }}</td>
                                <td>Initiation</td>
                                <td class="text-right">0</td>
                                <td class="text-right">{{ $item['tc_init_bottles_count'] }}</td>
                                <td class="text-right">{{ $item['total'] }}</td>
                            </tr>
                        @endforeach

                        @foreach ($data['callus_trans'] as $item)
                        <tr>
                            <td>{{ $item['created_at'] }}</td>
                            <td>Callus Transfer</td>
                            <td class="text-right">0</td>
                            <td class="text-right">{{ $item['stock_used'] }}</td>
                            <td class="text-right">{{ $item['total'] }}</td>
                        </tr>
                        @endforeach
                        @foreach ($data['embryo_trans'] as $item)
                        <tr>
                            <td>{{ $item['created_at'] }}</td>
                            <td>Embryo Transfer</td>
                            <td class="text-right">0</td>
                            <td class="text-right">{{ $item['used_stock'] }}</td>
                            <td class="text-right">{{ $item['total'] }}</td>
                        </tr>
                        @endforeach
                        @foreach ($data['liquid_trans'] as $item)
                        <tr>
                            <td>{{ $item['created_at'] }}</td>
                            <td>Liquid Transfer</td>
                            <td class="text-right">0</td>
                            <td class="text-right">{{ $item['used_stock'] }}</td>
                            <td class="text-right">{{ $item['total'] }}</td>
                        </tr>
                        @endforeach
                        @foreach ($data['matur_trans'] as $item)
                        <tr>
                            <td>{{ $item['created_at'] }}</td>
                            <td>Maturation Transfer</td>
                            <td class="text-right">0</td>
                            <td class="text-right">{{ $item['used_stock'] }}</td>
                            <td class="text-right">{{ $item['total'] }}</td>
                        </tr>
                        @endforeach
                        @foreach ($data['germin_trans'] as $item)
                        <tr>
                            <td>{{ $item['created_at'] }}</td>
                            <td>Germination Transfer</td>
                            <td class="text-right">0</td>
                            <td class="text-right">{{ $item['used_stock'] }}</td>
                            <td class="text-right">{{ $item['total'] }}</td>
                        </tr>
                        @endforeach
                        @foreach ($data['rooting_trans'] as $item)
                        <tr>
                            <td>{{ $item['created_at'] }}</td>
                            <td>Rooting Transfer</td>
                            <td class="text-right">0</td>
                            <td class="text-right">{{ $item['used_stock'] }}</td>
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


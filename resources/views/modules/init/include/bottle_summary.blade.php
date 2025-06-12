<table class="table table-xs table-bordered" id="summery">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Code</th>
            <th>Laminar</th>
            <th class="text-right">Block</th>
            <th class="text-right">Bottle</th>
            <th class="text-right">Explant</th>
        </tr>
    </thead>
    <tbody>
            @foreach ($data['worker'] as $item)
                <tr>
                    <td>{{ $loop->index +=1 }}</td>
                    <td>{{ $item['tc_workers']['name'] }}</td>
                    <td>{{ $item['tc_workers']['code'] }}</td>
                    <td>{{ $item['tc_laminars']['code'] }}</td>
                    <td class="text-right">{{ $item['block_load'] }}</td>
                    <td class="text-right">{{ $item['bottle_load'] }}</td>
                    <td class="text-right">{{ $item['explant_load'] }}</td>
                </tr>
            @endforeach
            <tr class="bg-light">
                <th colspan="4" class="text-right font-weight-bold py-1">Total:</th>
                <th class="text-right py-1">{{ $data['block_total'] }}</th>
                <th class="text-right py-1">{{ $data['bottle_total'] }}</th>
                <th class="text-right py-1">{{ $data['explant_total'] }}</th>
            </tr>
    </tbody>
</table>
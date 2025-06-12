<div class="table-responsive">
    <table class="table table-xs">
        <thead>
            <tr>
                <th>Date</th>
                <th>Total</th>
                <th>Reason</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @if (count($data['subtractions'])!=0)
                @foreach ($data['subtractions'] as $item)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') }}</td>
                        <td>{{ $item->bottle_count }}</td>
                        <td>{{ $item->reason }}</td>
                        <td>
                            <form class="delSubtraction">@csrf @method('DELETE')
                                <input type="hidden" name="id" value="{{ $item->id }}">
                                <button type="submit" class="btn btn-sm btn-danger py-0 my-1">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr><td colspan="4">No Data</td></tr>
            @endif
        </tbody>
    </table>
</div>

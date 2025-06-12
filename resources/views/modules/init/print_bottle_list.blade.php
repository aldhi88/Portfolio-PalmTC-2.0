@if (is_null($data["print_bottle_custom"]))
    <p class="mt-2 mb-0">Please select the bottle data by "check" to add data into the printing label list</p>
@else
    <table class="table table-sm">
        <thead>
            <tr class="text-center">
                <th>Block <br> Number</th>
                <th>Bottle <br> Number</th>
                <th>Worker</th>
                <th></th>
            </tr>
        </thead>
        @foreach ($data["print_bottle_custom"] as $item)
            <tr class="text-center">
                <td>{{ $item->block_number }}</td>
                <td>{{ $item->bottle_number}}</td>
                <td>{{ $item->tc_workers->code }}</td>
                <td>
                    <button class="btn btn-danger btn-sm trigger-check" value="{{ $item->id }}">
                        <i class="feather icon-trash-2"></i>
                    </button>
                </td>
            </tr>
        @endforeach
    </table>
@endif
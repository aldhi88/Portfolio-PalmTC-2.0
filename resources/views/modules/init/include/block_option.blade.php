<select name="block_number" class="form-control form-control-sm">
    @foreach ($data["blocks"] as $item)
        <option value="{{ $item[0]['block_number'] }}">{{ $item[0]['block_number'] }}</option>
    @endforeach
</select>
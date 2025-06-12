@foreach ($data['dateList'] as $item)
    <option value="{{ Carbon\Carbon::parse($item->date_work)->format('Y-m-d') }}">{{ Carbon\Carbon::parse($item->date_work)->format('d/m/Y') }}</option>
@endforeach
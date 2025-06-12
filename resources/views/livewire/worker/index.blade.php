@extends('layouts.app', ['data'=>$data])

@section('content')

    @livewire($data['lw'], ['data'=>$data])

@endsection


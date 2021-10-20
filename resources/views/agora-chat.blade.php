@extends('layouts.app')

@section('content')    
    <agora-chat :allusers="{{ $users }}" authuserid="{{ auth()->id() }}" authuser="{{ auth()->user()->name }}"
        agora_id="{{ env('AGORA_APP_ID') }}" audio-call="false" video-call="false" onlymesssage="true" />
@endsection

@push('scripts')
	
@endpush
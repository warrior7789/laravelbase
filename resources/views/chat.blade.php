@extends('layouts.app')

@section('content')  
	
	
    <chat :Logeduser="{{ $Logeduser}}" :allusers="{{ $listusers }}" authuserid="{{ auth()->id() }}" authuser="{{ auth()->user()->name }}"
        agora_id="{{ env('AGORA_APP_ID') }}" chat_with_name="{{ $chat_with_name }}" chat_with_id="{{ $chat_with_id }}" audio-call="false" video-call="false" onlymesssage="true" />
@endsection

@push('scripts')
	
@endpush 
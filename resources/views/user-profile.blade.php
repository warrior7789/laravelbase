@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                @if(!empty($user))
                    <div class="row">                       
                        <div class="col-md-12">                           
                            <h3>{{ $user->name }}</h3>   
                                                 
                            <audio-video-chat :callto="{{ $user }}" :allusers="{{ $users }}" authuserid="{{ auth()->id() }}" authuser="{{ auth()->user()->name }}"   :authdata="{{ $authUSer }}"  />                           
                        </div>                        
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

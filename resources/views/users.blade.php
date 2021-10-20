@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                @if(!empty($users))
                    <div class="row">
                        @foreach($users as $user)
                            <div class="col-md-3">
                                <a href="{{ url('profile/'.$user->id)}}">
                                    <h3>{{ $user->name }}</h3>
                                    <h3>{{ $user->email }}</h3>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

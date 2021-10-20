@if(!empty($chats))
	
	@foreach($chats as $chat)

		@if(auth()->id() == $chat->userfrom->id)
			<div class="col-md-12" >
				<span style="float:right"><b>{{  $chat->userfrom->name }}</b> {{ $chat->message }} </span>
			</div>

		@else
			<div class="col-md-12" style=''>
				<span style="float:left"><b>{{  $chat->userfrom->name }}</b> {{ $chat->message }} </span>
			</div>
		@endif

	@endforeach
@endif
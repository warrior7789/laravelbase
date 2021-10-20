@foreach ($chats as $chat)

	@if($chat['from_id'] == $auth_id)
		<div class="message-card mc-sender">
			<p> 
				@if(!empty($chat['image']))
					@if( strtolower($chat['filetype']) =='jpg' || strtolower($chat['filetype']) =='png' || strtolower($chat['filetype']) =='jpeg' )
						<img src="{{URL::asset($chat['image'])}}" style="width: 150px">
					@else
						<iframe height='150px' width='150px' src="{{URL::asset($chat['image'])}}"></iframe>
					@endif

				@else
					{{ $chat['message'] }}
				@endif
				<br>
				<sub class="message-time-">                                
				    {{ $chat['created_at'] }}
				</sub>
			</p>
		</div>
	@else
		<div class="message-card">
			<p>
				@if(!empty($chat['image']))
					@if( strtolower($chat['filetype']) =='jpg' || strtolower($chat['filetype']) =='png' || strtolower($chat['filetype']) =='jpeg' )
						<img src="{{URL::asset($chat['image'])}}" style="width: 150px">
					@else
						<iframe height='150px' width='150px' src="{{URL::asset($chat['image'])}}"></iframe>
					@endif
				@else
					{{ $chat['message'] }}
				@endif
				<br>
				<sub class="message-time-">                                
				    {{ $chat['created_at'] }}
				</sub>
			</p>
		</div>
	@endif	
@endforeach
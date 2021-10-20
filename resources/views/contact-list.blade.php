@foreach ($users as $user)
    <table id="table_user_id_{{ $user->id }}"  class="start_chat messenger-list-item m-list-active_" data-to_id="{{ $user->id }}" data-to_name="{{ $user->name }}" data-to_socket_id={{ $user->socket_id }} >                            
        <tr data-action="0">
            <td style="position: relative">
            	@php 
            		if(\Cache::has('user-is-online-' . $user->id)){
            			echo '<span class="activeStatus" ></span>';
            		}
            	@endphp
               {{--  <span class="activeStatus" ></span> --}}                                    
               {{--  <div class="avatar av-m" style="background-image:url()"  ></div> --}}
            </td>                               
            <td>
            <p >
                {{ $user->name }}

                @if(!empty($user->chat))
                	<span > {{ $user->chat->created_at->diffForHumans() }}</span></p>
                @endif
                
                @if(!empty($user->chat) && $user->chat->filename)
                    <span >
                        {{ $user->chat->filename }}      
                    </span>
                @elseif(!empty($user->chat) )
                    <span >
                        {{ $user->chat->message }}                                   
                    </span>
                @endif
            	@if($user->unSeenMsg > 0)
                	<b id="unread_{{ $user->id }}">{{ $user->unSeenMsg }}</b>
                @endif
            </td>                                
        </tr>
    </table>
@endforeach
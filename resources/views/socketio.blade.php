@extends('layouts.app')

@section('content') 
    
	{{-- <div class="row">
		<div class="col-md-12">
		<h6> Socke id = <span id="mysocketid"></span></h6>
		</div>
		<div class="col-md-4">
			<h1>Group message</h1>
			<ul id="messages_ul"></ul>
			<form id="sendmessage" class="col-md-4" autocomplete="off">
				<input type="text" id="messge" class="form-control" autocomplete="off" />
				<button type="submit">Send</button>
			</form>
		</div>

		<div class="col-md-4">
			<h1>Privat Message</h1>
			<ul id="messages_ul_private"></ul>
			<form id="sendprivate" class="col-md-4" autocomplete="off">
				user id
				<select id="user_id" class="form-control">
					
				</select>
				
				message<input type="text" id="messgeprivate" class="form-control" autocomplete="off" />
				<button type="submit">Send private</button>
			</form>
		</div>
	</div> --}}
	<div class="messenger">
		<div class="messenger-listView">		   
		    <div class="m-header">
		        <nav>
		            <a href="#"><i class="fa fa-inbox"></i> <span class="messenger-headTitle">MESSAGES</span> </a>
		           
		            <nav class="m-header-right">
		                <a href="#"><i class="fa fa-cog settings-btn"></i></a>
		                <a href="#" class="listView-x"><i class="fa fa-times"></i></a>
		            </nav>
		        </nav>
		       
		        <input type="text" class="messenger-search" placeholder="Search" />
		        
		        <div class="messenger-listView-tabs">
		            <a href="#" class="active-tab" data-view="users">
		                <span class="fa fa-user"></span> People</a>
		           
		        </div>
		    </div>
		   
		    <div class="m-body">

		       <div class=" show  messenger-tab app-scroll" data-view="users" id="app-scroll" >
		           <div id="listOfContacts" class="listOfContacts" style="width: 100%;height: calc(100% - 200px);position: relative;">
		                @foreach ($users as $user)
			                <table id="user_id_{{ $user->id }}"  class="start_chat messenger-list-item m-list-active_" data-to_id="{{ $user->id }}" data-to_name="{{ $user->name }}" data-to_socket_id={{ $user->socket_id }} >                            
			                    <tr data-action="0">
			                        <td style="position: relative">
			                        	@php 
			                        		if(\Cache::has('user-is-online-' . $user->id)){
			                        			echo '<span class="activeStatus" ></span>';
			                        		}
			                        	@endphp
			                           {{--  <span class="activeStatus" ></span> --}}                                    
			                            <div class="avatar av-m" style="background-image:url('https://ptetutorials.com/images/user-profile.png')"  ></div>
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
		           </div>
		       </div>		      
		    </div>
		</div>


		<div class="messenger-messagingView">           
		    <div class="m-header m-header-messaging">
		        <nav>                    
		            <div style="display: inline-flex;">
		                <a href="#" class="show-listView" ><i class="fas fa-arrow-left"></i></a>
		                <div class="avatar av-s header-avatar" style="margin: 0px 10px; margin-top: -5px; margin-bottom: -5px;">
		                </div>
		                <a href="#" class="user-name">Chat with name</a>
		            </div>                    
		            <nav class="m-header-right">
		                <a href="#" class="add-to-favorite"><i class="fas fa-star"></i></a>
		                <a href="#" class="show-infoSide" v-on:click="ShowUserDetails = !ShowUserDetails"><i class="fas fa-info-circle"></i></a>
		            </nav>
		        </nav>
		    </div>     
		    <div class="m-body app-scroll" >
		        <div class="messages" id="messages_div">
		            <p class="message-hint center-el" >
		                <span>Please select a chat to start messaging</span>
		            </p>
		        </div>		        
		        <div class="typing-indicator">
		            <div class="message-card typing">
		                <p>
		                    <span class="typing-dots">
		                        <span class="dot dot-1"></span>
		                        <span class="dot dot-2"></span>
		                        <span class="dot dot-3"></span>
		                    </span>
		                </p>
		            </div>
		        </div> 
		        <div class="messenger-sendCard">
		            <form  id="send_message_form" autocomplete="off">
		            	<label>
		            	    <span class="fas fa-paperclip"  for="file"></span>
		            	    <input id="fileattach" onchange="previewFiles(this)"  type="file" class="upload-attachment" name="file" />
		            	</label>
		                <input type="text" id="channelMessage" name="message" class="m-send app-scroll" placeholder="Type a message.." autocomplete="off" />
		                <button type="submit" >
		                    <span class="fas fa-paper-plane"></span>
		                </button>
		            </form>
		        </div>
		    </div>
		</div>
	</div>
@endsection

@push('scripts')
<script type="text/javascript"></script>
<style type="text/css">
	.messenger-listView{
		width: 25% !important;
	}
	.mc-sender p{
	    background: #2180f3;
	}
	div#messages_div {
	    float: right;    
		overflow: auto;
		position: relative;
		border-bottom: 1px solid black;
		height: calc(100%);
	}

	.blink_me {
	  animation: blinker 2s linear infinite;
	}

	@keyframes blinker {
	  50% {
	    opacity: 0;
	  }
	}
</style>

<script type="text/javascript">

	var logedUSer = {!! json_encode($Authuser->toArray()) !!};
	var Start_chat_with =0;
	var to_socket_id =0;
	var ajax_get_history =1;
	var next_page_url ="";
	// establish socket.io connection	
	


	function dataURLToBlob(dataurl){
	    var arr = dataurl.split(',')
	    var mime = arr[0].match(/:(.*?);/)[1]
	    var bstr = atob(arr[1])
	    var n = bstr.length
	    var u8arr = new Uint8Array(n)
	    while(n--){
	        u8arr[n] = bstr.charCodeAt(n)
	    }
	    return new Blob([u8arr], {type:mime})
	}

  	function previewFiles(event) {
  		console.log(event);
  		if(Start_chat_with == 0){
  			alert("Plese select User")
  			return false;
  		}  		
  	    var file = $('#fileattach')[0].files[0];  	    
  	    var file_name = file.name;
  	    var file_type = file.type;
  	    var file_size = file.size;
  	    var reader = new FileReader();
  	    reader.onload = function (e) {
  	        var file_url = e.target.result;
  	        var blob_message = dataURLToBlob(file_url);
  	        var data ={
  	        	to_id		: 	parseInt(Start_chat_with),
  	        	from_id		:   parseInt(logedUSer.id),
  	        	message		:   "image",
  	        	to_socket_id :   to_socket_id,
  	        	image_data  : {
  	        		file_name : file_name,
  	        		file_type : file_type,
  	        		file_size : file_size,
  	        		file_data : file_url,
  	        	}
  	        }
  	        socket.emit("private-message", data);	
  	        //socket.emit('image', { image: true, buffer: buf });  
  	    }
  	    reader.readAsDataURL(file)
  	}
	
	jQuery(document).ready(function ($) {
		$("#messages_div").bind('mousewheel', function(event) {
		    if (event.originalEvent.wheelDelta >= 0) {
		        //console.log('Scroll up');
		        if(next_page_url && ajax_get_history){
		        	next_page_data()
		        }
		    }
		    else {
		        //console.log('Scroll down');
		    }
		});

		function getChatHstory(){
			$.ajax({
				method: "POST",
				url: "{{url('makeseen')}}",
				data:{
					"_token": "{{ csrf_token() }}",
					"to_user_id": Start_chat_with
				},
			}).done(function( responce ) {				
			});


			$.ajax({
				method: "POST",
				url: "{{ url('getChathistory') }}",
				data:{
					"_token" 	: "{{ csrf_token() }}",
					"from_id" 	: logedUSer.id,
					"to_id" 	: Start_chat_with,
				},
			}).done(function( responce ) {		
				//console.log("chat history")		
				//console.log(responce)	
				$("#messages_div").html(responce.message);	
				next_page_url = responce.chats.next_page_url

				
				/*var objDiv = document.getElementById("messages_div");
				objDiv.scrollTop = objDiv.scrollHeight +30 ;*/

				$('#messages_div').stop().animate({
				  scrollTop: $('#messages_div')[0].scrollHeight
				}, 800);
				
			});

		}

		function next_page_data(){
			ajax_get_history=0;
			$.ajax({
				async:false,
				method: "POST",
				url: next_page_url,
				data:{
					"_token" 	: "{{ csrf_token() }}",
					"from_id" 	: logedUSer.id,
					"to_id" 	: Start_chat_with,
				},
			}).done(function( responce ) {		
				//console.log("chat history")		
				//console.log(responce)	
				$("#messages_div").prepend(responce.message);	
				next_page_url = responce.chats.next_page_url;
				ajax_get_history=1;
			});
		}

		function updateContactList(){
			$.ajax({
				method: "POST",
				url: "{{url('getContactlist')}}",
				data:{
					"_token": "{{ csrf_token() }}",
				},
			}).done(function( responce ) {
				$("#listOfContacts").html(responce.list)
			});
		}

		$(document).on("click", ".start_chat", function(){	
			to_socket_id =0;	
			Start_chat_with= $(this).attr("data-to_id");
			
			if($(this).attr("data-to_socket_id"))
				to_socket_id= $(this).attr("data-to_socket_id");

			var to_name= $(this).attr("data-to_name");
			$(".user-name").html(to_name)

			// remove 
			$(".start_chat").removeClass('m-list-active');
			$(this).removeClass('blink_me');
			$(this).addClass('m-list-active');
			$("#messages_div").html("");
			getChatHstory();
			$("#unread_"+Start_chat_with).remove()
		});


		$("#send_message_form").on("submit", function(e){			
			e.preventDefault();
			if(Start_chat_with == 0){
				alert("Plese select User")
				return false;
			}
			var message = $("#channelMessage").val();
			$("#channelMessage").val("")
			var data ={
				to_id	: 	parseInt(Start_chat_with),
				from_id	:   parseInt(logedUSer.id),
				message		:   message,
				to_socket_id :   to_socket_id,
			}

			socket.emit("private-message", data);	
			var html ="";
			html ='<div class="message-card mc-sender">';
				html +='<p>';
					html +=message;
					html +='<sub class="message-time-">'+moment().fromNow();				   
					html +='</sub>';
				html +='</p>';
			html +='</div>';
			$("#messages_div").append(html)
			$(".message-hint").remove();
			$('#messages_div').stop().animate({
				scrollTop: $('#messages_div')[0].scrollHeight
			}, 800);
		});
	});

  	socket.on('private-message', function (data) {
  		var html ="";
		if(data.from_id == Start_chat_with || data.me){			
			if(data.me){
				html ='<div class="message-card mc-sender">';
					html +='<p>';
					html +=data.message;
					html +='<sub class="message-time-">'+moment().fromNow()+'</sub>';
					html +='</p>';
				html +='</div>';
			}else{
				html ='<div class="message-card">';
					html +='<p>';
						html +=data.message;
						html +='<sub class="message-time-">'+moment().fromNow()+'</sub>';
					html +='</p>';
				html +='</div>';
			}
			$("#messages_div").append(html)
			$(".message-hint").remove()
			$('#messages_div').stop().animate({
			  	scrollTop: $('#messages_div')[0].scrollHeight
			}, 800);
		}else{
			// send notification or refresh listing			
			$.ajax({
				method: "POST",
				url: "{{url('getContactlist')}}",
				data:{
					"_token": "{{ csrf_token() }}",
				},
			}).done(function( responce ) {
				$("#listOfContacts").html(responce.list)
				$("#unread_"+data.from_id).addClass('blink_me');
			});
		}          
    });
	// on socket connet save socket id to databse

	// watch for socket to emit a 'message'
		socket.on('bmessage', function (data) {
	        addMessageToHTML(data);
	    });

	    socket.on('usersList', function (data) {
	        console.log("usersList");
	        console.log(data);
	    });
	    socket.on('mysocketid', function (data) {
        	$.ajax({
            	method: "POST",
            	url: "{{url('save-socketid')}}",
                data:{
        		"_token": "{{ csrf_token() }}",
        		"socket_id": data.socket_id,
        	},
        	}).done(function( msg ) {
        		if(msg.error == 0){
        			//$('.sucess-status-update').html(msg.message);
        			//alert(msg.message);
        		}else{
        			//alert(msg.message);
        			//$('.error-favourite-message').html(msg.message);
        		}
           	});
	    });


  	
		// fetch user list
		socket.on('usersList', function (data) {
		  	console.log("usersList"); // world
		  	console.log(data); // world
		});

		// watch for socket to emit a 'message'
		socket.on('message', function (data) {
	        //console.log("new message" + data);
	        addMessageToHTML(data);
	    });



    	// watch for socket to emit a 'user connected' event
    	socket.on('user connected', function (data) {
            //console.log("user connected");
            //addMessageToHTML("User connected");
        });


    	socket.on('broadcast', function (data) {
            //console.log("Broad cast message");
            //console.log(data);
        });

    	socket.on("newUser", (arg) => {
    		//console.log("new user login")
          	//console.log(arg); // world
        });
    	
    	socket.on('disconnect', function () {	    
    	    socket.emit('UserLogout', socket.id);
    	});

  	

	
	jQuery(document).ready(function ($) {		
		$("#sendmessage").on("submit", function(e){			
			e.preventDefault();
			var message = logedUSer.name + " : " + $("#messge").val();
			$("#messge").val("");
			//socket.emit("message", message);
			socket.emit("bmessage", message);
		});


		$("#sendprivate").on("submit", function(e){	
			e.preventDefault();
			var message = $("#messgeprivate").val();
			var user_id = $("#user_id").val();
			var data = {
				message:message,
				to_user_id:user_id,
			}

			socket.emit("private-message", data);
			$("#messgeprivate").val("");
			
			$.ajax({
				method: "POST",
				url: "{{url('private-message')}}",
				data:{
					"_token": "{{ csrf_token() }}",
					"user_id": user_id,
					"message": message,
				},
			}).done(function( msg ) {
				if(msg.error == 0){
					//$('.sucess-status-update').html(msg.message);
					//alert(msg.message);
				}else{
					//alert(msg.message);
					//$('.error-favourite-message').html(msg.message);
				}
			});
		});

		/*$("#sendmessage").on("submit", function(e){			
			e.preventDefault();
			var message = logedUSer.name + " : " + $("#messge").val();
			$("#messge").val("");
			//socket.emit("message", message);
			socket.emit("bmessage", message);
		});*/
	});

	function addMessageToHTML(message) {			
		//alert("here" + message)
		var html = "<li >"+ message +"</li>";
		$("#messages_ul").append(html);
	}
</script>
	
@endpush 
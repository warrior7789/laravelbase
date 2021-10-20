<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Chat;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use App\Classes\AgoraDynamicKey\RtcTokenBuilder;
use App\Classes\AgoraDynamicKey\RtmTokenBuilder;



use App\Events\MakeAgoraCall;
use App\Events\Notify;
use Response;
use Illuminate\Support\Facades\File; 
use Cache;
use DB;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Http;
use Pusher\Pusher;

use App\Classes\Version3X;
use App\Classes\Emitters;
use ElephantIO\Client, ElephantIO\Engine\SocketIO\Version2X;


use Workerman\Worker;
use PHPSocketIO\SocketIO;

class AgoraVideoController extends Controller
{   

    public function SaveSocketId(Request $request){
        die("sdfsa");
        $data =array();
        if(empty($request->user_id)){
            $user            = User::find(Auth::id());
        }else{
            $user            = User::find($request->user_id);
        }
        $user->socket_id = $request->socket_id; 
        $user->save();
        $data['message'] = 1;
        return Response::json($data, 200); 
    }

    public function socketio(Request $request){
        $Authuser =  User::find(Auth::id());

        //socketio
        $users = $this->getContactsObject();   
        //dd($users);    
        return view('socketio',["Authuser" =>$Authuser,'users'=>$users]);
        //die("Socket io");

    }

    public function getContactlist(){
        $data =array();
        $data['status'] =0;

        $users = $this->getContactsObject(); 
        //dd($users);
        if(!empty($users)){
            $data['list'] =view('contact-list',["auth_id" =>Auth::id(),'users'=>$users])->render();
            $data['status'] =1;
        }
        return Response::json($data, 200);       
    }

    public function ReadSocketio(Request $request){

        $client = new Client(new Version3X(env('SOCKET_IO_SERVER')));
        $client->initialize();
        $engine =$client->getEngine();
        $user_socket_id = $engine->session->id;
        $client->emit('newUser',$user_socket_id);
        /*while (true) {
            $r = $client->read();

            if (!empty($r)) {
                dd($r);
            }
        }*/
        //$client->close();
    }
    public function PrivateMessage(Request $request){
        $user  = User::find($request->user_id);       
        $socket_data=array();
        $socket_data['event_name'] = "private-message";
        $socket_data['from_user'] = Auth::id();        
        $socket_data['to_user'] = $request->user_id;        
        $socket_data['send_all_socket_to_user'] = true;        
        $socket_data['to_socket_id'] = $user->socket_id;
        $socket_data['message'] = Auth::user()->name . " : " . $request->message;
        $response = $this->SocketIoEmit($socket_data);
        print_r($response);       
    }

    public function index(Request $request){
        // fetch all users apart from the authenticated user
        $users = User::where('id', '<>', Auth::id())->get();
        return view('agora-chat', ['users' => $users]);
    }

    public function chat(Request $request,$id =0){
        // fetch all users apart from the authenticated user
        $chat_with_name = "";
        $chat_with_id = "";
        if($id > 0){
            $chat_with =array();
            $user = User::where('id',$id)->first()->toArray();           
            $chat_with_id =$user['id'];
            $chat_with_name =$user['name'];
        }        
        //$users = User::where('id', '<>', Auth::id())->get()->toArray();      
        //return view('chat', compact('listusers','chat_with_name', 'chat_with_id'));
        $Logeduser = User::find(Auth::id());        
        return view('chat', [
                'listusers' => json_encode($this->getContacts()), 
                'chat_with_name' => $chat_with_name,
                'chat_with_id'=>$chat_with_id,
                'Logeduser' =>$Logeduser
            ]
        );
    }

    public function alluser(Request $request){
        // fetch all users apart from the authenticated user
        $users = User::where('id', '<>', Auth::id())->get();
        return view('users', ['users' => $users]);
    }

    public function token(Request $request){
        //return false; 
        $appID = env('AGORA_APP_ID');
        $appCertificate = env('AGORA_APP_CERTIFICATE');
        $channelName = $request->channelName;
        $user = Auth::user()->name;
        $user_id = Auth::id();
        //$role = RtcTokenBuilder::RoleAttendee;
        $role = 0;
        $expireTimeInSeconds = 3600;
        //$expireTimeInSeconds = 0;
        //$currentTimestamp = now()->getTimestamp() ; 

        $date = new \DateTime("now", new \DateTimeZone('UTC'));
        $currentTimestamp = $date->getTimestamp();
        $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;
        
       // $privilegeExpiredTs = 1446455471;
        if($request->rtm == 1){
            $role = 1;
            $token = RtmTokenBuilder::buildToken($appID, $appCertificate, $channelName, $user, $role, $privilegeExpiredTs);
        }else{
            $token = RtcTokenBuilder::buildTokenWithUserAccount($appID, $appCertificate, $channelName, $user, $role, $privilegeExpiredTs);
        }
        return $token;
    }

    public function callUser(Request $request){
        $data['userToCall'] = $request->user_to_call;
        $to_user = User::find($request->user_to_call);
        $data['channelName'] = $request->channel_name;
        $data['video_call'] = $request->video_call;
        $data['audio_call'] = $request->audio_call;
        $data['only_chat'] =false;
        if(!empty($request->only_chat)){            
            $data['only_chat'] = $request->only_chat;
            $data['channelMessage'] = $request->channelMessage;            
        }
        $user = User::find(Auth::id());
        $data['href'] = url('chat/'.Auth::id());
        $data['user_name'] = $user->name;
        $data['from'] = Auth::id();

        // if($request->to_online==0){ 
            $Notify_data = array();
            $user = User::find(Auth::id());
            $Notify_data['user'] = $user->id;            
            $Notify_data['url']  = url('profile/'.$request->call_from_id);
            $message= "New Incoming Call from ". $user->name; 
            if(!empty($request->only_chat)){ 
                $message= "New Message from ". $user->name; 
                $Notify_data['url'] = url('chat/'.Auth::id()); 
            }
            $Notify_data['to_user']      = $request->user_to_call; 
            $Notify_data['to_socket_id'] = $to_user->socket_id;   
            $Notify_data['message']      = $message;
            $Notify_data['send_all_socket_to_user'] = true;
            $Notify_data['count_notification']=1;                 
            $Notify_data['event_name'] = "notify-channel"; 
            $response = $this->SocketIoEmit($Notify_data);
           // broadcast(new Notify($Notify_data))->toOthers();
        //}else{
            //broadcast(new MakeAgoraCall($data))->toOthers();
            
            $data['event_name'] = "MakeAgoraCall";                 
            $data['to_user'] = $request->user_to_call;        
            $data['send_all_socket_to_user'] = true;
                    
            $data['to_socket_id'] = $to_user->socket_id;            
            $response = $this->SocketIoEmit($data);
        //}
    }

    public function endcallUser(Request $request){
        $data['userToCall'] = $request->user_to_call;
        $data['channelName'] = $request->channel_name;
        $data['from'] = Auth::id();
        broadcast(new MakeAgoraCall($data))->toOthers();
    }


    public function userprofile(Request $request,$userid){
        
        // user to call
        $user = User::where('id',$userid)->first();  
        $user->videocall = true;
        $user->onlyvoice = true;  
        $user->onlychat   = false;        
       
        $user->id_new=$user->id;
        if($user->id==8){            
            $user->id_new=$user->id;
        }
        // loged user data
        $authUSer= array();
        $authUSer = User::where('id',Auth::id())->first(); 
       
        $authUSer->id_new=$authUSer->id;        
        if($authUSer->id==8){           
            $authUSer->id_new=$authUSer->id;
        }       

        $users = User::where('id', '<>', Auth::id())->get(); 
        
        return view('user-profile')->with([
            'user' =>$user,
            'users' =>$users,
            'authUSer' =>$authUSer
        ]);
    }

    public function SaveChat(Request $request){

        $chat = new Chat();
        $chat->from_id = $request->from_id;
        $chat->to_id = $request->to_id;
        $chat->message = $request->message;
        if($request->join_chat==1){ 
            $chat->seen = 1;
        }
        $chat->save();

        if($request->to_online==0){ 
            $data = array();
            $data['user'] = $request->to_id;
            $user = User::find(Auth::id());
            $message= "New message from ". $user->name;   

            $data['message'] = $message;
            $data['sub_message'] = $request->message;
            $data['url'] = url('chat/'.Auth::id());
            $data['count_notification']=1;                 
            //broadcast(new Notify($data))->toOthers();
        }   
        return Response::json([
            'chat'=>$chat
        ]);
        
    }

    public function GetChatAttachent($from_id,$to_id){
        //DB::enableQueryLog();        
        $chats = Chat::with('userfrom')->with('userto')->whereNotNull('image')
        ->where(function($q) use($from_id,$to_id) {
            $q->where('from_id',$from_id)->where('to_id',$to_id)->orWhere(function($q) use($from_id,$to_id) {
                $q->where('from_id',$to_id)->where('to_id',$from_id);
            });
        })->where('deleted_by','!=',Auth::user()->id)
        ->orderBy('id', 'DESC')
        ->paginate(60);
        //dd(DB::getQueryLog());
        $data['chats']= $chats;
        return Response::json($data, 200); 
    }
    

    public function GetChat(Request $request,$from_id,$to_id){
        if(!empty($request->type) && $request->type =="attach"){
            return $this->GetChatAttachent($from_id,$to_id);
        }
        $data=array();
        $data['message']="No record Found";
        if(Auth::id() !=$from_id && Auth::id() !=$to_id){
            return Response::json($data, 200);
        }
        $chats = Chat::with('userfrom')->with('userto')
        ->where(function($q) use($from_id,$to_id) {
            $q->where('from_id',$from_id)->where('to_id',$to_id)
                ->orWhere(function($q) use($from_id,$to_id) {
                    $q->where('from_id',$to_id)->where('to_id',$from_id);
            });
        })->where('deleted_by','!=',Auth::user()->id)->orderBy('id', 'DESC')
        ->paginate();       
        //->paginate(10);
        
        $data['chats']= [];
        if(!empty($chats)){
            $data['chats']= $chats;
        }
        return Response::json($data, 200);   
    }
    public function getChathistory(Request $request){
        if(!empty($request->type) && $request->type =="attach"){
            return $this->GetChatAttachent($from_id,$to_id);
        }
        $from_id = $request->from_id;
        $to_id =  $request->to_id;
        $data=array();
        $data['message']="No record Found";
        if(Auth::id() !=$from_id && Auth::id() !=$to_id){
            return Response::json($data, 200);
        }
        $chats = Chat::with('userfrom')->with('userto')->where(function($q) use($from_id,$to_id) {
            $q->where('from_id',$from_id)->where('to_id',$to_id)
                ->orWhere(function($q) use($from_id,$to_id) {
                    $q->where('from_id',$to_id)->where('to_id',$from_id);
            });
        })->where('deleted_by','!=',Auth::user()->id)->orderBy('id', 'DESC')->paginate();

        $data['chats']= [];
        if(!empty($chats)){ 
            $chats_array = array();
            foreach ($chats as $key => $value) {
               $chats_array[$key]['from_id']    = $value->from_id;
               $chats_array[$key]['to_id']      = $value->to_id;
               $chats_array[$key]['message']    = $value->message;
               $chats_array[$key]['filetype']   = $value->filetype;
               $chats_array[$key]['image']      = $value->image;
               $chats_array[$key]['created_at'] = $value->created_at->diffForHumans();
            }            
            array_reverse($chats_array);

            $data['chats']   = $chats;
            $data['message'] = view('list-message',["auth_id" =>Auth::id(),'chats'=>array_reverse($chats_array)])->render();
        }        
        return Response::json($data, 200);
        //return view('list-message',["auth_id" =>Auth::id(),'chats'=>$chats]);  
    }

    public function SaveChatFile(Request $request){
        $this->validate($request, [
            'file' => 'required|mimes:jpg,jpeg,png,csv,txt,xlx,xls,pdf,mp4,avi,3gp,mp3,wav,doc,docx|max:20480'
        ]); 
        if ($request->hasFile('file')) {
            $sub_folder="";
            if($request->from_id > $request->to_id){
                $sub_folder = 'channel_'.$request->from_id.'_'.$request->to_id;
            }else{
                $sub_folder = 'channel_'.$request->to_id.'_'.$request->from_id;                
            }

            $path = public_path().'/chatfiles/'.$sub_folder;
            File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);
            $image1 = $request->file('file');
            $name1 = time().'.'.$image1->getClientOriginalExtension();
            $destinationPath = public_path("/chatfiles/".$sub_folder);
            $image1->move($destinationPath, $name1);

            $chat = new Chat();
            $chat->message = time()."_".$request->message;
            $chat->filename = $request->message;
            $chat->from_id = $request->from_id;
            $chat->to_id = $request->to_id;
            $chat->image="/chatfiles/".$sub_folder."/".$name1;
            $chat->filetype=$image1->getClientOriginalExtension();
            $chat->save();            
            return response()->json([
                'success'=>'File uploaded successfully.'
            ]);
        }       
    }


    /**
     * Make messages between the sender [Auth user] and
     * the receiver [User id] as seen.
     *
     * @param int $user_id
     * @return bool
     */
    public function makeSeen(Request $request){
            Chat::Where('from_id',$request->to_user_id)
                ->where('to_id',Auth::user()->id)
                ->where('seen',0)
                ->update(['seen' => 1]);
            return 1;
    }


    public function deleteChats(Request $request){

        $from_id =Auth::user()->id;  
        $to_id = $request->to_id;
        $data =array();          

        $chats = Chat::where(function($q) use($from_id,$to_id) {
            $q->where('from_id',$from_id)->where('to_id',$to_id);
        })->orWhere(function($q) use($from_id,$to_id) {
            $q->where('from_id',$to_id)->where('to_id',$from_id);
        })->where('deleted_by','!=',$from_id)->get();

        if(!empty($chats)){
            $data['total_delete'] = $chats->count();            
            foreach ($chats as $key => $chat) {
                //return response()->json($chat);
                if($chat->deleted_by !=$from_id &&  $chat->deleted_by > 0){
                    // delete  this chat for both user
                    Chat::find($chat->id)->delete();
                }else{
                    // update deleted_by by loged user id
                    Chat::where('id',$chat->id)->update(['deleted_by' => $from_id]);
                }
            }
        }
        return response()->json($data);
    }

   
    /**
     * Get contacts list
     *
     * @param Request $request
     * @return JSON response
     */
    public function getContacts(){
        $loged_id = Auth::id();       
        $sql    = " SELECT 
                    `users`.id,`users`.name,`users`.id, 
                        (SELECT `id` FROM `chats` WHERE ( (`from_id` = $loged_id AND `to_id` = users.id) OR (`from_id` = users.id AND `to_id` = $loged_id ) ) AND deleted_by != $loged_id ORDER BY `id` DESC, `created_at` desc limit 1) as `chat_id` ,
                        (select count(*) from `chats` where ( (`from_id` = users.id and `to_id` = $loged_id) ) and seen=0 AND deleted_by != $loged_id) as `unSeenMsg`
                        from `users` where users.id <> $loged_id  ORDER BY `chat_id` DESC" ; 
        
        $All_users = DB::select($sql);        
        $listusers = array();
        if(!empty($All_users)){
            foreach ($All_users as $key => $user) {
                $listusers[$key] = $user;
                if(!empty($user->chat_id)){
                   $listusers[$key]->chat=Chat::find($user->chat_id)->toArray();
                }                
            }
        }
        return  $listusers;
    }

    public function getContactsObject(){
        $loged_id = Auth::id();       
        $sql = "SELECT `users`.id,`users`.name,`users`.id,`users`.socket_id, 
                    (SELECT `id` FROM `chats` WHERE ( (`from_id` = $loged_id AND `to_id` = users.id) OR (`from_id` = users.id AND `to_id` = $loged_id ) ) AND deleted_by != $loged_id ORDER BY `id` DESC limit 1) as `chat_id` ,
                        
                    (select count(*) from `chats` where ( (`from_id` = users.id and `to_id` = $loged_id) ) and seen=0 AND deleted_by != $loged_id) as `unSeenMsg`
                        

                    from `users` where users.id <> $loged_id  ORDER BY `chat_id` DESC" ; 
        //echo $sql;

        //DB::enableQueryLog();        
        //dd(DB::getQueryLog());
        /*$users = User::select([
            'users.id',
            'users.name',
            'users.socket_id'
        ])->addSelect([
            'chat_id' => Chat::select(['chats.id'])->where(function($q) use($loged_id) {
                            $q->where('chats.from_id',$loged_id)->where('chats.to_id','users.id');
                        })->orWhere(function($q) use($loged_id) {
                            $q->where('chats.from_id','users.id')->where('chats.to_id',$loged_id);
                        })->where('chats.deleted_by','!=',$loged_id)
                        ->orderBy('chats.id', 'desc')
                        ->limit(1)
        ])->addSelect([
            'unSeenMsg' => Chat::where('chats.to_id','=',$loged_id)
                        ->where('chats.from_id','=','users.id')
                        ->where('chats.deleted_by','!=',$loged_id)
                        ->where('chats.seen',0)->count()
                        
        ])->addSelect([
            'chat_idP' => Chat::select(['chats.id'])->where(function($q) use($loged_id) {
                            $q->where('chats.from_id',$loged_id)->where('chats.to_id','users.id');
                        })->orWhere(function($q) use($loged_id) {
                            $q->where('chats.from_id','users.id')->where('chats.to_id',$loged_id);
                        })->where('chats.deleted_by','!=',$loged_id)
                        ->orderBy('chats.id', 'desc')
                        ->limit(1)
        ])->where('users.id', '<>', Auth::id())->get();

        dd(DB::getQueryLog());
        dd($users);*/
        $All_users = DB::select($sql);        
        $listusers = array();
        if(!empty($All_users)){
            foreach ($All_users as $key => $user) {
                $listusers[$key] = $user;
                if(!empty($user->chat_id)){
                   $listusers[$key]->chat=Chat::where('id',$user->chat_id)->first();
                }                
            }
        }
        return $listusers;
    }

    public function refreshUserlist(){
        return response()->json([
            'userList'=>$this->getContacts()
        ]);
    }


    public function getContacts_old(Request $request){
       /* $from_id = Auth::id();
        //DB::enableQueryLog();
        $users = User::query()
            ->addSelect(
                [
                    'chat_id' => Chat::select('id')->
                    where(function($q) use($from_id) {
                        $q->where('from_id',$from_id)->where('to_id','users.id');
                    })->orWhere(function($q) use($from_id) {
                        $q->where('from_id','users.id')->where('to_id',$from_id);
                    })->orderBy('id', 'DESC')->take(1)                
                ]
            )->get();*/
        //dd(DB::getQueryLog());
        //dd($users) ;
    }

    public function auth(Request $request){

        $data = array();
        $pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            array(
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'useTLS' => true,                
            )
        );        

        $userdata=array();        
        $user = User::find(Auth::id());
        
        $user->id_new=$user->id;
        $userdata['user_id'] = Auth::user()->id;
        if($user->id==8){            
            $user->id_new=$user->id;
            $userdata['user_id'] = Auth::user()->id;
        }
        $userdata['user_info']['id'] =$user->id_new;
        $userdata['user_info']['name'] =$user->name;
        $userdata['user_info']['email'] =$user->email;
       
        $data['channel_data'] = json_encode($userdata);
        $pusher = $pusher->socket_auth($request->request->get('channel_name'),$request->request->get('socket_id'),json_encode($userdata));
        return $pusher;
    }

    public function rating(Request $request){
        die("ok  do calculation");
    }


    /**
     * SocketIoEmit
     *
     * @param Request $request
     * @return JSON response
        $data[
            'event_name'    => 'name of event',
            'to_socket_id'  => 'to_socket_id',
            'message'       => 'message',
        ]
    */
    public function SocketIoEmit($data){
        echo "<pre>";print_r($data);die("dsaf");
        $response = array();
        //echo env('SOCKET_IO_SERVER');
        //die("ASdf");
        try {
            $response = (new \GuzzleHttp\Client())->post('http://127.0.0.1:2021', [
                'transports' => ['websocket'],
                'form_params' => [
                    'content' => $data->content,
                    'to' => $data->to ?? '',
                    'type' => $data->type ?? 'publish',
                ],
            ]);
        }
        catch(Exception $e) {
           $response["status"] =0;          
           $response["Message"] =$e->getMessage();          
        }
        return $response;
    }

    public function SocketIoEmit_($data){
        $response = array();
        try {
            $socketIo = new Client(new Version3X(env('SOCKET_IO_SERVER')));
            $socketIo->initialize();
            $socketIo->emit('all',$data);
            $socketIo->close();
            $response['status'] = 1;
            $response['message'] = 'Success';
            $response['socket'] = $socketIo;
        }
        catch(Exception $e) {
           $response["status"] =0;          
           $response["Message"] =$e->getMessage();          
        }
        return $response;
    }

    public function getcurl(){
        //die("sadf");
        $data = array();
        /*$data[] = $this->check('ICPUSDT',50.77,4);
        $data[] = $this->check('MATICUSDT',1.41870,100);
        $data[] = $this->check('DENTUSDT',0.004893,50000);
        $data[] = $this->check('BTCUSDT',0,0);
        $data[] = $this->check('AAVEUSDT',356,1);
        $data[] = $this->check('TRXUSDT',0.11520,6000);*/
        //$data[] = $this->check('DOGEUSDT',0.251630,400);
        // $data[] = $this->check('DOGEUSDT',0.268330,300);
        //$data[] = $this->check('MASKUSDT',11.3534,5);
        $data[] = $this->check('BTCUSDT',0,0);
        //$data[] = $this->check('ICPUSDT',50.77,4);
        $data[] = $this->check('SOLUSDT',150.30,2);
        $data[] = $this->check('OMGUSDT',14.1326,5);
        //$data[] = $this->check('ATAUSDT',0.96,150);
        //$data[] = $this->check('ATAUSDT',0,0);
        $data[] = $this->check('DOGEUSDT',0.241225,1000);
        //$data[] = $this->check('DOTUSDT',33.326,5);
        
        $total_g=0;
        foreach ($data as $key => $d) {
           $total_g =$total_g + $d['gain'];
        }

        $data['total_g']=$total_g;
        // json_encode(value)
        Storage::disk('local')->put('example.txt', json_encode($data) );
        return response()->json($data);
    }
    public function check($s,$bp,$q){
        $response = Http::get('https://api.binance.com/api/v3/ticker/price?symbol='.$s);
        $jsonData = $response->json();
        $now = $jsonData['price'];
        $gain = ($now - $bp) * $q;
        $data = array();
        $data['sy'] =$s;
        $data['now'] =$now;
        $data['buy'] =$bp;
        $data['gain'] =round($gain,2);
        return $data;
    }
   
}
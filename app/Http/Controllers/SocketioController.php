<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Workerman\Worker;
use PHPSocketIO\SocketIO;

use App\Models\User;
use App\Models\Chat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File; 
use Cache;
use DB;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Http;

class SocketioController extends Controller
{
    public $users = array();
    public $userslist = array();
    public $userslistFinal = array();
    public $counter =0;
    


    public function index()
    {
       $io = new SocketIO(2021);       
       $User_Model = new User();
       $Chat_Model = Chat::class;

        $io->on('connection', function ($socket) use ($io,$User_Model,$Chat_Model) {
           //echo"<pre>";print_r($users);echo"</pre>";
            echo "\nconnected socket _id : ".$socket->handshake['query']['user_id']." =>" .$socket->id;
            $data = array();
            $data['socket_id']=$socket->id;

            // update user data
            $user = $User_Model::find($socket->handshake['query']['user_id']);
            $user->socket_id =  $socket->id;
            $user->save();    
            //echo"<pre>";print_r($user);echo"</pre>";

            $socket->on('bmessage', function($data) use ($io){
               $io->emit('bmessage', $data);
            });

            $socket->on('CallUser', function($data) use ($io){
               $this->callUser($data,$io);
            });
           
            $this->UserLogIn($socket,$io);

            // private-message toperticular socket id 
            $socket->on('private-message', function($data) use ($io,$User_Model,$Chat_Model){               
               $responce = $data;
                if(!empty($data['to_id']) && !empty($data['message']) && !empty($data['from_id'])){
                    if(!empty($data['image_data'])){
                        $responce = $this->saveChatFile($data,$io);
                        if(!empty($responce['to_socket_id']))
                           $io->to($responce['to_socket_id'])->emit('private-message',$responce);
                       
                        $responce['me']=1;
                        if(!empty($responce['from_socket_id']))
                            $io->to($responce['from_socket_id'])->emit('private-message',$responce);
                    }else{                       
                       $responce = $this->saveChat($data);
                       $io->to($responce['to_socket_id'])->emit('private-message',$responce);
                    }                    
                }
            });            
            //echo"<pre>";print_r($this->userslistFinal);echo"</pre>";            
            $socket->on('disconnect', function () use($socket,$io) {
               echo "\ndisconnected socket_id : " .$socket->id;
               $this->UserLogedOut($socket,$io);  
            });            
       });
       Worker::runAll();
    }

    public function saveChat($data){
        $Chat_Model_ = new Chat();
        $Chat_Model_->from_id = $data['from_id'];
        $Chat_Model_->to_id = $data['to_id'];
        $Chat_Model_->message = $data['message'];                    
        $Chat_Model_->save();

        $to_user_data = User::find($data['to_id']);
        $from_user_data = User::find($data['from_id']);
        $data['from_name'] = $from_user_data->name;
        $data['to_socket_id'] = $to_user_data->socket_id;
        return $data;
    }

    public function saveChatFile($data,$io){
        if($data['from_id'] > $data['to_id']){
            $sub_folder = 'channel_'.$data['from_id'].'_'.$data['to_id'];
        }else{
            $sub_folder = 'channel_'.$data['to_id'].'_'.$data['from_id'];                
        }

        $image_64 = $data['image_data']['file_data'];echo "\n";
        $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];echo "\n";   // .jpg .png .pdf
        $replace = substr($image_64, 0, strpos($image_64, ',')+1);
        $imageName = time().'.'.$extension;
        $path = public_path().'/chatfiles/'.$sub_folder;echo "\n";
        File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);

        $image_parts = explode(";base64,", $data['image_data']['file_data']);
        file_put_contents($path."/".$imageName, base64_decode($image_parts[1]) );
        
        $Chat_Model_ = new Chat();
        $Chat_Model_->from_id = $data['from_id'];
        $Chat_Model_->to_id = $data['to_id'];
        $Chat_Model_->message =$imageName;                    
        $Chat_Model_->filename =$data['image_data']['file_name'];                    
        $Chat_Model_->image = "/chatfiles/".$sub_folder."/".$imageName;;                    
        $Chat_Model_->filetype = $extension;                    
        $Chat_Model_->save();
        $to_user_data = User::find($data['to_id']);
        $from_user_data = User::find($data['from_id']);

        if(strtolower($extension) =='jpg' || strtolower($extension) =='png' || strtolower($extension) =='jpeg'){
            $data['message']="<img height='150px' width='150px' src='".url('/').$Chat_Model_->image."'>";
        }else{            
            $data['message']="<iframe height='150px' width='150px' src='".url('/').$Chat_Model_->image."'></iframe>";
        }
        $data['from_name'] = $from_user_data->name;
        $data['to_socket_id'] = $to_user_data->socket_id;
        $data['from_socket_id'] = $from_user_data->socket_id;
        return $data;        
    }


    public function UserLogIn($socket,$io){
        if(!empty($socket->id)){
            $user = array();
            if(!empty($socket->handshake['query']['user_id'])){                
                if(!empty($socket->handshake['query']['user_name'])){
                    $user['name'] =$socket->handshake['query']['user_name'];
                }
                if(!empty($socket->handshake['query']['user_id'])){
                    $user['id'] = (int)$socket->handshake['query']['user_id'];
                }                
                if(!empty($socket->handshake['query']['user_email'])){
                    $user['email'] = $socket->handshake['query']['user_email'];
                }
                $user['socket_id'] = $socket->id;
                $this->users[$socket->handshake['query']['user_id']][$socket->id] = $user;
                array_push($this->userslistFinal, $user);
            }
            sleep(1);
            $io->emit('usersList', array_values($this->userslistFinal) );
        }
    }

    public function UserLogedOut($socket,$io){

        if(!empty( $this->users[$socket->handshake['query']['user_id']][$socket->id])){
            unset( $this->users[$socket->handshake['query']['user_id']][$socket->id]);
        }
        foreach ($this->userslistFinal as $key => $value) {
           //echo "<pre>";print_r($value);echo"<pre>";
            if(!empty($socket->handshake['query']['user_id'])){
                if($socket->handshake['query']['user_id'] == $value['id'] ){
                    unset($this->userslistFinal[$key]);
                }
            }
        }
        $io->emit('usersList', array_values($this->userslistFinal) );
        //echo"<pre>";print_r($this->userslistFinal);echo"</pre>";
    }


    public function callUser($data,$io){
        //echo"<pre>";print_r($data);echo"</pre>";
       
        $from_id        = $data['from_id'];
        $to_id          = $data['user_to_call'];

        $channel_name   ="";
        if(!empty($data['channel_name']))
            $channel_name   = $data['channel_name'];

        $channelMessage   ="";
        if(!empty($data['channelMessage']))
            $channelMessage     = $data['channelMessage'];

        $from_user      = User::find($from_id);
        $to_user        = User::find($to_id);

        $MakeAgoraCall = array();
        $MakeAgoraCall['userToCall']    = $to_id;        
        $MakeAgoraCall['channelName']   = $channel_name;
        $MakeAgoraCall['channelMessage']= $channelMessage;
        $MakeAgoraCall['user_name']     = $from_user->name;
        $MakeAgoraCall['from']          = $from_id;  
        $MakeAgoraCall['to_user']       = $to_id;
        
        $MakeAgoraCall['only_chat'] =false;         
        if(!empty($data['only_chat']) && $data['only_chat']){            
            $MakeAgoraCall['only_chat'] =   $data['only_chat'];            
            $MakeAgoraCall['href']      =   url('chat/'.$from_id);            
        }

        if( (!empty($data['video_call']) && $data['video_call']) || (!empty($data['video_call']) && $data['video_call']) ){
            $MakeAgoraCall['video_call'] =  $data['video_call'];
            $MakeAgoraCall['audio_call'] =  $data['audio_call'];
        }    
        
        if(!empty($to_user->socket_id))
            $io->to($to_user->socket_id)->emit('MakeAgoraCall',$MakeAgoraCall);

        // send notification 

        // if($request->to_online==0){ 
        $Notify_data = array();
        $user = $from_user;
        $Notify_data['user'] = $user->id;            
        $Notify_data['url']  = url('profile/'.$user->id);
        $message= "New Incoming Call from ". $user->name; 
        if(!empty($data['only_chat']) && $data['only_chat']){ 
            $message= "New Message from ". $user->name; 
            $Notify_data['url'] = url('chat/'.$from_id); 
        }
        $Notify_data['to_user']      = $to_id;               
        $Notify_data['message']      = $message;            
        $Notify_data['count_notification']=1;   
        if(!empty($to_user->socket_id))
            $io->to($to_user->socket_id)->emit('notify-channel',$Notify_data);              
            
    }
}
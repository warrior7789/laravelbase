<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Workerman\Worker;
use PHPSocketIO\SocketIO;
use App\Models\User;


class _socketConnect extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */



    protected $signature = 'socket:run {action}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Workerman PHPSocketIO custome';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */

    public $users = array();
    public $userslist = array();
    public $userslistFinal = array();

    public $counter =0;


   

    public function handle(){
        //return 0;

        $io = new SocketIO(2021);
       
        $User_Model = new User();

        $io->on('connection', function ($socket) use ($io,$User_Model) {
            //echo"<pre>";print_r($users);echo"</pre>";
            echo "\nconnected socket _id : ".$socket->handshake['query']['user_id']." =>" .$socket->id;
            $data = array();
            $data['socket_id']=$socket->id;

            $user = $User_Model::find($socket->handshake['query']['user_id']);
            $user->socket_id =  $socket->id;
            $user->save();    
            //echo"<pre>";print_r($user);echo"</pre>";
            $socket->on('bmessage', function($data) use ($io){
                $io->emit('bmessage', $data);
            });

            $io->to($socket->id)->emit('mysocketid',json_encode($data));
            $this->UserLogIn($socket,$io);

            // private-message toperticular socket id 
            $socket->on('private-message', function($data) use ($io){
               $io->to($data->to_socket_id)->emit('private-message',json_encode($data));
            });
            
            //echo"<pre>";print_r($this->userslistFinal);echo"</pre>";            
            $socket->on('disconnect', function () use($socket,$io) {
                echo "\ndisconnected socket_id : " .$socket->id;               
                $this->UserLogedOut($socket,$io);                
            });

            // general 
            $socket->on('all', function($data) use ($socket,$io){
                 echo"<pre>";print_r($data);echo"</pre>";
                if(!empty($data->to_socket_id)) {
                    if(!empty($data->send_all_socket_to_user) && $data->send_all_socket_to_user){
                        $user_details = $this->users[$data->to_user];
                        if(!empty($user_details)){
                            foreach ($user_details as $key => $value) {
                                 $io->to($key)->emit($data->event_name,$data);
                            }
                        }
                    }else{
                        $io->to($data->to_socket_id)->emit($data->event_name,$data);
                    }
                }else{
                    $io->emit($data->event_name,$data);
                }
            });
        });
        Worker::runAll();
    }

    public function UserLogIn($socket,$io){
        if(!empty($socket->id)){
            $user = array();
            if(!empty($socket->handshake['query']['user_id'])){                
                if(!empty($socket->handshake['query']['user_name'])){
                    $user['name'] =$socket->handshake['query']['user_name'];
                }
                if(!empty($socket->handshake['query']['user_id'])){
                    $user['id'] = $socket->handshake['query']['user_id'];
                }                
                if(!empty($socket->handshake['query']['user_email'])){
                    $user['email'] = $socket->handshake['query']['user_email'];
                }
                $user['socket_id'] = $socket->id;
                $this->users[$socket->handshake['query']['user_id']][$socket->id] = $user;
                array_push($this->userslistFinal, $user);
            }
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
}

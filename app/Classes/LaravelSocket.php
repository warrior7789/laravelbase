<?php

namespace App\Classes;


use App\Classes\Version3X;
use ElephantIO\Client, ElephantIO\Engine\SocketIO\Version2X;

class LaravelSocket 
{

    
	public $client;

    public function __construct(){
        $this->client = new Client(new Version3X(env('SOCKET_IO_SERVER')));
        $this->client->initialize();
    }
    
    public function brodcastToall($eventName,$message){
    	$this->client->emit($eventName,$message);
    }
}


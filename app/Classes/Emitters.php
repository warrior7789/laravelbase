<?php
/**
 * This file is part of the Elephant.io package
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 *
 * @copyright Wisembly
 * @license   http://www.opensource.org/licenses/MIT-License MIT License
 */

namespace App\Classes;


class Emitters
{
    protected $_rooms = array();
    
    protected $_flags = array();
    
    protected $_remoteIp = '';
    
    protected $_remotePort = 2206;
    
    protected $_key = 'socket.io#/#';
    
    protected $_client = null;
    
    protected $_context = null;

    public function __construct($ip = '127.0.0.1', $port = 2206, $context = null)
    {   


        $this->_remoteIp = $ip;
        $this->_remotePort = $port;
        $this->_context = $context;
        $this->connect();
    }
    
    protected function connect()
    {   

        if(is_array($this->_context))
        {
            $context = stream_context_create($this->_context);
            $this->_client = stream_socket_client("tcp://{$this->_remoteIp}:{$this->_remotePort}", $errno, $errmsg, 3, STREAM_CLIENT_CONNECT, $context);
        }else{
            $this->_client = stream_socket_client("tcp://{$this->_remoteIp}:{$this->_remotePort}", $errno, $errmsg, 3);
        }
        
        if(!$this->_client)
        {
            throw new \Exception($errmsg);
        }
    }
    
    public function __get($name)
    {
        if($name === 'broadcast')
        {
            $this->_flags['broadcast'] = true;
            return $this;
        }
        return null;
    }
    
    public function to($name)
    {
        if(!isset($this->_rooms[$name]))
        {
            $this->_rooms[$name] = $name;
        }
        return $this;
    }
    
    public function in($name)
    {
        return $this->to($name);
    }
    
    public function emit($ev)
    {   
        //die("sadfsafsf");
        if(feof($this->_client))
        {
            $this->connect();
        }
        
        $args = func_get_args();

        //echo "<pre>";print_r($args);die("dsaf");
    
        $parserType = 2;// Parser::EVENT

        $packet = array('type'=> $parserType, 'data'=> $args, 'nsp'=>'/' );
         // echo "<pre>";print_r($packet);die("dsaf");
        $buffer = serialize(array(
                'type' => 'publish', 
                'channels'=>array($this->_key), 
                'data' => array('-', $packet, 
                        array(
                                'rooms' => $this->_rooms,
                                'flags' => $this->_flags
                                )
                        )
                )
        );
        
        
        $buffer = pack('N', strlen($buffer)+4).$buffer;
        
        fwrite($this->_client, $buffer);

        $this->_rooms = array();
        $this->_flags = array();
        return $this;
    }
}

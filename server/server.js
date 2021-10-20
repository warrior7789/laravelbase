const cors = require('cors');
require('dotenv').config({path: '../.env'});

const database = {
    'name'      : process.env.DB_DATABASE,
    'user'      : process.env.DB_USERNAME,
    'password'  : process.env.DB_PASSWORD,
    'host'      : process.env.DB_HOST,
}

var  server =   require('http').createServer();
var io      =   require('socket.io')(server,{
					allowEIO3: true,
					cors: {
						//origin: ['*'],
						hamdlePreflightRequest:(req,res)=>{
							res.write({
								"Access-control-Allow-Origin":"",
								"Access-control-Allow-Methods":"GET,POST",
								"Access-control-Allow-Credentials":true,
							})
						}
					}
				});
const winston  = require('winston');
var port       = 1338;

const logConfiguration = {
    'transports': [
        new winston.transports.Console({})
    ]
};

logger = winston.createLogger(logConfiguration);
// Logger config
//logger.remove(logger.transports.Console);
//logger.add(logger.transports.Console, { colorize: true, timestamp: true });
logger.info('SocketIO > listening on port ' + port);

const connectedUsers = {};

const userlist = [];







io.on('connection', function (socket){

    if(socket.handshake.query.user_id > 0){
       addClientToMap(socket);
    }

    //io.emit("user connected");
    
    
    //logger.info('SocketIO > Connected socket ' + socket.id);    

    socket.on('disconnect', function () { 

        if(socket.handshake.query.user_id > 0){
            let leaving = {
                email : socket.handshake.query.user_email,
                id : socket.handshake.query.user_id,
                name : socket.handshake.query.user_name,
                socketId : socket.id
            }
            io.emit('leaving', leaving);
        
            removeClientFromMap(socket)
            io.emit('usersList', userlist);

        }
        
    });
    
    socket.on('broadcast', function (message) {
        //logger.info('newUser ElephantIO broadcast > ' + JSON.stringify(message));
        io.emit("broadcast", JSON.stringify(message));
    });

    socket.on('newUser', function(data){
        io.emit('newUser', data);  
    });


    socket.on('message', function(data){
        io.emit('message', data);
    });

    // private-message toperticular socket id 
    socket.on('private-message', function(data){
        io.to(data.to_socket_id).emit('private-message',JSON.stringify(data));
    });

    // data{
    //    'event_name' : 'private-message',
    //    'to_socket_id' : 'to_socket_id',
    //    'message'      : 'message'    
    //}
    socket.on('all', function(data){
        console.log("all event")        
        console.log(data)        
        if(data.to_socket_id){
            // send multiple device            
            if(data.send_all_socket_to_user){
                var user_details = connectedUsers[data.to_user];
                console.table(user_details)       
                Object.keys(user_details).forEach(to_socket_id => {
                    console.log("Emit => " +to_socket_id) // key , value
                    io.to(to_socket_id).emit(data.event_name,data);
                })
            }else{
                // send single device
                io.to(data.to_socket_id).emit(data.event_name,data);
            }
        }else{
            io.emit(data.event_name,data.message);
        }
    });

    // emit loges user list
    setTimeout(function () {     
        //io.emit('usersList', connectedUsers);
        if(socket.handshake.query.user_id > 0){
            var joinuser = {
                email : socket.handshake.query.user_email,
                id : socket.handshake.query.user_id,
                name : socket.handshake.query.user_name,
                socketId : socket.id
            };
            io.emit('joining', joinuser);
            io.emit('usersList', userlist);
            io.emit('here', userlist);
        }

    }, 2000)   

    //console.dir(connectedUsers, {'maxArrayLength': null});

});

function addClientToMap(socket){
    //console.log("=======================addClientToMap================");
    const joiningUserIndex = userlist.findIndex(
       (user) => parseInt(user.id) === parseInt(socket.handshake.query.user_id)
    );
    if(joiningUserIndex == -1){
        userlist.push({
            email : socket.handshake.query.user_email,
            id : parseInt(socket.handshake.query.user_id),
            name : socket.handshake.query.user_name,
            socketId : socket.id
        });
        //console.log(socket.handshake.query.user_email + " added to list")
    }else{
       // console.log(socket.handshake.query.user_email + " alread there in list")
    }



    if(!connectedUsers[socket.handshake.query.user_id]){
        connectedUsers[socket.handshake.query.user_id] ={};        
    }

    connectedUsers[socket.handshake.query.user_id][socket.id] = {};
    connectedUsers[socket.handshake.query.user_id][socket.id]['id'] = socket.id;
    connectedUsers[socket.handshake.query.user_id][socket.id]['email'] = socket.handshake.query.user_email;
    connectedUsers[socket.handshake.query.user_id][socket.id]['user_id'] = socket.handshake.query.user_id;
    connectedUsers[socket.handshake.query.user_id][socket.id]['name'] = socket.handshake.query.user_name;
       
    //console.table(connectedUsers);
    //console.log(joiningUserIndex);
    //console.table(userlist);    
    //console.log("===============================================================");

    printusers();
    
}


function removeClientFromMap(socket){
    console.log("=====================remove call ============================");
    //console.log("removeClientFromMap => " + socket.id)
    //console.log("removeClientFromMap user=> " + socket.handshake.query.user_id)
   //console.log(connectedUsers)
    if(connectedUsers[socket.handshake.query.user_id][socket.id]){
        //console.log("remove " + socket.id )
        delete connectedUsers[socket.handshake.query.user_id][socket.id];
    }else{
       // console.log("no fond to delete")
    }

    const leavingUserIndex = userlist.findIndex(
       (user) => parseInt(user.id) === parseInt(socket.handshake.query.user_id)
    );

    if(leavingUserIndex != -1){
        userlist.splice(leavingUserIndex, 1);
    }

   
   //console.table(connectedUsers);
   //console.log("leavingUserIndex " +leavingUserIndex);
   console.table(userlist);
   console.log("=============================================================");
}

function getUserOnline(){
    var online_users_id ={};    
    Object.keys(connectedUsers).forEach(key => {       
        var user_details = connectedUsers[key];

        Object.keys(user_details).forEach(key2 => {
            console.log(user_details[key2]) // key , value
        })
    })

}

function printusers(){
    console.log("=========Start Print USer ======================")
    Object.keys(connectedUsers).forEach(key => {       
        var user_details = connectedUsers[key];
        console.log("=============USER ID "+key +"===============")
        Object.keys(user_details).forEach(key2 => {
            console.table(user_details[key2]) // key , value
        })
    })
    console.log("=========End Print USer ======================")
}

server.listen(port);


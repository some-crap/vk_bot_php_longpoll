<?php
//ini_set("log_errors", 1);
//ini_set("error_log", "logsbot.txt");
include 'config.php';
$access_token = '';
$group_id = ; 
function GetLongPollServer($group_id, $access_token){
$request_params = array(
	'group_id' => $group_id,
	'access_token' => $access_token,
	'v' => '5.102' 
);
function vk_msg_send($peer_id,$text,$keyboard=null){
	if(is_null($keyboard)){
			$request_params = array(
			'message' => $text, 
			'peer_id' => $peer_id, 
			'access_token' => TOKEN,
			'v' => '5.87' 
			);
	}
	else{
	$request_params = array(
		'message' => $text, 
        'peer_id' => $peer_id, 
    	'keyboard' => $keyboard,
		'access_token' => TOKEN,
		'v' => '5.87' 
		);
	}
	$get_params = http_build_query($request_params); 
	file_get_contents('https://api.vk.com/method/messages.send?'. $get_params);
}
function tg_msg_send($chat_id,$text,$keyboard=null){
	if(is_null($keyboard)){
    	$request_params = array(
        	'text' => $text, 
    		'chat_id' => $chat_id
		);
	}
	else{
		$request_params = array(
    		'text' => $text, 
            'chat_id' => $chat_id, 
        	'reply_markup' => $keyboard
		);
	}
	$get_params = http_build_query($request_params); 
	file_get_contents('https://api.telegram.org/bot'.TG_TOKEN.'/sendMessage?'. $get_params);
	}
$get_params = http_build_query($request_params); 
$LongPollServer = json_decode(file_get_contents('https://api.vk.com/method/groups.getLongPollServer?'. $get_params));
print "LongPoll Server is found\n";
return $LongPollServer;
}
$server = GetLongPollServer($group_id, $access_token);
$ts = $server -> response -> ts;
$key = $server -> response -> key;
$server = $server -> response -> server;
print "Connecting to DB...\n";
$link = new mysqli(HOST, USERNAME, PASS, DBNAME);
if($link == false){
    print "No connection to DB.\n";
    exit();
}
else{
    print "Connection with DB is established\n";
}
print "Polling started\n";
while(true){
    if($link == false){
        print "DB connection is lost.\nReconnectiong...\n";
        while($link == false){
            $link = new mysqli(HOST, USERNAME, PASS, DBNAME);
        }
        print "Connected\n";
    }
    $data = json_decode(file_get_contents($server."?act=a_check&key=".$key."&ts=".$ts."&wait=25"));
    if(isset($data -> updates)){
        $ts = $data -> ts;
        $updates = $data -> updates;
        $c = 0;
        while($updates[$c]){
            include 'code.php';
            $c++;
        }
    }
    else{
        print "The connection is lost.\n Connecting...\n";
        $server = GetLongPollServer($group_id, $access_token);
        $ts = $server -> response -> ts;
        $key = $server -> response -> key;
        $server = $server -> response -> server;
    }
}
?>

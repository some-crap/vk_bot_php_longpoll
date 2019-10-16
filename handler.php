<?php
ini_set("log_errors", 1);
ini_set("error_log", "logsbot.txt");
$access_token = 'token';
$group_id = id; 
function GetLongPollServer($group_id, $access_token){
$request_params = array(
	'group_id' => $group_id,
	'access_token' => $access_token,
	'v' => '5.102' 
);
$get_params = http_build_query($request_params); 
$LongPollServer = json_decode(file_get_contents('https://api.vk.com/method/groups.getLongPollServer?'. $get_params));
print "LongPoll Server is found\n";
return $LongPollServer;
}
$server = GetLongPollServer($group_id, $access_token);
$ts = $server -> response -> ts;
$key = $server -> response -> key;
$server = $server -> response -> server;
print "Polling started\n";
while(true){
    $data = json_decode(file_get_contents($server."?act=a_check&key=".$key."&ts=".$ts."&wait=25"));
    if(isset($data -> updates)){
        $ts = $data -> ts;
        $updates = $data -> updates;
        $c = 0;
        while($updates[$c]){
            //echo $c.' ';
            echo $ts . "\n";
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

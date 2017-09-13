<?php

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ERROR);

include("Telegram.php");

$bot_id = "444284570:AAE6283re17v8X7oJgXTNIs1nCvvh_mJtAE";

$telegram = new Telegram($bot_id);

$msg   = "
ربات شماره ". $argv[1] ." ساخته شد.
لطفا کد را ارسال کنید.";

$content = array( 'chat_id' => 93077939, 'text' => $msg );
$telegram->sendMessage( $content );

$msg   = "لطفا کد را ارسال کنید.";
$content = array( 'chat_id' => 231812624, 'text' => $msg );
$telegram->sendMessage( $content );

$loop = true;
$code = '';
do{
	$req = $telegram->getUpdates();
	for ($i = 0; $i < $telegram->UpdateCount(); $i++) {
		// You NEED to call serveUpdate before accessing the values of message in Telegram Class
		$telegram->serveUpdate($i);
		$result  = $telegram->getData();
		$text = "";
		if(isset( $result["message"]["text"] )){
			$text    = $result["message"]["text"];
		}
		$chat_id = $result["message"]["chat"]["id"];
		$code = CheckText($text, $chat_id, $telegram);

		if($code != ""){
			$loop = false;
		}
	}
}while ($loop);

print_r($code);
//exec("cd /root/Telegram/bot/ && tmux kill-session -t create");
//exec("cd /root/Telegram/bot/ && tmux new-session -d -s create php create.php && tmux detach -s create");

function CheckText($text, $chat_id, $telegram){
	global $code;
	$iscode = preg_match('/^[0-9]{5}$/', $text) ? true : false;
	switch ( $text ) {
		case $iscode:
			$code = $text;
			break;
	}

	return $code;
}

function getLast(){
	$bots = glob("bot*");
	$i = 0;
	foreach($bots as $bot){
		$i++;
	}

	return ++$i;
}


?>







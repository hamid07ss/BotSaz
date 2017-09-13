<?php
/**
 * Telegram Bot Example whitout WebHook.
 * It uses getUpdates Telegram's API.
 * @author Gabriele Grillo <gabry.grillo@alice.it>
 */

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ERROR);

include("Telegram.php");

// number of arguments passed to the script
//--->$argc

// the arguments as an array. first argument is always the script name
//--->$argv

$admins = [93077939, 231812624];

$bot_id = "444284570:AAE6283re17v8X7oJgXTNIs1nCvvh_mJtAE";


// Get all the new updates and set the new correct update_id
$telegram = new Telegram($bot_id);
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
    if(in_array($chat_id, $admins))
		  CheckText($text, $chat_id, $telegram);
	}
}while (true);

function CheckText($text, $chat_id, $telegram){
	$isCreate = preg_match('/create .*/', $text) ? true : false;
	$Start = preg_match('/start .*/', $text) ? true : false;
	$Stop = preg_match('/stop .*/', $text) ? true : false;
	$create = preg_match('/new (.*) (.*)/', $text) ? true : false;
	if(!$create)
		$create = preg_match('/new (.*)/', $text) ? true : false;
	switch ( $text ) {
		case '/start':
			$option = array(
				array( "start tabchi", "stop tabchi" )
			);
			$keyb    = $telegram->buildKeyBoard( $option, false, true );
			$content = array( 'chat_id' => $chat_id,'reply_markup' => $keyb, 'text' => "برای ساخت ربات تبچی دستور زیر را ارسال کنید:
			new (phone number) (password)
			example:
			new 989101961375 farhad07ss
			" );
			$telegram->sendMessage( $content );
			break;
		case $create:
			preg_match('/new (.*) (.*)/', $text, $match);
			if(!isset($match[2])){
				preg_match('/new (.*)/', $text, $match);
				$cmd = "./all ". $match[1];
			}else{
				$cmd = "./all ". $match[1] . " " . $match[2];
			}
			
			$content = array( 'chat_id' => $chat_id, 'text' => "
			صبور باشید!
			" );
			$telegram->sendMessage( $content );

			print_r(shell_exec($cmd));
			break;
		case '/start':
			$option = array(
				array('start all', 'start tabchi', 'create tabchi')
			);
			// Get the keyboard
			$keyb    = $telegram->buildKeyBoard($option, false, true);
			$content = array( 'chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => "salam admin" );
			$telegram->sendMessage( $content );
			
			break;
		case $isCreate:
			preg_match('/create (.*):(.*)/', $text, $match);
			$botId = getLast();
			
			print_r($match); echo "\n";
			print_r($botId); echo "\n";
			print_r($match[1] . $match[2] . "\n");
			
			mkdir('bot'. $botId, '0777');
			$output = exec("touch /root/Telegram/bot/bot". $botId ."/token.txt");
			file_put_contents("/root/Telegram/bot/bot". $botId ."/token.txt", $match[1] .":". $match[2]);
			$output = exec("cat /root/Telegram/bot/pattern/data-pat.json > /root/Telegram/bot/bot". $botId ."/data.json");
			$output = exec("cat /root/Telegram/bot/pattern/index-pat.php > /root/Telegram/bot/bot". $botId ."/index.php");
			$output = exec("sed -i 's/BOT-ID/".$botId."/g' '/root/Telegram/bot/bot". $botId ."/index.php'");
			$output = exec("cat /root/Telegram/bot/pattern/index-tel.php > /root/Telegram/bot/bot". $botId ."/Tel.php");
			$botchatid = json_decode( file_get_contents( "chatIds.json" ) );
			$botchatid[] = $match[1];
			file_put_contents( "chatIds.json", $botchatid );
			
			
			$content = array( 'chat_id' => $chat_id, 'text' => 'ok' );
			$telegram->sendMessage( $content );
			break;
		case $Stop:
			preg_match('/stop (.*)/', $text, $match);
			if($match[1] == "tabchi"){
				$output = exec("cd ../tabchi/ && ./bot stopall");
				print_r($output);
				$content = array( 'chat_id' => $chat_id, 'text' => "انجام شد. \n" . $output );
				$telegram->sendMessage( $content );
			}
			break;
		case $Start:
			preg_match('/start (.*)/', $text, $match);
			if($match[1] == "tabchi"){
				$botp = 'auto';
				$output = exec("cd ../tabchi/ && tmux kill-session -t ". $botp);
				$output = exec("cd ../tabchi/ && tmux new-session -d -s ". $botp . " ./bot autolaunch && tmux detach -s ". $botp);
				print_r($output);
				$content = array( 'chat_id' => $chat_id, 'text' => "انجام شد. \n" . $output );
				$telegram->sendMessage( $content );
			}
			if($match[1] == "api"){
				$bots = glob("bot*");

				$str = '';
				foreach($bots as $bot){
					$i = (int)str_replace('bot', '', $bot);
					
					$botp = 'rob-'. $i;
					$output = exec("cd ../Telegram/bot/bot". $i ."/ && tmux kill-session -t ". $botp);
					$output = exec("cd ../Telegram/bot/bot". $i ."/ && tmux new-session -d -s ". $botp . " php index.php ". $botp . " && tmux detach -s ". $botp);
					$str .= 'ربات شماره '.$i.' راه اندازی شد.' . "\n" . $output ."\n";
				}
				$content = array( 'chat_id' => $chat_id, 'text' => "انجام شد. \n" . $str );
				$telegram->sendMessage( $content );
			}
			
		
			break;
	}
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
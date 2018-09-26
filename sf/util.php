<?php


function getChat($gid){
	$chat = $GLOBALS['db']->query("SELECT players.name, guildchat.message, guildchat.time FROM guildchat LEFT JOIN players ON guildchat.playerID = players.ID 
				WHERE guildchat.guildID = $gid ORDER BY chattime DESC LIMIT 5");
	return $chat->fetchAll(PDO::FETCH_ASSOC);
}

//returns chattime
function chatInsert($message, $guild, $player){
	$time = time();
	$chattime = $GLOBALS['db']->query("SELECT @cht := Max(chattime) AS chattimer FROM guildchat WHERE guildID = $guild;
		INSERT INTO guildchat(guildID, playerID, message, time, chattime) VALUES($guild, $player, '$message', $time, @cht + 1)");
	return $chattime->fetch(PDO::FETCH_ASSOC)['chattimer'] + 1;
}

function formatChat($messages){

	//gold donate
	//#dg#14:29 Pan Marcel#38500


	$chatHistory = ['', '', '', '', ''];

	$i = 0;
	foreach($messages as $msg){


		if(strpos($msg['message'], '#') !== false){
			//system message, formatted beforehand
			$chatHistory[$i] = $msg['message'];

		}else{
			//normal player message
			$message = $msg['message'];
			$formattedTime = gmdate("H:i", $msg['time'] + 3600);
			$name = $msg['name'];

			$chatHistory[$i]= "$formattedTime $name:§ $message"; 
		}
		$i++;
	}

	return join('/', $chatHistory);
}

//checks if chat message is a system message
function containsSystemMessage($chat){

	//fix this shit

	foreach($chat as $msg)
		if(strpos($msg['message'], '#') !== false)
			return true;
	

	return false;
}

function formatMessages($messages){
	//guild invite
	//601979186,maks03,0,5,1388396179

	//guild disbanded
	//1077418003,Pan Marcel,0,1,1387482797

	$msgs = [];
	foreach($messages as $msg){
		if(strlen($msg['name']) == 0)
			$msg['name'] = 'admin';
		$msgs[] = join(',', $msg);
	}

	return join(';', $msgs);
}

?>
<?php

// Fixed login bug by Greg
// Based on Krexxon's (Marcel's) script.
// Made in Hungary


include('sf/util.php');
include('sf/album.php');
include('sf/entity.php');
include('sf/fortress.php');
include('sf/player.php');
include('sf/account.php');
include('sf/item.php');
include('sf/simulate.php');
include('sf/guild.php');


$dateTime = new DateTime(null, new DateTimeZone('Europe/Stockholm'));
$passSalt = '7PsyHi6Icp7E2ArwpF1R1U7xMVcYmzhw';

$req = substr( $_GET['req'], 16);
$req = base64_decode(str_pad(strtr($req, '-_', '+/'), strlen($req) % 4, '=', STR_PAD_RIGHT));

$key = '[_/$VV&*Qg&)r?~g';
$iv = 'jXT#/vz]3]5X7Jl\\';
$keyId = substr( $_GET['req'], 0, 16);

if($keyId == "0-0K36aS2567C735")
	$key = "5O4ddy4KZLs41n6W";
else if($keyId != "0-00000000000000")
	exit("Error:cryptoid not found&cryptoid:0-0K36aS2567C735&cryptokey:5O4ddy4KZLs41n6W");

$req = rtrim ( mcrypt_decrypt (MCRYPT_RIJNDAEL_128, $key, $req, MCRYPT_MODE_CBC, $iv), "\0");


$db = new PDO ( 'mysql:host=localhost;dbname=sfgame;charset=utf8', 'sfgame', 'sfgame' );
$db->setAttribute ( PDO::ATTR_ORACLE_NULLS, PDO::NULL_TO_STRING );

$req = explode("|", $req);

$ssid = $req[0];
$req = explode(":", $req[1]);

$act = $req[0];
$args = explode("/", $req[1]);

if(strlen($ssid) != 32)
	exit();

if($ssid != "00000000000000000000000000000000"){
	if($act == "poll"){
		$qry = $db->prepare("SELECT players.ID, players.poll, players.guild, fortress.ut1, fortress.ut2, fortress.ut3, fortress.uttime1, fortress.uttime2, fortress.uttime3 FROM players LEFT JOIN fortress ON players.ID = fortress.owner WHERE ssid = :ssid");
	}else{
		$qry = $db->prepare("SELECT ID, poll, guild FROM players WHERE ssid = :ssid");
	}
	$qry->bindParam(':ssid', $ssid);
	$qry->execute();

	//$baseData = $qry->fetch ( PDO::FETCH_ASSOC );

	if($qry->rowCount() == 0)
		exit("Error:sessionid invalid");

	$playerData = $qry->fetch(PDO::FETCH_ASSOC);
	$playerID = $playerData['ID'];
	$playerPoll = $playerData['poll'];
	$playerGuild = $playerData['guild'];
}


switch($act){
	case 'accountcreate':
		//"00000000000000000000000000000000|accountcreate:sp/pass/ddask@ldpwqe.com/2/8/3/3,302,4,6,5,6,1,2,3/0/sfgame_new_flash/pl
		//race/gender/class/face/


		$name = $args[0];
		$pass = $args[1];
		$mail = $args[2];

		$gender = $args[3];
		$race = $args[4];
		$class = $args[5];

		$face = $args[6];//str_replace(",", "/", $args[6]);

		
		$qry = $db->prepare("SELECT name FROM players WHERE name = :name");
		$qry->bindParam(':name', $name);
		$qry->execute();

		if($qry->fetch( PDO::FETCH_ASSOC ))
			exit("Error:character exists");


		//password salt
		//ahHoj2woo1eeChiech6ohphoB7Aithoh
		$pass = sha1($pass . $passSalt . $pass); // This is the way in Greg's swf

		$qry = $db->prepare("INSERT INTO players(name, password, face, race, gender, class)
			VALUES(:name, :pass, :face, :race, :gender, :class)");
		$qry->bindParam(':name', $name);
		$qry->bindParam(':pass', $pass);
		$qry->bindParam(':face', $face);
		$qry->bindParam(':race', $race);
		$qry->bindParam(':gender', $gender);

		$qry->bindParam(':class', $class);
		$qry->execute();

		$qry = $db->prepare("SELECT ID FROM players WHERE name = :name");
		$qry->bindParam(':name', $name);
		$qry->execute();

		$pid = $qry->fetch(PDO::FETCH_ASSOC)['ID'];

		//insert a nice welcoming message :)
		$db->exec("INSERT INTO messages(sender, reciver, time, topic, message) VALUES(0, $pid, ".time().", 'Hello :)', 'Beers reset portal timer!\$bTo report bugs or to contact me message Marcel\$b\$bHave fun!')");

		//fortress
		$db->exec("INSERT INTO fortress(owner) VALUES($pid)");

		//copycats
		$db->exec("INSERT INTO copycats(owner, class, str, dex, intel, wit) VALUES($pid, 1, 1046, 358, 531, 1065);
					INSERT INTO copycats(owner, class, str, dex, intel, wit) VALUES($pid, 2, 358, 531, 1046, 799);
					INSERT INTO copycats(owner, class, str, dex, intel, wit) VALUES($pid, 3, 358, 1046, 531, 799);");


		//starting weapon
		$weapon = Item::genItem(1, 1, $class);
		$weapon['value_silver'] = 1;
		$weapon['item_id'] = 1 + ($class - 1) * 1000;
		$db->exec('INSERT INTO items(owner, slot, type, item_id, dmg_min, dmg_max, a1, a2, a3, a4, a5, a6, value_silver, value_mush) VALUES('.$pid.', 18, '.join(', ', $weapon).')');

		//album
		$db->exec('INSERT INTO items(owner, slot, type, item_id, value_silver) VALUES('.$pid.', 0, 13, 1, 1)');

		//shops
		for($i = 0; $i < 12; $i++){
			$type = $i < 6 ? rand(1, 7) : rand(8, 10);
			$item = Item::genItem($type, 1, $class);
			$slot = 20 + $i;
			$db->exec('INSERT INTO items(owner, slot, type, item_id, dmg_min, dmg_max, a1, a2, a3, a4, a5, a6, value_silver, value_mush) VALUES('.$pid.', '.$slot.', '.join(', ', $item).')');
		}

		$questArgs = [];
		//quests
		for($i = 1; $i <= 3; $i++){
			$quest = Account::generateQuest(1);
			$questArgs[] = "quest_exp$i = ".$quest['exp'];
			$questArgs[] = "quest_silver$i = ".$quest['silver'];
			$questArgs[] = "quest_dur$i = ".$quest['duration'];
		}

		$db->exec('UPDATE players SET '.join(', ', $questArgs).' WHERE ID = '.$pid);

		//resp
		exit("skipallow:1&timestamp:".time()."&playerid:$pid&tracking.s:signup&success:");

		break; 
	case 'accountcheck':
		//success if name avalible, error in case of login
		//if keyid default, give out another keyset

		$keyId = "0-0K36aS2567C735";
		$key = "5O4ddy4KZLs41n6W";

		$name = $args[0];

		//check if name allowed, if not exit like this
		if(preg_match('/[^A-Za-z0-9 ]/', $name))
			exit('Error:name is not avaible');

		//client reserved name
		if(strtolower($name) == "admin")
			exit('Error:name is not avaible');

		//numeric numbers not allowed, compliactions with arena, clients fault...
		if(is_numeric($name))
			exit('Error:name is not avaible');

		$qry = $db->prepare("SELECT name FROM players WHERE name = :name");
		$qry->bindParam(':name', $name);
		$qry->execute();

		//if character exists -> login
		if($qry->fetch( PDO::FETCH_ASSOC ))
			exit("Error:character exists&cryptoid:$keyId&cryptokey:$key");

		//if name is free
		exit("Success:&cryptoid:$keyId&cryptokey:$key");
		break;
	case 'accountlogin':
		// "00000000000000000000000000000000|accountlogin:name/612651e328e54e811d628b90a35419743087b6c5/[login count]/flash/not_initialized/56|||||||||"

		//if($args[2] <1)
		//	echo "login count:501&Error:login count too low";
		//else
			//exit("Error:wrong pass");


		$qry = $db->prepare("SELECT players.*, fortress.*, guilds.portal AS guild_portal, guilds.instructor, guilds.treasure, guilds.dungeon AS raid FROM players LEFT JOIN fortress ON players.ID = fortress.owner LEFT JOIN guilds ON players.guild = guilds.ID WHERE players.name = :name");
		$qry->bindParam(':name', $args[0]);
		$qry->execute();

		$playerData = $qry->fetch ( PDO::FETCH_ASSOC );


		if($qry->rowCount() == 0)
			exit('Error:player not found');

		$args[1] = substr( $args[1], 0, (strlen($args[1]) - strlen($args[2])) );
		
		// Password check by Greg (working only with Greg's SWF)
		if( sha1( $args[1] . $passSalt . $args[1] ) != $playerData['password'] ) { // Normal password check
			$tt = explode('.', $args[1]);
			if ( is_numeric($args[1]) && count($tt) == 2 && $tt[0] == '0' && $tt[1] > 0 && $playerData['lastLoginCount'] == $args[2] ) { // Auto login/Face login (without real password)
			}else{
				exit("Error:wrong pass");
			}
		}


		//get items
		$items = $db->query("SELECT * FROM items WHERE owner = ".$playerData['ID']." ORDER BY slot ASC");
		$items = $items->fetchAll(PDO::FETCH_ASSOC);


		//gen ssid and LoginCount (Fix by Greg)
		$ssid = md5(microtime());
		$time = time();
		$loco = rand(0, 999);
		$db->exec("UPDATE players SET ssid = '$ssid', lastLoginCount = '$loco', poll = $time WHERE ID = '".$playerData['ID']."'");

		//get copycats
		$copycats = $db->query("SELECT * FROM copycats WHERE owner = ".$playerData['ID']." ORDER BY class ASC");
		$copycats = $copycats->fetchAll();

		//get messages
		$messages = $db->query('SELECT messages.ID, players.name, messages.hasRead, messages.topic, messages.time 
				FROM messages LEFT JOIN players ON messages.sender = players.ID WHERE reciver = '.$playerData['ID'].' ORDER BY time DESC');
		$messages = $messages->fetchAll(PDO::FETCH_ASSOC);

		//create account obj
		$acc = new Account($playerData, $items, $copycats, true);

		$acc->data['new_msg'] = $db->query('SELECT Count(ID) AS c FROM messages WHERE reciver = '.$playerData['ID'].' AND hasRead = false')->fetch(PDO::FETCH_ASSOC)['c'];
		// var_dump($acc->data['new_msg']);

		$ret[] = "login count:192";
		$ret[] = "sessionid:".$ssid;
		// $ret[] = "stoneperhournextlevel:580";
		// $ret[] = "woodperhournextlevel:3850";
		// $ret[] = "fortresswalllevel:35";
		$ret[] = "inboxcapacity:1000";
		$ret[] = "ownplayersave.playerSave:".$acc->getPlayerSave();
		$ret[] = "owndescription.s:";
		$ret[] = "ownplayername.r:".$acc->getName();
		$ret[] = "maxrank:395994";
		$ret[] = "skipallow:1";

		$ret[] = "fortresspricereroll:1";
		$ret[] = "fortressprice.fortressPrice(13):".$acc->getFortressPriceSave();
		$ret[] = "fortressGroupPrice.fortressPrice:".$acc->getHallOfKnightsPriceSave();
		$ret[] = "unitprice.fortressPrice(3):".$acc->getTrainUnitsPrice();
		$ret[] = "upgradeprice.upgradePrice(3):".$acc->getUpgradeUnitsPrice();
		$ret[] = "unitlevel(4):".$acc->getUnitLvls();

		if(($fortressBackpackSize = $acc->getFortressBackpackSize()) > 0)
			$ret[] = "fortresschest.item(".$fortressBackpackSize."):".$acc->getFortressBackpackSave();


		$ret[] = "singleportalenemylevel:200";
		if($acc->hasTower()){
			$ret[] = "owntower.towerSave:".$acc->getTowerSave();
			//0 bo poniewaz, chuj z tym narazie
			$ret[] = "owntowerlevel:0";
		}
		// $ret[] = "fortressGroupPrice.fortressPrice:0/0/720/240";

		//guild
		if($acc->hasGuild()){

			// $guildData = $db->query('SELECT * FROM guilds WHERE ID = '.$playerData['guild'])->fetch(PDO::FETCH_ASSOC);
			// $players = $db->query('SELECT ID, name, lvl, poll, guild_rank, potion_type1, potion_type2, potion_type3, potion_dur1, potion_dur2, potion_dur3 
			// FROM players WHERE guild = '.$playerData['guild'].' ORDER BY guild_rank, lvl DESC')->fetchAll(PDO::FETCH_ASSOC);

			$guild = new Guild($acc->data['guild']);

			$ret[] = 'owngroupsave.groupSave:'.$guild->getGroupSave();
			$ret[] = "owngrouppotion.r:".$guild->getPotionData();
			$ret[] = "owngroupname.r:".$guild->data['name'];
			$ret[] = "owngroupdescription.s:";
			$ret[] = "owngroupmember.r:".$guild->getMemberList();
			$ret[] = "owngrouprank:1";
			if(($oga = $guild->getOwnGroupAttack()) !== false)
				$ret[] = $oga;

			//chat
			// $chat = $db->query("SELECT players.name, guildchat.message, guildchat.time FROM guildchat LEFT JOIN players ON guildchat.playerID = players.ID 
			// WHERE guildchat.guildID = ".$playerData['guild']." ORDER BY guildchat.time DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

			
			$chattime = $db->query("SELECT Max(chattime) as chattime FROM guildchat WHERE guildID = $playerData[guild]")->fetch(PDO::FETCH_ASSOC)['chattime'];
			$chat = getChat($playerData['guild']);

			$ret[] = 'chathistory.s(5):'.formatChat($chat);
			$ret[] = "chattime:$chattime";
		}




		$ret[] = "witch.witchData:9/30097904/-1/7/1452384000/0/1402139157/9/6/51/1387968268/0/61/1389353441/5/31/1390907951/8/101/1392626428/1/71/1394196822/2/41/1396169319/4/81/1398237044/7/11/1400137421/3/91/1402139201/0/0/0/0/0/0/0/0/0/0/0/0/0/0/0/";
		$ret[] = "tavernspecial:0";
		$ret[] = 'wagesperhour:'.Account::getWagesPerHour($playerData['lvl']);
		$ret[] = "dragongoldbonus:0";
		$ret[] = "toilettfull:".$acc->toiletFullToday();

		// $msga = [];
		// foreach($messages as $msg){
		// 	if(strlen($msg['name']) == 0)
		// 			$msg['name'] = 'admin';
		// 	$msga[] = join(',', $msg);
		// }
		$ret[] = 'messagelist.r:'.formatMessages($messages);
		// $ret[] = "combatloglist.s:2034579109,Traxoy1906,0,9,1452337465,0;1409607638,Grzybniak,0,9,1452287972,0;1361082607,FilipoV,0,9,1452177994,0;358841208,MageÅ‚,0,9,1452117768,0;301766846,xNemeczek,0,9,1452116196,0;239302932,FilipoV,0,9,1452082534,0;492327969,matesiak,0,9,1452031776,0;51494050,Grzywa,0,9,1452024299,0;374931086,Im Braum,0,9,1451585628,0;542652924,Bubka16,0,9,1451257560,0;250822796,gotik3,1,0,1451243594,0;513526444,Falubaz01,1,0,1451178057,0;1337078527,HappyPL,1,0,1451066730,0;229879563,DoNe753,0,9,1450930633,0;835722920,bibka1,0,9,1450823171,0;497563903,jakubiecxd,0,9,1450807528,0;129795927,adamekK,0,9,1450689259,0;2132309532,GILOTYNA,0,9,1450683946,0;719221438,jakubiecxd,0,9,1450647652,0;1400034954,pati2,0,9,1450558250,0;922757884,Grzegorz8525,0,9,1450541416,0;2085125364,fenix07,0,9,1450524856,0;405420628,fenix07,0,9,1450519115,0;270681433,spuÅ‚ka M A,0,8,1450264629,0;1837182713,Zientar123,0,8,1450242224,0;420414246,Mathew z Przeworska,0,8,1450166475,0;1136919070,daromen12321,0,8,1450036702,0;750152477,Vezoxor,0,8,1450021289,0;1539440341,MegiCoMaPiegi,0,8,1449693611,0;1365178155,supereniu148,0,8,1449564241,0;1200435972,Pustynny Buntownik,0,8,1449522579,0;2097753313,daromen12321,0,8,1449514115,0;906864435,oliverr,0,8,1449362545,0;1234493481,Kaczorr,0,8,1449354523,0;1470649540,michaÅ‚ zabÃ³jca,0,8,1449327091,0;1213655724,Janush794,0,8,1448928724,0;2133365428,Adisho,0,8,1448741189,0;568588024,dawidkostrzew,0,8,1448575072,0;926302142,domez,0,8,1448562007,0;640352012,Gospodarz,0,8,1448126181,0;1790793820,nertus,0,8,1448126172,0;2041125606,kinger231,0,8,1448048643,0;1957099852,michuuu,1,8,1447969467,0;1236456535,BilliTalent,0,8,1447969460,0;1011750065,kruki123,1,8,1447969455,0;736448167,Waszi,1,8,1447957524,0;2110632971,mak244,1,8,1447957494,0;2084698169,Necros,1,8,1447957488,0;1637334102,Wallow,1,8,1447954174,0;405159156,dahar,1,8,1447954170,0;";
		

		$ret[] = 'combatloglist.s:';


		$ret[] = "friendlist.r:";
		if($acc->hasAlbum())
			$ret[] = "scrapbook.r:".$acc->album->data;
		$ret[] = "skipallow:1";
		$ret[] = "timestamp:".time();
		$ret[] = "serverversion:853";
		$ret[] = "success:";


		break;
	case 'playerarenaenemy':
		//act used to get new enemies for arena

		$acc = new Account(null, null, false, false);



		//set new enemies for arena if time is up or have no enemies
		if($acc->data['arena_nme1'] == 0){
			//alg: get rank, get 20 enemies around, select 3 at random
			$rank = $db->query("SELECT Count(*) as rank FROM players WHERE honor > ".$acc->data['honor']);
			$rank = $rank->fetch(PDO::FETCH_ASSOC)['rank'];

			if($rank < 10)
				$rank = 0;
			else
				$rank -= 10;

			$playerpool = $db->query("SELECT ID FROM players FORCE INDEX(honor) WHERE honor >= 0 ORDER BY honor DESC, ID DESC LIMIT $rank, 20")->fetchAll(PDO::FETCH_ASSOC);

			if(count($playerpool) < 4)
				exit('Error:no player data');

			//shuffle once
			shuffle($playerpool);

			//shuffle while play in first 3
			while($playerpool[0] == $playerID || $playerpool[1] == $playerID || $playerpool[2] == $playerID)
				shuffle($playerpool);


			$acc->data['arena_nme1'] = $playerpool[0]['ID'];
			$acc->data['arena_nme2'] = $playerpool[1]['ID'];
			$acc->data['arena_nme3'] = $playerpool[2]['ID'];

			$db->exec("UPDATE players SET arena_nme1 = ".$playerpool[0]['ID'].", arena_nme2 = ".$playerpool[1]['ID'].", arena_nme3 = ".$playerpool[2]['ID']." WHERE ID = $playerID");
		}


		$ret[] = 'Success:';
		$ret[] = 'ownplayersave.playerSave:'.$acc->getPlayerSave();

		break;
	case 'playerdungeonopen':
		//normal dungeon
		//|playerdungeonopen:||||||
		//shadow world dungeon
		//|playerdungeonopen:1||||

		//TODO: check if has key, open

		//echo "dungeonfaces(14):12/1/1/1/1/1/1/1/1/1/1/1/&shadowfaces(14):1/131/28/124/9/128/66/38/136/101/173/183/243/600/&Success:";
		$dungs = $db->query("SELECT d1, d2, d3, d4, d5, d6, d7, d8, d9, d10, d11, d12, d13, d14, dd1, dd2, dd3, dd4, dd5, dd6, dd7, dd8, dd9, dd10, dd11, dd12, dd13, dd14 FROM players 
			WHERE ID = $playerID");
		$dungs = $dungs->fetch(PDO::FETCH_ASSOC);


		$faces = [
		[129, 112, 6, 84, 31, 74, 116, 114, 4, 166],
		[131, 38, 112, 86, 51, 102, 23, 67, 92, 169],
		[28, 3, 57, 94, 140, 78, 93, 162, 142, 170],
		[124, 45, 94, 107, 46, 39, 141, 47, 137, 172],
		[9, 150, 36, 153, 17, 151, 161, 118, 160, 171],
		[128, 86, 77, 81, 89, 16, 88, 30, 87, 167],
		[66, 97, 82, 52, 158, 135, 102, 52, 149, 168],
		[38, 143, 147, 144, 99, 154, 146, 98, 156, 164],
		[136, 125, 99, 37, 129, 138, 90, 42, 74, -1],
		[101, 115, 159, 21, 61, 163, 161, 159, 158, 165],
		[173, 174, 175, 176, 177, 178, 179, 180, 181, 182],
		[183, 184, 185, 186, 187, 188, 189, 190, 191, 192],
		[243, 244, 245, 246, 247, 248, 249, 250, 251, 252],
		[600, 601, 602, 603, 604, 605, 606, 607, 608, 609, 610, 611, 612, 613, 614, 615, 616, 617, 618, 619]];

		$dungeonfaces = [];
		for($i = 1; $i <= 14; $i++){
			$d = $dungs['d'.$i];
			if($d > 1)
				$d -= 2;
			if( ($d == 10 && $i < 14) || $d == 20 )
				$d--;
			$dungeonfaces[] = $faces[$i-1][$d];
		}

		$shadowfaces = [];
		for($i = 1; $i <= 14; $i++){
			$d = $dungs['dd'.$i];
			if($d > 1)
				$d -= 2;
			if($d == 10 && $i < 14)
				$d--;
			$shadowfaces[] = $faces[$i-1][$d];
		}


		$ret[] = "dungeonfaces(14):".join("/", $dungeonfaces);
		$ret[] = "shadowfaces(14):".join("/", $shadowfaces);
		$ret[] = "Success:";

		// echo "dungeonfaces(14):166/169/170/172/171/167/168/156/90/101/173/183/243/600/&shadowfaces(14):129/131/28/124/9/128/66/38/136/101/173/183/243/600/&Success:";
		break;
	case 'playershadowbattle':

		//args for query
		$qryArgs = [];

		$acc = new Account(null, null, true, true);


		// if(dung complete)
		$dung = $acc->data['dd'.$args[0]] - 1;
		if($dung < 0)
			exit("Error:");
		if($args[0] != 14 && $dung > 10)
			exit();
		if($acc->data['dungeon_time'] > time()){ //if time not up
			if($acc->data['mush'] <= 0)
				exit("Error:need more coins");
			$acc->data['mush']--;
			$qryArgs[] = "mush = mush - 1";
		}else{
			$acc->data['dungeon_time'] = time() + 600;
			$qryArgs[] = "dungeon_time = ".$acc->data['dungeon_time'];
		}

		if(($freeSlot = $acc->getFreeBackpackSlot()) === false)
			exit("Error:need a free slot");


		//if dung 9 boss, monster = acc, ID -1?
		if($args[0] == 9 && $acc->data['d9'] == 11){
			$monster = clone($acc);
			$monster->exp = 10000000;
			$monster->gold = 1000000;
			$monster->ID = 0;
			$monster->hp = round($monster->hp * 2.5);
			$monster->maxHp = $monster->hp;
			//fightheader takes id from here
			$monster->data['ID'] = 0;
			$mirrorFight = true;
		}else{
			$monster = Monster::getDungMonster($args[0], $dung);
			$monster->buff();
			$mirrorFight = false;
		}

		$playerGroup = $acc->copycats;
		$playerGroup[] = $acc;

		$simulation = new GroupSimulation($playerGroup, [$monster]);
		$simulation->simulate();

		$bg = $args[0] + 50;
		for($i = 0; $i < count($simulation->simulations); $i++){
			$fight = $i+1;
			$ret[] = "fightheader".$fight.".fighters:4/0/0/".$bg."/1/".$simulation->fightHeaders[$i];
			$ret[] = "fight".$fight.".r:".$simulation->simulations[$i]->fightLog;
			$ret[] = "winnerid".$fight.".s:".$simulation->simulations[$i]->winnerID;
		}


		$rewardLog = [];
		for($i = 0; $i < 21; $i++)
			$rewardLog[] = 0;

		if($simulation->win){
			//win true
			$rewardLog[0] = 1;
			//silver
			// $rewardLog[2] = 1;
			//exp
			$rewardLog[3] = $monster->exp;

			$acc->addExp($monster->exp);

			$qryArgs[] = "exp = ".$acc->data['exp'];
			$qryArgs[] = "lvl = ".$acc->data['lvl'];

			$acc->data['dd'.$args[0]]++;

			$dung += 2;
			$qryArgs[] = "dd".$args[0]." = ".$acc->data['dd'.$args[0]];


			//item reward, always epic, random class, no silver value, NEVER SHIELD
			while(($itemid = mt_rand(1, 10)) == 2);
			$item = Item::genItem($itemid, $acc->lvl, mt_rand(1, 3), 100);
			$item['value_silver'] = 0;
			$itemReward = $acc->insertItem($item, $freeSlot);

			$i = 9;
			foreach($item as $s){
				$rewardLog[$i] = $s;
				$i++;
			}
			

			//album
			if($acc->hasAlbum() && !$mirrorFight){
				$a1 = $acc->album->addMonster($monster->ID);
				$a2 = $acc->album->addItem($itemReward);
				if($a1 || $a2){
					$acc->album->encode();

					$ret[] = "scrapbook.r:".$acc->album->data;
					$acc->data['album'] = $acc->album->count;

					// $db->query("UPDATE players SET album = ".$acc->album->count.", album_data = '".$acc->album->data."' WHERE ID = ".$acc->data['ID']);
					$qryArgs[] = "album = ".$acc->album->count;
					$qryArgs[] = "album_data = '".$acc->album->data."'";
				}
			}
		}

		$db->exec("UPDATE players SET ".join(", ", $qryArgs)." WHERE ID = ".$acc->data['ID']);

		$ret[] = "fightresult.battlereward:".join("/", $rewardLog);
		$ret[] = "Success:";
		$ret[] = "ownplayersave.playerSave:".$acc->getPlayerSave();
		$ret[] = "timestamp:".time();



		break;
	case 'playertowerbattle':
		//arg 0 = tower lvl, fuck your input m8

		//args for query
		$qryArgs = [];

		$acc = new Account(null, null, true, true);

		//error checking, TODO: if tower open
		if(($freeSlot = $acc->getFreeBackpackSlot()) === false)
			exit("Error:need a free slot");
		if($acc->data['dungeon_time'] > time()){ //if time not up
			if($acc->data['mush'] <= 0)
				exit("Error:need more coins");
			$acc->data['mush']--;
			$qryArgs[] = "mush = mush - 1";
		}else{
			$acc->data['dungeon_time'] = time() + 600;
			$qryArgs[] = "dungeon_time = ".$acc->data['dungeon_time'];
		}

		$monster = Monster::getTowerMonster($acc->data['tower']);

		$playerGroup = $acc->copycats;
		$playerGroup[] = $acc;

		$simulation = new GroupSimulation($playerGroup, [$monster]);
		$simulation->simulate();



		//WHEN GETTING FIGHT HEADERS REMEMBER TO ADD SCENE, BACKGROUND ETC AT THE BEGINING!

		for($i = 0; $i < count($simulation->simulations); $i++){
			$fight = $i+1;
			$ret[] = "fightheader".$fight.".fighters:5/0/0/0/1/".$simulation->fightHeaders[$i];
			$ret[] = "fight".$fight.".r:".$simulation->simulations[$i]->fightLog;
			$ret[] = "winnerid".$fight.".s:".$simulation->simulations[$i]->winnerID;
		}

		$rewardLog = [];
		for($i = 0; $i < 21; $i++)
			$rewardLog[] = 0;

		if($simulation->win){
			//win true
			$rewardLog[0] = 1;
			//silver
			$rewardLog[2] = 1;
			//no exp for tower
			// $rewardLog[3] = $monster->exp;

			$acc->data['tower']++;
			$qryArgs[] = "tower = tower + 1";


			//item reward, always epic for random claass, no silver value, NOT SHIELD
			while(($itemid = mt_rand(1, 10)) == 2);
			$item = Item::genItem($itemid, $acc->lvl, mt_rand(1, 3), 100);
			$item['value_silver'] = 0;
			$itemReward = $acc->insertItem($item, $freeSlot);

			$i = 9;
			foreach($item as $s){
				$rewardLog[$i] = $s;
				$i++;
			}
			

			//album
			if($acc->hasAlbum()){
				$a1 = $acc->album->addMonster($monster->ID);
				$a2 = $acc->album->addItem($itemReward);
				if($a1 || $a2){
					$acc->album->encode();

					$ret[] = "scrapbook.r:".$acc->album->data;
					$acc->data['album'] = $acc->album->count;

					// $db->query("UPDATE players SET album = ".$acc->album->count.", album_data = '".$acc->album->data."' WHERE ID = ".$acc->data['ID']);
					$qryArgs[] = "album = ".$acc->album->count;
					$qryArgs[] = "album_data = '".$acc->album->data."'";
				}
			}
		}

		$db->exec("UPDATE players SET ".join(", ", $qryArgs)." WHERE ID = ".$acc->data['ID']);

		$ret[] = "fightresult.battlereward:".join("/", $rewardLog)."/";
		$ret[] = "Success:";
		$ret[] = "ownplayersave.playerSave:".$acc->getPlayerSave();
		$ret[] = "timestamp:".time();
	
		break;
	case 'playerdungeonbattle':
		//TODO: check user input

		//args for query
		$qryArgs = [];

		$acc = new Account(null, null, false, true);

		//error checking
		if(($freeSlot = $acc->getFreeBackpackSlot()) === false)
			exit("Error:need a free slot");
		$dung = $acc->data['d'.$args[0]] - 1;
		if($dung < 0)//if closed
			exit("Error:");
		if($args[0] != 14 && $dung > 10) // if complete
			exit();
		if($acc->data['dungeon_time'] > time()){ //if time not up
			if($acc->data['mush'] <= 0)
				exit("Error:need more coins");
			$acc->data['mush']--;
			$qryArgs[] = "mush = mush - 1";
		}else{
			$acc->data['dungeon_time'] = time() + 600;
			$qryArgs[] = "dungeon_time = ".$acc->data['dungeon_time'];
		}

		//if dung 9 boss, monster = acc, ID -1?
		if($args[0] == 9 && $acc->data['d9'] == 11){
			$monster = clone($acc);
			$monster->exp = 10000000;
			$monster->gold = 1000000;
			$monster->ID = 0;
			//fightheader takes id from here
			$monster->data['ID'] = 0;
			$mirrorFight = true;
		}else{
			$monster = Monster::getDungMonster($args[0], $dung);
			$mirrorFight = false;
		}

		$bg = $args[0] + 50;
		$ret[] = "fightheader.fighters:4/0/0/".$bg."/2/".$acc->getFightHeader().$monster->getFightHeader();

		$simulation = new Simulation($acc, $monster);
		$simulation->simulate();


		$ret[] = "fight.r:".$simulation->fightLog;
		$ret[] = "winnerid:".$simulation->winnerID;

		$rewardLog = [];
		for($i = 0; $i < 21; $i++)
			$rewardLog[] = 0;
		
		//rewarding
		if($simulation->winnerID == $acc->data['ID']){
			//win true
			$rewardLog[0] = 1;
			//silver
			$rewardLog[2] = 0;
			//exp
			$rewardLog[3] = $monster->exp;

			$acc->addExp($monster->exp);

			$qryArgs[] = "exp = ".$acc->data['exp'];
			$qryArgs[] = "lvl = ".$acc->data['lvl'];

			//displaying of dung
			$acc->data['d'.$args[0]] ++;

			$dung += 2;
			$qryArgs[] = "d".$args[0]." = ".$dung;

			//item reward
			$itemChance = $dung == 12 ? 100 : 50;
			$epicChance = $dung == 12 ? 100 : 50;
			if($itemChance > rand(0, 99)){
				$item = Item::genItem(rand(1, 10), $acc->lvl, $acc->class, $epicChance);
				$itemReward = $acc->insertItem($item, $freeSlot);

				$i = 9;
				foreach($item as $s){
					$rewardLog[$i] = $s;
					$i++;
				}
			}

			//album
			if($acc->hasAlbum() && !$mirrorFight){
				$a1 = $acc->album->addMonster($monster->ID);
				$a2 = isset($itemReward) ? $acc->album->addItem($itemReward) : false;
				if($a1 || $a2){
					$acc->album->encode();

					$ret[] = "scrapbook.r:".$acc->album->data;
					$acc->data['album'] = $acc->album->count;

					// $db->query("UPDATE players SET album = ".$acc->album->count.", album_data = '".$acc->album->data."' WHERE ID = ".$acc->data['ID']);
					$qryArgs[] = "album = ".$acc->album->count;
					$qryArgs[] = "album_data = '".$acc->album->data."'";
				}
			}
		}
		
		$db->exec("UPDATE players SET ".join(", ", $qryArgs)." WHERE ID = ".$acc->data['ID']);

		$ret[] = "fightresult.battlereward:".join("/", $rewardLog)."/";
		$ret[] = "Success:";
		$ret[] = "ownplayersave.playerSave:".$acc->getPlayerSave();
		$ret[] = "timestamp:".time();

		break;
	case 'playerportalbattle':

		//args for query
		$qryArgs = [];

		$acc = new Account(null, null, false, true);

		//error checking | no need for a free slot here
		// if(($freeSlot = $acc->getFreeBackpackSlot()) === false)
		// 	exit("Error:need a free slot");

		//if time not up | now = current day since start of the year
		if( ($now = floor((time() - strtotime("2010-01-01")) / 86400) % 365) == $acc->data['portal_time'])
			exit();

		//set new date and update db
		$acc->data['portal_time'] = ($now) % 365;
		$qryArgs[] = 'portal_time = '.$acc->data['portal_time'];


		//set monster current hp to the hp from database
		$monster = Monster::getPortalMonster($acc->data['portal'] + 1);
		$monster->hp = $acc->data['portal_hp'];

		$ret[] = "fightheader.fighters:6/0/0/1/2/".$acc->getFightHeader().$monster->getFightHeader();


		$simulation = new Simulation($acc, $monster);
		$simulation->simulate();

		$ret[] = "fight.r:".$simulation->fightLog;
		$ret[] = "winnerid:".$simulation->winnerID;

		$rewardLog = [];
		for($i = 0; $i < 21; $i++)
			$rewardLog[] = 0;
		
		//rewarding
		if($simulation->winnerID == $acc->data['ID']){
			//win true
			$rewardLog[0] = 1;

			$acc->data['portal']++;
			$qryArgs[] = 'portal = '.$acc->data['portal'];

			//update mob hp in database
			if($acc->data['portal'] < 50){
				$acc->data['portal_hp'] = Monster::getPortalMonster($acc->data['portal'] + 1)->hp;
				$qryArgs[] = 'portal_hp = '.$acc->data['portal_hp'];
			}

			//album
			if($acc->hasAlbum()){
				if($acc->album->addMonster($monster->ID)){
					$acc->album->encode();

					$ret[] = "scrapbook.r:".$acc->album->data;
					$acc->data['album'] = $acc->album->count;

					// $db->query("UPDATE players SET album = ".$acc->album->count.", album_data = '".$acc->album->data."' WHERE ID = ".$acc->data['ID']);
					$qryArgs[] = "album = ".$acc->album->count;
					$qryArgs[] = "album_data = '".$acc->album->data."'";
				}
			}
		}else{
			//if lost, update database with remaining hp of mob

			$qryArgs[] = 'portal_hp = '.$monster->hp;
			$acc->data['portal_hp'] = $monster->hp;

		}
		
		$db->exec("UPDATE players SET ".join(", ", $qryArgs)." WHERE ID = ".$acc->data['ID']);

		$ret[] = "fightresult.battlereward:".join("/", $rewardLog)."/";
		$ret[] = "Success:";
		$ret[] = "ownplayersave.playerSave:".$acc->getPlayerSave();
		$ret[] = "timestamp:".time();
		break;
	case 'groupportalbattle':

		$qryArgs = [];
		$time = time();

		$acc = new Account(null, null, false, true);

		//if time not up | now = current day since start of the year
		// if( ($now = floor((time() - strtotime("2010-01-01")) / 86400) % 365) == $acc->data['portal_time'])
		// 	exit();


		$guild = new Guild($playerGuild);

		//set new date and update db
		$acc->data['gportal_time'] = $time;
		$guild->guildPortalCD($playerID);

		if($guild->data['portal'] >= 50)
			exit('Error:');

		$monster = Monster::getGuildPortalMonster($guild->data['portal']);
		$monster->hp = $guild->data['portal_hp'];

		$ret[] = "fightheader.fighters:7/0/0/0/1/".$acc->getFightHeader().$monster->getFightHeader();

		$simulation = new Simulation($acc, $monster);
		$simulation->simulate();

		$ret[] = "fight.r:".$simulation->fightLog;
		$ret[] = "winnerid:".$simulation->winnerID;

		$rewardLog = [];
		for($i = 0; $i < 21; $i++)
			$rewardLog[] = 0;
		
		//rewarding
		if($simulation->winnerID == $acc->data['ID']){
			//win true
			$rewardLog[0] = 1;

			//chat log
			$dmgdealt = $guild->data['portal_hp'] - $monster->hp;
			$log = "#pw#".$acc->data['name']."#".$guild->data['portal']."#$dmgdealt";

			$guild->data['portal']++;
			$acc->data['guild_portal']++;
			$qryArgs[] = 'portal = '.$guild->data['portal'];

			//update mob hp in database
			if($guild->data['portal'] < 50){
				$guild->data['portal_hp'] = Monster::getGuildPortalMonster($guild->data['portal'])->hp;
				$qryArgs[] = 'portal_hp = '.$guild->data['portal_hp'];
			}

			//album for all members
			$guild->addAlbumMonster($monster->ID + 1);


		}else{
			//if lost, update database with remaining hp of mob
			$dmgdealt = $guild->data['portal_hp'] - $monster->hp;

			$qryArgs[] = 'portal_hp = '.$monster->hp;
			$guild->data['portal_hp'] = $monster->hp;
			
			$hpLeftPrc = round($guild->data['portal_hp'] / $monster->maxHp * 100);
			$log = "#po#".$acc->data['name']."#".$guild->data['portal']."#$dmgdealt#$hpLeftPrc";
		}


		
		//insert log && update guilds
		// $db->exec("INSERT INTO guildchat(guildID, playerID, message, time) VALUES($playerGuild, $playerID, '$log', $time)");
		$db->exec("UPDATE guilds SET ".join(", ", $qryArgs)." WHERE ID = ".$guild->data['ID']);

		//get guild chat
		// $chat = $db->query("SELECT players.name, guildchat.message, guildchat.time FROM guildchat LEFT JOIN players ON guildchat.playerID = players.ID 
		// 		WHERE guildchat.guildID = $playerGuild AND guildchat.time > $playerPoll ORDER BY guildchat.time DESC LIMIT 5");
		// $chat = $chat->fetchAll();

		$chattime = chatInsert($log, $playerGuild, $playerID);
		$chat = getChat($playerGuild);

		//update player portal time and poll
		$db->exec("UPDATE players SET gportal_time = ".$acc->data['gportal_time'].", poll = $time WHERE ID = $playerID");


		$ret[] = 'chathistory.s(5):'.formatChat($chat);
		$ret[] = "chattime:$chattime";
		$ret[] = "fightresult.battlereward:".join("/", $rewardLog)."/";
		$ret[] = "owngroupsave.groupSave:".$guild->getGroupSave();
		$ret[] = "Success:";
		$ret[] = "ownplayersave.playerSave:".$acc->getPlayerSave();
		$ret[] = "timestamp:$time";


		break;
	case 'playertowerbuylevel':

		$acc = new Account(null, null, true, false);

		$copycat = $acc->copycats[$args[0] - 1];

		if(($cost = Copycat::getLvlCost($copycat->data['lvl'])) > $acc->data['silver'])
			exit('Error:need more gold');

		$acc->data['silver'] -= $cost;
		$db->exec("UPDATE players SET silver = silver - $cost WHERE ID = ".$acc->data['ID']);

		$copycat->lvlUp();

		$ret[] = 'Success:';
		$ret[] = 'ownplayersave.playerSave:'.$acc->getPlayerSave();
		$ret[] = 'owntowerlevel:200';
		$ret[] = 'owntower.towerSave:'.$acc->getTowerSave();
		$ret[] = 'timestamp:'.time();

		break;
	case 'playersetface':
		//args race/gender/face

		//face:
		//mouth, hair, eyebrows, eyes, beard, nose, ears, special crap, x, color



		break;
	case 'groupgethalloffame':

		if(strlen($args[1]) > 2){

			$qry = $db->prepare('SELECT ID, honor FROM guilds WHERE name = :name');
			$qry->bindParam(':name', $args[1]);
			$qry->execute();

			if($qry->rowCount() == 0)
				exit('Error:group not found');

			$p = $qry->fetch(PDO::FETCH_ASSOC);

			$qry = $db->query('SELECT Count(*) AS rank FROM guilds WHERE honor > '.$p['honor'].' OR (honor = '.$p['honor'].' AND ID > '.$p['ID'].')');

			$args[0] = $qry->fetch(PDO::FETCH_ASSOC)['rank'];
			// var_dump($args[0]);
		}

		$args[0] -= $args[2] + 1;
		if($args[0] < 0)
			$args[0] = 0;

		//SELECT guilds.*, count(players.guild) AS membercount FROM guilds LEFT JOIN players ON guilds.ID = players.guild GROUP BY guild ORDER BY membercount DESC LIMIT 15;
		$qry = $db->prepare("SELECT guilds.ID as gID, guilds.name, GROUP_CONCAT(players.name ORDER BY guild_rank) AS leader, Count(*) AS membercount, guilds.honor, '0' FROM guilds FORCE INDEX(honor) LEFT JOIN players ON guilds.ID = players.guild WHERE guilds.honor >= 0 GROUP BY players.guild 
			ORDER BY guilds.honor DESC, guilds.ID DESC LIMIT :f, 30");
		$qry->bindParam(':f', intval($args[0]), PDO::PARAM_INT);
		$qry->execute();

		$guilds = $qry->fetchAll( PDO::FETCH_ASSOC );

		
		$list = [];
		$rank = $args[0] + 1;
		// for($i = 0; $i < count($guilds); $i++) {
		// 	var_dump($guilds[$i]);
		// 	// $list[] = "$rank,$guilds[$i]"
		// }
		foreach($guilds as $g){
			$g['leader'] = str_split(',', $g['leader'])[0];
			$list[] = "$rank,$g[name],$g[leader],$g[membercount],$g[honor],0"; 
			$rank++;
		}

						//rank, name, leader, memberc, honor, fightstatus
		// ranklistgroup.r:1,Asgard United,guzii,50,37728,0;
		$ret[] = 'ranklistgroup.r:'.join(';', $list);
		$ret[] = "Success:";


		break;
	case 'playergethalloffame':

		//TODO: check if search by username or rank number
		if(strlen($args[1]) > 2){

			$qry = $db->prepare('SELECT ID, honor FROM players WHERE name = :name');
			$qry->bindParam(':name', $args[1]);
			$qry->execute();

			if($qry->rowCount() == 0)
				exit('Error:player not found');

			$p = $qry->fetch(PDO::FETCH_ASSOC);

			// $qry = $db->prepare('SELECT Count(*) as rank FROM players WHERE honor >= (SELECT honor FROM players WHERE name = :name) ');
			// $qry->bindParam(':name', $args[1]);
			// $qry->execute();

			$qry = $db->query('SELECT Count(*) AS rank FROM players WHERE honor > '.$p['honor'].' OR (honor = '.$p['honor'].' AND ID > '.$p['ID'].')');

			$args[0] = $qry->fetch(PDO::FETCH_ASSOC)['rank'];
			// var_dump($args[0]);
		}

		$args[0] -= $args[2] + 1;
		if($args[0] < 0)
			$args[0] = 0;


		//TODO: get guild name | gr8 fix for indexing 8/8 m8
		$qry = $db->prepare("SELECT players.name, guilds.name AS gname, players.lvl, players.honor, players.class FROM players FORCE INDEX(honor) LEFT JOIN guilds ON players.guild = guilds.ID 
			WHERE players.honor >= 0 ORDER BY players.honor DESC, players.ID DESC LIMIT :f, 30");
		$qry->bindParam(':f', intval($args[0]), PDO::PARAM_INT);
		//$qry->bindParam(':t', intval(30), PDO::PARAM_INT);
		$qry->execute();

		$players = $qry->fetchAll( PDO::FETCH_ASSOC );
		// var_dump($players[3]);
		
		$list = [];
		for($i = 0; $i < count($players); $i++) {
			$rank = $args[0] + $i + 1;
			// $list[] = $rank.",".$players[$i]['name'].","."".",".$players[$i]['lvl'].",".$players[$i]['honor'].",".$players[$i]['class'];
			$list[] = $rank.','.join(',', $players[$i]);
		}

									//rank, name, gname, lvl, honor, class
		//$ret[] = "Ranklistplayer.r:"."1,lefek,Asgard United,366,606523,2";
		$ret[] = "Ranklistplayer.r:".join(';', $list);
		$ret[] = "Success:";

		break;
	case 'playerlookat':


		if($args[0] == "?"){
			$qry = $db->query("SELECT players.*, guilds.portal AS guild_portal, guilds.name AS gname FROM players LEFT join guilds ON players.guild = guilds.ID WHERE players.ID = (SELECT enemyid FROM fortress WHERE owner = $playerID)");
		}else if(is_numeric($args[0])){
			$qry = $db->query("SELECT players.*, guilds.portal AS guild_portal, guilds.name AS gname FROM players LEFT join guilds ON players.guild = guilds.ID WHERE players.ID = $args[0]");
			
		}else{
			$qry = $db->prepare("SELECT players.*, guilds.portal AS guild_portal, guilds.name AS gname FROM players LEFT JOIN guilds ON players.guild = guilds.ID WHERE players.name = :name");
			$qry->bindParam(':name', $args[0]);
			$qry->execute();
		}


		if($qry->rowCount() < 0)
			exit('Error:player not found');

		$playerData = $qry->fetch(PDO::FETCH_ASSOC);

		$qry = $db->query("SELECT * FROM items WHERE owner = $playerData[ID] AND slot BETWEEN 10 AND 19");
		$items = $qry->fetchAll(PDO::FETCH_ASSOC);
		$player = new Player($playerData, $items);

		$ret[] = "otherplayergroupname.r:".$playerData['gname'];
		$ret[] = "otherplayer.playerlookat:".$player->getLookatSave();
		$ret[] = "otherdescription.s:";
		$ret[] = "b";
		$ret[] = "otherplayername.r:".$player->data['name'];
		$ret[] = "otherplayerunitlevel(4):50/70/62/70";
		$ret[] = "otherplayerfriendstatus:0";
		$ret[] = "otherplayerfortressrank:13";
		$ret[] = "soldieradvice:1";
		$ret[] = "success:";
		break;
	case 'playerpollscrapbook':
		//dunno when this is called or why

		exit('Error:scrapbook poll call');

		$ret[] = "Success:";
		$albumData = $db->query("SELECT album_data FROM players WHERE ID = $playerID")->fetch(PDO::FETCH_ASSOC)['album_data'];

		$ret[] = "scrapbook.r:$albumData";


		break;
	case 'playerscrapbookcorrupt':
		//if scrapbook count in player data and count from data don't match, client calls this
		//arg0 clients count from album data
		exit('Error:scrapbook corrupt call');

		$ret[] = "Success:";
		$albumData = $db->query("SELECT album_data FROM players WHERE ID = $playerID")->fetch(PDO::FETCH_ASSOC)['album_data'];

		$ret[] = "scrapbook.r:$albumData";

		break;
	case 'playeradventurestart':
		//arg 0 = quest

		$acc = new Account(null, null, false, false);

		$acc->questStart($args[0]);


		$ret[] = "Success:";
		$ret[] = "ownplayersave.playerSave:".$acc->getPlayerSave();
		$ret[] = "timestamp:".time();


		break;
	case 'playeradventurestop':

		$acc = new Account(null, null, false, false);

		$acc->questStop();


		$ret[] = "Success:";
		$ret[] = "ownplayersave.playerSave:".$acc->getPlayerSave();
		$ret[] = "timestamp:".time();

		break;
	case 'playeradventurefinished':

		$acc = new Account(null, null, false, true);

		//see if skipped, take mushrooms
		if($acc->data['status_time'] > time() && $acc->data['mush'] <= 0)
			exit('Error:need more coins');

		//TODO: generate this somewhere
		// ($lvl, $class, $str, $agi, $int, $wit, $luck, $dmg_min, $dmg_max, $hp, $armor, $id, $exp, $weapon_id, $shield_id = 0)
		$monsterID = ($acc->data['quest_exp'.$acc->data['status_extra']] % 163) + 1;
		$monster = new Monster(1, 2, 10, 9, 21, 18, 8, 2, 6, 50, 1, -$monsterID, 1, 2001);

		$bg = rand(11,21);
		$ret[] = "fightheader.fighters:1/0/0/".$bg."/0/".$acc->getFightHeader().$monster->getFightHeader();
		
		$simulation = new Simulation($acc, $monster);
		$simulation->simulate();

		$win = $simulation->winnerID == $acc->data['ID'];

		

		$ret[] = "fight.r:".$simulation->fightLog;
		$ret[] = "winnerid:".$simulation->winnerID;
		$ret[] = "fightresult.battlereward:".$acc->questFinish($win, $monsterID);
		$ret[] = "Success:";
		$ret[] = "ownplayersave.playerSave:".$acc->getPlayerSave();
		$ret[] = "timestamp:".time();

		break;
	case 'playerworkstart':

		$hours = $args[0];

		$qry = $db->prepare('SELECT ID, status FROM players WHERE ssid = :ssid');
		$qry->bindParam(':ssid', $ssid);
		$qry->execute();

		$playerData = $qry->fetch(PDO::FETCH_ASSOC);

		if($playerData['status'] != 0)
			exit();

		$statusTime = time()+ 3600 * $args[0];

		$ret[] = 'Success:';
		$ret[] = '#ownplayersave.playerSave:45/1/47/'.$statusTime;

		$db->exec("UPDATE players SET status = 1, status_time = $statusTime, status_extra = $hours WHERE ID = ".$playerData['ID']);
		break;
	case 'playerworkstop':

		$ret[] = 'Success:';
		$ret[] = '#ownplayersave.playerSave:45/0/47/0';

		$qry = $db->prepare('UPDATE players SET status = 0, status_extra = 0, status_time = 0 WHERE ssid = :ssid');
		$qry->bindParam(':ssid', $ssid);
		$qry->execute();

		break;
	case 'playerworkfinished':

		$qry = $db->prepare("SELECT ID, lvl, silver, status, status_extra, status_time FROM players WHERE ssid = :ssid");
		$qry->bindParam(':ssid', $ssid);
		$qry->execute();
		$playerData = $qry->fetch(PDO::FETCH_ASSOC);

		$reward = Account::getWagesPerHour($playerData['lvl']) * $playerData['status_extra'];

		$db->exec("UPDATE players SET status = 0, status_extra = 0, status_time = 0, silver = silver + $reward WHERE ID = ".$playerData['ID']);

		$ret[] = 'Success:';
		$ret[] = "workreward:$reward";

		$reward += $playerData['silver'];

		$ret[] = "#ownplayersave.playerSave:13/$reward/45/0/47/0";

		break;
	case 'playeritemmove':

		if($args[0] == 1  && $args[2] == 1)
			exit("Success:");

		if($args[0] == 4  && $args[2] == 4)
			exit("Success:");

		if($args[0] == 3  && $args[2] == 3)
			exit("Success:");

		if($args[0] == 1 && $args[2] == 12)
			exit('Error:you cannot sell from here');

		if($args[2] == 3 || $args[2] == 4)
			if($args[0] == 1)
				exit('Error:you cannot sell from here');


		$itemBought = false;

		//if source shops, load album
		if($args[0] == 3 || $args[0] == 4)
			$itemBought = true;

		//TODO: maybe check source/target, if this needs to load or not
		//copycats
		// $copycats = $db->query("SELECT * FROM copycats WHERE owner = ".$playerData['ID']." ORDER BY class ASC");
		// $copycats = $copycats->fetchAll(PDO::FETCH_ASSOC);


		// $acc = new Account($playerData, $items, $copycats, $itemBought);
		$acc = new Account(null, null, true, $itemBought);

		$acc->moveItem($args);

		$ret[] = "ownplayersave.playerSave:".$acc->getPlayerSave();
		if(($fortressBackpackSize = $acc->getFortressBackpackSize()) > 0)
			$ret[] = "fortresschest.item(".$fortressBackpackSize."):".$acc->getFortressBackpackSave();
		$ret[] = "timestamp:".time();
		$ret[] = "Success:";



		break;
	case 'playertoilettflush':

		$acc = new Account(null, null);

		if($acc->toiletFull() != false)
			exit('Error:toilett is not full');

		if(($freeSlot = $acc->getFreeBackpackSlot()) === false)
			exit('Error:need a free slot');

		$db->exec("UPDATE players SET wcaura = wcaura + 1, wcexp = 0 WHERE ID = $playerID");
		$acc->data['wcaura']++;
		$acc->data['wcexp'] = 0;

		//last arg - epic chance
		$item = Item::genItem(rand(1, 10), $acc->lvl, $acc->class, 100);

		$acc->insertItem($item, $freeSlot);


		$freeSlot += $freeSlot >= 100 ? -94 : +1;
		$ret[] = 'Success:';
		$ret[] = 'toilettspawnslot:'.$freeSlot;
		$ret[] = 'ownplayersave.playerSave:'.$acc->getPlayerSave();
		if($freeSlot >= 100)
			$ret[] = "fortresschest.item(".$acc->getFortressBackpackSize()."):".$acc->getFortressBackpackSave();

		break;
	case 'playerpotionkill':

		$acc = new Account(null, null, false, false);

		$acc->data['potion_dur'.$args[0]] = 0;

		$db->exec("UPDATE players SET potion_dur$args[0] = 0 WHERE ID = ".$acc->data['ID']);

		$ret[] = "Success:";
		$ret[] = "ownplayersave.playerSave:".$acc->getPlayerSave();
		$ret[] = "timestamp:".time();

		break;
	case 'playerattributincrease':


		// exit('Error:need more coins');

		$args[0]--;

		$stat = ['str', 'dex', 'intel', 'wit', 'luck'][$args[0]];

		$qry = $db->prepare('SELECT ID, silver, '.$stat.' FROM players WHERE ssid = :ssid');
		$qry->bindParam(':ssid', $ssid);
		$qry->execute();
		$playerData = $qry->fetch(PDO::FETCH_ASSOC);

		$price = Account::getStatPrice($playerData[$stat] - 10);

		if($price > $playerData['silver'])
			exit("Error:need more gold");

		$db->exec("UPDATE players SET silver = silver - $price, $stat = $stat + 25 WHERE ID = $playerData[ID]");

		$ret[] = 'Success:';
		$ret[] = '#ownplayersave.playerSave:13/'.($playerData['silver'] - $price).'/'.($args[0] + 30).'/'.($playerData[$stat] + 25).'/'.($args[0] + 40).'/'.($playerData[$stat] - 9);



		break;
	case 'playerbeerbuy':

		$qry = $db->prepare('SELECT ID, thirst, beers, mush, class FROM players WHERE ssid = :ssid');
		$qry->bindParam(':ssid', $ssid);
		$qry->execute();
		$playerData = $qry->fetch(PDO::FETCH_ASSOC);


		if($playerData['thirst'] > 4800)
			exit();

		if($playerData['mush'] <= 0)
			exit('Error:need more coins');

		if($playerData['beers'] >= 11)
			exit();

		$playerData['beers']++;
		$playerData['thirst'] += 1200;
		$playerData['mush']--;



		//temporary for reseting portal timers, to revert, just switch out the comments and edit playersave
		// $db->exec('UPDATE players SET mush = mush - 1, thirst = thirst + 1200, beers = beers + 1 WHERE ID = '.$playerData['ID']);
		$db->exec("UPDATE players SET mush = mush - 1, thirst = thirst + 1200, beers = beers + 1, portal_time = 0, gportal_time = 0 WHERE ID = $playerID");
		$guild = new Guild($playerGuild);
		$ret[] = 'owngroupsave.groupSave:'.$guild->getGroupSave();

		$ret[] = 'Success:';
		$ret[] = '#ownplayersave.playerSave:14/'.$playerData['mush'].'/456/'.$playerData['thirst'].'/457/'.$playerData['beers'].'/29/'.$playerData['class'];

		break;
	case 'playernewwares':

		$acc = new Account(null, null, false, false);

		$acc->rerollShop($args[0]);

		$ret[] = "Success:";
		$ret[] = "ownplayersave.playerSave:".$acc->getPlayerSave();
		$ret[] = "timestamp:".time();

		break;
	case 'playermountbuy':

		$qry = $db->prepare('SELECT ID, silver, mush, mount, mount_time, tower, quest_dur1, quest_dur2, quest_dur3 FROM players WHERE ssid = :ssid');
		$qry->bindParam(':ssid', $ssid);
		$qry->execute();
		$playerData = $qry->fetch(PDO::FETCH_ASSOC);

		//mush cost
		$costMush = [0, 0, 1, 25][$args[0] - 1];
		if($costMush > $playerData['mush'])
			exit('Error:need more coins');

		//gold cost
		$costSilver = [100, 500, 1000, 0][$args[0] - 1];
		if($costSilver > $playerData['silver'])
			exit('Error:need more gold');

		$playerData['mush'] -= $costMush;
		$playerData['silver'] -= $costSilver;
		$resp = [];

		//if same mount, and time not expired, just inscrease the time
		if($playerData['mount'] == $args[0] && $playerData['mount_time'] > time()){
			$playerData['mount_time'] += 1209600;
		}else{
			$playerData['mount'] = $args[0];
			$playerData['mount_time'] = time() + 1209600;

			$mountMultiplier = [0.9, 0.8, 0.7, 0.5][$args[0] - 1];

			for($i = 1; $i <= 3; $i++){
				$resp[] = (240 + $i).'/'.ceil($playerData["quest_dur$i"] * $mountMultiplier);
			}
		}


		$resp[] = '13/'.$playerData['silver'].'/14/'.$playerData['mush'].'/286/'.($playerData['tower'] * 65536 + $args[0]).'/451/'.$playerData['mount_time'];

		$db->exec("UPDATE players SET mush = mush - $costMush, silver = silver - $costSilver, mount = $args[0], mount_time = ".$playerData['mount_time'].' WHERE ID = '.$playerData['ID']);




		$ret[] = 'Success:';
		$ret[] = '#ownplayersave.playerSave:'.join('/', $resp);
		$ret[] = 'timestamp:'.time();

		break;
	case 'playerwitchenchantitem':
		//arg 0 = enchant id counted from left to right

		//table of item types per enchant
		$itemType = [5, 6, 3, 10, 7, 4, 8, 1, 9][$args[0] - 1];

		$acc = new Account(null, null, false, false);

		foreach($acc->equip as $item){
			if($item->type == $itemType){
				$item->enchant();
				break;
			}
		}

		$ret[] = 'Success:';
		$ret[] = 'ownplayersave.playerSave:'.$acc->getPlayerSave();
		$ret[] = 'timestamp'.time();

		break;
	case 'playermessagesend':

		//args: reciver/topic/message

		//reserve single numeric topic for system, simplier solution
		if(strlen($args[1]) == 1 && is_numeric($args[1]))
			exit();

		$qry = $db->prepare('SELECT ID FROM players WHERE name = :name');
		$qry->bindParam(':name', $args[0]);
		$qry->execute();

		if($qry->rowCount() == 0)
			exit('Error:recipient not found');


		$reciver = $qry->fetch(PDO::FETCH_ASSOC)['ID'];
		$time = time();

		$qry = $db->prepare("INSERT INTO messages(sender, reciver, time, topic, message) VALUES($playerID, $reciver, $time, :topic, :message)");
		$qry->bindParam(':topic', $args[1]);
		$qry->bindParam(':message', $args[2]);
		$qry->execute();

		exit('Success:');

		break;
	case 'fortressbuildstart':
		//building id
		$args[0] --;

		$acc = new Account(null, null, false, false);

		$acc->fortressBuild($args[0]);


		$ret[] = "Success:";
		$ret[] = "ownplayersave.playerSave:".$acc->getPlayerSave();
		$ret[] = "timestamp:".time();

		break;
	case 'fortressbuildstop':
		//REMEMBER TO REDUCE RETURNED RESOURCES
		//arg building id, fuck yo input m89

		$acc = new Account(null, null, false, false);

		$acc->fortressBuildStop();

		$ret[] = "Success:";
		$ret[] = "ownplayersave.playerSave:".$acc->getPlayerSave();
		$ret[] = "timestamp:".time();

		break;
	case 'fortressbuildfinished':

		$acc = new Account(null, null, false, false);

		$acc->fortressBuildFinish();

		$ret[] = "Success:";
		$ret[] = "ownplayersave.playerSave:".$acc->getPlayerSave();
		$ret[] = "fortressprice.fortressPrice(13):".$acc->getFortressPriceSave();
		$ret[] = "timestamp:".time();

		//i guess if upgraded bank, mines or other shit that need update, include them
		$args[0]--;
		if($args[0] == 9){ 
			//fortress backpack
			$ret[] = "fortresschest.item(".$acc->getFortressBackpackSize()."):".$acc->getFortressBackpackSave();
		}

		break;
	case 'fortressgemstonestart':

		$acc = new Account(null, null, false, false);

		$acc->fortressDigStart();

		$ret[] = "Success:";
		$ret[] = "ownplayersave.playerSave:".$acc->getPlayerSave();
		$ret[] = "fortressprice.fortressPrice(13):".$acc->getFortressPriceSave();
		$ret[] = "timestamp:".time();
		
		break;
	case 'fortressgemstonestop':

		$acc = new Account(null, null, false, false);

		$acc->fortressDigStop();

		$ret[] = "Success:";
		$ret[] = "ownplayersave.playerSave:".$acc->getPlayerSave();
		$ret[] = "timestamp:".time();

		break;
	case 'fortressgemstonefinished':

		$acc = new Account(null, null, false, false);

		//client check if there is a free slot, doesn't send the request if there is no space, but it's better to keep this here
		if(($freeSlot = $acc->getFreeBackpackSlot()) === false)
			exit("Error:need a free slot");

		//checks if time's up, if enough mushrooms, resets db
		$acc->fortressDigFinish();

		//class is not needed, but have it anyway in case i wanna have higher chance for class specific gems or whatever
		$gem = Item::genItem(15, $acc->lvl, $acc->class, 0, $acc->data['b4']);

		$acc->insertItem($gem, $freeSlot);


		$freeSlot += $freeSlot >= 100 ? -94 : +1;
		$ret[] = "gemstonebackpackslot:".$freeSlot;
		$ret[] = "Success:";
		$ret[] = "ownplayersave.playerSave:".$acc->getPlayerSave();
		$ret[] = "timestamp:".time();

		break;
	case 'fortressgather':

		$acc = new Account(null, null, false, false);

		$acc->fortressGather($args[0]);

		$ret[] = "Success:";
		$ret[] = "ownplayersave.playerSave:".$acc->getPlayerSave();
		$ret[] = "fortressprice.fortressPrice(13):".$acc->getFortressPriceSave();
		$ret[] = "timestamp:".time();

		break;
	case 'fortressenemy':


		exit('Error:player not found');

		$ret[] = 'Success:';


		break;
	case 'fortressupgrade':

		$acc = new Account(null, null, false, false);

		$acc->fortressUnitUpgrade($args[0]);

		$ret[] = 'Success:';
		$ret[] = 'ownplayersave.playerSave:'.$acc->getPlayerSave();
		$ret[] = 'unitprice.fortressPrice(3):'.$acc->getTrainUnitsPrice();
		$ret[] = 'upgradeprice.upgradePrice(3):'.$acc->getUpgradeUnitsPrice();
		$ret[] = 'unitlevel(4):'.$acc->getUnitLvls();

		break;
	case 'fortressbuildunitstart':

		$acc = new Account(null, null, false, false);

		$acc->fortressUnitTrain($args[0], $args[1]);

		$ret[] = 'Success:';
		$ret[] = 'ownplayersave.playerSave:'.$acc->getPlayerSave();


		break;
	case 'fortressgroupbonusupgrade':

		$acc = new Account(null, null, false, false);


		$ret[] = 'Success:';
		$ret[] = 'ownplayersave.playerSave:'.$acc->getPlayerSave();

		break;
	case 'groupfound':
		//create guild
		//arg 0 name

		if(preg_match('/[^A-Za-z0-9 ]/', $args[0]))
			exit('Error:groupname is not available');
		if(strlen($args[0]) > 17)
			exit('Error:groupname is not available');

		$qry = $db->prepare('SELECT ID FROM guilds WHERE name = :name');
		$qry->bindParam(':name', $args[0]);
		$qry->execute();

		if($qry->rowCount() > 0)
			exit('Error:groupname is not available');

		
		$db->exec("INSERT INTO guilds(name) VALUES('$args[0]')");
		$guildID = $db->lastInsertId();

		$playerData = $db->query("SELECT name, lvl, silver FROM players WHERE ID = $playerID")->fetch(PDO::FETCH_ASSOC);

		if($playerData['silver'] < 1000)
			exit('Error:need more gold');
		$playerData['silver'] -= 1000;

		$db->exec("UPDATE players SET guild = $guildID, guild_rank = 1, silver = silver - 1000, event_trigger_count = 0, guild_fight = 0 WHERE ID = $playerID");

		$time = time();
		$message = '#in#'.$playerData['name'];

		$db->exec("INSERT INTO guildchat(guildID, playerID, message, time, chattime) VALUES($guildID, $playerID, '$message', $time, 1)");


		$ret[] = 'Success:';
		//443 = guild join date
		$ret[] = "#ownplayersave:2/$time/13/$playerData[silver]/435/$guildID/443/0";
		$ret[] = "timestamp:$time";

		$ret[] = 'owngroupsave.groupSave:'.Guild::getCreateGroupSave($guildID, $playerID, $playerData['lvl']);
		$ret[] = 'owngrouppotion.r:0,0,0,0,0,0,';	
		$ret[] = 'owngroupknights.r:0,';
		$ret[] = 'owngroupname.r:'.$args[0];
		$ret[] = 'owngroupdescription.s:';
		$ret[] = 'owngroupmember.r:'.$playerData['name'];
		$ret[] = 'owngrouprank:1';// rank = SELECT COUNT(ID) FROM guilds WHERE honor > 99
		$ret[] = "chathistory.s(5):$message////";
		$ret[] = "chattime:1";

		break;
	case 'playerarenafight':

		$qryArgs = [];

		//get opponent
		$qry = $db->prepare("SELECT players.*, guilds.portal AS guild_portal FROM players LEFT JOIN guilds ON players.guild = guilds.ID WHERE players.name = :name");
		$qry->bindParam(':name', $args[0]);
		$qry->execute();

		//player not found
		if($qry->rowCount() == 0)
			exit("Error:player not found");

		$opponentData = $qry->fetch(PDO::FETCH_ASSOC);

		$items = $db->query("SELECT * FROM items WHERE owner = ".$opponentData['ID']." AND slot BETWEEN 10 AND 19");
		$items = $items->fetchAll(PDO::FETCH_ASSOC);

		$opponent = new Player($opponentData, $items);

		//init account
		$acc = new Account(null, null, false, true);

		if($acc->data['arena_time'] > time()){
			if($acc->data['mush'] < 0)
				exit('Error:need more coins');
			else{
				$acc->data['mush']--;
				$qryArgs[] = 'mush = mush - 1';
			}
		}else{
			$acc->data['arena_time'] = time() + 60;
			$qryArgs[] = 'arena_time = '.$acc->data['arena_time'];
		}

		$ret[] = "fightheader.fighters:0/0/0/0/1/".$acc->getFightHeader().$opponent->getFightHeader();

		$simulation = new Simulation($acc, $opponent);
		$simulation->simulate();

		//max honor diff = 2k
		//formula: 100 + (opponent.honor - player.honor) / (max honor diff / 100)
		if($opponent->data['honor'] > $acc->data['honor'])
			$honor = min(200, 100 + round(($opponent->data['honor'] - $acc->data['honor']) / 20));
		else
			$honor = max(0, 100 + round(($opponent->data['honor'] - $acc->data['honor']) / 20));
		

		$rewardLog = [];
		for($i = 0; $i < 21; $i++)
			$rewardLog[] = 0;

		//album items before player save
		if($simulation->winnerID == $acc->data['ID']){

			$rewardLog[0] = 1;

			$rewardLog[5] = $honor;
			$qryArgs[] = "honor = honor + $honor";

			$db->exec("UPDATE players SET honor = GREATEST(0, honor - $honor) WHERE ID = ".$opponent->data['ID']);

			if($acc->hasAlbum() && $acc->album->addItems($opponent->equip)){
				$acc->album->encode();
				// $db->exec("UPDATE players SET album_data = '".$acc->album->data."', album = ".$acc->album->count." WHERE ID = ".$acc->data['ID']);
				$qryArgs[] = 'album_data = "'.$acc->album->data.'", album = '.$acc->album->count;
				$ret[] = "scrapbook.r:".$acc->album->data;
				$acc->data['album'] = $acc->album->count;
			}
		}else{
			$honor = 200 - $honor;

			$rewardLog[5] = '-'.$honor;
			$qryArgs[] = "honor = GREATEST(0, honor - $honor)";

			$db->exec("UPDATE players SET honor = honor + $honor WHERE ID = ".$opponent->data['ID']);
		}

		//reset arena enemies
		for($i = 1; $i <= 3; $i++){
			$acc->data["arena_nme$i"] = 0;
			$qryArgs[] = "arena_nme$i = 0";
		}


		$db->exec("UPDATE players SET ".join(',', $qryArgs)." WHERE ID = $playerID");


		$ret[] = "fight.r:".$simulation->fightLog;
		$ret[] = "winnerid:".$simulation->winnerID;
		$ret[] = "Success:";
		$ret[] = "fightresult.battlereward:".join("/", $rewardLog)."/";
		$ret[] = "ownplayersave.playerSave:".$acc->getPlayerSave();
		$ret[] = "timestamp:".time();
		// $ret[] = "combatloglist.s:178047146,RagnarÃ¶k,1,0,1453561071,0;1829371711,Mrozu,0,9,1453548804,0;2019179682,Arbuz,0,0,1453542838,0;608863024,Fort Szatana,1,2,1453536855,0;749062124,Schwarze Seelen,0,2,1453529461,0;1756257553,Smoke,1,9,1453527707,0;1430622921,KleinesGrÃ¼nesMÃ¤nnchen,1,0,1453498654,0;690391557,Fort Szatana,1,2,1453494628,0;209357733,Schwarze Seelen,0,2,1453484127,0;1069100244,Yufie,0,0,1453470289,0;1577088167,Fort Szatana,1,2,1453449488,0;1085402077,KeMi,0,0,1453448193,0;1891565546,Schwarze Seelen,0,2,1453439977,0;615269297,FaiX,0,0,1453410393,0;2118894081,Gnadenlos,1,2,1453405221,0;59208430,Mysticwoman,1,0,1453392274,0;937567384,Gnadenlos,1,2,1453355203,0;1508609115,Schwarze Seelen,0,2,1453352471,0;1891729205,spino,1,0,1453329887,0;35842795,Gnadenlos,1,2,1453313103,0;931279938,Schwarze Seelen,0,2,1453310626,0;1192275299,Yulivee,0,0,1453297521,0;1141435371,Aviro,0,0,1453280641,0;721157918,Yulivee,0,0,1453277920,0;2033375401,Yulivee,0,0,1453274653,0;1245941037,Gnadenlos,1,2,1453268569,0;1954219141,Schwarze Seelen,0,2,1453267929,0;1987180995,Petter,0,0,1453229942,0;932256980,Gnadenlos,1,2,1453225438,0;1134002502,Schwarze Seelen,0,2,1453225037,0;1463110198,Mysticwoman,1,0,1453209612,0;908422992,crpzh,1,0,1453207557,0;1239827316,Swordrain,0,0,1453206326,0;1135909002,Gnadenlos,1,2,1453180647,0;1367861018,crpzh,0,0,1453168359,0;811858250,X9Rambo6X,1,0,1453156913,0;866597934,Momochi,0,9,1453151581,0;1426181582,Terrorman79,1,0,1453148036,0;165539174,18,0,3,1453136739,0;1214503715,ChallEnGeRRR,0,0,1453131933,0;1220250895,zarondechanger,1,0,1453114143,0;1927290930,17,1,3,1453093834,0;784169800,GrupaAzoty,1,2,1453082945,0;173641481,crpzh,0,0,1453068685,0;2030313976,crpzh,0,0,1453053120,0;1703023368,Deathrix,1,0,1453052841,0;1171886111,16,1,3,1453051246,0;601901905,FaiX,1,0,1453038432,0;576197653,Jan,0,0,1453035790,0;1854324173,audia17,1,0,1453032401,0;";



		break;
	case 'wheeloffortune':
		$ret[] = "Success:";
		$ret[] = "wheelresult(2):0/420";
		$ret[] = "timestamp:".time();
		
		break;
	case 'playermessageview':

		//arg0 = messageID

		$qry = $db->prepare("SELECT ID, message FROM messages WHERE reciver = $playerID AND ID = :msgid");
		$qry->bindParam(':msgid', $args[0]);
		$qry->execute();
		$msg = $qry->fetch(PDO::FETCH_ASSOC);

		if($qry->rowCount()>0){
				
			$db->exec('UPDATE messages SET hasRead = true WHERE ID = '.$msg['ID']);

			$ret[] = 'messagetext.s:'.$msg['message'];
			$ret[] = 'Success:';
		}

		break;
	case 'playermessagedelete':

		if($args[0] == -1){
			$db->exec("DELETE FROM messages WHERE reciver = $playerID");

			exit('Success:&messagelist.r:');
		}else{
			$db->exec("DELETE FROM messages WHERE reciver = $playerID AND ID = $args[0]");

			$messages = $db->query("SELECT messages.ID, players.name, messages.hasRead, messages.topic, messages.time 
				FROM messages LEFT JOIN players ON messages.sender = players.ID WHERE reciver = $playerID ORDER BY time DESC");
			$messages = $messages->fetchAll(PDO::FETCH_ASSOC);
			// $msgs = [];
			// foreach($messages as $msg){
			// 	if(strlen($msg['name']) == 0)
			// 		$msg['name'] = 'admin';
			// 	$msgs[] = join(',', $msg);
			// }

			$ret[] = 'Success:';
			$ret[] = 'messagelist.r:'.formatMessages($messages);
		}



		break;
	case 'grouplookat':
		// arg 0 = guild name

		$qry = $db->prepare('SELECT ID FROM guilds WHERE name = :name');
		$qry->bindParam(':name', $args[0]);
		$qry->execute();

		if($qry->rowCount() == 0)
			exit('Error:group not found');
		
		$guild = new Guild($qry->fetch(PDO::FETCH_ASSOC)['ID']);

		$ret[] = 'Success:';
		$ret[] = 'othergroup.groupSave:'.$guild->getGroupSave();
		$ret[] = 'othergroupdescription.s:';
		$ret[] = 'othergroupname.r:'.$guild->data['name'];
		$ret[] = 'othergroupmember.s:'.$guild->getMemberList();
		$ret[] = 'othergrouprank:1';
		$ret[] = 'othergroupfightcost:'.Guild::getAttackCost($guild->data['base']);
		if(($oga = $guild->getOtherGroupAttack()) !== false)
			$ret[] = $oga;

		break;
	case 'groupsetofficer':

		$time = time();
		$names = $db->query("SELECT name, guild_rank FROM players WHERE ID = $playerID OR ID = $args[0] ORDER BY guild_rank")->fetchAll();

		$promote = $names[1]['guild_rank'] == 2? 3 : 2;


		//TODO: check if leader, where guild = (select guild from players where id = playerid and guildrank = leader) - see if that'll work
		if(!$db->exec("UPDATE players SET guild_rank = $promote WHERE ID = $args[0] AND guild = $playerGuild"))
			exit('Error:');

		$ftime = gmdate("H:i", time() + 3600);
		$name1 = $names[0]['name'];
		$name2 = $names[1]['name'];
		$message = "#ra#$ftime $name1#$promote#$name2";

		//get chat before updating poll
		// $db->exec("INSERT INTO guildchat(guildID, playerID, message, time) VALUES($playerGuild, $playerID, '$message', $time)");

		// $chat = $db->query("SELECT players.name, guildchat.message, guildchat.time FROM guildchat LEFT JOIN players ON guildchat.playerID = players.ID 
		// 	WHERE guildchat.guildID = $playerGuild AND guildchat.time > $playerPoll ORDER BY guildchat.time DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
		$chattime = chatInsert($message, $playerGuild, $playerID);
		$chat = getChat($playerGuild);

		//update player poll
		$db->exec("UPDATE players SET poll = $time WHERE ID = $playerID");

		$guild = new Guild($playerGuild);

		$ret[] = 'Success:';
		$ret[] = 'owngroupsave.groupSave:'.$guild->getGroupSave();
		$ret[] = 'chathistory.s(5):'.formatChat($chat);
		$ret[] = "chattime:$chattime";

		break;
	case 'groupsetleader':

		//TODO: check everything
		$qry = $db->query("SELECT name, guild, guild_rank FROM players WHERE ID = $args[0] OR ID = $playerID ORDER BY guild_rank DESC")->fetchAll(PDO::FETCH_ASSOC);
		$newleader = $qry[0];
		$player = $qry[1];

		if($newleader['guild'] != $player['guild'] || $player['guild_rank'] != 1)
			exit();

		$db->exec("UPDATE players SET guild_rank = 2 WHERE ID = $playerID;UPDATE players SET guild_rank = 1 WHERE ID = $args[0]");

		$message = "#rv#$newleader[name]#$player[name]";

		$chattime = chatInsert($message, $playerGuild, $playerID);

		$guild = new Guild($playerGuild);

		$ret[] ='Success:';
		$ret[] ='owngroupsave.groupSave:'.$guild->getGroupSave();
		$ret[] ='owngroupmember.r:'.$guild->getMemberList();
		$ret[] ='chathistory.s(5):'.formatChat(getChat($playerGuild));
		$ret[] ="chattime:$chattime";

		break;
	case 'groupremovemember':

		$time = time();	


		if($playerID == $args[0]){
			$acc = new Account(null, null, false, false);

			//disband guild
			if($acc->data['guild_rank'] == 1){
				//TODO: send mail to players with system message
				// 1077418003,Pan Marcel,0,1,1387482797

				$db->exec("INSERT INTO messages(sender, reciver, time, topic, message) SELECT $playerID, players.ID, UNIX_TIMESTAMP(), '1', '{$acc->data['gname']}' FROM players WHERE guild = $playerGuild AND players.ID != $playerID;
					UPDATE players SET guild = 0, guild_rank = 3, guild_fight = 0 WHERE guild = $playerGuild;
					DELETE FROM guilds WHERE ID = $playerGuild;
					DELETE FROM guildchat WHERE guildID = $playerGuild;
					DELETE FROM guildinvites WHERE guildID = $playerGuild;
					DELETE FROM guildfights WHERE guildAttacker = $playerGuild OR guildDefender = $playerGuild LIMIT 2;");

			}else{
				$ftime = gmdate("H:i", $time + 3600);
				$name = $acc->data['name'];
				$message = "#ou#$ftime $name";

				chatInsert($message, $playerGuild, $playerID);
				$db->exec("UPDATE players SET guild = 0, guild_rank = 3, guild_fight = 0 WHERE ID = $playerID");
			}

			$acc->data['guild'] = 0;
			$acc->data['guild_rank'] = 3;

			$ret[] = 'Success:';
			$ret[] = 'ownplayersave.playerSave:'.$acc->getPlayerSave();
			break;
		}

		//see if just removing invite
		if(!$db->exec("DELETE FROM guildinvites WHERE guildID = $playerGuild AND playerID = $args[0]")){

			//send message to the kicked player


			$db->exec("UPDATE players SET guild = 0, guild_rank = 3, guild_fight = 0 WHERE ID = $args[0]");

			$ftime = gmdate("H:i", $time + 3600);
			$name = $db->query("SELECT name FROM players WHERE ID = $args[0]")->fetch(PDO::FETCH_ASSOC)['name'];
			$message = "#ou#$ftime $name";

			//get chat before updating poll
			$chattime = chatInsert($message, $playerGuild, $playerID);

			$chat = getChat($playerGuild);

			//update player poll
			$db->exec("UPDATE players SET poll = $time WHERE ID = $playerID");
			$ret[] = 'chathistory.s(5):'.formatChat($chat);
			$ret[] = "chattime:$chattime";
		}
		
		$guild = new Guild($playerGuild);

		$ret[] = 'Success:';
		$ret[] = 'owngroupsave.groupSave:'.$guild->getGroupSave();

		break;
	case 'groupinvitemember':

		$guild = new Guild($playerGuild);

		if(!$guild->hasFreeInvitePlace())
			exit('Error:group is full');

		$time = time();
		$gName = $guild->data['name'];

		//insert message
		$qry = $db->prepare("INSERT INTO messages(sender, reciver, time, topic, message) SELECT $playerID, players.ID, $time, 5, '$gName' FROM players WHERE name = :name");
		$qry->bindParam(':name', $args[0]);
		$qry->execute();

		//insert invite
		$qry = $db->prepare("INSERT INTO guildinvites(guildID, playerID) SELECT $playerGuild, players.ID FROM players WHERE name = :name");
		$qry->bindParam(':name', $args[0]);
		$qry->execute();

		//update invites in guild object
		$invited = $db->query("SELECT players.ID, players.name, players.lvl FROM guildinvites LEFT JOIN players ON guildinvites.playerID = players.ID WHERE guildinvites.guildID = $playerGuild ORDER BY players.lvl DESC");
		$guild->invites = $invited->fetchAll(PDO::FETCH_ASSOC);

		

		$ret[] = 'Success:';
		$ret[] = 'owngroupsave.groupSave:'.$guild->getGroupSave();
		$ret[] = 'owngroupmember.r:'.$guild->getMemberList();

		break;
	case 'groupinviteaccept':

		if($playerGuild != 0)
			exit('Error:must leave group first');

		$qry = $db->prepare("SELECT guilds.ID, event_trigger_count FROM guilds WHERE name = :name");
		$qry->bindParam(':name', $args[0]);
		$qry->execute();
		$obj = $qry->fetch(PDO::FETCH_ASSOC);
		$guildID = $obj['ID'];
		$etc = $obj['event_trigger_count'];

		if($qry->rowCount() == 0)
			exit('Error:group not found');

		if(!$db->exec("DELETE FROM guildinvites WHERE guildID = $guildID AND playerID = $playerID"))
			exit('Error:you are not invited');

		$acc = new Account(null, null, false, false);
		$acc->data['guild'] = $guildID;
		$acc->data['guild_rank'] = 3;
		$acc->data['event_trigger_count'] = $etc;

		//insert message
		$message = '#in#'.$acc->data['name'];
		$time = time();
		// $db->exec("INSERT INTO guildchat(guildID, playerID, message, time) VALUES($guildID, $playerID, '$message', $time)");
		$chattime = chatInsert($message, $guildID, $playerID);
		$chat = getChat($guildID);

		$db->exec("UPDATE players SET guild = $guildID, guild_rank = 3, event_trigger_count = $etc, guild_fight = 0 WHERE ID = $playerID");


		$guild = new Guild($guildID);

		$ret[] = 'Success:';
		$ret[] = 'ownplayersave.playerSave:'.$acc->getPlayerSave();
		$ret[] = 'owngroupsave.groupSave:'.$guild->getGroupSave();
		$ret[] = "owngrouppotion.r:".$guild->getPotionData();
		$ret[] = "owngroupname.r:".$guild->data['name'];
		$ret[] = "owngroupdescription.s:";
		$ret[] = "owngroupmember.r:".$guild->getMemberList();
		$ret[] = "owngrouprank:1";
		$ret[] = "chathistory.s(5):".formatChat($chat);
		$ret[] = "chattime:$chattime";

		break;
	case 'groupspendgold':

		$time = time();

		$playerData = $db->query("SELECT players.name, players.silver, guilds.silver AS gsilver FROM players LEFT JOIN guilds ON players.guild = guilds.ID WHERE players.ID = $playerID")->fetch(PDO::FETCH_ASSOC);
		$playerSilver = $playerData['silver'];
		$name = $playerData['name'];
		$gSilver = $playerData['gsilver'];

		if($playerSilver < $args[0])
			exit("Error:need more gold");
		if($gSilver >= 1000000000)
			exit('Error:group chest is full');

		// TODO: if gSilver + args0 > cap, decrease to cap - gsivler
		if($gSilver + $args[0] > 1000000000)
			$args[0] = 1000000000 - $gSilver;

		$ftime = gmdate("H:i", $time + 3600);
		$message = "#dg#$ftime $name#$args[0]";

		//get chat before updating poll
		// $db->exec("INSERT INTO guildchat(guildID, playerID, message, time) VALUES($playerGuild, $playerID, '$message', $time)");

		// $chat = $db->query("SELECT players.name, guildchat.message, guildchat.time FROM guildchat LEFT JOIN players ON guildchat.playerID = players.ID 
		// 	WHERE guildchat.guildID = $playerGuild AND guildchat.time > $playerPoll ORDER BY guildchat.time DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
		$chattime = chatInsert($message, $playerGuild, $playerID);
		$chat = getChat($playerGuild);

		//update player and guild gold
		$db->exec("UPDATE players SET silver = silver - $args[0], poll = $time WHERE ID = $playerID;UPDATE guilds SET silver = silver + $args[0] WHERE ID = $playerGuild;");

		$guild = new Guild($playerGuild);
		
		$playerSilver -= $args[0];

		$ret[] = 'Success:';
		$ret[] = "#ownplayersave:2/$time/13/$playerSilver";//.$acc->getPlayerSave();
		$ret[] = "timestamp:$time";
		$ret[] = 'owngroupsave.groupSave:'.$guild->getGroupSave();
		$ret[] = 'chathistory.s(5):'.formatChat($chat);
		$ret[] = "chattime:$chattime";

		break;
	case 'groupspendcoins':

		$time = time();

		$playerData = $db->query("SELECT players.name, players.mush, guilds.mush AS gmush FROM players LEFT JOIN guilds ON players.guild = guilds.ID WHERE players.ID = $playerID")->fetch(PDO::FETCH_ASSOC);
		$playerMush = $playerData['mush'];
		$name = $playerData['name'];
		$gMush = $playerData['gmush'];


		if($playerMush < $args[0])
			exit("Error:need more coins");
		if($gMush >= 10000)
			exit('Error:group chest is full');

		if($gMush + $args[0] > 10000)
			$args[0] = 10000 - $gSilver;


		$ftime = gmdate("H:i", $time + 3600);
		$message = "#dm#$ftime $name#$args[0]";

		//get chat before updating poll
		// $db->exec("INSERT INTO guildchat(guildID, playerID, message, time) VALUES($playerGuild, $playerID, '$message', $time)");
		// $chat = $db->query("SELECT players.name, guildchat.message, guildchat.time FROM guildchat LEFT JOIN players ON guildchat.playerID = players.ID 
		// 	WHERE guildchat.guildID = $playerGuild AND guildchat.time > $playerPoll ORDER BY guildchat.time DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
		$chattime = chatInsert($message, $playerGuild, $playerID);
		$chat = getChat($playerGuild);


		//update player and guild gold
		$db->exec("UPDATE players SET mush = mush - $args[0], poll = $time WHERE ID = $playerID;UPDATE guilds SET mush = mush + $args[0] WHERE ID = $playerGuild;");

		//load guild
		$guild = new Guild($playerGuild);

		//insert this in chat #dg#14:29 Pan Marcel#38500
		$time = time();

		
		$playerMush -= $args[0];

		$ret[] = 'Success:';
		$ret[] = "#ownplayersave:2/$time/14/$playerMush/437/$playerMush";//.$acc->getPlayerSave();
		$ret[] = "timestamp:$time";
		$ret[] = 'owngroupsave.groupSave:'.$guild->getGroupSave();
		$ret[] = 'chathistory.s(5):'.formatChat($chat);
		$ret[] = "chattime:$chattime";

		// $message = "#dm#$time $name#$donation";


		break;
	case 'groupincreasebuilding':

		$player = $db->query("SELECT name, guild_rank FROM players WHERE ID = $playerID")->fetch(PDO::FETCH_ASSOC);

		if($player['guild_rank'] == 3)
			exit();

		$guild = new Guild($playerGuild);

		$building = ['base', 'treasure', 'instructor'][$args[0] - 1];

		if($guild->data[$building] >= 50)
			exit();

		$cost = Guild::getGuildBuildingCost($guild->data[$building]);

		if($guild->data['silver'] < $cost['silver'])
			exit('Error:need more gold');

		if($guild->data['mush'] < $cost['mushroom'])
			exit('Error:need more coins');

		$guild->data[$building] ++;
		$guild->data['silver'] -= $cost['silver'];
		$guild->data['mush'] -= $cost['mushroom'];

		$db->exec("UPDATE guilds SET $building = $building + 1, silver = silver - $cost[silver], mush = mush - $cost[mushroom] WHERE ID = $playerGuild");

		//log
		$time = time();
		$ftime = gmdate("H:i", $time + 3600);
		$message = "#bd#$ftime $player[name]#$args[0]";

		//get chat before updating poll
		// $db->exec("INSERT INTO guildchat(guildID, playerID, message, time) VALUES($playerGuild, $playerID, '$message', $time)");
		// $chat = $db->query("SELECT players.name, guildchat.message, guildchat.time FROM guildchat LEFT JOIN players ON guildchat.playerID = players.ID 
		// 	WHERE guildchat.guildID = $playerGuild AND guildchat.time > $playerPoll ORDER BY guildchat.time DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
		$chattime = chatInsert($message, $playerGuild, $playerID);
		$chat = getChat($playerGuild);

		//update player poll
		$db->exec("UPDATE players SET poll = $time WHERE ID = $playerID");


		$ret[] = 'Success:';
		$ret[] = 'owngroupsave.groupSave:'.$guild->getGroupSave();
		$ret[] = 'chathistory.s(5):'.formatChat($chat);
		$ret[] = "chattime:$chattime";

		break;
	case 'groupchat':

		$time = time();

		$chattime = chatInsert($args[0], $playerGuild, $playerID);
		$chat = getChat($playerGuild);

		$db->exec("UPDATE players SET poll = $time WHERE ID = $playerID");

		$ret[] = 'Success:';
		$ret[] = 'chathistory.s(5):'.formatChat($chat);
		$ret[] = "chattime:$chattime";


		break;
	case 'groupraiddeclare':
		//
		$args[0] = 1000000;
	case 'groupattackdeclare':


		$guild = new Guild($playerGuild);
		$guild->declareFight($args[0], $playerID);



		$ret[] = 'Success:';
		// $ret[] = '#ownplayersave:508/1';
		$ret[] = 'timestamp:'.time();
		$ret[] = 'owngroupsave.groupSave:'.$guild->getGroupSave();
		if($args[0] != 1000000){
			$aname = $db->query("SELECT name FROM guilds WHERE ID = $args[0]")->fetch(PDO::FETCH_ASSOC)['name'];
			$ret[] = "owngroupattack.r:".$aname;
		}

		break;
	case 'groupreadyattack':

		if($playerGuild == 0)
			exit();

		$fight = $db->query("SELECT guild_fight FROM players WHERE ID = $playerID")->fetch(PDO::FETCH_ASSOC)['guild_fight'];
		$fight++;
		if($fight != 1 && $fight != 3)
			exit();
		$db->exec("UPDATE players SET guild_fight = $fight WHERE ID = $playerID");

		$guild = new Guild($playerGuild);

		$ret[] = 'Success:';
		$ret[] = "#ownplayersave:508/$fight";
		$ret[] = 'timestamp:'.time();
		$ret[] = 'owngroupsave.groupSave:'.$guild->getGroupSave();

		break;
	case 'groupreadydefense':

		if($playerGuild == 0)
			exit();

		$fight = $db->query("SELECT guild_fight FROM players WHERE ID = $playerID")->fetch(PDO::FETCH_ASSOC)['guild_fight'];
		$fight += 2;
		if($fight != 2 && $fight != 3)
			exit();
		$db->exec("UPDATE players SET guild_fight = $fight WHERE ID = $playerID");

		$guild = new Guild($playerGuild);

		$ret[] = 'Success:';
		$ret[] = "#ownplayersave:508/$fight";
		$ret[] = 'timestamp:'.time();
		$ret[] = 'owngroupsave.groupSave:'.$guild->getGroupSave();


		break;
	case 'groupgetbattle':
		//#ownplayersave:2/1456434756/509/4709&

		$time = time();

		// //SEE IF SIM FIGHT and shiet
		$guildData = $db->query("SELECT event_trigger_count, dungeon, honor, name FROM guilds WHERE ID = $playerGuild")->fetch(PDO::FETCH_ASSOC);
		//TODO: check time here
		// $fights = $db->query("SELECT * FROM guildfights WHERE (guildAttacker = $playerGuild OR guildDefender = $playerGuild) AND time <= $time ORDER BY time ASC");
		$fights = $db->query("SELECT guildfights.ID, guildfights.guildAttacker, g1.name AS attacker, guildfights.guildDefender, g2.name AS defender, time 
			FROM guildfights LEFT JOIN guilds AS g1 ON guildfights.guildAttacker = g1.ID LEFT JOIN guilds AS g2 ON guildfights.guildDefender = g2.ID 
			WHERE (guildfights.guildAttacker = $playerGuild OR guildfights.guildDefender = $playerGuild) AND time <= $time ORDER BY time ASC");
		

		// //if simfight
		if(($n = $fights->rowCount()) > 0){
			$fights = $fights->fetchAll(PDO::FETCH_ASSOC);

			//delete fight from db right away
			$db->exec("DELETE FROM guildfights WHERE guildAttacker = $playerGuild OR guildDefender = $playerGuild LIMIT 2;");

			//update trigger count, defenders guild too
			foreach($fights as $fight){
				if($fight['guildDefender'] != 1000000)
					$db->exec("UPDATE guilds SET event_trigger_count = event_trigger_count + 1 WHERE ID = $fight[guildAttacker] OR ID = $fight[guildDefender]");
				else
					$db->exec("UPDATE guilds SET event_trigger_count = event_trigger_count + 1 WHERE ID = $fight[guildAttacker]");
				$guildData['event_trigger_count']++;
			}

			//ALG: loop through fights incase there are 2, always ordered by time ascending. Display only the lastest, which is simulated as 2nd
			//		if the fight is in guildfights table, it hasn't been simulated, simulate and add to logs
			//		always simulate from the perspective of the attacker, defender can use GroupSimulation::reverseGuildFightLog() on displaying

			foreach($fights as $fight){

				//fight log, plain string, fuck it
				$fightLog = [];

				//get players
				$players = $db->query("SELECT players.*, guilds.portal AS guild_portal FROM players LEFT JOIN guilds ON players.guild = guilds.ID WHERE players.guild = $fight[guildAttacker] ORDER BY lvl ASC")->fetchAll(PDO::FETCH_ASSOC);
				$playerObjects = [];
				$items = $db->query("SELECT players.ID AS pid, items.* FROM items LEFT JOIN players ON items.owner = players.ID WHERE players.guild = $fight[guildAttacker] AND items.slot BETWEEN 10 AND 19");
				$items = $items->fetchAll(PDO::FETCH_GROUP);
				foreach($players as $player){
					// @ - might not have any items
					@$playerObjects[] = new Player($player, $items[$player['ID']]);
				}

				//get opponents, see if guild raid
				if($fight['guildDefender'] == 1000000){
					//just get shit from guild data, only players from guild call this
					$opponentObjects = Monster::getGuildRaid($guildData['dungeon']);
				}else{
					//get other guild members here
					$opponents = $db->query("SELECT players.*, guilds.portal AS guild_portal, guilds.honor as ghonor FROM players LEFT JOIN guilds ON players.guild = guilds.ID WHERE players.guild = $fight[guildDefender] ORDER BY lvl ASC")->fetchAll(PDO::FETCH_ASSOC);
					$opponentObjects = [];
					$items = $db->query("SELECT players.ID as pid, items.* FROM items LEFT JOIN players ON items.owner = players.ID WHERE players.guild = $fight[guildDefender] AND items.slot BETWEEN 10 AND 19");
					$items = $items->fetchAll(PDO::FETCH_GROUP);
					foreach($opponents as $opponent){
						@$opponentObjects[] = new Player($opponent, $items[$opponent['ID']]);
					}
				}


				//simulate fight
				$simulation = new GroupSimulation($playerObjects, $opponentObjects);
				$simulation->simulate();

				//output logs
				for($i = 0; $i < count($simulation->simulations); $i++){
					$fightn = $i+1;
					$fightLog[] = "fightheader".$fightn.".fighters:3/0/0/1/1/".$simulation->fightHeaders[$i];
					$fightLog[] = "fight".$fightn.".r:".$simulation->simulations[$i]->fightLog;
					$fightLog[] = "winnerid".$fightn.".s:".$simulation->simulations[$i]->winnerID;
				}
				$fightLog[] = 'fightadditionalplayers.r:,,,,,,,,,,';

				
				if($fight['guildDefender'] == 1000000){
					//insert raid chat logs
					$guildData['dungeon']++;
					if($simulation->win)
						$chatTime = chatInsert("#rplus#$guildData[dungeon]#", $playerGuild, 0);
					else
						$chatTime = chatInsert("#rminus#$guildData[dungeon]#", $playerGuild, 0);
				}else{
					//count out honor

					//max honor diff = 2k								
					//formula: 100 + (opponent.honor - player.honor) / (max honor diff / (max diff / 100))
					$attHonor = $guildData['honor'];
					$defHonor = $opponents[0]['ghonor'];

					if(abs($diff = $defHonor - $attHonor) < 2000)
						$honor = 100 + round($diff / 20);
					else
						$honor = 0;

					//update guilds honor
					if($simulation->win)
						$db->exec("UPDATE guilds SET honor = honor + $honor WHERE ID = $fight[guildAttacker]; UPDATE guilds SET honor = GREATEST(0, honor - $honor) WHERE ID = $fight[guildDefender]");
					else
						$db->exec("UPDATE guilds SET honor = honor + $honor WHERE ID = $fight[guildDefender]; UPDATE guilds SET honor = GREATEST(0, honor - $honor) WHERE ID = $fight[guildAttacker]");


					//need names
					// $attName = $players[0]['gname'];
					// $defName = $opponents[0]['gname'];
					$attName = $fight['attacker'];
					$defName = $fight['defender'];


					//fight logs, both guilds
					if($simulation->win){
						$chatTimeAtt = chatInsert("#aplus#$defName#$honor#", $fight['guildAttacker'], 0);
						$chatTimeDef = chatInsert("#dminus#$attName#$honor#", $fight['guildDefender'], 0);
					}else{
						$chatTimeAtt = chatInsert("#aminus#$defName#$honor#", $fight['guildAttacker'], 0);
						$chatTimeDef = chatInsert("#dplus#$attName#$honor#", $fight['guildDefender'], 0);
					}

					// if($playerGuild == $fight['guildAttacker'])
					// 	$chatTime = $chatTimeAtt;
					// else
					// 	$chatTime = $chatTimeDef;
				}

				//battlereward
				$battleReward = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
				if($simulation->win){
					$battleReward[0] = 1;


					if($fight['guildDefender'] == 1000000){
						//add reward for dungeon
						$db->exec("UPDATE guilds SET dungeon = dungeon + 1 WHERE ID = $fight[guildAttacker]");
					}else{
						//add reward for guild war
						$battleReward[6] = $honor;
					}
				}
				$fightLog[] = 'fightresult.battlereward:'.join('/', $battleReward);

				



				// $ret[] = 'chathistory.s(5):'.formatChat(getChat($playerGuild));
				// $ret[] = "chattime:$chatTime";

				$fightLog = join('&', $fightLog);
				//save logs to db
				$db->exec("INSERT INTO guildfightlogs(guildAttacker, guildDefender, log, time) VALUES($fight[guildAttacker], $fight[guildDefender], '$fightLog', $fight[time])");

				//UPDATE player guild_fight of both guilds | this now is temporary
				$db->exec("UPDATE players SET guild_fight = 0 WHERE guild = $fight[guildAttacker]");
			}

			//TODO:check if have to use Guild::reverseGuildFightLog()
			if($args[0] == 1)
				$ret[] = $fightLog;

		}else if($args[0] == 1){
			//else if fight already simulated and player wants to see the fight, pull the logs ***** AND time > 0
			$log = $db->query("SELECT log FROM guildfightlogs WHERE (guildAttacker = $playerGuild OR guildDefender = $playerGuild) ORDER BY time DESC LIMIT 1");
			$log = $log->fetch(PDO::FETCH_ASSOC)['log'];
			$ret[] = $log;
		}

		
		//update player trigger count
		$db->exec("UPDATE players SET event_trigger_count = $guildData[event_trigger_count] WHERE ID = $playerID");
		
		//TODO:  include owngroupsave?
		$guild = new Guild($playerGuild);

		$ret[] = 'Success:';
		$ret[] = '#ownplayersave:509/'.$guildData['event_trigger_count'];
		$ret[] = 'owngroupsave.groupSave:'.$guild->getGroupSave();
		

		break;
	case 'playermessagewhisper':
		//arg 0 reciver
		//arg 1 msg

		//00:00 admin:§ not implemented
		exit('Error: not implemented');
		exit('Success:&chathistory.s(5):////');                                
		break;
	case 'playercombatlogmark':
		//fuck this feature
		$ret[] = 'Success:';
		break;
	case 'playerlastfightstore':
		$ret[] = 'Success:';
		break;
	case 'playersetdescription':

		// $qry = $db->prepare("UPDATE players SET description = :description WHERE ID = $playerID");
		// $qry->bindParam(':description', $args[0]);
		// $qry->execute();

		$ret[] = 'Success:';
		break;
	case 'poll':
		//check for new incomming messages, combat log, guild wars, and anything

		//TODO: separe update from chat update, give players chattime column? 
		$ret[] = 'Success:';
		$time = time();
		$update = false;


		//fortress unit train
		if($playerData['ut1'] > 0 || $playerData['ut2'] > 0 || $playerData['ut3'] > 0){
			$accUpdate = false;
			$qryArgs = [];
			for($i = 1; $i <= 3; $i++){
				if($playerData["ut$i"] > 0 && ($timeElapsed = time() - $playerData["uttime$i"]) > 600){

					$accUpdate = true;
					$units = floor($timeElapsed / 600);
					if($units < $playerData["ut$i"])
						$newtime = $playerData["uttime$i"] + $units * 600;
					else
						$newtime = 0;
					$qryArgs[] = "ut$i = ut$i - $units, u$i = u$i + $units, uttime$i = $newtime";
				}
			}

			if($accUpdate){
				$db->exec("UPDATE fortress SET ".join(',', $qryArgs)." WHERE owner = $playerID");
				$acc = new Account();

				$ret[] = 'ownplayersave.playerSave:'.$acc->getPlayerSave();
			}
		}else if($playerPoll < $time - 30){
			//check messages with 30 sec intervall, this is incase chat is polling to reduce the load somewhat significantly
			$messages = $db->query("SELECT COUNT(*) FROM messages WHERE reciver = $playerID AND messages.time > $playerPoll")->fetch(PDO::FETCH_ASSOC);
			$newMessages = $messages['COUNT(*)'];
			
			if($newMessages > 0){
				$update = true;

				$messages = $db->query("SELECT messages.ID, players.name, messages.hasRead, messages.topic, messages.time 
					FROM messages LEFT JOIN players ON messages.sender = players.ID WHERE messages.reciver = $playerID ORDER BY time DESC");
				$messages = $messages->fetchAll(PDO::FETCH_ASSOC);
				// $msgs = [];
				// foreach($messages as $msg){
				// 	if(strlen($msg['name']) == 0)
				// 		$msg['name'] = 'admin';
				// 	$msgs[] = join(',', $msg);
				// }

				$ret[] = 'messagelist.r:'.formatMessages($messages);
				$ret[] = '#ownplayersave.playerSave:434/'.$messages[0]['ID'];

				//check if kick from guild message, load playerdata
			}
		}


		//types: 1 - quest, 2 - guild, 3 - raid, 4 - dungeon, 5 - tower, 6 - portal, 7 = gportal, 8 - fortressAtt, 9 - fortressDef, 10 - dark dungeons
		//ID,target name, win, type, time, marked
		// $ret[] = "combatloglist.s:1234,603,0,1,1456680807,0;1234,Nowakowscy,1,2,1456677448,0;";

		//guild chat and guild refreshing
		if($playerGuild > 0){
			// $chat = $db->query("SELECT players.name, guildchat.message, guildchat.time FROM guildchat LEFT JOIN players ON guildchat.playerID = players.ID 
			// 	WHERE guildchat.guildID = $playerGuild AND guildchat.time > $playerPoll ORDER BY guildchat.time DESC LIMIT 5");
			$chat = $db->query("SELECT Max(time) as newm, Max(chattime) as chattime FROM guildchat WHERE guildID = $playerGuild")->fetch(PDO::FETCH_ASSOC);

			if($chat['newm'] > $playerPoll){
				$update = true;
				$chattime = $chat['chattime'];
				$chat = getChat($playerGuild);
				$ret[] = 'chathistory.s(5):'.formatChat($chat);
				$ret[] = "chattime:$chattime";

				//IF system message, poll guild dataww
				if(containsSystemMessage($chat) || $playerPoll < $time - 10){
					$guild = new Guild($playerGuild);
					$ret[] = 'owngroupsave.groupSave:'.$guild->getGroupSave();
					$ret[] = "owngrouppotion.r:".$guild->getPotionData();
					$ret[] = "owngroupname.r:".$guild->data['name'];
					$ret[] = "owngroupdescription.s:";
					$ret[] = "owngroupmember.r:".$guild->getMemberList();
					$ret[] = "owngrouprank:1";
					if(($oga = $guild->getOtherGroupAttack()) !== false)
						$ret[] = $oga;
				}

				//pull all shit above and bellow, namelist, potionlist, etc...
			}else if($playerPoll < $time - 30){
				//check time and load guild here every 60 sec or something?
				$update = true;
				$guild = new Guild($playerGuild);
				$ret[] = 'owngroupsave.groupSave:'.$guild->getGroupSave();
				$ret[] = "owngrouppotion.r:".$guild->getPotionData();
				$ret[] = "owngroupname.r:".$guild->data['name'];
				$ret[] = "owngroupdescription.s:";
				$ret[] = "owngroupmember.r:".$guild->getMemberList();
				$ret[] = "owngrouprank:1";
				if(($oga = $guild->getOwnGroupAttack()) !== false)
					$ret[] = $oga;
			}
		}




		//LIMIT THIS, CHECK IF 1 MIN GONE BY, chat will rek dis
		//update variable is true if player has recieved a message or read chat, without it shit will keep pulling
		if($update || $playerPoll < $time - 90)
			$db->exec("UPDATE players SET poll = $time WHERE ID = $playerID");

		
		break;
	default:

		//exit("Error:");

		$ret[] = "Success:";
		var_dump($act);
		var_dump($args);


		break;
}


echo join("&", $ret);
?>
<?php

class Guild{

	//guild data
	public $data;

	//members
	public $players;

	//invites players (grey)
	public $invites;

	//guild fights
	public $fights;

	// public function Guild($guildData, $players){
	// 	$this->data = $guildData;
	// 	$this->players = $players;
	// }

	public function Guild($guildID){
		$guildData = $GLOBALS['db']->query("SELECT * FROM guilds WHERE ID = $guildID");
		$this->data = $guildData->fetch(PDO::FETCH_ASSOC);

		$players = $GLOBALS['db']->query("SELECT ID, name, lvl, poll, guild_rank, gportal_time, guild_fight, potion_type1, potion_type2, potion_type3, potion_dur1, potion_dur2, potion_dur3 
			FROM players WHERE guild = $guildID ORDER BY guild_rank ASC, lvl DESC");
		$this->players = $players->fetchAll(PDO::FETCH_ASSOC);

		$invited = $GLOBALS['db']->query("SELECT players.ID, players.name, players.lvl FROM guildinvites LEFT JOIN players ON guildinvites.playerID = players.ID WHERE guildinvites.guildID = $guildID ORDER BY players.lvl DESC");
		$this->invites = $invited->fetchAll(PDO::FETCH_ASSOC);

		$gfights = $GLOBALS['db']->query("SELECT guildfights.guildAttacker, g1.name attacker, guildfights.guildDefender, g2.name as defender, guildfights.time FROM guildfights LEFT JOIN guilds AS g1 ON g1.ID = guildfights.guildAttacker LEFT JOIN guilds AS g2 ON g2.ID = guildfights.guildDefender WHERE guildfights.guildAttacker = $guildID OR guildfights.guildDefender = $guildID;");
		$this->fights = $gfights->fetchAll(PDO::FETCH_ASSOC);

		// $gfights = $GLOBALS['db']->query("SELECT * FROM guildfights WHERE guildAttacker = $guildID OR guildDefender = $guildID");
		// $this->fights = $gfights->fetchAll(PDO::FETCH_ASSOC);

		// foreach($this->fights as &$fight){
		// 	if($fight['guildDefender'] != 1000000){
		// 		$nid = $fight['guildAttacker'] == $this->data['ID'] ? $fight['guildDefender'] : $fight['guildAttacker'];
		// 		$fight['name'] = $GLOBALS['db']->query("SELECT name FROM guilds WHERE ID = $nid")->fetch(PDO::FETCH_ASSOC)['name'];
		// 	}
		// }

	}

	public function getGroupSave(){
		$ret = [];

		for($i = 0; $i <= 488; $i++)
			$ret[] = 0;

		$ret[0] = $this->data['ID'];
		$ret[1] = $this->data['silver'];
		$ret[2] = $this->data['mush'];

		$ret[3] = count($this->players) + count($this->invites);
		//chuj wie co to jest
		// $ret[4] = 5;

		$portalHP = $this->data['portal'] >= 50 ? 0 : round($this->data['portal_hp'] / Monster::getGuildPortalMonster($this->data['portal'])->hp * 100);


		$ret[5] = $this->data['base'];
		$ret[6] = $this->data['treasure'] + 65536 * $portalHP;
		$ret[7] = $this->data['instructor'] + $this->data['portal'] * 65536;

		$ret[8] = $this->data['dungeon'];


		//attack declarer
		$ret[10] = $this->data['attack_init'];

		//leader last login
		$ret[12] = $this->players[0]['poll'];


		//eventtriggerbullcrap, window popup about last guild fight, triggers the act to sim fight
		$ret[368] = $this->data['event_trigger_count'];


		foreach($this->fights as $fight){
			if($fight['guildAttacker'] == $this->data['ID']){
				$i = 364;
				if($fight['guildDefender'] == 1000000)
					$ret[9] = $this->data['dungeon'] + 1;
			} else
				$i = 366;

			$ret[$i] = $fight['guildAttacker'];
			$ret[$i + 1] = $fight['time'];

			if($fight['time'] <= time())
				$ret[368]++;
		}


		// for($i = 0; $i < $ret[3]; $i++){
		$i = 0;
		foreach($this->players as $player){
			$ret[14 + $i] = $player['ID'];
			$ret[64 + $i] = $player['lvl'] + ($player['guild_fight'] * 1000);
			$ret[114 + $i] = $player['poll'];
			$ret[164 + $i] = $player['gportal_time'];
			//donates
			// $ret[214 + $i] = 1;
			// $ret[264 + $i] = 1;
			$ret[314 + $i] = $player['guild_rank'];
			$i++;
		}


		//iterate through invites too
		foreach($this->invites as $invite){
			$ret[14 + $i] = $invite['ID'];
			$ret[64 + $i] = $invite['lvl'];

			$ret[314 + $i] = 4;

			$i++;
		}

		// $fn = "";
		// foreach($this->fights as $fight)
		// 	if($fight['guildAttacker'] == $this->data['ID'] && $fight['guildDefender'] != 1000000)
		// 		$fn .= "&owngroupattack.r:$fight[name]";
		// 	else
		// 		$fn .= "&owngroupdefend.r:$fight[name]";

		return join('/', $ret);
	}

	public function getOwnGroupAttack(){
		if(count($this->fights) == 0)
			return false;

		$ret = [];

		foreach($this->fights as $fight){
			if($fight['guildAttacker'] == $this->data['ID'] && $fight['guildDefender'] != 1000000)
				$ret[] = "owngroupattack.r:$fight[defender]";
			else if($fight['guildDefender'] == $this->data['ID'])
				$ret[] = "owngroupdefense.r:$fight[attacker]";
		}

		return join('&', $ret);
	}

	public function getOtherGroupAttack(){
		if(count($this->fights) == 0)
			return false;

		$ret = [];

		foreach($this->fights as $fight){
			if($fight['guildAttacker'] == $this->data['ID'] && $fight['guildDefender'] != 1000000)
				$ret[] = "othergroupattack.r:$fight[defender]";
			else if($fight['guildDefender'] == $this->data['ID'])
				$ret[] = "othergroupdefense.r:$fight[attacker]";
		}

		return join('&', $ret);
	}

	// public function getOwnGroupAttack(){
	// 	if(count($this->fights) == 0)
	// 		return false;
	// 	$ret = "";

	// 	$att = $this->fights[0]['name'];
	// 	$def = "dupaki";

	// 	foreach($this->fights as $fight){
	// 		if($fight['guildAttacker'] == $this->data['ID'] && $fight['guildDefender'] != 1000000)
	// 			$ret .= "&owngroupattack.r:$fight[name]";
	// 		else if($fight['guildDefender'] == $this->data['ID'])
	// 			$ret .= "&owngroupdefense.r:$fight[name]";
	// 	}

	// 	if(strlen($ret) > 1)
	// 		return $ret;
	// 	return false;

	// }

	// public function getOtherGroupAttack(){
	// 	if(count($this->fights) == 0)
	// 		return false;
	// 	$ret = "";

	// 	$att = $this->fights[0]['name'];
	// 	$def = "dupaki";

	// 	foreach($this->fights as $fight){
	// 		if($fight['guildAttacker'] == $this->data['ID'] && $fight['guildDefender'] != 1000000)
	// 			$ret .= "&othergroupattack.r:$fight[name]";
	// 		else if($fight['guildDefender'] == $this->data['ID'])
	// 			$ret .= "&othergroupdefense.r:$fight[name]";
	// 	}

	// 	if(strlen($ret) > 1)
	// 		return $ret;
	// 	return false;
	// }

	public function getPotionData(){
		$ret = [];

		$time = time();
		//type,power,type,power....
		foreach($this->players as $player){

			for($i = 1; $i <= 3; $i++){
				if($player["potion_dur$i"] > $time){
					$ret[] = $player["potion_type$i"];
					$ret[] = 25;
				}else{
					$ret[] = 0;
					$ret[] = 0;
				}
			}
		}

		return join(',', $ret).',';
	}

	public function getMemberList(){
		$ret = '';

		foreach($this->players as $player)
			$ret .= $player['name'].',';
		foreach($this->invites as $invite)
			$ret .= $invite['name'].',';

		return $ret;
	}

	//make this function add monster for every player that has album
	//also add every monster before that, wether its portal or dungeon
	public function addAlbumMonster($monsterID){
		
	}

	public function hasFreePlace(){
		return count($this->players) < $this->data['base'];
	}

	public function hasFreeInvitePlace(){
		return (count($this->players) + count($this->invites)) < $this->data['base'];
	}


	//sets portal time display data of ones own character for the response
	public function guildPortalCD($pid){
		foreach($this->players as &$player){
			if($player['ID'] == $pid){
				$player['gportal_time'] = time();
				break;
			}
		}
	}

	public function declareFight($target, $declarer){
		foreach($this->fights as $fight){
			if($fight['guildAttacker'] == $this->data['ID'])
				exit();
		}


		//2 hour wait time
		$fightTime = time() + 3600*2;

		if($target != 1000000){
			$targetGuild = $GLOBALS['db']->query("SELECT base FROM guilds WHERE ID = $target");

			if($targetGuild->rowCount() == 0)
				exit('Error:group not found');

			$targetGuild = $targetGuild->fetch(PDO::FETCH_ASSOC);
			$attack = $GLOBALS['db']->query("SELECT ID FROM guildfights WHERE guildDefender = $target");

			if($attack->rowCount() > 0)
				exit('Error:');

			if(($cost = Guild::getAttackCost($targetGuild['base'])) > $this->data['silver'])
				exit('Error:need more gold');

		}else{
			if(($cost = Guild::getRaidCost($this->data['dungeon'])) > $this->data['silver'])
				exit('Error:need more gold');
			//just in case
			if($this->data['dungeon'] >= 50)
				exit();
		}



		//set declarer attack state
		foreach($this->players as &$player){
			if($player['ID'] == $declarer){
				$player['guild_fight']++;
				break;
			}
		}

		$GLOBALS['db']->exec("UPDATE players SET guild_fight = guild_fight + 1 WHERE ID = $declarer");


		$this->fights[] = ['guildAttacker' => $this->data['ID'], 'guildDefender' => $target, 'time' => $fightTime];
		$this->data['attack_init'] = $declarer;
		$this->data['silver'] -= $cost;

		$GLOBALS['db']->exec("INSERT INTO guildfights(guildAttacker, guildDefender, time) VALUES(".$this->data['ID'].", $target, $fightTime)");
		$GLOBALS['db']->exec("UPDATE guilds SET attack_init = $declarer, silver = silver - $cost WHERE ID = ".$this->data['ID']);
	}

	public static function getChat($guildID){
		// $db->query('SELECT players.name, guildchat.message, guildchat.time FROM guildchat LEFT JOIN players ON guildchat.playerID = players.ID 
		// 	WHERE guildchat.guildID = $guildID AND guildchat.time ORDER BY guildchat.time DESC LIMIT 5')
	}

	public static function getCreateGroupSave($guildID, $playerID, $playerLvl){

		$ret = [];

		for($i = 0; $i <= 488; $i++)
			$ret[] = 0;

		$ret[0] = $guildID;
		$ret[1] = 1000;
		$ret[3] = 1;
		$ret[5] = 10;
		$ret[6] = 6553600;
		$ret[12] = time();
		$ret[13] = 100;
		$ret[14] = $playerID;
		$ret[64] = $playerLvl;
		$ret[114] = time();
		$ret[214] = 1000;
		$ret[314] = 1;




		return join('/', $ret);
	}

	public static function getGuildBuildingCost($current_lvl) {
		$current_lvl++;
		$ret = ['mushroom' => max(0, ($current_lvl - 25) * 5), 'silver' => 0];

		switch ($current_lvl) {
			case 1:
				$ret['silver'] = 500;
				break;
			case 2:
				$ret['silver'] = 900;
				break;
			case 3:
				$ret['silver'] = 1500;
				break;
			case 4:
				$ret['silver'] = 2200;
				break;
			case 5:
				$ret['silver'] = 3200;
				break;
			case 6:
				$ret['silver'] = 4500;
				break;
			case 7:
				$ret['silver'] = 6000;
				break;
			case 8:
				$ret['silver'] = 7800;
				break;
			case 9:
				$ret['silver'] = 10100;
				break;
			case 10:
				$ret['silver'] = 12800;
				break;
			case 11:
				$ret['silver'] = 16000;
				break;
			case 12:
				$ret['silver'] = 19700;
				break;
			case 13:
				$ret['silver'] = 24000;
				break;
			case 14:
				$ret['silver'] = 29100;
				break;
			case 15:
				$ret['silver'] = 34800;
				break;
			case 16:
				$ret['silver'] = 41200;
				break;
			case 17:
				$ret['silver'] = 48700;
				break;
			case 18:
				$ret['silver'] = 57000;
				break;
			case 19:
				$ret['silver'] = 66400;
				break;
			case 20:
				$ret['silver'] = 77000;
				break;
			case 21:
				$ret['silver'] = 88800;
				break;
			case 22:
				$ret['silver'] = 101800;
				break;
			case 23:
				$ret['silver'] = 116400;
				break;
			case 24:
				$ret['silver'] = 132500;
				break;
			case 25:
				$ret['silver'] = 150200;
				break;
			case 26:
				$ret['silver'] = 169900;
				break;
			case 27:
				$ret['silver'] = 191400;
				break;
			case 28:
				$ret['silver'] = 214900;
				break;
			case 29:
				$ret['silver'] = 240800;
				break;
			case 30:
				$ret['silver'] = 269000;
				break;
			case 31:
				$ret['silver'] = 299600;
				break;
			case 32:
				$ret['silver'] = 333000;
				break;
			case 33:
				$ret['silver'] = 369200;
				break;
			case 34:
				$ret['silver'] = 408300;
				break;
			case 35:
				$ret['silver'] = 450900;
				break;
			case 36:
				$ret['silver'] = 496800;
				break;
			case 37:
				$ret['silver'] = 546100;
				break;
			case 38:
				$ret['silver'] = 599600;
				break;
			case 39:
				$ret['silver'] = 656900;
				break;
			case 40:
				$ret['silver'] = 718400;
				break;
			case 41:
				$ret['silver'] = 784700;
				break;
			case 42:
				$ret['silver'] = 855700;
				break;
			case 43:
				$ret['silver'] = 931500;
				break;
			case 44:
				$ret['silver'] = 1012900;
				break;
			case 45:
				$ret['silver'] = 1099700;
				break;
			case 46:
				$ret['silver'] = 1192200;
				break;
			case 47:
				$ret['silver'] = 1291200;
				break;
			case 48:
				$ret['silver'] = 1396500;
				break;
			case 49:
				$ret['silver'] = 1508200;
				break;
			case 50:
				$ret['silver'] = 1627700;
				break;
		}
		return $ret;
	}

	public static function getAttackCost($baseLvl){
		return $baseLvl * 1000;
	}
	public static function getRaidCost($currentLvl){
		switch($currentLvl){
			case 0: return 10526100;
			case 1: return 22662600;
			case 2: return 36566100;
			case 3: return 52383600;
			case 4: return 70275000;
			case 5: return 75372000;
			case 6: return 80754000;
			case 7: return 86431500;
			case 8: return 92451000;
			case 9: return 98794500;
			case 10: return 105477000;
			case 11: return 112546500;
			case 12: return 119983500;
			case 13: return 127803000;
			case 14: return 136060500;
			case 15: return 144730500;
			case 16: return 153829500;
			case 17: return 163425000;
			case 18: return 173487000;
			case 19: return 184032000;
			case 20: return 195129000;
			case 21: return 206748000;
			case 22: return 218908500;
			case 23: return 231690000;
			case 24: return 245053500;
			case 25: return 259021500;
			case 26: return 273682500;
			case 27: return 288994500;
			case 28: return 304798500;
			case 29: return 321729000;
			case 30: return 339204000;
			case 31: return 357427500;
			case 32: return 376503000;
			case 33: return 396379500;
			case 34: return 417084000;
			case 35: return 438735000;
			case 36: return 461272500;
			case 37: return 484725000;
			case 38: return 509215500;
			case 39: return 534684000;
			case 40: return 561162000;
			case 41: return 588786000;
			case 42: return 617487000;
			case 43: return 647298000;
			case 44: return 678364500;
			case 45: return 710631000;
			case 46: return 744084000;
			case 47: return 778929000;
			case 48: return 815070000;
			case 49: return 852549000;
		}
	}
}

  // "groupsave" :
  // [
  // 0"groupid",
  // 1"gold",
  // 2"coins.w",
  // 2"lastdevilrefreshday.w",
  // 3"membercount",
  // 4"building0.w",
  // 4"enemylifepart1.w", monsterHpPRC * 65536
  // 5"building1.w",
  // 5"enemylifepart2.w",
  // 6"building2.w",
  // 6"enemylifepercent.w",
  // 7"building3.w",
  // 7"devildungeonlevel.w",
  // 8"dungeonlevel",
  // 9"nextattackdungeon",
  //10"attackdeclarer",
  //11"leaderlastcheck",
  //12"leaderlastlogin",
  //13"honor",
  //14"memberid(50)",
  //64"memberlevel(50)",
 //114"memberonlinestatus(50)",
 //164"memberlastfightdate(50)",
 //214"membergoldspend(50)",
 //264"memberpilzspend(50)",
 //314"membergrouprank(50)",
 //364"attacktarget",
  //65"attacktime",
  //66"defensetarget",
  //67"defensetime",
  //68"eventtriggercount",
  //69"lastautosavetime",
  //  "free(119)"
  // ],


?>
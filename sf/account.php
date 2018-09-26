<?php

//players own account
class Account extends Player{

	// public $fortress;

	//order by class
	public $copycats = [];

	public $backpack = [];
	public $fortressBackpack = [];
	public $shops = [];
	//display based on slot
	public $copycatEquip = [[], [], []];

	public $album;
	
	public function Account($playerData = null, $items = null, $copycats = false, $loadAlbum = false){

		if($playerData === null){
			$qry = $GLOBALS['db']->query('SELECT players.*, fortress.*, guilds.portal AS guild_portal, guilds.instructor, guilds.treasure, guilds.dungeon as raid, guilds.name AS gname FROM players LEFT JOIN fortress ON players.ID = fortress.owner LEFT JOIN guilds ON players.guild = guilds.ID WHERE players.ID = '.$GLOBALS['playerID'].' ORDER BY honor DESC');
			// $qry->bindParam(':ssid', $GLOBALS['ssid']);
			// $qry->execute();
			$playerData = $qry->fetch ( PDO::FETCH_ASSOC );
		}

		if($items === null){
			$items = $GLOBALS['db']->query("SELECT * FROM items WHERE owner = ".$playerData['ID']." ORDER BY slot ASC");
			$items = $items->fetchAll(PDO::FETCH_ASSOC);
		}

		if($copycats === true){
			$copycats = $GLOBALS['db']->query("SELECT * FROM copycats WHERE owner = ".$playerData['ID']." ORDER BY class ASC");
			$copycats = $copycats->fetchAll(PDO::FETCH_ASSOC);
		}

		parent::Player($playerData, $items);

		if($copycats)
			for($i = 0; $i < 3; $i++){
				$this->copycats[] = new Copycat($copycats[$i], $this->copycatEquip[$i]);
			}

		//fortress
		$lvls = [];
		for($i = 0; $i < 12; $i++)
			$lvls[] = $playerData['b'.$i];

		// $this->fortress = new Fortress($lvls);

		// $this->tower = new Tower();

		//album
		if($loadAlbum && $playerData['album'] >= 0)
			$this->album = new Album($playerData['album_data'], $playerData['album']);
	}

	public function getPlayerSave(){
		$ret = [];
		for($i = 0; $i < 611; $i++)
			$ret[] = 0;
		$ret[611] = "";



		//player ID
		$ret[0] = 1545570852;
		$ret[1] = $this->data['ID'];
		$ret[2] = time();
		$ret[3] = 1311714336;
		$ret[4] = 1126959955;
		//message count?
		$ret[5] = 0;


		//exp
		$ret[8] = $this->data['exp'];
		$ret[9] = Player::exp[$this->data['lvl'] - 1];

		// $SF_STATUS = 45; // 2 quest /1work /0none
		$ret[45] = $this->data['status'];
		// picked quest, needs testing
		$ret[46] = $this->data['status_extra'];
		$ret[47] = $this->data['status_time'];

		// $ret[45] = 2;
		// $ret[46] = 1;
		// $ret[47] = time() + 600;

		//guild bonus
		$ret[461] = $this->getGuildBonus('instructor');
		$ret[462] = $this->getGuildBonus('treasure');

		$expBonus = 100 + $ret[461] / 1000;
		$goldBonus = 100 + $ret[462] / 100000000;

		// $SF_QUEST_DURATION_1 = 241;
		$ret[241] = $this->data['quest_dur1'];
		$ret[242] = $this->data['quest_dur2'];
		$ret[243] = $this->data['quest_dur3'];

		// $SF_QUEST_DESC_1 = 232; 1-15 range, just get the remainder of raw exp 
		$ret[232] = ($this->data['quest_exp1'] % 16) + 1;
		$ret[233] = ($this->data['quest_exp2'] % 16) + 1;
		$ret[234] = ($this->data['quest_exp3'] % 16) + 1;


		//quest background, max 21, just do modulo 21 of something
		$ret[238] = $this->data['quest_exp1'] % 22;
		$ret[239] = $this->data['quest_exp2'] % 22;
		$ret[240] = $this->data['quest_exp3'] % 22;

		// $SF_QUEST_EXP_1 = 280;
		$alb = $this->getAlbumMultiplier();
		$ret[280] = round($this->data['quest_exp1'] * $alb * $expBonus);
		$ret[281] = round($this->data['quest_exp2'] * $alb * $expBonus);
		$ret[282] = round($this->data['quest_exp3'] * $alb * $expBonus);

		// $SF_QUEST_GOLD_1 = 283;
		$twr = 1 + ($this->data['tower'] / 1000);
		$ret[283] = round($this->data['quest_silver1'] * $goldBonus * $twr);
		$ret[284] = round($this->data['quest_silver2'] * $goldBonus * $twr);
		$ret[285] = round($this->data['quest_silver3'] * $goldBonus * $twr);

		// $SF_QUEST_REWARD_1_TYPE = 244;
		// $SF_QUEST_REWARD_2_TYPE = 256;
		// $SF_QUEST_REWARD_3_TYPE = 268;

		$face = explode(",", $this->data['face']);	
		for($i = 0; $i < 9; $i++)
			$ret[17 + $i] = $face[$i];

		$ret[27] = $this->data['race'] + 50 * 65536;
		$ret[28] = $this->data['gender'];
		$ret[29] = $this->data['class'] + ($this->data['portal_time'] * 65536);

		$ret[7] = $this->data['lvl'];

		//portal hp
		if($this->data['portal'] < 50){
			$portalMaxHp = Monster::getPortalMonster($this->data['portal'] + 1)->hp;

			//class rank?? + portal hp procent
			$ret[12] = 0 + round($this->data['portal_hp'] / $portalMaxHp * 100)  * 65536;
		}

		//silver
		$ret[13] = $this->data['silver'];

		//mush
		$ret[14] = $this->data['mush'];

		//shrooms may donate
		$ret[437] = $this->data['mush'];

		//honor
		$ret[10] = $this->data['honor'];

		//rank 
		// $ret[11] = $this->data['rank'];


		//album + 10 000
		if($this->hasAlbum())
			$ret[438] = 10000 + $this->data['album'];


		$stats = $this->baseStats;
		$equipStats = $this->getEquipStats();

		$ret[30] = $stats['str'];
		$ret[31] = $stats['dex'];
		$ret[32] = $stats['int'];
		$ret[33] = $stats['wit'];
		$ret[34] = $stats['luck'];
		$ret[35] = $equipStats['str'];
		$ret[36] = $equipStats['dex'];
		$ret[37] = $equipStats['int'];
		$ret[38] = $equipStats['wit'];
		$ret[39] = $equipStats['luck'];
		$ret[40] = $stats['str'] - 10;
		$ret[41] = $stats['dex'] - 10;
		$ret[42] = $stats['int'] - 10;
		$ret[43] = $stats['wit'] - 10;
		$ret[44] = $stats['luck'] - 10;

		//backpack 168
		foreach($this->backpack as $item){
			$save = $item->getSave();
			$slot = ($item->slot * 12) + 168;
			for($i = 0; $i < 12; $i++){
				$ret[$slot + $i] = $save[$i];
			}
		}

		//equip 48
		foreach($this->equip as $item){
			$save = $item->getSave();
			$slot = ($item->slot * 12) + 48;
			for($i = 0; $i < 12; $i++){
				$ret[$slot + $i] = $save[$i];
			}
		}

		//dmg min max, clients counts shit
		if($weapon = $this->getWeapon()){
			$ret[448] = $weapon->raw['dmg_min'];
			$ret[449] = $weapon->raw['dmg_max'];
		}else{
			$ret[448] = 1;
			$ret[449] = 2;
		}

		//shops
		foreach($this->shops as $item){
			$save = $item->getSave();
			$slot = ($item->slot * 12) + 288;
			//360 is what?? skip
			if($slot >= 360)
				$slot++;
			for($i = 0; $i < 12; $i++){
				$ret[$slot + $i] = $save[$i];
			}
		}

		//dungeon end time
		$ret[459] = $this->data['dungeon_time'];

		//ARENA END TIME 460
		$ret[460] = $this->data['arena_time'];


		//thirst
		$ret[456] = $this->data['thirst'];

		$ret[457] = $this->data['beers'];

		//potions
		for($i = 1; $i <= 3; $i++){
			if($this->data['potion_dur'.$i] > time()){
				$ret[492 + $i] = $this->data['potion_type'.$i];
				$ret[495 + $i] = $this->data['potion_dur'.$i];
				$ret[498 + $i] = 25;
				if($this->data['potion_type'.$i] < 16){
					$stat = ($ret[492 + $i] - 1) % 5;
					$ret[35 + $stat] += ceil(($ret[30 + $stat] + $ret[35 + $stat]) * 0.25) + 1;
				}
			}
		}

		//dungeons index 480
		for($i = 1; $i <= 12; $i++)
			$ret[479 + $i] = $this->data['d'.$i];

		//dung 11, 12, 13, 14
		$ret[441] = $this->data['d11'];
		$ret[442] = $this->data['d12'];
		$ret[490] = $this->data['d13'] + 120; // +120 bo chuj
		$ret[546] = $this->data['d14'];;


		//tower + mount added bellow
		$ret[286] = $this->data['tower'] * 65536;
		// $ret[286] = 0;

		$ret[287] = time() - (3600 * 24 * 3);


		// for($i = 0; $i < 3; $i++){
		// 	$d1 = $this->data['dd'.(($i*4)+1)];
		// 	$d2 = $this->data['dd'.(($i*4)+2)];
		// 	$d3 = $this->data['dd'.(($i*4)+3)];
		// 	$d4 = $this->data['dd'.(($i*4)+4)];
		// 	$ret[604 + $i] = $this->getShadowDungSave($d1, $d2, $d3, $d4);	
		// }
		$ret[604] = $this->getShadowDungSave($this->data['dd1'], $this->data['dd2'], $this->data['dd3'], $this->data['dd4']);
		$ret[605] = $this->getShadowDungSave($this->data['dd5'], $this->data['dd6'], $this->data['dd7'], $this->data['dd8']);
		$ret[606] = $this->getShadowDungSave($this->data['dd9'], $this->data['dd10'], $this->data['dd11'], $this->data['dd12']);
		$ret[607] = $this->getShadowDungSave($this->data['dd13'], $this->data['dd14'], 0, 0);

		// $ret[607] = $this->getShadowDungSave($this->data['dd13'], $this->data['dd14'], 0, 0);



		//mount
		if($this->data['mount_time'] > time() && $this->data['mount'] > 0){
			$ret[286] += $this->data['mount'];
			$ret[451] = $this->data['mount_time'];

			$mountMultiplier = [0.9, 0.8, 0.7, 0.5][$this->data['mount'] - 1];

			//adjust quest duration
			$ret[241] = ceil($this->data['quest_dur1'] * $mountMultiplier);
			$ret[242] = ceil($this->data['quest_dur2'] * $mountMultiplier);
			$ret[243] = ceil($this->data['quest_dur3'] * $mountMultiplier);
			// $ret[241] *= $mountMultiplier;
			// $ret[242] *= $mountMultiplier;
			// $ret[243] *= $mountMultiplier;
		}

		//fortress buildings lvl index 524
		// $fort = $this->fortress->lvls;
		// for($i = 0; $i < 12; $i++)
		// 	$ret[524 + $i] = $fort[$i];
		for($i = 0; $i < 12; $i++)
			$ret[524 + $i] = $this->data['b'.$i];

		#$ret[493] = 9;
		//fortress backpack size
		$ret[516] = $this->data['b9'];

		//required lvl for upgrades fuck knows why
		for($i = 1; $i <= 3; $i++)
			$ret[587 + $i] = $this->data["ul$i"] - 1;

		//stone & wood
		$ret[544] = $this->data['wood'];
		$ret[545] = $this->data['stone'];

		//army -> warrior, scout, mage
		$ret[547] = $this->data['u1'] + $this->data['u2'] * 65536;
		$ret[548] = $this->data['u3'] + 65536 * $this->data['ut1'];
		$ret[549] = $this->data['ut2'] + 65536 * $this->data['ut3'];

		for($i = 1; $i <= 3; $i++){
			if($this->data["ut$i"] > 0){
				$ret[549 + $i] = $this->data["uttime$i"];
				$ret[552 + $i] = $this->data["uttime$i"] + 600;
			}
		}

		// // ----fortressunitsbuildstarttime(3)
		// $ret[550] = $this->data['uttime1'];
		// $ret[551] = $this->data['uttime2'];
		// $ret[552] = $this->data['uttime3'];
		// // ----fortressunitsbuildfinishtime(3)
		// $ret[553] = $this->data['uttime1'];
		// $ret[554] = $this->data['uttime2'];
		// $ret[555] = $this->data['uttime3'];

		//UNITS BUILD = floor(time elapsed / build time), then modulo to get current progress
		
		//max resources in buildings
		$ret[565] = Fortress::getMaxResources(1, $this->data['b2']);
		$ret[566] = Fortress::getMaxResources(2, $this->data['b3']);
		$ret[567] = Fortress::getMaxResources(3, $this->data['b5'], $this->data['lvl']);
		
		//max resources globaly
		$ret[568] = Fortress::getGlobalMaxResources(1, $this->data['b0']);
		$ret[569] = Fortress::getGlobalMaxResources(2, $this->data['b0']);

		//budowanie, building id, endtime, starttime
		$ret[571] = $this->data['build_id'];
		$ret[572] = $this->data['build_end'];
		$ret[573] = $this->data['build_start'];

		//resources per hour
		$ret[574] = Fortress::getResourcesPerHour(1, $this->data['b2']);
		$ret[575] = Fortress::getResourcesPerHour(2, $this->data['b3']);
		$ret[576] = Fortress::getResourcesPerHour(3, $this->data['b5']);

		//last gather time
		$ret[577] = max($this->data['gather1'], $this->data['gather2'], $this->data['gather3']);

		//current resources in buildings -> wood, stone, exp, works together with 577
		//count resources gathered since ret[577]
		$ret[562] = floor(($ret[577] - $this->data['gather1']) * (Fortress::getResourcesPerHour(1, $this->data['b2']) / 3600));
		$ret[563] = floor(($ret[577] - $this->data['gather2']) * (Fortress::getResourcesPerHour(2, $this->data['b3']) / 3600));
		$ret[564] = floor(($ret[577] - $this->data['gather3']) * (Fortress::getResourcesPerHour(3, $this->data['b5']) / 3600));

		// $ret[562] = floor(( $this->data['gather1'] - $ret[577] ) * (Fortress::getResourcesPerHour(1, $this->data['b2']) / 3600));
		// $ret[563] = floor(( $this->data['gather2'] - $ret[577] ) * (Fortress::getResourcesPerHour(2, $this->data['b3']) / 3600));
		// $ret[564] = floor(( $this->data['gather3'] - $ret[577] ) * (Fortress::getResourcesPerHour(3, $this->data['b5']) / 3600));





		//kopanie klejnotow, 13, endtime, starttime
		if($this->data['dig_start'] > 0){
			$ret[594] = 13;
			$ret[595] = $this->data['dig_end'];
			$ret[596] = $this->data['dig_start'];
		}



		//email valid
		$ret[463] = 1;

		//dafuq is this
		// $ret[464] = 1;

		//witch cost
		$ret[519] = 1;

		//portal lvls and bonuses | (portal * 256 + guild_portal) * 65536
		// $portal = is_numeric($this->data['guild_portal']) ? $this->data['guild_portal'] : 0;
		$ret[445] = (($this->data['portal']) * 256 + $this->data['guild_portal']) * 65536;


		//guild and rank
		$ret[435] = $this->data['guild'];
		if($this->data['guild'] > 0)
			$ret[436] = $this->data['guild_rank'];


		//questrerolltime
		// $ret[228] = 1435802478;

		
		//new messags temp 
		$ret[434] = isset($this->data['new_msg']) ? $this->data['new_msg'] : 0;

		//guild attacks
		$ret[508] = $this->data['guild_fight'];
		$ret[509] = $this->data['event_trigger_count'];


		//tutorial skip
		$ret[597] = 16777215;

		//hall of knights
		$ret[598] = $this->data['hok'];
		
		//arena enemies
		$ret[599] = $this->data['arena_nme1'];
		$ret[600] = $this->data['arena_nme2'];
		$ret[601] = $this->data['arena_nme3'];
		//REROLL TIME FOR ARENA, client calls for reroll
		$ret[602] = 1956232814;


		//WC
		$ret[491] = $this->data['wcaura'];
		$ret[492] = $this->data['wcexp'];
		$ret[515] = Account::getWcRequiredExp($this->data['wcaura']);

		
		//fortress enemy time
		// $ret[586] = time() + 5;

		//fortress enemy id
		// $ret[587] = 513;


		return join("/", $ret);
	}

	public function getTowerSave(){





		$ret = $this->data['ID']."/0/".$this->data['tower']."/";

		foreach($this->copycats as $copycat){
			$ret .= $copycat->getSave()."/";
		}

		$ret .= "0/0/0/0/0/0/0/0/0/0/0/0/0/0/0/0/0/0/0/0/0/0/0/0/0/0/0/0/0";

		return $ret;

		// $ret = [];
		// for($i = 0; $i < 477; $i++)
		// 	$ret[] = 0;

		// $ret[476] = "";

		// $ret[0] = 57985;
		// $ret[1] = 1138596;
		// //lvl? bylo 81
		// $ret[2] = $this->data['tower'];

		// $ret[447] = 0;
		// $ret[448] = 0;
		// $ret[449] = 0;
		// $ret[450] = 0;
		// $ret[451] = 0;
		// $ret[452] = 0;
		// $ret[453] = 0;
		// $ret[454] = 0;
		// $ret[455] = 0;
		// $ret[456] = 0;
		// $ret[457] = 0;
		// $ret[458] = 0;
		// $ret[459] = 0;
		// $ret[460] = 0;
		// $ret[461] = 0;
		// $ret[462] = 0;
		// $ret[463] = 0;
		// $ret[464] = 0;
		// $ret[465] = 0;
		// $ret[466] = 0;
		// $ret[467] = 0;
		// $ret[468] = 0;
		// $ret[469] = 0;
		// $ret[470] = 0;
		// $ret[471] = 0;
		// $ret[472] = 0;
		// $ret[473] = 0;
		// $ret[474] = 0;
		// $ret[475] = 0;

		// return join("/", $ret);
	}

	public function getFortressPriceSave(){
		$ret = [];

		//upgrade prices, dig for gems price last
		//time/gold/wood/stone
		for($i = 0; $i < 12; $i++)
			foreach(Fortress::getUpgradePrice($i, $this->data['b'.$i]) as $s)
				$ret[] = $s;

		// for($i = 0; $i < 12; $i++){
		// 	$price = Fortress::getUpgradePriceNew($i, $this->data['b'.$i]);
		// 	$ret[] = round($price[0] * (1.0 - $this->data['b1'] * 0.05));
		// 	$ret[] = $price[1];
		// 	$ret[] = $price[2];
		// 	$ret[] = $price[3];
		// }

		$gemSearchPrice = Fortress::getSearchGemPrice();
		foreach ($gemSearchPrice as $x) {
			$ret[] = $x;
		}

		return join("/", $ret);
	}

	public function getTrainUnitsPrice(){
		//time/silver/wood/stone
		$ret = [];

		for($i = 1; $i <= 3; $i++)
			foreach(Fortress::getUnitTrainPrice($i, $this->data["ul$i"]) as $a)
				$ret[] = $a;

		return join('/', $ret);
		// return "600/0/15/5/900/0/11/6/600/0/19/4/";
	}

	public function getUpgradeUnitsPrice(){
		//next lvl, wood, stone
		$ret = [];

		for($i = 1; $i <= 3; $i++){
			$ret[] = Fortress::getUnitLvl($this->data["ul$i"] + 1);
			$price = Fortress::getUnitUpgradePrice($i, $this->data["ul$i"]);
			$ret[] = $price[0];
			$ret[] = $price[1];
		}
		
		return join('/', $ret);
	}

	public function getUnitLvls(){
		$ret = [];

		$ret[] = Fortress::getUnitLvl($this->data['b11']);

		for($i = 1; $i <= 3; $i++)
			$ret[] = Fortress::getUnitLvl($this->data["ul$i"]);

		return join('/', $ret);
	}

	public function getHallOfKnightsPriceSave(){
		return join('/', Fortress::getHallOfKnightsPrice($this->data['hok']));
	}

	public function rerollShop($shop){

		if($this->data['mush'] < 1)
			exit("Error:need more coins");

		$GLOBALS['db']->exec("UPDATE players SET mush = mush - 1 WHERE ID = ".$this->data['ID']);
		$this->data['mush'] --;

		$qry = [];


		$shop1 = ($shop - 1) * 6;
		for($i = $shop1; $i < $shop1 + 6; $i++){
			if($shop == "1")
				$type = rand(1, 7);
			else
				$type = rand(8, 11);

			//potion
			if($type == 11)
				$type = 12;

			$item = Item::genItem($type, $this->lvl, $this->class, 10);
			$slot = 20 + $i;

			$qry[] = "UPDATE items SET type = ".$item['type'].", item_id = ".$item['item_id'].", dmg_min = ".$item['dmg_min'].", dmg_max = ".$item['dmg_max'].", a1 = ".$item['a1'].", a2 = ".$item['a2'].", a3 = ".$item['a3'].", a4 = ".$item['a4'].", a5 = ".$item['a5'].", a6 = ".$item['a6'].", value_silver = ".$item['value_silver'].", value_mush = ".$item['value_mush']." WHERE owner = ".$this->data['ID']." AND slot = $slot";

			//this is enought to display the items correctly
			$this->shops[$i]->raw = $item;
		}

		$GLOBALS['db']->exec(join(';', $qry));
	}

	//TODO: check fortress backpack size, if not exceeding
	public function moveItem($args){
		$sourceSlot = Item::getItemSlot($args[0], $args[1]);
		$sourceItem = $this->getItemAtSlot($args[0], $sourceSlot);

		//if there are any items in that slot already, type if target slot -1	
		$targetSlot = Item::getItemSlot($args[2], $args[3], $sourceItem->type);
		$targetItem = $this->getItemAtSlot($args[2], $targetSlot);

		//moving between equip, backpack and fortress backpack
		if(($args[0] == 2 || $args[0] == 5 || $args[0]  == 1) && ($args[2] == 2 || $args[2] == 5 || $args[2] == 1)){
			//on equiping/using items 
			if($args[2] == 1 || $args[0] == 1){
				//check if equiping items that are not equipable
				if($targetItem){
					if($sourceItem->type <= 10 && $targetItem->type > 10)
						exit("Error:class cannot use this item");
					if($sourceItem->type <= 10 && $sourceItem->type != $targetItem->type)
						exit("Error:item does not belong here");
					if($targetItem->type <= 7 && $targetItem->forClass != $this->class)
						exit("Error:class cannot use this item");
				}

				//check if item for your class
				if($sourceItem->type <= 7){
					if($sourceItem->forClass != $this->class)
						exit("Error:class cannot use this item");
				}else if($sourceItem->type > 10){ 
					$this->useItem($args, $sourceItem, $targetItem);
					return;
				}
			}

			//see if not moving to an additional slot that player does not have yet
			if($args[2] == 5 && $args[3] > $this->getFortressBackpackSize())
				exit('Error:target item not found');

			$sourceItem->move($args[2], $args[3], $targetSlot);
			//remove item from array, add to target array
			$this->removeItem($args[0], $sourceItem);
			$this->addItem($args[2], $sourceItem);

			if($targetItem){
				//$GLOBALS['db']->exec("UPDATE items SET slot = ".$sourceSlot." WHERE ID = ".$targetItem->getID());
				$targetItem->move($args[0], $args[1], $sourceSlot);
				$this->removeItem($args[2], $targetItem);
				$this->addItem($args[0], $targetItem);
			}
		}else if($args[0] == 3 || $args[0] == 4){ 
			//buying items, if source = shops

			//check if not putting item in non active fortress backpack slot
			if($args[2] == 5 && $args[3] > $this->getFortressBackpackSize())
				exit('Error:target item not found');

			//check price
			if($sourceItem->cost > $this->data['silver'])
				exit("Error:need more gold");
			if($sourceItem->costMush > $this->data['mush'])
				exit("Error:need more coins");

			if($sourceItem->type > 10 && $args[2] == 1)
				$this->useItem($args, $sourceItem, $targetItem);

			//if there is item in target slot, and not using potion - exit
			// else if($targetItem)// && $sourceItem->type != 12)
			// 	exit("Success:");

			//temporary potion fix
			// if($sourceItem->type > 10 && ($args[2] != 2 && $args[2] != 5))
			// 	exit('Success:');
			else{
				if($targetItem){
					if(($freeslot = $this->getFreeBackpackSlot()) === false)
						exit('Error:no backspace free');
					// exit("Error:$freeslot");
					$this->removeItem($args[2], $targetItem);
					if($freeslot >= 100){
						$targetItem->move(5, $freeslot - 99, $freeslot);
						$this->addItem(5, $targetItem);
					}else{
						$targetItem->move(2, $freeslot + 1, $freeslot);
						$this->addItem(2, $targetItem);
					}
				}

				//item->buy, updates the item
				$sourceItem->buy($args[2], $args[3], $targetSlot);
				$this->removeItem($args[0], $sourceItem);
				$this->addItem($args[2], $sourceItem);
			}
				//generate new item, insert into db
			if($args[0] == "3"){
				$type = rand(1, 7);
			}else{
				$type = rand(8, 11);
				//potion
				if($type == 11)
					$type = 12;
			}


			$newItem = Item::genItem($type, $this->lvl, $this->class);
			$newItem['slot'] = 20 + (($args[0] - 3) * 6) + $args[1] - 1;
			$newItemObj = new Item($newItem);
			$this->addItem($args[0], $newItemObj);


			$GLOBALS['db']->exec("INSERT INTO items(owner, slot, type, item_id, dmg_min, dmg_max, a1, a2, a3, a4, a5, a6, value_silver, value_mush) 
				VALUES(".$this->data['ID'].", $newItem[slot], $newItem[type], $newItem[item_id], $newItem[dmg_min], $newItem[dmg_max], 
					$newItem[a1], $newItem[a2], $newItem[a3], $newItem[a4], $newItem[a5], $newItem[a6], $newItem[value_silver], $newItem[value_mush])");

			//pay up bitch
			$GLOBALS['db']->exec("UPDATE players SET silver = silver - $sourceItem->cost, mush = mush - $sourceItem->costMush WHERE ID = ".$this->data['ID']);

			$this->data['silver'] -= $sourceItem->cost;
			$this->data['mush'] -= $sourceItem->costMush;

			//album add
			if($this->hasAlbum() && $sourceItem->type <= 10){
				$this->album = new Album($this->data['album_data'], $this->data['album']);
				if($this->album->addItem($sourceItem)){
					$this->album->encode();
					$GLOBALS['db']->exec("UPDATE players SET album_data = '".$this->album->data."', album = ".$this->album->count." WHERE ID = ".$this->data['ID']);
					$GLOBALS['ret'][] = "scrapbook.r:".$this->album->data;
					$this->data['album'] = $this->album->count;
				}
			}
		}else if($args[2] == 3 || $args[2] == 4){
			//selling items

			// $valS = $sourceItem->cost;


			//DELETE item from db
			$GLOBALS['db']->exec("DELETE FROM items WHERE ID = ".$sourceItem->raw['ID']);

			//give value to player
			$GLOBALS['db']->exec("UPDATE players SET silver = silver + ".$sourceItem->cost.", mush = mush + ".$sourceItem->costMush." WHERE ID = ".$this->data['ID']);

			//for display data
			$this->data['silver'] +=  $sourceItem->cost;
			$this->data['mush'] += $sourceItem->costMush;

			$this->removeItem($args[0], $sourceItem);
		}else if($args[0] > 100 || $args[2] > 100){ 
			//equiping/unequiping on copycats

			//if both items are set, check they are for the same class and of same type
			// var_dump($targetSlot);
			if($targetItem){
				if($sourceItem->type == 15){
					if($targetItem->gemSlot){
						$targetItem->setGem($sourceItem);
						$this->removeItem($args[0], $sourceItem);
						$GLOBALS['ret'][] = "owntower.towerSave:".$this->getTowerSave();
						return;
					}else
						exit('Error:item has no socket');
				}
				if($sourceItem->type != $targetItem->type)
					exit("Error:target item not found");
				if($sourceItem->type <= 7 && $targetItem->type <= 7 && $sourceItem->forClass != $targetItem->forClass)
					exit("Error:class cannot use this item");
			}

			//if not equipable, shields included, copycat can't have shield
			if($sourceItem->type > 10 || $sourceItem->type == 2)
				exit("Error:class cannot use this item");

			//if target to equip the item, if not for target class
			if($args[2] > 100 && $sourceItem->type <= 7 && $sourceItem->forClass != $this->copycats[$args[2]-101]->class)
				exit("Error:class cannot use this item");


			$sourceItem->move($args[2], $args[3], $targetSlot);
			//remove item from array, add to target array
			$this->removeItem($args[0], $sourceItem);
			$this->addItem($args[2], $sourceItem);

			if($targetItem){
				//$GLOBALS['db']->exec("UPDATE items SET slot = ".$sourceSlot." WHERE ID = ".$targetItem->getID());
				$targetItem->move($args[0], $args[1], $sourceSlot);
				$this->removeItem($args[2], $targetItem);
				$this->addItem($args[0], $targetItem);
			}


			$GLOBALS['ret'][] = "owntower.towerSave:".$this->getTowerSave();
		}else if($args[2] == 12){
			//spooling items in the toilet

			//only from equip
			if($args[0] != 2 && $args[0] != 5)
				exit('Error:');

			if($sourceItem->raw['value_silver'] <= 0)
				exit("Error:items cannot be flushed twice");
			
			if($sourceItem->type <= 10)
				$sourceItem->flush();
			else{
				$GLOBALS['db']->exec("DELETE FROM items WHERE ID = ".$sourceItem->raw['ID']);
			}

			if(!$this->toiletFullToday()){
				$reqExp = Account::getWcRequiredExp($this->data['wcaura']);
				$this->data['wcexp'] += 25;
				//this is in case you want some items to add more exp than others
				if($this->data['wcexp'] > $this->data['wcexp'])
					$this->data['wcexp'] = $reqExp;
				$GLOBALS['db']->exec("UPDATE players SET wcexp = ".$this->data['wcexp'].", wcdate = ".time()." WHERE ID = ".$this->data['ID']);
				$GLOBALS['ret'][] = "toilettfull:1";
			}
		}
	}

	public function useItem($args, $sourceItem, $targetItem){
		switch($sourceItem->type){
			case 11:
				//dungeon key, do nothing
				break;
			case 12:
				//elixirs

				for($i = 1; $i <= 3; $i++){
					if($this->data["potion_dur$i"] < time()){
						//if there is no potion in this slot (if expired basicaly)
						$this->data["potion_dur$i"] = $sourceItem->raw['a4'] * 3600 + time();
						$this->data["potion_type$i"] = $sourceItem->raw['item_id'];

						$GLOBALS['db']->exec("UPDATE players SET potion_dur$i = ".$this->data["potion_dur$i"].", potion_type$i = ".$this->data["potion_type$i"]." WHERE ID = ".$this->data['ID']);

						$GLOBALS['db']->exec('DELETE FROM items WHERE ID = '.$sourceItem->raw['ID']);
						$this->removeItem($args[0], $sourceItem);

						break 2;
					}else if($this->data["potion_type$i"] == $sourceItem->raw['item_id']){
						$this->data["potion_dur$i"] += $sourceItem->raw['a4'] * 3600;

						$GLOBALS['db']->exec("UPDATE players SET potion_dur$i = ".$this->data["potion_dur$i"]." WHERE ID = ".$this->data['ID']);

						$GLOBALS['db']->exec('DELETE FROM items WHERE ID = '.$sourceItem->raw['ID']);
						$this->removeItem($args[0], $sourceItem);

						break 2;
					}
				}

				exit("Error:no potion downgrade");

				break;
			case 13:
				//klaser
				$this->album = new Album(Album::getDefaultData(), 0);

				foreach($this->equip as $item)
					$this->album->addItem($item);

				foreach($this->backpack as $item)
					$this->album->addItem($item);

				foreach($this->fortressBackpack as $item)
					$this->album->addItem($item);

				foreach($this->copycatEquip as $cceq)
					foreach($cceq as $item)
						$this->album->addItem($item);

				$this->album->encode();
				$this->data['album'] = $this->album->count;
				$this->removeItem($args[0], $sourceItem);

				$GLOBALS['ret'][] = 'scrapbook.r:'.$this->album->data;

				$GLOBALS['db']->exec('DELETE FROM items WHERE ID = '.$sourceItem->raw['ID']);
				$GLOBALS['db']->exec('UPDATE players SET album = '.$this->album->count.', album_data = "'.$this->album->data.'" WHERE ID = '.$this->data['ID']);
				break;
			case 15:
				//gems
				if($targetItem){
					if($targetItem->gemSlot){
						$targetItem->setGem($sourceItem);
						$this->removeItem($args[0], $sourceItem);
					}else{
						exit('Error:item has no socket');
					}
				}
				break;
			default:
				break;
		}
	}

	public function getItemAtSlot($source, $rawSlot){
		//sources
		//equip = 1 ?
		//backpack = 2, 
		//fortress backpack = 5;
		//shops = 3/4
		switch($source){
			case 1:
				//moved to Item::getItemSlot
				//if($type > 0)
				//	$rawSlot = Item::getItemSlot($source, $rawSlot, $type);

				foreach($this->equip as $item)
					if($rawSlot == $item->raw['slot'])
						return $item;
				break;
			case 2:
				foreach($this->backpack as $item)
					if($rawSlot == $item->raw['slot'])
						return $item;
				break;
			case 3:
			case 4:
				foreach($this->shops as $item)
					if($rawSlot == $item->raw['slot'])
						return $item;
				break;
			case 5:
				foreach($this->fortressBackpack as $item)
					if($rawSlot == $item->raw['slot'])
						return $item;
				break;
			case 101:
			case 102:
			case 103:
				$source-=101;
				foreach($this->copycats[$source]->equip as $item)
					if($rawSlot == $item->raw['slot'])
						return $item;
				break;
		}

		return null;
	}

	//used in moveItem
	private function addItem($target, $item){
		switch($target){
			case 1:
				$this->equip[] = $item;
				break;
			case 2:
				$this->backpack[] = $item;
				break;
			case 3:
			case 4:
				$this->shops[] = $item;
				break;
			case 5:
				$this->fortressBackpack[] = $item;
				break;
			case 101:
			case 102:
			case 103:
				$target-=101;
				$this->copycatEquip[$target][] = $item;
				$this->copycats[$target]->equip[] = $item;
				break;
		}
	}

	//used in moveItem
	private function removeItem($source, $item){
		switch($source){
			case 1:
				for($i = 0; $i < count($this->equip); $i++){
					if($this->equip[$i]->raw['ID'] == $item->raw['ID'])
						unset($this->equip[$i]);
				}
				break;
			case 2:
				for($i = 0; $i < count($this->backpack); $i++){
					if($this->backpack[$i]->raw['ID'] == $item->raw['ID']){
						unset($this->backpack[$i]);
						$this->backpack = array_values($this->backpack);
					}
				}
				break;
			case 3:
			case 4:
				for($i = 0; $i < count($this->shops); $i++){
					if($this->shops[$i]->raw['ID'] == $item->raw['ID']){
						unset($this->shops[$i]);
						$this->shops = array_values($this->shops);
					}
				}
				break;
			case 5:
				for($i = 0; $i < count($this->fortressBackpack); $i++){
					if($this->fortressBackpack[$i]->raw['ID'] == $item->raw['ID']){
						unset($this->fortressBackpack[$i]);
						$this->fortressBackpack = array_values($this->fortressBackpack);
					}
				}
				break;
			case 101:
			case 102:
			case 103:
				$source-=101;
				for($i = 0; $i < count($this->copycats[$source]->equip); $i++){
					if($this->copycats[$source]->equip[$i]->raw['ID'] == $item->raw['ID']){
						// unset($this->copycatEquip[$source][$i]);
						// $this->copycatEquip[$source] = array_values($this->copycatEquip);

						unset($this->copycats[$source]->equip[$i]);
						$this->copycats[$source]->equip = array_values($this->copycats[$source]->equip);
					}
				}

				break;
		}
	}

	//inserts item into a free slot, adds to db
	//params: items - raw values for db, 
	//slot - if have target slot, if you checked wether or not there is a free slot before using this function 
	//returns item object
	public function insertItem($item, $slot = false){

		if($slot === false)
			$item['slot'] = $this->getFreeBackpackSlot();
		else
			$item['slot'] = $slot;

		$itemObject = new Item($item);

		if($item['slot'] < 5)
			$this->backpack[] = $itemObject;
		else{
			$this->fortressBackpack[] = $itemObject;
			$GLOBALS['ret'][] = "fortresschest.item(".$this->getFortressBackpackSize()."):".$this->getFortressBackpackSave();
		}

		$GLOBALS['db']->exec("INSERT INTO items(owner, slot, type, item_id, dmg_max, dmg_min, a1, a2, a3, a4, a5, a6, value_silver, value_mush)
			VALUES(".$this->data['ID'].", $item[slot], $item[type], $item[item_id], $item[dmg_max], $item[dmg_min], $item[a1], $item[a2], $item[a3], $item[a4], $item[a5], $item[a6], $item[value_silver], $item[value_mush])");


		return $itemObject;
	}


	//returns raw backpack slot
	public function getFreeBackpackSlot(){

		$bool = [true, true, true, true, true];

		//look at backpack first
		$size = count($this->backpack);
		if($size < 5){
			foreach($this->backpack as $item)
				$bool[$item->slot] = false;
			for($i = 0; $i < 5; $i++)
				if($bool[$i] == true)
					return $i;
		}

		//if nothing found, look at fortress backpack
		$size = $this->getFortressBackpackSize();
		$arSize = count($this->fortressBackpack);
		if($size > 0 && $arSize < $size){
			$bool = [];
			for($i = 0; $i < $size; $i++)
				$bool[] = true;
			foreach($this->fortressBackpack as $item)
				$bool[$item->slot] = false;
			for($i = 0; $i < $size; $i++)
				if($bool[$i] == true)
					return $i + 100;
		}

		return false;
	}



	//lvling up displaying and all that shit
	public function addExp($exp){
		$this->data['exp'] += $exp;
		// $expReq = Player::exp[$this->data['lvl'] - 1];
		while($this->data['exp'] > Player::exp[$this->data['lvl'] - 1]){
			$this->data['exp'] -= Player::exp[$this->data['lvl'] - 1];
			$this->data['lvl']++;
		}
	}

	public function questStart($quest){
		//TODO: check status and thirst

		

		if($this->data['mount_time'] > time())
			$mountMultiplier = [0.9, 0.8, 0.7, 0.5][$this->data['mount'] - 1];
		else
			$mountMultiplier = 1;

		if($this->data["quest_dur$quest"] * $mountMultiplier > $this->data['thirst'])
			exit('Error:need more adventurelust');

		$this->data['status'] = 2;
		$this->data['status_extra'] = $quest;
		$this->data['status_time'] = time() + ceil($this->data['quest_dur'.$quest] * $mountMultiplier);

		$GLOBALS['db']->exec("UPDATE players SET status = 2, status_extra = ".$quest.", status_time = ".$this->data['status_time']." WHERE ID = ".$this->data['ID']);
	}

	public function questStop(){

		$this->data['status'] = 0;
		$this->data['status_extra'] = 0;
		$this->data['status_time'] = 0;

		$GLOBALS['db']->exec("UPDATE players SET status = 0, status_extra = 0, status_time = 0 WHERE ID = ".$this->data['ID']);
	}

	public function questFinish($win, $monsterID){

		$ret = [];
		for($i = 0; $i < 21; $i++)
			$ret[] = 0;

		$qryArgs = [];

		//picked quest
		$pickedQuest = $this->data['status_extra'];

		//thirst
		$mountMultiplier = $this->data['mount_time'] > time() ? [0.9, 0.8, 0.7, 0.5][$this->data['mount'] - 1] : 1;
		$this->data['thirst'] -= ceil($this->data['quest_dur'.$pickedQuest] * $mountMultiplier);
		$qryArgs[] = "thirst = ".$this->data['thirst'];

		//skip cost
		if($this->data['status_time'] > time()){
			$this->data['mush']--;
			$qryArgs[] = 'mush = mush - 1';
		}

		//status reset
		$this->data['status'] = 0;
		$this->data['status_extra'] = 0;
		$this->data['status_time'] = 0;

		$qryArgs[] = "status = 0, status_extra = 0, status_time = 0";

		if($win){
			$ret[0] = 1;

			//silver
			$ret[2] = round($this->data['quest_silver'.$pickedQuest] * (1 + $this->data['tower'] / 100) * (1 + $this->getGuildBonus('treasure') / 100));
			$this->data['silver'] += $ret[2];

			//exp
			$ret[3] = round($this->data['quest_exp'.$pickedQuest] * $this->getAlbumMultiplier() * (1 + $this->getGuildBonus('instructor') / 100));
			$this->addExp($ret[3]);

			//honor
			$ret[5] = 10;
			$this->data['honor'] += 10;

			//shrooms gained
			if(rand(0, 3) == 3){
				$this->data['mush']++;
				$qryArgs[] = 'mush = mush + 1';
			}

			//honor
			$ret[5] = 10;
			$this->data['honor'] += 10;

			$qryArgs[] = 'silver = '.$this->data['silver'];
			$qryArgs[] = 'exp = '.$this->data['exp'];
			$qryArgs[] = 'lvl = '.$this->data['lvl'];
			$qryArgs[] = 'honor = honor + 10';


			//items starts at 9
			// $ret[9] = 9;

			//add monster to scrapbook
			if($this->hasAlbum() && $this->album->addMonster($monsterID)){
				$this->album->encode();
				$this->data['album']++;
				$GLOBALS['ret'][] = "scrapbook.r:".$this->album->data;
				$qryArgs[] = "album = ".$this->data['album'];
				$qryArgs[] = "album_data = '".$this->album->data."'";
			}
		}


		//generate new quests
		for($i = 1; $i <= 3; $i++){
			$quest = Account::generateQuest($this->data['lvl']);

			//not sure about this
			// if($this->data['thirst'] > 0 && $quest['duration'] > $this->data['thirst'])
			// 	$quest['duration'] = $this->data['thirst'];

			$qryArgs[] = "quest_exp$i = ".$quest['exp'];
			$qryArgs[] = "quest_silver$i = ".$quest['silver'];
			$qryArgs[] = "quest_dur$i = ".$quest['duration'];

			$this->data["quest_exp$i"] = $quest['exp'];
			$this->data["quest_silver$i"] = $quest['silver'];
			$this->data["quest_dur$i"] = $quest['duration'];
			// var_dump($quest);
		}



		$GLOBALS['db']->exec("UPDATE players SET ".join(", ", $qryArgs)." WHERE ID = ".$this->data['ID']);

		return join("/", $ret)."/";
	}

	public function fortressBuild($id){

		//time/silver/wood/stone
		$price = Fortress::getUpgradePriceNew($id, $this->data['b'.$id]);

		//check if enough resources, if not exit(), no errors, client doesnt allow to build if not enough anyway
		if($price[1] > $this->data['silver'] || $price[2] > $this->data['wood'] || $price[3] > $this->data['stone'])
			exit();

		$this->data['silver'] -= $price[1];
		$this->data['wood'] -= $price[2];
		$this->data['stone'] -= $price[3];

		$GLOBALS['db']->exec("UPDATE players SET silver = silver - $price[1] WHERE ID = ".$this->data['ID'].";UPDATE fortress SET wood = wood - $price[2], stone = stone - $price[3] WHERE owner = ".$this->data['ID']);

		//update db and display variables
		$this->data['build_id'] = $id + 1;
		$this->data['build_start'] = time();
		$this->data['build_end'] = $this->data['build_start'] + $price[0];

		//TODO: decrease resources in this query, another query for gold
		$GLOBALS['db']->query("UPDATE fortress SET build_id = ".$this->data['build_id'].", build_start = ".$this->data['build_start'].", build_end = ".$this->data['build_end']." WHERE owner = ".$this->data['ID']);
	}

	public function fortressBuildStop(){

		$id = $this->data['build_id'] - 1;

		//decrease the returned resources
		$price = Fortress::getUpgradePrice($id, $this->data['b'.$id]);


		$this->data['build_id'] = 0;
		$this->data['build_start'] = 0;
		$this->data['build_end'] = 0;


		$GLOBALS['db']->query("UPDATE fortress SET build_id = 0, build_start = 0, build_end = 0 WHERE owner = ".$this->data['owner']);
	}

	public function fortressDigStart(){

		$price = Fortress::getSearchGemPrice();

		$this->data['dig_start'] = time();
		$this->data['dig_end'] = time() + $price[0];

		$GLOBALS['db']->exec("UPDATE fortress SET dig_start = ".$this->data['dig_start'].", dig_end = ".$this->data['dig_end']." WHERE owner = ".$this->data['ID']);
	}

	public function fortressDigStop(){
		$this->data['dig_start'] = 0;
		$this->data['dig_end'] = 0;

		$GLOBALS['db']->exec("UPDATE fortress SET dig_start = 0, dig_end = 0 WHERE owner = ".$this->data['ID']);
	}

	public function fortressDigFinish(){

		$mushcost = 0;
		if($this->data['dig_end'] > time())
			$mushcost = ceil(($this->data['dig_end'] - time()) / 600);

		if($mushcost > $this->data['mush'])
			exit("Error:need more coins");

		$this->data['dig_start'] = 0;
		$this->data['dig_end'] = 0;

		$GLOBALS['db']->exec("UPDATE fortress SET dig_start = 0, dig_end = 0 WHERE owner = ".$this->data['ID']);

		if($mushcost > 0){
			$this->data['mush'] -= $mushcost;
			$GLOBALS['db']->exec("UPDATE players SET mush = mush - ".$mushcost." WHERE ID = ".$this->data['ID']);
		}
	}

	public function fortressBuildFinish(){

		$mushcost = 0;
		if($this->data['build_end'] > time())
			$mushcost = ceil(($this->data['build_end'] - time()) / 600);

		if($mushcost > $this->data['mush'])
			exit("Error:need more coins");

		$id = $this->data['build_id'] - 1;
		$this->data['b'.$id] ++;

		$this->data['build_id'] = 0;
		$this->data['build_end'] = 0;
		$this->data['build_start'] = 0;

		$GLOBALS['db']->exec("UPDATE fortress SET b$id = ".$this->data["b$id"].", build_id = 0, build_end = 0, build_start = 0 WHERE owner = ".$this->data['ID']);

		//if finishing first lvl of mines, set gather time to timestamp
		if($id == 2 || $id == 3 || $id == 5){
			if($this->data["b$id"] == 1){
				$gather = $id < 4 ? 'gather'.($id - 1) : 'gather3';

				$this->data[$gather] = time();

				// var_dump("UPDATE fortress SET $gather = ".$this->data[$gather]." WHERE owner = ".$this->data['ID']);

				$GLOBALS['db']->exec("UPDATE fortress SET $gather = ".$this->data[$gather]." WHERE owner = ".$this->data['ID']);
			}
		}

		if($mushcost > 0){
			$this->data['mush'] -= $mushcost;
			$GLOBALS['db']->exec("UPDATE players SET mush = mush - ".$mushcost." WHERE ID = ".$this->data['ID']);
		}
	}

	public function fortressGather($building){

		if($building == 3){
			$time = time();

			$gain = floor(($time - $this->data['gather'.$building]) * (Fortress::getResourcesPerHour(3, $this->data['b5']) / 3600));

			$this->data['gather3'] = $time;
			$this->addExp($gain);

			$GLOBALS['db']->exec("UPDATE players SET exp = ".$this->data['exp'].", lvl = ".$this->data['lvl']." WHERE ID = ".$this->data['ID']);
			$GLOBALS['db']->exec("UPDATE fortress SET gather3 = $time WHERE owner = ".$this->data['ID']);
		}else{
			$resource = $building == 1 ? "wood" : "stone";

			$time = time();

			//count gain
			$gainMax = Fortress::getMaxResources($building, $this->data['b'.($building + 1)]);
			$gain = min($gainMax, floor(($time - $this->data['gather'.$building]) * (Fortress::getResourcesPerHour($building, $this->data['b'.($building + 1)]) / 3600)));

			//see if not going over fortress cap
			$gainMaxGlobal = Fortress::getGlobalMaxResources($building, $this->data['b0']);
			if($this->data[$resource] + $gain > $gainMaxGlobal){
				// $gain = $gainMaxGlobal - $this->data[$resource];

				//count out the gather time, time - leftovers / res/min 
				$time -= round((($this->data[$resource] + $gain) - $gainMaxGlobal) / (Fortress::getResourcesPerHour($building, $this->data['b'.($building + 1)]) / 3600));

				$this->data[$resource] = $gainMaxGlobal;
				$this->data['gather'.$building] =  $time;
			}else{
				$this->data['gather'.$building] = $time;
				$this->data[$resource] += $gain;
			}



			$GLOBALS['db']->exec("UPDATE fortress SET gather$building = $time, $resource = ".$this->data[$resource]." WHERE owner = ".$this->data['ID']);
		}
	}

	public function fortressUnitUpgrade($unit){
		$price = Fortress::getUnitUpgradePrice($unit, $this->data["ul$unit"]);

		if($price[0] > $this->data['wood'] || $price[1] > $this->data['stone'])
			exit();

		$this->data['wood'] -= $price[0];
		$this->data['stone'] -= $price[1];

		$this->data["ul$unit"] ++;

		$GLOBALS['db']->exec("UPDATE fortress SET wood = wood - $price[0], stone = stone - $price[1], ul$unit = ul$unit + 1 WHERE owner = ".$this->data['ID']);
	}

	public function fortressUnitTrain($unit, $quantity){
		//time/gold(0)/wood/stone
		$price = Fortress::getUnitTrainPrice($unit, $this->data["ul$unit"]);

		$price[2] *= $quantity;
		$price[3] *= $quantity;

		if($price[2] > $this->data['wood'] || $price[3] > $this->data['stone'])
			exit();

		$this->data['wood'] -= $price[2];
		$this->data['stone'] -= $price[3];
		$this->data["ut$unit"] += $quantity;
		if($this->data["uttime$unit"] == 0)
			$this->data["uttime$unit"] = time();


		$GLOBALS['db']->exec("UPDATE fortress SET wood = wood - $price[2], stone = stone - $price[3], ut$unit = ut$unit + $quantity, uttime$unit = ".$this->data["uttime$unit"]." WHERE owner = ".$this->data['ID']);
	}

	//saveData for shadow world dungeon
	private function getShadowDungSave($d1, $d2, $d3, $d4){
		return  ($d4 * 16843008) + ($d1)  + (256 * ($d2 - $d4)) + (65536 * ($d3 - $d4));
	}

	public function getFortressBackpackSize(){
		return $this->data['b9'];
	}

	public function getFortressBackpackSave(){
		$ret = [];

		for($i = 0; $i < $this->getFortressBackpackSize() * 12; $i++){
				$ret[] = 0;
		}

		foreach($this->fortressBackpack as $item){
			$save = $item->getSave();
			$slot = $item->slot * 12;
			for($i = 0; $i < 12; $i++)
				$ret[$slot + $i] = $save[$i];
		}
		
		return join("/", $ret);
	}

	public function hasTower(){
		return $this->data['tower'] >= 0;
	}

	public function hasGuild(){
		return $this->data['guild'] > 0;
	}

	private function getAlbumMultiplier(){
		if($this->data['album'] > 0)
			return 1 + round($this->data['album'] / 2022, 2);
		return 1;
	}

	private function getGuildBonus($building){
		//TODO: add dungeon bonus
		if($this->data['guild'] > 0)
			return ($this->data[$building] + $this->data['raid']) * 2;
		return 1;
	}

	public function toiletFullToday(){
		//if not same day or been more than 24 hours
		if((time() / 86400) % 365 != ($this->data['wcdate'] / 86400) % 365 || time() - $this->data['wcdate'] > 86400)
			return 0;
		return 1;
	}

	public function toiletFull(){
		return Account::getWcRequiredExp($this->data['wcaura']) >= $this->data['wcexp'];
	}

	public static function getWagesPerHour($lvl){
		return 9 + round(pow($lvl, 2.9));
	}

	//returns an array with 3 values: exp, silver, duration
	public static function generateQuest($lvl){
		//random duration 5, 10, 15, 20 min
		$duration = rand(1,4);

		$exp = floor(Player::exp[$lvl - 1] / (2 + ($lvl * 0.05)) * (0.85 + mt_rand() / mt_getrandmax() * 0.3)) * $duration;

		$silver = rand(5000, 6500) * $duration * $lvl;

		return ['exp' => $exp, 'silver' => $silver, 'duration' => $duration * 300];
	}

	public static function getStatPrice($stat){
		if($stat > 3151)
			return 1000000000;
		$stats = [25, 30, 35, 40, 45, 50, 55, 60, 65, 70, 75, 75, 80, 85, 90, 95, 95, 100, 105, 110, 115, 120, 125, 135, 140, 150, 155, 160, 170, 175, 185, 190, 200, 205, 215, 225, 230, 240, 250, 260, 270, 280, 290, 300, 310, 325, 335, 345, 355, 365, 380, 390, 405, 420, 435, 450, 460, 475, 490, 505, 520, 535, 550, 565, 580, 600, 615, 630, 650, 665, 685, 705, 725, 745, 765, 785, 805, 825, 845, 865, 885, 910, 935, 960, 985, 1010, 1035, 1060, 1085, 1110, 1135, 1160, 1190, 1220, 1250, 1280, 1310, 1340, 1370, 1400, 1430, 1460, 1495, 1530, 1565, 1600, 1630, 1665, 1700, 1735, 1770, 1810, 1850, 1890, 1930, 1975, 2015, 2055, 2095, 2135, 2180, 2225, 2270, 2315, 2360, 2405, 2450, 2500, 2545, 2595, 2645, 2695, 2750, 2800, 2855, 2910, 2960, 3015, 3065, 3120, 3175, 3235, 3295, 3355, 3415, 3480, 3540, 3600, 3660, 3720, 3785, 3850, 3920, 3985, 4055, 4125, 4195, 4265, 4335, 4405, 4480, 4555, 4635, 4710, 4790, 4870, 4945, 5025, 5100, 5180, 5260, 5345, 5435, 5525, 5615, 5705, 5790, 5880, 5970, 6060, 6150, 6245, 6345, 6440, 6540, 6640, 6740, 6840, 6940, 7040, 7145, 7255, 7365, 7475, 7585, 7700, 7810, 7920, 8030, 8140, 8255, 8380, 8505, 8630, 8755, 8880, 9005, 9130, 9255, 9380, 9505, 9640, 9775, 9910, 10045, 10185, 10325, 10465, 10605, 10745, 10885, 11035, 11185, 11340, 11490, 11645, 11795, 11945, 12100, 12250, 12405, 12575, 12745, 12915, 13085, 13255, 13425, 13595, 13765, 13935, 14105, 14285, 14470, 14655, 14840, 15025, 15210, 15400, 15590, 15780, 15970, 16170, 16375, 16580, 16785, 16990, 17190, 17395, 17600, 17805, 18010, 18235, 18460, 18685, 18910, 19140, 19365, 19590, 19815, 20040, 20270, 20510, 20755, 21000, 21245, 21490, 21740, 21990, 22240, 22490, 22740, 23005, 23275, 23540, 23810, 24080, 24345, 24615, 24880, 25150, 25420, 25715, 26010, 26305, 26600, 26900, 27195, 27490, 27785, 28080, 28380, 28695, 29010, 29325, 29640, 29960, 30280, 30605, 30925, 31250, 31575, 31920, 32265, 32610, 32955, 33305, 33650, 33995, 34340, 34685, 35035, 35410, 35790, 36165, 36545, 36925, 37300, 37680, 38055, 38435, 38815, 39215, 39620, 40025, 40430, 40835, 41245, 41660, 42070, 42485, 42900, 43335, 43775, 44215, 44655, 45095, 45530, 45970, 46410, 46850, 47290, 47765, 48245, 48720, 49200, 49680, 50155, 50635, 51110, 51590, 52070, 52575, 53085, 53590, 54100, 54610, 55125, 55645, 56165, 56685, 57205, 57755, 58305, 58855, 59405, 59960, 60510, 61060, 61610, 62160, 62715, 63310, 63905, 64500, 65095, 65695, 66290, 66885, 67480, 68075, 68675, 69305, 69940, 70570, 71205, 71840, 72485, 73130, 73775, 74420, 75065, 75745, 76425, 77110, 77790, 78475, 79155, 79835, 80520, 81200, 81885, 82620, 83355, 84095, 84830, 85570, 86305, 87040, 87780, 88515, 89255, 90030, 90810, 91590, 92370, 93150, 93940, 94730, 95525, 96315, 97110, 97945, 98780, 99620, 100455, 101295, 102130, 102965, 103805, 104640, 105480, 106375, 107275, 108175, 109075, 109975, 110870, 111770, 112670, 113570, 114470, 115420, 116370, 117320, 118270, 119220, 120185, 121150, 122115, 123080, 124045, 125060, 126075, 127090, 128105, 129125, 130140, 131155, 132170, 133185, 134205, 135290, 136380, 137470, 138560, 139650, 140735, 141825, 142915, 144005, 145095, 146240, 147385, 148530, 149675, 150825, 151985, 153150, 154315, 155480, 156645, 157870, 159095, 160320, 161545, 162770, 163995, 165220, 166445, 167670, 168895, 170200, 171510, 172815, 174125, 175435, 176740, 178050, 179355, 180665, 181975, 183350, 184725, 186100, 187475, 188855, 190250, 191645, 193040, 194435, 195830, 197290, 198755, 200215, 201680, 203145, 204605, 206070, 207530, 208995, 210460, 212020, 213580, 215140, 216700, 218265, 219825, 221385, 222945, 224505, 226070, 227705, 229340, 230975, 232610, 234250, 235905, 237565, 239220, 240880, 242540, 244280, 246020, 247760, 249500, 251240, 252980, 254720, 256460, 258200, 259940, 261785, 263635, 265480, 267330, 269180, 271025, 272875, 274720, 276570, 278420, 280355, 282290, 284230, 286165, 288105, 290065, 292025, 293990, 295950, 297915, 299965, 302015, 304065, 306115, 308170, 310220, 312270, 314320, 316370, 318425, 320600, 322780, 324955, 327135, 329315, 331490, 333670, 335845, 338025, 340205, 342480, 344755, 347035, 349310, 351590, 353890, 356195, 358500, 360805, 363110, 365515, 367925, 370335, 372745, 375155, 377560, 379970, 382380, 384790, 387200, 389745, 392295, 394845, 397395, 399945, 402490, 405040, 407590, 410140, 412690, 415350, 418015, 420680, 423345, 426010, 428705, 431400, 434095, 436790, 439485, 442295, 445105, 447915, 450725, 453535, 456345, 459155, 461965, 464775, 467585, 470555, 473525, 476495, 479465, 482435, 485405, 488375, 491345, 494315, 497285, 500380, 503475, 506570, 509665, 512765, 515895, 519025, 522155, 525285, 528420, 531685, 534950, 538215, 541480, 544750, 548015, 551280, 554545, 557810, 561080, 564520, 567960, 571405, 574845, 578290, 581730, 585170, 588615, 592055, 595500, 599085, 602675, 606260, 609850, 613440, 617065, 620690, 624315, 627940, 631570, 635340, 639110, 642885, 646655, 650430, 654200, 657970, 661745, 665515, 669290, 673260, 677235, 681210, 685185, 689160, 693130, 697105, 701080, 705055, 709030, 713160, 717295, 721425, 725560, 729695, 733870, 738045, 742220, 746395, 750575, 754920, 759265, 763610, 767955, 772300, 776645, 780990, 785335, 789680, 794025, 798585, 803150, 807715, 812280, 816845, 821405, 825970, 830535, 835100, 839665, 844410, 849160, 853905, 858655, 863405, 868200, 872995, 877790, 882585, 887380, 892355, 897335, 902315, 907295, 912275, 917250, 922230, 927210, 932190, 937170, 942395, 947625, 952855, 958085, 963315, 968540, 973770, 979000, 984230, 989460, 994885, 1000310, 1005740, 1011165, 1016595, 1022070, 1027550, 1033025, 1038505, 1043985, 1049670, 1055360, 1061050, 1066740, 1072430, 1078115, 1083805, 1089495, 1095185, 1100875, 1106835, 1112795, 1118755, 1124715, 1130680, 1136640, 1142600, 1148560, 1154520, 1160485, 1166670, 1172860, 1179045, 1185235, 1191425, 1197670, 1203915, 1210160, 1216405, 1222650, 1229120, 1235590, 1242065, 1248535, 1255010, 1261480, 1267950, 1274425, 1280895, 1287370, 1294145, 1300925, 1307705, 1314485, 1321265, 1328040, 1334820, 1341600, 1348380, 1355160, 1362180, 1369205, 1376230, 1383255, 1390280, 1397365, 1404450, 1411540, 1418625, 1425715, 1433060, 1440405, 1447755, 1455100, 1462450, 1469795, 1477140, 1484490, 1491835, 1499185, 1506860, 1514540, 1522215, 1529895, 1537575, 1545250, 1552930, 1560605, 1568285, 1575965, 1583920, 1591875, 1599835, 1607790, 1615750, 1623770, 1631795, 1639820, 1647845, 1655870, 1664170, 1672475, 1680775, 1689080, 1697385, 1705685, 1713990, 1722290, 1730595, 1738900, 1747575, 1756250, 1764925, 1773600, 1782280, 1790955, 1799630, 1808305, 1816980, 1825660, 1834635, 1843610, 1852585, 1861560, 1870540, 1879590, 1888640, 1897690, 1906740, 1915790, 1925155, 1934520, 1943890, 1953255, 1962625, 1971990, 1981355, 1990725, 2000090, 2009460, 2019225, 2028990, 2038760, 2048525, 2058295, 2068060, 2077825, 2087595, 2097360, 2107130, 2117235, 2127340, 2137445, 2147550, 2157660, 2167845, 2178030, 2188215, 2198400, 2208590, 2219115, 2229640, 2240165, 2250690, 2261215, 2271740, 2282265, 2292790, 2303315, 2313840, 2324815, 2335790, 2346765, 2357740, 2368715, 2379690, 2390665, 2401640, 2412615, 2423590, 2434925, 2446265, 2457600, 2468940, 2480280, 2491700, 2503125, 2514550, 2525975, 2537400, 2549205, 2561010, 2572815, 2584620, 2596430, 2608235, 2620040, 2631845, 2643650, 2655460, 2667745, 2680035, 2692325, 2704615, 2716905, 2729190, 2741480, 2753770, 2766060, 2778350, 2791045, 2803740, 2816440, 2829135, 2841835, 2854625, 2867415, 2880205, 2892995, 2905790, 2918990, 2932190, 2945390, 2958590, 2971790, 2984990, 2998190, 3011390, 3024590, 3037790, 3051525, 3065260, 3079000, 3092735, 3106475, 3120210, 3133945, 3147685, 3161420, 3175160, 3189330, 3203505, 3217675, 3231850, 3246025, 3260300, 3274575, 3288850, 3303125, 3317405, 3332140, 3346875, 3361610, 3376345, 3391080, 3405815, 3420550, 3435285, 3450020, 3464755, 3480060, 3495370, 3510680, 3525990, 3541300, 3556605, 3571915, 3587225, 3602535, 3617845, 3633640, 3649435, 3665235, 3681030, 3696830, 3712735, 3728640, 3744550, 3760455, 3776365, 3792760, 3809155, 3825550, 3841945, 3858340, 3874735, 3891130, 3907525, 3923920, 3940315, 3957350, 3974385, 3991420, 4008455, 4025490, 4042525, 4059560, 4076595, 4093630, 4110665, 4128220, 4145775, 4163330, 4180885, 4198440, 4216110, 4233785, 4251460, 4269135, 4286810, 4305030, 4323250, 4341470, 4359690, 4377910, 4396130, 4414350, 4432570, 4450790, 4469010, 4487905, 4506805, 4525705, 4544605, 4563505, 4582400, 4601300, 4620200, 4639100, 4658000, 4677475, 4696955, 4716435, 4735915, 4755395, 4775000, 4794610, 4814220, 4833830, 4853440, 4873625, 4893815, 4914005, 4934195, 4954385, 4974570, 4994760, 5014950, 5035140, 5055330, 5076270, 5097215, 5118155, 5139100, 5160045, 5180985, 5201930, 5222870, 5243815, 5264760, 5286315, 5307875, 5329435, 5350995, 5372555, 5394250, 5415945, 5437640, 5459335, 5481035, 5503375, 5525715, 5548060, 5570400, 5592745, 5615085, 5637425, 5659770, 5682110, 5704455, 5727600, 5750745, 5773890, 5797035, 5820180, 5843325, 5866470, 5889615, 5912760, 5935905, 5959730, 5983560, 6007390, 6031220, 6055050, 6079025, 6103005, 6126980, 6150960, 6174940, 6199600, 6224260, 6248920, 6273580, 6298245, 6322905, 6347565, 6372225, 6396885, 6421550, 6447095, 6472640, 6498185, 6523730, 6549280, 6574825, 6600370, 6625915, 6651460, 6677010, 6703280, 6729550, 6755825, 6782095, 6808370, 6834800, 6861235, 6887665, 6914100, 6940535, 6967725, 6994915, 7022110, 7049300, 7076495, 7103685, 7130875, 7158070, 7185260, 7212455, 7240585, 7268715, 7296845, 7324975, 7353105, 7381235, 7409365, 7437495, 7465625, 7493755, 7522685, 7551620, 7580550, 7609485, 7638420, 7667520, 7696625, 7725730, 7754835, 7783940, 7813845, 7843750, 7873660, 7903565, 7933475, 7963380, 7993285, 8023195, 8053100, 8083010, 8113950, 8144890, 8175830, 8206770, 8237715, 8268655, 8299595, 8330535, 8361475, 8392420, 8424210, 8456000, 8487790, 8519580, 8551375, 8583350, 8615325, 8647300, 8679275, 8711250, 8744110, 8776975, 8809835, 8842700, 8875565, 8908425, 8941290, 8974150, 9007015, 9039880, 9073835, 9107790, 9141745, 9175700, 9209660, 9243615, 9277570, 9311525, 9345480, 9379440, 9414330, 9449225, 9484115, 9519010, 9553905, 9588995, 9624085, 9659175, 9694265, 9729360, 9765385, 9801410, 9837440, 9873465, 9909495, 9945520, 9981545, 10017575, 10053600, 10089630, 10126855, 10164080, 10201305, 10238530, 10275760, 10312985, 10350210, 10387435, 10424660, 10461890, 10500105, 10538320, 10576535, 10614750, 10652970, 10691395, 10729820, 10768245, 10806670, 10845100, 10884555, 10924015, 10963470, 11002930, 11042390, 11081845, 11121305, 11160760, 11200220, 11239680, 11280405, 11321130, 11361855, 11402580, 11443310, 11484035, 11524760, 11565485, 11606210, 11646940, 11688755, 11730570, 11772385, 11814200, 11856015, 11898055, 11940095, 11982135, 12024175, 12066215, 12109340, 12152470, 12195595, 12238725, 12281855, 12324980, 12368110, 12411235, 12454365, 12497495, 12542005, 12586520, 12631030, 12675545, 12720060, 12764570, 12809085, 12853595, 12898110, 12942625, 12988285, 13033945, 13079605, 13125265, 13170930, 13216830, 13262730, 13308635, 13354535, 13400440, 13447535, 13494635, 13541730, 13588830, 13635930, 13683025, 13730125, 13777220, 13824320, 13871420, 13919980, 13968540, 14017100, 14065660, 14114220, 14162780, 14211340, 14259900, 14308460, 14357020, 14406835, 14456655, 14506470, 14556290, 14606110, 14656180, 14706250, 14756325, 14806395, 14856470, 14907795, 14959125, 15010455, 15061785, 15113115, 15164440, 15215770, 15267100, 15318430, 15369760, 15422685, 15475610, 15528535, 15581460, 15634390, 15687315, 15740240, 15793165, 15846090, 15899020, 15953270, 16007520, 16061770, 16116020, 16170275, 16224795, 16279320, 16333840, 16388365, 16442890, 16498790, 16554690, 16610590, 16666490, 16722390, 16778290, 16834190, 16890090, 16945990, 17001890, 17059465, 17117045, 17174625, 17232205, 17289785, 17347360, 17404940, 17462520, 17520100, 17577680, 17636705, 17695730, 17754760, 17813785, 17872815, 17932130, 17991450, 18050765, 18110085, 18169405, 18230170, 18290935, 18351705, 18412470, 18473240, 18534005, 18594770, 18655540, 18716305, 18777075, 18839665, 18902260, 18964855, 19027450, 19090045, 19152635, 19215230, 19277825, 19340420, 19403015, 19467130, 19531245, 19595360, 19659475, 19723595, 19788015, 19852435, 19916860, 19981280, 20045705, 20111705, 20177710, 20243715, 20309720, 20375725, 20441725, 20507730, 20573735, 20639740, 20705745, 20773675, 20841605, 20909535, 20977465, 21045395, 21113325, 21181255, 21249185, 21317115, 21385045, 21454630, 21524220, 21593810, 21663400, 21732990, 21802905, 21872820, 21942735, 22012650, 22082570, 22154145, 22225720, 22297295, 22368870, 22440450, 22512025, 22583600, 22655175, 22726750, 22798330, 22871990, 22945655, 23019315, 23092980, 23166645, 23240305, 23313970, 23387630, 23461295, 23534960, 23610365, 23685770, 23761175, 23836580, 23911990, 23987745, 24063500, 24139255, 24215010, 24290765, 24368325, 24445890, 24523455, 24601020, 24678585, 24756145, 24833710, 24911275, 24988840, 25066405, 25146160, 25225915, 25305670, 25385425, 25465185, 25544940, 25624695, 25704450, 25784205, 25863965, 25945615, 26027265, 26108920, 26190570, 26272225, 26354240, 26436260, 26518275, 26600295, 26682315, 26766225, 26850140, 26934055, 27017970, 27101885, 27185795, 27269710, 27353625, 27437540, 27521455, 27607745, 27694035, 27780325, 27866615, 27952910, 28039200, 28125490, 28211780, 28298070, 28384365, 28472640, 28560920, 28649195, 28737475, 28825755, 28914420, 29003090, 29091760, 29180430, 29269100, 29359830, 29450560, 29541290, 29632020, 29722755, 29813485, 29904215, 29994945, 30085675, 30176410, 30269630, 30362850, 30456075, 30549295, 30642520, 30735740, 30828960, 30922185, 31015405, 31108630, 31204005, 31299385, 31394765, 31490145, 31585525, 31681315, 31777110, 31872905, 31968700, 32064495, 32162445, 32260395, 32358345, 32456295, 32554250, 32652200, 32750150, 32848100, 32946050, 33044005, 33144650, 33245295, 33345940, 33446585, 33547235, 33647880, 33748525, 33849170, 33949815, 34050465, 34153370, 34256275, 34359180, 34462085, 34564995, 34668335, 34771675, 34875015, 34978355, 35081700, 35187380, 35293060, 35398745, 35504425, 35610110, 35715790, 35821470, 35927155, 36032835, 36138520, 36247025, 36355530, 36464035, 36572540, 36681050, 36789555, 36898060, 37006565, 37115070, 37223580, 37334535, 37445490, 37556445, 37667400, 37778355, 37889770, 38001185, 38112605, 38224020, 38335440, 38449305, 38563170, 38677035, 38790900, 38904770, 39018635, 39132500, 39246365, 39360230, 39474100, 39591010, 39707920, 39824830, 39941740, 40058650, 40175560, 40292470, 40409380, 40526290, 40643200, 40762665, 40882135, 41001605, 41121075, 41240545, 41360500, 41480460, 41600420, 41720380, 41840340, 41962950, 42085560, 42208175, 42330785, 42453400, 42576010, 42698620, 42821235, 42943845, 43066460, 43192255, 43318055, 43443855, 43569655, 43695455, 43821250, 43947050, 44072850, 44198650, 44324450, 44453015, 44581580, 44710150, 44838715, 44967285, 45096365, 45225445, 45354525, 45483605, 45612685, 45744530, 45876375, 46008225, 46140070, 46271920, 46403765, 46535610, 46667460, 46799305, 46931155, 47066435, 47201720, 47337005, 47472290, 47607575, 47742855, 47878140, 48013425, 48148710, 48283995, 48422170, 48560345, 48698520, 48836695, 48974875, 49113595, 49252315, 49391035, 49529755, 49668475, 49810185, 49951895, 50093610, 50235320, 50377035, 50518745, 50660455, 50802170, 50943880, 51085595, 51230890, 51376190, 51521485, 51666785, 51812085, 51957380, 52102680, 52247975, 52393275, 52538575, 52686995, 52835415, 52983835, 53132255, 53280680, 53429675, 53578670, 53727670, 53876665, 54025665, 54177780, 54329900, 54482020, 54634140, 54786260, 54938375, 55090495, 55242615, 55394735, 55546855, 55702830, 55858805, 56014785, 56170760, 56326740, 56482715, 56638690, 56794670, 56950645, 57106625, 57265855, 57425085, 57584320, 57743550, 57902785, 58062620, 58222455, 58382290, 58542125, 58701960, 58865160, 59028365, 59191570, 59354775, 59517980, 59681180, 59844385, 60007590, 60170795, 60334000, 60501235, 60668470, 60835705, 61002940, 61170180, 61337415, 61504650, 61671885, 61839120, 62006360, 62177100, 62347845, 62518590, 62689335, 62860080, 63031460, 63202840, 63374225, 63545605, 63716990, 63891880, 64066770, 64241660, 64416550, 64591440, 64766330, 64941220, 65116110, 65291000, 65465890, 65645100, 65824315, 66003530, 66182745, 66361960, 66541170, 66720385, 66899600, 67078815, 67258030, 67440900, 67623775, 67806645, 67989520, 68172395, 68355940, 68539485, 68723030, 68906575, 69090125, 69277450, 69464775, 69652100, 69839425, 70026755, 70214080, 70401405, 70588730, 70776055, 70963385, 71155220, 71347055, 71538890, 71730725, 71922565, 72114400, 72306235, 72498070, 72689905, 72881745, 73077515, 73273285, 73469055, 73664825, 73860600, 74057070, 74253545, 74450020, 74646495, 74842970, 75043375, 75243785, 75444195, 75644605, 75845015, 76045420, 76245830, 76446240, 76646650, 76847060, 77052305, 77257555, 77462800, 77668050, 77873300, 78078545, 78283795, 78489040, 78694290, 78899540, 79108880, 79318225, 79527570, 79736915, 79946260, 80156345, 80366430, 80576520, 80786605, 80996695, 81211010, 81425330, 81639650, 81853970, 82068290, 82282605, 82496925, 82711245, 82925565, 83139885, 83359240, 83578595, 83797955, 84017310, 84236670, 84456025, 84675380, 84894740, 85114095, 85333455, 85557210, 85780965, 86004725, 86228480, 86452240, 86676780, 86901320, 87125860, 87350400, 87574940, 87803880, 88032820, 88261760, 88490700, 88719640, 88948580, 89177520, 89406460, 89635400, 89864340, 90098675, 90333010, 90567345, 90801680, 91036015, 91270350, 91504685, 91739020, 91973355, 92207690, 92446600, 92685515, 92924430, 93163345, 93402260, 93641990, 93881720, 94121450, 94361180, 94600915, 94845370, 95089825, 95334280, 95578735, 95823190, 96067645, 96312100, 96556555, 96801010, 97045465, 97295535, 97545610, 97795685, 98045760, 98295835, 98545905, 98795980, 99046055, 99296130, 99546205, 99801190, 100056175, 100311160, 100566145, 100821135, 101076980, 101332825, 101588675, 101844520, 102100370, 102361125, 102621885, 102882640, 103143400, 103404160, 103664915, 103925675, 104186430, 104447190, 104707950, 104974705, 105241465, 105508220, 105774980, 106041740, 106308495, 106575255, 106842010, 107108770, 107375530, 107647390, 107919250, 108191110, 108462970, 108734835, 109007600, 109280370, 109553135, 109825905, 110098675, 110376705, 110654740, 110932770, 111210805, 111488840, 111766870, 112044905, 112322935, 112600970, 112879005, 113163280, 113447560, 113731840, 114016120, 114300400, 114584675, 114868955, 115153235, 115437515, 115721795, 116011535, 116301280, 116591025, 116880770, 117170515, 117461200, 117751885, 118042575, 118333260, 118623950, 118920100, 119216250, 119512405, 119808555, 120104710, 120400860, 120697010, 120993165, 121289315, 121585470, 121888290, 122191115, 122493940, 122796765, 123099590, 123402410, 123705235, 124008060, 124310885, 124613710, 124922210, 125230710, 125539215, 125847715, 126156220, 126465715, 126775210, 127084705, 127394200, 127703695, 128019040, 128334385, 128649735, 128965080, 129280430, 129595775, 129911120, 130226470, 130541815, 130857165, 131179435, 131501705, 131823975, 132146245, 132468520, 132790790, 133113060, 133435330, 133757600, 134079875, 134408220, 134736565, 135064910, 135393255, 135721600, 136050985, 136380375, 136709765, 137039155, 137368545, 137704005, 138039465, 138374930, 138710390, 139045855, 139381315, 139716775, 140052240, 140387700, 140723165, 141066010, 141408860, 141751705, 142094555, 142437405, 142780250, 143123100, 143465945, 143808795, 144151645, 144500790, 144849940, 145199085, 145548235, 145897385, 146247615, 146597850, 146948085, 147298320, 147648555, 148005280, 148362005, 148718735, 149075460, 149432190, 149788915, 150145640, 150502370, 150859095, 151215825, 151580225, 151944630, 152309035, 152673440, 153037845, 153402245, 153766650, 154131055, 154495460, 154859865, 155230995, 155602130, 155973265, 156344400, 156715535, 157087810, 157460085, 157832360, 158204635, 158576910, 158955915, 159334920, 159713925, 160092930, 160471935, 160850940, 161229945, 161608950, 161987955, 162366960, 162754130, 163141305, 163528475, 163915650, 164302825, 164689995, 165077170, 165464340, 165851515, 166238690, 166632840, 167026995, 167421150, 167815305, 168209460, 168604810, 169000165, 169395515, 169790870, 170186225, 170588760, 170991300, 171393840, 171796380, 172198920, 172601455, 173003995, 173406535, 173809075, 174211615, 174622630, 175033645, 175444660, 175855675, 176266690, 176677705, 177088720, 177499735, 177910750, 178321765, 178740225, 179158690, 179577150, 179995615, 180414080, 180833785, 181253495, 181673200, 182092910, 182512620, 182939775, 183366930, 183794085, 184221240, 184648395, 185075550, 185502705, 185929860, 186357015, 186784170, 187220345, 187656520, 188092700, 188528875, 188965055, 189401230, 189837405, 190273585, 190709760, 191145940, 191589830, 192033720, 192477615, 192921505, 193365400, 193810600, 194255800, 194701000, 195146200, 195591400, 196044540, 196497685, 196950825, 197403970, 197857115, 198310255, 198763400, 199216540, 199669685, 200122830, 200585320, 201047810, 201510305, 201972795, 202435290, 202897780, 203360270, 203822765, 204285255, 204747750, 205218460, 205689170, 206159885, 206630595, 207101310, 207573390, 208045470, 208517555, 208989635, 209461720, 209942020, 210422320, 210902625, 211382925, 211863230, 212343530, 212823830, 213304135, 213784435, 214264740, 214754980, 215245220, 215735460, 216225700, 216715940, 217206180, 217696420, 218186660, 218676900, 219167140, 219665895, 220164650, 220663405, 221162160, 221660915, 222161090, 222661265, 223161445, 223661620, 224161800, 224670730, 225179665, 225688600, 226197535, 226706470, 227215400, 227724335, 228233270, 228742205, 229251140, 229770375, 230289610, 230808845, 231328080, 231847320, 232366555, 232885790, 233405025, 233924260, 234443500, 234971800, 235500100, 236028400, 236556700, 237085000, 237614790, 238144580, 238674370, 239204160, 239733950, 240272800, 240811655, 241350510, 241889365, 242428220, 242967070, 243505925, 244044780, 244583635, 245122490, 245672265, 246222045, 246771820, 247321600, 247871380, 248421155, 248970935, 249520710, 250070490, 250620270, 251179420, 251738575, 252297730, 252856885, 253416040, 253976755, 254537470, 255098185, 255658900, 256219620, 256789980, 257360340, 257930700, 258501060, 259071420, 259641780, 260212140, 260782500, 261352860, 261923220, 262504895, 263086575, 263668255, 264249935, 264831615, 265413290, 265994970, 266576650, 267158330, 267740010, 268331655, 268923305, 269514950, 270106600, 270698250, 271291515, 271884780, 272478045, 273071310, 273664580, 274267815, 274871050, 275474285, 276077520, 276680755, 277283990, 277887225, 278490460, 279093695, 279696930, 280312170, 280927415, 281542660, 282157905, 282773150, 283388390, 284003635, 284618880, 285234125, 285849370, 286474925, 287100480, 287726040, 288351595, 288977155, 289604405, 290231655, 290858910, 291486160, 292113415, 292751260, 293389110, 294026960, 294664810, 295302660, 295940505, 296578355, 297216205, 297854055, 298491905, 299142165, 299792430, 300442695, 301092960, 301743225, 302393485, 303043750, 303694015, 304344280, 304994545, 305655760, 306316980, 306978195, 307639415, 308300635, 308963620, 309626610, 310289600, 310952590, 311615580, 312289520, 312963465, 313637410, 314311355, 314985300, 315659240, 316333185, 317007130, 317681075, 318355020, 319042115, 319729215, 320416315, 321103415, 321790515, 322477610, 323164710, 323851810, 324538910, 325226010, 325924425, 326622845, 327321260, 328019680, 328718100, 329418355, 330118610, 330818865, 331519120, 332219375, 332931260, 333643145, 334355030, 335066915, 335778805, 336490690, 337202575, 337914460, 338626345, 339338235, 340063735, 340789235, 341514740, 342240240, 342965745, 343691245, 344416745, 345142250, 345867750, 346593255, 347330765, 348068275, 348805785, 349543295, 350280810, 351020240, 351759670, 352499100, 353238530, 353977965, 354729405, 355480845, 356232285, 356983725, 357735165, 358486605, 359238045, 359989485, 360740925, 361492365, 362258200, 363024040, 363789875, 364555715, 365321555, 366087390, 366853230, 367619065, 368384905, 369150745, 369928990, 370707235, 371485480, 372263725, 373041975, 373822225, 374602475, 375382730, 376162980, 376943235, 377736225, 378529215, 379322205, 380115195, 380908190, 381701180, 382494170, 383287160, 384080150, 384873145, 385681020, 386488895, 387296770, 388104645, 388912520, 389720395, 390528270, 391336145, 392144020, 392951895, 393772920, 394593950, 395414975, 396236005, 397057035, 397880135, 398703240, 399526345, 400349450, 401172555, 402008810, 402845065, 403681325, 404517580, 405353840, 406190095, 407026350, 407862610, 408698865, 409535125, 410387125, 411239125, 412091125, 412943125, 413795130, 414647130, 415499130, 416351130, 417203130, 418055135, 418920710, 419786285, 420651860, 421517435, 422383015, 423250760, 424118505, 424986250, 425853995, 426721740, 427603420, 428485105, 429366790, 430248475, 431130160, 432011840, 432893525, 433775210, 434656895, 435538580, 436436520, 437334460, 438232400, 439130340, 440028285, 440926225, 441824165, 442722105, 443620045, 444517990, 445430305, 446342620, 447254940, 448167255, 449079575, 449994150, 450908730, 451823310, 452737890, 453652470, 454581425, 455510380, 456439335, 457368290, 458297245, 459226200, 460155155, 461084110, 462013065, 462942020, 463888155, 464834290, 465780425, 466726560, 467672695, 468618830, 469564965, 470511100, 471457235, 472403370, 473364340, 474325310, 475286285, 476247255, 477208230, 478171540, 479134855, 480098170, 481061485, 482024800, 483003335, 483981870, 484960405, 485938940, 486917475, 487896010, 488874545, 489853080, 490831615, 491810150, 492806425, 493802700, 494798980, 495795255, 496791535, 497787810, 498784085, 499780365, 500776640, 501772920, 502784895, 503796870, 504808845, 505820820, 506832800, 507847215, 508861635, 509876050, 510890470, 511904890, 512935005, 513965120, 514995240, 516025355, 517055475, 518085590, 519115705, 520145825, 521175940, 522206060, 523254890, 524303720, 525352550, 526401380, 527450215, 528499045, 529547875, 530596705, 531645535, 532694370, 533759385, 534824400, 535889420, 536954435, 538019455, 539087015, 540154580, 541222145, 542289710, 543357275, 544441445, 545525615, 546609785, 547693955, 548778125, 549862295, 550946465, 552030635, 553114805, 554198975, 555302465, 556405955, 557509445, 558612935, 559716430, 560819920, 561923410, 563026900, 564130390, 565233885, 566354485, 567475085, 568595685, 569716285, 570836890, 571960120, 573083355, 574206585, 575329820, 576453055, 577593395, 578733735, 579874080, 581014420, 582154765, 583295105, 584435445, 585575790, 586716130, 587856475, 589017200, 590177930, 591338655, 592499385, 593660115, 594820840, 595981570, 597142295, 598303025, 599463755, 600642125, 601820495, 602998865, 604177235, 605355605, 606536715, 607717830, 608898945, 610080060, 611261175, 612460370, 613659565, 614858765, 616057960, 617257160, 618456355, 619655550, 620854750, 622053945, 623253145, 624473350, 625693555, 626913760, 628133965, 629354175, 630574380, 631794585, 633014790, 634234995, 635455205, 636694045, 637932885, 639171725, 640410565, 641649405, 642891100, 644132795, 645374490, 646616185, 647857885, 649118210, 650378540, 651638870, 652899200, 654159530, 655419855, 656680185, 657940515, 659200845, 660461175, 661743645, 663026120, 664308590, 665591065, 666873540, 668156010, 669438485, 670720955, 672003430, 673285905, 674587570, 675889235, 677190900, 678492565, 679794235, 681098850, 682403470, 683708085, 685012705, 686317325, 687641615, 688965910, 690290205, 691614500, 692938795, 694263085, 695587380, 696911675, 698235970, 699560265, 700907400, 702254540, 703601675, 704948815, 706295955, 707643090, 708990230, 710337365, 711684505, 713031645, 714399035, 715766430, 717133825, 718501220, 719868615, 721239080, 722609545, 723980010, 725350475, 726720945, 728111665, 729502385, 730893110, 732283830, 733674555, 735065275, 736455995, 737846720, 739237440, 740628165, 742042925, 743457685, 744872445, 746287205, 747701970, 749116730, 750531490, 751946250, 753361010, 754775775, 756211400, 757647025, 759082650, 760518275, 761953900, 763392720, 764831545, 766270365, 767709190, 769148015, 770608210, 772068405, 773528600, 774988795, 776448990, 777909185, 779369380, 780829575, 782289770, 783749965, 785234930, 786719900, 788204870, 789689840, 791174810, 792659775, 794144745, 795629715, 797114685, 798599655, 800106625, 801613595, 803120565, 804627535, 806134510, 807644780, 809155050, 810665320, 812175590, 813685865, 815218135, 816750405, 818282680, 819814950, 821347225, 822879495, 824411765, 825944040, 827476310, 829008585, 830566930, 832125280, 833683630, 835241980, 836800330, 838358675, 839917025, 841475375, 843033725, 844592075, 846173065, 847754060, 849335050, 850916045, 852497040, 854081465, 855665890, 857250320, 858834745, 860419175, 862026795, 863634420, 865242045, 866849670, 868457295, 870064915, 871672540, 873280165, 874887790, 876495415, 878129885, 879764355, 881398830, 883033300, 884667775, 886302245, 887936715, 889571190, 891205660, 892840135, 894498465, 896156795, 897815125, 899473455, 901131790, 902793690, 904455590, 906117490, 907779390, 909441290, 911127045, 912812805, 914498560, 916184320, 917870080, 919555835, 921241595, 922927350, 924613110, 926298870, 928012865, 929726860, 931440860, 933154855, 934868855, 936582850, 938296845, 940010845, 941724840, 943438840, 945177390, 946915940, 948654490, 950393040, 952131595, 953873825, 955616060, 957358295, 959100530, 960842765, 962610135, 964377505, 966144875, 967912245, 969679615, 971446985, 973214355, 974981725, 976749095, 978516465, 980312910, 982109360, 983905810, 985702260, 987498710, 989295155, 991091605, 992888055, 994684505, 996480955, 998303255];
		return $stats[$stat];
	}

	public static function getWcRequiredExp($lvl){
		return $lvl * 50;
	}
}
?>
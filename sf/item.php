<?php

class Item{


	public $raw;

	public $forClass;

	public $slot;

	//public $color;
	public $type;
	public $id;
	public $dmg_min;
	public $dmg_max;
	
	//includes gem stats
	public $stats;
	 // = [
		// "str" => "0",
		// "dex" => "0",
		// "int" => "0",
		// "wit" => "0",
		// "luck" => "0"];

	public $cost;
	public $costMush;

	public $isEpic;

	public $gemSlot;
	public $gem = array(
		"type" => 0,
		"val" => 0);

	public $enchanted;
	public $enchant = array(
		"type" => 0,
		"power" => 0);
 	
 	//TODO: enchant stats include?
	public function Item($data){
		$this->raw = $data;

		//bad planning on the fly... 
		//fortress backpack 100 offset
		//equip offset 10
		//shop offset 20 and 30
		//copycat equip offset 50, 60, 70
		if($data['slot'] >= 100)
			$this->slot = $data['slot'] - 100;
		else if($data['slot']>= 20 && $data['slot'] <=31)
			$this->slot = $data['slot'] % 20;
		else 
			$this->slot = $data['slot'] % 10;
		
		// if type > 65536 -> has slot
		if ($data['type'] % 16777216 > 65535) {
			$this->gemSlot = true;

			$this->gem['type'] = floor($data['type'] % 16777216 / 65536);
			$this->gem['val'] = floor($data['value_mush'] / 65536);
		}


		//if id/65536>1 -> enchanted
		if($data['type'] > 16777216){
			$this->enchanted = true;

			$this->enchant['type'] = floor($data['type'] / 16777216);
			$this->enchant['power'] = floor($data['item_id'] / 65536);
		}



		$this->type = $data['type'] % 65536;
		$this->id = $data['item_id'] % 65536;
		$this->dmg_min = $data['dmg_min'];
		$this->dmg_max = $data['dmg_max'];

		//stats
		$this->setStats();

		$this->cost = $data['value_silver'];
		$this->costMush = $data['value_mush'] % 65536;

		// if not accessory, set forclass
		if ($this->type <= 7){
			$this->forClass = floor($this->id / 1000) + 1;
			$this->id = $this->id % 1000;
		}else{
			$this->forClass = 0;
		}

		if ($this->id >= 50) {
			$this->isEpic = true;
		}
		
		//if you want to this, just make a getter
		//#if(!isEpic && $this->type < 10)
		//	$this->color = (dmg_min + dmg_max + attr_t1 + attr_t2 + attr_t3 + attr_v1 + attr_v2 + attr_v3) % 5;
	}

	public function getColor(){
		return ($this->raw['dmg_min'] + $this->raw['dmg_max'] + $this->raw['a1'] + $this->raw['a2'] + $this->raw['a3'] + $this->raw['a4'] + $this->raw['a5'] + $this->raw['a6']) % 5;
	}

	//set stats in a function for setting gem, incase there was already a gem, delete the stats it gave (for response)
	private function setStats(){

		$this->stats = 
		["str" => "0",
		"dex" => "0",
		"int" => "0",
		"wit" => "0",
		"luck" => "0"];

		//regular stats
		for($i = 1; $i <= 3; $i++){
			$attrIndex = $this->raw['a'.$i];
			if($attrIndex == 6){
				$this->stats["str"] = $this->raw['a4'];
				$this->stats["dex"] = $this->raw['a4'];
				$this->stats["int"] = $this->raw['a4'];
				$this->stats["wit"] = $this->raw['a4'];
				$this->stats["luck"] = $this->raw['a4'];
			}else if($attrIndex > 0)
				$this->stats[Entity::getStatName($attrIndex)] = $this->raw['a'.(3+$i)];
		}

		//gem stats
		if($this->gemSlot && $this->gem['val'] > 0){
			// var_dump($this->gem );
			if($this->gem['type'] > 1 && ($this->gem['type']+1) % 10 == 6){
				$this->stats["str"] += $this->gem['val'];
				$this->stats["dex"] += $this->gem['val'];
				$this->stats["int"] += $this->gem['val'];
				$this->stats["wit"] += $this->gem['val'];
				$this->stats["luck"] += $this->gem['val'];
			}else{
				$this->stats[Entity::getStatName( ($this->gem['type']+1) % 10 )] += $this->gem['val'];
			}
		}
	}

	public function getSave(){
		//return explode("/", "2097159/1010/856/0/3/2/5/672/0/0/31002671/19922944");
		return [$this->raw['type'], 
			$this->raw['item_id'],
			$this->raw['dmg_min'],
			$this->raw['dmg_max'], 
			$this->raw['a1'], 
			$this->raw['a2'], 
			$this->raw['a3'], 
			$this->raw['a4'], 
			$this->raw['a5'], 
			$this->raw['a6'], 
			$this->raw['value_silver'], 
			$this->raw['value_mush']];
	}

	//TODO: setgem and enchant need to check one another
	public function enchant(){
		//update $this->raw only - for getSave()


		$this->enchant['type'] = ($this->type * 10 + 1);
		$this->enchant['power'] = 10;

		$this->raw['type'] = $this->type + ($this->gem['type'] * 65536) + ($this->enchant['type'] * 16777216);


		//enchant power 
		$this->raw['item_id'] += $this->enchant['power'] * 65536;

		$GLOBALS['db']->exec('UPDATE items SET type = '.$this->raw['type'].', item_id = '.$this->raw['item_id'].' WHERE ID = '.$this->raw['ID']);
	}

	public function setGem($gem){
		//here: modify this item with gem
		//remove gem from db, update this item db

		$this->gem['type'] = $gem->id;
		$this->gem['val'] = floor($gem->raw['value_mush'] / 65536);

		//raw = type + (gem*65536) + (ench*16777216)

		$this->raw['type'] = $this->type + ($this->gem['type'] * 65536) + ($this->enchant['type'] * 16777216);
		$this->raw['value_mush'] = ($this->gem['val'] * 65536);

		//set stats, used for displaying after setting gem?
		$this->setStats();

		//update item
		$GLOBALS['db']->exec("UPDATE items SET type = ".$this->raw['type'].", value_mush = ".$this->raw['value_mush']." WHERE ID = ".$this->raw['ID']);

		//delete gem
		$GLOBALS['db']->exec("DELETE FROM items WHERE ID = ".$gem->raw['ID']);
	}

	public function move($target, $slot, $rawTargetSlot){
		$slot--;
		if($target == 1 || $target > 100)
			$this->slot = Item::getEquipSlot($this->type);
		else
			$this->slot = $slot;

		
		$GLOBALS['db']->exec("UPDATE items SET slot = $rawTargetSlot WHERE ID = ".$this->raw['ID']);
	}

	public function buy($target, $slot, $rawTargetSlot){
		$slot--;
		if($target == 1)
			$this->slot = Item::getEquipSlot($this->type);
		else
			$this->slot = $slot;

		// $this->raw['slot'] = $rawTargetSlot;
		// var_dump($slot);


		//decrease value
		// $this->cost = floor($this->cost / 3);
		$this->raw['value_silver'] = $this->cost;
		$this->raw['value_mush'] = 0;

		//NOTICE: IF YOU SET GEMS IN SHOPS, THEIR STAT VALUES WILL NULLIFY BECOUSE OF MUSH VALUE HERE
		$GLOBALS['db']->exec("UPDATE items SET slot = $rawTargetSlot, value_mush = 0, value_silver = $this->cost WHERE ID = ".$this->raw['ID']);
	}

	//use potions, album and whatnot, maybe will be here instead with moveItem revamp
	public function useItem(){

	}



	//flush down the toilet
	public function flush(){
		$qry = ['value_silver = 0'];
		$this->raw['value_silver'] = 0;
		$oldClass = $this->forClass;

		//switch class
		if($this->type <= 7 && $this->type != 2){
			$this->raw['item_id'] -= ($this->forClass -1) * 1000;
			$this->forClass = rand(1, 3);
			$this->raw['item_id'] += ($this->forClass - 1) * 1000;
		}

		$this->raw['item_id'] -= $this->id;
		if($this->isEpic){
			$this->id = rand(50, 58);

			if($this->raw['a1'] <  6){
				if($this->type < 8)
					$this->raw['a1'] = [1, 3, 2][$this->forClass - 1];
				else
					$this->raw['a1'] = rand(1, 3);
				
				$qry[] = 'a1 = '.$this->raw['a1'];
			}

		}else{
			$this->id = rand(1, 10);


			//TODO: check if double stat
			$this->raw['a1'] = rand(1, 5);
			$qry[] = 'a1 = '.$this->raw['a1'];
				
		}
		$this->raw['item_id'] += $this->id;
		$qry[] = 'item_id = '.$this->raw['item_id'];

		//adjust weapon dmg range
		//weapon multiplier
		if($this->type == 1){
			$weaponMultipliers = [2.3, 5.5, 3];

			$wmOld = $weaponMultipliers[$oldClass - 1];
			$wmNew = $weaponMultipliers[$this->forClass - 1];

			$this->raw['dmg_min'] = round($this->raw['dmg_min'] / $wmOld * $wmNew);
			$this->raw['dmg_max'] = round($this->raw['dmg_max'] / $wmOld * $wmNew);

			$qry[] = 'dmg_min = '.$this->raw['dmg_min'];
			$qry[] = 'dmg_max = '.$this->raw['dmg_max'];

		}

		$GLOBALS['db']->exec('UPDATE items SET '.join(',', $qry).' WHERE ID = '.$this->raw['ID']);
	}

	//returns raw/source slot, for moving items
	public static function getItemSlot($target, $slot, $type = 0){
		$slot--;

		switch($target){
			case 1:
				//equip, return slot for item, new func

				//if slot -1, equiping, target from type
				if($type == 0 || $type >= 14 )
					$slot += 10;
				else
					$slot = 10 + Item::getEquipSlot($type);

				//var_dump($slot);
				//exit();
				break;
			case 2:
				//do nothing, same as given -1
				break;
			case 3:
				$slot += 20;
				break;
			case 4:
				$slot += 26;
				break;
			case 5:
				$slot += 100;
				break;
			case 101:
			case 102:
			case 103:
				$target-=101;
				if($type == 0 || $type >= 14 )
					$slot += ($target*10) + 50;
				else
					$slot = ($target*10) + 50 + Item::getEquipSlot($type);

				break;
		}

		return $slot;
	}

	public static function getEquipSlot($type){
		$type = $type;
		switch($type){
			// weapon
	        case 1:
	            return 8;
	        // shield
	        case 2:
	            return 9;
	        // chest
	        case 3:
	            return 1;
	        // boots
	        case 4:
	            return 3;
	        // gloves
	        case 5:
	            return 2;
	        // helmet
	        case 6:
	            return 0;
	        // belt
	        case 7:
	            return 5;        
	        // necklace
	        case 8:            
	            return 4;        
	        // ring
	        case 9:            
	            return 6;        
	        // relic
	        case 10:
	            return 7;
        }
    }

    //NEW GEN ITEM FUNCTION
    public static function genItem($type, $lvl, $class, $epicChance = 0, $mineLvl = 0){

    	//debugging
    	// $epicChance = 50;
		// $type = 1;


    	//sometimes when passing random args 1 through 10 in type, it can end up being shield for a mage/rouge
    	//so just make sure here that it isnt a shield for a mage/rouge, just roll another type
    	while($type == 2 && $class != 1)
    		$type = rand(1, 7);


    	$item = [
		"type" => (rand(0, 1) * 65536) + $type,
		"item_id" => rand(1, 10),
		"dmg_min" => 0,
		"dmg_max" => 0,
		"a1" => 0,
		"a2" => 0,
		"a3" => 0,
		"a4" => 0,
		"a5" => 0,
		"a6" => 0,
		"value_silver" => rand(2312, 29803),
		"value_mush" => 0];

		if($type <= 10){
			$isEpic = rand(0, 99) < $epicChance;
			if($isEpic){
				//set stats here
				//25% for allstat
				if(rand(1, 4) > 1){
					$statVal = 2 + round($lvl * 1.5);
					$z = round(3 + $lvl * 0.05);
					$statVal += round(rand($z / -2, $z / 2));

					$item['a1'] = [1, 3, 2][$class - 1];
					$item['a2'] = 4;
					$item['a3'] = 5;
					$item['a4'] = $statVal;
					$item['a5'] = $statVal;
					$item['a6'] = $statVal;
				}else{
					$statVal = 4 + round($lvl * 1.27);
					$z = round(3 + $lvl * 0.05);
					$statVal += round(rand($z / -2, $z / 2));

					$item['a1'] = 6;
					$item['a4'] = $statVal;
				}
			}else{

				$statVal = round($lvl * 2.33);
				$z = round(3 + $lvl * 0.05);
				$statVal += rand($z / -2, $z / 2);



				//double stat
				// if(rand(0, 1) == 2){

				// }else
				$item['a1'] = rand(1,5);
				$item['a4'] = $statVal;
			}
		}

		//armor
		if($type > 2 && $type < 8)
			$item['dmg_min'] = 1;

		//set class
		

		switch($type){
			case 1:

				if($isEpic)
					$item['item_id'] = rand(50, 63);
				else if($class == 1)
					$item['item_id'] = rand(1,30);
				else
					$item['item_id'] = rand(1,10);

				//weapon multiplier
				if($class == 1)
					$weaponMultiplier = 2.3;
				else if($class == 3)
					$weaponMultiplier = 3;
				else 
					$weaponMultiplier = 5.5;

				$dmg = 1 + floor($weaponMultiplier * $lvl);
				$minmax = 2 + floor($lvl * rand(5, 100) / 100);

				$item['dmg_min'] = $dmg - $minmax;
				$item['dmg_max'] = $dmg + $minmax;

				break;
			case 2:
				if($isEpic)
					$item['item_id'] = rand(50, 63);
				else
					$item['item_id'] = rand(1, 10);

				//block
				$item['dmg_min'] = 25;
				break;
			case 3:
				if($isEpic)
					$item['item_id'] = [50, 51, 52, 53, 54, 55, 56, 57, 58, 61, 62, 63][rand(0, 11)];
				else
					$item['item_id'] = rand(1, 10);

				break;
			case 4:
				if($isEpic)
					$item['item_id'] = [50, 51, 52, 53, 54, 55, 56, 57, 58, 61, 62, 63][rand(0, 11)];
				else
					$item['item_id'] = rand(1, 10);

				break;
			case 5:
				if($isEpic)
					$item['item_id'] = [50, 51, 52, 53, 54, 55, 56, 57, 58, 61, 62, 63][rand(0, 11)];
				else
					$item['item_id'] = rand(1, 10);
				break;
			case 6:
				if($isEpic)
					$item['item_id'] = [50, 51, 52, 53, 54, 55, 56, 57, 58, 61, 62, 63][rand(0, 11)];
				else
					$item['item_id'] = rand(1, 10);
				break;
			case 7:
				if($isEpic)
					$item['item_id'] = [50, 51, 52, 53, 54, 55, 56, 57, 58, 61, 62, 63][rand(0, 11)];
				else
					$item['item_id'] = rand(1, 10);
				break;
			case 8:
				if($isEpic)
					$item['item_id'] = rand(50, 63);
				else
					$item['item_id'] = rand(1, 21);
				break;
			case 9:
				if($isEpic)
					$item['item_id'] = rand(50, 63);
				else
					$item['item_id'] = rand(1, 16);
				break;
			case 10:
				if($isEpic)
					$item['item_id'] = rand(50, 63);
				else
					$item['item_id'] = rand(1, 37);
				break;
			case 12: //potion
				//type is always type value, no gems slots etc
					$item['type'] = $type;

				//hp pot 25%
				if(rand(0, 99) > 25){
					$item['item_id'] = rand(11, 15);

					//duration
					$item['a1'] = 11;
					$item['a4'] = 72;

					//value %
					$item['a2'] = $item['item_id'] - 10;
					$item['a5'] = 25;

					//always no mush cost
					$item['value_mush'] = 0;
				}else{
					$item['item_id'] = 16;

					//duration
					$item['a1'] = 11;
					$item['a4'] = 168;

					//health 
					$item['a2'] = 12;
					$item['a5'] = 25;

					$item['value_mush'] = rand(0, 1) * 15;
				}
				break;
			case 15: //gem
				//type is always type value, no gems slots etc
				$item['type'] = $type;

				$quality = rand(1, 3);
				$stat = rand(0, 5);
				$value = rand(250, 400);

				$item['item_id'] = $quality * 10 + $stat;
				$item['value_mush'] = $value * 65536;
				break;
		}

		//set class
		if($type <= 7)
			$item['item_id'] += 1000 * ($class - 1);

		return $item;
	}
}
?>
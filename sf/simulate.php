<?php

class Simulation{

	private $player;
	private $opponent;

	//winner id
	public $winner;

	public $fightLog;

	//db ID of winner
	public $winnerID;

	//the longer the fights go on, the higher the hits
	private $progressionMultiplier;

	//reference needed for group simulation
	public function Simulation(&$player, &$opponent){
		$this->player = $player;
		$this->opponent = $opponent;

	}

	public function simulate(){
		//block = rand(1, 100), 0 = no block
		//hit types:
		// 0 = normal | 1 = crit | 2 = catapult | 3 = blok | 4 = dodge


		//log structure: hitter id/hit type/hp after hit

		// $this->fightLog = "2,1,2687957,10971,4,1503765,2,1,1959808,10971,0,1387165,2,0,1728563,10971,0,1268027,2,1,1533291,10971,4,1268027,2,0,1323200,10971,4,1268027,2,0,989674,10971,4,1268027,2,3,989674,10971,0,1007179,2,0,794697,10971,4,1007179,2,0,616343,10971,0,772272,2,3,616343,10971,4,772272,2,1,-92278";
		// $this->fightLog = "5,3,-17663";

		$this->fightLog = [];

		//randomize who begins fight
		if(rand(0, 1) == 1){
			$first = "player";
			$second = "opponent";
		}else{
			$first = "opponent";
			$second = "player";
		}

		$blocks = ($this->player->class != 2 && $this->opponent->class != 2);
		$this->progressionMultiplier = 1.0;

		while($this->opponent->hp > 0 && $this->player->hp > 0){
			if($this->$first->hp > 0){
				$this->hit($first, $second, $blocks);
			}

			if($this->$second->hp > 0){
				$this->hit($second, $first, $blocks);
			}

			if($this->progressionMultiplier < 1.7)
				$this->progressionMultiplier += 0.1;
		}

		$this->fightLog = join(",", $this->fightLog);
		
		if($this->player->hp > 0)
			$this->winnerID = $this->player->ID;
		else{
			//monsters need negative value on this becouse fucking reasons
			$this->winnerID = $this->opponent->ID;
			if(get_class($this->opponent) == "Monster")
				$this->winnerID = abs($this->winnerID) * -1;
		}


	}


	//TODO: count dmg with armor and shit
	private function hit($hitter, $target, $block){
		//make this a float later
		$crit = (rand(0,100) < $this->$hitter->crit);

		//if warrior has no shield / wmoved to player, if no shield block chance = 0
		// if($this->$target->class == 1 && !isset($this->$target->shield))
		// 	$block = false;

		//block/dodge
		if($block)
			$block = (rand(0, 100) < $this->$target->block);

		//log structure: hitter id/hit type/hp after hit
		$this->fightLog[] = $this->$hitter->ID;

		if($block)
			$hitType = ($this->$target->class == 1) ? 3 : 4;
		else if($crit)
			$hitType = 1;
		else
			$hitType = 0;

		$this->fightLog[] = $hitType;

		//TODO: count the dmg

		$dmg = round(rand($this->$hitter->dmg_min, $this->$hitter->dmg_max) * $this->progressionMultiplier);

		if($crit)
			$dmg *= 2;

		if(!$block)
			$this->$target->dmg($dmg);
		$this->fightLog[] = $this->$target->hp;
	}


}




class GroupSimulation{

	public $playerGroup;
	public $opponentGroup;

	//array of simulation objects to access fight logs and winners
	public $simulations = [];

	//array of fight headers, contain starting hp, so they need to be set before fight simulation
	public $fightHeaders = [];

	//boolean, true if playerGroup wins
	public $win;

	public function GroupSimulation($playerGroup, $opponentGroup){
		$this->playerGroup = $playerGroup;
		$this->opponentGroup = $opponentGroup;
	}

	public function simulate(){
		$playerLast = &$this->playerGroup[count($this->playerGroup) - 1];
		$opponentLast = &$this->opponentGroup[count($this->opponentGroup) - 1];

		//counters
		$pc = 0;
		$oc = 0;

		while($playerLast->hp > 0 && $opponentLast->hp > 0){

			//scene, background etc handled outside
			$this->fightHeaders[] = $this->playerGroup[$pc]->getFightHeader().$this->opponentGroup[$oc]->getFightHeader();

			$simulation = new Simulation($this->playerGroup[$pc], $this->opponentGroup[$oc]);
			$simulation->simulate();


			$this->simulations[] = $simulation;

			//increase counter depending on winner
			if($simulation->winnerID == $this->playerGroup[$pc]->ID)
				$oc++;
			else
				$pc++;
		}

		//set winner
		$this->win = $playerLast->hp > 0;
	}

	public static function reverseGuildFightLog($log){

		return $log;

		$log = split('&', $log);

		foreach($log as $line){
			switch($line){

			}
		}
	}
}

?>
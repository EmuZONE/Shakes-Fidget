<?php


//max lvle
//twierdza 20;
//robotnicy 15;
//kopalnie surowcow 20;
//kopalnia klejnotow 20;
//exp 20;
//archery 15;
//woje 15;
//magi 15;
//skarbiec 15;
//kuznia 20;
//fort 20;

//ID order:
//twierdza/robotnicy/kopalnia/kopalnia/kopalnia klejnotow/exp/archery/woje/magi/skarbiec/kuznia/fort

//twierdzda 0
//robotnicy 1
//wood 2
//stone 3
//gem 4
//exp 5
//archers 6
//warriors 7
//mages 8
//bank 9
//upgrades 10
//fort 11

//STATS FROM THIS SITE
//http://en.4m7.de/sammelalbum/festung.php

class Fortress{

	//returns array [time, gold, wood, stone]
	public static function getUpgradePriceNew($id, $lvl){
		switch($id){
			case 0:
			switch($lvl){
				case 0:return [3600, 10000, 0, 0];
				case 1:return [3600, 20000, 150, 50];
				case 2:return [3600, 30000, 440, 140];
				case 3:return [3600, 40000, 1100, 333];
				case 4:return [3600, 50000, 2500, 800];
				case 5:return [3600, 60000, 6000, 2000];
				case 6:return [3600, 70000, 13417, 4433];
				case 7:return [3600, 80000, 27200, 9280];
				case 8:return [3600, 90000, 57375, 19125];
				case 9:return [3600, 100000, 154000, 50000];
				case 10:return [3600, 110000, 379500, 122100];
				case 11:return [3600, 120000, 830400, 273600];
				case 12:return [3600, 130000, 1872000, 619200];
				case 13:return [3600, 140000, 3744000, 1248000];
				case 14:return [3600, 150000, 7200000, 2340000];
				case 15:return [3600, 160000, 15120000, 5040000];
				case 16:return [3600, 170000, 27350000, 9000000];
				case 17:return [3600, 180000, 50000000, 17500000];
				case 18:return [3600, 190000, 90000000, 30000000];
				case 19:return [3600, 200000, 165000000, 54000000];
			}
			case 1:
			switch($lvl){
				case 0:return [3600, 5000, 35, 12];
				case 1:return [3600, 10000, 138, 46];
				case 2:return [3600, 15000, 406, 129];
				case 3:return [3600, 20000, 1015, 308];
				case 4:return [3600, 25000, 2308, 738];
				case 5:return [3600, 30000, 5538, 1849];
				case 6:return [3600, 35000, 12385, 4092];
				case 7:return [3600, 40000, 25108, 8566];
				case 8:return [3600, 45000, 52962, 17654];
				case 9:return [3600, 50000, 142154, 46154];
				case 10:return [3600, 55000, 350308, 112708];
				case 11:return [3600, 60000, 766523, 252554];
				case 12:return [3600, 65000, 1872000, 619200];
				case 13:return [3600, 70000, 3744000, 1248000];
				case 14:return [3600, 75000, 7200000, 2340000];
			}
			case 2:
			switch($lvl){
				case 0:return[3600, 200, 0, 0];
				case 1:return[3600, 400, 30, 20];
				case 2:return[3600, 600, 88, 56];
				case 3:return[3600, 800, 220, 133];
				case 4:return[3600, 1000, 500, 320];
				case 5:return[3600, 1200, 1200, 800];
				case 6:return[3600, 1400, 2683, 1773];
				case 7:return[3600, 1600, 5440, 3712];
				case 8:return[3600, 1800, 11475, 7650];
				case 9:return[3600, 200, 30800, 20000];
				case 10:return[3600, 2200, 75900, 48840];
				case 11:return[3600, 2400, 166080, 109440];
				case 12:return[3600, 2600, 405600, 268320];
				case 13:return[3600, 2800, 873600, 582400];
				case 14:return[3600, 3000, 1800000, 1170000];
				case 15:return[3600, 3200, 1800000, 1170000];
				case 16:return[3600, 3400, 1800000, 1170000];
				case 17:return[3600, 3600, 1800000, 1170000];
				case 18:return[3600, 3800, 1800000, 1170000];
				case 19:return[3600, 4000, 1800000, 1170000];
			}
			case 3:
			switch($lvl){
				case 0:return[3600, 3000, 22, 0];
				case 1:return[3600, 6000, 90, 16];
				case 2:return[3600, 9000, 264, 45];
				case 3:return[3600, 12000, 660, 107];
				case 4:return[3600, 15000, 1500, 256];
				case 5:return[3600, 18000, 3600, 640];
				case 6:return[3600, 21000, 8050, 1419];
				case 7:return[3600, 24000, 16320, 2970];
				case 8:return[3600, 27000, 34425, 6120];
				case 9:return[3600, 30000, 92400, 16000];
				case 10:return[3600, 33000, 227700, 39072];
				case 11:return[3600, 36000, 498240, 87552];
				case 12:return[3600, 39000, 1216800, 214656];
				case 13:return[3600, 42000, 2620800, 465920];
				case 14:return[3600, 45000, 5400000, 936000];
				case 15:return[3600, 45000, 5400000, 936000];
				case 16:return[3600, 45000, 5400000, 936000];
				case 17:return[3600, 45000, 5400000, 936000];
				case 18:return[3600, 45000, 5400000, 936000];
				case 19:return[3600, 45000, 5400000, 936000];
			}
			case 4:
			switch($lvl){
				case 0:return[3600, 15000, 50, 17];
				case 1:return[3600, 30000, 200, 67];
				case 2:return[3600, 45000, 587, 187];
				case 3:return[3600, 60000, 1467, 444];
				case 4:return[3600, 75000, 3333, 1067];
				case 5:return[3600, 90000, 8000, 2667];
				case 6:return[3600, 105000, 17889, 5911];
				case 7:return[3600, 120000, 36267, 12373];
				case 8:return[3600, 135000, 76500, 25500];
				case 9:return[3600, 150000, 184800, 60000];
				case 10:return[3600, 165000, 414000, 133200];
				case 11:return[3600, 180000, 830400, 273600];
				case 12:return[3600, 195000, 1872000, 619200];
				case 13:return[3600, 210000, 3744000, 1248000];
				case 14:return[3600, 225000, 3744000, 1248000];
				case 15:return[3600, 240000, 3744000, 1248000];
				case 16:return[3600, 255000, 3744000, 1248000];
				case 17:return[3600, 270000, 3744000, 1248000];
				case 18:return[3600, 285000, 3744000, 1248000];
				case 19:return[3600, 300000, 3744000, 1248000];
			}
			case 5:
			switch($lvl){
				case 0: return[3600, 7000, 7, 9];
				case 1: return[3600, 14000, 28, 37];
				case 2: return[3600, 21000, 81, 103];
				case 3: return[3600, 28000, 203, 246];
				case 4: return[3600, 35000, 462, 591];
				case 5: return[3600, 42000, 1108, 1477];
				case 6: return[3600, 49000, 2477, 3247];
				case 7: return[3600, 56000, 5022, 6853];
				case 8: return[3600, 63000, 10592, 14123];
				case 9: return[3600, 70000, 28431, 36923];
				case 10: return[3600, 77000, 70062, 90166];
				case 11: return[3600, 84000, 153305, 202043];
				case 12: return[3600, 91000, 374400, 495360];
				case 13: return[3600, 98000, 748800, 998400];
				case 14: return[3600, 98000, 748800, 998400];
				case 15: return[3600, 98000, 748800, 998400];
				case 16: return[3600, 98000, 748800, 998400];
				case 17: return[3600, 98000, 748800, 998400];
				case 18: return[3600, 98000, 748800, 998400];
				case 19: return[3600, 98000, 748800, 998400];
			}
			case 6:
			switch($lvl){
				case 0: return[3600, 5000, 41, 7];
				case 1: return[3600, 10000, 164, 27];
				case 2: return[3600, 15000, 480, 76];
				case 3: return[3600, 20000, 1200, 182];
				case 4: return[3600, 25000, 2727, 436];
				case 5: return[3600, 30000, 6545, 1091];
				case 6: return[3600, 35000, 14636, 2418];
				case 7: return[3600, 40000, 29673, 5062];
				case 8: return[3600, 45000, 62591, 10432];
				case 9: return[3600, 50000, 168000, 27273];
				case 10: return[3600, 55000, 414000, 66600];
				case 11: return[3600, 60000, 830400, 136800];
				case 12: return[3600, 65000, 1872000, 309600];
				case 13: return[3600, 70000, 3744000, 624000];
				case 14: return[3600, 75000, 3744000, 624000];
			}
			case 7:
			switch($lvl){
				case 0: return[3600, 4000, 20, 14];
				case 1: return[3600, 8000, 82, 55];
				case 2: return[3600, 12000, 240, 153];
				case 3: return[3600, 16000, 600, 364];
				case 4: return[3600, 20000, 1364, 873];
				case 5: return[3600, 24000, 3273, 2182];
				case 6: return[3600, 28000, 7318, 4836];
				case 7: return[3600, 32000, 14836, 10124];
				case 8: return[3600, 36000, 31295, 20864];
				case 9: return[3600, 40000, 84000, 54545];
				case 10: return[3600, 44000, 207000, 133200];
				case 11: return[3600, 48000, 415200, 273600];
				case 12: return[3600, 52000, 415200, 273600];
				case 13: return[3600, 56000, 415200, 273600];
				case 14: return[3600, 60000, 415200, 273600];
			}
			case 8:
			switch($lvl){
				case 0: return[3600, 6000, 61, 20];
				case 1: return[3600, 12000, 240, 61];
				case 2: return[3600, 18000, 675, 205];
				case 3: return[3600, 24000, 1636, 524];
				case 4: return[3600, 30000, 4091, 1364];
				case 5: return[3600, 36000, 9409, 3109];
				case 6: return[3600, 42000, 19473, 6644];
				case 7: return[3600, 48000, 41727, 13909];
				case 8: return[3600, 54000, 113400, 36818];
				case 9: return[3600, 60000, 282273, 90818];
				case 10: return[3600, 66000, 622800, 205200];
				case 11: return[3600, 72000, 1404000, 464400];
				case 12: return[3600, 78000, 2808000, 936000];
				case 13: return[3600, 84000, 5400000, 1755000];
				case 14: return[3600, 90000, 5400000, 1755000];
			}
			case 9:
			switch($lvl){
				case 0: return[3600, 25000, 40, 13];
				case 1: return[3600, 50000, 160, 53];
				case 2: return[3600, 75000, 469, 149];
				case 3: return[3600, 100000, 1173, 356];
				case 4: return[3600, 125000, 2667, 853];
				case 5: return[3600, 150000, 6400, 2133];
				case 6: return[3600, 175000, 14311, 4729];
				case 7: return[3600, 200000, 29013, 9899];
				case 8: return[3600, 225000, 61200, 20400];
				case 9: return[3600, 250000, 147840, 48000];
				case 10: return[3600, 275000, 331200, 106560];
				case 11: return[3600, 300000, 664320, 218880];
				case 12: return[3600, 325000, 664320, 218880];
				case 13: return[3600, 350000, 664320, 218880];
				case 14: return[3600, 375000, 664320, 218880];
			}
			case 10:
			switch($lvl){
				case 0: return[3600, 4000, 25, 8];
				case 1: return[3600, 8000, 100, 33];
				case 2: return[3600, 12000, 293, 93];
				case 3: return[3600, 16000, 733, 222];
				case 4: return[3600, 20000, 1667, 533];
				case 5: return[3600, 24000, 4000, 1333];
				case 6: return[3600, 28000, 8944, 2956];
				case 7: return[3600, 32000, 18133, 6187];
				case 8: return[3600, 36000, 38250, 12750];
				case 9: return[3600, 40000, 92400, 30000];
				case 10: return[3600, 44000, 207000, 66600];
				case 11: return[3600, 48000, 415200, 136800];
				case 12: return[3600, 52000, 936000, 309600];
				case 13: return[3600, 56000, 1872000, 624000];
				case 14: return[3600, 60000, 3600000, 1170000];
				case 15: return[3600, 64000, 3600000, 1170000];
				case 16: return[3600, 68000, 3600000, 1170000];
				case 17: return[3600, 72000, 3600000, 1170000];
				case 18: return[3600, 76000, 3600000, 1170000];
				case 19: return[3600, 80000, 3600000, 1170000];
			}
			case 11:
			switch($lvl){
				case 0: return[3600, 15000, 30, 13];
				case 1: return[3600, 30000, 120, 53];
				case 2: return[3600, 45000, 352, 149];
				case 3: return[3600, 60000, 880, 356];
				case 4: return[3600, 75000, 2000, 853];
				case 5: return[3600, 90000, 4800, 2133];
				case 6: return[3600, 105000, 10733, 4729];
				case 7: return[3600, 120000, 21760, 9899];
				case 8: return[3600, 135000, 45900, 20400];
				case 9: return[3600, 150000, 110880, 48000];
				case 10: return[3600, 165000, 248400, 106560];
				case 11: return[3600, 180000, 498240, 218880];
				case 12: return[3600, 195000, 1123200, 495360];
				case 13: return[3600, 210000, 2246400, 998400];
				case 14: return[3600, 225000, 4320000, 1872000];
				case 15: return[3600, 240000, 4320000, 1872000];
				case 16: return[3600, 255000, 4320000, 1872000];
				case 17: return[3600, 270000, 4320000, 1872000];
				case 18: return[3600, 285000, 4320000, 1872000];
				case 19: return[3600, 300000, 4320000, 1872000];
			}
			default:
				return [0, 0, 0, 0];
		}
	}

	public static function getHallOfKnightsPrice($currentLvl){
		switch($currentLvl){
			case 0: return [0, 0, 720, 240];
			case 1: return [0, 0, 1408, 448];
			case 2: return [0, 0, 2640, 800];
			case 3: return [0, 0, 4800, 1536];
			case 4: return [0, 0, 9600, 3200];
			case 5: return [0, 0, 18400, 6080];
			case 6: return [0, 0, 32640, 11136];
			case 7: return [0, 0, 61200, 20400];
			case 8: return [0, 0, 147840, 48000];
			case 9: return [0, 0, 331200, 106560];
			case 10: return [0, 0, 664320, 218880];
			case 11: return [0, 0, 1497600, 495360];
			case 12: return [0, 0, 2995200, 998400];
			case 13: return [0, 0, 5760000, 1872000];
			case 14: return [0, 0, 12096000, 4032000];
			case 15: return [0, 0, 1, 1];
			case 16: return [0, 0, 1, 1];
			case 17: return [0, 0, 1, 1];
			case 18: return [0, 0, 1, 1];
			case 19: return [0, 0, 1, 1];
		}
	}
	
	public static function getUpgradePrice($id, $lvl){
		//returns array time/gold/wood/stone
		$ret = [];

		if(Fortress::getMaxLevel($id) == $lvl){

			$ret[] = '0/0/0/0';
		}else{
			$ret[] = join('/', Fortress::getUpgradePriceNew($id, $lvl));
			// $ret[] = 600 * $lvl + 3600;
			// $ret[] = 20000;
			// $ret[] = 0;
			// $ret[] = 0;
		}


		return $ret;
	}

	public static function getMaxLevel($id){
		//twierdza 20;
		//robotnicy 15;
		//kopalnie surowcow 20;
		//kopalnia klejnotow 20;
		//exp 20;
		//archery 15;
		//woje 15;
		//magi 15;
		//skarbiec 15;
		//kuznia 20;
		//fort 20;

		switch($id){
			case 0:
			case 2:
			case 3:
			case 4:
			case 5:
		 	case 10:
		 	case 11:
		 		return 20;
		 	default:
		 		return 15;
		}
	}

	public static function getResourcesPerHour($resource, $lvl){
		// return floor(pow(50, (1 + $lvl*0.1)));

		$multiplier = 50;
		
		switch($resource){
			case 1:
				switch($lvl){
					case 1: return 150 * $multiplier;
					case 2: return 220 * $multiplier;
					case 3: return 330 * $multiplier;
					case 4: return 500 * $multiplier;
					case 5: return 750 * $multiplier;
					case 6: return 1150 * $multiplier;
					case 7: return 1700 * $multiplier;
					case 8: return 2550 * $multiplier;
					case 9: return 3850 * $multiplier;
					case 10: return 5750 * $multiplier;
					case 11: return 8650 * $multiplier;
					case 12: return 13000 * $multiplier;
					case 13: return 19500 * $multiplier;
					case 14: return 30000 * $multiplier;
					case 15: return 45000 * $multiplier;
					case 16: return 65000 * $multiplier;
					case 17: return 80000 * $multiplier;
					case 18: return 120000 * $multiplier;
					case 19: return 185000 * $multiplier;
					case 20: return 250000 * $multiplier;
				}
			case 2:
				switch($lvl){
					case 1: return 50 * $multiplier;
					case 2: return 70 * $multiplier;
					case 3: return 100 * $multiplier;
					case 4: return 160 * $multiplier;
					case 5: return 250 * $multiplier;
					case 6: return 380 * $multiplier;
					case 7: return 580 * $multiplier;
					case 8: return 850 * $multiplier;
					case 9: return 1250 * $multiplier;
					case 10: return 1850 * $multiplier;
					case 11: return 2850 * $multiplier;
					case 12: return 4300 * $multiplier;
					case 13: return 6500 * $multiplier;
					case 14: return 9750 * $multiplier;
					case 15: return 15000 * $multiplier;
					case 16: return 27500 * $multiplier;
					case 17: return 52600 * $multiplier;
					case 18: return 96400 * $multiplier;
					case 19: return 126000 * $multiplier;
					case 20: return 180000 * $multiplier;
				}
			case 3:
				return floor(pow(50, (1 + $lvl*0.1)));
		}
	}

	public static function getMaxResources($resource, $lvl, $playerLvl = 0){
		switch($resource){
			case 1:
				switch($lvl){
					case 1: return 375;
					case 2: return 605;
					case 3: return 990;
					case 4: return 1625;
					case 5: return 2625;
					case 6: return 4312;
					case 7: return 6800;
					case 8: return 12750;
					case 9: return 23100;
					case 10: return 46000;
					case 11: return 86500;
					case 12: return 156000;
					case 13: return 312000;
					case 14: return 600000;
					case 15: return 1080000;
					case 16: return 1080000;
					case 17: return 1080000;
					case 18: return 1080000;
					case 19: return 1080000;
					case 20: return 1080000;
				}
			case 2:
				switch($lvl){
					case 1: return 125;
					case 2: return 192;
					case 3: return 300;
					case 4: return 520;
					case 5: return 875;
					case 6: return 1425;
					case 7: return 2320;
					case 8: return 4250;
					case 9: return 7500;
					case 10: return 14800;
					case 11: return 28500;
					case 12: return 51600;
					case 13: return 104000;
					case 14: return 195000;
					case 15: return 360000;
					case 16: return 360000;
					case 17: return 360000;
					case 18: return 360000;
					case 19: return 360000;
					case 20: return 360000;
				}
			case 3:
				return 10000000 * $lvl;
		}
	}

	public static function getUnitLvl($lvl){
		switch($lvl){
			case 1: return 25;
			case 2: return 28;
			case 3: return 30;
			case 4: return 35;
			case 5: return 40;
			case 6: return 45;
			case 7: return 50;
			case 8: return 55;
			case 9: return 62;
			case 10: return 70; 
			case 11: return 77;
			case 12: return 85;
			case 13: return 95;
			case 14: return 105;
			case 15: return 115;
			case 16: return 125;
			case 17: return 135;
			case 18: return 145;
			case 19: return 155;
			case 20: return 170;
		}
	}

	public static function getUnitTrainPrice($unit, $lvl){
		switch($unit){
			case 1:
				switch($lvl){
					case 1: return [600, 0, 15, 5];
					case 2: return [600, 0, 48, 15];
					case 3: return [600, 0, 54, 16];
					case 4: return [600, 0, 65, 20];
					case 5: return [600, 0, 78, 26];
					case 6: return [600, 0, 107, 35];
					case 7: return [600, 0, 136, 46];
					case 8: return [600, 0, 255, 85];
					case 9: return [600, 0, 346, 112];
					case 10: return [600, 0, 690, 222];
					case 11: return [600, 0, 1298, 428];
					case 12: return [600, 0, 1560, 516];
					case 13: return [600, 0, 3120, 1040];
					case 14: return [600, 0, 6000, 1950];
					case 15: return [600, 0, 10800, 3600];
					case 16: return [600, 0, 10800, 3600];
					case 17: return [600, 0, 10800, 3600];
					case 18: return [600, 0, 10800, 3600];
					case 19: return [600, 0, 10800, 3600];
					case 20: return [600, 0, 10800, 3600];
				}
			case 2:
				switch($lvl){
					case 1: return [600, 0, 11, 6];
					case 2: return [600, 0, 36, 19];
					case 3: return [600, 0, 40, 20];
					case 4: return [600, 0, 49, 25];
					case 5: return [600, 0, 59, 32];
					case 6: return [600, 0, 80, 44];
					case 7: return [600, 0, 102, 57];
					case 8: return [600, 0, 191, 106];
					case 9: return [600, 0, 260, 140];
					case 10: return [600, 0, 517, 277];
					case 11: return [600, 0, 973, 534];
					case 12: return [600, 0, 1170, 645];
					case 13: return [600, 0, 2340, 1300];
					case 14: return [600, 0, 2340, 1300];
					case 15: return [600, 0, 2340, 1300];
					case 16: return [600, 0, 2340, 1300];
					case 17: return [600, 0, 2340, 1300];
					case 18: return [600, 0, 2340, 1300];
					case 19: return [600, 0, 2340, 1300];
					case 20: return [600, 0, 2340, 1300];
				}
			case 3:
				switch($lvl){
					case 1: return [600, 0, 19, 4];
					case 2: return [600, 0, 60, 11];
					case 3: return [600, 0, 67, 12];
					case 4: return [600, 0, 81, 15];
					case 5: return [600, 0, 98, 19];
					case 6: return [600, 0, 134, 26];
					case 7: return [600, 0, 195, 34];
					case 8: return [600, 0, 319, 64];
					case 9: return [600, 0, 433, 84];
					case 10: return [600, 0, 862, 166];
					case 11: return [600, 0, 1622, 320];
					case 12: return [600, 0, 1950, 387];
					case 13: return [600, 0, 3900, 780];
					case 14: return [600, 0, 7500, 1462];
					case 15: return [600, 0, 7500, 1462];
					case 16: return [600, 0, 7500, 1462];
					case 17: return [600, 0, 7500, 1462];
					case 18: return [600, 0, 7500, 1462];
					case 19: return [600, 0, 7500, 1462];
					case 20: return [600, 0, 7500, 1462];
				}
		}
	}

	public static function getUnitUpgradePrice($unit, $lvl){
		switch($unit){
			case 1:
				switch($lvl){
					case 1: return [270, 210];
					case 2: return [528, 392];
					case 3: return [990, 700];
					case 4: return [1800, 1344];
					case 5: return [3600, 2800];
					case 6: return [6900, 5320];
					case 7: return [12240, 9744];
					case 8: return [22950, 17850];
					case 9: return [55440, 42000];
					case 10: return [124200, 93240];
					case 11: return [249120, 191520];
					case 12: return [561600, 433440];
					case 13: return [1123200, 873600];
					case 14: return [2160000, 1638000];
					case 15: return [4536000, 3528000];
					case 16: return [4536000, 3528000];
					case 17: return [4536000, 3528000];
					case 18: return [4536000, 3528000];
					case 19: return [4536000, 3528000];
					case 20: return [4536000, 3528000];
				}
			case 2:
				switch($lvl){
					case 1: return [720, 60];
					case 2: return [1408, 112];
					case 3: return [2640, 200];
					case 4: return [4800, 384];
					case 5: return [9600, 800];
					case 6: return [18400, 1520];
					case 7: return [32640, 2784];
					case 8: return [61200, 5100];
					case 9: return [147840, 12000];
					case 10: return [331200, 26640];
					case 11: return [664320, 54720];
					case 12: return [1497600, 123840];
					case 13: return [2995200, 249600];
					case 14: return [5760000, 468000];
					case 15: return [12096000, 1008000];
					case 16: return [12096000, 1008000];
					case 17: return [12096000, 1008000];
					case 18: return [12096000, 1008000];
					case 19: return [12096000, 1008000];
					case 20: return [12096000, 1008000];
				}
			case 3:
				switch($lvl){
					case 1: return [360, 180];
					case 2: return [704, 336];
					case 3: return [1320, 600];
					case 4: return [2400, 1152];
					case 5: return [4800, 2400];
					case 6: return [9200, 4560];
					case 7: return [16320, 8352];
					case 8: return [30600, 15300];
					case 9: return [73920, 36000];
					case 10: return [165600, 79920];
					case 11: return [332160, 164160];
					case 12: return [748800, 371520];
					case 13: return [1497600, 748800];
					case 14: return [2880000, 1404000];
					case 15: return [6048000, 3024000];
					case 16: return [6048000, 3024000];
					case 17: return [6048000, 3024000];
					case 18: return [6048000, 3024000];
					case 19: return [6048000, 3024000];
					case 20: return [6048000, 3024000];
				}
		}
	}

	public static function getUnit($unit, $lvl){
		
	}

	public static function getGlobalMaxResources($resource, $fortlvl){
		// return 10000000 * $fortlvl / $resource;
		$resource --;
		switch($fortlvl){
			case 1: return [900, 300][$resource];
			case 2: return [1760, 560][$resource];
			case 3: return [3300, 1000][$resource];
			case 4: return [6000, 1920][$resource];
			case 5: return [12000, 4000][$resource];
			case 6: return [23000, 7600][$resource];
			case 7: return [40800, 13920][$resource];
			case 8: return [76500, 25500][$resource];
			case 9: return [184800, 60000][$resource];
			case 10: return [414000, 133200][$resource];
			case 11: return [830400, 273600][$resource];
			case 12: return [1872000, 619200][$resource];
			case 13: return [3744000, 1248000][$resource];
			case 14: return [7200000, 2340000][$resource];
			case 15: return [15120000, 5040000][$resource];
			case 16: return [27350000, 9000000][$resource];
			case 17: return [50000000, 17500000][$resource];
			case 18: return [90000000, 30000000][$resource];
			case 19: return [165000000, 54000000][$resource];
			case 20: return [320000000, 120000000][$resource];
		}
	}

	public static function getSearchGemPrice(){
		//time/gold/wood/stone
		// $this->lvls[4]
		return [1800, 1000, 10, 5];
		// return [1800, 100, 420, 69];
		$lvl = 1;
		switch($lvl){
			case 1: return [600, 150, 10, 1];
			case 2: return [600, 300, 40, 12];
			case 3: return [600, 450, 117, 18];
			case 4: return [600, 600, 293, 44];
			case 5: return [600, 750, 667, 106];
			case 6: return [600, 900, 1600, 266];
			case 7: return [600, 1050, 3578, 591];
			case 8: return [600, 1200, 7253, 1237];
			case 9: return [600, 1350, 15300, 2550];
			case 10: return [600, 1500, 36960, 6000];
			case 11: return [600, 1650, 82800, 13320];
			case 12: return [600, 1800, 166080, 27360];
			case 13: return [600, 1950, 374400, 61920];
			case 14: return [600, 2100, 640000, 180000];
			case 15: return [600, 2250, 1440000, 468000];
		}
	}
}

?>
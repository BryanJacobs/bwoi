<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * BattleConWoI implementation : © <Bryan Jacobs & Craig Lavine> <raptorbonz42@gmail.com>
 * 
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * material.inc.php
 *
 * BattleConWoI game material description
 *
 * Here, you can describe the material of your game with PHP variables.
 *   
 * This file is loaded in your game logic class constructor, ie these variables
 * are available everywhere in your game logic code.
 *
 */


/*

Example:

$this->card_types = array(
    1 => array( "card_name" => ...,
                ...
              )
);

*/

// it is unclear to me if this belongs in this file or in the battlecon.game.php file
class Pair {
	public function Pair($name, $dist, $prox, $power, $priority, $rev, $ong $sob, $bfa, $onh, $ond, $afa, $eob, $stun, $soak, $nature){
		$this->name = $name;
		$this->range = array($dist, $prox) ;
		$this->power = $power;
		$this->priority = $priority;
		$this->effects = array($rev, $ong $sob, $bfa, $onh, $ond, $afa, $eob);
		$this->stun = $stun;
		$this->soak = $soak;
		$this->nature = $nature;
	}
}

// building cards. was not sure if I could build a new card like I did the
// dash here. 
$dash = new Pair($name = "Dash", $priority = 9, $afa = "move funtion 1/2/3" . "dash ability", $nature =  "base" );
$grasp = new Pair("Grasp", 1, 1, 2, 5, "", "", "", "", "moveO function 1", "", "", "", "", "", "base");
$drive = new Pair("Drive", 1, 1, 3, 4, "", "", "", "advance function 1/2", "", "", "", "", "", "", "base");
$strike = new Pair("Strike", 1, 1, 4, 3, "", "", "", "", "", "", "", "", "5", "", "base");
$shot = new Pair("Shot", 4, 1, 3, 2, "", "", "", "", "", "", "", "", "2", "", "base");
$burst = new Pair("Burst", 3, 2, 3, 1, "", "", "", "", "retreat function 1/2", "", "", "", "", "", "base");




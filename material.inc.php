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
class BaseCard {
	public function BaseCard($name, $proxRange = 0, $distRange = 0, $power = 0, $priority = 0, $stun = 0, $soak = 0, $isBase = False) {
		$this->name = $name;
		$this->range = array($proxRange, $distRange);
		$this->power = $power;
		$this->priority = $priority;
		$this->stun = $stun;
		$this->soak = $soak;
		$this->isBase = $isBase;
	}

	public function revealEffects() {
	}

	public function ongoingEffects() {
	}

	public function startOfBeatEffects() {
	}

	public function beforeActivatingEffects() {
	}

	public function onHitEffects() {
	}

	public function onDamageEffects() {
	}

	public function afterActivatingEffects() {
	}

	public function endOfBeatEffects() {
	}
}

// Generic Bases
$dash = new BaseCard($name="Dash", $priority=9, $isBase=True);
$grasp = new BaseCard($name="Grasp", $proxRange=1, $distRange=1, $power=2, $priority=5, $isBase=True);
$drive = new BaseCard($name="Drive", $proxRange=1, $distRange=1, $power=3, $priority=4, $isBase=True);
$strike = new BaseCard($name="Strike", $proxRange=1, $distRange=1, $power=4, $priority=3, $stun=5, $isBase=True);
$shot = new BaseCard($name="Shot", $proxRange=1, $distRange=4, $power=3, $priority=2, $stun=2, $isBase=True);
$burst = new BaseCard($name="Burst", $proxRange=2, $distRange=3, $power=3, $priority=1, $isBase=True);

//Cadenza's Kit (use this for first character work)
$hydraulic = new BaseCard($name="Hydraulic", $power=2, $priority=-1, $soak=1);
$battery = new BaseCard($name="Battery", $power=1, $priority=-1);
$clockwork = new BaseCard($name="Clockwork", $power=3, $priority=-3, $soak=3);
$grapnel = new BaseCard($name="Grapenel", $proxRange=2, $distRange=4);
$mechanical = new BaseCard($name="Mechanical", $power=2, $priority=-2);
$press = new BaseCard($name="Press", $proxRange=1, $distRange=2, $power=1, $stun=6, $isBase=True);

//Vanaah Kalmor's Kit
$reaping = new BaseCard($name="Reaping", $proxRange=0, $distRange=1, $priority=1);
$glorious = new BaseCard($name="Glorious", $power=2);
$judgement = new BaseCard($name="Judgment", $proxRange=1, $distRange =1, $power=1, $priority=-1);
$vengance = new BaseCard($name="Vengance", $power=2, $stun=4);
$paladin = new BaseCard($name="Paladin", $proxRange=0, $distRange=1, $power=1, $priority=-2, $stun=3);
$scyth = new BaseCard($name="Scyth", $proxRange=1, $distRange=2, $power=3, $priority=3, $stun=3, $isBase=True);

//Luc Von Gott's Kit
$chrono = new BaseCard($name="Chrono", $priority=1);
$eternal = new BaseCard($name="Eternal", $priority=-4, $soak=1);
$memento = new BaseCard($name="Memento", $priority=-1);
$fusion = new BaseCard($name="Fusion", $priority=1);
$feinting = new BaseCard($name="Feinting", $proxRange=1, $distRange=1, $priority=-2);
$flash = new BaseCard($name="Flash", $proxRage=1, $distRange=1, $power=1, $priority=6, $isBase=True);

//Cherri Seneca's Kit
$dreamscape = new  BaseCard($name="Dreamscape", $power=-1, $priority=1);
$crimson = new BaseCard($name="Crimson", $distRange=1, $power= -1);
$catatonic = new BaseCard($name="Catatonic", $priority=-2, $stun=3, $soak=1);
$mirage = new BaseCard($name="Mirage", $power=-1);
$blind = new BaseCard($name="Blind", $distRange=1, $priority=-1);
$stare = new BaseCard($name="Stare", $proxRange=1, $distRange=3, $power=2, $isBase=True);

//Demitras Desnigrande's Kit
$darkside = new BaseCard($name= "Darkside", $power=-2, $priority=1);
$jousting = new BaseCard($name="Jousting", $power=-2, $priority=1);
$bloodletting = new BaseCard($name="Bloodletting", $power=-2 $priority=3);
$illusory = new BaseCard($name="Illusory", $power=-1, $priority=1);
$vapid = new BaseCard($name="Vapid", $distRange=1, $power=-1);
$deathblow = new BaseCard($name="Deathblow", $proxRange=1, $distRange=1, $priority=8, $isBase=True);

//Hepzibah Culotre's Kit
$pactbond = new BaseCard($name="Pactbond", $power=-1, $priority=-1);
$darkheart = new BaseCard($name="Darkheart", $priority=-1);
$anathema = new BaseCard($name="Anathema", $power=-1, $priority=-1);
$accursed = new BaseCard($name="Accursed", $distRange=1, $power=-1);
$necrotizing = new BaseCard($name="Necrotizing", $distRange=2, $power=-1);
$bloodlight = new BaseCard($name="Bloodlight", $proxRange=1, $distRange=3, $power=2, $priority=3);


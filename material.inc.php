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

function registerEvent($eventType, $eventAction, $extraEventData=NULL) {
}

abstract class Events {
    const REVEAL = 0;
    const STARTOFBEAT = 1;
    const BEFOREACTIVATING = 2;
    const ONHIT = 3;
    const ONDAMAGE = 4;
    const AFTERACTIVATING = 5;
    const ENDOFBEAT = 6;
    const ANTE = 7;
}

// it is unclear to me if this belongs in this file or in the battlecon.game.php file
class BaseCard {
    public function BaseCard($name, $proxRange = 0, $distRange = 0, $power = 0, $priority = 0, $stun = 0, $soak = 0, $isBase = False, $events=array()) {
        $this->name = $name;
        $this->range = array($proxRange, $distRange);
        $this->power = $power;
        $this->priority = $priority;
        $this->stun = $stun;
        $this->soak = $soak;
        $this->isBase = $isBase;

        foreach ($events as $eventType => $eventAction) {
            registerEvent($eventType, $eventAction);
        }
    }
}

class Character {
    public function Character() {
    }
}

// Generic Bases
$dash = new BaseCard($name="Dash", $priority=9, $isBase=True);
$grasp = new BaseCard($name="Grasp", $proxRange=1, $distRange=1, $power=2, $priority=5, $isBase=True);
$drive = new BaseCard($name="Drive", $proxRange=1, $distRange=1, $power=3, $priority=4, $isBase=True);
$strike = new BaseCard($name="Strike", $proxRange=1, $distRange=1, $power=4, $priority=3, $stun=5, $isBase=True);
$shot = new BaseCard($name="Shot", $proxRange=1, $distRange=4, $power=3, $priority=2, $stun=2, $isBase=True);
$burst = new BaseCard($name="Burst", $proxRange=2, $distRange=3, $power=3, $priority=1, $isBase=True);

class Cadenza extends Character {
    public function gameStart() {
        // TODO: Set tokens to 3

        registerEvent(Events::ANTE, $self->ante);
        registerEvent(Events::ONDAMAGE, $self->onDamageEffects);
    }

    public function ante() {
        // TODO: Present option to ante a token
    }

    public function onDamageEffects() {
        // TODO: Present option for using a token
    }
}

function getAdvancer($distanceLow, $distanceHigh) {
    $ret = function($eventDetails, $extraData) {
        // TODO: Iff this is for me, advance by some distance
    };
    return $ret;
}

function setNextBeatRelativePriority($priorityModifier) {
    $ret = function($eventDetails, $extraData) {
        // TODO: register a next-beat one-time priority boost or penalty
    };
    return $ret;
}

function getPuller($distanceLow, $distanceHigh) {
    $ret = function($eventDetails, $extraData) {
        // TODO: Pull someone by a distance
    };
    return $ret;
}

$cardRegistry = array();

//Cadenza's Kit (use this for first character work)

$hydraulic = new BaseCard($name="Hydraulic", $power=2, $priority=-1, $soak=1,
                          $events=array(Events::BEFOREACTIVATING => getAdvancer(1, 1)));

$battery = new BaseCard($name="Battery", $power=1, $priority=-1,
                    $events=array(Events::ENDOFBEAT => setNextBeatRelativePriority(4)));

$clockwork = new BaseCard($name="Clockwork", $power=3, $priority=-3, $soak=3);
$grapnel = new BaseCard($name="Grapenel", $proxRange=2, $distRange=4,
                        $events=array(Events::ONHIT => getPuller(0, 3)));

$mechanical = new BaseCard($name="Mechanical", $power=2, $priority=-2,
                          $events=array(Events::ENDOFBEAT => getAdvancer(0, 3)));

$increasePressDamage = function($eventDetails, $extraData) {
    // TODO: increase power by the amount of damage taken, iff I just took damage
};
$resetPressDamage = function($eventDetails, $extraData) {
    // TODO: reset press power to one
};

$press = new BaseCard($name="Press", $proxRange=1, $distRange=2, $power=1, $stun=6, $isBase=True,
                      $events=array(Events::ONDAMAGE => $increasePressDamage,
                                    Events::ENDOFBEAT => $resetPressDamage));
//+1 power for each point of damage taken

$cardRegistry["Cadenza"] = array($hydraulic, $battery, $clockwork, $grapnel, $mechanical, $press);

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
$bloodletting = new BaseCard($name="Bloodletting", $power=-2, $priority=3);
$illusory = new BaseCard($name="Illusory", $power=-1, $priority=1);
$vapid = new BaseCard($name="Vapid", $distRange=1, $power=-1);
$deathblow = new BaseCard($name="Deathblow", $proxRange=1, $distRange=1, $priority=8, $isBase=True);

//Hepzibah Culotre's Kit
$pactbond = new BaseCard($name="Pactbond", $power=-1, $priority=-1);
$darkheart = new BaseCard($name="Darkheart", $priority=-1);
$anathema = new BaseCard($name="Anathema", $power=-1, $priority=-1);
$accursed = new BaseCard($name="Accursed", $distRange=1, $power=-1);
$necrotizing = new BaseCard($name="Necrotizing", $distRange=2, $power=-1);
$bloodlight = new BaseCard($name="Bloodlight", $proxRange=1, $distRange=3, $power=2, $priority=3, $isBase=True);

//Hikaru Sorayama's Kit
$trance = new BaseCard($name="Trance", $distRange=1);
$geomantic = new BaseCard($name="Geomantic", $power=1);
$focused = new BaseCard($name="Focused", $priority=1, $stun=2);
$advancing = new BaseCard($name="Advancing", $power=1, $priority=1);
$sweeping = new BaseCard($name="Sweeping", $power=-1, $priority=3);
$palmStrike = new BaseCard($name="Palm Strike", $proxRange=1, $distRange=1, $power=2, $priority=5, $isBase=True);

//Kallistar Flarechild's Kit
$flare = new BaseCard($name="Flare", $power=3);
$ignition = new BaseCard($name="Ignition", $power=1, $priority=-1);
$caustic = new BaseCard($name="Caustic", $power=1, $priority=-1, $soak=2);
$blazing = new BaseCard($name="Blazing", $priority=1);
$volcanic = new BaseCard($name="Volcanic", $proxRange=2, $distRange=4);
$spellbolt = new BaseCard($name="Spellbolt", $proxRange=2, $distRange=6, $power=2, $priority=3, $isBase=True);

//Kehrolyn Ross's Kit
$mutating = new BaseCard($name="Mutating");
$whip = new BaseCard($name="Whip", $distRange=1);
$bladed = new BaseCard($name="Bladed", $power=2, $stun=2);
$exoskeletal = new BaseCard($name="Exoskelital", $soak=2);
$quicksilver = new BaseCard($name="Quicksilver", $priority=2);
$overload = new BaseCard($name="Overload", $proxRange=1, $distRange=1, $power=3, $priority=3, $isBase=True);

//Khadath Ahemusei's Kit
$evacuation = new BaseCard($name="Evacuation", $distRange=1);
$hunters = new BaseCard($name="Hunter's");
$teleport = new BaseCard($name="Teleport", $power=1, $priority=-3);
$lure = new BaseCard($name="Lure", $distRange=5, $power=-1, $priority=-1);
$blight = new BaseCard($name="Blight", $distRange=2);
$snare = new BaseCard($name="Snare", $power=3, $priority=1, $isBase=True);

//Lixis Ran Kanda's Kit
$pruning = new BaseCard($name="Pruning", $distRange=1, $power=-1, $priority=-2);
$venomous = new BaseCard($name="Venomous", $power=1, $stun=2);
$rooted = new BaseCard($name="Rooted", $proxRange=-1, $power=1, $priority=-2, $soak=2);
$naturalizing = new BaseCard($name="Naturalizing", $distRange=1, $power=-1, $priority=1);
$vine = new BaseCard($name="Vine", $distRane=2, $priority=-2, $stun=3);
$lance = new BaseCard($name="Lance", $proxRange=2, $distRange=2, $power=3, $priority=5, $isBase=True);

//Luc Von Gott's Kit
$chrono = new BaseCard($name="Chrono", $priority=1);
$eternal = new BaseCard($name="Eternal", $priority=-4, $soak=1);
$memento = new BaseCard($name="Memento", $priority=-1);
$fusion = new BaseCard($name="Fusion", $priority=1);
$feinting = new BaseCard($name="Feinting", $proxRange=1, $distRange=1, $priority=-2);
$flash = new BaseCard($name="Flash", $proxRage=1, $distRange=1, $power=1, $priority=6, $isBase=True);


//Magdelina Larington's Kit
$spiritual = new BaseCard($name="Spiritual", $power=1, $priority=1);
$sanctimonious = new BaseCard($name="Sanctimonious", $power=-1, $priority=-2);
$priestess = new BaseCard($name="Priestess", $power=-2, $priority=-1);
$saftey = new BaseCard($name="Saftey", $power=-2, $priority=-1);
$excelsius = new BaseCard($name="Excelsius", $distRange=1, $power=-2, $priority=-1);
$blessing = new BaseCard($name="Blessing", $proxRange=1, $distRange=2, $priority=3, $stun=3);

//Regicide Heketch's Kit
$merciless = new BaseCard($name="Merciless", $distRange=1, $power=-1);
$critical = new BaseCard($name="Critical", $power=-1, $priority=1);
$rasping = new BaseCard($name="Rasping", $distRange=1, $power=-1, $priority=1);
$assassin = new BaseCard($name="Assassin");
$psycho = new BaseCard($name="Psycho", $priority=1);
$knives = new BaseCard($name="Knives", $proxRange=1, $distRange=2, $power=4, $priority=5, $isBase=True);

//Rukyuk Amberdeen's Kit
$gunner = new BaseCard($name="Gunner", $proxRange=2, $distRange=4);
$sniper = new BaseCard($name="Sniper", $proxRange=3, $distRange=5, $power=1, $priority=2);
$pointBlank = new BaseCard($name="Point Blank", $distRange=1, $stun=2);
$trick = new BaseCard($name="Trick", $proxRange=1, $distRange=2, $priority=-3);
$crossfire = new BaseCard($name="Crossfire", $proxRange=2, $distrange=3, $priority=-2, $soak=2);
$reload = new BaseCard($name="Reload", $priority=4, $isBase=True);

//Sagas Seities's Kit
$negation = new BaseCard($name="Negation");
$echo = new BaseCard($name="Echo");
$repelling = new BaseCard($name="Repelling", $proxRange=1, $distRange=2, $power=-1, $priority=1);
$shadow = new BaseCard($name="Shadow", $distRange=1, $power=1);
$shattering = new BaseCard($name="Shattering", $proxRange=1, $distRange=2, $power=1, $priority=-1, $stun=2);
$staff = new BaseCard($name="Staff", $proxRange=1, $distRange=2, $power=3, $priority=4, $stun=4, $isBase=True);

//Seth Cremmul's Kit
$compelling = new BaseCard($name="Compelling");
$fools = new BaseCard($name="Fool's", $power=-1, $priority=-2);
$mimics = new BaseCard($name="Mimic's", $power=1);
$wyrding = new BaseCard($name="Wyrding");
$vanishing = new BaseCard($name="Vanishing", $proxRange=1, $distRange=1);
$omen = new BaseCard($name="Omen", $proxRange=1, $distRange=1, $power=3, $priority=1, $isBase=True);

//Tatsumi Nuac's Kit
$empathic = new BaseCard($name="Empathic", $priority=-1);
$siren = new BaseCard($name="Siren", $power=-1, $priority=1);
$fearless = new BaseCard($name="Fearless", $proxRange=-1, $priority=1);
$wave = new BaseCard($name="Wave", $proxRange=2, $distRange=4, $power=-1);
$riptide = new BaseCard($name="Riptide", $distRange=2, $priority=-1);
$whirlpool = new BaseCard($name="Whirlpool", $proRange=1, $distRange=2, $power=3, $priority=3, $isBase=True);

//Vanaah Kalmor's Kit
$reaping = new BaseCard($name="Reaping", $proxRange=0, $distRange=1, $priority=1);
$glorious = new BaseCard($name="Glorious", $power=2);
$judgement = new BaseCard($name="Judgment", $proxRange=1, $distRange =1, $power=1, $priority=-1);
$vengance = new BaseCard($name="Vengance", $power=2, $stun=4);
$paladin = new BaseCard($name="Paladin", $proxRange=0, $distRange=1, $power=1, $priority=-2, $stun=3);
$scyth = new BaseCard($name="Scyth", $proxRange=1, $distRange=2, $power=3, $priority=3, $stun=3, $isBase=True);

//Zaamassel Kett's Kit
$urgent = new BaseCard($name="Urgent", $distRange=1, $power=1, $priority=2);
$malicious = new BaseCard($name="Malicious", $power=1, $priority=-1, $stun=2);
$sinuous = new BaseCard($name="Sinuous", $priority=1);
$warped = new BaseCard($name="Warped", $distRange=2);
$sturdy = new BaseCard($name="Sturdy");
$paradigmShift = new BaseCard($name="Paradigm Shift", $proxRange=2, $distRange=3, $power=3, $priority=3, $isBase=True);



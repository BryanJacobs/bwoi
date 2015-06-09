<?php

function registerEvent($eventType, $eventAction, $extraEventData=NULL) {
//TODO generate an actual dictionary item here
}

abstract class CardEvents {
    const REVEAL = 0;
    const STARTOFBEAT = 1;
    const BEFOREACTIVATING = 2;
    const ONHIT = 3;
    const ONDAMAGE = 4;
    const AFTERACTIVATING = 5;
    const ENDOFBEAT = 6;
    const ANTE = 7;
    const RECYCLE = 8;
    const GAMESTART = 9;
}

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
$dash = new BaseCard($name="Dash", $priority=9, $isBase=True, 
                                $events=array(CardEvents::AFTERACTIVATING => getDasher(-3,3),
                                              CardEvents::ONHIT => hitFail()));
$grasp = new BaseCard($name="Grasp", $proxRange=1, $distRange=1, $power=2, $priority=5, $isBase=True,
                                 $events=array(CardEvents::ONHIT => getPuller(-1,1)));
$drive = new BaseCard($name="Drive", $proxRange=1, $distRange=1, $power=3, $priority=4, $isBase=True,
                                 $events=array(CardEvents::BEFOREACTIVATING => getAdvancer(1,2)));
$strike = new BaseCard($name="Strike", $proxRange=1, $distRange=1, $power=4, $priority=3, $stun=5, $isBase=True);
$shot = new BaseCard($name="Shot", $proxRange=1, $distRange=4, $power=3, $priority=2, $stun=2, $isBase=True);
$burst = new BaseCard($name="Burst", $proxRange=2, $distRange=3, $power=3, $priority=1, $isBase=True,
                                  $events=array(CardEvents::STARTOFBEAT => getAdvancer(-2,-1)));

class Cadenza extends Character {
    public function Cadenza() {
        registerEvent(CardEvents::GAMESTART, $self->gameStart);
    }

    public function gameStart($eventDetails, $extraData) {
        $self->ironBodyTokens = 3;

        registerEvent(CardEvents::ANTE, $self->ante);
        registerEvent(CardEvents::ONDAMAGE, $self->onDamageEffects);
    }

    public function ante($eventDetails, $extraData) {
        if ($self->ironBodyTokens > 0) {
            // TODO: present choice of ante to user
            // Iff user antes, then:
            // $self->ironBodyTokens -= 1;
            $self->stunImmune = true;
            registerEvent(CardEvents::RECYCLE, $self->removeStunImmunity);
        }

        return $self->ironBodyTokens > 0;
    }

    public function removeStunImmunity($eventDetails, $extraData) {
        $self->stunImmune = false;
        return false;
    }

    public function removeInfiniteStunGuard($eventDetails, $extraData) {
        $self->stunGuard = 0;
        return false;
    }

    public function onDamageEffects($eventDetails, $extraData) {
        if ($self->ironBodyTokens > 0) {
            // TODO: present choice to use token
            // Iff token used, then:
            // $self->ironBodyTokens -= 1
            // $self->stunGuard = 99999;
            // registerEvent(CardEvents::ENDOFBEAT, $self->removeInfiniteStunGuard);
        }
        return $self->ironBodyTokens > 0;
    }
}
function getDasher($distanceLow, $distanceHigh){
    $ret = function($eventDetails, $extraData) {
            // TODO: Iff this is for me, advance by some distance with each advance check:
        if ($active_player_location == $extradata) {
            registerEvent(CardEvents::ONHIT, hitFail);
        }
    };
    return $ret;
}

function hitFail(){
    return $hitConfirm = false;
}
function getAdvancer($distanceLow, $distanceHigh) {
    $ret = function($eventDetails, $extraData) {
        // TODO: Iff this is for me, advance by some distance
        return true;
    };
    return $ret;
}

function setNextBeatRelativePriority($priorityModifier) {
    $ret = function($eventDetails, $extraData) {
        // TODO: register a next-beat one-time priority boost or penalty
        return true;
    };
    return $ret;
}

function getPuller($distanceLow, $distanceHigh) {
    $ret = function($eventDetails, $extraData) {
        // TODO: Pull someone by a distance
        return true;
    };
    return $ret;
}

function buildCardRegistry() {
    $ret = array();

    //Cadenza's Kit (use this for first character work)

    $hydraulic = new BaseCard($name="Hydraulic", $power=2, $priority=-1, $soak=1,
                              $events=array(CardEvents::BEFOREACTIVATING => getAdvancer(1, 1)));

    $battery = new BaseCard($name="Battery", $power=1, $priority=-1,
                        $events=array(CardEvents::ENDOFBEAT => setNextBeatRelativePriority(4)));

    $clockwork = new BaseCard($name="Clockwork", $power=3, $priority=-3, $soak=3);
    $grapnel = new BaseCard($name="Grapenel", $proxRange=2, $distRange=4,
                            $events=array(CardEvents::ONHIT => getPuller(0, 3)));

    $mechanical = new BaseCard($name="Mechanical", $power=2, $priority=-2,
                              $events=array(CardEvents::ENDOFBEAT => getAdvancer(0, 3)));

    $increasePressDamage = function($eventDetails, $extraData) {
        // TODO: increase power by the amount of damage taken, iff I just took damage
    };
    $resetPressDamage = function($eventDetails, $extraData) {
        // TODO: reset press power to one
    };

    $press = new BaseCard($name="Press", $proxRange=1, $distRange=2, $power=1, $stun=6, $isBase=True,
                          $events=array(CardEvents::ONDAMAGE => $increasePressDamage,
                                        CardEvents::RECYCLE => $resetPressDamage));

    $ret["Cadenza"] = array($hydraulic, $battery, $clockwork, $grapnel, $mechanical, $press);

    return $ret;
}

$cardRegistry = buildCardRegistry();

/*
//Cherri Seneca's Kit
//My activation
$dreamscape = new  BaseCard($name="Dreamscape", $power=-1, $priority=1,
                    $events=array(CardEvents::BEFOREACTIVATING=>getSwitchSides));
//Character specific function
$crimson = new BaseCard($name="Crimson", $distRange=1, $power= -1,
                    $events=array(CardEvents::REVEAL=>getForceClash));
//Character specific function
$catatonic = new BaseCard($name="Catatonic", $priority=-2, $stun=3, $soak=1,
                    $events=array(CardEvents::ENDOFBEAT=>getInsightToken));
//Character specific function (function 2), My activation
$mirage = new BaseCard($name="Mirage", $power=-1,
                    $events=array(CardEvents::REVEAL=>getSetFoeStyle($priority, 0),
                                  CardEvents::AFTERACTIVATINg=>getMirage));
//Character Specificfunction My activation
$blind = new BaseCard($name="Blind", $distRange=1, $priority=-1, 
                    $events=array(CardEvents::AFTERACTIVATING=>getBlind));
$stare = new BaseCard($name="Stare", $proxRange=1, $distRange=3, $power=2, $isBase=True, 
                    $events=array(CardEvents::REVEAL=>getStare));


//Demitras Desnigrande's Kit
//I need to know how much I anteed eachturn
// when I get hit for 1; when I hit for 2
$darkside = new BaseCard($name= "Darkside", $power=-2, $priority=1,
                    $events=array(CardEvents::ONHIT=>getForceShort(4),
                                  CardEvents::ONHIT=>getadvancer(-9,0)));
//when I hit
$jousting = new BaseCard($name="Jousting", $power=-2, $priority=1, 
                    $events=array(CardEvents::STAROFBEAT=>getMakeAdjacent,
                                  CardEvents::ONHIT=>getJousting));
//When I hit both on hits; need to store number of anteed crescendo tokens
$bloodletting = new BaseCard($name="Bloodletting", $power=-2, $priority=3,
                    $events=array(CardEvents::ONHIT=>getSetFoePair($soak, 0),
                                  CardEvents::ONHIT=>GetBloodletting));
$illusory = new BaseCard($name="Illusory", $power=-1, $priority=1,
                    $events=array(CardEvents::REVEAL=>getIllusory));
//when I hit
$vapid = new BaseCard($name="Vapid", $distRange=1, $power=-1,
                    $events=array(CardEvents::ONHIT=>getVapid));
//my Activation: both events
$deathblow = new BaseCard($name="Deathblow", $proxRange=1, $distRange=1, $priority=8, $isBase=True,
                    $events=array(CardEvents::ONHIT=>getDeathblowSpend,
                                  CardEvents::AFTERACTIVATING=>getDeathblowGain));

//Hepzibah Culotre's Kit
//I needto know how much I anteed each turn
$pactbond = new BaseCard($name="Pactbond", $power=-1, $priority=-1,
                    $events=array(CardEvents::REVEAL=>getPactboundLife,
                                  CardEvents::ENDOFBEAT=>getPactboundfree));
//when I hit both events
$darkheart = new BaseCard($name="Darkheart", $priority=-1, 
                    $events=array(Event::ONHIT=>getLifeGain(2),
                                  Event::ONHIT=>getDarkheartDiscard));
$anathema = new BaseCard($name="Anathema", $power=-1, $priority=-1
                    $events=array(Event::REVEAL=>getAnathema));
//when I am hit
$accursed = new BaseCard($name="Accursed", $distRange=1, $power=-1,
                    $events=array(Event::ONDAMAGE=>getAccursed));
//when I hit
$necrotizing = new BaseCard($name="Necrotizing", $distRange=2, $power=-1,
                    $events=array(Event::ONHIT=>getNecrotizing));
//When I damage
$bloodlight = new BaseCard($name="Bloodlight", $proxRange=1, $distRange=3, $power=2, $priority=3, $isBase=True,
                    $events=array(Event::ONDAMAGE=>getBloodlight));

//Hikaru Sorayama's Kit
//I can't regain a token I successfully anteed on a given beat
$trance = new BaseCard($name="Trance", $distRange=1,
                    $events=array(Event::REVEAL=>getCancelMyAnte,
                                  Event::ENDOFBEAT=>getGainHikaruToken));
$geomantic = new BaseCard($name="Geomantic", $power=1,
                    $events=array(Event::STARTOFBEAT=>getGeomantic));
//When I hit
$focused = new BaseCard($name="Focused", $priority=1, $stun=2,
                    $events=array(Event::ONHIT=>getGainHikaruToken));
$advancing = new BaseCard($name="Advancing", $power=1, $priority=1,
                    $event=array(Event::STARTOFBEAT=>getAdvancing));
//When I get hit; add damage to their attack
$sweeping = new BaseCard($name="Sweeping", $power=-1, $priority=3,
                    $event=array(Event::ONDAMAGE=>getAdjustFoePair($power, 2));
//When I hit
$palmStrike = new BaseCard($name="Palm Strike", $proxRange=1, $distRange=1, $power=2, $priority=5, $isBase=True,
                    $event=array(Event::STARTOFBEAT=>getAdvancer(1,1),
                                 Event::ONDAMAGE=>getGainHikaruToken));

//Kallistar Flarechild's Kit
//I have to know know either if my elemental Form is on or off
$flare = new BaseCard($name="Flare", $power=3,
                    $event=array(Event::REVEAL=>getCheckForm("flare")
                                 Event::ENDOFBEAT=>getCheckForm("flare")));
//above is one option for Kallistar below is a second with each base handledby itself.
$ignition = new BaseCard($name="Ignition", $power=1, $priority=-1,
                    $event=array(Event::REVEAL=>getIgnition));
//a third option s to move her event tree from here onto her character. will wait for discussion
$caustic = new BaseCard($name="Caustic", $power=1, $priority=-1, $soak=2);
$blazing = new BaseCard($name="Blazing", $priority=1);
$volcanic = new BaseCard($name="Volcanic", $proxRange=2, $distRange=4);
$spellbolt = new BaseCard($name="Spellbolt", $proxRange=2, $distRange=6, $power=2, $priority=3, $isBase=True);

//Kehrolyn Ross's Kit
//I need to trigger my Styles from discard 1
$mutating = new BaseCard($name="Mutating", 
                    $events=array(Event::REVEAL=>getMutating));
//when I hit
$whip = new BaseCard($name="Whip", $distRange=1, 
                    $events=array(Event::ONHIT=>getWhip));
$bladed = new BaseCard($name="Bladed", $power=2, $stun=2);
//This should have record triggers at any time a move effect could occur. is generic
$exoskeletal = new BaseCard($name="Exoskeletal", $soak=2,
                    $events=array(Event:REVEAL=>getIgnoreMove));
$quicksilver = new BaseCard($name="Quicksilver", $priority=2, 
                    $events=array(Evnet::ENDOFBEAT=>getAdvancer(-1,1)));
//need to activate the new styles Reveal effects during STARTOFBEAT
$overload = new BaseCard($name="Overload", $proxRange=1, $distRange=1, $power=3, $priority=3, $isBase=True,
                    $events=array(Event::REVEAL=>getOverloadTie,
                    $events=array(Event::STARTOFBEAT=>getOverloadStyle));

//Khadath Ahemusei's Kit
//I play tokens onto the field that stop my opponent's movement
//note on evacuation: When I am hit
$evacuation = new BaseCard($name="Evacuation", $distRange=1,
                    $events=array(Event::STARTOFBEAT=>getKhadathTrap(0),
                                  Event::STARTOFBEAT=>getAdvancer(-1,-1),
                                  Event::ONHIT=>getEvacuationDodge));
//When I hit
$hunters = new BaseCard($name="Hunter's", 
                    $events=array(Event::REVEAL=>getHuntersPriority,
                                  Event::ONHIT=>getHuntersPower));
//when I am hit
$teleport = new BaseCard($name="Teleport", $power=1, $priority=-3,
                    $events=array(Event::ONHIT=>getTeleportDodge,
                                  Event::ENDOFBEAT=>getDirectMove,
                                  Event::ENDOFBEAT=>getTeleportTrap));
//when I hit; can ignore trap
$lure = new BaseCard($name="Lure", $distRange=5, $power=-1, $priority=-1,
                    $events=array(Event::ONHIT=>getLure));
$blight = new BaseCard($name="Blight", $distRange=2,
                    $events=array(Event::STARTOFBET=>getBlightTrap));
//when I hit for event 2; When I am hit for event 3
$snare = new BaseCard($name="Snare", $power=3, $priority=1, $isBase=True,
                    $events=array(Event::REVEAL=>getCancelTrapEffects));
                                  Event::ONHIT=>getSetRangeOnTrap
                                  Event::ONHIT=>getStunImmune

//Lixis Ran Kanda's Kit
//Character UA when I hit: Event::ONHIT=>getFoeDiscardsBase
//when I hit
$pruning = new BaseCard($name="Pruning", $distRange=1, $power=-1, $priority=-2,
                    $events=array(Event::REVEAl=>getPruningBonus,
                                  Event::ONHIT=>getFoeDiscardsBase));
//My activation: both events
$venomous = new BaseCard($name="Venomous", $power=1, $stun=2,
                    $events=array(Event::BEFOREACTIVATING=>getAdvancer(1,1),
                                  Event::ONHIT=>getFoeNextBeatPairAdjust($priority, -2)));
//can ignore her own movements as well!
$rooted = new BaseCard($name="Rooted", $proxRange=-1, $power=1, $priority=-2, $soak=2,
                    $events=array(Event::REVEAL=>getIgnoreMove,
                                  Event::REVEAL=>getRooted);
//Negate all nearest Foe's ante effects. players cannot spend tokens/counters; pow/pri/range<=printed pair
$naturalizing = new BaseCard($name="Naturalizing", $distRange=1, $power=-1, $priority=1,
                    $events=array(Event::REVEAL=>getNaturalizing));
//when I hit
$vine = new BaseCard($name="Vine", $distRane=2, $priority=-2, $stun=3,
                    $events=array(Event:ONHIT=>getPuller(0,2)));
//opponents canot create adjacency
$lance = new BaseCard($name="Lance", $proxRange=2, $distRange=2, $power=3, $priority=5, $isBase=True,
                    $events=array(Event::REVEAL=>getLance));

//Luc Von Gott's Kit
//My ante effects are not cumulative
//advance 1 per token spet not passed opponent
$chrono = new BaseCard($name="Chrono", $priority=1,
                    $events=array(Event::STARTOFBEAT=>getChrono));
//when I am hit gain soak=tokens spent
$eternal = new BaseCard($name="Eternal", $priority=-4, $soak=1,
                    $events=array(Event::ONHIT=>getEternal));
//one time: 2 tokens to perform attack again
$memento = new BaseCard($name="Memento", $priority=-1,
                    $events=array(Event::AFTERACTIVATING=>getMemento));
//when I deal damage
$fusion = new BaseCard($name="Fusion", $priority=1,
                    $events=array(Event::ONDAMAGE=>getFusion));
$feinting = new BaseCard($name="Feinting", $proxRange=1, $distRange=1, $priority=-2,
                    $events=array(Event::STARTOFBEAT=>getAdvancer(-1,-1),
                                  Event::ENDOFBEAT=>getAdvancer(1,2)));
//when I hit
$flash = new BaseCard($name="Flash", $proxRage=1, $distRange=1, $power=1, $priority=6, $isBase=True,
                    $events=array(Event::STARTOFBEAT=>getAdvancer(1,1),
                                  Event::ONHIT=>getAdjustFoePair($stun,0)));


//Magdelina Larington's Kit
//UA:check if level advance, then if no advance, gain token
$spiritual = new BaseCard($name="Spiritual", $power=1, $priority=1,
                    $events=array(Event::ENDOFBEAT=>getCancelMyUA));
//when I hit
$sanctimonious = new BaseCard($name="Sanctimonious", $power=-1, $priority=-2,
                    $events=array(Event::ONHIT=>getSanctimoniousRange));
//when I activate: both events
$priestess = new BaseCard($name="Priestess", $power=-2, $priority=-1,
                    $events=array(Event::ONHIT=>getAdjustFoePair($power, -2),
                                  Event::AFTERACTIVATING=>getLifeGain(1)));
//when I am damaged
$saftey = new BaseCard($name="Saftey", $power=-2, $priority=-1,
                    $events=array(Event::ONDAMAGE=>getSafetyDamageAdjust,
                                  Event::ENDOFBEAT=>getSafetyMove));
//my activation: both events
$excelsius = new BaseCard($name="Excelsius", $distRange=1, $power=-2, $priority=-1,
                    $events=array(Event::BEFOREACTIVATING=>getAdvancer(1,1),
                                  Event::ONHIT=>getExcelsius));
//when I hit
$blessing = new BaseCard($name="Blessing", $proxRange=1, $distRange=2, $priority=3, $stun=3,
                    $events=array(Event::ONHIT=>getBlessing));

//Regicide Heketch's Kit
//I gain my token at the end of beat if my oppponent is far away
//event 2: my activation
$merciless = new BaseCard($name="Merciless", $distRange=1, $power=-1,
                    $events=array(Event::REVEAL=>getMercilessMoveStop,
                                  Event::AFTERACTIVATING=>getMercilessDodge));
//my activtion: both events
$critical = new BaseCard($name="Critical", $power=-1, $priority=1,
                    $events=array(Event::ONHIT=>getHeketchDamage,
                                  Event::ONDAMAGE=>getAdjustFoePair($stun,0)));
//my acivation: both events
$rasping = new BaseCard($name="Rasping", $distRange=1, $power=-1, $priority=1,
                    $events=array(Event::ONHHIT=>getHeketchDamage,
                                  Event::ONDAMAGE=>getRaspingHeal));
//my activation
$assassin = new BaseCard($name="Assassin",
                    $events=array(Event::ONHIT=>getAdvancer(-9,0),
                                 Event::ONDAMAGE=>getAssassin));
$psycho = new BaseCard($name="Psycho", $priority=1,
                    $events=array(Event::STARTOFBEAT=>getMakeAdjacent,
                                 Event::ENDOFBEAT=>getPsycho));
//my activation
$knives = new BaseCard($name="Knives", $proxRange=1, $distRange=2, $power=4, $priority=5, $isBase=True
                    $events=array(Event::REVEAL=>getKnivesClash,
                                  Event::ONHIT=>getKnivesStun));

//Rukyuk Amberdeen's Kit
//not move 0
$gunner = new BaseCard($name="Gunner", $proxRange=2, $distRange=4,
                    $events=array((Event::BEFOREACTIVATING=>getGunnerRange,
                                   Event::AFTERACTIVATING=>getAdvancer(-2,2)))
//not move 0
$sniper = new BaseCard($name="Sniper", $proxRange=3, $distRange=5, $power=1, $priority=2,
                    $events=array(Event::AFTERACTIVATING=>getAdvancer(-3,3)));
//my activation
$pointBlank = new BaseCard($name="Point Blank", $distRange=1, $stun=2,
                    $events=array(Event::ONDAMAGE=>getPuller(-2,0)));
//When I am damaged
$trick = new BaseCard($name="Trick", $proxRange=1, $distRange=2, $priority=-3,
                    $events=array(Event::ONDAMAGE=>getStunImmune));
//when I hit
$crossfire = new BaseCard($name="Crossfire", $proxRange=2, $distrange=3, $priority=-2, $soak=2,
                    $events=array(Event::ONHIT=>getCrossfire));
//when I activate
$reload = new BaseCard($name="Reload", $priority=4, $isBase=True,
                    $events=array(Event::ONHIT=>getHitFail,
                                  Event::AFTERACTIVATING=>getDierectMove,
                                  Event::ENDOFBEAT=>getReload));

//Sagas Seities's Kit
//I need to know hat my opponent has played and is playing
//only ENDOFBEAT from opp styles, and if you have mirror win priority
$negation = new BaseCard($name="Negation", 
                    $events=array(Event::REVEAL=>getNegtion));
//gain a style in Foe's discard. ignore effects with their name
$echo = new BaseCard($name="Echo",
                    $events=array(event::REVEAL=>getEcho));
$repelling = new BaseCard($name="Repelling", $proxRange=1, $distRange=2, $power=-1, $priority=1,
                    $events=array(Event::STARTOFBEAT=>getRepelling));
$shadow = new BaseCard($name="Shadow", $distRange=1, $power=1,
                    $events=array(Event::REVEAL=>getShadowWrap,
                                  Event::BEFOREACTIVATING=>getAdvancer(1,1),
                                  Event::ENDOFBEAT=>getShadowAttack));
//my activation
$shattering = new BaseCard($name="Shattering", $proxRange=1, $distRange=2, $power=1, $priority=-1, $stun=2,
                    $events=array(Event::ONHIT=>getShatteringStun,
                                  Event::ONDAMAGE=>getSetFoePair($stun, 0),
                                  Event::ENDOFBEAT=>getShatteringCancel));
//my activation
$staff = new BaseCard($name="Staff", $proxRange=1, $distRange=2, $power=3, $priority=4, $stun=4, $isBase=True,
                    $events=array(Event::REVEAL=>getStaffCopy,
                                  Event::ONHIT=>getStaffRoot
                                  Event::ONHIT=>getPuller(-1,-1),
                                  Event::ONDAMAGE=>getStaffNoStun));

//Seth Cremmul's Kit
//I need to name a base in my opponents hand
//my activation
$compelling = new BaseCard($name="Compelling",
                    $events=array(Event::BEFOREACTIVATING=>getPuller(-1,1),
                                  Event::AFTERACTIVATING=>getPuller(-1,1)));
$fools = new BaseCard($name="Fool's", $power=-1, $priority=-2,
                    $events=array(Event::STARTOFBEAT=>getAdjustFoePair($proxrange, -1),
                                  Event::STARTOFBEAT=>getAdjustFoePair($distrange, -1));
//move exactly as opponent moves if able
$mimics = new BaseCard($name="Mimic's", $power=1,
                    $events=array(Event::REVEAL=>getMimic));
//add an additional base to discard
$wyrding = new BaseCard($name="Wyrding",
                    $events=array(Event::STARTOFBEAT=>getWyrding));
//when I am hit
$vanishing = new BaseCard($name="Vanishing", $proxRange=1, $distRange=1,
                    $events=array(Event::ONHIT=>getForceShort(4),
                                  Event::STARTOFBEAT=>getAdvancer(-1,-1)));
$omen = new BaseCard($name="Omen", $proxRange=1, $distRange=1, $power=3, $priority=1, $isBase=True,
                    $events=array(Event::STARTOFBEAT=>getOmen));

//Tatsumi Nuac's Kit
//I need to know the relative position of Juto
$empathic = new BaseCard($name="Empathic", $priority=-1,
                    $events=array(Event::STARTOFBEAT=>getEmpathicSwap,
                                  Event::ENDOFBEAT=>getEmpathicDamage));
//when I hit
$siren = new BaseCard($name="Siren", $power=-1, $priority=1,
                    $events=array(Event::ONHIT=>getStunner,
                                  Event::ENDOFBEAT=>getJutoMove(-2,2)));
//when I hit
$fearless = new BaseCard($name="Fearless", $proxRange=-1, $priority=1,
                    $events=array(Event::ONHIT=>getFearlessRangeCheck,
                                  Event::ENDOFBEAT=>getSetJuto));
//my activation
$wave = new BaseCard($name="Wave", $proxRange=2, $distRange=4, $power=-1,
                    $events=array(Event::ONHIT=>getPuller(-2,0),
                                  Event::AFTERACTIVATING=>getJutoMove(0,9)));
$riptide = new BaseCard($name="Riptide", $distRange=2, $priority=-1,
                    $events=array(Event::STARTOFBEAT=>getRiptideDodge,
                                  Event::ENDOFBEAT=>getRiptideJutoMove));
//my activation
$whirlpool = new BaseCard($name="Whirlpool", $proRange=1, $distRange=2, $power=3, $priority=3, $isBase=True,
                    $events=array(Event::STARTOFBEAT=>getWhirlpoolPull,
                                  Event::AFTERACTIVATING=>getAdvancer(-2,2),
                                  Event::AFTERACTIVATING=>getJutoMove(-2,2)));

//Vanaah Kalmor's Kit
//my token cycles with its attack pair, but can return early
//when i hit
$reaping = new BaseCard($name="Reaping", $proxRange=0, $distRange=1, $priority=1,
                    $events=array(Event::ONHIT=>getReaping));
//my activtion
$glorious = new BaseCard($name="Glorious", $power=2,
                    $events=array(Event::ONHIT=>getGloriousMiss,
                                  Event::BEFOREACTIVATING=>getAdvancer(1,1)));
//affects all movements of foe
$judgement = new BaseCard($name="Judgment", $proxRange=1, $distRange =1, $power=1, $priority=-1,
                    $events=array(Event::REVEAL=>getJudgment));
//my activation
$vengance = new BaseCard($name="Vengance", $power=2, $stun=4,
                    $events=array(Event::ONHIT=>getVenganceMiss));
$paladin = new BaseCard($name="Paladin", $proxRange=0, $distRange=1, $power=1, $priority=-2, $stun=3,
                    $events=array(Event::ENDOFBEAT=>getPaladinTeleport));
//my activation
$scyth = new BaseCard($name="Scyth", $proxRange=1, $distRange=2, $power=3, $priority=3, $stun=3, $isBase=True,
                    $events=array(Event::BEFOREACTIVATING=>getAdvancer(1,1),
                                  Event::ONHIT=>getPuller(0,1)));

//Zaamassel Kett's Kit
//one paradigm active at a time, and lose paradigme when stunned
//my activation
$urgent = new BaseCard($name="Urgent", $distRange=1, $power=1, $priority=2,
                    $events=array(Event::BEFOREACTIVATING=>getAdvancer(0,1),
                                  Event::AFTERACTIVATING=>getParadigm("haste"));
//my activation
$malicious = new BaseCard($name="Malicious", $power=1, $priority=-1, $stun=2,
                    $events=array(Event::AFTERACTIVATING=>getParadigm("pain"));
//my activation
$sinuous = new BaseCard($name="Sinuous", $priority=1,
                    $events=array(Event::AFTERACTIVATING=>getParadigm("fluidity"),
                                  Event::ENDOFBEAT=>getDirectMove));
//my activation
$warped = new BaseCard($name="Warped", $distRange=2,
                    $events=array(Event::STARTOFBEAT=>getadvance(-1,-1),
                                  Event::AFTERACTIVATING=>getParadigm("distortion")));
//when I am damaged for first event, ,y activation for third event
$sturdy = new BaseCard($name="Sturdy",
                    $events=array(Event::ONDAMAGE=>getStunImmune,
                                  Event::REVEAL=>getIgnoreMove,
                                  Event::AFTERACTIVATING=>getParadigm("resiliance")));
//my activation
$paradigmShift = new BaseCard($name="Paradigm Shift", $proxRange=2, $distRange=3, $power=3, $priority=3, $isBase=True, $events=array(Event::AFTERACTIVATING=>getParadigm("$paradigmChoice")));
*/ 

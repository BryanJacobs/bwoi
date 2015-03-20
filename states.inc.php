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
 * states.inc.php
 *
 * BattleConWoI game states description
 *
 */

/*
   Game state machine is a tool used to facilitate game developpement by doing common stuff that can be set up
   in a very easy way from this configuration file.

   Please check the BGA Studio presentation about game state to understand this, and associated documentation.

   Summary:

   States types:
   _ activeplayer: in this type of state, we expect some action from the active player.
   _ multipleactiveplayer: in this type of state, we expect some action from multiple players (the active players)
   _ game: this is an intermediary state where we don't expect any actions from players. Your game logic must decide what is the next game state.
   _ manager: special type for initial and final state

   Arguments of game states:
   _ name: the name of the GameState, in order you can recognize it on your own code.
   _ description: the description of the current game state is always displayed in the action status bar on
                  the top of the game. Most of the time this is useless for game state with "game" type.
   _ descriptionmyturn: the description of the current game state when it's your turn.
   _ type: defines the type of game states (activeplayer / multipleactiveplayer / game / manager)
   _ action: name of the method to call when this game state become the current game state. Usually, the
             action method is prefixed by "st" (ex: "stMyGameStateName").
   _ possibleactions: array that specify possible player actions on this step. It allows you to use "checkAction"
                      method on both client side (Javacript: this.checkAction) and server side (PHP: self::checkAction).
   _ transitions: the transitions are the possible paths to go from a game state to another. You must name
                  transitions in order to use transition names in "nextState" PHP method, and use IDs to
                  specify the next game state for each transition.
   _ args: name of the method to call to retrieve arguments for this gamestate. Arguments are sent to the
           client side to be used on "onEnteringState" or to set arguments in the gamestate description.
   _ updateGameProgression: when specified, the game progression is updated (=> call to your getGameProgression
                            method).
*/

//    !! It is not a good idea to modify this file when a game is running !!

$machinestates = array(

    // The initial state. Please do not modify.
    1 => array(
        "name" => "gameSetup",
        "description" => clienttranslate("Game setup"),
        "type" => "manager",
        "action" => "stGameSetup",
        "transitions" => array( "" => 2 )
    ),
    
    // Note: ID=2 => your first state
/*
uniqe cases		(simultaneous)
Set pairs  		(simultaneous)
Ante       		(turn order)
Reveal     		(simultaneous, automatic)
clash     		(simultaneous, then re-choose)
start Beat		(Turn order)
check for stun 		(Simultaneous, automatic)
Activation  		(turn order)
->before activating	(active player)
->check range		(automatic)
->on hit 		(active player)
->hit			(automatic)
->on damage		(active player)
->damage		(automatic)
->after activating	(active player)
"" "" second player
End of beat		(Turn order)
recycle			(automatic)

 */

   2 => array(
    	"name" => "characterSpecial",
    	"description" => clienttranslate('${actplayer} must make a choice before attack pair selection'),
    	"descriptionmyturn" => clienttranslate('${you} must make a choice before selecting an attack pair'),
    	"type" => "multipleactiveplayer",
    	"possibleactions" => array( "playCard", "pass" ),
    	"transitions" => array( "playCard" => 3, "pass" => 3 )
    ),

    3 => array(
        "name" = "chooseBasepairs",
        "description" => clienttranslate('${activeplayer} must select an attack pair'),
    	"descriptionmyturn" => clienttranslate('${you} must select an attack pair'),
    	"type" => "multipleactiveplayer",
    	"possibleactions" => array( "playCard" ),
    	"transitions" => array( "playCard" => 4 )
    )

    4 => array(
        "name" = "ante",
        "description" => clienttranslate('${actplayer} must select an ante or pass'),
    	"descriptionmyturn" => clienttranslate('${you} must select an ante or pass'),
    	"type" => "activeplayer",
    	"possibleactions" => array( "chooseSelected", "pass" ),
    	"transitions" => array( "chooseSelected"=> 5, "pass" => 5 )
    )

    5 => array(
        "name" = "revealEffects",
        "description" => clienttranslate(""),
        "type" => "game",
    	"possibleactions" => array( "" ),
    	"transitions" => array( "" => 6 )
    )
	//automatic check for a clash between players' atack pairs
    6 => array(
        "name" = "clash",
        "description" => clienttranslate("Check for clash"),
    	"type" => "game",
    	"possibleactions" => array( "" ),
    	"transitions" => array( "True" => 7, "False" => 8 )
    )

    7 => array(
        "name" = "chooseClashpairs",
        "description" => clienttranslate('${activeplayer} must select an attack pair'),
    	"descriptionmyturn" => clienttranslate('${you} must select an attack pair'),
    	"type" => "multipleactiveplayer",
    	"possibleactions" => array( "playCard" ),
    	"transitions" => array( "playCard" => 5 )
    )

    8 => array(
        "name" = "startBeat",
        "description" => clienttranslate('${activeplayer} must complete start of beat effect(s)'),
    	"descriptionmyturn" => clienttranslate('${you} must complete start of beat effect(s)'),
    	"type" => "activeplayer",
	// each possible action puts you back in the action window here because you must complete all actions. only once all
    // actions have been performed can players move into the next action window
    	"possibleactions" => array( "startStyle", "startBase", "done" ),
    	"transitions" => array( "startStyle" => 8, "startBase" => 8, "done" => 9 )
    )

    9 => array(
        "name" = "beforeActivation",
        "description" => clienttranslate('${activeplayer} must complete their activation'),
    	"descriptionmyturn" => clienttranslate('${you} must complete your activation'),
    	"type" => "activeplayer",
    	"possibleactions" => array( "preStyle", "preBase", "done" )
    	"transitions" => array( "preStyle" => 9, "preBase" => 9, "done" => 10 )
    )

    10 => array(
        "name" = "onHit",
        "description" => clienttranslate('${activeplayer} must complete their activation'),
    	"descriptionmyturn" => clienttranslate('${you} must complete your activation'),
    	"type" => "activeplayer",
    	"possibleactions" => array( "hitStyle", "hitBase", "done" )
    	"transitions" => array( "hitStyle" => 10, "hitBase" => 10, "done" => 11 )
    )

    11 => array(
        "name" = "onDamage",
        "description" => clienttranslate('${activeplayer} must complete their activation'),
    	"descriptionmyturn" => clienttranslate('${you} must complete your activation'),
    	"type" => "activeplayer",
    	"possibleactions" => array( "damageStyle", "damageBase", "done" )
    	"transitions" => array( "damageStyle" => 11, "damageBase" => 11, "done" => 12 )
    )

    12 => array(
        "name" = "afterActivation",
        "description" => clienttranslate('${activeplayer} must complete their activation'),
    	"descriptionmyturn" => clienttranslate('${you} must complete your activation'),
    	"type" => "activeplayer",
    	"possibleactions" => array( "postStyle", "postBase", "done" )
    	"transitions" => array( "postStyle" => 12 "postBase" => 12, "done" => 13 )
    )

    13 => array(
        "name" = "endBeat",
        "description" => clienttranslate('${activeplayer} must complete end of beat effect(s)'),
    	"descriptionmyturn" => clienttranslate('${you} must complete end of beat effect(s)'),
    	"type" => "activeplayer",
    	"possibleactions" => array( "endStyle", "endBase", "done" )
    	"transitions" => array( "endStyle" => 13, "endBase" => 13, "done" => 14 )
    )

    14 => array(
        "name" = "recycle",
        "description" => clienttranslate('complete beat and check for game end'),
    	"type" => "game",
    	"possibleactions" => array( "" )
    	"transitions" => array( "winner" => 99, "" => 2 )

/*
    Examples:
    
    2 => array(
        "name" => "nextPlayer",
        "description" => '',
        "type" => "game",
        "action" => "stNextPlayer",
        "updateGameProgression" => true,   
        "transitions" => array( "endGame" => 99, "nextPlayer" => 10 )
    ),
    
    10 => array(
        "name" => "playerTurn",
        "description" => clienttranslate('${actplayer} must play a card or pass'),
        "descriptionmyturn" => clienttranslate('${you} must play a card or pass'),
        "type" => "activeplayer",
        "possibleactions" => array( "playCard", "pass" ),
        "transitions" => array( "playCard" => 2, "pass" => 2 )
    ), 

*/    
   
    // Final state.
    // Please do not modify.
    99 => array(
        "name" => "gameEnd",
        "description" => clienttranslate("End of game"),
        "type" => "manager",
        "action" => "stGameEnd",
        "args" => "argGameEnd"
    )

);



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
  * battleconwoi.game.php
  *
  * This is the main file for your game logic.
  *
  * In this PHP file, you are going to defines the rules of the game.
  *
  */


require_once(APP_GAMEMODULE_PATH . 'module/table/table.game.php');
require_once('basics.php');

class BattleConWoI extends Table
{
    function BattleConWoI()
    {
        // Your global variables labels:
        //  Here, you can assign labels to global variables you are using for this game.
        //  You can use any number of global variables with IDs between 10 and 99.
        //  If your game has options (variants), you also have to associate here a label to
        //  the corresponding ID in gameoptions.inc.php.
        // Note: afterwards, you can get/set the global variables with getGameStateValue/setGameStateInitialValue/setGameStateValue
        parent::__construct();
        self::initGameStateLabels(array(
            "beatCount" => 11,
        ));

        $this->cardRegistry = buildCardRegistry();
    }

    protected function getGameName()
    {
        return "battleconwoi";
    }

    /*
        setupNewGame:

        This method is called only once, when a new game is launched.
        In this method, you must set up the game according to the game rules, so that
        the game is ready to be played.
    */
    protected function setupNewGame($players, $options = array())
    {
        self::DbQuery("DELETE FROM player WHERE 1");

        // Set the colors of the players with HTML color code
        // The default below is red/green/blue/orange/brown
        // The number of colors defined here must correspond to the maximum number of players allowed for the gams
        $default_colors = array( "0000ff", "ffa500" );

        // Create players
        // Note: if you added some extra field on "player" table in the database (dbmodel.sql), you can initialize it there.
        $sql = "INSERT INTO player (player_id, player_color, player_canal, player_name, player_avatar, life) VALUES ";
        $values = array();
        foreach( $players as $player_id => $player )
        {
            $color = array_shift( $default_colors );

            $curvalue = array(
                "$player_id", "\"${color}\"", "\"${player['player_canal']}\"",
                '"' . addslashes($player['player_name']) . '"',
                '"' . addslashes($player['player_avatar']) . '"',
                "20"
            );

            $values[] = "(" . implode($curvalue, ',') . ")";
        }
        $sql .= implode( $values, ',' );
        self::DbQuery( $sql );
        self::reloadPlayersBasicInfos();

        /************ Start the game initialization *****/

        // Init global values with their initial values
        //self::setGameStateInitialValue( 'my_first_global_variable', 0 );
        self::setGameStateInitialValue("beatCount", 0);

        // Init game statistics
        // (note: statistics used in this file must be defined in your stats.inc.php file)
        //self::initStat( 'table', 'table_teststat1', 0 );    // Init a table statistics
        //self::initStat( 'player', 'player_teststat1', 0 );  // Init a player statistics (for all players)

        /*self::placePlayerAt(1, -2);
        self::placePlayerAt(2, 2);*/

        $this->gamestate->setAllPlayersMultiactive();

        /************ End of the game initialization *****/
    }

    private function placePlayerAt($playerNumber, $location) {
        $table = "board_object";
        $playerObjectType = "PLAYER";
        $playerObjectDescription = "NO${playerNumber}";

        $where_clause = "`object_type`=\"${playerObjectType}\" AND `object_description`=\"${playerObjectDescription}\"";
        self::DbQuery("DELETE FROM `${table}` WHERE ${where_clause}");

        $values = "${location}, \"${playerObjectType}\", \"${playerObjectDescription}\"";
        self::DbQuery("INSERT INTO `${table}` (`position`, `object_type`, `object_description`) VALUES(${values})");
    }

    private function getPlayerLocation($playerNumber)
    {
        $table = "board_object";
        $playerObjectType = "PLAYER";
        $playerObjectDescription = "NO${playerNumber}";

        $where_clause = "`object_type`=\"${playerObjectType}\" AND `object_description`=\"${playerObjectDescription}\"";

        return (int) self::getUniqueValueFromDB("SELECT location FROM `${table}` WHERE ${where_clause}");
    }

    protected function moveToLocation($targetLocation)
    {
        $requestingPlayer = self::getCurrentPlayerId();
        $activePlayer = self::getActivePlayerId();

        self::placePlayerAt($activePlayer, $targetLocation);
    }

    /*
        getAllDatas: 

        Gather all informations about current game situation (visible by the current player).

        The method is called each time the game interface is displayed to a player, ie:
        _ when the game starts
        _ when a player refreshes the game page (F5)
    */
    protected function getAllDatas()
    {
        $result = array();

        $sql = "SELECT player_id id, player_score score FROM player ";
        $result['players'] = self::getCollectionFromDb($sql);

        $boardState = self::getCollectionFromDb("SELECT `position`,`object_type`,`object_description` FROM `board_object`");
        $result['boardState'] = $boardState;

        $result['characters'] = array_keys($this->cardRegistry);

        $result['cardsInHand'] = self::getPlayerCardSet(self::getCurrentPlayerId());

        return $result;
    }

    /*
        Return the cards "in hand" for the given player
    */
    protected function getPlayerCardSet($player_id)
    {
        return self::getCollectionFromDb("SELECT `card_id` id,`card_name` name,`card_type` type FROM `card` WHERE `player_id`=${player_id} AND `card_location`=\"HAND\"");
    }

    /*
        getGameProgression:

        Compute and return the current game progression.
        The number returned must be an integer beween 0 (=the game just started) and
        100 (= the game is finished or almost finished).

        This method is called each time we are in a game state with the "updateGameProgression" property set to true 
        (see states.inc.php)
    */
    function getGameProgression()
    {
        $num_beats = 20.0;
        $percentage_per_beat = $num_beats / 100;

        return $percentage_per_beat * self::getGameStateValue("beatCount");
    }


//////////////////////////////////////////////////////////////////////////////
//////////// Utility functions
////////////

    /*
        In this space, you can put any utility methods useful for your game logic
    */



//////////////////////////////////////////////////////////////////////////////
//////////// Player actions
//////////// 

    /*
        Each time a player is doing some game action, one of the methods below is called.
        (note: each method below must match an input method in battleconwoi.action.php)
    */

    /*

    Example:
    */

    function selectChar($choice)
    {
        self::checkAction('selectChar');

        $player_id = self::getCurrentPlayerID();

        $sql = "
            UPDATE  player
            SET     `character`=\"$choice\"
            WHERE   `player_id`=$player_id
        ";
        self::DbQuery($sql);

        // Fill the player's hand
        $cards = array_merge($this->cardRegistry[$choice], getBasicCards());

        $sql = "INSERT INTO card (`player_id`, `card_name`, `card_type`, `card_location`) VALUES ";
        $card_sql = array();
        foreach ($cards as $card) {
            $card_sql[] = '(' . implode(',', array('' . $player_id, '"' . $card->name . '"',
                                $card->isBase ? '"BASE"' : '"STYLE"',
                                '"HAND"')) . ')';
        }
        $sql .= implode(',', $card_sql);

        self::DbQuery($sql);

        $this->gamestate->setPlayerNonMultiactive($player_id, "selectChar");
    }

    function playPair($beatBase, $beatStyle)
    {
        self::checkAction('playPair');
        $player_id = self::getActivePlayerID();

        //TODO
        //get user input for BaseCard where $isBase == False
        //get user input for BaseCard where $isBase == True
        for ($i=0; $i < $players; $i++)
        {
            //$playercharacter.setPair($beatStyle, $beatBase);
        }
    }

    function anteSelected($playerCharacter, $anteInput)
    {
        self::checkAction('anteSelected');
        $player_id = self::getActivePlayerID();
        if ($playerCharacter.ante != false or $playerCharacter.setPair.ante != false)
        {
            //TODO get player input for what to ante and how many
            //and a pass break
            $anteInput.ante();
        }
        $eventClock = REVEAL;
    }

    function clashPair($beatBase)//, $playerCharacter.setPair())
    {
         //$playercharacter.setPair = ($playercharacter.setPair(0));
        //TODO get new base choice only
         //$playercharacter.setPair = ($playercharacter.setPair(0), $beatBase);
    }

    function actions($playerCharacter,$eventClock)
    {
        self::checkAction('anteSelected');
        $player_id = self::getActivePlayerID();
        if ($playerCharacter.$eventClock != false or $playerCharacter.setPair.$eventClock != false)
        {
        }
    }


    /*
    function playCard( $card_id )
    {
        // Check that this is the player's turn and that it is a "possible action" at this game state (see states.inc.php)
        self::checkAction( 'playCard' );

        $player_id = self::getActivePlayerId();

        // Add your game logic to play a card there 
        ...

        // Notify all players about the card played
        self::notifyAllPlayers( "cardPlayed", clienttranslate( '${player_name} played ${card_name}' ), array(
            'player_id' => $player_id,
            'player_name' => self::getActivePlayerName(),
            'card_name' => $card_name,
            'card_id' => $card_id
        ) );

    }

    */

    
//////////////////////////////////////////////////////////////////////////////
//////////// Game state arguments
////////////

    /*
        Here, you can create methods defined as "game state arguments" (see "args" property in states.inc.php).
        These methods function is to return some additional information that is specific to the current
        game state.
    */

    /*
    
    Example for game state "MyGameState":
    
    function argMyGameState()
    {
        // Get some values from the current game situation in database...
    
        // return values:
        return array(
            'variable1' => $value1,
            'variable2' => $value2,
            ...
        );
    }
    */

//////////////////////////////////////////////////////////////////////////////
//////////// Game state actions
////////////

    function checkForCharacterSpecificSetup()
    {
        // TODO: implement properly
        $this->gamestate->nextState('noCharacterSpecificSetup');

        // else $this->gamestate->nextState('characterSpecificSetupNecessary');
    }


//////////////////////////////////////////////////////////////////////////////
//////////// Zombie
////////////

    /*
        zombieTurn:
        
        This method is called each time it is the turn of a player who has quit the game (= "zombie" player).
        You can do whatever you want in order to make sure the turn of this player ends appropriately
        (ex: pass).
    */

    function zombieTurn( $state, $active_player )
    {
        $statename = $state['name'];

        /*if ($state['type'] == "activeplayer") {
            switch ($statename) {
                default:
                    $this->gamestate->nextState( "zombiePass" );
                    break;
            }

            return;
        }

        if ($state['type'] == "multipleactiveplayer") {
            // Make sure player is in a non blocking status for role turn
            $sql = "
                UPDATE  player
                SET     player_is_multiactive = 0
                WHERE   player_id = $active_player
            ";
            self::DbQuery( $sql );

            $this->gamestate->updateMultiactiveOrNextState( '' );
            return;
        }*/

        throw new feException( "Zombie mode not supported at this game state: ".$statename );
    }
}

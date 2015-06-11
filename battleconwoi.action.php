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
 * battleconwoi.action.php
 *
 * BattleConWoI main action entry point
 *
 *
 * In this file, you are describing all the methods that can be called from your
 * user interface logic (javascript).
 *       
 * If you define a method "myAction" here, then you can call it from your javascript code with:
 * this.ajaxcall( "/battleconwoi/battleconwoi/myAction.html", ...)
 *
 */


class action_battleconwoi extends APP_GameAction
{
    // Constructor: please do not modify
    public function __default()
    {
        if( self::isArg( 'notifwindow') )
        {
            $this->view = "common_notifwindow";
            $this->viewArgs['table'] = self::getArg( "table", AT_posint, true );
        }
        else
        {
            $this->view = "battleconwoi_battleconwoi";
            self::trace( "Complete reinitialization of board game" );
        }
    }

    // TODO: defines your action entry points there

    public function moveToLocation()
    {
        self::setAjaxMode();

        $location = self::getArg("location", AT_int, true);

        $this->game->moveToLocation($location);

        self::ajaxResponse();
    }

    public function selectChar()
    {
        self::setAjaxMode();

        $characterSelection = self::getArg("character", AT_enum, true, NULL, array_keys($cardRegistry));
        self::trace("Selected character: " . var_dump($characterSelection, true));

        self::ajaxResponse();
    }


  }

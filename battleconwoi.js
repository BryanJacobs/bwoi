/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * BattleConWoI implementation : © <Bryan Jacobs & Craig Lavine> <raptorbonz42@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * battleconwoi.js
 *
 * BattleConWoI user interface script
 * 
 * In this file, you are describing the logic of your user interface, in Javascript language.
 *
 */

define([
    "dojo","dojo/_base/declare",
    "ebg/core/gamegui",
    "ebg/counter"
],
function (dojo, declare) {
    return declare("bgagame.battleconwoi", ebg.core.gamegui, {
        constructor: function(){
            console.log('battleconwoi constructor');

            // Here, you can init the global variables of your user interface
            // Example:
            // this.myGlobalValue = 0;

        },

        /*
            setup:

            This method must set up the game user interface according to current game situation specified
            in parameters.

            The method is called each time the game interface is displayed to a player, ie:
            _ when the game starts
            _ when a player refreshes the game page (F5)

            "gamedatas" argument contains all datas retrieved by your "getAllDatas" PHP method.
        */

        setup: function(gamedata)
        {
            console.log("Starting game setup");

            console.log("Fetched game data %o", gamedata);
            console.log("Board state %o", gamedata.boardState);

            this.setupNotifications();

            switch (gamedata.gamestate.name) {
                case 'characterSelect':
                    console.log("Enabling character selection...");

                    var character_select_block = this.format_block('jstpl_character_select_start', {});
                    for (i=0; i<gamedata.characters.length; i++) {
                        character_select_block += this.format_block('jstpl_character_select_block', {'name': gamedata.characters[i]});
                    }
                    character_select_block += this.format_block('jstpl_character_select_end', {});

                    dojo.place(character_select_block, 'bwoiboard');

                    dojo.query('.bwoiCharacter').connect('onclick', this, 'characterClicked');
                    break;

                case 'initialBasePairDiscards':
                    this.displayHand(gamedata.cardsInHand);
                    break;

                case 'characterSpecificSetup':
                    break;

                default:
                    this.displayBoard();
                    break;
            }

            console.log("Ending game setup");
        },

        displayBoard: function() {
            console.log("Displaying board");
            for (var i = 0; i < 7; ++i) {
                var xpos = 60 * i + 200;
                dojo.place(this.format_block('jstpl_space', {'no': i, 'X': xpos}), 'bwoiboard');
            }
        },

        displayHand: function(cards) {
            var hand_block = this.format_block('jstpl_card_chooser_start', {});
            for (card in cards) {
                hand_block += this.format_block('jstpl_card_chooser_card', {'name': card.name});
            }
            hand_block += this.format_block('jstpl_card_chooser_end', {});

            dojo.place(hand_block, 'bwoiboard');

            dojo.connect($('.bwoiCard'), 'onclick', this, 'cardClicked');
        },

        ///////////////////////////////////////////////////
        //// Game & client states

        // onEnteringState: this method is called each time we are entering into a new game state.
        //                  You can use this method to perform some user interface changes at this moment.
        //
        onEnteringState: function(stateName, args)
        {
            console.log('Entering state: ' + stateName);

            switch (stateName)
            {
                case 'characterSelect':
                    break;
            }
        },

        // onLeavingState: this method is called each time we are leaving a game state.
        //                 You can use this method to perform some user interface changes at this moment.
        //
        onLeavingState: function(stateName)
        {
            console.log('Leaving state: '+ stateName);

            switch (stateName)
            {
                case 'characterSelect':
                    dojo.style('characterSelect', 'display', 'none');
                    break;
                case 'initialBasePairDiscards':
                    this.displayBoard();
                    break;
            }
        },

        // onUpdateActionButtons: in this method you can manage "action buttons" that are displayed in the
        //                        action status bar (ie: the HTML links in the status bar).
        //
        onUpdateActionButtons: function(stateName, args)
        {
            console.log( 'onUpdateActionButtons: '+stateName );

            if( this.isCurrentPlayerActive() )
            {
                switch (stateName)
                {
/*
                 Example:

                 case 'myGameState':
                    // Add 3 action buttons in the action status bar:
                    this.addActionButton( 'button_1_id', _('Button 1 label'), 'onMyMethodToCall1' ); 
                    this.addActionButton( 'button_2_id', _('Button 2 label'), 'onMyMethodToCall2' ); 
                    this.addActionButton( 'button_3_id', _('Button 3 label'), 'onMyMethodToCall3' ); 
                    break;
*/
                }
            }
        },

        ///////////////////////////////////////////////////
        //// Utility methods
        /*
        
            Here, you can defines some utility methods that you can use everywhere in your javascript
            script.
        
        */


        ///////////////////////////////////////////////////
        //// Player's action
        
        /*
        
            Here, you are defining methods to handle player's action (ex: results of mouse click on 
            game objects).
            
            Most of the time, these methods:
            _ check the action is possible at this game state.
            _ make a call to the game server
        
        */

        locationClicked: function( evt )
        {
            console.log('locationClicked');

            // Preventing default browser reaction
            dojo.stopEvent(evt);

            // Check that this action is possible (see "possibleactions" in states.inc.php)
            /*if( ! this.checkAction( 'myAction' ) )
            {   return; }*/

            this.ajaxcall( "/battleconwoi/battleconwoi/moveToLocation.html", {
                                                                    lock: true,
                                                                    location: 3,
                                                                 },
                         this, function(result) {
                            // What to do after the server call if it succeeded
                            // (most of the time: nothing)
                            console.log("moveToLocation success %o", result);
                         }, function(is_error) {
                            console.log("moveToLocation result %o", is_error);
                     });
        },

        cardClicked: function(evt) {
            console.log('cardClicked %o', evt);
            // TODO: handle this
        },

        characterClicked: function(evt) {
            console.log('characterClicked %o', evt);
            if (!this.checkAction( 'selectChar'))
            {
                return;
            }

            var target = evt.target.innerHTML;

            console.log('Click is on character %s', target);

            this.ajaxcall("/battleconwoi/battleconwoi/selectChar.html", {
                                                                    lock: true,
                                                                    character: target,
                                                                 },
                         this, function(result) {
                            console.log("selectChar success %o", result);
                         }, function(is_error) {
                            console.log("selectChar result %o", is_error);
             });
        },

        ///////////////////////////////////////////////////
        //// Reaction to cometD notifications

        /*
            setupNotifications:

            In this method, you associate each of your game notifications with your local method to handle it.

            Note: game notification names correspond to "notifyAllPlayers" and "notifyPlayer" calls in
                  your battleconwoi.game.php file.

        */
        setupNotifications: function()
        {
            console.log( 'notifications subscriptions setup' );
            
            // TODO: here, associate your game notifications with local methods
            
            // Example 1: standard notification handling
            // dojo.subscribe( 'cardPlayed', this, "notif_cardPlayed" );
            
            // Example 2: standard notification handling + tell the user interface to wait
            //            during 3 seconds after calling the method in order to let the players
            //            see what is happening in the game.
            // dojo.subscribe( 'cardPlayed', this, "notif_cardPlayed" );
            // this.notifqueue.setSynchronous( 'cardPlayed', 3000 );
            // 
        },  
        
        // TODO: from this point and below, you can write your game notifications handling methods
        
        /*
        Example:
        
        notif_cardPlayed: function( notif )
        {
            console.log( 'notif_cardPlayed' );
            console.log( notif );
            
            // Note: notif.args contains the arguments specified during you "notifyAllPlayers" / "notifyPlayer" PHP call
            
            // TODO: play the card in the user interface.
        },    
        
        */
   });
});

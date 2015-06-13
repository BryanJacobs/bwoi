
-- ------
-- BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
-- BattleConWoI implementation : © <Bryan Jacobs & Craig Lavine> <raptorbonz42@gmail.com>
-- 
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
-- -----

-- dbmodel.sql

-- This is the file where you are describing the database schema of your game
-- Basically, you just have to export from PhpMyAdmin your table structure and copy/paste
-- this export here.
-- Note that the database itself and the standard tables ("global", "stats", "gamelog" and "player") are
-- already created and must not be created here

-- Note: The database schema is created from this file when the game starts. If you modify this file,
--       you have to restart a game to see your changes in database.


ALTER TABLE `player` ADD `life` INT UNSIGNED NOT NULL;
ALTER TABLE `player` ADD `character` VARCHAR(20);

CREATE TABLE `board_object` (
    `position` int(2) NOT NULL,
    `object_type` VARCHAR(16) NOT NULL,
    `object_description` VARCHAR(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `card` (
  `card_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `card_name` varchar(16) NOT NULL,
  `card_type` varchar(16) NOT NULL,
  `player_id` int(11) NOT NULL,
  `card_location` varchar(16) NOT NULL,
  PRIMARY KEY (`card_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

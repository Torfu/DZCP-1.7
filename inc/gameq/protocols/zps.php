<?php
/**
 * This file is part of GameQ.
 *
 * GameQ is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * GameQ is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Zombie Panic Source Protocol Class
 *
 * @author Austin Bischoff <austin@codebeard.com>
 */
class GameQ_Protocols_Zps extends GameQ_Protocols_Source
{
    //Game or Mod
    protected $name = "zps";
    protected $name_long = "Zombie Panic! Source";
    protected $name_short = "ZPS";

    //Basic Game
    protected $basic_game_dir = 'hl2mp';
    protected $basic_game_long = "Half-Life 2";
    protected $basic_game_short = "HL2";

    //Settings
    protected $goldsource = false;
    protected $is_mod = true;
    protected $modlist = array();
}
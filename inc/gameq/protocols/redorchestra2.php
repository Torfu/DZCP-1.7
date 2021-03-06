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
 * Red Orchestra 2 Protocol Class
 *
 * Thanks to http://forums.tripwireinteractive.com/showthread.php?t=72439 for information about the protocol
 *
 * @author Austin Bischoff <austin@codebeard.com>
 */
class GameQ_Protocols_Redorchestra2 extends GameQ_Protocols_Source
{
    protected $name = "redorchestra2";
    protected $name_long = "Red Orchestra 2";
    protected $name_short = "RO2";

    //Basic Game
    protected $basic_game_long = '';
    protected $basic_game_short = '';
    protected $basic_game_dir = 'redorchestra2';

    //Settings
    protected $goldsource = false;
    protected $is_mod = false;
    protected $modlist = array();

    protected $port = 27015;
}
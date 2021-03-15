<?php

require_once 'game/challengeMode.php';
require_once 'game/hallOfFame.php';
require_once 'game/loader.php';
require_once 'game/menu.php';
require_once 'game/pokedex.php';
require_once 'game/singleBattle.php';
require_once 'game/types.php';
require_once 'game/fight.php';
require_once 'game/standbyPhase.php';
require_once 'game/attackPhase.php';
require_once 'game/attack.php';
require_once 'game/specialAttacks.php';
require_once 'game/damage.php';
require_once 'game/stat.php';
require_once 'game/status.php';

echo "\n--- Welcome to the world of Pokémon! ---\n\n";

$menu = new Menu();
$menu->show();

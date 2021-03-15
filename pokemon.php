<?php

require_once 'game/hallOfFame.php';
require_once 'game/loader.php';
require_once 'game/menu.php';
require_once 'game/pokedex.php';
require_once 'game/fight.php';
require_once 'game/standbyPhase.php';
require_once 'game/specialAttacks.php';
require_once 'game/damage.php';
require_once 'game/stat.php';

require_once 'game/model.php';
require_once 'game/outputInterface.php';

require_once 'game/attack/controller.php';

require_once 'game/attackPhase/controller.php';
require_once 'game/attackPhase/model.php';

require_once 'game/challengeMode/controller.php';
require_once 'game/challengeMode/model.php';

require_once 'game/singleBattle/controller.php';
require_once 'game/singleBattle/model.php';

require_once 'game/status/model.php';
require_once 'game/status/controller.php';

require_once 'game/types/model.php';
require_once 'game/types/controller.php';

echo "\n--- Welcome to the world of Pokémon! ---\n\n";

$menu = new Menu();
$menu->show();

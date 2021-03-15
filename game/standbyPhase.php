<?php
// --- Processes and calculations of a battle's standby phase

class StandbyPhase
{
    public function __construct()
    {
        $this->loader = new Loader();
        $this->moves = $this->loader->getData('moves');
        $this->types = $this->loader->getData('types');
    }

    // --- sending out a pokemon
    public function sendOut($trainer, &$pokemon)
    {
        sleep(2);
        echo $trainer . ":\t I choose you, " . $pokemon['Name'] . "!\n";
        $this->setupPokemon($pokemon);
    }

    // --- setup pokemons moves and modifiers ready for battle
    public function setupPokemon(&$pokemon)
    {
        // setup stat mods
        $pokemon['Attack Mod'] = 0;
        $pokemon['Defence Mod'] = 0;
        $pokemon['Sp Attack Mod'] = 0;
        $pokemon['Sp Defence Mod'] = 0;
        $pokemon['Speed Mod'] = 0;
        $pokemon['Accuracy Mod'] = 0;
        $pokemon['Evasion Mod'] = 0;
        // setup move PP
        $pokemon['M1 PP'] = 0;
        $pokemon['M2 PP'] = 0;
        $pokemon['M3 PP'] = 0;
        $pokemon['M4 PP'] = 0;
        // setup battle mods
        $pokemon['Current Move'] = 0;
        $pokemon['Move Turns'] = 0;
        $pokemon['Damage Dealt'] = 0;
        $pokemon['Move Category'] = '';
        $pokemon['Status'] = '';
        $pokemon['Confusion'] = -1;
        $pokemon['Flinch'] = 0;
        $pokemon['Critical'] = 0;

        $this->getPP($pokemon);
        $this->getHP($pokemon);
    }

    // --- get the PP of each pokemons moves
    public function getPP(&$pokemon)
    {
        $move1 = $this->loader->findByName($pokemon['Move 1'], $this->moves);
        $pokemon['M1 PP'] = $move1['PP'];
        if ($pokemon['Move 2']) {
            $move2 = $this->loader->findByName($pokemon['Move 2'], $this->moves);
            $pokemon['M2 PP'] = $move2['PP'];
        } else {
            $pokemon['M2 PP'] = 0;
        }
        if ($pokemon['Move 3']) {
            $move3 = $this->loader->findByName($pokemon['Move 3'], $this->moves);
            $pokemon['M3 PP'] = $move3['PP'];
        } else {
            $pokemon['M3 PP'] = 0;
        }
        if ($pokemon['Move 4']) {
            $move4 = $this->loader->findByName($pokemon['Move 4'], $this->moves);
            $pokemon['M4 PP'] = $move4['PP'];
        } else {
            $pokemon['M4 PP'] = 0;
        }
    }

    // --- get the HP of each pokemon
    public function getHP(&$pokemon)
    {
        $pokemon['HP Left'] = $pokemon['HP'];
    }

    // --- detect HP remaining and print based on that
    public function printHP($name, $pokemon)
    {
        if ($pokemon['HP Left'] <= ($pokemon['HP'] / 2)) {
            if ($pokemon['HP Left'] <= ($pokemon['HP'] / 8)) {
                $color = "\e[38;5;196m";
            } else {
                $color = "\e[38;5;226m";
            }
        } else {
            $color = "\e[38;5;46m";
        }

        $healthBar = "";
        $percentLeft = $pokemon['HP Left'] / $pokemon['HP'];
        $barLeft = ceil(50 * $percentLeft);

        for ($i = 1; $i <= $barLeft; $i++) {
            $healthBar .= "=";
        }

        $mask = "\n\t[" . $color . "%-50s\e[0m] %d/%d\n";

        echo "\n\t" . $name . "'s " . $pokemon['Name'] . ": ";
        if ($pokemon['Status']) {
            echo $this->loader->statusColor($pokemon['Status']) . $pokemon['Status'] . "\e[0m";
        }
        printf($mask, $healthBar, $pokemon['HP Left'], $pokemon['HP']);
    }

    // --- get attack for pokemon
    public function getAttack(&$pokemon, $player)
    {
        if ($pokemon['Move Turns'] <= 0) {
            if ($pokemon['M1 PP'] == 0 && $pokemon['M2 PP'] == 0 && $pokemon['M3 PP'] == 0 && $pokemon['M4 PP'] == 0) {
                $attack = 'Struggle';
                $move = $this->loader->findByName($attack, $this->moves);
            } else {
                if ($player == true) {
                    $move = $this->playerAttack($pokemon);
                } else {
                    $move = $this->computerAttack($pokemon);
                }
            }
            $pokemon['Move Turns'] = $move['Turns'];
        } else {
            $attack = $pokemon['Current Move'];
            $move = $this->loader->findByName($pokemon['Move ' . $attack], $this->moves);
        }
        return $move;
    }

    // --- get attack for player
    public function playerAttack(&$pokemon)
    {
        $this->printAttacks($pokemon);
        $attack = readline("- What will " . $pokemon['Name'] . " do? ");

        if (!isset($pokemon['Move ' . $attack]) || $pokemon['Move ' . $attack] == "") {
            $attack = 0;
            echo "\nInvalid move. Choose again.\n\n";
            $move = $this->playerAttack($pokemon);
        } elseif ($pokemon['M' . $attack . ' PP'] == 0) {
            $attack = 0;
            echo "\nThere is no PP left for this move. Choose again.\n\n";
            $move = $this->playerAttack($pokemon);
        } else {
            $move = $this->loader->findByName($pokemon['Move ' . $attack], $this->moves);
            $pokemon['Current Move'] = $attack;
        }
        return $move;
    }

    // --- get attack for computer
    public function computerAttack(&$pokemon)
    {
        $attacks = ['1'];
        $pokemon['M2 PP'] > 0 ? array_push($attacks, '2') : 0;
        $pokemon['M3 PP'] > 0 ? array_push($attacks, '3') : 0;
        $pokemon['M4 PP'] > 0 ? array_push($attacks, '4') : 0;
        $chosen = rand(0, count($attacks) - 1);

        $move = $this->loader->findByName($pokemon['Move ' . $attacks[$chosen]], $this->moves);
        $pokemon['Current Move'] = $attacks[$chosen];
        return $move;
    }

    public function printAttacks($pokemon)
    {
        for ($i = 1; $i <= 4; $i++) {
            $move = $this->loader->findByName($pokemon['Move ' . $i], $this->moves);
            $attackMask = "%d. " . $this->loader->getMoveType($pokemon['Move ' . $i]) . "%-15s\e[0m PP: %3d/%-3d\t| ";
            $pokemon['Move ' . $i] ? printf($attackMask, $i, $pokemon['Move ' . $i], $pokemon['M' . $i . ' PP'], $move['PP']) : 0;
        }
        echo "\n";
    }
}

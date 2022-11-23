<?php

namespace Game;

class AttackPhase extends Loader
{
    /**
     * Gets an attack of a pokémon
     * @param array $pokemon The pokémon to get an attack for
     * @param boolean $player If this is the player's pokémon or not
     * @return boolean The move to use as an attack
     */
    public function getAttack($pokemon, $player = false)
    {
        if ($pokemon['PP'][1] == 0 && $pokemon['PP'][2] == 0 && $pokemon['PP'][3] == 0 && $pokemon['PP'][4] == 0) {
            return array(0, $this->findByName('Struggle', 'moves'));
        } else {
            return $player ? $this->playerAttack($pokemon) : $this->computerAttack($pokemon);
        }
    }

    /**
     * Get the attack chosen by a player
     * @param array $pokemon The player's pokémon
     * @return boolean The move to use as an attack
     */
    public function playerAttack($pokemon)
    {
        $this->printAttacks($pokemon);

        $move = null;

        while (!$move) {
            $attack = readline("- What will " . $pokemon['Name'] . " do? ");

            if (!isset($pokemon['Move ' . $attack]) || $pokemon['Move ' . $attack] == "") {
                $attack = 0;
                echo "\nInvalid move. Choose again.\n\n";
            } elseif ($pokemon['PP'][$attack] == 0) {
                $attack = 0;
                echo "\nThere is no PP left for this move. Choose again.\n\n";
            } else {
                $move = $this->find($pokemon['Move ' . $attack], 'moves');
            }
        }

        return array($attack, $move);
    }

    /**
     * Get an attack for the computer
     * @param array $pokemon The computer's pokémon
     * @return boolean The move to use as an attack
     */
    public function computerAttack($pokemon)
    {
        $attacks = ['1'];

        $pokemon['PP'][2] > 0 ? array_push($attacks, '2') : 0;
        $pokemon['PP'][3] > 0 ? array_push($attacks, '3') : 0;
        $pokemon['PP'][4] > 0 ? array_push($attacks, '4') : 0;

        $chosen = rand(0, count($attacks) - 1);

        $move = $this->find($pokemon['Move ' . $attacks[$chosen]], 'moves');

        return array($attacks[$chosen], $move);
    }

    /**
     * Prints the attacks of a pokémon
     * @param array $pokemon The pokémon
     */
    public function printAttacks($pokemon)
    {
        $moves = new Moves();

        for ($i = 1; $i <= 4; $i++) {
            if ($pokemon['Move ' . $i]) {
                $move = $this->find($pokemon['Move ' . $i], 'moves');

                $attackMask = "%d. " . $moves->getMoveType($pokemon['Move ' . $i]) . "%-15s\e[0m PP: %3d/%-3d\t| ";

                printf($attackMask, $i, $pokemon['Move ' . $i], $pokemon['PP'][$i], $move['PP']);
            }
        }

        echo "\n";
    }
}

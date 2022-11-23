<?php

namespace Game;

class Team extends Loader
{
    /**
     * Creates a team for the player
     * @return array The player's team
     */
    public function createPlayerTeam()
    {
        $playerTeam = array(1 => "", 2 => "", 3 => "");

        for ($i = 1; $i <= 3; $i++) {
            switch ($i) {
                case 1:
                    $ordinal = "1st";
                    break;
                case 2:
                    $ordinal = "2nd";
                    break;
                case 3:
                    $ordinal = "3rd";
                    break;
                default:
                    $ordinal = "error";
                    break;
            }

            while (!$playerTeam[$i]) {
                $pokemon = readline("- Select your " . $ordinal . " Pokémon or enter '?' to get one at random: ");

                if ($pokemon == '?') {
                    $playerTeam[$i] = $this->findRandom('monsters');
                } else {
                    $playerTeam[$i] = $this->find($pokemon, 'monsters');
                }

                if (!$playerTeam[$i]) {
                    echo "Unknown Pokémon, choose again.\n";
                }
            }

            echo ucfirst($playerTeam[$i]['Name']) . " set as your " . $ordinal . " Pokémon.\n";
        }

        return $playerTeam;
    }

    /**
     * Get a team for a computer trainer
     * @return array The computer's team
     */
    public function getComputerTeam($computer)
    {
        for ($i = 1; $i <= 3; $i++) {
            $pokemon = $computer['Pokemon ' . $i];
            $computerTeam[$i] = $this->find($pokemon, 'monsters');
        }

        return $computerTeam;
    }

    /**
     * Creates a random team for the computer
     * @return array The computer's team
     */
    public function createRandomComputerTeam()
    {
        for ($i = 1; $i <= 3; $i++) {
            $computerTeam[$i] = $this->findRandom('monsters');
        }

        return $computerTeam;
    }
}

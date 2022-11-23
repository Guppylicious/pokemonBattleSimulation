<?php

namespace Game;

class Menu
{
    /**
     * @param string $command The command to run
     * @param array $options An array of options to provide to commands
     */
    public function __construct($command = '', $options = array()) {
        switch ($command) {
            case '--battle':
                $battle = new Battle();
                break;
            case '--challenge':
                $challenge = new Challenge();
                break;
            case '--hallOfFame':
                $hallOfFame = new HallOfFame();
                $hallOfFame->showHall();
                break;
            case '--pokedex':
                if (isset($options[0])) {
                    $pokedex = new Pokedex($options[0]);
                } else {
                    echo "No Pokédex or Pokémon given\n\n";
                }
                break;
            case '--types':
                $types = new Types();
                $types->showTable();
                break;
            case '--help':
            default:
                $this->help();
                break;
        }

    }

    /**
     * Prints the help screen
     */
    public function help() {
        echo "\t usage: start [--command]\n\n";

        echo "\t [--battle] Battle a trainer\n";
        echo "\t [--challenge] Take on a challenge\n";
        echo "\t [--hallOfFame] View the Hall of Fame\n";
        echo "\t [--pokedex] [pokedex] || [pokemon no.] || [pokemon name] View a Pokédex or a Pokémon's entry\n";
        echo "\t [--types] Learn type advantages\n";
        echo "\t [--help] This screen\n\n";
    }
}

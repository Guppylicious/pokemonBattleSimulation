<?php

namespace Game;

class Pokedex extends Loader
{
    /**
     * @param string $viewable The pokédex or pokémon to view
     */
    public function __construct($viewable)
    {
        $pokedexs = array('Kanto', 'Johto');
        $viewable = ucfirst($viewable);

        if (in_array($viewable, $pokedexs)) {
            $this->printPokedex($viewable);
        } else {
            $this->printPokemon($viewable);
        }
    }

    /**
     * Prints the given pokédex
     * @param string $region The region of pokédex to print
     */
    public function printPokedex($region)
    {
        $monsters = $this->getDexMonsters($region);
        $column = 0;

        echo "\n--- " . $region . " Dex ---\n\n";

        foreach ($monsters as $monster) {
                $column++;

            if ($column % 10 == 0) {
                $mask = "%3s. %-15s\n";
            } else {
                $mask = "%3s. %-15s\t";
            }

            printf($mask, $monster['Number'], $monster['Name']);
        }

        echo "\n";
    }

    /**
     * Get all pokémon from a given pokédex
     * @param string $region The name of the region to get
     * @return array The found pokémon of the region
     */
    public function getDexMonsters($region) {
        $monsters = $this->getData('monsters');
        $dexMonsters = array();

        foreach ($monsters as $monster) {
            if ($region == $monster['Region']) {
                $dexMonsters[] = $monster;
            }
        }

        return $dexMonsters;
    }

    /**
     * Prints the given pokémon
     * @param string $pokemon The name or number of the pokémon to print
     */
    public function printPokemon($pokemon) {
        $pokemonData = $this->find($pokemon, 'monsters');

        if ($pokemonData) {
            echo "\n********** " . $pokemonData['Number'] . ". " . $pokemonData['Name'] . " **********\n";
            echo "\n " . $pokemonData['Description'] . "\n";

            echo "\n***** Type *****\n\n";

            $typeMask = Types::typeColor($pokemonData['Type 1']) . "%s\e[0m";

            printf($typeMask, " " . $pokemonData['Type 1']);

            if ($pokemonData['Type 2']) {
                $typeMask = "%1s" . Types::typeColor($pokemonData['Type 2']) . "%s\e[0m";
                printf($typeMask, "/", $pokemonData['Type 2']);
            }

            echo "\n";

            $statMask = " %-10s\t %-3d\n";

            echo "\n***** Stats *****\n\n";

            printf($statMask, "HP:", $pokemonData['HP']);
            printf($statMask, "Attack:", $pokemonData['Attack']);
            printf($statMask, "Defence:", $pokemonData['Defence']);
            printf($statMask, "Sp Attack:", $pokemonData['Sp Attack']);
            printf($statMask, "Sp Defence:", $pokemonData['Sp Defence']);
            printf($statMask, "Speed:", $pokemonData['Speed']);

            echo "\n***** Moves *****\n";

            $pokemonData['Move 1'] ? $this->printMove($pokemonData['Move 1']) : "";
            $pokemonData['Move 2'] ? $this->printMove($pokemonData['Move 2']) : "";
            $pokemonData['Move 3'] ? $this->printMove($pokemonData['Move 3']) : "";
            $pokemonData['Move 4'] ? $this->printMove($pokemonData['Move 4']) : "";

            echo "\n";
        }
    }

    /**
     * Prints the move of a pokédmon
     * @param string $move The name of the move to print
     */
    public function printMove($move)
    {
        $moveMask = " %-12s\t %-s\n";
        $moveData = $this->find($move, 'moves');

        echo "\n*** " . Types::typeColor($moveData['Type']) . $move . "\e[0m\n";

        $typeMask = " %-12s\t " . Types::typeColor($moveData['Type']) . "%-s\e[0m\n";

        printf($typeMask, "Type:", $moveData['Type']);
        printf($moveMask, "Category:", $moveData['Category']);
        printf($moveMask, "PP:", $moveData['PP']);
        printf($moveMask, "Power:", $moveData['Power']);
        printf($moveMask, "Accuracy:", $moveData['Accuracy']);
        printf($moveMask, "Description:", $moveData['Description']);
    }
}

<?php
// --- print the entite list of pokemon

class Pokedex
{
    public function __construct()
    {
        $this->loader = new Loader();
        $this->monsters = $this->loader->getData('monsters');
        $this->moves = $this->loader->getData('moves');
    }

    public function show()
    {
        echo "\n--- Pokédex ---\n\n";
        echo "- 1. Kanto\n";
        echo "- 2. Johto\n";
        echo "- 3. Hoenn\n";
        echo "- 4. Sinnoh\n";
        echo "- 5. Unova\n";
        echo "- 6. Kalos\n";
        echo "- 7. Alola\n";
        echo "- 8. National\n\n";

        $region = readline("- Enter the number of the Pokédex you would like to view (or enter 'x' to return to the main menu): ");

        switch ($region) {
            case 1:
                $this->printPokedex('Kanto');
                break;
            case 2:
                $this->printPokedex('Johto');
                break;
            case 3:
                echo "\n*** Under maintenance. Come back later. ***\n\n";
                $this->show();
                break;
            case 4:
                echo "\n*** Under maintenance. Come back later. ***\n\n";
                $this->show();
                break;
            case 5:
                echo "\n*** Under maintenance. Come back later. ***\n\n";
                $this->show();
                break;
            case 6:
                echo "\n*** Under maintenance. Come back later. ***\n\n";
                $this->show();
                break;
            case 7:
                echo "\n*** Under maintenance. Come back later. ***\n\n";
                $this->show();
                break;
            case 8:
                $this->printPokedex('Kanto');
                $this->printPokedex('Johto');
                break;
            case 'x':
                $menu = new Menu();
                echo "\n";
                $menu->show();
                break;
            default:
                echo "Unknown option, try again.\n\n";
                $this->show();
        }

        while (1) {
            $pokemon = readline("- Enter a Pokémon to look at (or enter 'x' to return to the Pokédex selection): ");
            if ($pokemon == 'x') {
                $this->show();
                echo "\n";
            } else {
                $pokemonData = $this->loader->find($pokemon, $this->monsters);
                if (!$pokemonData) {
                    echo "Unknown Pokémon, choose again.\n";
                } else {
                    echo "\n********** " . $pokemonData['Number'] . ". " . $pokemonData['Name'] . " **********\n";
                    echo "\n " . $pokemonData['Description'] . "\n";
                    echo "\n***** Type *****\n\n";
                    $typeMask = $this->loader->typeColor($pokemonData['Type 1']) . "%s\e[0m";
                    printf($typeMask, " " . $pokemonData['Type 1']);
                    if ($pokemonData['Type 2']) {
                        $typeMask = "%1s" . $this->loader->typeColor($pokemonData['Type 2']) . "%s\e[0m";
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
        }
    }

    public function printPokedex($region)
    {
        echo "\n--- " . $region . " Dex ---\n\n";
        $column = 0;
        foreach ($this->monsters as $monster) {
            if ($region == $monster['Region']) {
                $column++;
                if ($column % 10 == 0) {
                    $mask = "%3s. %-15s\n";
                } else {
                    $mask = "%3s. %-15s\t";
                }
                printf($mask, $monster['Number'], $monster['Name']);
            }
        }
        echo "\n";
    }

    public function printMove($move)
    {
        $moveMask = " %-12s\t %-s\n";
        $moveData = $this->loader->findByName($move, $this->moves);
        echo "\n*** " . $this->loader->typeColor($moveData['Type']) . $move . "\e[0m\n";
        $typeMask = " %-12s\t " . $this->loader->typeColor($moveData['Type']) . "%-s\e[0m\n";
        printf($typeMask, "Type:", $moveData['Type']);
        printf($moveMask, "Category:", $moveData['Category']);
        printf($moveMask, "PP:", $moveData['PP']);
        printf($moveMask, "Power:", $moveData['Power']);
        printf($moveMask, "Accuracy:", $moveData['Accuracy']);
        printf($moveMask, "Description:", $moveData['Description']);
    }
}

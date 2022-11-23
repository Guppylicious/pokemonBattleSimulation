<?php

namespace Game;

class HallOfFame extends Loader
{
    /**
     * Displays the Hall of Fame
     */
    public function showHall()
    {
        echo "\n--- Hall of Fame ---\n\n";

        $hallMask = " %-20s\t| %-20s\t| %-20s\t| %-20s\t| %-20s\t| %-20s\t|\n";

        printf($hallMask, "Name", "1st Pokémon", "2nd Pokémon", "3rd Pokémon", "Region", "Date");

        $hall = $this->getData('hall');

        foreach ($hall as $entry) {
            printf($hallMask, $entry['Name'], $entry['Pokemon 1'], $entry['Pokemon 2'], $entry['Pokemon 3'], $entry['Region'], $entry['Date']);
        }

        echo "\n";
    }

    /**
     * Adds a player to the Hall of Fame
     * @param string $player The players name
     * @param array $team The player's team
     * @param string $region The region the player completed
     */
    public function addToHall($player, $team, $region)
    {
        echo "\nYou are a true Pokémon Master!\n";
        sleep(2);
        echo "\n " . $player . "\n";
        sleep(2);
        echo "\n " . $team[1]['Name'] . "\n";
        sleep(2);
        echo "\n " . $team[2]['Name'] . "\n";
        sleep(2);
        echo "\n " . $team[3]['Name'] . "\n";
        sleep(2);
        echo "\nWelcome to the Hall of Fame!\n\n";
        sleep(2);

        $hall = fopen('csv/hall.csv', 'a');

        fputcsv($hall, [$player, $team[1]['Name'], $team[2]['Name'], $team[3]['Name'], $region, date('H:i:s d/m/Y')]);
    }
}

<?php
// --- The Hall of Fame of all trainers to defeat a challenge.

class HallOfFame
{
    public function __construct()
    {
        $this->loader = new Loader();
        $this->hall = $this->loader->getData('hall');
    }

    public function show()
    {
        echo "\n--- Hall of Fame ---\n\n";

        $hallMask = " %-20s\t| %-20s\t| %-20s\t| %-20s\t| %-20s\t| %-20s\t|\n";
        printf($hallMask, "Name", "1st Pokémon", "2nd Pokémon", "3rd Pokémon", "Region", "Date");
        foreach ($this->hall as $entry) {
            printf($hallMask, $entry['Name'], $entry['Pokemon 1'], $entry['Pokemon 2'], $entry['Pokemon 3'], $entry['Region'], $entry['Date']);
        }
        echo "\n";

        $menu = new Menu();
        $menu->show();
    }
}

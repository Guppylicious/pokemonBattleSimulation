<?php

namespace Game;

class StandbyPhase extends Loader
{
    /**
     * Prints the remaining HP of a pokémon
     * @param string $name The trainer's name
     * @param array $pokemon The pokémon to show HP for
     * @return string A formatted string with the remaining HP
     */
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
            echo $this->statusColor($pokemon['Status']) . $pokemon['Status'] . "\e[0m";
        }
        printf($mask, $healthBar, $pokemon['HP Left'], $pokemon['HP']);
    }
}

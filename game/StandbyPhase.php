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

        $mask = "\n\t[" . $color . "%-50s\e[0m] %d/%d \t%s\n";

        echo "\n\t" . $name . "'s " . $pokemon['Name'] . ": [";

        $typeMask = Types::typeColor($pokemon['Type 1']) . "%s\e[0m";

        printf($typeMask, "" . $pokemon['Type 1']);

        if ($pokemon['Type 2']) {
            $typeMask = "%1s" . Types::typeColor($pokemon['Type 2']) . "%s\e[0m";
            printf($typeMask, "/", $pokemon['Type 2']);
        }

        echo "]";

        if ($pokemon['Status']) {
            echo " " . Status::statusColor($pokemon['Status']) . $pokemon['Status'] . "\e[0m";
        }

        printf($mask, $healthBar, $pokemon['HP Left'], $pokemon['HP'], $this->printStatMods($pokemon));
    }

    public function printStatMods($pokemon) {
        $stats = array('ATT' => 'Attack', 'DEF' => 'Defence', 'SPA' => 'Sp Attack', 'SPD' => 'Sp Defence', 'SPD' => 'Speed', 'ACC' => 'Accuracy', 'EVA' => 'Evasion');

        $statMods = "";

        foreach ($stats as $shorthand => $stat) {
            if ($pokemon[$stat . ' Mod'] > 0) {
                $statMods .= "\t" . $shorthand . " +" . $pokemon[$stat . ' Mod'];
            } elseif ($pokemon[$stat . ' Mod'] < 0) {
                $statMods .= "\t" . $shorthand . " " . $pokemon[$stat . ' Mod'];
            }
        }

        return $statMods;
    }
}

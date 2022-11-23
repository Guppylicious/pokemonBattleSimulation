<?php

namespace Game;

class Stats
{
    /**
     * Calculate the stat of a pokémon after any stat modifiers
     * @param array $pokemon The pokémon to get stats for
     * @param string $stat The stat to get
     * @return int The stat after modifications
     */
    public function getStat($pokemon, $stat)
    {
        $mod = $pokemon[$stat . ' Mod'];

        if ($mod >= 1) {
            $modifier = ($mod + 2) / 2;
        } elseif ($mod <= -1) {
            $modifier = 2 / (abs($mod) + 2);
        } else {
            $modifier = 2 / 2;
        }

        return round($pokemon[$stat] * $modifier);
    }

    /**
     * Calculate the accuracy or evasion stat of a pokémon after any stat modifiers
     * @param array $pokemon The pokémon to get stats for
     * @param string $stat The stat to get
     * @return int The stat after modifications
     */
    public function getAccuracyStat($pokemon, $stat)
    {
        $mod = $pokemon[$stat . ' Mod'];

        if ($mod >= 1) {
            $modifier = ($mod + 3) / 3;
        } elseif ($mod <= -1) {
            $modifier = 3 / (abs($mod) + 3);
        } else {
            $modifier = 3 / 3;
        }

        return $modifier;
    }

    /**
     * Calculate the effects of a stat changing move
     * @param array $pokemon The pokémon to get stats for
     * @param array $statChanges An list of changes that need to be applied
     * @param int $amount The amount to change the stat by
     * @return int The stat after modifications
     */
    public function statCalc($pokemon, $statChanges, $amount)
    {
        $stats = explode(", ", $statChanges);

        foreach ($stats as $stat) {
            if ($pokemon[$stat . ' Mod'] >= 6) {
                sleep(2);
                echo "\n " . $pokemon['Name'] . "'s " . $stat . " won't go any higher!\n";
            } elseif ($pokemon[$stat . ' Mod'] <= -6) {
                sleep(2);
                echo "\n " . $pokemon['Name'] . "'s " . $stat . " won't go any lower!\n";
            } else {
                $pokemon[$stat . ' Mod'] = $pokemon[$stat . ' Mod'] + $amount;

                sleep(2);
                switch ($amount) {
                    case 1:
                        echo "\n " . $pokemon['Name'] . "'s " . $stat . " rose!\n";
                        break;
                    case 2:
                        echo "\n " . $pokemon['Name'] . "'s " . $stat . " sharply rose!\n";
                        break;
                    case -1:
                        echo "\n " . $pokemon['Name'] . "'s " . $stat . " fell!\n";
                        break;
                    case -2:
                        echo "\n " . $pokemon['Name'] . "'s " . $stat . " harshly fell!\n";
                        break;
                }
            }

            if ($pokemon[$stat . ' Mod'] > 6) {
                $pokemon[$stat . ' Mod'] = 6;
            } elseif ($pokemon[$stat . ' Mod'] < -6) {
                $pokemon[$stat . ' Mod'] = -6;
            }
        }

        return $pokemon;
    }
}

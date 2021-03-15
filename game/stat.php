<?php
// --- All calculations to do with stats

class Stat
{
    public function __construct()
    {
        $this->loader = new Loader();
        $this->moves = $this->loader->getData('moves');
        $this->types = $this->loader->getData('types');
    }

    // --- stat change move calculator
    public function statCalc(&$pokemon, $statChanges, $amount)
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
    }

    // --- calculate the stat of a pokemon after any stat modifiers
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

    // --- calculate accuracy stat after modifiers
    public function getAccuracyMod($pokemon, $stat)
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
}

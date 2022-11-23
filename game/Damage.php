<?php

namespace Game;

class Damage extends Loader
{
    /**
     * Damage calculator based on attacker attack stat and defender defence stat
     * @param array $attacker The pokémon performing the attack
     * @param array $attack The move the pokémon is using to attack
     * @param array $defender The pokémon defending the attack
     * @return array The attacker and defender after any damage or statuses have been applied
     */
    public function damageCalc($attacker, $attack, $defender)
    {
        $modifier = $this->damageModifier($attacker, $attack, $defender);

        $stats = new Stats();

        if ($attack['Category'] == 'Physical') {
            $damage = round((((((2 * 50) / 5) + 2) * ($attack['Power'] * ($stats->getStat($attacker, 'Attack') / $stats->getStat($defender, 'Defence'))) / 50) + 2) * $modifier);
        } else {
            $damage = round((((((2 * 50) / 5) + 2) * ($attack['Power'] * ($stats->getStat($attacker, 'Sp Attack') / $stats->getStat($defender, 'Sp Defence'))) / 50) + 2) * $modifier);
        }

        // minimum damage is 1, unless we're not dealing damage
        if ($damage > 0 && $damage < 1) {
            $damage = 1;
        }

        $damage > $defender['HP Left'] ? $damage = $defender['HP Left'] : $damage; // maximum damage is remaining defender HP

        $attacker['Damage Dealt'] = $damage;
        $attacker['Move Category'] = $attack['Category'];

        return array('attacker' => $attacker, 'defender' => $defender);
    }

    /**
     * Get any modifiers to be applied to the damage
     * @param array $attacker The pokémon performing the attack
     * @param array $attack The move the pokémon is using to attack
     * @param array $defender The pokémon defending the attack
     * @return int The modifier value to be applied
     */
    public function damageModifier($attacker, $attack, $defender)
    {
        $weather = 1;
        $critical = 1;
        $random = rand(85, 100) / 100;
        $stab = 1;
        $type1 = 1;
        $type2 = 1;
        $burn = 1;

        // type and stab modifier
        if ($attacker['Type 1']) {
            if ($attacker['Type 1'] == $attack['Type'] || $attacker['Type 2'] == $attack['Type']) {
                $stab = 1.5;
            }

            $types = new Types();

            $type1 = $types->findTypeMatch($attack['Type'], $defender['Type 1']);

            if ($defender['Type 2']) {
                $type2 = $types->findTypeMatch($attack['Type'], $defender['Type 2']);
            }
        }

        $type = $type1 * $type2;

        if ($type > 1) {
            sleep(2);
            echo "\n It's super effective!";
        } elseif ($type < 1) {
            sleep(2);

            if ($type == 0) {
                echo "\n It has no effect!\n";
            } else {
                echo "\n It's not very effective!";
            }
        }

        // only continue calculations if move has effect
        if ($type != 0) {
            // critical modifier
            $crit = $attacker['Critical'] + $attack['Critical'];

            if ($crit == 0) {
                $critChance = 6;
            } elseif ($crit == 1) {
                $critChance = 12;
            } elseif ($crit == 2) {
                $critChance = 50;
            } elseif ($crit >= 3) {
                $critChance = 100;
            } else {
                $critChance = 0;
            }

            $critHit = rand(0, 100);

            if ($critHit <= $critChance) {
                sleep(2);
                echo "\n A critical hit!";
                $critical = 2;
            }

            // burn modifier
            if ($attacker['Status'] == 'BRN' && $attack['Category'] == 'Physical') {
                $burn = 0.5;
            }
        }

        return $weather * $critical * $random * $stab * $type * $burn;
    }

    /**
     * Calculate the percent to heal
     * @param array $pokemon The pokémon to heal
     * @param int $percent The percentage of health to heal
     * @return array The pokémon after healing
     */
    public function healPercent($pokemon, $percent)
    {
        $pokemon['Damage Dealt'] = 0;
        $heal = round($pokemon['HP'] / (100 / $percent));
        $diff = $pokemon['HP'] - $pokemon['HP Left'];

        sleep(2);

        if ($diff == 0) {
            echo "\n It has no effect!\n";
        } else {
            $heal > $diff ? $heal = $diff : $heal;
            $pokemon['HP Left'] = $pokemon['HP Left'] + $heal;

            echo "\n " . $pokemon['Name'] . " regained " . $heal . " health!\n";
        }

        return $pokemon;
    }

    /**
     * Calculate the amount to heal from damage given
     * @param array $pokemon The pokémon to heal
     * @param int $percent The percentage of health to heal
     * @return array The pokémon after healing
     */
    public function healDamage($pokemon, $percent)
    {
        $heal = round($pokemon['Damage Dealt'] / (100 / $percent));
        $diff = $pokemon['HP'] - $pokemon['HP Left'];

        if ($diff != 0) {
            $heal > $diff ? $heal = $diff : $heal;
            $pokemon['HP Left'] = $pokemon['HP Left'] + $heal;

            sleep(2);

            echo "\n " . $pokemon['Name'] . " regained " . $heal . " health!\n";
        }

        return $pokemon;
    }

    /**
     * Calculations based on damage just dealt
     * @param array $attacker The attacking pokémon
     * @param array $attack The attack that just happened
     * @param array $defender The defending pokémon
     * @return array The attacker and defender after any damage has been applied
     */
    public function postDamage($attacker, $attack, $defender)
    {
        if ($attacker['Damage Dealt'] > 0) {
            sleep(2);
            echo "\n It deals " . $attacker['Damage Dealt'] . " damage!\n";

            $defender['HP Left'] = $defender['HP Left'] - $attacker['Damage Dealt'];

            if ($attack['Type'] == 'Fire' && $defender['Status'] == 'FRZ') {
                echo "\n" . $defender['Name'] . " thawed out!\n";

                $defender['Status'] = '';
            }
        }

        if ($defender['HP Left'] > 0) {
            if ($attack['Stat']) {
                $statHit = rand(0, 100);

                if ($statHit <= $attack['Stat Chance']) {
                    $stats = new Stats();

                    if ($attack['Target'] == 'User') {
                        $attacker = $stats->statCalc($attacker, $attack['Stat'], $attack['Amount']);
                    } elseif ($attack['Target'] == 'Foe') {
                        $defender = $stats->statCalc($defender, $attack['Stat'], $attack['Amount']);
                    }
                }
            }

            $status = new Status();

            if ($attack['Status'] && !$defender['Status']) {
                $statusHit = rand(0, 100);

                if ($statusHit <= $attack['Status Chance']) {
                    $defender = $status->statusAttack($defender, $attack['Status']);
                }
            }

            if ($attack['Flinch']) {
                $defender = $status->flinch($defender, $attack['Flinch']);
            }
        }

        if ($attack['Recoil'] > 0) {
            sleep(2);
            echo "\n " . $attacker['Name'] . " is hit with recoil!\n";

            $recoil = round($attacker['Damage Dealt'] * $attack['Recoil']);

            echo "\n It deals " . $recoil . " damage!\n";

            $attacker['HP Left'] = $attacker['HP Left'] - $recoil;
        }

        return array('attacker' => $attacker, 'defender' => $defender);
    }
}

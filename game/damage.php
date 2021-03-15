<?php
// --- All calculations to do with damage

class Damage
{
    public function __construct()
    {
        $this->loader = new Loader();
        $this->stat = new Stat();
        $this->status = new Status();
        $this->moves = $this->loader->getData('moves');
        $this->types = $this->loader->getData('types');
    }

    // --- damage calculator based on attacker attack stat and defender defence stat
    public function damageCalc(&$attacker, $attack, $defender)
    {
        $modifier = $this->damageModifier($attacker, $attack, $defender);

        if ($attack['Category'] == 'Physical') {
            $damage = round((((((2 * 50) / 5) + 2) * ($attack['Power'] * ($this->stat->getStat($attacker, 'Attack') / $this->stat->getStat($defender, 'Defence'))) / 50) + 2) * $modifier);
        } else {
            $damage = round((((((2 * 50) / 5) + 2) * ($attack['Power'] * ($this->stat->getStat($attacker, 'Sp Attack') / $this->stat->getStat($defender, 'Sp Defence'))) / 50) + 2) * $modifier);
        }

        $damage < 1 ? $damage = 1 : $damage; // minimum damage is 1
        $damage > $defender['HP Left'] ? $damage = $defender['HP Left'] : $damage; // maximum damage is remaining defender HP

        $attacker['Damage Dealt'] = $damage;
        $attacker['Move Category'] = $attack['Category'];
    }

    // --- handler for damage modifiers
    public function damageModifier($attacker, $attack, $defender)
    {
        $weather = 1;
        $critical = 1;
        $random = rand(85, 100) / 100;
        $stab = 1;
        $type1 = 1;
        $type2 = 1;
        $burn = 1;

        // weather modifier
        // TODO
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
        // type and stab modifier
        if ($attacker['Type 1']) {
            if ($attacker['Type 1'] == $attack['Type'] || $attacker['Type 2'] == $attack['Type']) {
                $stab = 1.5;
            }

            $type1 = $this->loader->findTypeMatch($this->types, $attack['Type'], $defender['Type 1']);
            if ($defender['Type 2']) {
                $type2 = $this->loader->findTypeMatch($this->types, $attack['Type'], $defender['Type 2']);
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
        // burn modifier
        if ($attacker['Status'] == 'BRN' && $attack['Category'] == 'Physical') {
            $burn = 0.5;
        }

        return $weather * $critical * $random * $stab * $type * $burn;
    }

    // --- heal calculator based on percentage of max health
    public function healPercent(&$pokemon, $percent)
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
    }

    // --- heal calculator based on damage dealt
    public function healDamage(&$pokemon, $percent)
    {
        $heal = round($pokemon['Damage Dealt'] / (100 / $percent));
        $diff = $pokemon['HP'] - $pokemon['HP Left'];
        if ($diff != 0) {
            $heal > $diff ? $heal = $diff : $heal;
            $pokemon['HP Left'] = $pokemon['HP Left'] + $heal;
            sleep(2);
            echo "\n " . $pokemon['Name'] . " regained " . $heal . " health!\n";
        }
    }

    // --- processes to be done straight after damage
    public function postDamage(&$attacker, $attack, &$defender)
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
                    if ($attack['Target'] == 'User') {
                        $this->stat->statCalc($attacker, $attack['Stat'], $attack['Amount']);
                    } elseif ($attack['Target'] == 'Foe') {
                        $this->stat->statCalc($defender, $attack['Stat'], $attack['Amount']);
                    }
                }
            }

            if ($attack['Status'] && !$defender['Status']) {
                $statusHit = rand(0, 100);
                if ($statusHit <= $attack['Status Chance']) {
                    $this->status->statusAttack($defender, $attack['Status']);
                }
            }

            if ($attack['Flinch']) {
                $this->status->flinch($defender, $attack['Flinch']);
            }
        }

        if ($attack['Recoil'] > 0) {
            sleep(2);
            echo "\n " . $attacker['Name'] . " is hit with recoil!\n";
            $recoil = round($attacker['Damage Dealt'] * $attack['Recoil']);
            echo "\n It deals " . $recoil . " damage!\n";
            $attacker['HP Left'] = $attacker['HP Left'] - $recoil;
        }
    }
}

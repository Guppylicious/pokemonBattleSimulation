<?php

namespace Game;

class SpecialAttacks extends Loader
{
    /**
     * Handler for attacks that have other effects
     * @param array $attacker The pokémon performing the attack
     * @param array $attack The move the pokémon is using to attack
     * @param array $defender The pokémon defending the attack
     * @return array The attacker and defender after any damage or statuses have been applied
     */
    public function specialAttack($attacker, $attack, $defender)
    {
        $attackFunction = lcfirst(str_replace(" ", "", $attack['Name']));

        $afterSpecialAttack = $this->$attackFunction($attacker, $attack, $defender);

        return array('attacker' => $afterSpecialAttack['attacker'], 'defender' => $afterSpecialAttack['defender']);
    }

    /**
     * Deal damage as it usually would be done after any special effects take place
     * @param array $attacker The pokémon performing the attack
     * @param array $attack The move the pokémon is using to attack
     * @param array $defender The pokémon defending the attack
     * @return array The attacker and defender after any damage has been applied
     */
    public function dealDamage($attacker, $attack, $defender)
    {
        $damage = new Damage();

        $damageDealt = $damage->damageCalc($attacker, $attack, $defender);
        $damageTotal = $damage->postDamage($damageDealt['attacker'], $attack, $damageDealt['defender']);

        return $damageTotal;
    }

    /**
     * Handler for if a move fails
     * @param array $pokemon The pokémon that has failed an attack
     * @return array The pokémon after the failed attack attempt
     */
    public function itFailed($pokemon)
    {
        sleep(2);
        echo "\n It failed!\n";

        $pokemon['Damage Dealt'] = 0;

        return $pokemon;
    }

    /**
     * Handler for fatigue that occurs at the end of a move
     * @param array $pokemon The pokémon becoming fatigued
     * @param array $move The move the pokémon is using to attack
     * @param int $min The minimum number of turns the move can be
     * @param int $max The maximum number of turns the move can be
     * @return array The pokémon after an fatigue has been applied
     */
    public function fatigue($pokemon, $move, $min, $max)
    {
        if ($pokemon['Move Turns'] == $move['Turns']) {
            $pokemon['Move Turns'] = rand($min, $max);
        }
        if ($pokemon['Move Turns'] == 1 && $pokemon['Confusion'] == -1) {
            $status = new Status();

            $status->statusCalc($pokemon, 'CON');
        }

        return $pokemon;
    }

    /*************************
    SPECIAL MOVE HANDLER (A-Z)
    *************************/

    /**
     * Handler for the move Absorb
     * @param array $attacker The pokémon performing the attack
     * @param array $attack The move the pokémon is using to attack
     * @param array $defender The pokémon defending the attack
     * @return array The attacker and defender after the attack has happened
     */
    public function absorb($attacker, $attack, $defender)
    {
        $damage = new Damage();

        $damageDealt = $this->dealDamage($attacker, $attack, $defender);
        $healedAttacker = $damage->healDamage($damageDealt['attacker'], 50);

        return array('attacker' => $healedAttacker, 'defender' => $damageDealt['defender']);
    }

    /**
     * Handler for the move Counter
     * @param array $attacker The pokémon performing the attack
     * @param array $attack The move the pokémon is using to attack
     * @param array $defender The pokémon defending the attack
     * @return array The attacker and defender after the attack has happened
     */
    public function counter($attacker, $attack, $defender)
    {
        $counter = array('attacker' => $attacker, 'defender' => $defender);

        if ($defender['Move Category'] == 'Physical') {
            $attacker['Damage Dealt'] = $defender['HP Left'] < $defender['Damage Dealt'] * 2 ? $defender['HP Left'] : $defender['Damage Dealt'] * 2;

            $damage = new Damage();

            $counter = $damage->postDamage($attacker, $attack, $defender);
        } else {
            $counter['attacker'] = $this->itFailed($attacker);
        }

        return $counter;
    }

    /**
     * Handler for the move Hidden Power
     * @param array $attacker The pokémon performing the attack
     * @param array $attack The move the pokémon is using to attack
     * @param array $defender The pokémon defending the attack
     * @return array The attacker and defender after the attack has happened
     */
    public function hiddenPower($attacker, $attack, $defender)
    {
        $randType = rand(1, 18);
        $count = 1;

        foreach ($this->types as $t) {
            if ($randType == $count) {
                $attack['Type'] = $t['Type'];
                break;
            } else {
                $count++;
            }
        }

        sleep(2);
        echo "\n Hidden Power became a " . $this->typeColor($attack['Type']) . $attack['Type'] . "\e[0m move!\n";

        $damageTotal = $this->dealDamage($attacker, $attack, $defender);

        return $damageTotal;
    }

    /**
     * Handler for the move Metronome
     * @param array $attacker The pokémon performing the attack
     * @param array $attack The move the pokémon is using to attack
     * @param array $defender The pokémon defending the attack
     * @return array The attacker and defender after the attack has happened
     */
    public function metronome($attacker, $attack, $defender)
    {
        $move = $this->findRandom('moves');

        sleep(2);
        echo "\n " . $attacker['Name'] . " used " . $this->getMoveType($move['Name']) . $move['Name'] . "\e[0m!\n";

        $attack = new Attack();

        return $attack->start($attacker, $move, $defender);
    }

    /**
     * Handler for the move Mirror Coat
     * @param array $attacker The pokémon performing the attack
     * @param array $attack The move the pokémon is using to attack
     * @param array $defender The pokémon defending the attack
     * @return array The attacker and defender after the attack has happened
     */
    public function mirrorCoat($attacker, $attack, $defender)
    {
        $mirrorCoat = array('attacker' => $attacker, 'defender' => $defender);

        if ($defender['Type 1'] == 'Dark' || $defender['Type 2'] == 'Dark') {
            sleep(2);
            echo "\n It has no effect!\n";
        } elseif ($defender['Move Category'] == 'Special') {
            $attacker['Damage Dealt'] = $defender['HP Left'] < $defender['Damage Dealt'] * 2 ? $defender['HP Left'] : $defender['Damage Dealt'] * 2;

            $damage = new Damage();

            $mirrorCoat = $damage->postDamage($attacker, $attack, $defender);
        } else {
            $mirrorCoat['attacker'] = $this->itFailed($attacker);
        }

        return $mirrorCoat;
    }

    /**
     * Handler for the move Outrage
     * @param array $attacker The pokémon performing the attack
     * @param array $attack The move the pokémon is using to attack
     * @param array $defender The pokémon defending the attack
     * @return array The attacker and defender after the attack has happened
     */
    public function outrage($attacker, $attack, $defender)
    {
        $attacker = $this->fatigue($attacker, $attack, 2, 3);

        $damageTotal = $this->dealDamage($attacker, $attack, $defender);

        return $damageTotal;
    }

    /**
     * Handler for the move Petal Dance
     * @param array $attacker The pokémon performing the attack
     * @param array $attack The move the pokémon is using to attack
     * @param array $defender The pokémon defending the attack
     * @return array The attacker and defender after the attack has happened
     */
    public function petalDance($attacker, $attack, $defender)
    {
        $attacker = $this->fatigue($attacker, $attack, 2, 3);

        $damageTotal = $this->dealDamage($attacker, $attack, $defender);

        return $damageTotal;
    }

    /**
     * Handler for the move Recover
     * @param array $attacker The pokémon performing the attack
     * @param array $attack The move the pokémon is using to attack
     * @param array $defender The pokémon defending the attack
     * @return array The attacker and defender after the attack has happened
     */
    public function recover($attacker, $attack, $defender)
    {
        $damage = new Damage();

        $attacker = $damage->healPercent($attacker, 50);

        return array('attacker' => $attacker, 'defender' => $defender);
    }

    /**
     * Handler for the move Rollout
     * @param array $attacker The pokémon performing the attack
     * @param array $attack The move the pokémon is using to attack
     * @param array $defender The pokémon defending the attack
     * @return array The attacker and defender after the attack has happened
     */
    public function rollout($attacker, $attack, $defender)
    {
        if ($attacker['Move Turns'] == $attack['Turns']) {
            $damageTotal = $this->dealDamage($attacker, $attack, $defender);
        } else {
            $attacker['Damage Dealt'] = $attacker['Damage Dealt'] * 2;

            $damage = new Damage();

            $damageTotal = $damage->postDamage($attacker, $attack, $defender);
        }

        return $damageTotal;
    }

    /**
     * Handler for the move Present
     * @param array $attacker The pokémon performing the attack
     * @param array $attack The move the pokémon is using to attack
     * @param array $defender The pokémon defending the attack
     * @return array The attacker and defender after the attack has happened
     */
    public function present($attacker, $attack, $defender)
    {
        $present = array('attacker' => $attacker, 'defender' => $defender);

        $presentHit = rand(0, 100);

        if ($presentHit <= 40) {
            $attack['Power'] = 40;

            $present = $this->dealDamage($attacker, $attack, $defender);
        } elseif ($presentHit > 40 && $presentHit <= 70) {
            $attack['Power'] = 80;

            $present = $this->dealDamage($attacker, $attack, $defender);
        } elseif ($presentHit > 70 && $presentHit <= 80) {
            $attack['Power'] = 120;

            $present = $this->dealDamage($attacker, $attack, $defender);
        } else {
            $damage = new Damage();

            $present['defender'] = $damage->healPercent($defender, 20);
        }

        return $present;
    }

    /**
     * Handler for the move Sketch
     * @param array $attacker The pokémon performing the attack
     * @param array $attack The move the pokémon is using to attack
     * @param array $defender The pokémon defending the attack
     * @return array The attacker and defender after the attack has happened
     */
    public function sketch($attacker, $attack, $defender)
    {
        $attackerMoves = array();

        for ($i = 1; $i <= 4; $i++) {
            if ($attacker['Move ' . $i]) {
                array_push($attackerMoves, $attacker['Move ' . $i]);
            }
        }

        $defenderMoves = array();

        for ($j = 1; $j <= 4; $j++) {
            if ($defender['Move ' . $j] && $defender['Move ' . $j] != 'Sketch' && !in_array($defender['Move ' . $j], $attackerMoves)) {
                array_push($defenderMoves, $defender['Move ' . $j]);
            }
        }

        if (empty($defenderMoves)) {
            $attacker = $this->itFailed($attacker);
        } else {
            $sketched = rand(0, count($defenderMoves) - 1);

            $move = $this->find($defenderMoves[$sketched], 'moves');

            $attacker['Move ' . $attacker['Current Move']] = $move['Name'];
            $attacker['PP'][$attacker['Current Move']] = $move['PP'];

            sleep(2);
            echo "\n " . $attacker['Name'] . " sketched " . $this->getMoveType($defenderMoves[$sketched]) . $defenderMoves[$sketched] . "\e[0m!\n";
        }

        return array('attacker' => $attacker, 'defender' => $defender);
    }

    /**
     * Handler for the move Sucker Punch
     * @param array $attacker The pokémon performing the attack
     * @param array $attack The move the pokémon is using to attack
     * @param array $defender The pokémon defending the attack
     * @return array The attacker and defender after the attack has happened
     */
    public function suckerPunch($attacker, $attack, $defender)
    {
        $suckerPunch = array('attacker' => $attacker, 'defender' => $defender);

        $defenderMove = $this->find($defender['Move ' . $defender['Current Move']], 'moves');

        if ($defenderMove['Category'] == 'Physical' || $defenderMove['Category'] == 'Special') {
            $suckerPunch = $this->dealDamage($attacker, $attack, $defender);
        } else {
            $suckerPunch['attacker'] = $this->itFailed($attacker);
        }

        return $suckerPunch;
    }

    /**
     * Handler for the move Transform
     * @param array $attacker The pokémon performing the attack
     * @param array $attack The move the pokémon is using to attack
     * @param array $defender The pokémon defending the attack
     * @return array The attacker and defender after the attack has happened
     */
    public function transform($attacker, $attack, $defender)
    {
        $attacker['Type 1'] = $defender['Type 1'];
        $defender['Type 2'] ? $attacker['Type 2'] = $defender['Type 2'] : $attacker['Type 2'] = '';
        $attacker['Attack'] = $defender['Attack'];
        $attacker['Defence'] = $defender['Defence'];
        $attacker['Sp Attack'] = $defender['Sp Attack'];
        $attacker['Sp Defence'] = $defender['Sp Defence'];
        $attacker['Speed'] = $defender['Speed'];

        for ($i = 1; $i <= 4; $i++) {
            if ($defender['Move ' . $i]) {
                $attacker['Move ' . $i] = $defender['Move ' . $i];
                $attacker['PP'][$i] = 5;
            } else {
                $attacker['Move ' . $i] = '';
                $attacker['PP'][$i] = 0;
            }
        }

        $attacker['Attack Mod'] = $defender['Attack Mod'];
        $attacker['Defence Mod'] = $defender['Defence Mod'];
        $attacker['Sp Attack Mod'] = $defender['Sp Attack Mod'];
        $attacker['Sp Defence Mod'] = $defender['Sp Defence Mod'];
        $attacker['Speed Mod'] = $defender['Speed Mod'];
        $attacker['Accuracy Mod'] = $defender['Accuracy Mod'];
        $attacker['Evasion Mod'] = $defender['Evasion Mod'];

        sleep(2);
        echo "\n " . $attacker['Name'] . " transformed into " . $defender['Name'] . "!\n";

        return array('attacker' => $attacker, 'defender' => $defender);
    }

    /**
     * Handler for the move Tri Attack
     * @param array $attacker The pokémon performing the attack
     * @param array $attack The move the pokémon is using to attack
     * @param array $defender The pokémon defending the attack
     * @return array The attacker and defender after the attack has happened
     */
    public function triAttack($attacker, $attack, $defender)
    {
        $status = rand(1, 3);

        switch ($status) {
            case 1:
                $attack['Status'] = 'PAR';
                break;
            case 2:
                $attack['Status'] = 'BRN';
                break;
            case 3:
                $attack['Status'] = 'FRZ';
                break;
        }

        $damageTotal = $this->dealDamage($attacker, $attack, $defender);

        return $damageTotal;
    }

    /**
     * Handler for the move Venoshock
     * @param array $attacker The pokémon performing the attack
     * @param array $attack The move the pokémon is using to attack
     * @param array $defender The pokémon defending the attack
     * @return array The attacker and defender after the attack has happened
     */
    public function venoshock($attacker, $attack, $defender)
    {
        if ($defender['Status'] == 'PSN') {
            $attack['Power'] = $attack['Power'] * 2;
        }

        $damageTotal = $this->dealDamage($attacker, $attack, $defender);

        return $damageTotal;
    }
}

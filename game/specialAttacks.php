<?php
// --- All attacks that have unique effects

class SpecialAttacks
{
    public function __construct()
    {
        $this->loader = new Loader();
        $this->standbyPhase = new StandbyPhase();
        $this->damage = new Damage();
        $this->status = new Status_Controller();
        $this->moves = $this->loader->getData('moves');
        $this->types = $this->loader->getData('types');
    }

    // --- get attack to call from name
    public function getAttack($attack)
    {
        return $attack = lcfirst(str_replace(" ", "", $attack));
    }

    // --- handler for damage attacks that have unique effects
    public function specialAttack(&$attacker, &$attack, &$defender)
    {
        $function = $this->getAttack($attack['Name']);
        $this->$function($attacker, $attack, $defender);
    }

    // --- handler for if a move fails
    public function itFailed(&$pokemon)
    {
        sleep(2);
        echo "\n It failed!\n";
        $pokemon['Damage Dealt'] = 0;
    }

    // --- handler for fatigue that occurs at the end of a move
    public function fatigue(&$pokemon, $move, $min, $max)
    {
        if ($pokemon['Move Turns'] == $move['Turns']) {
            $pokemon['Move Turns'] = rand($min, $max);
        }
        if ($pokemon['Move Turns'] == 1 && $pokemon['Confusion'] == -1) {
            $this->status->statusCalc($pokemon, 'CON');
        }
    }

// --- Special Move handlers (A-Z)

    // --- handler for the move absorb
    public function absorb(&$attacker, $attack, &$defender)
    {
        $this->damage->damageCalc($attacker, $attack, $defender);
        $this->damage->postDamage($attacker, $attack, $defender);
        $this->damage->healDamage($attacker, 50);
    }

    // --- handler for the move counter
    public function counter(&$attacker, $attack, $defender)
    {
        if ($defender['Move Category'] == 'Physical') {
            $attacker['Damage Dealt'] = $defender['HP Left'] < $defender['Damage Dealt'] * 2 ? $defender['HP Left'] : $defender['Damage Dealt'] * 2;
            $this->damage->postDamage($attacker, $attack, $defender);
        } else {
            $this->itFailed($attacker);
        }
    }

    // --- choose random type for hidden power
    public function hiddenPower(&$attacker, &$attack, &$defender)
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
        echo "\n Hidden Power became a " . $this->loader->typeColor($attack['Type']) . $attack['Type'] . "\e[0m move!\n";
        $this->damage->damageCalc($attacker, $attack, $defender);
        $this->damage->postDamage($attacker, $attack, $defender);
    }

    // --- handler for the move metronome
    public function metronome(&$attacker, &$attack, &$defender)
    {
        $move = $this->loader->find(rand(1, $this->loader->counter('moves')), $this->moves);
        sleep(2);
        echo "\n " . $attacker['Name'] . " used " . $this->loader->getMoveType($move['Name']) . $move['Name'] . "\e[0m!\n";
        $this->start($attacker, $move, $defender);
    }

    // --- handler for the move mirror coat
    public function mirrorCoat(&$attacker, $attack, $defender)
    {
        if ($defender['Type 1'] == 'Dark' || $defender['Type 2'] == 'Dark') {
            sleep(2);
            echo "\n It has no effect!\n";
        } elseif ($defender['Move Category'] == 'Special') {
            $attacker['Damage Dealt'] = $defender['HP Left'] < $defender['Damage Dealt'] * 2 ? $defender['HP Left'] : $defender['Damage Dealt'] * 2;
            $this->damage->postDamage($attacker, $attack, $defender);
        } else {
            $this->itFailed($attacker);
        }
    }

    // --- handler for the move outrage
    public function outrage(&$attacker, $attack, $defender)
    {
        $this->fatigue($attacker, $attack, 2, 3);
        $this->damage->damageCalc($attacker, $attack, $defender);
        $this->damage->postDamage($attacker, $attack, $defender);
    }

    // --- handler for the move outrage
    public function petalDance(&$attacker, $attack, $defender)
    {
        $this->fatigue($attacker, $attack, 2, 3);
        $this->damage->damageCalc($attacker, $attack, $defender);
        $this->damage->postDamage($attacker, $attack, $defender);
    }

    //handler for the move recover
    public function recover(&$attacker, $attack, $defender)
    {
        $this->damage->healPercent($attacker, 50);
    }

    // --- handler for the move rollout
    public function rollout(&$attacker, $attack, $defender)
    {
        if ($attacker['Move Turns'] == $attack['Turns']) {
            $this->damage->damageCalc($attacker, $attack, $defender);
            $this->damage->postDamage($attacker, $attack, $defender);
        } else {
            $attacker['Damage Dealt'] = $attacker['Damage Dealt'] * 2;
            $this->damage->postDamage($attacker, $attack, $defender);
        }
    }

    // --- handler for the move present
    public function present(&$attacker, &$attack, &$defender)
    {
        $presentHit = rand(0, 100);
        if ($presentHit <= 40) {
            $attack['Power'] = 40;
            $this->damage->damageCalc($attacker, $attack, $defender);
            $this->damage->postDamage($attacker, $attack, $defender);
        } elseif ($presentHit > 40 && $presentHit <= 70) {
            $attack['Power'] = 80;
            $this->damage->damageCalc($attacker, $attack, $defender);
            $this->damage->postDamage($attacker, $attack, $defender);
        } elseif ($presentHit > 70 && $presentHit <= 80) {
            $attack['Power'] = 120;
            $this->damage->damageCalc($attacker, $attack, $defender);
            $this->damage->postDamage($attacker, $attack, $defender);
        } else {
            $this->damage->heal($defender, 25);
        }
    }

    // --- handler for the move sketch
    public function sketch(&$attacker, $attack, $defender)
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
            $this->itFailed($attacker);
        } else {
            $sketched = rand(0, count($defenderMoves) - 1);
            $move = $this->loader->findByName($defenderMoves[$sketched], $this->moves);
            $attacker['Move ' . $attacker['Current Move']] = $move['Name'];
            $attacker['M' . $attacker['Current Move'] . ' PP'] = $move['PP'];
            sleep(2);
            echo "\n " . $attacker['Name'] . " sketched " . $this->loader->getMoveType($defenderMoves[$sketched]) . $defenderMoves[$sketched] . "\e[0m!\n";
        }
    }

    // --- handler for the move sucker punch
    public function suckerPunch(&$attacker, $attack, $defender)
    {
        $defenderMove = $this->loader->findByName($defender['Move ' . $defender['Current Move']], $this->moves);
        if ($defenderMove['Category'] == 'Physical' || $defenderMove['Category'] == 'Special') {
            $this->damage->damageCalc($attacker, $attack, $defender);
            $this->damage->postDamage($attacker, $attack, $defender);
        } else {
            $this->itFailed($attacker);
        }
    }

    // --- handler for the move transform
    public function transform(&$attacker, $attack, $defender)
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
                $attacker['M' . $i . ' PP'] = 5;
            } else {
                $attacker['Move ' . $i] = '';
                $attacker['M' . $i . ' PP'] = 0;
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
    }

    // --- choose random status for tri attack
    public function triAttack(&$attacker, &$attack, &$defender)
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

        $this->damage->damageCalc($attacker, $attack, $defender);
        $this->damage->postDamage($attacker, $attack, $defender);
    }

    // --- handler for the move venoshock
    public function venoshock(&$attacker, &$attack, $defender)
    {
        if ($defender['Status'] == 'PSN') {
            $attack['Power'] = $attack['Power'] * 2;
        }
        $this->damage->damageCalc($attacker, $attack, $defender);
        $this->damage->postDamage($attacker, $attack, $defender);
    }
}

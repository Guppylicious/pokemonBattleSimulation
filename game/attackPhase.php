<?php
// --- Processes and calculations of a battle's attack phase

class AttackPhase
{
    public function __construct()
    {
        $this->loader = new Loader();
        $this->standbyPhase = new StandbyPhase();
        $this->specialAttacks = new SpecialAttacks();
        $this->attack = new Attack();
        $this->damage = new Damage();
        $this->status = new Status();
        $this->stat = new Stat();
        $this->moves = $this->loader->getData('moves');
        $this->types = $this->loader->getData('types');
    }

    public function start($player, &$playerPokemon, $playerAttack, $playerTeam, &$playerFaints, $computer, &$computerPokemon, $computerAttack, $computerTeam, &$computerFaints)
    {
        if ($playerAttack['Priority'] >= $computerAttack['Priority']) {
            $this->startAttacks($player, $playerPokemon, $playerAttack, $playerTeam, $playerFaints, $computer, $computerPokemon, $computerAttack, $computerTeam, $computerFaints);
        } elseif ($playerAttack['Priority'] < $computerAttack['Priority']) {
            $this->startAttacks($computer, $computerPokemon, $computerAttack, $computerTeam, $computerFaints, $player, $playerPokemon, $playerAttack, $playerTeam, $playerFaints);
        } else {
            if ($this->getStat($playerPokemon, 'Speed') >= $this->getStat($computerPokemon, 'Speed')) {
                $this->startAttacks($player, $playerPokemon, $playerAttack, $playerTeam, $playerFaints, $computer, $computerPokemon, $computerAttack, $computerTeam, $computerFaints);
            } else {
                $this->startAttacks($computer, $computerPokemon, $computerAttack, $computerTeam, $computerFaints, $player, $playerPokemon, $playerAttack, $playerTeam, $playerFaints);
            }
        }

        if ($computerPokemon['Status']) {
            $this->status->statusDamage($computerPokemon);
            if ($computerPokemon['HP Left'] <= 0) {
                $this->faint($computerPokemon, $computerTeam, $computer, $computerFaints);
            }
        }
        if ($playerPokemon['Status']) {
            $this->status->statusDamage($playerPokemon);
            if ($playerPokemon['HP Left'] <= 0) {
                $this->faint($playerPokemon, $playerTeam, $player, $playerFaints);
            }
        }
    }

    // --- the first attack in an attack phase
    public function startAttacks($fastTrainer, &$fastPokemon, $fastMove, $fastTeam, &$fastFaints, $slowTrainer, &$slowPokemon, $slowMove, $slowTeam, &$slowFaints)
    {
        $this->attack($fastTrainer, $fastPokemon, $fastMove, $slowPokemon);
        if ($slowPokemon['HP Left'] <= 0 || $fastPokemon['HP Left'] <= 0) {
            if ($slowPokemon['HP Left'] <= 0) {
                $this->faint($slowPokemon, $slowTeam, $slowTrainer, $slowFaints);
            }
            if ($fastPokemon['HP Left'] <= 0) {
                $this->faint($fastPokemon, $fastTeam, $fastTrainer, $fastFaints);
            }
        } else {
            $this->attack($slowTrainer, $slowPokemon, $slowMove, $fastPokemon);
            if ($fastPokemon['HP Left'] <= 0 || $slowPokemon['HP Left'] <= 0) {
                if ($fastPokemon['HP Left'] <= 0) {
                    $this->faint($fastPokemon, $fastTeam, $fastTrainer, $fastFaints);
                }
                if ($slowPokemon['HP Left'] <= 0) {
                    $this->faint($slowPokemon, $slowTeam, $slowTrainer, $slowFaints);
                }
            }
        }
    }

    // --- an attack done by a pokemon
    public function attack($attackerTrainer, &$attackerPokemon, $attackerMove, &$defenderPokemon)
    {
        if ($attackerMove['Name'] == 'Struggle') {
            sleep(2);
            echo "\n" . $attackerTrainer . "'s " . $attackerPokemon['Name'] . " has no moves left!";
            sleep(2);
            echo "\n" . $attackerPokemon['Name'] . " used Struggle!\n";
        } else {
            sleep(2);
            if ($attackerPokemon['Move Turns'] == $attackerMove['Turns']) {
                echo "\n" . $attackerTrainer . ":\t " . $attackerPokemon['Name'] . "! Use " . $this->loader->getMoveType($attackerMove['Name']) . $attackerMove['Name'] . "\e[0m!\n";
                $attack = $attackerPokemon['Current Move'];
                $attackerPokemon['M' . $attack . ' PP'] = $attackerPokemon['M' . $attack . ' PP'] - 1;
            } else {
                echo $attackerPokemon['Name'] . " continued to use " . $this->loader->getMoveType($attackerMove['Name']) . $attackerMove['Name'] . "\e[0m!\n";
            }
        }

        if (!$this->status->checkFlinch($attackerPokemon)) {
            if (!$this->status->statusCheck($attackerPokemon)) {
                if (!$this->status->confusion($attackerPokemon)) {
                    if ($this->accuracyCheck($attackerPokemon, $attackerMove, $defenderPokemon)) {
                        $this->attack->start($attackerPokemon, $attackerMove, $defenderPokemon);
                    } else {
                        sleep(2);
                        echo "\n It missed!\n";
                        $attackerPokemon['Move Turns'] = 0;
                    }
                }
            }
        }
    }

    // --- accuracy check based off accuracy and evasion stats
    public function accuracyCheck($attacker, $attack, $defender)
    {
        if ($attack['Accuracy'] == '-') {
            return true;
        }
        $accuracyHit = rand(1, 100);
        $accuracyCheck = round($attack['Accuracy'] * ($this->stat->getAccuracyMod($attacker, 'Accuracy') / $this->stat->getAccuracyMod($defender, 'Evasion')));
        if ($accuracyHit <= $accuracyCheck) {
            return true;
        } else {
            return false;
        }
    }

    // --- where pokemon come to faint
    public function faint(&$pokemon, &$team, $name, &$faints)
    {
        sleep(2);
        echo "\n" . $name . "'s " . $pokemon['Name'] . " fainted.\n";
        sleep(2);
        echo "\n" . $name . ":\t " . $pokemon['Name'] . " return!\n\n";

        $faints++;
        if ($faints < 3) {
            $pokemon = $team[$faints + 1];
            $this->standbyPhase->getPP($pokemon);
            $this->standbyPhase->getHP($pokemon);
            $this->standbyPhase->sendOut($name, $pokemon);
        }
    }
}

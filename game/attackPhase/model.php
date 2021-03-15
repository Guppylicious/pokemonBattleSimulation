<?php

class AttackPhase_Model extends Model
{
    public function __construct(Stat $stat, Status_Controller $status)
    {
        parent::__construct();
        $this->stat = $stat;
        $this->status = $status;
    }

    public function getSpeed($pokemon)
    {
        return $this->stat->getStat($pokemon, 'Speed');
    }

    public function getStatusDamage($pokemon)
    {
        return $this->status->statusDamage($pokemon);
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
            $standbyPhase = new StandbyPhase();
            $standbyPhase->getPP($pokemon);
            $standbyPhase->getHP($pokemon);
            $standbyPhase->sendOut($name, $pokemon);
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
    private function attack($attackerTrainer, &$attackerPokemon, $attackerMove, &$defenderPokemon)
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
                        $attack = new Attack_Controller();
                        $attack->start($attackerPokemon, $attackerMove, $defenderPokemon);
                    } else {
                        sleep(2);
                        echo "\n It missed!\n";
                        $attackerPokemon['Move Turns'] = 0;
                    }
                }
            }
        }
    }
}

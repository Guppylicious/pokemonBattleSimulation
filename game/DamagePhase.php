<?php

namespace Game;

class DamagePhase extends Loader
{
    /**
     * An attack of a pokémon
     * @param array $attacker The trainer, pokémon and move being used to attack
     * @param array $defenderPokemon The pokémon defending the attack
     * @return array The attacking and defending pokémon after any damage or status conditions
     */
    public function attack($attacker, $defenderPokemon)
    {
        $attackerTrainer = $attacker['trainer'];
        $attackerPokemon = $attacker['pokemon'];
        $attackerMove = $attacker['move'];

        if ($attackerMove['Name'] == 'Struggle') {
            sleep(2);
            echo "\n" . $attackerTrainer . "'s " . $attackerPokemon['Name'] . " has no moves left!";
            sleep(2);
            echo "\n" . $attackerPokemon['Name'] . " used Struggle!\n";
        } else {
            sleep(2);

            $moves = new Moves();

            if ($attackerPokemon['Move Turns'] == $attackerMove['Turns']) {
                echo "\n" . $attackerTrainer . ":\t " . $attackerPokemon['Name'] . "! Use " . $moves->getMoveType($attackerMove['Name']) . $attackerMove['Name'] . "\e[0m!\n";

                $attack = $attackerPokemon['Current Move'];
                $attackerPokemon['PP'][$attack]--;
            } else {
                echo $attackerPokemon['Name'] . " continued to use " . $moves->getMoveType($attackerMove['Name']) . $attackerMove['Name'] . "\e[0m!\n";
            }
        }

        $status = new Status();

        $attackerPokemon = $status->checkFlinch($attackerPokemon);

        if ($attackerPokemon['Move Turns'] > 0) {
            $attackerPokemon = $status->statusCheck($attackerPokemon);

            if ($attackerPokemon['Move Turns'] > 0) {
                $attackerPokemon = $status->confusion($attackerPokemon);

                if ($attackerPokemon['Move Turns'] > 0) {
                    if ($this->accuracyCheck($attackerPokemon, $attackerMove, $defenderPokemon)) {
                        $attack = new Attack();

                        $afterAttack = $attack->start($attackerPokemon, $attackerMove, $defenderPokemon);

                        $attackerPokemon = $afterAttack['attacker'];
                        $defenderPokemon = $afterAttack['defender'];
                    } else {
                        sleep(2);
                        echo "\n It missed!\n";

                        $attackerPokemon['Move Turns'] = 0;
                    }
                }
            }
        }

        return array('attacker' => $attackerPokemon, 'defender' => $defenderPokemon);
    }

    /**
     * Check if an will land based on accuracy and evasion stats
     * @param array $attacker The pokémon performing the attack
     * @param array $attack The move the pokémon is using to attack
     * @param array $defender The pokémon defending the attack
     * @return boolean If the attack is going to land or not
     */
    public function accuracyCheck($attacker, $attack, $defender)
    {
        if ($attack['Accuracy'] == '-') {
            return true;
        }

        $stats = new Stats();
        $accuracyHit = rand(1, 100);

        $accuracyCheck = round($attack['Accuracy'] * ($stats->getAccuracyStat($attacker, 'Accuracy') / $stats->getAccuracyStat($defender, 'Evasion')));

        return $accuracyHit <= $accuracyCheck ? true : false;
    }
}

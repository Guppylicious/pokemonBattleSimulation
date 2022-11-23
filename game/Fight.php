<?php

namespace Game;

class Fight extends Loader
{
    /**
     * Starts a fight
     * @param string $player The player's name
     * @param array $playerTeam The player's team
     * @param array $computer The computer trainer
     * @param array $computerTeam The computer's team
     * @return boolean The result of the fight, true if win
     */
    public function start($player, $playerTeam, $computer, $computerTeam) {
        /*************
         BATTLE START
        *************/

        sleep(2);
        echo "\n" . $computer['Type'] . " " . $computer['Name'] . " would like to battle!\n";
        sleep(2);
        echo "\n--- Battle start! ---\n\n";

        $playerActivePokemon = $this->sendOut($player, $playerTeam[1]);
        $computerActivePokemon = $this->sendOut($computer['Name'], $computerTeam[1]);

        $playerFaintedPokemon = 0;
        $computerFaintedPokemon = 0;

        $fightOver = false;

        while (!$fightOver) {
            /**************
             STANDBY PHASE
            **************/

            $standbyPhase = new StandbyPhase();

            sleep(2);
            $standbyPhase->printHP($player, $playerActivePokemon);
            $standbyPhase->printHP($computer['Name'], $computerActivePokemon);
            echo "\n\n";
            sleep(2);

            /*************
             ATTACK PHASE
            *************/

            $attackPhase = new AttackPhase();

            if ($playerActivePokemon['Move Turns'] == 0) {
                $playerChosenAttack = $attackPhase->getAttack($playerActivePokemon, true);
                $playerActivePokemon['Current Move'] = $playerChosenAttack[0];
                $playerActivePokemon['Move Turns'] = $playerChosenAttack[1]['Turns'];

                $playerAttack = $playerChosenAttack[1];
            }

            if ($computerActivePokemon['Move Turns'] == 0) {
                $computerChosenAttack = $attackPhase->getAttack($computerActivePokemon);
                $computerActivePokemon['Current Move'] = $computerChosenAttack[0];
                $computerActivePokemon['Move Turns'] = $computerChosenAttack[1]['Turns'];

                $computerAttack = $computerChosenAttack[1];
            }

            $playerMove = array(
                'trainer' => $player,
                'pokemon' => $playerActivePokemon,
                'move' => $playerAttack,
            );

            $computerMove = array(
                'trainer' => $computer['Name'],
                'pokemon' => $computerActivePokemon,
                'move' => $computerAttack,
            );

            /*************
             DAMAGE PHASE
            *************/

            $damagePhase = new DamagePhase();

            if ($playerAttack['Priority'] >= $computerAttack['Priority']) {
                $playerFirst = true;

                $attacks = array('1st' => $playerMove, '2nd' => $computerMove);
            } elseif ($playerAttack['Priority'] < $computerAttack['Priority']) {
                $playerFirst = false;

                $attacks = array('1st' => $computerMove, '2nd' => $playerMove);
            } else {
                if (Stats::getStat($playerActivePokemon, 'Speed') >= Stats::getStat($computerActivePokemon, 'Speed')) {
                    $playerFirst = true;

                    $attacks = array('1st' => $playerMove, '2nd' => $computerMove);
                } else {
                    $playerFirst = false;

                    $attacks = array('1st' => $computerMove, '2nd' => $playerMove);
                }
            }

            $firstMove = $damagePhase->attack($attacks['1st'], $attacks['2nd']['pokemon']);

            $attacks['1st']['pokemon'] = $firstMove['attacker'];
            $attacks['2nd']['pokemon'] = $firstMove['defender'];

            if ($playerFirst) {
                $playerActivePokemon = $firstMove['attacker'];
                $computerActivePokemon = $firstMove['defender'];
            } else {
                $computerActivePokemon = $firstMove['attacker'];
                $playerActivePokemon = $firstMove['defender'];
            }

            if ($playerActivePokemon['HP Left'] == 0 || $computerActivePokemon['HP Left'] == 0) {
                if ($playerActivePokemon['HP Left'] == 0) {
                    $this->faint($player, $playerActivePokemon);

                    $playerFaintedPokemon++;

                    if ($playerFaintedPokemon < 3) {
                        $playerActivePokemon = $this->sendOut($player, $playerTeam[$playerFaintedPokemon + 1]);
                    }
                }

                if ($computerActivePokemon['HP Left'] == 0) {
                    $this->faint($computer['Name'], $computerActivePokemon);

                    $computerFaintedPokemon++;

                    if ($computerFaintedPokemon < 3) {
                        $computerActivePokemon = $this->sendOut($computer['Name'], $computerTeam[$computerFaintedPokemon + 1]);
                    }
                }
            } else {
                $secondMove = $damagePhase->attack($attacks['2nd'], $attacks['1st']['pokemon']);

                $attacks['2nd']['pokemon'] = $secondMove['attacker'];
                $attacks['1st']['pokemon'] = $secondMove['defender'];

                if ($playerFirst) {
                    $computerActivePokemon = $secondMove['attacker'];
                    $playerActivePokemon = $secondMove['defender'];
                } else {
                    $playerActivePokemon = $secondMove['attacker'];
                    $computerActivePokemon = $secondMove['defender'];
                }

                if ($playerActivePokemon['HP Left'] == 0) {
                    $this->faint($player, $playerActivePokemon);

                    $playerFaintedPokemon++;

                    if ($playerFaintedPokemon < 3) {
                        $playerActivePokemon = $this->sendOut($player, $playerTeam[$playerFaintedPokemon + 1]);
                    }
                }

                if ($computerActivePokemon['HP Left'] == 0) {
                    $this->faint($computer['Name'], $computerActivePokemon);

                    $computerFaintedPokemon++;

                    if ($computerFaintedPokemon < 3) {
                        $computerActivePokemon = $this->sendOut($$computer['Name'], $computerTeam[$computerFaintedPokemon + 1]);
                    }
                }
            }

            /**********
             END PHASE
            **********/

            if ($playerFaintedPokemon == 3 || $computerFaintedPokemon == 3) {
                $fightOver = true;
            }
        }

        /*************
         BATTLE END
        *************/

        if (($playerFaintedPokemon == 3 && $computerFaintedPokemon == 3) || $playerFaintedPokemon == 3) {
            sleep(2);
            echo "Your final Pokémon has fainted.\n";
            sleep(2);
            echo "\n" . $computer['Name'] . ":\t " . $computer['Victory'] . "\n";
            sleep(2);
            return false;
        } elseif ($computerFaintedPokemon == 3) {
            sleep(2);
            echo $computer['Type'] . " " . $computer['Name'] . "'s final Pokémon has fainted.\n";
            sleep(2);
            echo "\n" . $computer['Name'] . ":\t " . $computer['Defeat'] . "\n";
            sleep(2);
            return true;
        }
    }

    /**
     * Sends out a pokémon
     * @param string $trainer The trainer's name
     * @param array $pokemon The pokémon to send out
     * @return array The pokémon with stat mods and PP
     */
    public function sendOut($trainer, $pokemon)
    {
        sleep(2);
        echo $trainer . ":\t I choose you, " . $pokemon['Name'] . "!\n";

        return $this->setupPokemon($pokemon);
    }

    /**
     * Sets up a pokémon ready for battle
     * @param array $pokemon The pokémon to setup
     * @return array The pokémon with stat mods and PP
     */
    public function setupPokemon($pokemon)
    {
        // setup stat mods
        $pokemon['Attack Mod'] = 0;
        $pokemon['Defence Mod'] = 0;
        $pokemon['Sp Attack Mod'] = 0;
        $pokemon['Sp Defence Mod'] = 0;
        $pokemon['Speed Mod'] = 0;
        $pokemon['Accuracy Mod'] = 0;
        $pokemon['Evasion Mod'] = 0;

        // setup battle mods
        $pokemon['Current Move'] = 0;
        $pokemon['Move Turns'] = 0;
        $pokemon['Damage Dealt'] = 0;
        $pokemon['Move Category'] = '';
        $pokemon['Status'] = '';
        $pokemon['Confusion'] = -1;
        $pokemon['Flinch'] = 0;
        $pokemon['Critical'] = 0;

        // setup move PP
        $pokemon['PP'][1] = $this->getPP($pokemon['Move 1']);
        $pokemon['PP'][2] = isset($pokemon['Move 2']) ? $this->getPP($pokemon['Move 2']) : null;
        $pokemon['PP'][3] = isset($pokemon['Move 3']) ? $this->getPP($pokemon['Move 3']) : null;
        $pokemon['PP'][4] = isset($pokemon['Move 3']) ? $this->getPP($pokemon['Move 4']) : null;

        $pokemon['HP Left'] = $pokemon['HP'];

        return $pokemon;
    }

    /**
     * Gets the PP of a pokémon's moves
     * @param string $move The move to get PP for
     * @return string The PP of the move, 0 if no move
     */
    public function getPP($move)
    {
        $move = $this->find($move, 'moves');
        $movePP = $move ? $move['PP'] : '0';

        return $movePP;
    }

    /**
     * Handle when a pokémon faints
     * @param string $trainer The name of the trainer
     * @param array $pokemon The fainting pokémon
     */
    public function faint($trainer, $pokemon)
    {
        sleep(2);
        echo "\n" . $trainer . "'s " . $pokemon['Name'] . " fainted.\n";
        sleep(2);
        echo "\n" . $trainer . ":\t " . $pokemon['Name'] . " return!\n\n";
    }
}

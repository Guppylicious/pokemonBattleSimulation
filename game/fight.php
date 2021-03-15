<?php
// --- The fight sequence of a battle

class Fight
{
    public function __construct()
    {
        $this->standbyPhase = new StandbyPhase();
        $this->attackPhase = new AttackPhase();
    }

    public function start($player, $playerTeam, $computer, $computerTeam)
    {
// --- Start Phase
        $playerPokemon = $playerTeam[1];
        $playerFaints = 0;
        $computerPokemon = $computerTeam[1];
        $computerFaints = 0;

        sleep(2);
        echo "\n" . $computer['Type'] . " " . $computer['Name'] . " would like to battle!\n";
        sleep(2);
        echo "\n--- Battle start! ---\n\n";

        $this->standbyPhase->sendOut($computer['Name'], $computerPokemon);
        $this->standbyPhase->sendOut($player, $playerPokemon);

        $fightOver = false;
        while (!$fightOver) {
// --- Standby Phase
            sleep(2);
            $this->standbyPhase->printHP($player, $playerPokemon);
            $this->standbyPhase->printHP($computer['Name'], $computerPokemon);
            echo "\n\n";
            sleep(2);
            $playerAttack = $this->standbyPhase->getAttack($playerPokemon, true);
            $computerAttack = $this->standbyPhase->getAttack($computerPokemon, false);
// --- Attack Phase
            $playerPokemon['Flinch'] = 0;
            $computerPokemon['Flinch'] = 0;
            $playerPokemon['Move Category'] = '';
            $computerPokemon['Move Category'] = '';
            $this->attackPhase->start($player, $playerPokemon, $playerAttack, $playerTeam, $playerFaints, $computer['Name'], $computerPokemon, $computerAttack, $computerTeam, $computerFaints);
// --- End Phase
            if ($playerPokemon['HP Left'] <= 0 && $playerFaints == 3) {
                sleep(2);
                echo "Your final Pokémon has fainted.\n";
                sleep(2);
                echo "\n" . $computer['Name'] . ":\t " . $computer['Victory'] . "\n";
                sleep(2);
                echo "\nYou have lost the battle! Try again!\n\n";
                sleep(2);
                $menu = new Menu();
                $menu->show();
            } elseif ($computerPokemon['HP Left'] <= 0 && $computerFaints == 3) {
                sleep(2);
                echo $computer['Type'] . " " . $computer['Name'] . "'s final Pokémon has fainted.\n";
                sleep(2);
                echo "\n" . $computer['Name'] . ":\t " . $computer['Defeat'] . "\n";
                sleep(2);
                echo "\nCongratulations! You have won the battle!\n\n";
                sleep(2);
                $fightOver = true;
            }
        }
    }
}

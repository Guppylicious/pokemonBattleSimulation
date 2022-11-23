<?php

namespace Game;

class Battle extends Loader
{
    public function __construct()
    {
        $team = new Team();

        $player = readline("- Enter your name: ");
        $playerTeam = $team->createPlayerTeam($player);

        // computer player init
        echo "\n--- Choose an opponent ---\n\n";

        $trainers = $this->getData('trainers');

        foreach ($trainers as $trainer) {
            echo "- " . $trainer['Number'] . ".\t" . $trainer['Name'] . " - " . $trainer['Type'] . "\n";
        }

        echo "\n";

        $computer = null;
        $computerTeam = null;

        while (!$computer && ! $computerTeam) {
            $opponent = readline("- Select the trainer you would like to face: ");

            $computer = $this->find($opponent, 'trainers');

            if (!$computer) {
                echo "Unknown Trainer, choose again.\n";
            } else {
                $computerTeam = $computer['Number'] == 999 ? $team->createRandomComputerTeam() : $team->getComputerTeam($computer);
            }
        }

        $fight = new Fight();

        $fightResult = $fight->start($player, $playerTeam, $computer, $computerTeam);

        if ($fightResult) {
            echo "\nCongratulations! You have won the battle!\n\n";
            sleep(2);
        } else {
            echo "\nYou have lost the battle!\n\n";
            sleep(2);
        }
    }
}

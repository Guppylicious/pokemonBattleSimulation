<?php

class SingleBattle_Controller implements OutputInterface
{
    public function __construct()
    {
        $this->model    = new SingleBattle_Model();
        $this->trainers = $this->model->getTrainers();
    }

    public function show()
    {
        // --- player init
        $player = readline("- Enter your name: ");
        $playerTeam = $this->model->getPlayerTeam($player);
        $computer = '';
        $computerTeam = '';

        // --- computer player init
        echo "\n--- Choose an opponent ---\n\n";
        $i = 1;

        foreach ($this->trainers as $trainer) {
            echo "- " . $trainer['Number'] . ".\t" . $trainer['Name'] . " - " . $trainer['Type'] . "\n";
            $i++;
        }
        echo "\n";

        $this->getOpponent($computer, $computerTeam);

        $fight = new Fight();
        $fight->start($player, $playerTeam, $computer, $computerTeam);

        $menu = new Menu();
        $menu->show();
    }

    private function getOpponent(&$computer, &$computerTeam)
    {
        while (!$computer) {
            $opponent = readline("- Select the trainer you would like to face: ");
            $computer = $this->model->findOpponent($opponent, $this->trainers);
            if (!$computer) {
                echo "Unknown Trainer, choose again.\n";
            } else {
                $computer['Number'] == 999 ? $computerTeam = $this->model->getRandomComputerTeam() : $computerTeam = $this->model->getComputerTeam($computer);
            }
        }
    }
}

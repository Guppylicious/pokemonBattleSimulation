<?php
// --- Single battle is where you can choose the trainer you want to battle.

class SingleBattle
{
    public function __construct()
    {
        $this->loader = new Loader();
        $this->trainers = $this->loader->getData('trainers');
    }

    public function start()
    {
        // --- player init
        $player = readline("- Enter your name: ");
        $playerTeam = $this->loader->createPlayerTeam($player);
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

    public function getOpponent(&$computer, &$computerTeam)
    {
        while (!$computer) {
            $opponent = readline("- Select the trainer you would like to face: ");
            $computer = $this->loader->find($opponent, $this->trainers);
            if (!$computer) {
                echo "Unknown Trainer, choose again.\n";
            } else {
                $computer['Number'] == 999 ? $computerTeam = $this->loader->createRandomComputerTeam() : $computerTeam = $this->loader->getComputerTeam($computer);
            }
        }
    }
}

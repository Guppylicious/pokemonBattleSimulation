<?php
// --- Kanto Challenge puts you up against each Kanto trainer in order until you lose or defeat them all.

class ChallengeMode
{
    public function __construct()
    {
        $this->loader = new Loader();
        $this->trainers = $this->loader->getData('trainers');
    }

    public function show()
    {
        echo "\n--- Challenge Mode ---\n\n";

        // --- player init
        $player = readline("- Enter your name: ");
        $playerTeam = $this->loader->createPlayerTeam($player);
        $this->menu($player, $playerTeam);
    }

    public function menu($player, $playerTeam)
    {
        echo "\n--- Challenges ---\n\n";
        echo "- 1. Kanto\n";
        echo "- 2. Johto\n";
        echo "- 3. Hoenn\n";
        echo "- 4. Sinnoh\n";
        echo "- 5. Unova\n";
        echo "- 6. Kalos\n";
        echo "- 7. Alola\n";
        echo "- 8. All\n\n";

        $challenge = readline("- Enter the number of the challenge you would like to face: ");

        switch ($challenge) {
            case 1:
                $this->start($player, $playerTeam, 'Kanto');
                break;
            case 2:
                $this->start($player, $playerTeam, 'Johto');
                $this->menu($player, $playerTeam);
                break;
            case 3:
                echo "\n*** Under maintenance. Come back later. ***\n\n";
                $this->menu($player, $playerTeam);
                break;
            case 4:
                echo "\n*** Under maintenance. Come back later. ***\n\n";
                $this->menu($player, $playerTeam);
                break;
            case 5:
                echo "\n*** Under maintenance. Come back later. ***\n\n";
                $this->menu($player, $playerTeam);
                break;
            case 6:
                echo "\n*** Under maintenance. Come back later. ***\n\n";
                $this->menu($player, $playerTeam);
                break;
            case 7:
                echo "\n*** Under maintenance. Come back later. ***\n\n";
                $this->menu($player, $playerTeam);
                break;
            case 8:
                $this->start($player, $playerTeam, 'All', true);
                break;
            default:
                echo "Unknown option, try again.\n\n";
                $this->menu($player, $playerTeam);
        }
    }

    public function start($player, $playerTeam, $region, $all = false)
    {
        foreach ($this->trainers as $computer) {
            if ($computer['Region'] == $region) {
                $computerTeam = $this->loader->getComputerTeam($computer);
                $fight = new Fight();
                $fight->start($player, $playerTeam, $computer, $computerTeam);
            } elseif ($computer['Region'] && $all) {
                $computerTeam = $this->loader->getComputerTeam($computer);
                $fight = new Fight();
                $fight->start($player, $playerTeam, $computer, $computerTeam);
            }
        }

        sleep(2);
        echo "\nYou have beaten the " . $region . " Challenge!\n";
        sleep(2);

        $this->loader->addToHall($player, $playerTeam, $region);
        $menu = new Menu();
        $menu->show();
    }
}

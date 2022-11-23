<?php

namespace Game;

class Challenge extends Loader
{
    public function __construct()
    {
        echo "\n--- Challenge Mode ---\n\n";

        $team = new Team();

        $player = readline("- Enter your name: ");
        $playerTeam = $team->createPlayerTeam($player);

        echo "\n--- Challenges ---\n\n";
        echo "- 1. Kanto\n";
        echo "- 2. Johto\n";
        echo "- 3. Hoenn\n";
        echo "- 4. Sinnoh\n";
        echo "- 5. Unova\n";
        echo "- 6. Kalos\n";
        echo "- 7. Alola\n";
        echo "- 8. All\n\n";

        $challenge = false;

        while (!$challenge) {
            $challenge = readline("- Enter the number of the challenge you would like to face: ");

            switch ($challenge) {
                case 1:
                    $this->startChallenge($player, $playerTeam, 'Kanto');
                    break;
                case 2:
                    $this->startChallenge($player, $playerTeam, 'Johto');
                    break;
                case 3:
                    echo "\n*** Under maintenance. Come back later. ***\n\n";
                    break;
                case 4:
                    echo "\n*** Under maintenance. Come back later. ***\n\n";
                    break;
                case 5:
                    echo "\n*** Under maintenance. Come back later. ***\n\n";
                    break;
                case 6:
                    echo "\n*** Under maintenance. Come back later. ***\n\n";
                    break;
                case 7:
                    echo "\n*** Under maintenance. Come back later. ***\n\n";
                    break;
                case 8:
                    $this->startChallenge($player, $playerTeam, 'All', true);
                    break;
                default:
                    echo "Unknown option, try again.\n\n";
                    $challenge = false;
                    break;
            }
        }
    }

    /**
     * Starts a challenge which loops through and fights all trainers of a given region
     * @param string $player The players name
     * @param array $playerTeam The player's team
     * @param string $region The region the player completed
     * @param boolean $all If true, loop throigh every region's trainers
     */
    public function startChallenge($player, $playerTeam, $region, $all = false)
    {
        $trainers = $this->getData('trainers');

        $team = new Team();

        foreach ($trainers as $computer) {
            if ($computer['Region'] == $region) {
                $computerTeam = $team->getComputerTeam($computer);

                $fight = new Fight();

                $fightResult = $fight->start($player, $playerTeam, $computer, $computerTeam);
            } elseif ($computer['Region'] && $all) {
                $computerTeam = $team->getComputerTeam($computer);

                $fight = new Fight();

                $fightResult = $fight->start($player, $playerTeam, $computer, $computerTeam);
            }

            if (!$fightResult) {
                echo "\nYou have lost the battle!\n\n";
                sleep(2);
                echo "\nChallenge failed!\n\n";
                sleep(2);

                exit;
            }
        }

        sleep(2);
        echo "\nYou have beaten the " . $region . " Challenge!\n";
        sleep(2);

        $hallOfFame = new HallOfFame();

        $hallOfFame->addToHall($player, $playerTeam, $region);
    }
}

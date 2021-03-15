<?php
// --- The main menu screen for the game.

class Menu
{
    public function show()
    {
        echo "--- Main menu ---\n\n";
        echo "- 1. Battle a trainer\n";
        echo "- 2. Take on a challenge\n";
        echo "- 3. View the Hall of Fame\n";
        echo "- 4. View the Pokédex\n";
        echo "- 5. Learn type advantages\n";
        echo "- 6. Exit\n\n";
        $menuOption = readline("- Enter the number of what you would like to do: ");

        switch ($menuOption) {
            case 1:
                $battle = new SingleBattle();
                $battle->start();
                break;
            case 2:
                $challenge = new ChallengeMode();
                $challenge->show();
                break;
            case 3:
                $hall = new HallOfFame();
                $hall->show();
                break;
            case 4:
                $pokedex = new Pokedex();
                $pokedex->show();
                break;
            case 5:
                $types = new Types();
                $types->show();
                break;
            case 6:
                exit;
            default:
                echo "Unknown option, try again.\n\n";
                $this->show();
        }
    }
}

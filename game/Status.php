<?php

namespace Game;

class Status extends Loader
{
    /**
     * Get the color mask of a status condition
     * @param string $status The status condition
     * @return string The color mask
     */
    public function statusColor($status)
    {
        switch ($status) {
            case 'PAR':
                $color = "\e[38;5;226m";
                break;
            case 'BRN':
                $color = "\e[38;5;202m";
                break;
            case 'FRZ':
                $color = "\e[38;5;51m";
                break;
            case 'PSN':
                $color = "\e[38;5;93m";
                break;
            case 'SLP':
                $color = "\e[38;5;15m";
                break;
        }

        return $color;
    }

    /**
     * Handle a pokémon's status condition
     * @param array $pokemon The pokémon
     * @return array The pokémon after any conditions have taken effect
     */
    public function statusCheck($pokemon)
    {
        switch ($pokemon['Status']) {
            case 'PAR':
                $pokemon = $this->paralyze($pokemon);
                break;
            case 'FRZ':
                $pokemon = $this->freeze($pokemon);
                break;
            case 'SLP':
                $pokemon = $this->sleep($pokemon);
                break;
        }

        return $pokemon;
    }

    /**
     * Handle a pokémon's status condition if it damages
     * @param array $pokemon The pokémon
     * @return array The pokémon after any conditions have taken effect
     */
    public function statusDamage($pokemon)
    {
        switch ($pokemon['Status']) {
            case 'BRN':
                $pokemon = $this->burn($pokemon);
            case 'PSN':
                $pokemon = $this->poison($pokemon);
        }

        return $pokemon;
    }

    /**
     * Apply a status effect
     * @param array $pokemon The pokémon to get the status effect
     * @param string $status The status effect to apply
     * @param string $move Some status moves ignore types so can apply manually
     * @return array The pokémon after any conditions have taken effect
     */
    public function statusCalc($pokemon, $status, $move)
    {
        sleep(2);
        if (!$pokemon['Status']) {
            switch ($status) {
                case 'PAR':
                    if (($pokemon['Type 1'] == 'Electric' || $pokemon ['Type 2'] == 'Electric') || (($pokemon['Type 1'] == 'Ground' || $pokemon ['Type 2'] == 'Ground') && $move == 'Thunder Wave')) {
                        echo "\n It has no effect!\n";
                    } else {
                        $pokemon['Status'] = 'PAR';
                        $pokemon['Speed'] = $pokemon['Speed'] / 2;
                        echo "\n " . $pokemon['Name'] . " became " . $this->statusColor($pokemon['Status']) . "paralyzed\e[0m!\n";
                    }
                    break;
                case 'BRN':
                    if ($pokemon['Type 1'] == 'Fire' || $pokemon ['Type 2'] == 'Fire') {
                        echo "\n It has no effect!\n";
                    } else {
                        $pokemon['Status'] = 'BRN';
                        echo "\n " . $pokemon['Name'] . " was " . $this->statusColor($pokemon['Status']) . "burned\e[0m!\n";
                    }
                    break;
                case 'FRZ':
                    if ($pokemon['Type 1'] == 'Ice' || $pokemon ['Type 2'] == 'Ice') {
                        echo "\n It has no effect!\n";
                    } else {
                        $pokemon['Status'] = 'FRZ';
                        echo "\n " . $pokemon['Name'] . " was " . $this->statusColor($pokemon['Status']) . "frozen\e[0m solid!\n";
                    }
                    break;
                case 'PSN':
                    if ($pokemon['Type 1'] == 'Poison' || $pokemon ['Type 2'] == 'Poison' || $pokemon['Type 1'] == 'Steel' || $pokemon ['Type 2'] == 'Steel') {
                        echo "\n It has no effect!\n";
                    } else {
                        $pokemon['Status'] = 'PSN';
                        echo "\n " . $pokemon['Name'] . " was " . $this->statusColor($pokemon['Status']) . "poisoned\e[0m!\n";
                    }
                    break;
                case 'SLP':
                    $pokemon['Status'] = 'SLP';
                    echo "\n " . $pokemon['Name'] . " fell " . $this->statusColor($pokemon['Status']) . "asleep\e[0m!\n";
                    $pokemon['Sleep'] = rand(1, 3);
                    break;
                case 'CON':
                    if ($pokemon['Confusion'] == -1) {
                        $pokemon['Confusion'] = rand(1, 4);
                        echo "\n " . $pokemon['Name'] . " became confused!\n";
                    } else {
                        echo "\n " . $pokemon['Name'] . " is already confused!\n";
                    }
                    break;
            }
        } else {
            echo "\n It has no effect!\n";
        }

        return $pokemon;
    }

    /**
     * Apply a status effect if inflicted from an attack
     * @param array $pokemon The pokémon to get the status effect
     * @param string $status The status effect to apply
     * @param string $move Some status moves ignore types so can apply manually
     * @return array The pokémon after any conditions have taken effect
     */
    public function statusAttack($pokemon, $status)
    {
        sleep(2);
        if (!$pokemon['Status']) {
            switch ($status) {
                case 'PAR':
                    if ($pokemon['Type 1'] == 'Electric' || $pokemon ['Type 2'] == 'Electric') {
                        // do nothing
                    } else {
                        $pokemon['Status'] = 'PAR';
                        $pokemon['Speed'] = $pokemon['Speed'] / 2;
                        echo "\n " . $pokemon['Name'] . " became " . $this->statusColor($pokemon['Status']) . "paralyzed\e[0m!\n";
                    }
                    break;
                case 'BRN':
                    if ($pokemon['Type 1'] == 'Fire' || $pokemon ['Type 2'] == 'Fire') {
                        // do nothing
                    } else {
                        $pokemon['Status'] = 'BRN';
                        echo "\n " . $pokemon['Name'] . " was " . $this->statusColor($pokemon['Status']) . "burned\e[0m!\n";
                    }
                    break;
                case 'FRZ':
                    if ($pokemon['Type 1'] == 'Ice' || $pokemon ['Type 2'] == 'Ice') {
                        // do nothing
                    } else {
                        $pokemon['Status'] = 'FRZ';
                        echo "\n " . $pokemon['Name'] . " was " . $this->statusColor($pokemon['Status']) . "frozen\e[0m solid!\n";
                    }
                    break;
                case 'PSN':
                    if ($pokemon['Type 1'] == 'Poison' || $pokemon ['Type 2'] == 'Poison' || $pokemon['Type 1'] == 'Steel' || $pokemon ['Type 2'] == 'Steel') {
                        // do nothing
                    } else {
                        $pokemon['Status'] = 'PSN';
                        echo "\n " . $pokemon['Name'] . " was " . $this->statusColor($pokemon['Status']) . "poisoned\e[0m!\n";
                    }
                    break;
                case 'SLP':
                    $pokemon['Status'] = 'SLP';
                    echo "\n " . $pokemon['Name'] . " fell " . $this->statusColor($pokemon['Status']) . "asleep\e[0m!\n";
                    $pokemon['Sleep'] = rand(1, 3);
                    break;
                case 'CON':
                    if ($pokemon['Confusion'] == -1) {
                        $pokemon['Confusion'] = rand(1, 4);
                        echo "\n " . $pokemon['Name'] . " became confused!\n";
                    } else {
                        echo "\n " . $pokemon['Name'] . " is already confused!\n";
                    }
                    break;
            }
        } else {
            echo "\n It has no effect!\n";
        }
    }

    /**
     * Handle a paralysis status condition
     * @param array $pokemon The pokémon
     * @return array The pokémon after any paralysis has taken effect
     */
    public function paralyze($pokemon)
    {
        $parHit = rand(0, 100);

        if ($parHit <= 25) {
            sleep(2);
            echo "\n " . $pokemon['Name'] . " is " . $this->statusColor($pokemon['Status']) . "paralyzed\e[0m! It can't move!\n";

            $pokemon['Move Turns'] = 0;
        }

        return $pokemon;
    }

    /**
     * Handle a burnt status condition
     * @param array $pokemon The pokémon
     * @return array The pokémon after any burn has taken effect
     */
    public function burn($pokemon)
    {
        sleep(2);
        echo "\n " . $pokemon['Name'] . " is hurt by it's " . $this->statusColor($pokemon['Status']) . "burn\e[0m!\n";

        $damage = round($pokemon['HP'] / 16);
        $pokemon['HP Left'] = $pokemon['HP Left'] - $damage;

        echo "It deals " . $damage . " damage.\n";

        return $pokemon;
    }

    /**
     * Handle a frozen status condition
     * @param array $pokemon The pokémon
     * @return array The pokémon after any freeze has taken effect
     */
    public function freeze($pokemon)
    {
        $frzHit = rand(0, 100);

        if ($frzHit >= 20) {
            sleep(2);
            echo "\n " . $pokemon['Name'] . " is " . $this->statusColor($pokemon['Status']) . "frozen\e[0m solid!\n";

            $pokemon['Move Turns'] = 0;
        } else {
            echo "\n" . $pokemon['Name'] . " thawed out!\n";

            $pokemon['Status'] = '';
        }

        return $pokemon;
    }

    /**
     * Handle a poisoned status condition
     * @param array $pokemon The pokémon
     * @return array The pokémon after any poison has taken effect
     */
    public function poison($pokemon)
    {
        sleep(2);
        echo "\n " . $pokemon['Name'] . " is hurt by " . $this->statusColor($pokemon['Status']) . "poison\e[0m!\n";

        $damage = round($pokemon['HP'] / 8);

        $pokemon['HP Left'] = $pokemon['HP Left'] - $damage;

        echo "It deals " . $damage . " damage.\n";

        return $pokemon;
    }

    /**
     * Handle a sleeping status condition
     * @param array $pokemon The pokémon
     * @return array The pokémon after any sleep has taken effect
     */
    public function sleep($pokemon)
    {
        if ($pokemon['Sleep'] > 0) {
            sleep(2);
            echo "\n " . $pokemon['Name'] . " is fast " . $this->statusColor($pokemon['Status']) . "asleep\e[0m!\n";

            $pokemon['Move Turns'] = 0;
            $pokemon['Sleep']--;
        } else {
            echo "\n" . $pokemon['Name'] . " woke up!\n";

            $pokemon['Status'] = '';
        }

        return $pokemon;
    }

    /**
     * Handle a confused status condition
     * @param array $pokemon The pokémon
     * @return array The pokémon after any confusion has taken effect
     */
    public function confusion($pokemon)
    {
        if ($pokemon['Confusion'] > 0) {
            sleep(2);
            echo "\n " . $pokemon['Name'] . " is confused!\n";

            $pokemon['Confusion']--;

            $confuseHit = rand(0, 100);

            if ($confuseHit <= 33) {
                sleep(2);
                echo "\nIt hurt itself in its confusion!\n";

                $pokemon['Move Turns'] = 0;

                $stats = new Stats();

                $damage = round((((((2 * 50) / 5) + 2) * (40 * ($stats->getStat($pokemon, 'Attack') / $stats->getStat($pokemon, 'Defence'))) / 50) + 2));

                $pokemon['HP Left'] = $pokemon['HP Left'] - $damage;

                echo "It deals " . $damage . " damage.\n";
            }
        } elseif ($pokemon['Confusion'] == 0) {
            sleep(2);
            echo "\n " . $pokemon['Name'] . " snapped out of its confusion!\n";

            $pokemon['Confusion'] = -1;
        }

        return $pokemon;
    }

    /**
     * Check if a pokémon will be made to flinch
     * @param array $pokemon The pokémon
     * @return array The pokémon after any flinch has been applied
     */
    public function flinch($pokemon, $flinch)
    {
        $flinchHit = rand(0, 100);

        if ($flinchHit <= $flinch) {
            $pokemon['Flinch'] = 1;
        }

        return $pokemon;
    }

    /**
     * Check if a pokémon will flinch
     * @param array $pokemon The pokémon
     * @return array The pokémon after any flinching has taken effect
     */
    public function checkFlinch($pokemon)
    {
        if ($pokemon['Flinch'] == 1) {
            sleep(2);
            echo "\n " . $pokemon['Name'] . " flinched and couldn't move!\n";

            $pokemon['Move Turns'] = 0;
            $pokemon['Flinch'] = 0;
        }

        return $pokemon;
    }
}

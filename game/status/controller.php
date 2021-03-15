<?php

// --- Controller for status conditinos

class Status_Controller
{
    public function __construct()
    {
        $this->model = new Status_Model(new Stat());
    }

    // status effect move calc
    public function statusCalc(&$pokemon, $status, $move)
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
                        echo "\n " . $pokemon['Name'] . " became " . $this->model->getColor($pokemon['Status']) . "paralyzed\e[0m!\n";
                    }
                    break;
                case 'BRN':
                    if ($pokemon['Type 1'] == 'Fire' || $pokemon ['Type 2'] == 'Fire') {
                        echo "\n It has no effect!\n";
                    } else {
                        $pokemon['Status'] = 'BRN';
                        echo "\n " . $pokemon['Name'] . " was " . $this->model->getColor($pokemon['Status']) . "burned\e[0m!\n";
                    }
                    break;
                case 'FRZ':
                    if ($pokemon['Type 1'] == 'Ice' || $pokemon ['Type 2'] == 'Ice') {
                        echo "\n It has no effect!\n";
                    } else {
                        $pokemon['Status'] = 'FRZ';
                        echo "\n " . $pokemon['Name'] . " was " . $this->model->getColor($pokemon['Status']) . "frozen\e[0m solid!\n";
                    }
                    break;
                case 'PSN':
                    if ($pokemon['Type 1'] == 'Poison' || $pokemon ['Type 2'] == 'Poison' || $pokemon['Type 1'] == 'Steel' || $pokemon ['Type 2'] == 'Steel') {
                        echo "\n It has no effect!\n";
                    } else {
                        $pokemon['Status'] = 'PSN';
                        echo "\n " . $pokemon['Name'] . " was " . $this->model->getColor($pokemon['Status']) . "poisoned\e[0m!\n";
                    }
                    break;
                case 'SLP':
                    $pokemon['Status'] = 'SLP';
                    echo "\n " . $pokemon['Name'] . " fell " . $this->model->getColor($pokemon['Status']) . "asleep\e[0m!\n";
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

    // status effect calc if applied through an attack, main difference is we don't want to print if typing can't be affected
    public function statusAttack(&$pokemon, $status)
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
                        echo "\n " . $pokemon['Name'] . " became " . $this->model->getColor($pokemon['Status']) . "paralyzed\e[0m!\n";
                    }
                    break;
                case 'BRN':
                    if ($pokemon['Type 1'] == 'Fire' || $pokemon ['Type 2'] == 'Fire') {
                        // do nothing
                    } else {
                        $pokemon['Status'] = 'BRN';
                        echo "\n " . $pokemon['Name'] . " was " . $this->model->getColor($pokemon['Status']) . "burned\e[0m!\n";
                    }
                    break;
                case 'FRZ':
                    if ($pokemon['Type 1'] == 'Ice' || $pokemon ['Type 2'] == 'Ice') {
                        // do nothing
                    } else {
                        $pokemon['Status'] = 'FRZ';
                        echo "\n " . $pokemon['Name'] . " was " . $this->model->getColor($pokemon['Status']) . "frozen\e[0m solid!\n";
                    }
                    break;
                case 'PSN':
                    if ($pokemon['Type 1'] == 'Poison' || $pokemon ['Type 2'] == 'Poison' || $pokemon['Type 1'] == 'Steel' || $pokemon ['Type 2'] == 'Steel') {
                        // do nothing
                    } else {
                        $pokemon['Status'] = 'PSN';
                        echo "\n " . $pokemon['Name'] . " was " . $this->model->getColor($pokemon['Status']) . "poisoned\e[0m!\n";
                    }
                    break;
                case 'SLP':
                    $pokemon['Status'] = 'SLP';
                    echo "\n " . $pokemon['Name'] . " fell " . $this->model->getColor($pokemon['Status']) . "asleep\e[0m!\n";
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

    // --- check if status takes affect before attacking
    public function statusCheck(&$pokemon)
    {
        switch ($pokemon['Status']) {
            case 'PAR':
                return $this->paralyze($pokemon);
            case 'FRZ':
                return $this->freeze($pokemon);
            case 'SLP':
                return $this->sleep($pokemon);
            default:
                return false;
        }
    }

    // --- check if status will deal damage
    public function statusDamage(&$pokemon)
    {
        switch ($pokemon['Status']) {
            case 'BRN':
                return $this->burn($pokemon);
            case 'PSN':
                return $this->poison($pokemon);
            default:
                return false;
        }
    }

    // --- check if paralyze takes effect before attacking
    public function paralyze(&$pokemon)
    {
        $parHit = rand(0, 100);
        if ($parHit <= 25) {
            sleep(2);
            echo "\n " . $pokemon['Name'] . " is " . $this->model->getColor($pokemon['Status']) . "paralyzed\e[0m! It can't move!\n";
            $pokemon['Move Turns'] = 0;
            return true;
        } else {
            return false;
        }
    }

    // --- apply burn damage
    public function burn(&$pokemon)
    {
        sleep(2);
        echo "\n " . $pokemon['Name'] . " is hurt by it's " . $this->model->getColor($pokemon['Status']) . "burn\e[0m!\n";
        $damage = round($pokemon['HP'] / 16);
        $pokemon['HP Left'] = $pokemon['HP Left'] - $damage;
        echo "It deals " . $damage . " damage.\n";
    }

    // --- check if freeze thaws before attacking
    public function freeze(&$pokemon)
    {
        $frzHit = rand(0, 100);
        if ($frzHit >= 20) {
            sleep(2);
            echo "\n " . $pokemon['Name'] . " is " . $this->model->getColor($pokemon['Status']) . "frozen\e[0m solid!\n";
            $pokemon['Move Turns'] = 0;
            return true;
        } else {
            echo "\n" . $pokemon['Name'] . " thawed out!\n";
            $pokemon['Status'] = '';
            return false;
        }
    }

    // --- apply poison damage
    public function poison(&$pokemon)
    {
        sleep(2);
        echo "\n " . $pokemon['Name'] . " is hurt by " . $this->model->getColor($pokemon['Status']) . "poison\e[0m!\n";
        $damage = round($pokemon['HP'] / 8);
        $pokemon['HP Left'] = $pokemon['HP Left'] - $damage;
        echo "It deals " . $damage . " damage.\n";
    }

    // --- check if awoken before attacking
    public function sleep(&$pokemon)
    {
        if ($pokemon['Sleep'] > 0) {
            sleep(2);
            echo "\n " . $pokemon['Name'] . " is fast " . $this->model->getColor($pokemon['Status']) . "asleep\e[0m!\n";
            $pokemon['Move Turns'] = 0;
            $pokemon['Sleep']--;
            return true;
        } else {
            echo "\n" . $pokemon['Name'] . " woke up!\n";
            $pokemon['Status'] = '';
            return false;
        }
    }

    // --- check if confusion takes affect before attacking
    public function confusion(&$pokemon)
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
                $damage = round((((((2 * 50) / 5) + 2) * (40 * ($this->model->getStat($pokemon, 'Attack') / $this->model->getStat($pokemon, 'Defence'))) / 50) + 2));
                $pokemon['HP Left'] = $pokemon['HP Left'] - $damage;
                echo "It deals " . $damage . " damage.\n";
                return true;
            } else {
                return false;
            }
        } elseif ($pokemon['Confusion'] == 0) {
            sleep(2);
            echo "\n " . $pokemon['Name'] . " snapped out of its confusion!\n";
            $pokemon['Confusion'] = -1;
            return false;
        } else {
            return false;
        }
    }

    // --- check if a pokemon will flinch
    public function checkFlinch(&$pokemon)
    {
        if ($pokemon['Flinch'] == 1) {
            sleep(2);
            echo "\n " . $pokemon['Name'] . " flinched and couldn't move!\n";
            $pokemon['Move Turns'] = 0;
            $pokemon['Flinch'] = 0;
            return true;
        } else {
            return false;
        }
    }

    // --- check if an attack will make an enemy flinch
    public function flinch(&$pokemon, $flinch)
    {
        $flinchHit = rand(0, 100);
        if ($flinchHit <= $flinch) {
            $pokemon['Flinch'] = 1;
        }
    }
}

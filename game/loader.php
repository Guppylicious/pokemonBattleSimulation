<?php

// --- Get the data from csv files when required.

require 'vendor/autoload.php';
use League\Csv\Writer;
use League\Csv\Reader;
use League\Csv\Statment;

header('Content-Type: text/csv; charset=ISO-8859-1');

class Loader
{
    public function __construct()
    {
        $this->moves = $this->getData('moves');
        $this->monsters = $this->getData('monsters');
        $this->playerTeam = array(1 => "", 2 => "", 3 => "");
        $this->computerTeam = array(1 => "", 2 => "", 3 => "");
    }

    // --- get data
    public function getData($get)
    {
        $file = Reader::createFromPath('csv/' . $get . '.csv', 'r');
        $file->addStreamFilter('convert.iconv.ISO-8859-1/UTF-8//TRANSLIT');
        $fileKeys = $file->fetchOne();
        $data = $file->setHeaderOffset(0)->getRecords($fileKeys);
        return $data;
    }

    // --- create team for player
    public function createPlayerTeam($player)
    {
        for ($i = 1; $i <= 3;) {
            switch ($i) {
                case 1:
                    $ordinal = "1st";
                    break;
                case 2:
                    $ordinal = "2nd";
                    break;
                case 3:
                    $ordinal = "3rd";
                    break;
                default:
                    $ordinal = "error";
                    break;
            }
            while (!$this->playerTeam[$i]) {
                $pokemon = readline("- Select your " . $ordinal . " Pokémon or enter '?' to get one at random: ");

                if ($pokemon == '?') {
                    $pokemon = rand(1, $this->counter('monsters'));
                }

                $this->playerTeam[$i] = $this->find($pokemon, $this->monsters);

                if (!$this->playerTeam[$i]) {
                    echo "Unknown Pokémon, choose again.\n";
                }
            }
            echo ucfirst($this->playerTeam[$i]['Name']) . " set as your " . $ordinal . " Pokémon.\n";
            $i++;
        }

        return $this->playerTeam;
    }

    // --- create computer team
    public function createComputerTeam($computer)
    {
        for ($i = 1; $i <= 3; $i++) {
            $pokemon = $computer['Pokemon ' . $i];
            $this->computerTeam[$i] = $this->find($pokemon, $this->monsters);
        }

        return $this->computerTeam;
    }

    // --- create random team for computer
    public function createRandomComputerTeam()
    {
        for ($i = 1; $i <= 3; $i++) {
            $pokemon = rand(1, $this->counter('monsters'));
            $this->computerTeam[$i] = $this->find($pokemon, $this->monsters);
        }

        return $this->computerTeam;
    }

    // --- find an object from an array
    public function find($needle, $haystack)
    {
        $needle = ucfirst($needle);

        foreach ($haystack as $hay) {
            if ($hay['Name'] == $needle || $hay['Number'] == $needle) {
                return $hay;
            }
        }
    }

    // --- find an object from an array by its name alone
    public function findByName($needle, $haystack)
    {
        $needle = ucfirst($needle);

        foreach ($haystack as $hay) {
            if ($hay['Name'] == $needle) {
                return $hay;
            }
        }
    }

    // --- count the number of stored rows in a csv file
    public function counter($count)
    {
        $file = Reader::createFromPath('csv/' . $count . '.csv', 'r');
        $file->setHeaderOffset(0);
        return count($file);
    }

    // --- find the type effect power of an attack
    public function findTypeMatch($types, $attacker, $defender)
    {
        foreach ($types as $type) {
            if ($type['Type'] == $attacker) {
                return $type[$defender];
            }
        }
    }

    // --- get the type of a move and return a color for a printf mask
    public function getMoveType($move)
    {
        $m = $this->findByName($move, $this->moves);
        $type = $m['Type'];

        $color = $this->typeColor($type);

        return $color;
    }

    // --- select the color of a printf mask for a type
    public function typeColor($type)
    {
        switch ($type) {
            case 'Normal':
                $color = "\e[38;5;15m";
                break;
            case 'Fire':
                $color = "\e[38;5;202m";
                break;
            case 'Water':
                $color = "\e[38;5;26m";
                break;
            case 'Grass':
                $color = "\e[38;5;28m";
                break;
            case 'Electric':
                $color = "\e[38;5;226m";
                break;
            case 'Ice':
                $color = "\e[38;5;51m";
                break;
            case 'Fighting':
                $color = "\e[38;5;88m";
                break;
            case 'Poison':
                $color = "\e[38;5;93m";
                break;
            case 'Ground':
                $color = "\e[38;5;179m";
                break;
            case 'Flying':
                $color = "\e[38;5;87m";
                break;
            case 'Psychic':
                $color = "\e[38;5;200m";
                break;
            case 'Bug':
                $color = "\e[38;5;100m";
                break;
            case 'Rock':
                $color = "\e[38;5;94m";
                break;
            case 'Ghost':
                $color = "\e[38;5;54m";
                break;
            case 'Dragon':
                $color = "\e[38;5;220m";
                break;
            case 'Dark':
                $color = "\e[38;5;236m";
                break;
            case 'Steel':
                $color = "\e[38;5;240m";
                break;
            case 'Fairy':
                $color = "\e[38;5;13m";
                break;
            default:
                $color = "\e[38;5;15m";
        }
        return $color;
    }

    // --- select the color of a status
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

    // --- enter a player to the hall of fame
    public function addToHall($player, $team, $region)
    {
        echo "\nYou are a true Pokémon Master!\n";
        sleep(2);
        echo "\n " . $player . "\n";
        sleep(2);
        echo "\n " . $team[1]['Name'] . "\n";
        sleep(2);
        echo "\n " . $team[2]['Name'] . "\n";
        sleep(2);
        echo "\n " . $team[3]['Name'] . "\n";
        sleep(2);
        echo "\nWelcome to the Hall of Fame!\n\n";
        sleep(2);

        $hall = fopen('csv/hall.csv', 'a');
        fputcsv($hall, [$player, $team[1]['Name'], $team[2]['Name'], $team[3]['Name'], $region, date('H:i:s d/m/Y')]);
    }
}

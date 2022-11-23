<?php

namespace Game;

class Types extends Loader
{
    protected $types;

    public function __construct()
    {
        $this->types = $this->getData('types');
    }

    /**
     * Get the modifier of an attack type
     * @param string $attackerType The type of the attacker
     * @param string $defenderType The type of the defender
     */
    public function findTypeMatch($attackerType, $defenderType)
    {
        foreach ($this->types as $type) {
            if ($type['Type'] == $attackerType) {
                return $type[$defenderType];
            }
        }
    }

    /**
     * Prints the table of type advantages
     */
    public function showTable() {
        echo "\n--- Type advantages ---\n\n";
        echo " ↓ Attack | Defence →\n";

        foreach ($this->types as $type) {
            foreach ($type as $key => $value) {
                $typeMask = $this->typeColor($key) . "%9s\e[0m | ";
                printf($typeMask, $key);
            }
            echo "\n";
            break;
        }

        foreach ($this->types as $type) {
            foreach ($type as $t) {
                switch ($t) {
                    case '0':
                        $typeMask = "\e[38;5;196m%9s\e[0m | ";
                        $effect = "No effect";
                        break;
                    case '0.5':
                        $typeMask = "\e[38;5;166m%9s\e[0m | ";
                        $effect = "Half";
                        break;
                    case '1':
                        $typeMask = "\e[38;5;15m%9s\e[0m | ";
                        $effect = "Base";
                        break;
                    case '2':
                        $typeMask = "\e[38;5;46m%9s\e[0m | ";
                        $effect = "Double";
                        break;
                    default:
                        $typeMask = $this->typeColor($t) . "%9s\e[0m | ";
                        $effect = $t;
                }
                printf($typeMask, $effect);
            }
            echo "\n";
        }

        echo "\n\n";
    }

    /**
     * Gets the colour of the type to print
     * @param string $type The type to get the colour for
     * @return string The color mask of the type
     */
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
}

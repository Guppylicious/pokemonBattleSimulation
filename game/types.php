<?php
// --- print the type advantages

class Types
{
    public function __construct()
    {
        $this->loader = new Loader();
        $this->types = $this->loader->getData('types');
    }

    public function show()
    {
        echo "\n--- Type advantages ---\n\n";
        echo " ↓ Attack | Defence →\n";

        foreach ($this->types as $type) {
            foreach ($type as $key => $value) {
                $typeMask = $this->loader->typeColor($key) . "%9s\e[0m | ";
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
                        $typeMask = $this->loader->typeColor($t) . "%9s\e[0m | ";
                        $effect = $t;
                }
                printf($typeMask, $effect);
            }
            echo "\n";
        }

        echo "\n\n";

        $menu = new Menu();
        $menu->show();
    }
}

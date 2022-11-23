<?php

namespace Game;

class Moves extends Loader
{
    /**
     * Gets the type of the move and returns it has a color mask
     * @param string $move The name of the move
     * @return string The color mask of the type
     */
    public function getMoveType($move)
    {
        $types = new Types();

        $m = $this->find($move, 'moves');
        $type = $m['Type'];

        $color = $types->typeColor($type);

        return $color;
    }
}

<?php

class SingleBattle_Model extends Model
{
    public function findOpponent($opponent, $trainers)
    {
        return $this->loader->find($opponent, $trainers);
    }

    public function getPlayerTeam($player)
    {
        return $this->loader->createPlayerTeam($player);
    }

    public function getComputerTeam($computer)
    {
        return $this->loader->createComputerTeam($computer);
    }

    public function getRandomComputerTeam($computer)
    {
        return $this->loader->createRandomComputerTeam($computer);
    }
}

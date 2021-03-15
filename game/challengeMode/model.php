<?php

class ChallengeMode_Model extends Model
{
    public function getPlayerTeam($player)
    {
        return $this->loader->createPlayerTeam($player);
    }

    public function getComputerTeam($computer)
    {
        return $this->loader->createComputerTeam($computer);
    }
}

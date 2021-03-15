<?php

// --- Model for status conditions

class Status_Model extends Model
{
    public function __construct(Stat $stat)
    {
        parent::__construct();
        $this->stat = $stat;
    }

    public function getStat($pokemon, $stat)
    {
        return $this->stat->getStat($pokemon, $stat);
    }
}

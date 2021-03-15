<?php

class AttackPhase_Controller
{
    public function __construct()
    {
        $this->model = new AttackPhase_Model(new Stat(), new Status_Controller());
        $this->moves = $this->model->getMoves();
        $this->types = $this->model->getTypes();
    }

    public function start($player, &$playerPokemon, $playerAttack, $playerTeam, &$playerFaints, $computer, &$computerPokemon, $computerAttack, $computerTeam, &$computerFaints)
    {
        if ($playerAttack['Priority'] >= $computerAttack['Priority']) {
            $this->model->startAttacks($player, $playerPokemon, $playerAttack, $playerTeam, $playerFaints, $computer, $computerPokemon, $computerAttack, $computerTeam, $computerFaints);
        } elseif ($playerAttack['Priority'] < $computerAttack['Priority']) {
            $this->model->startAttacks($computer, $computerPokemon, $computerAttack, $computerTeam, $computerFaints, $player, $playerPokemon, $playerAttack, $playerTeam, $playerFaints);
        } else {
            if ($this->model->getSpeed($playerPokemon) >= $this->model->getSpeed($computerPokemon)) {
                $this->model->startAttacks($player, $playerPokemon, $playerAttack, $playerTeam, $playerFaints, $computer, $computerPokemon, $computerAttack, $computerTeam, $computerFaints);
            } else {
                $this->model->startAttacks($computer, $computerPokemon, $computerAttack, $computerTeam, $computerFaints, $player, $playerPokemon, $playerAttack, $playerTeam, $playerFaints);
            }
        }

        if ($computerPokemon['Status']) {
            $this->model->getStatusDamage($computerPokemon);
            if ($computerPokemon['HP Left'] <= 0) {
                $this->model->faint($computerPokemon, $computerTeam, $computer, $computerFaints);
            }
        }
        if ($playerPokemon['Status']) {
            $this->model->getStatusDamage($playerPokemon);
            if ($playerPokemon['HP Left'] <= 0) {
                $this->model->faint($playerPokemon, $playerTeam, $player, $playerFaints);
            }
        }
    }
}

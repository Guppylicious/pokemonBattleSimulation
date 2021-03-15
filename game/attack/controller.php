<?php

class Attack_Controller
{
    public function __construct()
    {
        $this->specialAttacks = new SpecialAttacks();
        $this->damage = new Damage();
        $this->status = new Status_Controller();
        $this->stat = new Stat();
    }

    public function start(&$attackerPokemon, &$attackerMove, &$defenderPokemon)
    {
        if ($attackerMove['Category'] == 'Status') {
            $attackerPokemon['Damage Dealt'] = 0;
            if ($attackerMove['Other'] == 1) {
                $this->specialAttacks->specialAttack($attackerPokemon, $attackerMove, $defenderPokemon);
            } elseif ($attackerMove['Target'] == 'User') {
                $this->stat->statCalc($attackerPokemon, $attackerMove['Stat'], $attackerMove['Amount']);
            } elseif ($attackerMove['Target'] == 'Foe') {
                $this->stat->statCalc($defenderPokemon, $attackerMove['Stat'], $attackerMove['Amount']);
            } else {
                $this->status->statusCalc($defenderPokemon, $attackerMove['Status'], $attackerMove['Name']);
            }
        } else {
            if ($attackerMove['Other'] == 1) {
                $this->specialAttacks->specialAttack($attackerPokemon, $attackerMove, $defenderPokemon);
            } else {
                $this->damage->damageCalc($attackerPokemon, $attackerMove, $defenderPokemon);
                $this->damage->postDamage($attackerPokemon, $attackerMove, $defenderPokemon);
            }
        }
        $attackerPokemon['Move Turns']--;
    }
}

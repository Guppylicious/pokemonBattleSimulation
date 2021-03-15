<?php
// --- The attack itself

class Attack
{
    public function __construct()
    {
        $this->loader = new Loader();
        $this->specialAttacks = new SpecialAttacks();
        $this->damage = new Damage();
        $this->status = new Status();
        $this->stat = new Stat();
    }

    public function start(&$attackerPokemon, &$attackerMove, &$defenderPokemon)
    {
        if ($attackerMove['Category'] == 'Status') {
            $attackerPokemon['Damage Dealt'] = 0;
            if ($attackerMove['Other'] == 1) {
                $this->specialAttacks->statusOther($attackerPokemon, $attackerMove, $defenderPokemon);
            } elseif ($attackerMove['Target'] == 'User') {
                $this->stat->statCalc($attackerPokemon, $attackerMove['Stat'], $attackerMove['Amount']);
            } elseif ($attackerMove['Target'] == 'Foe') {
                $this->stat->statCalc($defenderPokemon, $attackerMove['Stat'], $attackerMove['Amount']);
            } else {
                $this->status->statusCalc($defenderPokemon, $attackerMove['Status'], $attackerMove['Name']);
            }
        } else {
            if ($attackerMove['Other'] == 1) {
                $this->specialAttacks->damageOther($attackerPokemon, $attackerMove, $defenderPokemon);
            } else {
                $this->damage->damageCalc($attackerPokemon, $attackerMove, $defenderPokemon);
                $this->damage->postDamage($attackerPokemon, $attackerMove, $defenderPokemon);
            }
        }
        $attackerPokemon['Move Turns']--;
    }
}

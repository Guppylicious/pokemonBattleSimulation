<?php

namespace Game;

class Attack
{
    /**
     * Start the attack of a pokémon
     * @param array $attackerPokemon The pokémon performing the attack
     * @param array $attackerMove The move the pokémon is using to attack
     * @param array $defenderPokemon The pokémon defending the attack
     * @return array The attacker and defender after any damage or statuses have been applied
     */
    public function start($attackerPokemon, $attackerMove, $defenderPokemon)
    {
        $specialAttacks = new SpecialAttacks();
        $stats = new Stats();
        $status = new Status();
        $damage = new Damage();

        if ($attackerMove['Category'] == 'Status') {
            $attackerPokemon['Damage Dealt'] = 0;

            if ($attackerMove['Other'] == 1) {
                $specialAttack = $specialAttacks->specialAttack($attackerPokemon, $attackerMove, $defenderPokemon);

                $attackerPokemon = $specialAttack['attacker'];
                $defenderPokemon = $specialAttack['defender'];
            } elseif ($attackerMove['Target'] == 'User') {
                $attackerPokemon = $stats->statCalc($attackerPokemon, $attackerMove['Stat'], $attackerMove['Amount']);
            } elseif ($attackerMove['Target'] == 'Foe') {
                $defenderPokemon = $stats->statCalc($defenderPokemon, $attackerMove['Stat'], $attackerMove['Amount']);
            } else {
                $defenderPokemon = $status->statusCalc($defenderPokemon, $attackerMove['Status'], $attackerMove['Name']);
            }
        } else {
            if ($attackerMove['Other'] == 1) {
                $damageTotal = $specialAttacks->specialAttack($attackerPokemon, $attackerMove, $defenderPokemon);
            } else {
                $damageDealt = $damage->damageCalc($attackerPokemon, $attackerMove, $defenderPokemon);
                $damageTotal = $damage->postDamage($damageDealt['attacker'], $attackerMove, $damageDealt['defender']);
            }

            $attackerPokemon = $damageTotal['attacker'];
            $defenderPokemon = $damageTotal['defender'];
        }

        $attackerPokemon['Move Turns']--;

        return array('attacker' => $attackerPokemon, 'defender' => $defenderPokemon);
    }
}

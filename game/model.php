<?php

class Model
{
    public function __construct()
    {
        $this->loader = new Loader();
    }

    public function getMoves()
    {
        return $this->loader->getData('moves');
    }

    public function getTrainers()
    {
        return $this->loader->getData('trainers');
    }

    public function getTypes()
    {
        return $this->loader->getData('types');
    }

    public function getColor($key)
    {
        return $this->loader->typeColor($key);
    }
}

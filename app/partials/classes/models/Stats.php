<?php

namespace MMWS\Model;

use MMWS\Interfaces\AbstractModel;
use MMWS\Model\Food;

class Stats extends AbstractModel
{
    protected float $carb;
    protected float $prot;
    protected float $tfat;
    protected float $calories;
    protected float $fibers;
    protected float $sodium;
    protected float $amount;

    protected Food $food;

    public function __construct(Food $food, float $amount)
    {
        $this->food = $food;
        $this->amount = $amount;
        $this->calcStats();
        $this->setHiddenFields(['food']);
    }

    private function calcStats()
    {
        $weight = $this->food->weight ?? 1;
        $this->carb = $this->food->carb / $weight * $this->amount;
        $this->prot = $this->food->prot / $weight * $this->amount;
        $this->tfat = $this->food->tfat / $weight * $this->amount;
        $this->calories = $this->food->cal / $weight * $this->amount;
        $this->fibers = $this->food->fiber / $weight * $this->amount;
        $this->sodium = $this->food->sodium / $weight * $this->amount;
    }
}

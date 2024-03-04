<?php

namespace App\Dtos\Events;

use App\Dtos\Dto;

class DepositDto extends Dto
{
    public int $destination;
    public float $amount;
}

<?php

namespace App\Dtos\Events;

use App\Dtos\Dto;

class WithdrawDto extends Dto
{
    public int $origin;
    public float $amount;
}

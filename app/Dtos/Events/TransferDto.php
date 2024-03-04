<?php

namespace App\Dtos\Events;

use App\Dtos\Dto;

class TransferDto extends Dto
{
    public int $origin;
    public float $amount;
    public int $destination;
}

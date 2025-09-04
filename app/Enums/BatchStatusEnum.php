<?php

namespace App\Enums;

enum BatchStatusEnum: int
{
    case PENDING    = 0;
    case PROCESSING = 1;
    case COMPLETED  = 2;
    case FAILED     = 3;
    case PARTIAL    = 4;
}

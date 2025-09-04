<?php

namespace App\Enums;

enum FileStatusEnum: int
{
    case PENDING    = 0;
    case PROCESSING = 1;
    case COMPLETED  = 2;
    case FAILED     = 3;
}

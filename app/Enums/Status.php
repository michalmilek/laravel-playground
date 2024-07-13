<?php

namespace App\Enums;
enum Status: string
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case DELETED = 'deleted';
    case ERROR = 'error';
    case SUCCESS = 'success';
}

<?php

namespace App\Enums;

enum UserRole: int
{
   // The value of the constant is the role ID in the database for DEV
    case DEV = -1;

    // The value of the constant is the role ID in the database for ADMIN
    case ADMIN = 1;

    // The value of the constant is the role ID in the database for MEMBER
    case MEMBER = 2;

    // The value of the constant is the role ID in the database for GUEST
    case GUEST = 3;
}

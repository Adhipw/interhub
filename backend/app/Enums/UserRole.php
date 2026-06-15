<?php

namespace App\Enums;

enum UserRole: string
{
    case SUPER_ADMIN = 'super_admin';
    case ADMIN = 'admin';
    case HR = 'hr';
    case MENTOR = 'mentor';
    case USER = 'user';

    public function label(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Super Admin',
            self::ADMIN => 'Administrator',
            self::HR => 'Human Resources',
            self::MENTOR => 'Mentor',
            self::USER => 'User / Mahasiswa',
        };
    }
}

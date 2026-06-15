<?php

namespace App\Enums;

enum CompanyRole: string
{
    case OWNER = 'owner';
    case HR = 'hr';
    case MENTOR = 'mentor';
    case VIEWER = 'viewer';

    public function label(): string
    {
        return match ($this) {
            self::OWNER => 'Owner / Pemilik',
            self::HR => 'HR Representative',
            self::MENTOR => 'Mentor / Supervisor',
            self::VIEWER => 'Viewer Only',
        };
    }
}

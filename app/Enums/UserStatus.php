<?php

namespace App\Enums;

enum UserStatus: int
{
    case INACTIVE = 0;
    case ACTIVE = 1;
    case SUSPENDED = 2;

    public function label(): string
    {
        return match($this) {
            self::INACTIVE => 'Tidak Aktif',
            self::ACTIVE => 'Aktif',
            self::SUSPENDED => 'Ditangguhkan',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::INACTIVE => 'secondary',
            self::ACTIVE => 'success',
            self::SUSPENDED => 'warning',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
<?php

namespace App\Enums;

enum UserStatus: int
{
    case RESIGNED = 0;
    case ACTIVE = 1;

    public function label(): string
    {
        return match($this) {
            self::RESIGNED => 'Resign',
            self::ACTIVE => 'Aktif',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::RESIGNED => 'secondary',
            self::ACTIVE => 'success',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
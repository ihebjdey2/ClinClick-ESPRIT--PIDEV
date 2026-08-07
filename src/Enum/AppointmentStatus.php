<?php

declare(strict_types=1);

namespace App\Enum;

enum AppointmentStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case NO_SHOW = 'no_show';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'En attente',
            self::CONFIRMED => 'Confirmé',
            self::COMPLETED => 'Terminé',
            self::CANCELLED => 'Annulé',
            self::NO_SHOW => 'Absent',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING => 'bg-warning text-dark',
            self::CONFIRMED => 'bg-primary',
            self::COMPLETED => 'bg-success',
            self::CANCELLED => 'bg-secondary',
            self::NO_SHOW => 'bg-danger',
        };
    }

    public function releasesTimeSlot(): bool
    {
        return in_array($this, [self::CANCELLED, self::NO_SHOW], true);
    }
}

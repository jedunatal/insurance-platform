<?php

namespace App\Enums;

enum LeadSourceEnum: string
{
    case Site = 'site';
    case Whatsapp = 'whatsapp';
    case Instagram = 'instagram';
    case Facebook = 'facebook';
    case Referral = 'referral';
    case Google = 'google';
    case Manual = 'manual';

    public function label(): string
    {
        return match ($this) {
            self::Site => 'Site',
            self::Whatsapp => 'WhatsApp',
            self::Instagram => 'Instagram',
            self::Facebook => 'Facebook',
            self::Referral => 'Indicação',
            self::Google => 'Google',
            self::Manual => 'Manual',
        };
    }

    public function emoji(): string
    {
        return match ($this) {
            self::Site => '🌐',
            self::Whatsapp => '💬',
            self::Instagram => '📸',
            self::Facebook => '👥',
            self::Referral => '🤝',
            self::Google => '🔍',
            self::Manual => '✍️',
        };
    }

    public function colorClass(): string
    {
        return match ($this) {
            self::Site => 'text-slate-600',
            self::Whatsapp => 'text-green-600',
            self::Instagram => 'text-pink-600',
            self::Facebook => 'text-blue-600',
            self::Referral => 'text-amber-600',
            self::Google => 'text-red-500',
            self::Manual => 'text-gray-600',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return array_column(
            array_map(
                fn (self $case) => [
                    'value' => $case->value,
                    'label' => $case->label(),
                ],
                self::cases()
            ),
            'label',
            'value'
        );
    }
}
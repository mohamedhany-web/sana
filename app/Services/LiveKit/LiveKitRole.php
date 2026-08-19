<?php

namespace App\Services\LiveKit;

/**
 * Role grants for LiveKit VideoGrant.
 */
final class LiveKitRole
{
    public const HOST = 'host';

    public const PARTICIPANT = 'participant';

    public const SUPERVISOR = 'supervisor';

    public const HIDDEN_OBSERVER = 'hidden_observer';

    public const GUEST = 'guest';

    /**
     * @return array{can_publish: bool, can_subscribe: bool, can_publish_data: bool, hidden: bool}
     */
    public static function grants(string $role): array
    {
        return match ($role) {
            self::HOST => [
                'can_publish' => true,
                'can_subscribe' => true,
                'can_publish_data' => true,
                'hidden' => false,
            ],
            self::SUPERVISOR => [
                'can_publish' => false,
                'can_subscribe' => true,
                'can_publish_data' => true,
                'hidden' => false,
            ],
            self::HIDDEN_OBSERVER => [
                'can_publish' => false,
                'can_subscribe' => true,
                'can_publish_data' => false,
                'hidden' => true,
            ],
            self::PARTICIPANT, self::GUEST => [
                'can_publish' => true,
                'can_subscribe' => true,
                'can_publish_data' => true,
                'hidden' => false,
            ],
            default => [
                'can_publish' => true,
                'can_subscribe' => true,
                'can_publish_data' => true,
                'hidden' => false,
            ],
        };
    }

    public static function isHiddenObserver(string $role): bool
    {
        return $role === self::HIDDEN_OBSERVER;
    }
}

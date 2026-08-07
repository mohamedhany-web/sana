<?php

namespace App\Services\LiveKit;

use Agence104\LiveKit\AccessToken;
use Agence104\LiveKit\AccessTokenOptions;
use Agence104\LiveKit\VideoGrant;
use RuntimeException;

class LiveKitTokenService
{
    public function __construct(
        protected LiveKitRoomService $rooms,
    ) {}

    /**
     * Mint a LiveKit access token for a single room + role.
     *
     * @param  array{mute_on_join?: bool, video_off_on_join?: bool}  $options
     * @return array{token: string, url: string, room: string, identity: string, role: string, ttl: int}
     */
    public function issue(
        string $roomName,
        string $identity,
        string $displayName,
        string $role = LiveKitRole::PARTICIPANT,
        array $options = [],
    ): array {
        $apiKey = (string) config('livekit.api_key');
        $apiSecret = (string) config('livekit.api_secret');
        $url = (string) config('livekit.url');
        $ttl = max(300, (int) config('livekit.token_ttl', 14400));

        if ($apiKey === '' || $apiSecret === '') {
            throw new RuntimeException('مفاتيح LiveKit غير مضبوطة على السيرفر. أضف LIVEKIT_API_KEY و LIVEKIT_API_SECRET في ملف .env ثم نفّذ php artisan config:clear.');
        }

        $room = $this->rooms->sanitize($roomName);
        $identity = $this->sanitizeIdentity($identity);
        $displayName = mb_substr(trim($displayName) !== '' ? trim($displayName) : $identity, 0, 120);
        $grants = LiveKitRole::grants($role);

        $tokenOptions = (new AccessTokenOptions)
            ->setIdentity($identity)
            ->setName($displayName)
            ->setTtl($ttl);

        $videoGrant = (new VideoGrant)
            ->setRoomJoin(true)
            ->setRoomName($room)
            ->setCanPublish($grants['can_publish'])
            ->setCanSubscribe($grants['can_subscribe'])
            ->setCanPublishData($grants['can_publish_data']);

        if (! empty($grants['hidden']) && method_exists($videoGrant, 'setHidden')) {
            $videoGrant->setHidden(true);
        }

        $token = (new AccessToken($apiKey, $apiSecret))
            ->init($tokenOptions)
            ->setGrant($videoGrant)
            ->toJwt();

        return [
            'token' => $token,
            'url' => $url,
            'room' => $room,
            'identity' => $identity,
            'role' => $role,
            'ttl' => $ttl,
            'mute_on_join' => (bool) ($options['mute_on_join'] ?? false),
            'video_off_on_join' => (bool) ($options['video_off_on_join'] ?? false),
        ];
    }

    public function identityForUser(int $userId): string
    {
        return 'user:'.$userId;
    }

    public function identityForGuest(string $participantToken): string
    {
        return 'guest:'.substr(hash('sha256', $participantToken), 0, 32);
    }

    protected function sanitizeIdentity(string $identity): string
    {
        $identity = trim($identity);
        $identity = preg_replace('/\s+/', '-', $identity) ?? $identity;
        $identity = preg_replace('/[^A-Za-z0-9_\-\:\.]/', '', $identity) ?? $identity;

        if ($identity === '') {
            throw new RuntimeException('LiveKit identity cannot be empty.');
        }

        return mb_substr($identity, 0, 128);
    }
}

<?php

return [

    /*
    |--------------------------------------------------------------------------
    | LiveKit (Sana realtime classrooms)
    |--------------------------------------------------------------------------
    | Self-hosted SFU at live.sanaedu.com — used for ClassroomMeeting,
    | LiveSession, and curriculum unit rooms.
    */

    'url' => env('LIVEKIT_URL', 'wss://live.sanaedu.com'),

    'public_url' => env('LIVEKIT_PUBLIC_URL', 'https://live.sanaedu.com'),

    'api_key' => env('LIVEKIT_API_KEY'),

    'api_secret' => env('LIVEKIT_API_SECRET'),

    /** Access token TTL in seconds (default 4 hours). */
    'token_ttl' => (int) env('LIVEKIT_TOKEN_TTL', 14400),

    'turn' => [
        'url' => env('LIVEKIT_TURN_URL', 'turn:live.sanaedu.com:3478'),
        'url_tls' => env('LIVEKIT_TURN_URL_TLS', 'turns:live.sanaedu.com:5349'),
        'secret' => env('LIVEKIT_TURN_SECRET'),
    ],

];

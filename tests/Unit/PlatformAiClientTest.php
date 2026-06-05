<?php

namespace Tests\Unit;

use App\Services\PlatformAiClient;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PlatformAiClientTest extends TestCase
{
    public function test_is_configured_requires_key_and_enabled_flag_in_tests(): void
    {
        Config::set('platform_ai.enabled', false);
        Config::set('platform_ai.api_key', 'secret');
        $this->assertFalse(app(PlatformAiClient::class)->isConfigured());

        Config::set('platform_ai.enabled', true);
        Config::set('platform_ai.api_key', '');
        $this->assertFalse(app(PlatformAiClient::class)->isConfigured());

        Config::set('platform_ai.enabled', true);
        Config::set('platform_ai.api_key', 'secret');
        $this->assertTrue(app(PlatformAiClient::class)->isConfigured());
    }

    public function test_generate_from_prompt_parses_text(): void
    {
        Config::set('platform_ai.enabled', true);
        Config::set('platform_ai.api_key', 'test-api-key');
        Config::set('platform_ai.model', 'gemini-flash-latest');
        Config::set('platform_ai.base_url', 'https://generativelanguage.googleapis.com/v1beta');
        Config::set('platform_ai.http_timeout', 10);
        Config::set('platform_ai.max_output_tokens', 256);

        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'Hello from model'],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $out = app(PlatformAiClient::class)->generateFromPrompt('[SYSTEM] test');
        $this->assertSame('Hello from model', $out);
    }
}

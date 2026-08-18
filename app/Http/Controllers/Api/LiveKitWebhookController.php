<?php

namespace App\Http\Controllers\Api;

use Agence104\LiveKit\WebhookReceiver;
use App\Http\Controllers\Controller;
use App\Services\LessonRecordingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * LiveKit server webhook (EGRESS_ENDED) — يربط ملف R2 بالحصة والمعلم والطالب.
 */
class LiveKitWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $apiKey = (string) config('livekit.api_key');
        $apiSecret = (string) config('livekit.api_secret');
        if ($apiKey === '' || $apiSecret === '') {
            return response()->json(['error' => 'LiveKit is not configured'], 503);
        }

        try {
            $auth = (string) $request->header('Authorization');
            $auth = preg_replace('/^Bearer\s+/i', '', $auth) ?: null;
            $receiver = new WebhookReceiver($apiKey, $apiSecret);
            $event = $receiver->receive($request->getContent(), $auth);
        } catch (Throwable $e) {
            Log::warning('LiveKit webhook rejected', ['message' => $e->getMessage()]);

            return response()->json(['error' => 'Invalid webhook'], 401);
        }

        $eventName = method_exists($event, 'getEvent') ? (string) $event->getEvent() : '';
        if (! in_array($eventName, ['egress_ended', 'EGRESS_ENDED'], true)) {
            return response()->json(['ok' => true, 'ignored' => $eventName]);
        }

        $info = method_exists($event, 'getEgressInfo') ? $event->getEgressInfo() : null;
        $egressId = $info && method_exists($info, 'getEgressId') ? (string) $info->getEgressId() : '';
        $roomName = $info && method_exists($info, 'getRoomName') ? (string) $info->getRoomName() : '';
        $filePath = null;
        $fileSize = 0;
        $duration = 0;

        if ($info && method_exists($info, 'getFileResults')) {
            foreach ($info->getFileResults() as $file) {
                if (method_exists($file, 'getFilename')) {
                    $filePath = (string) $file->getFilename();
                }
                if (method_exists($file, 'getSize')) {
                    $fileSize = (int) $file->getSize();
                }
                if (method_exists($file, 'getDuration')) {
                    $duration = (int) round(((int) $file->getDuration()) / 1e9);
                }
                break;
            }
        }

        $error = '';
        if ($info && method_exists($info, 'getError')) {
            $error = trim((string) $info->getError());
        }

        app(LessonRecordingService::class)->markFromWebhook(
            $egressId !== '' ? $egressId : null,
            $roomName !== '' ? $roomName : null,
            $filePath,
            $fileSize,
            $duration,
            $error !== '' ? $error : null
        );

        return response()->json(['ok' => true]);
    }
}

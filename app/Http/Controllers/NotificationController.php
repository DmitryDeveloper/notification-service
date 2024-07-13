<?php

namespace App\Http\Controllers;

use App\DTOs\NotificationDTO;
use App\DTOs\NotificationPayloadDTO;
use App\DTOs\RecipientDTO;
use App\Http\Requests\SendNotificationRequest;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    /**
     * @param SendNotificationRequest $request
     * @param NotificationService $notificationService
     * @return JsonResponse
     */
    public function send(SendNotificationRequest $request, NotificationService $notificationService): JsonResponse
    {
        $notification = new NotificationDTO(
            $request->validated('channels'),
            $request->validated('sender_uuid'),
            new RecipientDTO(
                $request->validated('recipient_uuid'),
                $request->validated('recipient_email'),
                $request->validated('recipient_phone'),
                $request->validated('recipient_device_token'),
            ),
            new NotificationPayloadDTO(
                $request->validated('subject'),
                $request->validated('message'),
            )
        );

        $notificationService->send($notification);
        return response()->json(['message' => 'Notification is being processed'], 200);
    }
}

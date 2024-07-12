<?php

namespace App\Http\Controllers;

use App\DTOs\NotificationDTO;
use App\DTOs\NotificationPayloadDTO;
use App\DTOs\RecipientDTO;
use App\Http\Requests\SendRequest;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    /**
     * @param SendRequest $request
     * @param NotificationService $notificationService
     * @return JsonResponse
     */
    public function send(SendRequest $request, NotificationService $notificationService): JsonResponse
    {
        //TODO Missing requirement, authorize request (Solution: Middleware with checking Auth signature)
        $notification = new NotificationDTO(
            $request->validated()['channels'],
            $request->validated()['sender_uuid'],
            new RecipientDTO(
                $request->validated()['recipient_uuid'],
                $request->validated()['recipient_address']
            ),
            new NotificationPayloadDTO(
                $request->validated()['subject'],
                $request->validated()['message'],
            )
        );

        $notificationService->send($notification);
        return response()->json(['message' => 'Notification is being processed'], 200);
    }
}

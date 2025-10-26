<?php

namespace App\Channels;

use App\Services\SmsService;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class SmsChannel
{
    protected $smsService;

    /**
     * Create a new SMS channel instance.
     */
    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        // Get phone number from notifiable entity
        $phoneNumber = $notifiable->phone ?? $notifiable->phone_number ?? null;

        // Debug logging
        Log::info('SMS Channel: Attempting to send SMS', [
            'notifiable_id' => $notifiable->id ?? null,
            'notifiable_type' => get_class($notifiable),
            'notifiable_name' => $notifiable->name ?? 'Unknown',
            'phone' => $phoneNumber,
            'has_phone_field' => property_exists($notifiable, 'phone'),
            'has_phone_number_field' => property_exists($notifiable, 'phone_number')
        ]);

        if (!$phoneNumber) {
            Log::warning('SMS Channel: No phone number found for notifiable', [
                'notifiable_id' => $notifiable->id ?? null,
                'notifiable_type' => get_class($notifiable),
                'notifiable_name' => $notifiable->name ?? 'Unknown'
            ]);
            return;
        }

        // Get SMS message from notification
        if (!method_exists($notification, 'toSms')) {
            Log::warning('SMS Channel: Notification does not implement toSms method', [
                'notification_type' => get_class($notification)
            ]);
            return;
        }

        $message = $notification->toSms($notifiable);

        if (!$message) {
            Log::warning('SMS Channel: Empty SMS message from notification', [
                'notification_type' => get_class($notification)
            ]);
            return;
        }

        // Send SMS
        try {
            $result = $this->smsService->sendSms($phoneNumber, $message, 'notification');
            
            if ($result['success']) {
                Log::info('SMS Channel: SMS sent successfully', [
                    'phone' => $phoneNumber,
                    'notification_type' => get_class($notification),
                    'message_id' => $result['data']['message_id'] ?? null
                ]);
            } else {
                Log::error('SMS Channel: Failed to send SMS', [
                    'phone' => $phoneNumber,
                    'notification_type' => get_class($notification),
                    'error' => $result['message'] ?? 'Unknown error'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('SMS Channel: Exception while sending SMS', [
                'phone' => $phoneNumber,
                'notification_type' => get_class($notification),
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}

<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Services\KafkaService;
use Illuminate\Support\Facades\Log;

class SendUserCreatedToKafka
{
    private KafkaService $kafkaService;
    private static array $processedUsers = [];

    public function __construct(KafkaService $kafkaService)
    {
        $this->kafkaService = $kafkaService;
    }

    public function handle(UserCreated $event): void
    {
        if (app()->environment('testing')) {
            Log::info('Skipping Kafka event in test environment', [
                'user_id' => $event->user->id,
                'user_email' => $event->user->email
            ]);
            return;
        }

        $userId = $event->user->id;
        $userEmail = $event->user->email;
        
        if (isset(self::$processedUsers[$userId])) {
            Log::info('User already processed in this request, skipping duplicate', [
                'user_id' => $userId,
                'user_email' => $userEmail,
                'processed_at' => self::$processedUsers[$userId]
            ]);
            return;
        }
        
        self::$processedUsers[$userId] = now()->toISOString();
        
        $jobId = uniqid('kafka_', true);
        
        Log::info('SendUserCreatedToKafka listener started', [
            'job_id' => $jobId,
            'user_id' => $userId,
            'user_email' => $userEmail,
            'timestamp' => now()->toISOString()
        ]);

        try {
            $userData = [
                'id' => $userId,
                'name' => $event->user->name,
                'email' => $userEmail,
                'created_at' => $event->user->created_at->toISOString(),
                'updated_at' => $event->user->updated_at->toISOString(),
            ];

            Log::info('Preparing to send user data to Kafka', [
                'job_id' => $jobId,
                'user_data' => $userData
            ]);

            $success = $this->kafkaService->produceUserCreated($userData);

            if ($success) {
                Log::info('User created event sent to Kafka successfully', [
                    'job_id' => $jobId,
                    'user_id' => $userId,
                    'email' => $userEmail
                ]);
            } else {
                Log::error('Failed to send user created event to Kafka', [
                    'job_id' => $jobId,
                    'user_id' => $userId,
                    'email' => $userEmail
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Exception occurred while sending user created event to Kafka', [
                'job_id' => $jobId,
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
} 
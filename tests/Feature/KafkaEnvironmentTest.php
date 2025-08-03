<?php

namespace Tests\Feature;

use App\Services\KafkaService;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class KafkaEnvironmentTest extends TestCase
{
    public function test_environment_is_testing()
    {
        $this->assertEquals('testing', app()->environment());
    }

    public function test_kafka_service_skips_in_test_environment()
    {
        Log::shouldReceive('info')
            ->with('Skipping Kafka produce in test environment', [
                'topic' => 'user-events',
                'key' => '123'
            ])
            ->once();

        $kafkaService = new KafkaService();
        $result = $kafkaService->produce('user-events', ['test' => 'data'], '123');

        $this->assertTrue($result);
    }

    public function test_kafka_service_produces_user_created_in_test_environment()
    {
        Log::shouldReceive('info')
            ->with('Skipping Kafka produce in test environment', [
                'topic' => 'user-events',
                'key' => '123'
            ])
            ->once();

        $kafkaService = new KafkaService();
        $userData = [
            'id' => 123,
            'name' => 'Test User',
            'email' => 'test@example.com'
        ];

        $result = $kafkaService->produceUserCreated($userData);

        $this->assertTrue($result);
    }
} 
<?php

namespace App\Services;

use RdKafka\Conf;
use RdKafka\Producer;
use RdKafka\TopicConf;
use Illuminate\Support\Facades\Log;

class KafkaService
{
    private Producer $producer;
    private string $brokers;

    public function __construct()
    {
        $this->brokers = config('kafka.brokers', 'kafka:29092');
        $this->initializeProducer();
    }

    private function initializeProducer(): void
    {
        $conf = new Conf();
        $conf->set('metadata.broker.list', $this->brokers);
        $conf->set('socket.timeout.ms', 10000);
        $conf->set('queue.buffering.max.messages', 100000);
        $conf->set('queue.buffering.max.ms', 1000);
        $conf->set('batch.num.messages', 1000);
        $conf->set('delivery.timeout.ms', 30000);
        $conf->set('request.timeout.ms', 30000);

        $this->producer = new Producer($conf);
    }

    public function produce(string $topic, array $message, ?string $key = null): bool
    {
        if (app()->environment('testing')) {
            Log::info('Skipping Kafka produce in test environment', [
                'topic' => $topic,
                'key' => $key
            ]);
            return true;
        }

        try {
            $topicConf = new TopicConf();
            $topicConf->set('request.required.acks', 1);
            $topicConf->set('request.timeout.ms', 5000);

            $kafkaTopic = $this->producer->newTopic($topic, $topicConf);
            
            $payload = json_encode($message);
            $kafkaTopic->produce(RD_KAFKA_PARTITION_UA, 0, $payload, $key);
            
            $this->producer->flush(10000);
            
            Log::info('Message sent to Kafka', [
                'topic' => $topic,
                'key' => $key,
                'message' => $message
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send message to Kafka', [
                'topic' => $topic,
                'key' => $key,
                'message' => $message,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    public function produceUserCreated(array $userData): bool
    {
        $message = [
            'event' => 'user.created',
            'timestamp' => now()->toISOString(),
            'data' => $userData,
            'metadata' => [
                'source' => 'laravel-api',
                'version' => '1.0.0'
            ]
        ];

        return $this->produce('user-events', $message, $userData['id'] ?? null);
    }
} 
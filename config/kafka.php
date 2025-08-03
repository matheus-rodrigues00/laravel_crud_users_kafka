<?php

return [
    'brokers' => env('KAFKA_BROKERS', 'kafka:29092'),

    'topics' => [
        'user_events' => env('KAFKA_TOPIC_USER_EVENTS', 'user-events'),
        'email_notifications' => env('KAFKA_TOPIC_EMAIL_NOTIFICATIONS', 'email-notifications'),
    ],

    'consumer_groups' => [
        'email_service' => env('KAFKA_CONSUMER_GROUP_EMAIL', 'email-service-group'),
        'notification_service' => env('KAFKA_CONSUMER_GROUP_NOTIFICATIONS', 'notification-service-group'),
    ],

    'producer' => [
        'socket_timeout_ms' => env('KAFKA_SOCKET_TIMEOUT_MS', 10000),
        'queue_buffering_max_messages' => env('KAFKA_QUEUE_BUFFERING_MAX_MESSAGES', 100000),
        'queue_buffering_max_ms' => env('KAFKA_QUEUE_BUFFERING_MAX_MS', 1000),
        'batch_num_messages' => env('KAFKA_BATCH_NUM_MESSAGES', 1000),
        'delivery_timeout_ms' => env('KAFKA_DELIVERY_TIMEOUT_MS', 30000),
        'request_timeout_ms' => env('KAFKA_REQUEST_TIMEOUT_MS', 30000),
    ],

    'consumer' => [
        'auto_offset_reset' => env('KAFKA_AUTO_OFFSET_RESET', 'earliest'),
        'enable_auto_commit' => env('KAFKA_ENABLE_AUTO_COMMIT', true),
        'auto_commit_interval_ms' => env('KAFKA_AUTO_COMMIT_INTERVAL_MS', 5000),
        'session_timeout_ms' => env('KAFKA_SESSION_TIMEOUT_MS', 30000),
        'heartbeat_interval_ms' => env('KAFKA_HEARTBEAT_INTERVAL_MS', 3000),
    ],
]; 
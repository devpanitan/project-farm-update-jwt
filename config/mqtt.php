<?php
return [
    'default' => env('MQTT_CONNECTION', 'default'),
    'connections' => [
        'default' => [
            'host' => env('MQTT_HOST', '127.0.0.1'),
            'port' => env('MQTT_PORT', 1883),
            'protocol' => env('MQTT_PROTOCOL', '3.1.1'),
            'client_id' => env('MQTT_CLIENT_ID'),

            // CORRECTED: The auth array should directly use the main MQTT credentials
            'auth' => [
                 'username' => env('MQTT_USERNAME'),
                 'password' => env('MQTT_PASSWORD'),
            ],

            'tls' => [
                'enabled' => env('MQTT_TLS_ENABLED', false),
                'allow_self_signed_cert' => env('MQTT_TLS_ALLOW_SELF_SIGNED_CERT', false),
                'ca_file' => env('MQTT_TLS_CA_FILE'),
                'cert_file' => env('MQTT_TLS_CERT_FILE'),
                'key_file' => env('MQTT_TLS_KEY_FILE'),
                'verify_peer' => env('MQTT_TLS_VERIFY_PEER', true),
                'passphrase' => env('MQTT_TLS_PASSPHRASE'),
            ],
            
            'repository' => \PhpMqtt\Client\Repositories\MemoryRepository::class,
            'clean_session' => env('MQTT_CLEAN_SESSION', true),
            'quality_of_service' => env('MQTT_QOS', 0),
            'retain' => env('MQTT_RETAIN', false),
            
            'last_will' => [
                'topic' => env('MQTT_LAST_WILL_TOPIC'),
                'message' => env('MQTT_LAST_WILL_MESSAGE'),
                'quality_of_service' => env('MQTT_LAST_WILL_QOS', 0),
                'retain' => env('MQTT_LAST_WILL_RETAIN', false),
            ],
            
            'connect_timeout' => env('MQTT_CONNECT_TIMEOUT', 10),
            'socket_timeout' => env('MQTT_SOCKET_TIMEOUT', 5),
            'keep_alive_interval' => env('MQTT_KEEP_ALIVE_INTERVAL', 10),
        ],
    ],
];

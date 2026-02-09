<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth; // Kept for auth middleware, but not for MQTT password
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

class MQTTController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Provides the necessary credentials for the frontend MQTT client.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCredentials(Request $request)
    {
        try {
            // Important: Ensure these config keys match your config/mqtt.php file
            $credentials = [
                'host'      => config('mqtt.connections.default.host'),
                'port'      => (int) config('mqtt.connections.default.port', 1883),
                'username'  => config('mqtt.connections.default.auth.username'),
                'password'  => config('mqtt.connections.default.auth.password'),
                'clientId'  => 'frontend-' . auth()->id() . '-' . uniqid(), // Create a unique and identifiable client ID
                'useTls'    => config('mqtt.connections.default.tls.enabled', false),
            ];

            return response()->json(['status' => 'success', 'credentials' => $credentials]);

        } catch (\Exception $e) {
            Log::error("Failed to retrieve MQTT credentials: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Could not retrieve MQTT credentials.'
            ], 500);
        }
    }


    public function publishMessage(Request $request)
    {
        $request->validate([
            'topic'   => 'required|string',
            'message' => 'required|string',
        ]);

        $mqtt = null; 

        try {
            $user = auth()->user();
            
            $topic   = $request->input('topic');
            $message = $request->input('message');

            // --- CORRECTED, DIRECT APPROACH ---

            // 1. Get all connection details directly from the config file.
            $host     = config('mqtt.connections.default.host');
            $port     = config('mqtt.connections.default.port');
            $clientId = config('mqtt.connections.default.client_id', 'laravel-app') . '-' . uniqid(); // Make client ID unique
            $username = config('mqtt.connections.default.auth.username');
            $password = config('mqtt.connections.default.auth.password');

            // 2. Create a new MQTT client instance.
            $mqtt = new MqttClient($host, $port, $clientId);

            // 3. Create Connection Settings using the CORRECT credentials from the config.
            $connectionSettings = (new ConnectionSettings)
                ->setUsername($username)
                ->setPassword($password);

            // Optional: Configure TLS if needed (this part was already correct)
            if (config('mqtt.connections.default.tls.enabled', false)) {
                $connectionSettings->setUseTls(true)
                    ->setTlsSelfSigned(config('mqtt.connections.default.tls.allow_self_signed_cert', false));
            }

            // 4. Connect, publish, and disconnect
            $mqtt->connect($connectionSettings, true); // true = clean session
            $mqtt->publish($topic, $message, MqttClient::QOS_AT_LEAST_ONCE);

            Log::info("MQTT message published by user {$user->id} to topic '{$topic}'");

            return response()->json(['status' => 'success', 'message' => 'Message published successfully.']);

        } catch (\Exception $e) {
            Log::error("Failed to publish MQTT message: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to publish message.',
                'error_details' => $e->getMessage()
            ], 500);
        } finally {
            if ($mqtt && $mqtt->isConnected()) {
                $mqtt->disconnect();
            }
        }
    }
}

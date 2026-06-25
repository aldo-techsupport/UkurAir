<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

class DeviceController extends Controller
{
    private function publishMqtt(string $message): bool
    {
        $host     = env('MQTT_BROKER', '127.0.0.1');
        $port     = (int) env('MQTT_PORT', 1883);
        $topic    = env('MQTT_TOPIC_COMMAND', 'tandon/perintah');
        $clientId = env('MQTT_CLIENT_ID', 'publisher-ukurair') . '-pub-' . uniqid();
        $username = env('MQTT_USERNAME', '');
        $password = env('MQTT_PASSWORD', '');

        try {
            $client = new MqttClient($host, $port, $clientId, MqttClient::MQTT_3_1_1);

            $settings = new ConnectionSettings();
            $settings = $settings->setKeepAliveInterval(60);

            if ($username) {
                $settings = $settings->setUsername($username);
                $settings = $settings->setPassword($password ?: null);
            }

            $client->connect($settings, true);
            $client->publish($topic, $message, MqttClient::QOS_AT_MOST_ONCE);
            $client->disconnect();

            return true;
        } catch (\Throwable $e) {
            \Log::error("MQTT Publish Error: {$e->getMessage()}");
            return false;
        }
    }

    public function setRelay(Request $request)
    {
        $request->validate([
            'relay' => 'required|boolean',
        ]);

        $relay = $request->input('relay');
        $payload = json_encode([
            'relay' => $relay,
        ]);

        $sent = $this->publishMqtt($payload);

        if (!$sent) {
            return response()->json(['success' => false, 'message' => 'Gagal mengirim perintah ke ESP'], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Relay berhasil di' . ($relay ? 'nyalakan' : 'matikan'),
            'relay' => $relay,
        ]);
    }

    public function setMode(Request $request)
    {
        $request->validate([
            'mode' => 'required|in:AUTO,MANUAL',
        ]);

        $mode = $request->input('mode');
        $payload = json_encode([
            'mode' => $mode,
        ]);

        $sent = $this->publishMqtt($payload);

        if (!$sent) {
            return response()->json(['success' => false, 'message' => 'Gagal mengirim perintah ke ESP'], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Mode berhasil diubah ke ' . $mode,
            'mode' => $mode,
        ]);
    }
}

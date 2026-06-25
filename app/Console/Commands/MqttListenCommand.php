<?php

namespace App\Console\Commands;

use App\Models\WaterLevel;
use Illuminate\Console\Command;
use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\Exceptions\MqttClientException;

class MqttListenCommand extends Command
{
    protected $signature = 'mqtt:listen';
    protected $description = 'Subscribe ke topic MQTT dan update data ketinggian air secara real-time';

    public function handle(): int
    {
        $host     = env('MQTT_BROKER', '127.0.0.1');
        $port     = (int) env('MQTT_PORT', 1883);
        $topic    = env('MQTT_TOPIC', 'tandon/ketinggian');
        $clientId = env('MQTT_CLIENT_ID', 'subscriber-ukurair');
        $username = env('MQTT_USERNAME', '');
        $password = env('MQTT_PASSWORD', '');

        $this->info("MQTT Listener starting...");
        $this->info("Broker: {$host}:{$port} | Topic: {$topic}");

        while (true) {
            try {
                $client = new MqttClient($host, $port, $clientId, MqttClient::MQTT_3_1_1);

                $settings = new ConnectionSettings();
                $settings = $settings->setKeepAliveInterval(60);
                $settings = $settings->setReconnectAutomatically(false);
                $settings = $settings->setMaxReconnectAttempts(3);

                if ($username) {
                    $settings = $settings->setUsername($username);
                    $settings = $settings->setPassword($password ?: null);
                }

                $client->connect($settings, true);
                $this->info("Terhubung ke MQTT Broker ({$host}:{$port})");

                $client->subscribe($topic, function (string $topic, string $message) use ($client) {
                    $this->handleMessage($message);
                }, MqttClient::QOS_AT_MOST_ONCE);

                $this->info("Subscribe topic: {$topic}");
                $this->info("Menunggu data...");

                $client->loop(true);
            } catch (MqttClientException $e) {
                $this->error("MQTT Error: {$e->getMessage()}");
                $this->info("Reconnect dalam 5 detik...");
                sleep(5);
            } catch (\Throwable $e) {
                $this->error("Error: {$e->getMessage()}");
                $this->info("Reconnect dalam 5 detik...");
                sleep(5);
            }
        }

        return self::SUCCESS;
    }

    private function handleMessage(string $payload): void
    {
        try {
            $payload = trim($payload);
            $payload = html_entity_decode($payload, ENT_QUOTES, 'UTF-8');

            $data = json_decode($payload, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->warn("JSON decode error: " . json_last_error_msg() . " | Payload: {$payload}");
                return;
            }

            if (!is_array($data) || !array_key_exists('tinggi', $data)) {
                $this->warn("Payload tidak valid (missing 'tinggi'): {$payload}");
                return;
            }

            $deviceId = $data['device_id'] ?? '001';
            $tinggi   = (float) $data['tinggi'];
            $relay    = isset($data['relay']) ? (bool) $data['relay'] : false;
            $mode     = $data['mode'] ?? 'AUTO';
            $status   = WaterLevel::hitungStatus($tinggi);

            WaterLevel::create([
                'device_id' => $deviceId,
                'tinggi'    => $tinggi,
                'status'    => $status,
                'relay'     => $relay,
                'mode'      => $mode,
            ]);

            $relayLabel = $relay ? 'ON' : 'OFF';
            $this->info("Device [{$deviceId}] | {$tinggi}% | Status: {$status} | Relay: {$relayLabel} | Mode: {$mode}");
        } catch (\Throwable $e) {
            $this->error("Parse error: {$e->getMessage()}");
        }
    }
}

# 🌊 Web Monitoring Ketinggian Air Tandon — AI Agent Brief

> **Proyek:** Rancang Bangun Sistem Pemantauan Ketinggian Air Tandon Rumah Tangga Berbasis IoT dan Web Secara Real-Time  
> **Peneliti:** Muhammad Adityawarman Hidayat — NIM 2227200899  
> **Institusi:** Universitas Dharma Adi Unggul Bhirawa (UNDHA-AUB), Surakarta  
> **Tahun:** 2026

---

## 1. Gambaran Umum Sistem

Sistem ini memantau ketinggian air tandon rumah tangga secara otomatis dan real-time menggunakan teknologi IoT. Data dari sensor ultrasonik dikirim melalui NodeMCU ESP8266 menggunakan protokol **MQTT** ke broker, lalu disimpan ke database dan ditampilkan di website monitoring.

### Arsitektur Sistem (3 Layer IoT)

```
[Sensor HC-SR04] → [NodeMCU ESP8266] --MQTT--> [Broker] → [Subscriber] → [MySQL] → [Website]
  Perception Layer      Network Layer           (Mosquitto)   (Python)      Database  Application Layer
```

---

## 2. Spesifikasi Hardware

| Komponen | Spesifikasi | Fungsi |
|---|---|---|
| NodeMCU ESP8266 | WiFi terintegrasi | Mikrokontroler + publish MQTT |
| Sensor HC-SR04 | Ultrasonik | Mengukur ketinggian air |
| Power Supply | Input 220V AC → Output 5V DC | Sumber daya |
| Breadboard + Kabel Jumper | — | Rangkaian |

**Rumus pengukuran sensor:**
```
Jarak = (Waktu × Kecepatan Suara) / 2
```

---

## 3. Spesifikasi Software & Stack

| Komponen | Teknologi | Fungsi |
|---|---|---|
| Mikrokontroler | Arduino IDE + PubSubClient | Upload program & MQTT publish |
| MQTT Broker | Mosquitto | Menerima & meneruskan data sensor |
| Subscriber | Python + paho-mqtt | Subscribe broker, simpan ke DB |
| Database | MySQL | Penyimpanan data sensor |
| Web Server | XAMPP (Apache + PHP) | Serve halaman web |
| Frontend | HTML/CSS/JS + Chart.js | Tampilan monitoring |
| Browser | Google Chrome | Akses web monitoring |

---

## 4. Database — Struktur Tabel

### Tabel: `tb_ketinggian_air`

| Field | Type | Length | Keterangan |
|---|---|---|---|
| `id` | INT | 5 | Primary Key, auto increment |
| `tinggi_air` | FLOAT | 5,2 | Nilai ketinggian air dalam cm |
| `status` | VARCHAR | 20 | `Penuh` / `Sedang` / `Rendah` |
| `waktu` | TIMESTAMP | — | Waktu pengiriman data (otomatis) |

### SQL Create Table
```sql
CREATE TABLE tb_ketinggian_air (
  id         INT(5)       NOT NULL AUTO_INCREMENT,
  tinggi_air FLOAT(5,2)   NOT NULL,
  status     VARCHAR(20)  NOT NULL,
  waktu      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
);
```

---

## 5. Alur Kerja Sistem (Flowchart)

```
START
  ↓
Sistem aktif & dapat daya
  ↓
Sensor HC-SR04 baca jarak permukaan air
  ↓
NodeMCU ESP8266 hitung ketinggian air
  ↓
NodeMCU PUBLISH data ke MQTT Broker (topic: tandon/ketinggian)
  ↓
Python Subscriber terima data dari Broker
  ↓
Subscriber simpan data ke MySQL
  ↓
Website ambil data dari database (polling / WebSocket)
  ↓
Tampilkan real-time ke pengguna
  ↓
[Loop setiap interval waktu]
```

---

## 6. Perancangan Website Monitoring

### 6.1 Halaman & Fitur

| Halaman | Fitur |
|---|---|
| **Dashboard** | Ringkasan status terkini ketinggian air |
| **Monitoring Real-Time** | Nilai ketinggian air (cm), persentase pengisian, status |
| **Grafik** | Grafik historis ketinggian air |
| **Riwayat Data** | Tabel log data dari database |

### 6.2 Status Air

| Status | Kondisi |
|---|---|
| 🟢 **Penuh** | Ketinggian air ≥ 80% kapasitas |
| 🟡 **Sedang** | Ketinggian air 30–79% |
| 🔴 **Rendah** | Ketinggian air < 30% |

### 6.3 Tampilan UI yang Dibutuhkan

```
┌─────────────────────────────────────────────┐
│  🌊 Water Tank Monitor                      │
├──────────────┬──────────────┬───────────────┤
│  Tinggi Air  │  Persentase  │    Status      │
│   xx.xx cm   │    xx%       │   🟢 PENUH    │
├──────────────┴──────────────┴───────────────┤
│  📊 Grafik Real-Time (Chart.js)              │
│  [line chart data historis ketinggian air]   │
├─────────────────────────────────────────────┤
│  📋 Tabel Log Data Terbaru                  │
│  ID | Tinggi Air | Status | Waktu           │
└─────────────────────────────────────────────┘
```

---

## 7. Arsitektur Komunikasi MQTT

MQTT (Message Queuing Telemetry Transport) adalah protokol ringan berbasis **publish/subscribe**, sangat cocok untuk NodeMCU ESP8266 karena hemat bandwidth dan lebih efisien dibanding HTTP.

```
[NodeMCU ESP8266]                [MQTT Broker]              [Python Subscriber]
  Publish data         →       (Mosquitto:1883)     →       Subscribe + simpan DB
  Topic: tandon/ketinggian                                   lalu web bisa polling
```

### Topics MQTT

| Topic | Arah | Isi Payload |
|---|---|---|
| `tandon/ketinggian` | ESP → Broker | `{"tinggi_air": 45.23, "status": "Penuh", "persen": 85}` |

### Format Payload JSON
```json
{
  "tinggi_air": 45.23,
  "status": "Penuh",
  "persen": 85
}
```

---

## 8. Kode NodeMCU (Arduino + MQTT)

> **Library yang dibutuhkan (install via Arduino Library Manager):**
> - `PubSubClient` — by Nick O'Leary
> - `ArduinoJson` — by Benoit Blanchon
> - `ESP8266WiFi` — bawaan board ESP8266

```cpp
#include <ESP8266WiFi.h>
#include <PubSubClient.h>
#include <ArduinoJson.h>

// ── Konfigurasi WiFi ──────────────────────────
const char* ssid     = "NAMA_WIFI";
const char* password = "PASSWORD_WIFI";

// ── Konfigurasi MQTT Broker ───────────────────
const char* mqtt_server = "192.168.x.x"; // IP server/PC yang jalankan Mosquitto
const int   mqtt_port   = 1883;
const char* mqtt_client = "NodeMCU-Tandon";
const char* topic_pub   = "tandon/ketinggian";

// ── Pin Sensor HC-SR04 ────────────────────────
#define TRIG_PIN D1
#define ECHO_PIN D2

// ── Tinggi tandon dalam cm (sesuaikan fisik) ──
const float TINGGI_TANDON = 100.0;

WiFiClient   espClient;
PubSubClient mqttClient(espClient);

void setupWiFi() {
  WiFi.begin(ssid, password);
  Serial.print("Menghubungkan WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500); Serial.print(".");
  }
  Serial.println("\nWiFi Connected! IP: " + WiFi.localIP().toString());
}

void reconnectMQTT() {
  while (!mqttClient.connected()) {
    Serial.println("Menghubungkan ke MQTT Broker...");
    if (mqttClient.connect(mqtt_client)) {
      Serial.println("MQTT Connected!");
    } else {
      Serial.print("Gagal, rc=");
      Serial.print(mqttClient.state());
      Serial.println(" — coba lagi 3 detik");
      delay(3000);
    }
  }
}

float bacaKetinggianAir() {
  digitalWrite(TRIG_PIN, LOW);  delayMicroseconds(2);
  digitalWrite(TRIG_PIN, HIGH); delayMicroseconds(10);
  digitalWrite(TRIG_PIN, LOW);

  long  durasi = pulseIn(ECHO_PIN, HIGH);
  float jarak  = durasi * 0.034 / 2;
  float tinggi = TINGGI_TANDON - jarak;
  return (tinggi < 0) ? 0 : tinggi;
}

String tentukanStatus(float tinggi) {
  float persen = (tinggi / TINGGI_TANDON) * 100;
  if (persen >= 80) return "Penuh";
  else if (persen >= 30) return "Sedang";
  else return "Rendah";
}

void publishData(float tinggi, String status) {
  int persen = (int)((tinggi / TINGGI_TANDON) * 100);

  StaticJsonDocument<128> doc;
  doc["tinggi_air"] = tinggi;
  doc["status"]     = status;
  doc["persen"]     = persen;

  char payload[128];
  serializeJson(doc, payload);

  mqttClient.publish(topic_pub, payload, true); // retain=true
  Serial.println("Published → " + String(payload));
}

void setup() {
  Serial.begin(115200);
  pinMode(TRIG_PIN, OUTPUT);
  pinMode(ECHO_PIN, INPUT);
  setupWiFi();
  mqttClient.setServer(mqtt_server, mqtt_port);
}

void loop() {
  if (!mqttClient.connected()) reconnectMQTT();
  mqttClient.loop();

  float  tinggi = bacaKetinggianAir();
  String status = tentukanStatus(tinggi);
  publishData(tinggi, status);

  delay(5000); // Publish setiap 5 detik
}
```

---

## 9. Backend — MQTT Subscriber (Python)

Script ini berjalan di server, mendengarkan topic MQTT, lalu menyimpan data ke MySQL.

```python
# mqtt_subscriber.py
import paho.mqtt.client as mqtt
import mysql.connector
import json

# ── Konfigurasi Database ──────────────────────
db = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="db_monitoring_air"
)

MQTT_BROKER = "localhost"
MQTT_PORT   = 1883
TOPIC       = "tandon/ketinggian"

def on_connect(client, userdata, flags, rc):
    print(f"Connected to broker (rc={rc})")
    client.subscribe(TOPIC)
    print(f"Subscribed to: {TOPIC}")

def on_message(client, userdata, msg):
    try:
        payload = json.loads(msg.payload.decode())
        tinggi  = payload["tinggi_air"]
        status  = payload["status"]

        cursor = db.cursor()
        cursor.execute(
            "INSERT INTO tb_ketinggian_air (tinggi_air, status) VALUES (%s, %s)",
            (tinggi, status)
        )
        db.commit()
        print(f"[DB] Tersimpan → Tinggi: {tinggi} cm | Status: {status}")
    except Exception as e:
        print(f"[ERROR] {e}")

client = mqtt.Client()
client.on_connect = on_connect
client.on_message = on_message
client.connect(MQTT_BROKER, MQTT_PORT, 60)
client.loop_forever()
```

**Install dependency & jalankan:**
```bash
pip install paho-mqtt mysql-connector-python
python mqtt_subscriber.py
```

---

## 10. PHP API — Web Ambil Data dari Database

HTTP hanya digunakan oleh **web browser** untuk mengambil data dari MySQL. ESP tidak pakai HTTP sama sekali.

### `get_latest.php` — Data terbaru untuk dashboard
```php
<?php
header("Content-Type: application/json");
$conn   = new mysqli("localhost", "root", "", "db_monitoring_air");
$result = $conn->query("SELECT * FROM tb_ketinggian_air ORDER BY waktu DESC LIMIT 1");
echo json_encode($result->fetch_assoc());
$conn->close();
?>
```

### `get_all.php` — Semua data untuk grafik/tabel
```php
<?php
header("Content-Type: application/json");
$conn  = new mysqli("localhost", "root", "", "db_monitoring_air");
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 50;
$result = $conn->query(
    "SELECT * FROM tb_ketinggian_air ORDER BY waktu DESC LIMIT $limit"
);
$data = [];
while ($row = $result->fetch_assoc()) $data[] = $row;
echo json_encode(array_reverse($data));
$conn->close();
?>
```

---

## 11. Opsional — Real-Time via MQTT over WebSocket

Jika ingin web langsung menerima data **tanpa polling**, browser bisa subscribe langsung ke broker via WebSocket:

```javascript
// Tambahkan mqtt.js via CDN di HTML
// <script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>

const client = mqtt.connect('ws://192.168.x.x:9001'); // port WS Mosquitto

client.on('connect', () => {
  client.subscribe('tandon/ketinggian');
  console.log('Terhubung ke MQTT Broker via WebSocket');
});

client.on('message', (topic, message) => {
  const data = JSON.parse(message.toString());
  document.getElementById('tinggi').innerText  = data.tinggi_air + ' cm';
  document.getElementById('status').innerText  = data.status;
  document.getElementById('persen').innerText  = data.persen + '%';
  updateGrafik(data); // fungsi update Chart.js
});
```

**Aktifkan WebSocket di `mosquitto.conf`:**
```
listener 1883
listener 9001
protocol websockets
allow_anonymous true
```

---

## 12. Analisis Kelemahan Sistem Lama (PIECES)

| Aspek | Masalah Lama | Solusi Sistem Baru |
|---|---|---|
| **Performance** | Monitoring manual, tidak bisa setiap saat | Real-time otomatis via IoT + MQTT |
| **Information** | Informasi sering terlambat | Data akurat & otomatis via sensor |
| **Economy** | Boros waktu & tenaga | Sistem otomatis, minim intervensi |
| **Control** | Tidak ada pencatatan data | Data tersimpan di database MySQL |
| **Efficiency** | Pengecekan manual berulang | Otomatisasi dengan sensor ultrasonik |
| **Service** | Harus di lokasi tandon | Akses dari mana saja via web |

---

## 13. Ruang Lingkup & Batasan Sistem

### ✅ Yang Dikerjakan
- Monitoring ketinggian air tandon rumah tangga
- Komunikasi ESP → Server via protokol MQTT
- Tampilan web real-time
- Penyimpanan data historis (database MySQL)

### ❌ Di Luar Scope
- Kontrol otomatis pompa air
- Aplikasi mobile (Android/iOS)
- Kualitas air (pH, suhu, kekeruhan)
- Smart home automation menyeluruh
- Analisis stabilitas jaringan internet

---

## 14. Referensi Penelitian Terdahulu

| No | Peneliti & Tahun | Teknologi | Kelemahan |
|---|---|---|---|
| 1 | Rahman dkk. (2022) | IoT, NodeMCU, Web | Akurasi sensor belum dianalisis |
| 2 | Hidayat & Nugroho (2021) | NodeMCU, Web | Delay tinggi, tanpa notifikasi |
| 3 | Pratama dkk. (2021) | Sensor Ultrasonik | Belum berbasis IoT |
| 4 | Sari & Wijaya (2020) | Monitoring pompa air | Tidak berbasis web |
| 5 | Putra & Kurniawan (2023) | IoT + Web | Stabilitas sistem belum diuji |

---

## 15. Prompt untuk AI Agent (Contoh)

```
Kamu adalah AI assistant untuk sistem monitoring ketinggian air tandon rumah tangga.
Sistem ini menggunakan sensor ultrasonik HC-SR04 dan NodeMCU ESP8266 yang mengirim data 
via protokol MQTT ke broker Mosquitto, lalu disimpan ke database MySQL setiap 5 detik.

Data yang tersedia:
- tinggi_air : nilai ketinggian air dalam cm (float)
- status     : "Penuh" | "Sedang" | "Rendah"
- persen     : persentase pengisian tandon (integer)
- waktu      : timestamp pengiriman data

Tugasmu:
1. Jawab pertanyaan pengguna tentang kondisi air saat ini
2. Beri peringatan jika status "Rendah" — rekomendasikan segera mengisi
3. Analisis tren ketinggian air dari data historis
4. Rekomendasikan waktu pengisian berdasarkan pola konsumsi air

Gunakan bahasa Indonesia yang ramah dan mudah dipahami pengguna rumah tangga biasa.
```

---

*Dokumen ini dibuat berdasarkan Skripsi: "Rancang Bangun Sistem Pemantauan Ketinggian Air Tandon Rumah Tangga Berbasis IoT dan Web Secara Real-Time" — Muhammad Adityawarman Hidayat, UNDHA-AUB Surakarta, 2026.*
#!/usr/bin/env python3
"""
MQTT Subscriber — Monitoring Ketinggian Air Tandon
Subscribe ke topic MQTT, simpan data ke MySQL (tb_ketinggian_air).
"""

import json
import logging
import signal
import sys
import mysql.connector
import paho.mqtt.client as mqtt

# ── Konfigurasi ──────────────────────────────────────────
MQTT_BROKER   = "127.0.0.1"
MQTT_PORT     = 1883
MQTT_TOPIC    = "tandon/ketinggian"
MQTT_CLIENT   = "subscriber-ukurair"

DB_HOST       = "127.0.0.1"
DB_PORT       = 3306
DB_USER       = "root"
DB_PASSWORD   = ""
DB_NAME       = "ukurair"

# ── Logging ──────────────────────────────────────────────
logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s [%(levelname)s] %(message)s",
    handlers=[
        logging.StreamHandler(sys.stdout),
        logging.FileHandler("/www/wwwroot/UkurAir/mqtt/subscriber.log"),
    ],
)
log = logging.getLogger("mqtt-subscriber")

# ── Database ─────────────────────────────────────────────
def get_db():
    return mysql.connector.connect(
        host=DB_HOST,
        port=DB_PORT,
        user=DB_USER,
        password=DB_PASSWORD,
        database=DB_NAME,
    )

db = get_db()

def save_to_db(tinggi_air: float, status: str):
    global db
    try:
        if not db.is_connected():
            db = get_db()
            log.info("Reconnected to MySQL")
        cursor = db.cursor()
        cursor.execute(
            "INSERT INTO tb_ketinggian_air (tinggi_air, status, created_at, updated_at) VALUES (%s, %s, NOW(), NOW())",
            (tinggi_air, status),
        )
        db.commit()
        log.info(f"Tersimpan → {tinggi_air} cm | {status}")
    except Exception as e:
        log.error(f"DB Error: {e}")
        try:
            db = get_db()
        except:
            pass

# ── MQTT Callbacks ───────────────────────────────────────
def on_connect(client, userdata, flags, rc, properties=None):
    if rc == 0:
        log.info(f"Terhubung ke MQTT Broker ({MQTT_BROKER}:{MQTT_PORT})")
        client.subscribe(MQTT_TOPIC)
        log.info(f"Subscribe topic: {MQTT_TOPIC}")
    else:
        log.error(f"Gagal connect, rc={rc}")

def on_message(client, userdata, msg):
    try:
        payload = json.loads(msg.payload.decode())
        tinggi  = float(payload.get("tinggi_air", 0))
        status  = payload.get("status", "")

        if not status:
            persen = (tinggi / 100.0) * 100
            if persen >= 80:
                status = "Penuh"
            elif persen >= 30:
                status = "Sedang"
            else:
                status = "Rendah"

        save_to_db(tinggi, status)
    except json.JSONDecodeError:
        log.warning(f"Payload bukan JSON: {msg.payload}")
    except Exception as e:
        log.error(f"Error: {e}")

def on_disconnect(client, userdata, rc, properties=None):
    log.warning(f"Terputus dari broker (rc={rc}), mencoba reconnect...")

# ── Main ─────────────────────────────────────────────────
def main():
    signal.signal(signal.SIGINT, lambda s, f: sys.exit(0))
    signal.signal(signal.SIGTERM, lambda s, f: sys.exit(0))

    client = mqtt.Client(mqtt.CallbackAPIVersion.VERSION2, client_id=MQTT_CLIENT)
    client.on_connect    = on_connect
    client.on_message    = on_message
    client.on_disconnect = on_disconnect

    log.info(f"Connecting ke {MQTT_BROKER}:{MQTT_PORT} ...")
    client.connect(MQTT_BROKER, MQTT_PORT, 60)
    client.loop_forever()

if __name__ == "__main__":
    main()

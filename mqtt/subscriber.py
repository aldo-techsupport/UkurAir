#!/usr/bin/env python3
"""
MQTT Subscriber — Monitoring Ketinggian Air Tandon
Subscribe ke topic MQTT, simpan data ke MySQL (tb_ketinggian_air).
"""

import json
import logging
import os
import signal
import sys
from pathlib import Path

import mysql.connector
import paho.mqtt.client as mqtt

# ── Load .env ────────────────────────────────────────────
def _load_env(path: Path):
    if not path.exists():
        return
    for line in path.read_text().splitlines():
        line = line.strip()
        if not line or line.startswith("#") or "=" not in line:
            continue
        key, _, value = line.partition("=")
        key = key.strip()
        value = value.strip().strip('"').strip("'")
        os.environ.setdefault(key, value)

_load_env(Path(__file__).resolve().parent.parent / ".env")

# ── Konfigurasi dari ENV ─────────────────────────────────
MQTT_BROKER   = os.getenv("MQTT_BROKER", "127.0.0.1")
MQTT_PORT     = int(os.getenv("MQTT_PORT", "1883"))
MQTT_TOPIC    = os.getenv("MQTT_TOPIC", "tandon/ketinggian")
MQTT_CLIENT   = os.getenv("MQTT_CLIENT_ID", "subscriber-ukurair")
MQTT_USERNAME = os.getenv("MQTT_USERNAME", "")
MQTT_PASSWORD = os.getenv("MQTT_PASSWORD", "")

DB_HOST       = os.getenv("DB_HOST", "127.0.0.1")
DB_PORT       = int(os.getenv("DB_PORT", "3306"))
DB_USER       = os.getenv("DB_USERNAME", "root")
DB_PASSWORD   = os.getenv("DB_PASSWORD", "")
DB_NAME       = os.getenv("DB_DATABASE", "ukurair")

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

    if MQTT_USERNAME:
        client.username_pw_set(MQTT_USERNAME, MQTT_PASSWORD or None)

    log.info(f"Connecting ke {MQTT_BROKER}:{MQTT_PORT} ...")
    client.connect(MQTT_BROKER, MQTT_PORT, 60)
    client.loop_forever()

if __name__ == "__main__":
    main()

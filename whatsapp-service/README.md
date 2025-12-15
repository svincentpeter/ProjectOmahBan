# WhatsApp Baileys Service

Layanan Node.js untuk mengirim notifikasi WhatsApp menggunakan Baileys.

## Instalasi

```bash
cd whatsapp-service
npm install
```

## Konfigurasi

Copy `.env.example` ke `.env` dan sesuaikan:

```bash
cp .env.example .env
```

Edit `.env`:

-   `PORT`: Port server (default: 3001)
-   `API_KEY`: API key untuk keamanan
-   `OWNER_PHONE`: Nomor WhatsApp owner (format: 6282227863969)

## Menjalankan

```bash
# Development
npm run dev

# Production
npm start
```

## Cara Pakai

1. **Jalankan service**: `npm start`
2. **Buka browser**: http://localhost:3001/qr/view
3. **Scan QR code** dengan WhatsApp di HP Anda
4. **Selesai!** WhatsApp bot siap mengirim notifikasi

## API Endpoints

| Method | Endpoint        | Deskripsi          |
| ------ | --------------- | ------------------ |
| GET    | `/health`       | Health check       |
| GET    | `/status`       | Status koneksi     |
| GET    | `/qr`           | QR code (JSON)     |
| GET    | `/qr/view`      | QR code (HTML)     |
| POST   | `/send`         | Kirim pesan        |
| POST   | `/send-bulk`    | Kirim banyak pesan |
| POST   | `/notify-owner` | Kirim ke owner     |
| POST   | `/disconnect`   | Disconnect         |
| POST   | `/reconnect`    | Reconnect          |

## Contoh Request

### Kirim Pesan

```bash
curl -X POST http://localhost:3001/send \
  -H "Content-Type: application/json" \
  -H "X-API-Key: omahban-wa-secret-2024" \
  -d '{"phone": "6282227863969", "message": "Halo dari POS!"}'
```

### Kirim ke Owner

```bash
curl -X POST http://localhost:3001/notify-owner \
  -H "Content-Type: application/json" \
  -H "X-API-Key: omahban-wa-secret-2024" \
  -d '{"title": "Alert Stok", "message": "Stok ban rendah!"}'
```

## Session

Session WhatsApp disimpan di folder `sessions/`. Jika ingin login ulang, hapus folder ini.

## Troubleshooting

### QR tidak muncul

-   Pastikan tidak ada session lama. Hapus folder `sessions/`
-   Restart service: `npm start`

### Message gagal terkirim

-   Cek status koneksi: `GET /status`
-   Pastikan nomor valid (format 62xxx)
-   WhatsApp mungkin rate limit, tunggu beberapa menit

### Session expired

-   Hapus folder `sessions/`
-   Restart dan scan ulang QR

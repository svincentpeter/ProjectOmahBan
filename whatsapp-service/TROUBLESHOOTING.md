# Troubleshooting WhatsApp Connection

## ✅ Status Terkini

Service telah di-restart dengan **session bersih**. QR code baru sudah di-generate.

---

## 🔧 Yang Sudah Dilakukan

1. ✅ Service di-stop
2. ✅ Session lama dihapus
3. ✅ Service di-restart dengan QR baru
4. ✅ QR code tersedia di: http://localhost:3001/qr/view

---

## 📱 Cara Scan yang Benar

### Step-by-Step:

1. **Buka WhatsApp di HP Anda**

2. **Buka Menu Linked Devices**:

    - **Android**: Tap ⋮ (titik tiga) di pojok kanan atas → **Linked Devices**
    - **iPhone**: Tap **Settings** (gear icon) → **Linked Devices**

3. **Link a Device**:

    - Tap tombol hijau **"Link a Device"**
    - WhatsApp akan membuka kamera

4. **Scan QR Code**:

    - Buka browser: **http://localhost:3001/qr/view**
    - **PENTING**: QR harus terlihat **JELAS dan BESAR** di layar
    - Arahkan kamera HP ke QR code di layar komputer
    - Jaga jarak 20-30cm dari layar

5. **Tunggu Konfirmasi**:
    - Jika berhasil, akan muncul "Link a Device" dengan nama device
    - Tap **"Link"**
    - Anda akan menerima pesan: "🟢 WhatsApp Bot Connected"

---

## ⚠️ Penyebab Umum Gagal

| Masalah                   | Solusi                                                |
| ------------------------- | ----------------------------------------------------- |
| **QR terlalu kecil**      | Zoom browser (Ctrl + +) atau fullscreen (F11)         |
| **QR blur / tidak jelas** | Refresh halaman (F5)                                  |
| **Expired**               | QR hanya berlaku 20 detik, refresh jika expired       |
| **Internet lambat**       | Cek koneksi, Baileys butuh internet stabil            |
| **WhatsApp versi lama**   | Update WhatsApp ke versi terbaru                      |
| **Nomor WhatsApp baru**   | WhatsApp baru (<24 jam) kadang tidak bisa link device |

---

## 🔄 Jika Masih Gagal

### Option 1: Restart Complete

```powershell
# 1. Stop service (Ctrl+C di terminal whatsapp-service)

# 2. Hapus semua session
cd c:\laragon\www\ProjectOmahBan\whatsapp-service
Remove-Item -Recurse -Force sessions

# 3. Restart service
npm start

# 4. Buka QR: http://localhost:3001/qr/view

# 5. Scan ulang
```

### Option 2: Ganti Nomor WhatsApp

Jika nomor Anda pernah di-ban atau sering gagal link:

-   Gunakan nomor WhatsApp **lain**
-   Nomor harus **aktif** dan **verified**

### Option 3: Cek Log Error

```powershell
# Lihat error di terminal whatsapp-service
# Cari pesan seperti:
#   - "Connection closed"
#   - "QR timeout"
#   - "Authentication failed"
```

---

## 🧪 Test Koneksi

### Cek Status Service

```powershell
# Via browser
http://localhost:3001/status

# Atau via PowerShell
curl http://localhost:3001/status
```

Output yang benar:

```json
{
    "success": true,
    "status": "waiting_qr",
    "connected": false,
    "qrAvailable": true
}
```

---

## 💡 Tips Agar Berhasil

1. **Gunakan Chrome/Edge** - Firefox kadang ada masalah render QR
2. **Zoom QR** - QR besar lebih mudah di-scan (Ctrl + +)
3. **Cahaya cukup** - Layar komputer harus terang
4. **Jarak ideal** - 20-30cm antara kamera HP dan layar
5. **Steady** - Pegang HP dengan stabil saat scan
6. **Internet stabil** - Pastikan komputer dan HP online
7. **WhatsApp update** - Gunakan versi terbaru

---

## 📞 Alternatif Jika Tetap Gagal

Jika setelah 3-5 kali percobaan masih gagal, kemungkinan:

1. **Nomor WhatsApp bermasalah**:

    - Coba nomor WhatsApp lain
    - Nomor harus aktif minimal 1 hari

2. **WhatsApp Web limit**:

    - Disconnect device lain terlebih dahulu
    - Max 4 linked devices

3. **Network firewall**:
    - Disable antivirus sementara
    - Cek Windows Firewall tidak block port 3001

---

## ✅ Setelah Berhasil Connect

Anda akan menerima pesan otomatis:

```
🟢 WhatsApp Bot Connected

Bot Omah Ban POS sudah terhubung dan siap mengirim notifikasi.
```

Kemudian test kirim pesan:

```bash
php artisan stock:check-low --notify
```

---

## 🆘 Need Help?

Jika masih gagal, screenshot:

1. Terminal whatsapp-service (semua log)
2. Halaman QR code di browser
3. Error message di WhatsApp HP

Dan saya akan bantu troubleshoot lebih lanjut.

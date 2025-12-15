# 🚀 Quick Start - WhatsApp Connection

## ✅ Service Status: READY

QR Code baru sudah di-generate dan siap di-scan!

---

## 📱 CARA SCAN - Step by Step

### 1️⃣ Buka QR Code di Browser

**Gunakan Chrome atau Edge**, buka:

```
http://localhost:3001/qr/view
```

### 2️⃣ Perbesar QR Code

-   Tekan `Ctrl` + `+` beberapa kali hingga QR **BESAR** dan **JELAS**
-   Atau tekan `F11` untuk fullscreen

### 3️⃣ Buka WhatsApp di HP

**Android:**

-   Tap ⋮ (titik tiga pojok kanan) → **Linked Devices**

**iPhone:**

-   Tap ⚙️ (Settings) → **Linked Devices**

### 4️⃣ Link a Device

-   Tap tombol hijau **"Link a Device"**
-   WhatsApp akan buka kamera

### 5️⃣ Scan QR Code

**PENTING - Teknik Scan yang Benar:**

✅ **DO:**

-   Pegang HP dengan **2 tangan** (stabil)
-   Jarak **20-30 cm** dari layar
-   Cahaya **cukup terang**
-   Layar komputer **brightness maksimal**
-   **Tunggu** kamera fokus (2-3 detik)

❌ **DON'T:**

-   Jangan terlalu dekat (<10cm)
-   Jangan goyang-goyang HP
-   Jangan di ruangan gelap
-   Jangan buru-buru

### 6️⃣ Konfirmasi

-   Jika berhasil, muncul nama device
-   Tap **"Link"**
-   **Tunggu** 5-10 detik
-   Anda akan terima pesan: "🟢 WhatsApp Bot Connected"

---

## ⚠️ Jika QR Expired (20 detik)

QR code hanya berlaku 20 detik. Jika muncul "Generating QR Code...":

1. **Refresh browser** (F5)
2. Tunggu QR muncul (3-5 detik)
3. **Langsung scan** - jangan tunda!

---

## 🔧 Masih Gagal? Coba Ini:

### Problem: QR Tidak Terbaca

**Solusi:**

```
1. Bersihkan kamera HP
2. Copot case HP jika tebal
3. Matikan lampu flash
4. Screenshot QR, kirim ke HP, buka di galeri dan scan
```

### Problem: "Unable to Link"

**Solusi:**

```
1. Update WhatsApp ke versi terbaru
2. Restart WhatsApp di HP
3. Cek internet - HP dan komputer harus online
4. Disconnect device lain jika sudah 4 device
```

### Problem: "Connection Failed"

**Solusi:**

```
1. Cek Windows Firewall
2. Disable antivirus sementara
3. Pastikan tidak pakai VPN
```

---

## 🆘 Last Resort - Factory Reset

Jika sudah 5+ kali gagal:

```powershell
# Stop service (Ctrl+C)

# Clear everything
cd c:\laragon\www\ProjectOmahBan\whatsapp-service
Remove-Item -Recurse -Force sessions
Remove-Item -Recurse -Force node_modules

# Reinstall
npm install

# Restart
npm start
```

---

## ✅ Verification

Setelah berhasil connect, test dengan:

```bash
# Via browser
http://localhost:3001/status
# Should show: "connected": true

# Via Laravel
php artisan stock:check-low --notify
```

---

## 🎯 Success Indicator

Jika berhasil, di terminal whatsapp-service akan muncul:

```
✅ WhatsApp connected successfully!
```

Dan Anda terima pesan WhatsApp:

```
🟢 WhatsApp Bot Connected

Bot Omah Ban POS sudah terhubung dan siap mengirim notifikasi.
```

---

**🔗 QR Code Page:** http://localhost:3001/qr/view

**Selamat mencoba! Jika berhasil, langsung kabari saya!** 🚀

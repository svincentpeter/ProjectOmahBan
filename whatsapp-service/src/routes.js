/**
 * Express Routes for WhatsApp Service
 */

const express = require("express");
const router = express.Router();
const {
    sendMessage,
    sendBulkMessages,
    getConnectionStatus,
    getQrCode,
    disconnect,
    reconnect,
} = require("./whatsapp");

/**
 * Health check
 */
router.get("/health", (req, res) => {
    res.json({
        success: true,
        service: "whatsapp-service",
        timestamp: new Date().toISOString(),
    });
});

/**
 * Get connection status
 */
router.get("/status", (req, res) => {
    const status = getConnectionStatus();
    res.json({ success: true, ...status });
});

/**
 * Get QR code for authentication
 */
router.get("/qr", (req, res) => {
    const qrCode = getQrCode();
    const status = getConnectionStatus();

    if (status.connected) {
        return res.json({
            success: true,
            message: "Already connected",
            connected: true,
        });
    }

    if (!qrCode) {
        return res.json({
            success: false,
            message: "QR code not available. Wait a moment or check status.",
            connected: false,
        });
    }

    res.json({
        success: true,
        qrCode,
        message: "Scan this QR code with WhatsApp",
    });
});

/**
 * Get QR code as HTML page (for easy scanning)
 */
router.get("/qr/view", (req, res) => {
    const qrCode = getQrCode();
    const status = getConnectionStatus();

    if (status.connected) {
        return res.send(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>WhatsApp Connected</title>
                <meta http-equiv="refresh" content="5">
                <style>
                    body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; background: #f0f2f5; }
                    .container { text-align: center; padding: 40px; background: white; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
                    .success { color: #25D366; font-size: 64px; }
                    h1 { color: #333; margin-top: 20px; }
                    p { color: #666; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="success">✅</div>
                    <h1>WhatsApp Connected!</h1>
                    <p>Bot sudah terhubung dan siap mengirim notifikasi.</p>
                    <p style="color: #999; font-size: 12px;">Page will refresh automatically...</p>
                </div>
            </body>
            </html>
        `);
    }

    if (!qrCode) {
        return res.send(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>WhatsApp QR Code</title>
                <meta http-equiv="refresh" content="3">
                <style>
                    body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; background: #f0f2f5; }
                    .container { text-align: center; padding: 40px; background: white; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
                    .loading { color: #128C7E; font-size: 48px; animation: pulse 1s infinite; }
                    @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
                    h1 { color: #333; margin-top: 20px; }
                    p { color: #666; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="loading">⏳</div>
                    <h1>Generating QR Code...</h1>
                    <p>Mohon tunggu sebentar, halaman akan refresh otomatis.</p>
                </div>
            </body>
            </html>
        `);
    }

    res.send(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Scan WhatsApp QR Code</title>
            <meta http-equiv="refresh" content="30">
            <style>
                body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; background: #f0f2f5; }
                .container { text-align: center; padding: 40px; background: white; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
                img { max-width: 300px; border-radius: 8px; }
                h1 { color: #333; margin-bottom: 10px; }
                p { color: #666; margin-bottom: 20px; }
                .instructions { background: #e3f2fd; padding: 15px; border-radius: 8px; margin-top: 20px; text-align: left; }
                .instructions li { margin: 5px 0; color: #1976d2; }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>📱 Scan QR Code</h1>
                <p>Buka WhatsApp > Menu > Linked Devices > Link a Device</p>
                <img src="${qrCode}" alt="WhatsApp QR Code" />
                <div class="instructions">
                    <strong>Cara Scan:</strong>
                    <ol>
                        <li>Buka WhatsApp di HP Anda</li>
                        <li>Tap ⋮ (menu) > Linked Devices</li>
                        <li>Tap "Link a Device"</li>
                        <li>Arahkan kamera ke QR code di atas</li>
                    </ol>
                </div>
                <p style="color: #999; font-size: 12px; margin-top: 15px;">QR akan refresh otomatis setiap 30 detik</p>
            </div>
        </body>
        </html>
    `);
});

/**
 * Send single message
 */
router.post("/send", async (req, res) => {
    try {
        const { phone, message } = req.body;

        if (!phone || !message) {
            return res.status(400).json({
                success: false,
                error: "Phone and message are required",
            });
        }

        const result = await sendMessage(phone, message);
        res.json(result);
    } catch (error) {
        res.status(500).json({
            success: false,
            error: error.message,
        });
    }
});

/**
 * Send bulk messages
 */
router.post("/send-bulk", async (req, res) => {
    try {
        const { messages } = req.body;

        if (!Array.isArray(messages) || messages.length === 0) {
            return res.status(400).json({
                success: false,
                error: "Messages array is required",
            });
        }

        const results = await sendBulkMessages(messages);
        res.json({
            success: true,
            results,
            total: messages.length,
            sent: results.filter((r) => r.success).length,
        });
    } catch (error) {
        res.status(500).json({
            success: false,
            error: error.message,
        });
    }
});

/**
 * Send notification to owner
 */
router.post("/notify-owner", async (req, res) => {
    try {
        const { message, title } = req.body;
        const ownerPhone = process.env.OWNER_PHONE;

        if (!ownerPhone) {
            return res.status(400).json({
                success: false,
                error: "OWNER_PHONE not configured",
            });
        }

        if (!message) {
            return res.status(400).json({
                success: false,
                error: "Message is required",
            });
        }

        const fullMessage = title ? `*${title}*\n\n${message}` : message;
        const result = await sendMessage(ownerPhone, fullMessage);
        res.json(result);
    } catch (error) {
        res.status(500).json({
            success: false,
            error: error.message,
        });
    }
});

/**
 * Disconnect WhatsApp
 */
router.post("/disconnect", async (req, res) => {
    try {
        await disconnect();
        res.json({ success: true, message: "Disconnected" });
    } catch (error) {
        res.status(500).json({
            success: false,
            error: error.message,
        });
    }
});

/**
 * Reconnect WhatsApp
 */
router.post("/reconnect", async (req, res) => {
    try {
        await reconnect();
        res.json({ success: true, message: "Reconnecting..." });
    } catch (error) {
        res.status(500).json({
            success: false,
            error: error.message,
        });
    }
});

module.exports = router;

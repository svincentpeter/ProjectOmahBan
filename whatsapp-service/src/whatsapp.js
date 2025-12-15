/**
 * WhatsApp Baileys Client Handler
 *
 * Mengelola koneksi WhatsApp, session, dan pengiriman pesan
 */

const {
    makeWASocket,
    useMultiFileAuthState,
    DisconnectReason,
    fetchLatestBaileysVersion,
} = require("@whiskeysockets/baileys");
const pino = require("pino");
const path = require("path");
const fs = require("fs");
const qrcode = require("qrcode");
const qrcodeTerminal = require("qrcode-terminal");

// State
let sock = null;
let qrCodeData = null;
let connectionStatus = "disconnected";
let lastError = null;
let retryCount = 0;
const MAX_RETRIES = 5;

const SESSION_DIR = path.join(
    __dirname,
    "..",
    "sessions",
    process.env.SESSION_NAME || "default"
);

// Ensure session directory exists
if (!fs.existsSync(SESSION_DIR)) {
    fs.mkdirSync(SESSION_DIR, { recursive: true });
}

const logger = pino({
    level: process.env.LOG_LEVEL || "info",
});

/**
 * Initialize WhatsApp connection
 */
async function initWhatsApp() {
    try {
        const { state, saveCreds } = await useMultiFileAuthState(SESSION_DIR);
        const { version } = await fetchLatestBaileysVersion();

        sock = makeWASocket({
            version,
            auth: state,
            printQRInTerminal: true,
            logger: pino({ level: "silent" }), // Suppress baileys logs
            browser: ["Omah Ban POS", "Chrome", "120.0.0"],
            connectTimeoutMs: 60000,
            defaultQueryTimeoutMs: 60000,
            keepAliveIntervalMs: 30000,
            markOnlineOnConnect: true,
        });

        // Connection update handler
        sock.ev.on("connection.update", async (update) => {
            const { connection, lastDisconnect, qr } = update;

            if (qr) {
                // Generate QR code
                qrCodeData = await qrcode.toDataURL(qr);
                connectionStatus = "waiting_qr";
                logger.info("📱 QR Code generated - scan with WhatsApp");
                qrcodeTerminal.generate(qr, { small: true });
            }

            if (connection === "close") {
                const statusCode = lastDisconnect?.error?.output?.statusCode;
                const shouldReconnect =
                    statusCode !== DisconnectReason.loggedOut;

                lastError =
                    lastDisconnect?.error?.message || "Connection closed";
                connectionStatus = "disconnected";

                logger.warn(
                    { statusCode, error: lastError },
                    "Connection closed"
                );

                if (shouldReconnect && retryCount < MAX_RETRIES) {
                    retryCount++;
                    logger.info(
                        `Reconnecting... (attempt ${retryCount}/${MAX_RETRIES})`
                    );
                    setTimeout(initWhatsApp, 3000);
                } else if (statusCode === DisconnectReason.loggedOut) {
                    // Clear session on logout
                    logger.info("Logged out, clearing session...");
                    if (fs.existsSync(SESSION_DIR)) {
                        fs.rmSync(SESSION_DIR, {
                            recursive: true,
                            force: true,
                        });
                        fs.mkdirSync(SESSION_DIR, { recursive: true });
                    }
                    qrCodeData = null;
                    setTimeout(initWhatsApp, 1000);
                }
            }

            if (connection === "open") {
                connectionStatus = "connected";
                qrCodeData = null;
                retryCount = 0;
                lastError = null;
                logger.info("✅ WhatsApp connected successfully!");

                // Send startup notification to owner
                const ownerPhone = process.env.OWNER_PHONE;
                if (ownerPhone) {
                    try {
                        await sendMessage(
                            ownerPhone,
                            "🟢 *WhatsApp Bot Connected*\n\nBot Omah Ban POS sudah terhubung dan siap mengirim notifikasi."
                        );
                        logger.info("Startup notification sent to owner");
                    } catch (e) {
                        logger.error(
                            "Failed to send startup notification:",
                            e.message
                        );
                    }
                }
            }
        });

        // Save credentials on update
        sock.ev.on("creds.update", saveCreds);

        // Handle incoming messages (optional - untuk debugging)
        sock.ev.on("messages.upsert", async ({ messages }) => {
            const msg = messages[0];
            if (!msg.key.fromMe && msg.message) {
                const text =
                    msg.message.conversation ||
                    msg.message.extendedTextMessage?.text ||
                    "";
                logger.info(
                    { from: msg.key.remoteJid, text },
                    "Incoming message"
                );
            }
        });
    } catch (error) {
        logger.error({ error: error.message }, "Failed to initialize WhatsApp");
        lastError = error.message;
        connectionStatus = "error";
        throw error;
    }
}

/**
 * Send message to a phone number
 * @param {string} phone - Phone number (62xxx format)
 * @param {string} message - Message text
 * @returns {Promise<object>}
 */
async function sendMessage(phone, message) {
    if (!sock || connectionStatus !== "connected") {
        throw new Error("WhatsApp not connected");
    }

    // Normalize phone number
    let normalizedPhone = phone.replace(/[^0-9]/g, "");

    // Convert 08xx to 628xx
    if (normalizedPhone.startsWith("0")) {
        normalizedPhone = "62" + normalizedPhone.substring(1);
    }

    // Ensure starts with 62
    if (!normalizedPhone.startsWith("62")) {
        normalizedPhone = "62" + normalizedPhone;
    }

    const jid = `${normalizedPhone}@s.whatsapp.net`;

    try {
        const result = await sock.sendMessage(jid, { text: message });
        logger.info(
            { phone: normalizedPhone, messageId: result.key.id },
            "Message sent"
        );
        return {
            success: true,
            messageId: result.key.id,
            phone: normalizedPhone,
        };
    } catch (error) {
        logger.error(
            { phone: normalizedPhone, error: error.message },
            "Failed to send message"
        );
        throw error;
    }
}

/**
 * Send message to multiple recipients
 * @param {Array<{phone: string, message: string}>} messages
 * @returns {Promise<Array>}
 */
async function sendBulkMessages(messages) {
    const results = [];

    for (const { phone, message } of messages) {
        try {
            const result = await sendMessage(phone, message);
            results.push({ phone, success: true, ...result });
            // Small delay between messages to avoid rate limiting
            await new Promise((resolve) => setTimeout(resolve, 1000));
        } catch (error) {
            results.push({ phone, success: false, error: error.message });
        }
    }

    return results;
}

/**
 * Get current connection status
 */
function getConnectionStatus() {
    return {
        status: connectionStatus,
        connected: connectionStatus === "connected",
        qrAvailable: !!qrCodeData,
        lastError,
        retryCount,
    };
}

/**
 * Get QR code as base64 data URL
 */
function getQrCode() {
    return qrCodeData;
}

/**
 * Disconnect and logout
 */
async function disconnect() {
    if (sock) {
        await sock.logout();
        sock = null;
        connectionStatus = "disconnected";
        qrCodeData = null;
    }
}

/**
 * Force reconnect
 */
async function reconnect() {
    if (sock) {
        sock.end();
        sock = null;
    }
    retryCount = 0;
    await initWhatsApp();
}

module.exports = {
    initWhatsApp,
    sendMessage,
    sendBulkMessages,
    getConnectionStatus,
    getQrCode,
    disconnect,
    reconnect,
};

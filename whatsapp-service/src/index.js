/**
 * WhatsApp Baileys Service - Main Entry Point
 *
 * Express server untuk mengelola koneksi WhatsApp via Baileys
 */

require("dotenv").config();

const express = require("express");
const cors = require("cors");
const routes = require("./routes");
const { initWhatsApp, getConnectionStatus } = require("./whatsapp");
const pino = require("pino");

const app = express();
const PORT = process.env.PORT || 3001;

// Logger
const logger = pino({
    level: process.env.LOG_LEVEL || "info",
    transport: {
        target: "pino/file",
        options: { destination: 1 }, // stdout
    },
});

// Middleware
app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Request logging
app.use((req, res, next) => {
    logger.info({ method: req.method, url: req.url }, "Incoming request");
    next();
});

// API Key middleware (skip for health check)
const apiKeyAuth = (req, res, next) => {
    if (req.path === "/health" || req.path === "/status") {
        return next();
    }

    const apiKey = req.headers["x-api-key"] || req.query.api_key;
    const expectedKey = process.env.API_KEY;

    if (!expectedKey) {
        logger.warn("API_KEY not configured, allowing all requests");
        return next();
    }

    if (apiKey !== expectedKey) {
        return res.status(401).json({
            success: false,
            error: "Invalid API key",
        });
    }

    next();
};

app.use(apiKeyAuth);

// Routes
app.use("/", routes);

// Error handler
app.use((err, req, res, next) => {
    logger.error({ err }, "Unhandled error");
    res.status(500).json({
        success: false,
        error: "Internal server error",
    });
});

// Start server
app.listen(PORT, async () => {
    logger.info(`🚀 WhatsApp Service running on port ${PORT}`);
    logger.info(`📱 Owner phone: ${process.env.OWNER_PHONE || "Not set"}`);

    // Initialize WhatsApp connection
    try {
        await initWhatsApp();
        logger.info("✅ WhatsApp initialization started");
    } catch (error) {
        logger.error(
            { error: error.message },
            "❌ Failed to initialize WhatsApp"
        );
    }
});

// Graceful shutdown
process.on("SIGINT", () => {
    logger.info("Shutting down...");
    process.exit(0);
});

process.on("SIGTERM", () => {
    logger.info("Shutting down...");
    process.exit(0);
});

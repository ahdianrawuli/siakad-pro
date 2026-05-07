const express = require('express');
const { Client, LocalAuth } = require('whatsapp-web.js');
const qrcode = require('qrcode');

const app = express();
app.use(express.json());

const API_KEY = process.env.WA_SERVICE_KEY || 'siakad-wa-secret';

let qrDataUrl = null;
let status = 'disconnected'; // disconnected | qr_ready | connected

const client = new Client({
    authStrategy: new LocalAuth({ dataPath: '/app/.wwebjs_auth' }),
    puppeteer: {
        headless: true,
        executablePath: '/usr/bin/chromium',
        args: [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            '--disable-gpu',
            '--single-process'
        ]
    }
});

client.on('qr', async (qr) => {
    status = 'qr_ready';
    qrDataUrl = await qrcode.toDataURL(qr);
    console.log('QR Code generated');
});

client.on('ready', () => {
    status = 'connected';
    qrDataUrl = null;
    console.log('WhatsApp connected!');
});

client.on('disconnected', () => {
    status = 'disconnected';
    qrDataUrl = null;
    console.log('WhatsApp disconnected');
    client.initialize();
});

client.initialize();

// Middleware: API Key check
function auth(req, res, next) {
    if (req.headers['x-api-key'] !== API_KEY) return res.status(401).json({ error: 'Unauthorized' });
    next();
}

// GET /status — cek status & QR
app.get('/status', auth, (req, res) => {
    res.json({ status, qr: qrDataUrl });
});

// POST /send — kirim pesan ke satu nomor
// Body: { number: "628xxx", message: "..." }
app.post('/send', auth, async (req, res) => {
    if (status !== 'connected') return res.status(503).json({ error: 'WhatsApp not connected' });
    const { number, message } = req.body;
    if (!number || !message) return res.status(400).json({ error: 'number and message required' });

    try {
        const chatId = number.replace(/\D/g, '') + '@c.us';
        await client.sendMessage(chatId, message);
        res.json({ success: true });
    } catch (e) {
        res.status(500).json({ error: e.message });
    }
});

// POST /logout — logout WhatsApp
app.post('/logout', auth, async (req, res) => {
    await client.logout();
    status = 'disconnected';
    res.json({ success: true });
});

app.listen(3000, () => console.log('WA Service running on port 3000'));

const express = require("express");
const bodyParser = require("body-parser");
const fs = require("fs");
const axios = require("axios");
const qrcode = require("qrcode");
const { exec } = require('child_process');
const config = require("./config.json");
const { Client, LocalAuth } = require("whatsapp-web.js");
const wwebVersion = '2.2410.1';

process.title = "whatsapp-node-api";
global.client = new Client({
    restartOnAuthFail: true,
    puppeteer: {
	executablePath: '/usr/bin/google-chrome-stable',
        //headless: true,
	headless: 'new',
	args: [
          '--no-sandbox',
         /* '--disable-setuid-sandbox',
          '--disable-dev-shm-usage',
          '--disable-accelerated-2d-canvas',
          '--no-first-run',
          '--no-zygote',
          '--single-process', // <- this one doesn't works in Windows
          '--disable-gpu'*/
        ],
    },
    authStrategy: new LocalAuth({
        clientId: 'satu'
    }),
    webVersionCache: {
        type: 'remote',
        remotePath: `https://raw.githubusercontent.com/wppconnect-team/wa-version/main/html/${wwebVersion}.html`,
    },
});

global.authed = false;

const app = express();

const port = process.env.PORT || config.port;
// Setel Batas Ukuran Permintaan menjadi 50 MB
app.use(bodyParser.json({ limit: "512mb" }));

app.use(express.json());
app.use(bodyParser.urlencoded({ extended: true }));

client.on("qr", (qr) => {
    qrcode.toDataURL(qr, (err, url) => {
        if (err) {
            console.error("Kesalahan dalam membuat kode QR:", err);
            return;
        }
        console.log("Kode QR diterima, menyimpan...");
        fs.writeFileSync('./components/last.qr', url);
    });
});

client.on("authenticated", async () => {
    await console.log("TERAUTENTIKASI!");
    global.authed =await true;
    try {
        //fs.unlinkSync("./components/last.qr");
 	await (fs.rmSync ? fs.rmSync : fs.rmdirSync).call(this, './components/last.qr', { recursive: true, force: true })
    } catch (err) {
        await console.error("Kesalahan menghapus file QR:", err);
    }
});

client.on("auth_failure",async (msg) => {
    await console.error("Autentikasi Gagal!", msg);
    await process.exit(1); // Keluar dari proses dengan kode error
});

client.on("ready", () => {
    console.log("Klien siap!");
});

client.on("message", async (msg) => {
    if (config.webhook.enabled) {
        if (msg.hasMedia) {
            try {
                const attachmentData = await msg.downloadMedia();
                msg.attachmentData = attachmentData;
            } catch (err) {
                console.error("Kesalahan mengunduh media:", err);
            }
        }
        try {
            await axios.post(config.webhook.path, { msg });
        } catch (err) {
            console.error("Kesalahan mengirim webhook:", err);
        }
    }
});

client.on("disconnected", async (reason) => {
     await console.log("Klien terputus", reason);
     await exec('touch api.js',
     (error, stdout, stderr) => {
         console.log(stdout);
         console.log(stderr);
         if (error !== null) {
             console.log(`exec error: ${error}`);
         }
    });
    await exec('touch api.js',
     (error, stdout, stderr) => {
         console.log(stdout);
         console.log(stderr);
         if (error !== null) {
             console.log(`exec error: ${error}`);
         }
    });
    
    
});

client.initialize();

const chatRoute = require("./components/chatting");
const groupRoute = require("./components/group");
const authRoute = require("./components/auth");
const contactRoute = require("./components/contact");

app.use((req, res, next) => {
    console.log(`${req.method} : ${req.path}`);
    next();
});
app.use("/chat", chatRoute);
app.use("/group", groupRoute);
app.use("/auth", authRoute);
app.use("/contact", contactRoute);
app.get('/', (req, res) => {
  res.send('{"server": "Running"}\n');
});
app.listen(port, () => {
    console.log(`Server Berjalan di Port : ${port}`);
});

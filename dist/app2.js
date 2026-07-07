    
    "use strict";

    const express = require("express");
    const bodyParser = require("body-parser");
    const cors = require("cors");
    const { create } = require("@wppconnect-team/wppconnect");

    const app = express();
    const port = 3000;

    // Middleware
    app.use(bodyParser.json());
    app.use(cors());

    // Global WhatsApp client
    let whatsappClient = null;

    // WhatsApp Client'ı başlat
    create()
        .then((client) => {
            console.log("WhatsApp client is ready!");
            whatsappClient = client;
        })
        .catch((err) => {
            console.error("Client oluşturulurken hata oluştu:", err);
        });

    // Mesaj Gönderme Endpoint'i
    app.post("/send-message", async (req, res) => {
        const { number, message } = req.body;

        if (!whatsappClient) {
            return res.status(500).json({ error: "WhatsApp client hazır değil!" });
        }

        if (!number || !message) {
            return res.status(400).json({ error: "Lütfen 'number' ve 'message' bilgilerini sağlayın!" });
        }

        try {
            await whatsappClient.sendText(number, message);
            res.status(200).json({ success: true, message: "Mesaj başarıyla gönderildi!" });
        } catch (error) {
            console.error("Mesaj gönderilirken hata:", error);
            res.status(500).json({ error: "Mesaj gönderilirken hata oluştu." });
        }
    });

    // API Sunucusunu Başlat
    app.listen(port, () => {
      console.log(`API Sunucusu çalışıyor: http://localhost:${port}`);
    });

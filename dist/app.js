    
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });

    const wppconnect_1 = require("@wppconnect-team/wppconnect");
    (0, wppconnect_1.create)()
        .then((client) => {
        console.log('WhatsApp client is ready!');
        client.sendText('905332004780', 'Günaydın, Kalk Kahvaltıyı Hazırla :)')
            .then(() => console.log('Mesaj Gönderildi!'))
            .catch((err) => console.error('Mesaj Gönderirken Hata Oluştu:', err));
    })
        .catch((err) => console.error('Client Hata:', err));
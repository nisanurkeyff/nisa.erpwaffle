
  import { create } from '@wppconnect-team/wppconnect';

  create()
      .then((client) => {
          console.log('WhatsApp client is ready!');
          client.sendText('905515974820', 'Selam :)')
              .then(() => console.log('Mesaj Gönderildi!'))
              .catch((err) => console.error('Mesaj Gönderirken Hata Oluştu:', err));
      })
      .catch((err) => console.error('Client Hata:', err));
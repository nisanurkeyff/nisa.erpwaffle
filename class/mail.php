<?
	require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
	
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;
	use PHPMailer\PHPMailer\Exception;
	
	class Mail {
		
	    public function Gonder($kime = "", $konu = "", $icerik = "", $kimden = "", $cc = "", $firma = ""){

	    	$mail = new PHPMailer(true);
			$mail->isSMTP();
			$mail->SMTPDebug = 0;
			$mail->SMTPAuth = true;
			$mail->Host = 'smtp.turkticaret.net';
			$mail->Port = 587;
			$mail->Username = 'info@ozgurh.com.tr';
			$mail->Password = '4faurJbM4cBJDGG.';
			$mail->SMTPSecure = "tls";
			$mail->SMTPOptions = array(
			    'ssl' => array(
			        'verify_peer' => false,
			        'verify_peer_name' => false,
			        'allow_self_signed' => true
			    )
			);
			$mail->Timeout = 10;
			$mail->SMTPKeepAlive = true;
			$mail->SetLanguage("tr");
			$mail->setFrom($mail->Username, $firma);
			
			if (!is_array($kime)) {
				$kime = str_replace(" ","",$kime);
				$kime = explode(';', str_replace(",",";",$kime));
			}
			
			if (!is_array($cc)) {
				$cc = str_replace(" ","",$cc);
				$cc = explode(';', str_replace(",",";",$cc));
			}
			
			if (!empty($kime)) {
				if (is_array($kime)) {
					foreach ($kime as $k => $email) {
						$email = trim($email);
						if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
							$mail->AddAddress($email);
						}
					}
				} else {
					if (filter_var($kime, FILTER_VALIDATE_EMAIL)) {
						$mail->AddAddress($kime);
					}
				}
			}

			if (!empty($cc)) {
				if (is_array($cc)) {
					foreach ($cc as $c => $email) {
						$email = trim($email);
						if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
							$mail->AddCC($email);
						}
					}
				} else {
					if (filter_var($cc, FILTER_VALIDATE_EMAIL)) {
						$mail->AddCC($cc);
					}
				}
			}
			
			$mail->CharSet = 'UTF-8';
			$mail->Subject = $konu;
			$mail->msgHTML($icerik);
			
			if($mail->send()) {
			    //echo 'Mail gönderildi!';
			    return TRUE;
			} else {
			    return FALSE;
			}
	    	
	    }
	}

?>
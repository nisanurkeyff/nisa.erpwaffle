<?

class ResimYukle {

    private $izinliUzantilar = ['png', 'jpg', 'jpeg'];

    public function fncResimYukle($yol, $files) {
        $result = array();
        if (!is_dir($yol)) {
            mkdir($yol, 0777, true);
        }

        foreach ($files['tmp_name'] as $key => $tmp_name) {
            if (!empty($tmp_name)) {
                $dosyaUzantisi = strtolower(pathinfo($files['name'][$key], PATHINFO_EXTENSION));

                if (!in_array($dosyaUzantisi, $this->izinliUzantilar)) {
                    $result[] = [
                        "HATA"     => TRUE,
                        "DOSYA_AD" => "Geçersiz dosya türü: " . $files['name'][$key]
                    ];
                    continue;
                }

                $resimAdi = $this->fncBase64($dosyaUzantisi);
                $dosyaYolu = $yol . $resimAdi;

                if (move_uploaded_file($tmp_name, $dosyaYolu)) {
                    $this->fncResimBoyut($dosyaYolu, 1280, 1280); // Boyutlandırma işlemi

                    $result[] = [
                        "HATA"          => FALSE,
                        "RESIM_ADI"     => $resimAdi,
                        "RESIM_ADI_ILK" => $files['name'][$key],
                        "DOSYA_YOLU"    => $dosyaYolu
                    ];
                } else {
                    $result[] = [
                        "HATA"     => TRUE,
                        "DOSYA_AD" => "Dosya yüklenemedi: " . $files['name'][$key]
                    ];
                }
            }
        }

        return $result;
    }

    public function fncTekResimYukle($yol, $file) {
        if (!is_dir($yol)) {
            mkdir($yol, 0777, true);
        }

        if (!empty($file['tmp_name'])) {
            $dosyaUzantisi = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if (!in_array($dosyaUzantisi, $this->izinliUzantilar)) {
                return [
                    "HATA"     => TRUE,
                    "DOSYA_AD" => "Geçersiz dosya türü: " . $file['name']
                ];
            }

            $resimAdi = $this->fncBase64($dosyaUzantisi);
            $dosyaYolu = $yol . $resimAdi;

            if (move_uploaded_file($file['tmp_name'], $dosyaYolu)) {
                $this->fncResimBoyut($dosyaYolu, 1280, 1280); // Boyutlandırma işlemi

                return [
                    "HATA"          => FALSE,
                    "RESIM_ADI"     => $resimAdi,
                    "RESIM_ADI_ILK" => $file['name'],
                    "DOSYA_YOLU"    => $dosyaYolu
                ];
            } else {
                return [
                    "HATA"     => TRUE,
                    "DOSYA_AD" => "Dosya yüklenemedi: " . $file['name']
                ];
            }
        }

        return [
            "HATA"     => TRUE,
            "DOSYA_AD" => "Dosya bulunamadı!"
        ];
    }

    private function fncBase64($uzanti) {
        list($usec, $sec) = explode(' ', microtime());
        $benzersiz = str_replace('.', '', (((float)$usec + (float)$sec)));
        return str_pad($benzersiz, 14, "0", STR_PAD_RIGHT) . '.' . $uzanti;
    }

    private function fncResimBoyut($file, $maxWidth, $maxHeight) {
        list($originalWidth, $originalHeight) = getimagesize($file);

        if ($originalWidth <= $maxWidth && $originalHeight <= $maxHeight) {
            return;
        }

        $aspectRatio = $originalWidth / $originalHeight;

        if ($originalWidth > $originalHeight) {
            $newWidth = $maxWidth;
            $newHeight = round($maxWidth / $aspectRatio);
        } else {
            $newHeight = $maxHeight;
            $newWidth = round($maxHeight * $aspectRatio);
        }

        $imageExtension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        switch ($imageExtension) {
            case 'jpeg':
            case 'jpg':
                $source = imagecreatefromjpeg($file);
                $newImage = imagecreatetruecolor($newWidth, $newHeight);
                break;
            case 'png':
                $source = imagecreatefrompng($file);
                $newImage = imagecreatetruecolor($newWidth, $newHeight);
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);
                $transparent = imagecolorallocatealpha($newImage, 0, 0, 0, 127);
                imagefill($newImage, 0, 0, $transparent);
                break;
            default:
                return false;
        }

        imagecopyresampled($newImage, $source, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

        switch ($imageExtension) {
            case 'jpeg':
            case 'jpg':
                imagejpeg($newImage, $file, 80);
                break;
            case 'png':
                imagepng($newImage, $file);
                break;
        }

        imagedestroy($newImage);
        imagedestroy($source);
    }
}

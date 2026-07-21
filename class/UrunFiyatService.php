<?php

class UrunFiyatService {

    public const KANAL_MAGAZA       = 'MAGAZA';
    public const KANAL_TELEFON      = 'TELEFON';
    public const KANAL_DIS_PLATFORM = 'DIS_PLATFORM';

    // Aliases for backwards compatibility
    public const TUR_MAGAZA        = self::KANAL_MAGAZA;
    public const TUR_TELEFON       = self::KANAL_TELEFON;
    public const TUR_DIS_PLATFORM  = self::KANAL_DIS_PLATFORM;

    /**
     * Resolves selling price for a given product and sales channel.
     * Implements explicit NULL vs 0 rules:
     * - NULL (empty/unentered): Falls back to Mağaza price.
     * - 0 / 0.00 (explicitly set 0): Returns 0.00 TL.
     * 
     * @param object|array|int $urun Product DB object, array, or product ID
     * @param string $fiyatTuru Sales channel ('MAGAZA', 'TELEFON', 'DIS_PLATFORM')
     * @return float
     */
    public static function getUrunSatisFiyati($urun, string $fiyatTuru = self::KANAL_MAGAZA): float {
        $urunRow = null;

        if (is_numeric($urun) && intval($urun) > 0) {
            $sql = "SELECT ID, FIYAT_MAGAZA, FIYAT_TELEFON, FIYAT_DIS_PLATFORM FROM URUN WHERE ID = :ID";
            $urunRow = DB::getRow($sql, [':ID' => intval($urun)]);
        } else if (is_object($urun)) {
            $urunRow = $urun;
        } else if (is_array($urun)) {
            $urunRow = (object) $urun;
        }

        if (!$urunRow) {
            return 0.0;
        }

        $normalizedTur = strtoupper(trim($fiyatTuru));
        $magazaPrice = floatval($urunRow->FIYAT_MAGAZA ?? 0);

        switch ($normalizedTur) {
            case self::KANAL_TELEFON:
            case 'TELEFON_SIPARIS':
            case 'TELEFON_SIPARISI':
                if (!isset($urunRow->FIYAT_TELEFON) || $urunRow->FIYAT_TELEFON === null || trim((string)$urunRow->FIYAT_TELEFON) === '') {
                    return $magazaPrice;
                }
                return floatval($urunRow->FIYAT_TELEFON);

            case self::KANAL_DIS_PLATFORM:
            case 'DIS_PLATFORM_SATIS':
            case 'TRENDYOL':
            case 'YEMEKSEPETI':
            case 'GETIR':
                if (!isset($urunRow->FIYAT_DIS_PLATFORM) || $urunRow->FIYAT_DIS_PLATFORM === null || trim((string)$urunRow->FIYAT_DIS_PLATFORM) === '') {
                    return $magazaPrice;
                }
                return floatval($urunRow->FIYAT_DIS_PLATFORM);

            case self::KANAL_MAGAZA:
            case 'MAGAZA_SATIS':
            case 'GENEL':
            default:
                return $magazaPrice;
        }
    }

    /**
     * Normalizes and validates a price input value.
     * - Empty/NULL -> NULL
     * - Turkish format '120,50' -> 120.50
     * - Negative value -> Error
     * 
     * @param mixed $input
     * @return array ['valid' => bool, 'value' => float|null, 'error' => string|null]
     */
    public static function normalizePriceInput($input): array {
        if ($input === null || (is_string($input) && trim($input) === '')) {
            return ['valid' => true, 'value' => null, 'error' => null];
        }

        $str = trim((string)$input);
        if ($str === '') {
            return ['valid' => true, 'value' => null, 'error' => null];
        }

        // Convert Turkish comma decimal separator to dot if needed
        if (strpos($str, ',') !== false) {
            $str = FormatSayi::sayi2db($str);
        }

        if (!is_numeric($str)) {
            return ['valid' => false, 'value' => null, 'error' => 'Geçersiz fiyat formatı!'];
        }

        $val = floatval($str);

        if ($val < 0) {
            return ['valid' => false, 'value' => null, 'error' => 'Negatif fiyat kaydedilemez!'];
        }

        return ['valid' => true, 'value' => round($val, 4), 'error' => null];
    }

    /**
     * Saves multi-channel selling prices with DB Transaction guarantee and Audit Logging.
     * 
     * @param int $urunId
     * @param array $fiyatlar
     * @param int|null $kullaniciId
     * @param string|null $aciklama
     * @param string $kaynak WEB, API, IMPORT, CRON, TOPLU_GUNCELLEME
     * @param string|null $ipAdresi
     * @return array Result array ['HATA' => bool, 'ACIKLAMA' => string]
     */
    public static function saveUrunSatisFiyatlari(
        int $urunId, 
        array $fiyatlar, 
        ?int $kullaniciId = null, 
        ?string $aciklama = null,
        string $kaynak = 'WEB',
        ?string $ipAdresi = null
    ): array {
        if ($urunId <= 0) {
            return ['HATA' => true, 'ACIKLAMA' => 'Geçersiz ürün ID!'];
        }

        if (empty($ipAdresi)) {
            $ipAdresi = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        }

        // Fetch existing DB prices for comparison
        $eskiRow = DB::getRow("SELECT FIYAT_MAGAZA, FIYAT_TELEFON, FIYAT_DIS_PLATFORM FROM URUN WHERE ID = :ID", [':ID' => $urunId]);
        if (!$eskiRow) {
            return ['HATA' => true, 'ACIKLAMA' => 'Ürün veritabanında bulunamadı!'];
        }

        $eskiMagaza = $eskiRow->FIYAT_MAGAZA !== null ? floatval($eskiRow->FIYAT_MAGAZA) : null;
        $eskiTelefon = $eskiRow->FIYAT_TELEFON !== null ? floatval($eskiRow->FIYAT_TELEFON) : null;
        $eskiDis = $eskiRow->FIYAT_DIS_PLATFORM !== null ? floatval($eskiRow->FIYAT_DIS_PLATFORM) : null;

        // Process Magaza Price
        $hasMagazaKey = array_key_exists('fiyat_magaza', $fiyatlar) || array_key_exists(self::KANAL_MAGAZA, $fiyatlar) || array_key_exists('FIYAT_MAGAZA', $fiyatlar);
        if ($hasMagazaKey) {
            $rawMagaza = $fiyatlar['fiyat_magaza'] ?? $fiyatlar[self::KANAL_MAGAZA] ?? $fiyatlar['FIYAT_MAGAZA'] ?? null;
            $normMagaza = self::normalizePriceInput($rawMagaza);
            if (!$normMagaza['valid']) {
                return ['HATA' => true, 'ACIKLAMA' => 'Mağaza Fiyatı: ' . $normMagaza['error']];
            }
            $yeniMagaza = $normMagaza['value'];
        } else {
            $yeniMagaza = $eskiMagaza;
        }

        // Process Telefon Price
        $hasTelefonKey = array_key_exists('fiyat_telefon', $fiyatlar) || array_key_exists(self::KANAL_TELEFON, $fiyatlar) || array_key_exists('FIYAT_TELEFON', $fiyatlar);
        if ($hasTelefonKey) {
            $rawTelefon = $fiyatlar['fiyat_telefon'] ?? $fiyatlar[self::KANAL_TELEFON] ?? $fiyatlar['FIYAT_TELEFON'] ?? null;
            $normTelefon = self::normalizePriceInput($rawTelefon);
            if (!$normTelefon['valid']) {
                return ['HATA' => true, 'ACIKLAMA' => 'Telefon Fiyatı: ' . $normTelefon['error']];
            }
            $yeniTelefon = $normTelefon['value'];
        } else {
            $yeniTelefon = $eskiTelefon;
        }

        // Process Dis Platform Price
        $hasDisKey = array_key_exists('fiyat_dis_platform', $fiyatlar) || array_key_exists(self::KANAL_DIS_PLATFORM, $fiyatlar) || array_key_exists('FIYAT_DIS_PLATFORM', $fiyatlar);
        if ($hasDisKey) {
            $rawDis = $fiyatlar['fiyat_dis_platform'] ?? $fiyatlar[self::KANAL_DIS_PLATFORM] ?? $fiyatlar['FIYAT_DIS_PLATFORM'] ?? null;
            $normDis = self::normalizePriceInput($rawDis);
            if (!$normDis['valid']) {
                return ['HATA' => true, 'ACIKLAMA' => 'Dış Platform Fiyatı: ' . $normDis['error']];
            }
            $yeniDis = $normDis['value'];
        } else {
            $yeniDis = $eskiDis;
        }

        // BEGIN DB TRANSACTION
        DB::beginTransaction();

        try {
            // Update URUN table
            $sql = "UPDATE URUN SET 
                        FIYAT_MAGAZA        = :FIYAT_MAGAZA,
                        FIYAT_TELEFON       = :FIYAT_TELEFON,
                        FIYAT_DIS_PLATFORM  = :FIYAT_DIS_PLATFORM,
                        GTARIH              = NOW()
                    WHERE ID = :ID";

            $binds = [
                ':FIYAT_MAGAZA'       => $yeniMagaza,
                ':FIYAT_TELEFON'      => $yeniTelefon,
                ':FIYAT_DIS_PLATFORM' => $yeniDis,
                ':ID'                 => $urunId
            ];

            DB::exec($sql, $binds);

            // Audit log entries for changed channels
            $channelsToLog = [];
            if ($hasMagazaKey) {
                $channelsToLog[self::KANAL_MAGAZA] = ['eski' => $eskiMagaza, 'yeni' => $yeniMagaza];
            }
            if ($hasTelefonKey) {
                $channelsToLog[self::KANAL_TELEFON] = ['eski' => $eskiTelefon, 'yeni' => $yeniTelefon];
            }
            if ($hasDisKey) {
                $channelsToLog[self::KANAL_DIS_PLATFORM] = ['eski' => $eskiDis, 'yeni' => $yeniDis];
            }

            foreach ($channelsToLog as $kanal => $vals) {
                $oldVal = $vals['eski'];
                $newVal = $vals['yeni'];

                // Check if price changed
                $hasChanged = false;
                if ($oldVal === null && $newVal !== null) {
                    $hasChanged = true;
                } else if ($oldVal !== null && $newVal === null) {
                    $hasChanged = true;
                } else if ($oldVal !== null && $newVal !== null && abs($oldVal - $newVal) > 0.00001) {
                    $hasChanged = true;
                }

                if ($hasChanged) {
                    $logSql = "INSERT INTO URUN_FIYAT_LOG SET 
                                URUN_ID          = :URUN_ID,
                                KANAL            = :KANAL,
                                ESKI_FIYAT       = :ESKI_FIYAT,
                                YENI_FIYAT       = :YENI_FIYAT,
                                KAYNAK           = :KAYNAK,
                                IP_ADRESI        = :IP_ADRESI,
                                KULLANICI_ID     = :KULLANICI_ID,
                                ACIKLAMA         = :ACIKLAMA,
                                OLUSTURMA_TARIHI = NOW()";

                    $logBinds = [
                        ':URUN_ID'      => $urunId,
                        ':KANAL'        => $kanal,
                        ':ESKI_FIYAT'   => $oldVal,
                        ':YENI_FIYAT'   => $newVal,
                        ':KAYNAK'       => $kaynak,
                        ':IP_ADRESI'    => $ipAdresi,
                        ':KULLANICI_ID' => $kullaniciId,
                        ':ACIKLAMA'     => $aciklama
                    ];

                    DB::insert($logSql, $logBinds);
                }
            }

            DB::commit();
            return ['HATA' => false, 'ACIKLAMA' => 'Kayıt Edildi.'];
        } catch (Exception $e) {
            DB::rollBack();
            return ['HATA' => true, 'ACIKLAMA' => 'Veritabanı hatası: ' . $e->getMessage()];
        }
    }
}

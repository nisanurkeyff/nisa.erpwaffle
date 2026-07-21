<?php

class UrunMaliyetService {

    /**
     * Calculates product cost for a single product ID and saves/updates URUN_MALIYET.
     * 
     * @param int $urun_id
     * @return float
     */
    public static function hesaplaUrunMaliyeti($urun_id) {
        $urun_id = intval($urun_id);
        if ($urun_id <= 0) {
            return 0;
        }

        $bugun = date("Y-m-d");

        $sql = "SELECT U.*, HK.KATSAYI, HK.DURUM AS HAMUR_KULLANIM_DURUM 
                FROM URUN AS U 
                LEFT JOIN HAMUR_KULLANIM AS HK ON HK.ID = U.HAMUR_KULLANIM_ID 
                WHERE U.ID = :URUN_ID AND U.DURUM = 1";
        $row_urun = DB::getRow($sql, [':URUN_ID' => $urun_id]);

        if (!$row_urun || !$row_urun->ID) {
            return 0;
        }

        $row_site = DB::getRow("SELECT HAMUR_MALIYET, KRUVASAN_MALIYET FROM SITE LIMIT 1");
        $hamur_maliyet = $row_site ? floatval($row_site->HAMUR_MALIYET) : 0;
        $kruvasan_maliyet = $row_site ? floatval($row_site->KRUVASAN_MALIYET) : 0;

        $toplam_maliyet = 0;

        $sql = "SELECT 
                    UR.*,
                    M.MALZEME,
                    M.FIYAT AS MALZEME_FIYAT,
                    MT.KODU AS MALZEME_TIPI_KODU,
                    B.KODU AS BIRIM_KODU
                FROM URUN_RECETE AS UR
                    LEFT JOIN MALZEME AS M ON M.ID = UR.MALZEME_ID
                    LEFT JOIN MALZEME_TIPI AS MT ON MT.ID = M.MALZEME_TIPI_ID
                    LEFT JOIN BIRIM AS B ON B.ID = M.TEMEL_BIRIM_ID
                WHERE UR.URUN_ID = :URUN_ID";
        $rows_recete = DB::get($sql, [':URUN_ID' => $urun_id]);

        if ($rows_recete) {
            foreach ($rows_recete as $row_recete) {
                // Find latest purchase unit price from MALZEME_ALIS_DETAY
                $sql = "SELECT MAD.BIRIM_FIYAT, MA.FATURA_TARIH 
                        FROM MALZEME_ALIS_DETAY AS MAD
                        LEFT JOIN MALZEME_ALIS AS MA ON MA.ID = MAD.MALZEME_ALIS_ID
                        WHERE MAD.MALZEME_ID = :MALZEME_ID AND MA.DURUM = 1
                        ORDER BY MA.FATURA_TARIH DESC, MAD.ID DESC LIMIT 1";
                $row_alis = DB::getRow($sql, [':MALZEME_ID' => $row_recete->MALZEME_ID]);

                if ($row_alis && $row_alis->BIRIM_FIYAT !== null) {
                    $birim_fiyat = floatval($row_alis->BIRIM_FIYAT);
                } else {
                    // Fallback to material price if no active purchase invoice exists
                    $birim_fiyat = floatval($row_recete->MALZEME_FIYAT);
                }

                if ($row_recete->MALZEME_TIPI_KODU == 'PKG' || $row_recete->BIRIM_KODU == 'ADT') {
                    $birim_maliyet = $birim_fiyat;
                } else {
                    $birim_maliyet = $birim_fiyat / 1000;
                }

                $malzeme_tutar = floatval($row_recete->MIKTAR) * $birim_maliyet;
                $toplam_maliyet += $malzeme_tutar;
            }
        }

        $genel_maliyet = $toplam_maliyet;

        if (in_array($row_urun->KATEGORI_ID, array(6))) { // Kruvasan
            $genel_maliyet = $toplam_maliyet + $kruvasan_maliyet;
        } else if (in_array($row_urun->KATEGORI_ID, array(4))) { // İçecek
            $sql = "SELECT MAD.BIRIM_FIYAT
                    FROM MALZEME_ALIS_DETAY AS MAD
                    LEFT JOIN MALZEME_ALIS AS MA ON MA.ID = MAD.MALZEME_ALIS_ID
                    LEFT JOIN MALZEME AS M ON M.ID = MAD.MALZEME_ID
                    WHERE M.URUN_ID = :URUN_ID AND MA.DURUM = 1
                    ORDER BY MA.FATURA_TARIH DESC, MAD.ID DESC LIMIT 1";
            $row_alis_icecek = DB::getRow($sql, [':URUN_ID' => $urun_id]);

            if ($row_alis_icecek && $row_alis_icecek->BIRIM_FIYAT !== null) {
                $genel_maliyet = floatval($row_alis_icecek->BIRIM_FIYAT);
            } else {
                $sql = "SELECT FIYAT FROM MALZEME WHERE URUN_ID = :URUN_ID LIMIT 1";
                $row_m_icecek = DB::getRow($sql, [':URUN_ID' => $urun_id]);
                if ($row_m_icecek && $row_m_icecek->FIYAT !== null) {
                    $genel_maliyet = floatval($row_m_icecek->FIYAT);
                }
            }
        } else {
            // Dynamic Dough Cost = Tam Hamur Maliyeti * KATSAYI
            $katsayi = ($row_urun->HAMUR_KULLANIM_ID > 0 && $row_urun->HAMUR_KULLANIM_DURUM == 1 && $row_urun->KATSAYI !== null) 
                       ? floatval($row_urun->KATSAYI) 
                       : 1.00;
            $hamur_hesaplanan_maliyet = $hamur_maliyet * $katsayi;
            $genel_maliyet = $toplam_maliyet + $hamur_hesaplanan_maliyet;
        }

        // Save or update in URUN_MALIYET table
        $sql = "SELECT ID FROM URUN_MALIYET WHERE URUN_ID = :URUN_ID AND MALIYET_TARIH = :MALIYET_TARIH";
        $row_kontrol = DB::getRow($sql, [
            ':URUN_ID' => $urun_id,
            ':MALIYET_TARIH' => $bugun
        ]);

        if ($row_kontrol && $row_kontrol->ID > 0) {
            $sql = "UPDATE URUN_MALIYET SET MALIYET = :MALIYET WHERE ID = :ID";
            DB::exec($sql, [
                ':MALIYET' => $genel_maliyet,
                ':ID' => $row_kontrol->ID
            ]);
        } else {
            $sql = "INSERT INTO URUN_MALIYET SET URUN_ID = :URUN_ID, URUN = :URUN, MALIYET_TARIH = :MALIYET_TARIH, MALIYET = :MALIYET";
            DB::insert($sql, [
                ':URUN_ID' => $urun_id,
                ':URUN' => $row_urun->URUN,
                ':MALIYET_TARIH' => $bugun,
                ':MALIYET' => $genel_maliyet
            ]);
        }

        self::invalidateMaliyetCache($urun_id);

        return $genel_maliyet;
    }

    /**
     * Calculates costs only for products that are linked to or use the specified material ID(s).
     * Does NOT loop through all products.
     * 
     * @param int|array $malzeme_ids
     */
    public static function hesaplaMalzemeIleIlgiliUrunMaliyetleri($malzeme_ids) {
        if (!is_array($malzeme_ids)) {
            $malzeme_ids = array($malzeme_ids);
        }
        $malzeme_ids = array_filter(array_map('intval', $malzeme_ids));

        if (empty($malzeme_ids)) {
            return;
        }

        $placeholders = implode(',', array_fill(0, count($malzeme_ids), '?'));

        // 1. Products using these materials in URUN_RECETE
        $sql = "SELECT DISTINCT URUN_ID FROM URUN_RECETE WHERE MALZEME_ID IN ($placeholders)";
        $rows_recete = DB::get($sql, $malzeme_ids);

        // 2. Products associated directly with these materials (e.g. beverages)
        $sql = "SELECT DISTINCT URUN_ID FROM MALZEME WHERE ID IN ($placeholders) AND URUN_ID IS NOT NULL AND URUN_ID > 0";
        $rows_urun_id = DB::get($sql, $malzeme_ids);

        $urun_ids = array();
        if ($rows_recete) {
            foreach ($rows_recete as $r) {
                if ($r->URUN_ID > 0) {
                    $urun_ids[$r->URUN_ID] = true;
                }
            }
        }
        if ($rows_urun_id) {
            foreach ($rows_urun_id as $r) {
                if ($r->URUN_ID > 0) {
                    $urun_ids[$r->URUN_ID] = true;
                }
            }
        }

        foreach (array_keys($urun_ids) as $urun_id) {
            self::hesaplaUrunMaliyeti($urun_id);
        }
    }

    /**
     * Batch recalculates costs for all active products (used by maintenance CLI wrapper / admin tool).
     * 
     * @return int Count of recalculated products
     */
    public static function hesaplaTumUrunMaliyetleri() {
        $rows = DB::get("SELECT ID FROM URUN WHERE DURUM = 1");
        $count = 0;
        if ($rows) {
            foreach ($rows as $row) {
                self::hesaplaUrunMaliyeti($row->ID);
                $count++;
            }
        }
        return $count;
    }

    /**
     * Fetches the latest cost for a single product from URUN_MALIYET table.
     * 
     * @param int $urun_id
     * @return float|null
     */
    public static function getGuncelMaliyet($urun_id) {
        $urun_id = intval($urun_id);
        if ($urun_id <= 0) {
            return null;
        }

        $sql = "SELECT MALIYET FROM URUN_MALIYET WHERE URUN_ID = :URUN_ID ORDER BY MALIYET_TARIH DESC, ID DESC LIMIT 1";
        $row = DB::getRow($sql, [':URUN_ID' => $urun_id]);

        return ($row && $row->MALIYET !== null) ? floatval($row->MALIYET) : null;
    }

    /**
     * Fetches the latest costs for multiple products in a single SQL query (avoids N+1 queries).
     * 
     * @param array $urun_ids Optional list of product IDs. If empty, retrieves latest cost for all products.
     * @return array Map of urun_id => float|null
     */
    public static function getGuncelMaliyetler(array $urun_ids = array()) {
        $urun_ids = array_filter(array_map('intval', $urun_ids));
        
        $where = "";
        $params = array();
        if (!empty($urun_ids)) {
            $placeholders = implode(',', array_fill(0, count($urun_ids), '?'));
            $where = "WHERE URUN_ID IN ($placeholders)";
            $params = array_values($urun_ids);
        }

        $sql = "SELECT UM.URUN_ID, UM.MALIYET 
                FROM URUN_MALIYET AS UM
                INNER JOIN (
                    SELECT URUN_ID, MAX(ID) AS MAX_ID 
                    FROM URUN_MALIYET 
                    $where 
                    GROUP BY URUN_ID
                ) AS LATEST ON UM.ID = LATEST.MAX_ID";

        $rows = DB::get($sql, $params);
        $result = array();

        if ($rows) {
            foreach ($rows as $r) {
                $result[$r->URUN_ID] = ($r->MALIYET !== null) ? floatval($r->MALIYET) : null;
            }
        }

        return $result;
    }

    /**
     * Cache for cost breakdown detailed responses per product ID.
     */
    private static $cache = array();

    /**
     * Returns cached cost breakdown for product ID if available.
     * 
     * @param int $urun_id
     * @return array|null
     */
    public static function getCachedMaliyetDetayi($urun_id) {
        $urun_id = intval($urun_id);
        return isset(self::$cache[$urun_id]) ? self::$cache[$urun_id] : null;
    }

    /**
     * Invalidates cost breakdown cache for a specific product or all products.
     * 
     * @param int|null $urun_id
     */
    public static function invalidateMaliyetCache($urun_id = null) {
        if ($urun_id === null) {
            self::$cache = array();
        } else {
            $urun_id = intval($urun_id);
            unset(self::$cache[$urun_id]);
        }
    }

    /**
     * Cost Breakdown Engine Core Method.
     * Returns full modular cost breakdown for a product in standard UI-independent JSON format with versioning.
     * All financial values returned as pure numeric floats without currency formatting.
     * 
     * @param int $urun_id
     * @param bool $useCache
     * @param array $context Future context options (source, siparis_detay_id, etc.)
     * @return array
     */
    public static function getMaliyetDetayi($urun_id, $useCache = true, $context = array()) {
        $urun_id = intval($urun_id);
        $context = is_array($context) ? $context : array();

        if ($urun_id <= 0) {
            return array(
                "version" => 1,
                "urun_id" => 0,
                "urun_adi" => "",
                "urun_tipi" => "DIGER",
                "urun" => array(
                    "urun_id" => 0,
                    "urun_kodu" => null,
                    "urun_adi" => "",
                    "kategori_id" => 0,
                    "kategori" => null
                ),
                "context" => $context,
                "hamur" => array(),
                "malzemeler" => array(),
                "paketleme" => array(),
                "genel_giderler" => array(),
                "toplam" => array(
                    "hamur_toplami" => 0.00,
                    "malzeme_toplami" => 0.00,
                    "paketleme_toplami" => 0.00,
                    "genel_gider_toplami" => 0.00,
                    "toplam_urun_maliyet" => 0.00
                ),
                "analizler" => array(
                    "komisyon" => null,
                    "karlilik" => null,
                    "indirim_kampanya" => null,
                    "platform_maliyeti" => null
                ),
                "son_guncelleme" => array(
                    "son_hesaplama_tarihi" => null,
                    "son_recete_guncellemesi" => null,
                    "son_hamur_guncellemesi" => null,
                    "son_malzeme_fiyat_guncellemesi" => null
                )
            );
        }

        if ($useCache && empty($context)) {
            $cached = self::getCachedMaliyetDetayi($urun_id);
            if ($cached !== null) {
                return $cached;
            }
        }

        $sql = "SELECT U.*, K.KATEGORI, HK.KATSAYI, HK.HAMUR_KULLANIM, HK.DURUM AS HAMUR_KULLANIM_DURUM 
                FROM URUN AS U 
                LEFT JOIN KATEGORI AS K ON K.ID = U.KATEGORI_ID
                LEFT JOIN HAMUR_KULLANIM AS HK ON HK.ID = U.HAMUR_KULLANIM_ID 
                WHERE U.ID = :URUN_ID";
        $row_urun = DB::getRow($sql, array(':URUN_ID' => $urun_id));

        if (!$row_urun || !$row_urun->ID) {
            return array(
                "version" => 1,
                "urun_id" => $urun_id,
                "urun_adi" => "",
                "urun_tipi" => "DIGER",
                "urun" => array(
                    "urun_id" => $urun_id,
                    "urun_kodu" => null,
                    "urun_adi" => "",
                    "kategori_id" => 0,
                    "kategori" => null
                ),
                "context" => $context,
                "hamur" => array(),
                "malzemeler" => array(),
                "paketleme" => array(),
                "genel_giderler" => array(),
                "toplam" => array(
                    "hamur_toplami" => 0.00,
                    "malzeme_toplami" => 0.00,
                    "paketleme_toplami" => 0.00,
                    "genel_gider_toplami" => 0.00,
                    "toplam_urun_maliyet" => 0.00
                ),
                "analizler" => array(
                    "komisyon" => null,
                    "karlilik" => null,
                    "indirim_kampanya" => null,
                    "platform_maliyeti" => null
                ),
                "son_guncelleme" => array(
                    "son_hesaplama_tarihi" => null,
                    "son_recete_guncellemesi" => null,
                    "son_hamur_guncellemesi" => null,
                    "son_malzeme_fiyat_guncellemesi" => null
                )
            );
        }

        $row_site = DB::getRow("SELECT HAMUR_MALIYET, KRUVASAN_MALIYET, GTARIH FROM SITE LIMIT 1");

        // Determine Product Type Strategy (WAFFLE, KRUVASAN, ICECEK, DIGER)
        $urun_tipi = "WAFFLE";
        if (in_array(intval($row_urun->KATEGORI_ID), array(6))) {
            $urun_tipi = "KRUVASAN";
        } else if (in_array(intval($row_urun->KATEGORI_ID), array(4))) {
            $urun_tipi = "ICECEK";
        } else if (intval($row_urun->HAMUR_KULLANIM_ID) <= 0) {
            $urun_tipi = "DIGER";
        }

        $hamur_data = self::calculateHamurCost($row_urun, $row_site, $urun_tipi);
        $malzemeler_data = self::calculateMalzemeCost($urun_id);
        $paketleme_data = self::calculatePaketlemeCost($urun_id);
        $genel_giderler_data = self::calculateGenelGiderCost($urun_id);

        $toplam_data = self::calculateToplamCost($hamur_data, $malzemeler_data, $paketleme_data, $genel_giderler_data);
        $son_guncelleme_data = self::getLastUpdateMetadata($urun_id, $malzemeler_data['rows'], $row_site);

        $result = array(
            "version" => 1,
            "urun_id" => $urun_id,
            "urun_adi" => $row_urun->URUN,
            "urun_tipi" => $urun_tipi,
            "urun" => array(
                "urun_id" => $urun_id,
                "urun_kodu" => isset($row_urun->KODU) ? $row_urun->KODU : (isset($row_urun->URUN_KODU) ? $row_urun->URUN_KODU : null),
                "urun_adi" => $row_urun->URUN,
                "kategori_id" => intval($row_urun->KATEGORI_ID),
                "kategori" => isset($row_urun->KATEGORI) ? $row_urun->KATEGORI : null
            ),
            "context" => $context,
            "hamur" => $hamur_data['result'],
            "malzemeler" => $malzemeler_data['result'],
            "paketleme" => $paketleme_data['result'],
            "genel_giderler" => $genel_giderler_data['result'],
            "toplam" => $toplam_data,
            "analizler" => array(
                "komisyon" => null,
                "karlilik" => null,
                "indirim_kampanya" => null,
                "platform_maliyeti" => null
            ),
            "son_guncelleme" => $son_guncelleme_data
        );

        if (empty($context)) {
            self::$cache[$urun_id] = $result;
        }

        return $result;
    }

    /**
     * Modular Helper: Dough Cost Calculation
     */
    private static function calculateHamurCost($row_urun, $row_site, $urun_tipi) {
        $tam_hamur_maliyet = $row_site ? floatval($row_site->HAMUR_MALIYET) : 0.00;
        
        $katsayi = 0.00;
        $hamur_tipi = "Hamursuz";

        if ($urun_tipi === 'KRUVASAN') {
            $katsayi = 1.00;
            $hamur_tipi = "Kruvasan Hamuru";
            $tam_hamur_maliyet = $row_site ? floatval($row_site->KRUVASAN_MALIYET) : 0.00;
            $kullanilan_hamur_maliyet = $tam_hamur_maliyet;
        } else if ($urun_tipi === 'ICECEK' || $urun_tipi === 'DIGER' || intval($row_urun->HAMUR_KULLANIM_ID) <= 0) {
            $katsayi = 0.00;
            $hamur_tipi = "Hamursuz";
            $kullanilan_hamur_maliyet = 0.00;
        } else {
            $katsayi = ($row_urun->HAMUR_KULLANIM_DURUM == 1 && $row_urun->KATSAYI !== null) 
                       ? floatval($row_urun->KATSAYI) 
                       : 1.00;
            $hamur_tipi = !empty($row_urun->HAMUR_KULLANIM) ? $row_urun->HAMUR_KULLANIM : "Tam";
            $kullanilan_hamur_maliyet = $tam_hamur_maliyet * $katsayi;
        }

        $son_hamur_guncelleme_tarihi = ($row_site && !empty($row_site->GTARIH)) ? $row_site->GTARIH : null;

        return array(
            "toplam" => round($kullanilan_hamur_maliyet, 4),
            "result" => array(
                "hamur_tipi" => $hamur_tipi,
                "kullanilan_katsayi" => round($katsayi, 2),
                "tam_hamur_maliyet" => round($tam_hamur_maliyet, 4),
                "kullanilan_hamur_maliyet" => round($kullanilan_hamur_maliyet, 4),
                "son_hamur_guncelleme_tarihi" => $son_hamur_guncelleme_tarihi
            )
        );
    }

    /**
     * Modular Helper: Material Recipe Cost Calculation (N+1 SQL JOIN Optimized)
     */
    private static function calculateMalzemeCost($urun_id) {
        $sql = "SELECT 
                    UR.MIKTAR,
                    M.ID AS MALZEME_ID,
                    M.MALZEME AS MALZEME_ADI,
                    M.FIYAT AS MALZEME_FIYAT,
                    MT.KODU AS MALZEME_TIPI,
                    B.KODU AS BIRIM,
                    LATEST_ALIS.BIRIM_FIYAT AS ALIS_BIRIM_FIYAT,
                    LATEST_ALIS.FATURA_TARIH,
                    LATEST_ALIS.FATURA_NO,
                    LATEST_ALIS.MALZEME_ALIS_ID,
                    LATEST_ALIS.TEDARIKCI,
                    LATEST_ALIS.GUNCELLEME_TARIHI AS ALIS_GUNCELLEME_TARIHI
                FROM URUN_RECETE AS UR
                LEFT JOIN MALZEME AS M ON M.ID = UR.MALZEME_ID
                LEFT JOIN MALZEME_TIPI AS MT ON MT.ID = M.MALZEME_TIPI_ID
                LEFT JOIN BIRIM AS B ON B.ID = M.TEMEL_BIRIM_ID
                LEFT JOIN (
                    SELECT MAD1.MALZEME_ID, MAD1.BIRIM_FIYAT, MA1.FATURA_TARIH, MA1.FATURA_NO, MA1.ID AS MALZEME_ALIS_ID, C1.CARI AS TEDARIKCI, MA1.FATURA_TARIH AS GUNCELLEME_TARIHI
                    FROM MALZEME_ALIS_DETAY AS MAD1
                    JOIN MALZEME_ALIS AS MA1 ON MA1.ID = MAD1.MALZEME_ALIS_ID
                    LEFT JOIN CARI AS C1 ON C1.ID = MA1.CARI_ID
                    WHERE MA1.DURUM = 1
                    AND MAD1.ID = (
                        SELECT MAD2.ID
                        FROM MALZEME_ALIS_DETAY AS MAD2
                        JOIN MALZEME_ALIS AS MA2 ON MA2.ID = MAD2.MALZEME_ALIS_ID
                        WHERE MAD2.MALZEME_ID = MAD1.MALZEME_ID AND MA2.DURUM = 1
                        ORDER BY MA2.FATURA_TARIH DESC, MAD2.ID DESC
                        LIMIT 1
                    )
                ) AS LATEST_ALIS ON LATEST_ALIS.MALZEME_ID = UR.MALZEME_ID
                WHERE UR.URUN_ID = :URUN_ID";

        $rows = DB::get($sql, array(':URUN_ID' => $urun_id));
        $list = array();
        $malzeme_toplami = 0.00;

        if ($rows) {
            foreach ($rows as $row) {
                if ($row->ALIS_BIRIM_FIYAT !== null) {
                    $son_alis_fiyati = floatval($row->ALIS_BIRIM_FIYAT);
                } else {
                    $son_alis_fiyati = floatval($row->MALZEME_FIYAT);
                }

                if ($row->MALZEME_TIPI == 'PKG' || $row->BIRIM == 'ADT') {
                    $birim_maliyet = $son_alis_fiyati;
                } else {
                    $birim_maliyet = $son_alis_fiyati / 1000;
                }

                $kullanilan_miktar = floatval($row->MIKTAR);
                $satir_toplami = $kullanilan_miktar * $birim_maliyet;
                $malzeme_toplami += $satir_toplami;

                $list[] = array(
                    "malzeme_adi" => $row->MALZEME_ADI,
                    "malzeme_tipi" => $row->MALZEME_TIPI ? $row->MALZEME_TIPI : "HAM",
                    "kullanilan_miktar" => round($kullanilan_miktar, 4),
                    "birim" => $row->BIRIM ? $row->BIRIM : "GR",
                    "son_alis_fiyati" => round($son_alis_fiyati, 4),
                    "satir_toplami" => round($satir_toplami, 4),
                    "metadata" => array(
                        "tedarikci" => $row->TEDARIKCI ? $row->TEDARIKCI : null,
                        "fatura_no" => $row->FATURA_NO ? $row->FATURA_NO : null,
                        "fatura_tarihi" => $row->FATURA_TARIH ? $row->FATURA_TARIH : null,
                        "son_guncelleme_tarihi" => $row->ALIS_GUNCELLEME_TARIHI ? $row->ALIS_GUNCELLEME_TARIHI : null
                    )
                );
            }
        }

        return array(
            "toplam" => round($malzeme_toplami, 4),
            "result" => $list,
            "rows" => $rows
        );
    }

    /**
     * Modular Helper: Packaging Cost Calculation
     */
    private static function calculatePaketlemeCost($urun_id) {
        // Categories supported: Kap, Kapak, Poşet, Peçete, Çatal, Bıçak, Kaşık, Bardak, Pipet, Diğer
        // Currently returns [] as packaging is not configured per product yet
        $list = array();
        $toplam = 0.00;

        return array(
            "toplam" => round($toplam, 4),
            "result" => $list
        );
    }

    /**
     * Modular Helper: Overhead (Genel Gider) Cost Calculation
     */
    private static function calculateGenelGiderCost($urun_id) {
        $kalemler = array('Elektrik', 'Personel', 'Kira', 'Fire', 'Komisyon', 'KDV', 'Diğer');
        $list = array();
        $toplam = 0.00;

        foreach ($kalemler as $kalem) {
            $list[] = array(
                "tip" => $kalem,
                "hesaplama_tipi" => "SABIT", // 'ORAN' or 'SABIT'
                "oran" => 0.00,
                "tutar" => 0.00,
                "toplam" => 0.00
            );
        }

        return array(
            "toplam" => round($toplam, 4),
            "result" => $list
        );
    }

    /**
     * Modular Helper: Total Cost Calculations & Subtotals
     */
    private static function calculateToplamCost($hamur_data, $malzemeler_data, $paketleme_data, $genel_giderler_data) {
        $hamur_toplami = floatval($hamur_data['toplam']);
        $malzeme_toplami = floatval($malzemeler_data['toplam']);
        $paketleme_toplami = floatval($paketleme_data['toplam']);
        $genel_gider_toplami = floatval($genel_giderler_data['toplam']);

        $toplam_urun_maliyet = $hamur_toplami + $malzeme_toplami + $paketleme_toplami + $genel_gider_toplami;

        return array(
            "hamur_toplami" => round($hamur_toplami, 4),
            "malzeme_toplami" => round($malzeme_toplami, 4),
            "paketleme_toplami" => round($paketleme_toplami, 4),
            "genel_gider_toplami" => round($genel_gider_toplami, 4),
            "toplam_urun_maliyet" => round($toplam_urun_maliyet, 4)
        );
    }

    /**
     * Modular Helper: Last Update Metadata Timestamps
     */
    private static function getLastUpdateMetadata($urun_id, $recipeRows, $row_site) {
        $sql = "SELECT MALIYET_TARIH FROM URUN_MALIYET WHERE URUN_ID = :URUN_ID ORDER BY ID DESC LIMIT 1";
        $row_um = DB::getRow($sql, array(':URUN_ID' => $urun_id));
        $son_hesaplama_tarihi = ($row_um && !empty($row_um->MALIYET_TARIH)) ? $row_um->MALIYET_TARIH : date("Y-m-d H:i:s");

        $son_recete_guncellemesi = null;
        $son_hamur_guncellemesi = ($row_site && !empty($row_site->GTARIH)) ? $row_site->GTARIH : null;

        $latest_price_date = null;
        if ($recipeRows) {
            foreach ($recipeRows as $r) {
                if (!empty($r->FATURA_TARIH)) {
                    if ($latest_price_date === null || $r->FATURA_TARIH > $latest_price_date) {
                        $latest_price_date = $r->FATURA_TARIH;
                    }
                }
            }
        }

        return array(
            "son_hesaplama_tarihi" => $son_hesaplama_tarihi,
            "son_recete_guncellemesi" => $son_recete_guncellemesi,
            "son_hamur_guncellemesi" => $son_hamur_guncellemesi,
            "son_malzeme_fiyat_guncellemesi" => $latest_price_date
        );
    }
}



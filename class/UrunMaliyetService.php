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
}


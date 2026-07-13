<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');

echo "<pre style='font-family: monospace;'>";

// Tarih aralığı
$baslangic = new DateTime('2026-04-01');
$bitis     = new DateTime('2026-04-28');
$bitis->modify('+1 day'); // bitiş gününü dahil etmek için

for ($tarih = $baslangic; $tarih < $bitis; $tarih->modify('+1 day')) {

    $bugun = $tarih->format('Y-m-d');

    echo "=========================================\n";
    echo "ÜRÜN MALİYET HESAPLAMA RAPORU\n";
    echo "Tarih: $bugun\n";
    echo "=========================================\n\n";

    $data = array();
    $sql = "SELECT * FROM URUN WHERE DURUM = 1";
    $rows_urun = DB::get($sql, $data);

    $insert_say = 0;
    $update_say = 0;
    $urun_say   = 0;

    foreach ($rows_urun as $row_urun) {

        $urun_say++;
        $toplam_maliyet = 0;

        echo "-----------------------------------------\n";
        echo "Ürün: {$row_urun->URUN} (ID: {$row_urun->ID})\n";

        $data = array();
        $sql = "SELECT 
                    UR.*,
                    M.MALZEME,
                    M.AMBALAJ
                FROM URUN_RECETE AS UR
                    LEFT JOIN MALZEME AS M ON M.ID = UR.MALZEME_ID
                WHERE UR.URUN_ID = :URUN_ID
                ";
        $data[':URUN_ID'] = $row_urun->ID;
        $rows_recete = DB::get($sql, $data);

        foreach ($rows_recete as $row_recete) {

            $data = array();
            $sql = "SELECT MAD.BIRIM_FIYAT, MA.FATURA_TARIH 
                    FROM MALZEME_ALIS_DETAY AS MAD
                    LEFT JOIN MALZEME_ALIS AS MA ON MA.ID = MAD.MALZEME_ALIS_ID
                    WHERE MAD.MALZEME_ID = :MALZEME_ID AND MA.DURUM = 1
                    ORDER BY MA.FATURA_TARIH DESC LIMIT 1";
            $data[':MALZEME_ID'] = $row_recete->MALZEME_ID;
            $rows_alis = DB::get($sql, $data);

            if (!$rows_alis) {
                echo "  - Malzeme ID {$row_recete->MALZEME} için alış bulunamadı!\n";
                continue;
            }

            foreach ($rows_alis as $row_alis) {

                if ($row_recete->AMBALAJ == 1) {
                    $birim_maliyet = $row_alis->BIRIM_FIYAT;
                } else {
                    $birim_maliyet = $row_alis->BIRIM_FIYAT / 1000;
                }

                $malzeme_tutar = $row_recete->MIKTAR * $birim_maliyet;
                $toplam_maliyet += $malzeme_tutar;

                echo "  - Malzeme: {$row_recete->MALZEME}\n";
                echo "  - Malzeme Birim Fiyat: {$row_alis->BIRIM_FIYAT}\n";
                echo "    Miktar: {$row_recete->MIKTAR} gr\n";
                echo "    Tutar: " . number_format($malzeme_tutar, 2) . " TL\n";
                echo "<hr> <br>";
            }
        }

        if (in_array($row_urun->KATEGORI_ID, array(1,2))) { // Waffle, Bowl

            $genel_maliyet = $toplam_maliyet + $row_site->HAMUR_MALIYET;
            echo ">> Ürün Toplam Maliyet (Hamur Dahil): " . number_format($genel_maliyet, 2) . " TL\n";

        } else if (in_array($row_urun->KATEGORI_ID, array(3))) { // Bardak

            $genel_maliyet = $toplam_maliyet + ($row_site->HAMUR_MALIYET / 2);
            echo ">> Bardak Ürünü Toplam Maliyet (Hamur Dahil): " . number_format($genel_maliyet, 2) . " TL\n";

        } else if (in_array($row_urun->KATEGORI_ID, array(6))) { // Kruvasan

            $genel_maliyet = $toplam_maliyet + ($row_site->KRUVASAN_MALIYET);
            echo ">> Kruvasan Ürünü Toplam Maliyet: " . number_format($genel_maliyet, 2) . " TL\n";

        } else if (in_array($row_urun->KATEGORI_ID, array(4))) { // İçecek

            $data = array();
            $sql = "SELECT 
                        MAD.BIRIM_FIYAT,
                        MA.FATURA_TARIH
                    FROM MALZEME_ALIS_DETAY AS MAD
                        LEFT JOIN MALZEME_ALIS AS MA ON MA.ID = MAD.MALZEME_ALIS_ID
                        LEFT JOIN MALZEME AS M ON M.ID = MAD.MALZEME_ID
                    WHERE M.URUN_ID = :URUN_ID AND MA.DURUM = 1
                    ORDER BY MA.FATURA_TARIH DESC LIMIT 1";
            $data[':URUN_ID'] = $row_urun->ID;
            $rows_alis_icecek = DB::getRow($sql, $data);

            $genel_maliyet = $rows_alis_icecek->BIRIM_FIYAT;
            echo ">> Ürün Toplam Maliyet (İçecek): " . FormatSayi::sayi($genel_maliyet, 2) . " TL\n";
        }

        $data = array();
        $sql = "SELECT ID FROM URUN_MALIYET 
                WHERE URUN_ID = :URUN_ID 
                AND MALIYET_TARIH = :MALIYET_TARIH";
        $data[':URUN_ID']       = $row_urun->ID;
        $data[':MALIYET_TARIH'] = $bugun;
        $row_kontrol = DB::getRow($sql, $data);

        if ($row_kontrol AND $row_kontrol->ID > 0) {

            $data = array();
            $sql = "UPDATE URUN_MALIYET SET MALIYET = :MALIYET WHERE ID = :ID";
            $data[':MALIYET'] = $genel_maliyet;
            $data[':ID']      = $row_kontrol->ID;
            DB::exec($sql, $data);

            echo "✔ Güncellendi (ID: {$row_kontrol->ID})\n";
            $update_say++;

        } else {

            $data = array();
            $sql = "INSERT INTO URUN_MALIYET SET 
                        URUN_ID = :URUN_ID,
                        URUN = :URUN,
                        MALIYET_TARIH = :MALIYET_TARIH,
                        MALIYET = :MALIYET";

            $data[':URUN_ID']       = $row_urun->ID;
            $data[':URUN']          = $row_urun->URUN;
            $data[':MALIYET_TARIH'] = $bugun;
            $data[':MALIYET']       = $genel_maliyet;

            DB::insert($sql, $data);

            echo "➕ Yeni kayıt eklendi\n";
            $insert_say++;
        }

        echo "\n";
    }

    echo "=========================================\n";
    echo "TARİH SONU: $bugun\n";
    echo "TOPLAM ÜRÜN: $urun_say\n";
    echo "GÜNCELLENEN: $update_say\n";
    echo "EKLENEN: $insert_say\n";
    echo "=========================================\n\n";
}

echo "</pre>";
?>
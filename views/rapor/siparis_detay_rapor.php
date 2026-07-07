<?
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
    session_kontrol();

    if(is_null($_REQUEST['durum'])){
        $_REQUEST['durum'] = 1;
    }

    $_REQUEST['kategori_id'] = 1;

    $excel = new excelSayfasi();
    $excel->sutunEkle("Sipariş No","ID","");
    $excel->sutunEkle("İsim","ISIM","");
    $excel->sutunEkle("Telefon","TELEFON","");
    $excel->sutunEkle("Mail","MAIL","");
    $excel->sutunEkle("Firma","CARI","");
    $excel->sutunEkle("Tutar","TUTAR","");
    $excel->sutunEkle("Ürün Sayısı","URUN_SAYISI","");
    $excel->sutunEkle("Ödeme Durumu","ODEME_TEXT","");
    $excel->sutunEkle("Süreç","SUREC","");
    $excel->sutunEkle("Açıklama","ACIKLAMA","");
    $excelOut = $excel->excel();
    
    $result             = $cRapor->getSiparisDetaylar($_REQUEST);
    $rows               = $result['rows'];

    $_SESSION["Table"]  = $result;
    $_SESSION['excel']  = $excelOut;

    $rows_urun_maliyet = $cRapor->getUrunMaliyet(array('tarih' => $_REQUEST['siparis_tarih'], 'tarih_var' => $_REQUEST['siparis_tarih_var']));
    
    $rows_urun_maliyet_index = array();
    foreach ($rows_urun_maliyet as $key => $row_urun_maliyet) {
        $rows_urun_maliyet_index[$row_urun_maliyet->URUN_ID][$row_urun_maliyet->MALIYET_TARIH] = $row_urun_maliyet;
    }

    $rows_siparis = $cSiparis->getSiparisler2();

    $rows_siparis_index = array();
    foreach ($rows_siparis as $key => $row_siparis) {
        $rows_siparis_index[$row_siparis->SIPARIS_NO] = $row_siparis;
    }
    
?>
<!Doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title> <?=$row_site->TITLE?> | Sipariş Detay Listesi </title>
        <?=$cTheme->Linkler()?>
    </head>
    <body>
        <div class="layout-wrapper layout-content-navbar">
            <div class="layout-container">
                <?=$cTheme->Menu()?>
                <div class="layout-page">
                    <?=$cTheme->Header()?>
                    <div class="content-wrapper">
                        <div class="container-xxl flex-grow-1 container-p-y">

                            <div class="row">
                                <div class="col-xxl">
                                    <div class="card mb-6">
                                        <div class="card-body">
                                            <form>
                                                <input type="hidden" name="route" value="<?=$_REQUEST['route']?>">
                                                <div class="row">
                                                    <div class="col-md-3 mb-4">
                                                        <div class="input-group input-group-merge">
                                                            <span class="input-group-text"><i class="ri-hashtag"></i></span>
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="text" id="siparis_no" name="siparis_no" class="form-control" value="<?=$_REQUEST['siparis_no']?>" placeholder="Sipariş No">
                                                                <label>Sipariş No</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 mb-4">
                                                        <div class="input-group input-group-merge">
                                                            <span class="input-group-text"><i class="ri-user-line"></i></span>
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="text" id="musteri" name="musteri" class="form-control" value="<?=$_REQUEST['musteri']?>" placeholder="Müşteri">
                                                                <label>Müşteri</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-6 select2-primary">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="kategori_ids[]" id="kategori_ids" class="select2 form-select" data-style="btn-default" multiple>
                                                                <?=$cUrun->Kategoriler()->setSecilen($_REQUEST['kategori_ids'])->getSelect("ID", "AD")?>
                                                            </select>
                                                            <label>Kategoriler</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-6 select2-primary">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="siparis_surec_ids[]" id="siparis_surec_ids" class="select2 form-select" data-style="btn-default" multiple>
                                                                <?=$cRapor->SiparisSurecler()->setSecilen($_REQUEST['siparis_surec_ids'])->getSelect("ID", "AD")?>
                                                            </select>
                                                            <label>Sipariş Süreç</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 mb-4">
                                                        <div class="input-group">
                                                            <div class="input-group-text form-check mb-0">
                                                                <input class="form-check-input m-auto" type="checkbox" id="siparis_tarih_var" name="siparis_tarih_var" <?=($_REQUEST['siparis_tarih_var'] == 'on') ? 'checked' : ''?> aria-label="Checkbox for following text input">
                                                            </div>
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="text" name="siparis_tarih" id="siparis_tarih" class="form-control datepicker_range" value="<?=$_REQUEST['siparis_tarih']?>">
                                                                <label for="siparis_tarih">Sipariş Tarih</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 mb-4">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="sayfalama" id="sayfalama" class="btn select2 form-select" data-style="btn-default">
                                                                <?=$cUrun->Sayfalama()->setSecilen($_REQUEST['sayfalama'])->getSelect("ID", "AD")?>
                                                            </select>
                                                            <label>Sayfalama</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 mt-1">
                                                        <button type="submit" class="btn btn-primary">Filtrele</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-6">
                                <div class="card-header header-elements bg-primary py-1">
                                    <h6 class="mb-0 me-2 text-white"> <i class="ri-shopping-bag-line fs-4 me-2"></i> Sipariş Listesi <small><?=$result["sayfa_araligi"]?></small></h6>
                                    <div class="card-header-elements ms-auto">
                                        <a href="../excel_sql.php" data-bs-toggle="tooltip" title="Excel" class="btn btn-icon text-white float-right border-white borderd-radius btn-sm"> <i class="ri-file-excel-2-line"></i> </a>
                                    </div>
                                </div>
                                <div class="card-body mt-2">
                                    <div class="card-datatable table-responsive">
                                        <table class="table table-hover table-sm">
                                            <thead class="thead-themed fw-bold py-0">
                                                <tr class="table-primary">
                                                    <td nowrap>#</td>
                                                    <td nowrap align="center">Sipariş No</td>
                                                    <td nowrap align="center">Resmi</td>
                                                    <td nowrap>Ürün</td>
                                                    <td nowrap>Müşteri</td>
                                                    <td nowrap align="right">Fiyat</td>
                                                    <td nowrap align="center">Adet</td>
                                                    <td nowrap align="right">Tutar</td>
                                                    <td nowrap align="right">İndirim</td>
                                                    <td nowrap align="right">İndirimli Tutar</td>
                                                    <td nowrap align="right">Komisyon Tutar</td>
                                                    <td nowrap align="right">Komisyonsuz Tutar</td>
                                                    <td nowrap align="right">Ürün Maliyet</td>
                                                    <td nowrap align="right">Net Kar (Kazanç)</td>
                                                    <td nowrap align="center">Sipariş Tarih</td>
                                                    <td nowrap></td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?foreach ($rows as $key => $row) {
                                                    $row->KOMISYON_TUTAR            = ($row->TUTAR - $rows_siparis_index[$row->SIPARIS_NO]->INDIRIM_TUTAR) * ($row_site->TRENDYOL_KOMISYON / 100);
                                                    $row->KOMISUZYON_TUTAR          = ($row->TUTAR - $rows_siparis_index[$row->SIPARIS_NO]->INDIRIM_TUTAR) - $row->KOMISYON_TUTAR;
                                                 
                                                    $row->URUN_MALIYET = 0;
                                                    if($rows_urun_maliyet_index[$row->URUN_ID][$row->SIPARIS_TARIH_DATE] > 0){
                                                        $row->URUN_MALIYET = $rows_urun_maliyet_index[$row->URUN_ID][$row->SIPARIS_TARIH_DATE]->MALIYET * $row->ADET;

                                                        $row_toplam->URUN_MALIYET              += $row->URUN_MALIYET;
                                                    }

                                                    $row->NET_KAR = $row->KOMISUZYON_TUTAR - $row->URUN_MALIYET;

                                                    $row_toplam->FIYAT              += $row->FIYAT;
                                                    $row_toplam->ADET               += $row->ADET;
                                                    $row_toplam->TUTAR              += $row->TUTAR;
                                                    $row_toplam->KOMISYON_TUTAR     += $row->KOMISYON_TUTAR;
                                                    $row_toplam->KOMISUZYON_TUTAR   += $row->KOMISUZYON_TUTAR;
                                                    $row_toplam->NET_KAR            += $row->NET_KAR;


                                                    ?>
                                                    <tr>
                                                        <td><?=($key+1)?></td>
                                                        <td align="center"><a href="/views/siparis/siparis_detay.php?route=siparis/siparis_listesi&id=<?=$row->SIPARIS_ID?>&token=<?=$row->TOKEN?>" data-bs-toggle="tooltip" title="Sipariş Detayı">#<?=$row->SIPARIS_NO?></a></td>
                                                        <td align="center">
                                                            <?if(is_file(fncImgPathFolder2($row->RESIM_URL, $row_site->IMG_PATH))){?>
                                                                <img src="<?=fncImgPath($row->RESIM_URL, $row_site->IMG_PATH)?>" class="rounded-3 fancybox" alt="Ürün Resim" height="70">
                                                            <?}else{?>
                                                                <img src="<?=$row_site->LOGO?>" class="rounded-3 fancybox" alt="Menü Yönetim" height="70"/>
                                                            <?}?>
                                                        </td>
                                                        <td nowrap><?=$row->URUN?></td>
                                                        <td nowrap><?=$row->MUSTERI?></td>
                                                        <td nowrap align="right"><?=FormatSayi::sayi($row->FIYAT)?> ₺</td>
                                                        <td nowrap align="center"><?=$row->ADET?></td>
                                                        <td nowrap align="right"><?=FormatSayi::sayi($row->TUTAR)?> ₺</td>
                                                        <td nowrap align="right"><?=FormatSayi::sayi($rows_siparis_index[$row->SIPARIS_NO]->INDIRIM_TUTAR)?> ₺</td>
                                                        <td nowrap align="right"><?=FormatSayi::sayi($row->TUTAR - $rows_siparis_index[$row->SIPARIS_NO]->INDIRIM_TUTAR)?> ₺</td>
                                                        <td nowrap align="right"><?=FormatSayi::sayi($row->KOMISYON_TUTAR)?> ₺</td>
                                                        <td nowrap align="right"><?=FormatSayi::sayi($row->KOMISUZYON_TUTAR)?> ₺</td>
                                                        <td nowrap align="right"><?=FormatSayi::sayi($row->URUN_MALIYET)?> ₺</td>
                                                        <td nowrap align="right"><?=FormatSayi::sayi($row->NET_KAR)?> ₺</td>
                                                        <td nowrap align="center"><?=FormatTarih::tarih($row->SIPARIS_TARIH)?></td>
                                                        <td nowrap>
                                                            <a href="/views/siparis/siparis_detay.php?route=siparis/siparis_listesi&id=<?=$row->ID?>&token=<?=$row->TOKEN?>" data-bs-toggle="tooltip" class="btn btn-primary btn-icon btn-sm" title="Düzenle"> <i class="ri-arrow-right-double-fill"></i></a>
                                                            <!-- <a href="javascript:;" data-bs-toggle="tooltip" class="btn btn-danger btn-icon btn-sm" data-id="<?=$row->ID?>" onclick="fncUrunSil(this)" title="Sil"><i class="ri-delete-bin-5-line"></i></a> -->
                                                        </td>
                                                    </tr>
                                                <?}?>
                                            </tbody>
                                            <tfoot>
                                                <tr class="table-secondary fw-bold">
                                                    <td nowrap colspan="5" align="right">Genel Toplam :</td>
                                                    <td nowrap align="right"><?=FormatSayi::sayi($row_toplam->FIYAT,2)?> ₺</td>
                                                    <td nowrap align="center"><?=FormatSayi::sayi($row_toplam->ADET,0)?></td>
                                                    <td nowrap></td>
                                                    <td nowrap></td>
                                                    <td nowrap align="right"><?=FormatSayi::sayi($row_toplam->TUTAR,2)?> ₺</td>
                                                    <td nowrap align="right"><?=FormatSayi::sayi($row_toplam->KOMISYON_TUTAR,2)?> ₺</td>
                                                    <td nowrap align="right"><?=FormatSayi::sayi($row_toplam->KOMISUZYON_TUTAR,2)?> ₺</td>
                                                    <td nowrap align="right"><?=FormatSayi::sayi($row_toplam->URUN_MALIYET,2)?> ₺</td>
                                                    <td nowrap align="right"><?=FormatSayi::sayi($row_toplam->NET_KAR,2)?> ₺</td>
                                                    <td nowrap colspan="2"></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                <div class="pagination d-flex justify-content-center">
                                    <?=$result['sayfalama']->sayfalamaOlustur();?>
                                </div>
                            </div>

                        </div>
                        <?=$cTheme->Footer()?>
                        <div class="content-backdrop fade"></div>
                    </div>
                </div>
            </div>
            <div class="layout-overlay layout-menu-toggle"></div>
            <div class="drag-target"></div>
        </div>
        <?=$cTheme->Scriptler()?>
    </body>
</html>

<script type="text/javascript">

    var start = moment().subtract(29, 'days');
    var end = moment();

    function cb(start, end) {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    }

    $('#siparis_tarih').daterangepicker({
        timePicker: false,
        timePicker24Hour: true,
        timePickerIncrement: 30,
        locale: {
            "format": "DD.MM.YYYY",
            "separator": " , ",
            "applyLabel": "Uygula",
            "cancelLabel": "Vazgeç",
            "fromLabel": "Dan",
            "toLabel": "a",
            "customRangeLabel": "Seç",
            "weekLabel": "W",
            "daysOfWeek": [
                "Pa",
                "Pz",
                "Sa",
                "Ça",
                "Pe",
                "Cu",
                "Ct"
            ],
            "monthNames": [
                "Ocak",
                "Şubat",
                "Mart",
                "Nisan",
                "Mayıs",
                "Haziran",
                "Temmuz",
                "Ağustos",
                "Eylül",
                "Ekim",
                "Kasım",
                "Aralık"
            ],
            "firstDay": 1
        },
        ranges: {
            'Bugün': [moment(), moment()],
            'Dün': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Son 7 gün': [moment().subtract(6, 'days'), moment()],
            'Son 30 gün': [moment().subtract(29, 'days'), moment()],
            'Bu Ay': [moment().startOf('month'), moment().endOf('month')],
            'Geçen Ay': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            'Bu Yıl': [moment().startOf('year'), moment().endOf('year')]
        },
    }, cb);

    cb(start, end);

    $('#siparis_tarih').on('change', function (e) {
        $(this).closest('.input-group').find(":checkbox").prop("checked", true);
    });

</script>



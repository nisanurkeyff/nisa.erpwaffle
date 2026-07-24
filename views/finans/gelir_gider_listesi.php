<?
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
    session_kontrol();

    // Default filters
    if(is_null($_REQUEST['fatura_tarih_var'])){
        $_REQUEST['fatura_tarih_var'] = '';
        // Default to this month
        $_REQUEST['fatura_tarih'] = date('01.m.Y') . ' , ' . date('t.m.Y');
    }

    $excel = new excelSayfasi();
    $excel->sutunEkle("Tarih","FATURA_TARIH","");
    $excel->sutunEkle("Tip","TIP","");
    $excel->sutunEkle("Kaynak","KAYNAK_ADI","");
    $excel->sutunEkle("Durum","HAREKET_DURUMU","");
    $excel->sutunEkle("Cari","CARI","");
    $excel->sutunEkle("Kategori","KATEGORI_ADI","");
    $excel->sutunEkle("Açıklama","ACIKLAMA","");
    $excel->sutunEkle("Tutar","TUTAR","FormatSayi::virgul2");
    $excel->sutunEkle("Fatura No","FATURA_NO","");
    $excelOut = $excel->excel();

    $result             = $cGelirGider->getGelirGiderler($_REQUEST);
    $rows               = $result['rows'];
    $ozet               = $cGelirGider->getFinansOzet($_REQUEST);

    $rows_kategoriler   = $cGelirGider->getKategoriler();
    $rows_kaynaklar     = $cGelirGider->getIslemKaynaklari();

    $_SESSION["Table"]  = $result;
    $_SESSION['excel']  = $excelOut;

    // Report Summary totals at the bottom (calculated from currently loaded records on screen or all matches?)
    // ERP best practices calculate these totals from all filtered results (not just the page).
    // Our getFinansOzet already calculates all filtered records dynamically, so we can use $ozet directly!
    // This is much more accurate and robust than summing page-only values.
?>
<!Doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title> <?=$row_site->TITLE?> | Gelir / Gider Yönetimi </title>
        <?=$cTheme->Linkler()?>
        <style>
            .pointer { cursor: pointer; }
            .bg-label-primary { background-color: #e7e7ff !important; color: #696cff !important; }
            .bg-label-success { background-color: #e8fadf !important; color: #71dd37 !important; }
            .bg-label-danger { background-color: #ffe5e5 !important; color: #ff3e1d !important; }
            .bg-label-warning { background-color: #fff2e2 !important; color: #ff9f43 !important; }
            .bg-label-info { background-color: #e5f8fc !important; color: #03c3ec !important; }
            .bg-label-secondary { background-color: #ebeef0 !important; color: #8592a3 !important; }
            .bg-label-dark { background-color: #e1e2e3 !important; color: #233446 !important; }
        </style>
    </head>
    <body>
        <div class="layout-wrapper layout-content-navbar">
            <div class="layout-container">
                <?=$cTheme->Menu()?>
                <div class="layout-page">
                    <?=$cTheme->Header()?>
                    <div class="content-wrapper">
                        <div class="container-xxl flex-grow-1 container-p-y">
                            
                            <!-- Dynamic Financial Dashboard Cards -->
                            <div class="row g-6 mb-6">
                                <!-- Bu Ay Gelir -->
                                <div class="col-lg-2-4 col-md-4 col-sm-6 col-12">
                                    <div class="card h-100 border-start border-success border-3">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="card-info">
                                                    <p class="mb-1 text-muted text-nowrap">Bu Ay Gelir</p>
                                                    <h5 class="mb-0 text-success fw-bold"><?=FormatSayi::sayi($ozet['GELIR_TAMAMLANDI'], 2)?> ₺</h5>
                                                </div>
                                                <div class="avatar">
                                                    <span class="avatar-initial rounded-3 bg-label-success"><i class="ri-arrow-left-down-line ri-24px"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Bu Ay Gider -->
                                <div class="col-lg-2-4 col-md-4 col-sm-6 col-12">
                                    <div class="card h-100 border-start border-danger border-3">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="card-info">
                                                    <p class="mb-1 text-muted text-nowrap">Bu Ay Gider</p>
                                                    <h5 class="mb-0 text-danger fw-bold"><?=FormatSayi::sayi($ozet['GIDER_TAMAMLANDI'], 2)?> ₺</h5>
                                                </div>
                                                <div class="avatar">
                                                    <span class="avatar-initial rounded-3 bg-label-danger"><i class="ri-arrow-right-up-line ri-24px"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Net Nakit Akışı -->
                                <div class="col-lg-2-4 col-md-4 col-sm-6 col-12">
                                    <?php
                                        $nakit_color = ($ozet['NET_NAKIT'] >= 0) ? 'success' : 'danger';
                                        $nakit_bg = ($ozet['NET_NAKIT'] >= 0) ? 'bg-label-success' : 'bg-label-danger';
                                    ?>
                                    <div class="card h-100 border-start border-<?=$nakit_color?> border-3">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="card-info">
                                                    <p class="mb-1 text-muted text-nowrap">Net Nakit Akışı</p>
                                                    <h5 class="mb-0 text-<?=$nakit_color?> fw-bold"><?=FormatSayi::sayi($ozet['NET_NAKIT'], 2)?> ₺</h5>
                                                </div>
                                                <div class="avatar">
                                                    <span class="avatar-initial rounded-3 <?=$nakit_bg?>"><i class="ri-scales-3-line ri-24px"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Bekleyen Tahsilat -->
                                <div class="col-lg-2-4 col-md-4 col-sm-6 col-12">
                                    <div class="card h-100 border-start border-info border-3">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="card-info">
                                                    <p class="mb-1 text-muted text-nowrap">Bekleyen Tahsilat</p>
                                                    <h5 class="mb-0 text-info fw-bold"><?=FormatSayi::sayi($ozet['GELIR_BEKLIYOR'], 2)?> ₺</h5>
                                                </div>
                                                <div class="avatar">
                                                    <span class="avatar-initial rounded-3 bg-label-info"><i class="ri-time-line ri-24px"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Bekleyen Ödeme -->
                                <div class="col-lg-2-4 col-md-4 col-sm-6 col-12">
                                    <div class="card h-100 border-start border-warning border-3">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="card-info">
                                                    <p class="mb-1 text-muted text-nowrap">Bekleyen Ödeme</p>
                                                    <h5 class="mb-0 text-warning fw-bold"><?=FormatSayi::sayi($ozet['GIDER_BEKLIYOR'], 2)?> ₺</h5>
                                                </div>
                                                <div class="avatar">
                                                    <span class="avatar-initial rounded-3 bg-label-warning"><i class="ri-hourglass-line ri-24px"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Expanded Filters Panel -->
                            <div class="row">
                                <div class="col-xxl">
                                    <div class="card mb-6">
                                        <div class="card-body">
                                            <form>
                                                <input type="hidden" name="route" value="<?=$_REQUEST['route']?>">
                                                <div class="row g-4">
                                                    <div class="col-md-3">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="tip" id="tip" class="select2 form-select">
                                                                <option value="">Seçiniz</option>
                                                                <option value="GELIR" <?=($_REQUEST['tip'] == 'GELIR') ? 'selected' : ''?>>Gelir</option>
                                                                <option value="GIDER" <?=($_REQUEST['tip'] == 'GIDER') ? 'selected' : ''?>>Gider</option>
                                                            </select>
                                                            <label>İşlem Tipi</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="kategori_id" id="kategori_id" class="select2 form-select">
                                                                <option value="">Seçiniz</option>
                                                                <?foreach($rows_kategoriler as $kat){?>
                                                                    <option value="<?=$kat->ID?>" data-tip="<?=$kat->TIP?>" data-icon="<?=$kat->ICON?>" <?=($_REQUEST['kategori_id'] == $kat->ID) ? 'selected' : ''?>><?=$kat->KATEGORI?></option>
                                                                <?}?>
                                                            </select>
                                                            <label>Kategori</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="cari_id" id="cari_id" class="select2 form-select">
                                                                <?=$cGelirGider->Cariler()->setSecilen($_REQUEST['cari_id'])->setSeciniz()->getSelect("ID", "AD")?>
                                                            </select>
                                                            <label>Cari</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="hareket_durumu" id="hareket_durumu" class="select2 form-select">
                                                                <option value="">Seçiniz</option>
                                                                <option value="BEKLIYOR" <?=($_REQUEST['hareket_durumu'] == 'BEKLIYOR') ? 'selected' : ''?>>Bekliyor</option>
                                                                <option value="TAMAMLANDI" <?=($_REQUEST['hareket_durumu'] == 'TAMAMLANDI') ? 'selected' : ''?>>Tamamlandı</option>
                                                                <option value="IPTAL" <?=($_REQUEST['hareket_durumu'] == 'IPTAL') ? 'selected' : ''?>>İptal</option>
                                                            </select>
                                                            <label>Hareket Durumu</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="islem_kaynagi_id" id="islem_kaynagi_id" class="select2 form-select">
                                                                <option value="">Seçiniz</option>
                                                                <?foreach($rows_kaynaklar as $kyn){?>
                                                                    <option value="<?=$kyn->ID?>" <?=($_REQUEST['islem_kaynagi_id'] == $kyn->ID) ? 'selected' : ''?>><?=$kyn->KAYNAK_ADI?></option>
                                                                <?}?>
                                                            </select>
                                                            <label>Finans Kaynağı</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-floating form-floating-outline">
                                                            <input type="text" name="fatura_no" id="fatura_no" class="form-control" value="<?=$_REQUEST['fatura_no']?>" placeholder="Örn: FT-12345">
                                                            <label>Fatura / Belge No</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="row g-2">
                                                            <div class="col-6">
                                                                <div class="form-floating form-floating-outline">
                                                                    <input type="text" name="tutar_min" id="tutar_min" class="form-control decimal" value="<?=$_REQUEST['tutar_min']?>" placeholder="Min">
                                                                    <label>Tutar Min</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-floating form-floating-outline">
                                                                    <input type="text" name="tutar_max" id="tutar_max" class="form-control decimal" value="<?=$_REQUEST['tutar_max']?>" placeholder="Max">
                                                                    <label>Tutar Max</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="input-group">
                                                            <div class="input-group-text form-check mb-0">
                                                                <input class="form-check-input m-auto" type="checkbox" id="fatura_tarih_var" name="fatura_tarih_var" <?=($_REQUEST['fatura_tarih_var'] == 'on' || $_REQUEST['fatura_tarih_var'] == '1' || $_REQUEST['fatura_tarih_var'] > 0) ? 'checked' : ''?>>
                                                            </div>
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="text" name="fatura_tarih" id="fatura_tarih" class="form-control datepicker_range" value="<?=$_REQUEST['fatura_tarih']?>">
                                                                <label for="fatura_tarih">Fatura Tarihi</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="sayfalama" id="sayfalama" class="select2 form-select">
                                                                <?=$cUrun->Sayfalama()->setSecilen($_REQUEST['sayfalama'])->getSelect("ID", "AD")?>
                                                            </select>
                                                            <label>Sayfalama</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-9 text-end mt-4">
                                                        <button type="submit" class="btn btn-primary me-2"><i class="ri-search-line me-1"></i> Filtrele</button>
                                                        <a href="/views/finans/gelir_gider_listesi.php?route=finans/gelir_gider_listesi" class="btn btn-outline-secondary"><i class="ri-refresh-line me-1"></i> Sıfırla</a>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- List revamping -->
                            <div class="card mb-6">
                                <div class="card-header header-elements bg-primary py-1">
                                    <h6 class="mb-0 me-2 text-white"> <i class="ri-list-check fs-4 me-2"></i> Gelir / Gider Listesi <small><?=$result["sayfa_araligi"]?></small></h6>
                                    <div class="card-header-elements ms-auto">
                                        <a href="/views/finans/gelir_gider_ekle.php?route=finans/gelir_gider_listesi" data-bs-toggle="tooltip" class="btn btn-icon text-white float-right border-white border-radius btn-sm" title="Yeni Ekle"><i class="ri-add-line fs-4"></i></a>
                                    </div>
                                </div>
                                <div class="card-body mt-2">
                                    <div class="card-datatable table-responsive">
                                        <table class="table table-hover table-sm">
                                            <thead class="thead-themed fw-bold py-0">
                                                <tr class="table-primary">
                                                    <td nowrap>Tarih</td>
                                                    <td nowrap>Tip</td>
                                                    <td nowrap>Kaynak</td>
                                                    <td nowrap>Durum</td>
                                                    <td nowrap>Cari</td>
                                                    <td nowrap>Kategori</td>
                                                    <td>Açıklama</td>
                                                    <td nowrap align="right">Tutar</td>
                                                    <td nowrap>Fatura No</td>
                                                    <td nowrap>Belge</td>
                                                    <td nowrap>Kayıt Yapan</td>
                                                    <td nowrap></td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?foreach ($rows as $key => $row) {
                                                    $tip_renk = ($row->TIP == 'GELIR') ? "text-success" : "text-danger";
                                                    
                                                    // HAREKET DURUMU Badges
                                                    $durum_badge = '<span class="badge bg-label-warning rounded-pill">Bekliyor</span>';
                                                    if ($row->HAREKET_DURUMU == 'TAMAMLANDI') {
                                                        $durum_badge = '<span class="badge bg-label-success rounded-pill">Tamamlandı</span>';
                                                    } elseif ($row->HAREKET_DURUMU == 'IPTAL') {
                                                        $durum_badge = '<span class="badge bg-label-danger rounded-pill">İptal</span>';
                                                    }

                                                    // Visual source badges from DB configuration
                                                    $kaynak_badge = sprintf(
                                                        '<span class="badge bg-label-%s rounded-pill"><i class="%s me-1 fs-6"></i>%s</span>',
                                                        $row->KAYNAK_RENK ? $row->KAYNAK_RENK : 'primary',
                                                        $row->KAYNAK_ICON ? $row->KAYNAK_ICON : 'ri-checkbox-circle-line',
                                                        $row->KAYNAK_ADI
                                                    );

                                                    // Tek Kayıt Prensibi (SST) - Manuel değilse Düzenle yerine "Detay" (eye) ikonu gösterilir.
                                                    $is_locked = ($row->ISLEM_KAYNAGI_ID != 1);
                                                    ?>
                                                    <tr>
                                                        <td nowrap><?=FormatTarih::tarih($row->FATURA_TARIH)?></td>
                                                        <td nowrap class="fw-bold <?=$tip_renk?>"><?=$row->TIP == 'GELIR' ? 'Gelir' : 'Gider'?></td>
                                                        <td nowrap><?=$kaynak_badge?></td>
                                                        <td nowrap><?=$durum_badge?></td>
                                                        <td nowrap class="fw-bold"><?=$row->CARI?></td>
                                                        <td nowrap>
                                                            <?if($row->KATEGORI_ADI){?>
                                                                <i class="<?=$row->KATEGORI_ICON ? $row->KATEGORI_ICON : 'ri-folder-line'?> me-1 <?=$tip_renk?> fs-5"></i> <?=$row->KATEGORI_ADI?>
                                                            <?} else {?>
                                                                -
                                                            <?}?>
                                                        </td>
                                                        <td><?=$row->ACIKLAMA?></td>
                                                        <td nowrap align="right" class="fw-bold <?=$tip_renk?>"><?=FormatSayi::sayi($row->TUTAR,2)?> ₺</td>
                                                        <td nowrap><?=$row->FATURA_NO ? $row->FATURA_NO : '-'?></td>
                                                        <td nowrap align="center">
                                                            <?if($row->DOSYA_SAYISI > 0){?>
                                                                <span class="badge bg-primary pointer" data-id="<?=$row->ID?>" onclick="fncDosyaGoster(this)">
                                                                    <i class="ri-attachment-2 me-1"></i> <?=$row->DOSYA_SAYISI?>
                                                                </span>
                                                            <?} else {?>
                                                                -
                                                            <?}?>
                                                        </td>
                                                        <td><?=FormatYazi::kisalt2($row->KAYIT_YAPAN,25)?></td>
                                                        <td align="right" nowrap>
                                                            <?if($is_locked){?>
                                                                <!-- Kilitli Kayıt: Sadece Detay İnceleme Açık (Mavi Göz İkonu) -->
                                                                <a href="/views/finans/gelir_gider_duzenle.php?route=finans/gelir_gider_listesi&id=<?=$row->ID?>" data-bs-toggle="tooltip" class="btn btn-info btn-icon btn-sm" title="İncele"> <i class="ri-eye-line text-white"></i></a>
                                                            <?} else {?>
                                                                <!-- Manuel Kayıt: Düzenleme ve Silme Açık -->
                                                                <a href="/views/finans/gelir_gider_duzenle.php?route=finans/gelir_gider_listesi&id=<?=$row->ID?>" data-bs-toggle="tooltip" class="btn btn-primary btn-icon btn-sm" title="Düzenle"> <i class="ri-pencil-line"></i></a>
                                                                <a href="javascript:;" class="btn btn-danger btn-icon btn-sm" data-id="<?=$row->ID?>" onclick="fncSil(this)" title="Sil"><i class="ri-delete-bin-5-line"></i></a>
                                                            <?}?>
                                                        </td>
                                                    </tr>
                                                <?}?>
                                            </tbody>
                                            <tfoot>
                                                <tr class="table-secondary fw-bold text-dark">
                                                    <td colspan="7" align="right">Toplam Gelir (Tahsil Edilen) / Toplam Gider (Ödenen) :</td>
                                                    <td align="right">
                                                        <span class="text-success"><?=FormatSayi::sayi($ozet['GELIR_TAMAMLANDI'],2)?> ₺</span> / 
                                                        <span class="text-danger"><?=FormatSayi::sayi($ozet['GIDER_TAMAMLANDI'],2)?> ₺</span>
                                                    </td>
                                                    <td colspan="4"></td>
                                                </tr>
                                                <tr class="table-secondary fw-bold text-dark">
                                                    <td colspan="7" align="right">Bekleyen Tahsilat / Bekleyen Ödeme :</td>
                                                    <td align="right">
                                                        <span class="text-info"><?=FormatSayi::sayi($ozet['GELIR_BEKLIYOR'],2)?> ₺</span> / 
                                                        <span class="text-warning"><?=FormatSayi::sayi($ozet['GIDER_BEKLIYOR'],2)?> ₺</span>
                                                    </td>
                                                    <td colspan="4"></td>
                                                </tr>
                                                <tr class="table-secondary fw-bold text-dark">
                                                    <td colspan="7" align="right">Net Nakit :</td>
                                                    <td align="right">
                                                        <span class="text-<?=$nakit_color?>"><?=FormatSayi::sayi($ozet['NET_NAKIT'],2)?> ₺</span>
                                                    </td>
                                                    <td colspan="4"></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                <div class="pagination d-flex justify-content-center mt-3">
                                    <?=$result['sayfalama'] ? $result['sayfalama']->sayfalamaOlustur() : ''?>
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

        <!-- Dosya Görüntüleme Modalı -->
        <div class="modal fade" id="dosyalarModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-primary py-3">
                        <h5 class="modal-title text-white"><i class="ri-attachment-line me-2"></i> Finans Evrakları</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="list-group list-group-flush" id="lstDosyalar">
                            <!-- AJAX ile doldurulur -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                    </div>
                </div>
            </div>
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

    $('#fatura_tarih').daterangepicker({
        timePicker: false,
        locale: {
            "format": "DD.MM.YYYY",
            "separator": " , ",
            "applyLabel": "Uygula",
            "cancelLabel": "Vazgeç",
            "fromLabel": "Dan",
            "toLabel": "a",
            "customRangeLabel": "Seç",
            "weekLabel": "W",
            "daysOfWeek": ["Pa","Pz","Sa","Ça","Pe","Cu","Ct"],
            "monthNames": ["Ocak","Şubat","Mart","Nisan","Mayıs","Haziran","Temmuz","Ağustos","Eylül","Ekim","Kasım","Aralık"],
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

    $('#fatura_tarih').on('change', function (e) {
        $(this).closest('.input-group').find(":checkbox").prop("checked", true);
    });

    $('#fatura_tarih').on('apply.daterangepicker', function(ev, picker) {
        $(this).closest('.input-group').find(":checkbox").prop("checked", true);
    });

    var tumKategoriler = [];
    $(document).ready(function() {
        // Collect all categories
        $("#kategori_id option").each(function() {
            if ($(this).val()) {
                tumKategoriler.push({
                    id: $(this).val(),
                    text: $(this).text(),
                    tip: $(this).data("tip"),
                    icon: $(this).data("icon")
                });
            }
        });

        // Set up the original selected value of Kategori
        $("#kategori_id").data("secilen", "<?=$_REQUEST['kategori_id']?>");
        fncKategoriFiltrele();

        // Listen for tip changes
        $("#tip").on("change", function() {
            $("#kategori_id").data("secilen", "");
            fncKategoriFiltrele();
        });
    });

    function fncKategoriFiltrele() {
        var secilenTip = $("#tip").val();
        var secilenKat = $("#kategori_id").data("secilen");
        
        var filtered = tumKategoriler.filter(function(k) {
            return !secilenTip || k.tip == secilenTip;
        });
        
        var html = '<option value="">Seçiniz</option>';
        filtered.forEach(function(k) {
            var isSelected = (k.id == secilenKat) ? 'selected' : '';
            html += '<option value="' + k.id + '" ' + isSelected + '>' + k.text + '</option>';
        });
        $("#kategori_id").html(html).trigger('change');
    }

    function fncSil(obj){
        sweatAlert("Emin Misiniz?", "Evet, Sil").then(function (result) {
            if (result.value) {
                showSpinner();
                $.ajax({
                    url: "/router.php",
                    type: "POST",
                    data: {id: $(obj).data("id"), controller: "gelirGider", action: "gelir_gider_sil"},
                    dataType: "json",
                    success: function(response) {
                        $.unblockUI();
                        if (response.HATA) {
                            notyf.error(response.ACIKLAMA);
                        } else {
                            notyf.success(response.ACIKLAMA);
                            $(obj).closest('tr').fadeOut();
                            setTimeout(function(){ location.reload(); }, 1000);
                        }
                    }
                });
            }
        });
    }

    function fncDosyaGoster(obj) {
        var id = $(obj).data("id");
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: {gelir_gider_id: id, controller: "gelirGider", action: "dosya_listesi"},
            dataType: "json",
            success: function(response) {
                $.unblockUI();
                if (response.HATA) {
                    notyf.error(response.ACIKLAMA);
                } else {
                    var html = '';
                    if(response.ROWS.length > 0) {
                        response.ROWS.forEach(function(d) {
                            var tag = d.ACIKLAMA ? ' <span class="badge bg-label-info ms-2">' + d.ACIKLAMA + '</span>' : '';
                            html += `
                                <a href="/views/finans/dosya_goruntule.php?id=${d.ID}" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                                    <div>
                                        <i class="ri-file-text-line me-2 text-primary fs-5"></i>
                                        <strong>${d.DOSYA_ADI}</strong> ${tag}
                                    </div>
                                    <span class="badge bg-label-primary"><i class="ri-eye-line"></i> İncele</span>
                                </a>
                            `;
                        });
                    } else {
                        html = '<div class="p-4 text-center text-muted">Belge bulunamadı.</div>';
                    }
                    $("#lstDosyalar").html(html);
                    var modal = new bootstrap.Modal(document.getElementById('dosyalarModal'));
                    modal.show();
                }
            }
        });
    }
</script>

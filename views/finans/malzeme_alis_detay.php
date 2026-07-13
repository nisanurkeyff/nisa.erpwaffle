<?
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
    session_kontrol();

    $row = $cMalzemeAlis->getMalzemeAlis($_REQUEST['id']);
    fncTokenKontrol($row);

    $rows_detay = $cMalzemeAlis->getMalzemeAlisDetaylar($row->ID);
?>
<!Doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title> <?=$row_site->TITLE?> | Alış Fişi Detayı </title>
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
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 gap-6">
                                <div class="d-flex flex-column justify-content-center">
                                    <div class="d-flex align-items-center mb-1">
                                        <h5 class="mb-0">Alış Fişi #<?=$row->FIS_NO?></h5>
                                        <span class="badge bg-label-primary me-2 ms-2 rounded-pill"><?=FormatTarih::tarih($row->ALIS_TARIH)?></span>
                                    </div>
                                    <p class="mb-0">Fatura No: <?=$row->FATURA_NO ? $row->FATURA_NO : '-'?> | Fatura Tarihi: <?=FormatTarih::tarih($row->FATURA_TARIH)?></p>
                                </div>
                                <div>
                                    <a href="/views/finans/malzeme_alis_listesi.php" class="btn btn-outline-secondary waves-effect">Listeye Dön</a>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12 col-lg-8">
                                    <div class="card mb-6">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h5 class="card-title m-0">Malzeme Listesi</h5>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-hover table-striped mb-0 text-nowrap">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Malzeme</th>
                                                            <th class="text-end">Miktar</th>
                                                            <th class="text-center">Birim</th>
                                                            <th class="text-end text-nowrap">Birim F.</th>
                                                            <th class="text-end text-nowrap">KDV'li</th>
                                                            <th class="text-center text-nowrap">KDV %</th>
                                                            <th class="text-end text-nowrap">KDV Tutarı</th>
                                                            <th class="text-end text-nowrap">Ara Toplam</th>
                                                            <th class="text-end text-nowrap">Toplam</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?foreach ($rows_detay as $key => $row_detay) {
                                                            $net_price = $row_detay->BIRIM_FIYAT;
                                                            $gross_price = $row_detay->BIRIM_FIYAT * (1 + $row_detay->KDV / 100);
                                                            ?>
                                                            <tr>
                                                                <td><?=($key+1)?></td>
                                                                <td class="fw-semibold"><?=$row_detay->MALZEME?></td>
                                                                <td class="text-end"><?=FormatSayi::sayi($row_detay->MIKTAR, 2)?></td>
                                                                <td class="text-center"><span class="badge bg-label-secondary"><?=$row_detay->BIRIM?></span></td>
                                                                <td class="text-end text-nowrap"><?=FormatSayi::sayi($net_price, 2)?> ₺</td>
                                                                <td class="text-end text-nowrap"><?=FormatSayi::sayi($gross_price, 2)?> ₺</td>
                                                                <td class="text-center text-nowrap">% <?=$row_detay->KDV?></td>
                                                                <td class="text-end text-nowrap"><?=FormatSayi::sayi($row_detay->KDV_TUTAR, 2)?> ₺</td>
                                                                <td class="text-end text-nowrap"><?=FormatSayi::sayi($row_detay->ARA_TOPLAM, 2)?> ₺</td>
                                                                <td class="text-end text-nowrap fw-semibold"><?=FormatSayi::sayi($row_detay->TOPLAM_TUTAR, 2)?> ₺</td>
                                                            </tr>
                                                        <?}?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <?if(!empty($row->ACIKLAMA)){?>
                                        <div class="card mb-6">
                                            <div class="card-header">
                                                <h5 class="card-title m-0">Açıklama</h5>
                                            </div>
                                            <div class="card-body">
                                                <p class="mb-0 text-muted"><?=nl2br(htmlspecialchars($row->ACIKLAMA))?></p>
                                            </div>
                                        </div>
                                    <?}?>
                                </div>

                                <div class="col-12 col-lg-4">
                                    <div class="card mb-6">
                                        <div class="card-header">
                                            <h5 class="card-title m-0">Fiş Özeti</h5>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group list-group-flush mb-4">
                                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                    <span class="text-muted">Cari / Satıcı:</span>
                                                    <span class="fw-semibold"><?=$row->CARI?></span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                    <span class="text-muted">Ödeme Durumu:</span>
                                                    <span><?=fncOdemeDurumSpan($row->ODEME_DURUM_ID)?></span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                    <span class="text-muted">Fiyat Giriş Tipi:</span>
                                                    <span>
                                                        <?if($row->KDV_TIPI == 'dahil'){?>
                                                            <span class="badge bg-label-success">KDV Dahil</span>
                                                        <?}else{?>
                                                            <span class="badge bg-label-primary">KDV Hariç</span>
                                                        <?}?>
                                                    </span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                    <span class="text-muted">Ödeme Tarihi:</span>
                                                    <span class="fw-semibold"><?=($row->ODEME_TARIHI) ? FormatTarih::tarih($row->ODEME_TARIHI) : '-'?></span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                    <span class="text-muted">Ödeme Türü:</span>
                                                    <span class="fw-semibold"><?=$row->ODEME_TURU ? $row->ODEME_TURU : '-'?></span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                    <span class="text-muted">Vade Tarihi:</span>
                                                    <span class="fw-semibold"><?=FormatTarih::tarih($row->VADE_TARIH)?></span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                    <span class="text-muted">Kayıt Yapan:</span>
                                                    <span class="fw-semibold"><?=$row->KAYIT_YAPAN?></span>
                                                </li>
                                            </ul>

                                            <div class="border-top pt-3">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted">Ara Toplam:</span>
                                                    <span class="fw-semibold"><?=FormatSayi::sayi($row->ARA_TOPLAM, 2)?> ₺</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted">Toplam KDV:</span>
                                                    <span class="fw-semibold"><?=FormatSayi::sayi($row->KDV_TUTAR, 2)?> ₺</span>
                                                </div>
                                                <div class="d-flex justify-content-between border-top pt-2">
                                                    <span class="h6 mb-0 fw-bold">Genel Toplam:</span>
                                                    <span class="h6 mb-0 text-primary fw-bold"><?=FormatSayi::sayi($row->TOPLAM_TUTAR, 2)?> ₺</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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

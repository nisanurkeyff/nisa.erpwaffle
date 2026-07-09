<?
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
    session_kontrol();

    $result = $cSiparis->getSiparisLoglari($_REQUEST);
    $rows = $result['rows'];
?>
<!Doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title> <?=$row_site->TITLE?> | Sipariş Logları </title>
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
                                <div class="col-md-12">
                                    <div class="card mb-6">
                                        <div class="card-header pb-2">
                                            <h5 class="card-title m-0"><i class="ri-search-line me-2 text-primary"></i>Filtrele</h5>
                                        </div>
                                        <div class="card-body">
                                            <form method="GET" action="">
                                                <input type="hidden" name="route" value="siparis/siparis_loglari">
                                                <div class="row g-4 align-items-end">
                                                    <div class="col-md-4">
                                                        <div class="form-floating form-floating-outline">
                                                            <input type="text" name="siparis_no" id="siparis_no" class="form-control" value="<?=$_REQUEST['siparis_no']?>" placeholder="Sipariş No">
                                                            <label for="siparis_no">Sipariş No</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-floating form-floating-outline">
                                                            <input type="text" name="kullanici" id="kullanici" class="form-control" value="<?=$_REQUEST['kullanici']?>" placeholder="Kullanıcı">
                                                            <label for="kullanici">İşlem Yapan Kullanıcı</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <button type="submit" class="btn btn-primary me-2"><i class="ri-search-line me-1"></i>Filtrele</button>
                                                        <a href="?route=siparis/siparis_loglari" class="btn btn-outline-secondary"><i class="ri-refresh-line me-1"></i>Temizle</a>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-6">
                                <div class="card-header header-elements bg-primary py-1">
                                    <h6 class="mb-0 me-2 text-white"> <i class="ri-history-line fs-4 me-2"></i> Sipariş Logları <small><?=$result["sayfa_araligi"]?></small></h6>
                                    <div class="card-header-elements ms-auto">
                                        <a href="../excel_sql.php" data-bs-toggle="tooltip" title="Excel" class="btn btn-icon text-white float-right border-white borderd-radius btn-sm"> <i class="ri-file-excel-2-line"></i> </a>
                                    </div>
                                </div>
                                <div class="card-body mt-2">
                                    <div class="card-datatable table-responsive">
                                        <table class="table table-hover table-sm table-striped">
                                            <thead class="thead-themed fw-bold py-0">
                                                <tr class="table-primary">
                                                    <td nowrap>#</td>
                                                    <td nowrap>Sipariş No</td>
                                                    <td nowrap>İşlem Yapan</td>
                                                    <td nowrap>İşlem Türü</td>
                                                    <td nowrap>Değişen Alan</td>
                                                    <td nowrap>Eski Değer</td>
                                                    <td nowrap>Yeni Değer</td>
                                                    <td nowrap>Açıklama</td>
                                                    <td nowrap>Tarih & Saat</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?if(empty($rows)){?>
                                                    <tr>
                                                        <td colspan="9" class="text-center py-4 text-muted">Log kaydı bulunamadı.</td>
                                                    </tr>
                                                <?}else{?>
                                                    <?foreach ($rows as $key => $row) {?>
                                                        <tr>
                                                            <td><?=($key+1)?></td>
                                                            <td>
                                                                <a href="/views/siparis/siparis_detay.php?route=siparis/siparis_listesi&id=<?=$row->REFERANS_ID?>" class="fw-semibold">
                                                                    #<?=$row->REFERANS_NO?>
                                                                </a>
                                                            </td>
                                                            <td><?=$row->KULLANICI_ADSOYAD?></td>
                                                            <td>
                                                                <span class="badge bg-label-info"><?=$row->ISLEM_TURU?></span>
                                                            </td>
                                                            <td class="fw-bold"><?=$row->ALAN?></td>
                                                            <td class="text-danger text-decoration-line-through"><?=htmlspecialchars($row->ESKI_DEGER)?></td>
                                                            <td class="text-success fw-semibold"><?=htmlspecialchars($row->YENI_DEGER)?></td>
                                                            <td><?=htmlspecialchars($row->ACIKLAMA)?></td>
                                                            <td nowrap><?=FormatTarih::tarih($row->TARIH)?> <?=$row->SAAT?></td>
                                                        </tr>
                                                    <?}?>
                                                <?}?>
                                            </tbody>
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

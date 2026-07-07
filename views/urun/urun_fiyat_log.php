<?
	require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
	session_kontrol();

    $excel = new excelSayfasi();
    $excel->sutunEkle("Şube","SUBE","");
    $excel->sutunEkle("Ürün","URUN","");
    $excel->sutunEkle("Satış Türü","SATIS_TURU","");
    $excel->sutunEkle("Üst Kategori","UST_KATEGORI","");
    $excel->sutunEkle("Kategori","KATEGORI","");
    $excel->sutunEkle("Eski Fiyat","ESKI_FIYAT","");
    $excel->sutunEkle("Fiyat","FIYAT","");
    $excel->sutunEkle("Kayıt Yapan","KAYIT_YAPAN","");
    $excel->sutunEkle("Tarih","TARIH","format1");
    $excelOut = $excel->excel();
    
    $result             = $cUrun->getUrunFiyatLog($_REQUEST);
    $rows               = $result['rows'];

    $_SESSION["Table"]  = $result;
    $_SESSION['excel']  = $excelOut;

?>
<!Doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title> <?=$row_site->TITLE?> | Ürün Fiyat Log </title>
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
                                                    <div class="col-md-4 mb-4">
                                                        <div class="input-group input-group-merge">
                                                            <span class="input-group-text"><i class="ri-restaurant-2-fill"></i></span>
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="text" id="urun" name="urun" class="form-control" value="<?=$_REQUEST['urun']?>" placeholder="Ürün">
                                                                <label>Ürün</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 mb-4">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="kategori_id" id="kategori_id" class="btn select2 form-select" data-style="btn-default">
                                                                <?=$cUrun->Kategoriler()->setSecilen($_REQUEST['kategori_id'])->setSeciniz()->getSelect("ID", "AD")?>
                                                            </select>
                                                            <label>Kategori</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 mb-4">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="kayit_yapan_id" id="kayit_yapan_id" class="btn select2 form-select" data-style="btn-default">
                                                                <?=$cUrun->Kullanicilar()->setSecilen($_REQUEST['kayit_yapan_id'])->setSeciniz()->getSelect("ID", "AD")?>
                                                            </select>
                                                            <label>Kayıt Yapan</label>
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
                                    <h6 class="mb-0 me-2 text-white"> <i class="ri-history-line fs-4 me-2"></i> Ürün Fiyat Log Listesi <small><?=$result["sayfa_araligi"]?></small></h6>
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
                                                    <td nowrap>Şube</td>
                                                    <td nowrap>Ürün</td>
                                                    <td nowrap>Satış Türü</td>
                                                    <td nowrap>Üst Kategori</td>
                                                    <td nowrap>Kategori</td>
                                                    <td nowrap align="right">Eski Fiyat</td>
                                                    <td nowrap align="right">Fiyat</td>
                                                    <td nowrap>Kayıt Yapan</td>
                                                    <td nowrap align="center">Tarih</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?foreach ($rows as $key => $row) {?>
                                                    <tr>
                                                        <td><?=($key+1)?></td>
                                                        <td><?=$row->SUBE?></td>
                                                        <td><?=$row->URUN?></td>
                                                        <td><?=$row->SATIS_TURU?></td>
                                                        <td><?=$row->UST_KATEGORI?></td>
                                                        <td><?=$row->KATEGORI?></td>
                                                        <td nowrap align="right"><?=FormatSayi::sayi($row->ESKI_FIYAT,2)?> ₺</td>
                                                        <td nowrap align="right"><?=FormatSayi::sayi($row->FIYAT,2)?> ₺</td>
                                                        <td nowrap><?=$row->KAYIT_YAPAN?></td>
                                                        <td nowrap align="center"><?=FormatTarih::tarih($row->TARIH)?></td>
                                                    </tr>
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
<script type="text/javascript">
    
    $("#ust_kategori_id").on("change", function(event) {
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: {ust_kategori_id: $("#ust_kategori_id").val(), controller: "urun", action: "kategori_doldur"},
            dataType: "json",
            success: function(response) {
                if (response.HATA) {
                    $("#kategori_id").html('');
                } else {
                    $("#kategori_id").html(response.HTML);
                }
            }
        });
    });

</script>


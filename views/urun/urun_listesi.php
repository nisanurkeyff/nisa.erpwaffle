<?
	require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
	session_kontrol();

    if(is_null($_REQUEST['durum'])){
        $_REQUEST['durum'] = 1;
    }

    $excel = new excelSayfasi();
    $excel->sutunEkle("Ürün","URUN","");
    $excel->sutunEkle("Üst Kategori","UST_KATEGORI","");
    $excel->sutunEkle("Kategori","KATEGORI","");
    $excel->sutunEkle("Satış Türü","SATIS_TURU","");
    $excel->sutunEkle("Alerjenler","ALERJENLER","");
    $excel->sutunEkle("Açıklama","ACIKLAMA","");
    $excel->sutunEkle("Mağaza Satış Fiyatı (₺)","FIYAT_MAGAZA_TEXT","");
    $excel->sutunEkle("Telefon Sipariş Fiyatı (₺)","FIYAT_TELEFON_TEXT","");
    $excel->sutunEkle("Dış Platform Satış Fiyatı (₺)","FIYAT_DIS_PLATFORM_TEXT","");
    $excel->sutunEkle("Anlık Maliyet (₺)","MALIYET_TEXT","");
    $excel->sutunEkle("Durum","DURUM_TEXT","");
    $excelOut = $excel->excel();
    
    $result             = $cUrun->getUrunler($_REQUEST);
    $rows               = $result['rows'];

    $_SESSION["Table"]  = $result;
    $_SESSION['excel']  = $excelOut;

?>
<!Doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title> <?=$row_site->TITLE?> | Ürün Listesi </title>
        <?=$cTheme->Linkler()?>
    </head>
    <style type="text/css">
        
    </style>
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
                                                    <div class="col-md-4 mb-2">
                                                        <div class="input-group input-group-merge">
                                                            <span class="input-group-text"><i class="ri-restaurant-2-fill"></i></span>
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="text" id="urun" name="urun" class="form-control" value="<?=$_REQUEST['urun']?>" placeholder="Ürün">
                                                                <label>Ürün</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 mb-2">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="kategori_id" id="kategori_id" class="btn select2 form-select" data-style="btn-default">
                                                                <?=$cUrun->Kategoriler()->setSecilen($_REQUEST['kategori_id'])->setSeciniz()->getSelect("ID", "AD")?>
                                                            </select>
                                                            <label>Kategori</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 mb-2">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="durum" id="durum" class="select2 form-select" data-style="btn-default">
                                                                <?=$cKullanici->Durum()->setSecilen()->setSeciniz()->getSelect("ID", "AD")?>
                                                            </select>
                                                            <label>Durum</label>
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
                                    <h6 class="mb-0 me-2 text-white"> <i class="ri-restaurant-2-fill fs-4 me-2"></i> Ürün Listesi <small><?=$result["sayfa_araligi"]?></small></h6>
                                    <div class="card-header-elements ms-auto">
                                        <a href="/views/urun/urun_ekle.php?route=urun/urun_listesi" data-bs-toggle="tooltip" class="btn btn-icon text-white float-right border-white border-radius btn-sm" title="Ürün Ekle"><i class="ri-add-line fs-4"></i></a>
                                        <a href="../excel_sql.php" data-bs-toggle="tooltip" title="Excel" class="btn btn-icon text-white float-right border-white borderd-radius btn-sm"> <i class="ri-file-excel-2-line"></i> </a>
                                    </div>
                                </div>
                                <div class="card-body mt-2">
                                    <div class="card-datatable table-responsive">
                                        <table class="table table-hover table-sm">
                                            <thead class="thead-themed fw-bold py-0">
                                                <tr class="table-primary">
                                                    <td nowrap>#</td>
                                                    <td nowrap align="center">Resim</td>
                                                    <td nowrap>Ürün</td>
                                                    <td nowrap>Kategori</td>
                                                    <td nowrap>Açıklama</td>
                                                    <td nowrap align="right">Mağaza Satış Fiyatı (₺)</td>
                                                    <td nowrap align="right">Telefon Sipariş Fiyatı (₺)</td>
                                                    <td nowrap align="right">Dış Platform Satış Fiyatı (₺)</td>
                                                    <td nowrap align="right">Anlık Maliyet (₺)</td>
                                                    <td nowrap align="center">Durum</td>
                                                    <td nowrap></td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?foreach ($rows as $key => $row) {?>
                                                    <tr>
                                                        <td><?=($key+1)?></td>
                                                        <td align="center">
                                                            <?if(is_file(fncImgPathFolder2($row->RESIM_URL, $row_site->IMG_PATH))){?>
                                                                <img src="<?=fncImgPath($row->RESIM_URL, $row_site->IMG_PATH)?>" class="rounded-3 fancybox" alt="Ürün Resim" height="70">
                                                            <?}else{?>
                                                                <img src="<?=$row_site->LOGO?>" class="rounded-3 fancybox" alt="Menü Yönetim" height="70"/>
                                                            <?}?>
                                                        </td>
                                                        <td><?=FormatYazi::kisalt2($row->URUN,25)?></td>
                                                        <td><?=$row->KATEGORI?></td>
                                                        <td><?=FormatYazi::kisalt2($row->ACIKLAMA)?></td>
                                                        <td nowrap align="right"><?=$row->FIYAT_MAGAZA_TEXT?></td>
                                                        <td nowrap align="right"><?=$row->FIYAT_TELEFON_TEXT?></td>
                                                        <td nowrap align="right"><?=$row->FIYAT_DIS_PLATFORM_TEXT?></td>
                                                        <td nowrap align="right"><a href="javascript:;" class="text-primary fw-bold fncMaliyetGoster" data-urun-id="<?=$row->ID?>" title="Maliyet Detayı"><?=$row->MALIYET_TEXT?></a></td>
                                                        <td align="center"><?=fncDurumSpan($row->DURUM)?></td>
                                                        <td nowrap>
                                                            <a href="/views/urun/urun_duzenle.php?route=urun/urun_listesi&id=<?=$row->ID?>&token=<?=$row->TOKEN?>" data-bs-toggle="tooltip" class="btn btn-primary btn-icon btn-sm" title="Düzenle"> <i class="ri-pencil-line"></i></a>
                                                            <a href="javascript:;" data-bs-toggle="tooltip" class="btn btn-danger btn-icon btn-sm" data-id="<?=$row->ID?>" onclick="fncUrunSil(this)" title="Sil"><i class="ri-delete-bin-5-line"></i></a>
                                                        </td>
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

<div class="modal fade" id="subeUrunEkleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-edit-user">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-body p-0">
                <div class="text-center mb-6">
                    <h4 class="mb-2">Şubeye Ürün Ekleme</h4>
                </div>
                <form id="subeUrunEkle" class="row g-5">
                    <div class="col-md-12 select2-primary">
                        <div class="form-floating form-floating-outline">
                            <select name="sube_ids[]" id="sube_ids2" class="select2 form-select" data-style="btn-default" multiple>
                                <?=$cUrun->Subeler()->setSecilen()->setSeciniz()->getSelect("ID", "AD")?>
                            </select>
                            <label>Şube</label>
                        </div>
                    </div>
                    <div class="col-12 text-right d-flex flex-wrap justify-content-end gap-4 row-gap-4">
                        <button type="submit" class="btn btn-primary">Kaydet</button>
                        <button type="reset" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Kapat">Kapat</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

         <?=$cTheme->Scriptler()?>

<script type="text/javascript">

    fncSecimSec();
    function fncSecimSec(){
        if($("#secim_turu").val() == 1){ //Trafik
            $(".oran").show();
            $(".fiyat").hide();
        }else if($("#secim_turu").val() == 2){
            $(".fiyat").show();
            $(".oran").hide();
        }else{
            $(".oran").hide();
            $(".fiyat").hide();
        }
    }

    function fncDuzenle(obj){
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: {id: $(obj).data("id"), controller: "urun", action: "urun_duzenle"},
            dataType: "json",
            success: function(response) {
                $.unblockUI();
                if (response.HATA) {
                    Swal.fire({title: 'Uyarı!',text: response.ACIKLAMA ,icon: 'warning' ,customClass: {confirmButton: 'btn btn-primary waves-effect waves-light'},buttonsStyling: false});
                } else {
                    location.href = response.URL;
                }
            }
        });
    }

    function fncAdetGuncelle(obj){
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: {id: $(obj).data("id"), adet: $("#adet" + $(obj).data("id")).val(), controller: "urun", action: "adet_guncelle"},
            dataType: "json",
            success: function(response) {
                $.unblockUI();
                if (response.HATA) {
                    notyf.error(response.ACIKLAMA);
                } else {
                    notyf.success(response.ACIKLAMA);
                }
            }
        });
    }

    function fncFirsatGuncelle(obj) {
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: {"id": $(obj).data("id"), "firsat": $(obj).prop("checked") ? 1 : 0, controller: "urun", action: "firsat_kaydet" },
            dataType: 'json',
            success: function(response) {
                $.unblockUI();
                if (response.HATA) {
                    notyf.error(response.ACIKLAMA);
                } else {
                    notyf.success(response.ACIKLAMA);
                }
            }
        });
    }

    function fncYeniGuncelle(obj) {
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: {"id": $(obj).data("id"), "yeni": $(obj).prop("checked") ? 1 : 0, controller: "urun", action: "yeni_kaydet" },
            dataType: 'json',
            success: function(response) {
                $.unblockUI();
                if (response.HATA) {
                    notyf.error(response.ACIKLAMA);
                } else {
                    notyf.success(response.ACIKLAMA);
                }
            }
        });
    }

    function fncEnter(event, id) {
        if (event.key === "Enter" || event.keyCode === 13) {
            fncAdetGuncelle($("button[data-id='" + id + "']")[0]);
        }
    }

    function fncUrunSil(obj){
        sweatAlert("Sildiğiniz Ürün Tüm Şubelerden Kaldırılacaktır. Emin Misiniz?", "Evet, Sil").then(function (result) {
            if (result.value) {
                showSpinner();
                $.ajax({
                    url: "/router.php",
                    type: "POST",
                    data: {id: $(obj).data("id"), controller: "urun", action: "urun_sil"},
                    dataType: "json",
                    success: function(response) {
                        $.unblockUI();
                        if (response.HATA) {
                            notyf.error(response.ACIKLAMA);
                        } else {
                            notyf.success(response.ACIKLAMA);
                            $(obj).closest('tr').fadeOut();
                        }
                    }
                });
            }
        });
    }

    $("#ust_kategori_id").on("change", function(event) {
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: {ust_kategori_id: $("#ust_kategori_id").val(), controller: "urun", action: "kategori_doldur"},
            dataType: "json",
            success: function(response) {
                if (response.HATA) {
                    //$("#kategori_id").html('');
                } else {
                    $("#kategori_id").html(response.HTML);
                }
            }
        });
    });

    $("#subeUrunEkle").on("submit", function(event) {
        event.preventDefault();
        showSpinner();

        let urun_ids = getCheck(".table-check");
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: $(this).serialize() + "&controller=urun&action=sube_urun_kaydet&urun_ids=" + urun_ids,
            dataType: "json",
            success: function(response) {
                $.unblockUI();
                if (response.HATA) {
                    notyf.error(response.ACIKLAMA);
                } else {
                    notyf.success(response.ACIKLAMA);
                    $("#subeUrunEkleModal").modal("hide")
                }
            }
        });
    });

</script>
</body>
</html>



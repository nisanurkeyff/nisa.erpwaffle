<?
	require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
	session_kontrol();

    if(is_null($_REQUEST['durum'])){
        $_REQUEST['durum'] = 1;
    }

    $excel = new excelSayfasi();
    $excel->sutunEkle("Ürün No","ID","");
    $excel->sutunEkle("Ürün","URUN","");
    $excel->sutunEkle("Üst Kategori","UST_KATEGORI","");
    $excel->sutunEkle("Kategori","KATEGORI","");
    $excel->sutunEkle("Satış Türü","SATIS_TURU","");
    $excel->sutunEkle("Eski Fiyat","ESKI_FIYAT","FormatSayi::virgul2");
    $excel->sutunEkle("Fiyat","FIYAT","FormatSayi::virgul2");
    $excel->sutunEkle("Durum","DURUM_TEXT","");
    $excelOut = $excel->excel();
    
    $result             = $cUrun->getSubeUrunler($_REQUEST);
    $rows               = $result['rows'];

    $_SESSION["Table"]  = $result;
    $_SESSION['excel']  = $excelOut;

    // $rows_fiyat     = $cUrun->getSubeFiyat($_REQUEST);
    // foreach($rows_fiyat as $key => $row_fiyat){
    //     $rows_fiyat_index[$row_fiyat->URUN_ID][$row_fiyat->SUBE_ID]    = $row_fiyat;
    // }

    foreach ($rows as $key => $row) {
        $rows_index[$row->KATEGORI][] = $row;
    }
?>
<!Doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title> <?=$row_site->TITLE?> | Menü Listesi </title>
        <?=$cTheme->Linkler()?>
    </head>
    <style type="text/css">
        .bottom-fixed-save {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            text-align: center;
            padding: 10px 0;
        }
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
                                            <form id="formFiyatGuncelle">
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
                                                            <select name="satis_turu_id" id="satis_turu_id" class="btn select2 form-select" data-style="btn-default">
                                                                <?=$cUrun->SatisTürleri()->setSecilen($_REQUEST['satis_turu_id'])->setSeciniz()->getSelect("ID", "AD")?>
                                                            </select>
                                                            <label>Satış Türü</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-4">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="sube_id" id="sube_id" class="btn select2 form-select" data-style="btn-default">
                                                                <?=$cUrun->Subeler()->setSecilen($_REQUEST['sube_id'])->setSeciniz()->getSelect("ID", "AD")?>
                                                            </select>
                                                            <label>Şube</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 mb-4">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="ust_kategori_id" id="ust_kategori_id" class="btn select2 form-select" data-style="btn-default">
                                                                <?=$cUrun->UstKategoriler()->setSecilen($_REQUEST['ust_kategori_id'])->setSeciniz()->getSelect("ID", "AD")?>
                                                            </select>
                                                            <label>Üst Kategori</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-4 select2-primary">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="kategori_ids[]" id="kategori_ids" class="btn select2 form-select" data-style="btn-default" multiple>
                                                                <?=$cUrun->Kategoriler()->setSecilen($_REQUEST['kategori_ids'])->setSeciniz()->getSelect("ID", "AD")?>
                                                            </select>
                                                            <label>Kategori</label>
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
                                                   <div class="col-md-2 mb-4">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="durum" id="durum" class="select2 form-select" data-style="btn-default">
                                                                <?=$cKullanici->Durum()->setSecilen()->setSeciniz()->getSelect("ID", "AD")?>
                                                            </select>
                                                            <label>Durum</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 mt-1">
                                                        <button type="button" class="btn btn-primary" onclick="fncFiltrele(this)">Filtrele</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <form id="topluFiyatGuncelle">
                                <div class="card mb-6" style="position: relative;">
                                    <div class="card-header header-elements bg-primary py-1">
                                        <h6 class="mb-0 me-2 text-white"> 
                                            <i class="ri-restaurant-2-fill fs-4 me-2"></i> Menü Listesi <small><?=$result["sayfa_araligi"]?></small></h6>
                                        <div class="card-header-elements ms-auto">
                                            <a href="javascript:;" class="btn text-white float-right border-white border-radius btn-sm" data-bs-target="#excelFiyatGuncelleModal" data-bs-toggle="modal">Excel Fiyat Güncelle</a>
                                            <a href="javascript:;" class="btn text-white float-right border-white border-radius btn-sm" data-bs-target="#fiyatGuncelleModal" data-bs-toggle="modal">Toplu Fiyat Güncelle</a>
                                            <button type="button" data-bs-toggle="tooltip" class="btn border-white text-white btn-sm" onclick="fncTopluFiyatGuncelle(this)" title="Kaydet">Kaydet</button>
                                            <a href="../excel_sql.php" data-bs-toggle="tooltip" title="Excel" class="btn btn-icon text-white float-right border-white borderd-radius btn-sm"> <i class="ri-file-excel-2-line"></i> </a>
                                        </div>
                                    </div>
                                    <div class="card-body mt-2">
                                        <div class="card-datatable table-responsive">
                                            <table class="table table-hover table-sm">
                                                <thead class="thead-themed fw-bold py-0">
                                                    <tr class="table-primary">
                                                        <td nowrap>#</td>
                                                        <td nowrap>Resim</td>
                                                        <td nowrap>Ürün</td>
                                                        <td nowrap>Üst Kategori</td>
                                                        <td nowrap>Kategori</td>
                                                        <td nowrap>Satış Türü</td>
                                                        <td nowrap align="right">Eski Fiyat</td>
                                                        <td nowrap align="center">Fiyat</td>
                                                        <td nowrap align="center">Durum</td>
                                                        <td></td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?foreach($rows_index as $kategori => $rows){
                                                        $kategori_class = preg_replace('/[^a-zA-Z0-9]/', '_', $kategori);
                                                        ?>
                                                        <tr class="table-dark">
                                                            <td colspan="10" align="center" class="fs-xl" style="position: relative;">
                                                                <?=$kategori?>
                                                                <a href="javascript:void(0)" class="btn btn-dark btn-sm btn-icon" onclick="fncGizle(this)" title="Gizle" data-kategori="<?=$kategori_class?>" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%);">
                                                                    <i class="ri-arrow-down-double-line"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        <?foreach($rows as $key => $row){?>
                                                            <tr class="kategori<?=$kategori_class?>">
                                                                <td><?=($key+1)?></td>
                                                                <td>
                                                                    <?if(is_file(fncImgPathFolder2($row->RESIM_URL, $row_site->IMG_PATH))){?>
                                                                        <img src="<?=fncImgPath($row->RESIM_URL, $row_site->IMG_PATH)?>" class="rounded-3 fancybox" alt="Ürün Resim" height="50">
                                                                    <?}else{?>
                                                                        <img src="<?=$row_site->LOGO?>" class="fancybox" alt="Menü Yönetim" height="50"/>
                                                                    <?}?>
                                                                </td>
                                                                <td><?=$row->URUN?></td>
                                                                <td><?=$row->UST_KATEGORI?></td>
                                                                <td><?=$row->KATEGORI?></td>
                                                                <td><?=$row->SATIS_TURU?></td>
                                                                <td nowrap align="right"><?=FormatSayi::sayi($row->ESKI_FIYAT,2)?> ₺</td>
                                                                <td  align="center">
                                                                    <div class="input-group input-group-merge">
                                                                        <div class="form-floating form-floating-outline">
                                                                            <input type="text" class="form-control decimal text-center" id="fiyat<?=$row->ID?>" name="fiyat[<?=$row->ID?>]" value="<?=FormatSayi::sayi($row->FIYAT)?>" placeholder="6"/>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td align="center"><?=fncDurumSpan($row->DURUM)?></td>
                                                                <td>
                                                                    <a href="javascript:;" tabindex="-1" data-bs-toggle="tooltip" class="btn btn-danger btn-icon btn-sm" data-id="<?=$row->ID?>" data-sube_id="<?=$_REQUEST['sube_id']?>" onclick="fncUrunCikart(this)" title="Menüden Çıkart"><i class="ri-reply-line"></i></a>
                                                                </td>
                                                            </tr>
                                                        <?}?>
                                                    <?}?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="bottom-fixed-save">
                                            <button type="button" data-bs-toggle="tooltip" class="btn btn-primary text-white mb-5" style="width: 180px; margin-left: 200px;" onclick="fncTopluFiyatGuncelle(this)" title="Kaydet">Kaydet</button>
                                        </div>
                                    </div>
                                    <div class="pagination d-flex justify-content-center">
                                        <?=$result['sayfalama']->sayfalamaOlustur();?>
                                    </div>
                                </div>
                            </form>
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

<div class="modal fade" id="fiyatGuncelleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-edit-user">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-body p-0">
                <div class="text-center mb-6">
                    <h4 class="mb-2">Toplu Fiyat Güncelle</h4>
                </div>
                <form id="fiyatGuncelle" class="row g-5">
                    
                    <div class="col-md-12">
                        <div class="form-floating form-floating-outline">
                            <select name="sube_id" id="sube_id2" class="select2 form-select" data-style="btn-default">
                                <?=$cUrun->Subeler()->setSecilen()->setSeciniz()->getSelect("ID", "AD")?>
                            </select>
                            <label>Şube</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <select name="secim_islem" id="secim_islem" class="select2 form-select" data-style="btn-default">
                                <?=$cUrun->SecimIslem()->setSecilen()->setSeciniz()->getSelect("ID", "AD")?>
                            </select>
                            <label>Zam / İndirim</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <select name="secim_turu" id="secim_turu" class="select2 form-select" data-style="btn-default" onchange="fncSecimSec(this)">
                                <?=$cUrun->SecimTuru()->setSecilen()->setSeciniz()->getSelect("ID", "AD")?>
                            </select>
                            <label>Fiyat / Oran</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <select name="kategori_id" id="kategori_id2" class="select2 form-select" data-style="btn-default">
                                <?=$cUrun->Kategoriler()->setSecilen()->setSeciniz()->getSelect("ID", "AD")?>
                            </select>
                            <label>Kategori</label>
                        </div>
                    </div>
                    <div class="col-md-6 oran">
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="ri-percent-line"></i></span>
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control decimal" id="oran" name="oran" placeholder="3"/>
                                <label>Oran</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 fiyat">
                        <div class="input-group input-group-merge">
                            <span class="input-group-text">₺</span>
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control decimal" id="fiyat" name="fiyat" placeholder="40"/>
                                <label>Fiyat</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"></span>
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control decimal" id="yuvarlama" name="yuvarlama" placeholder="5"/>
                                <label>Yuvarlama</label>
                            </div>
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

<div class="modal fade" id="excelFiyatGuncelleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-edit-user">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-body p-0">
                <div class="text-center mb-6">
                    <h4 class="mb-2">Excel Fiyat Güncelle</h4>
                </div>
                <form id="excelFiyatGuncelle" class="row g-5">
                    <div class="col-md-12">
                        <div class="form-floating form-floating-outline">
                            <select name="sube_id" id="sube_id3" class="select2 form-select" data-style="btn-default">
                                <?=$cUrun->Subeler()->setSecilen()->setSeciniz()->getSelect("ID", "AD")?>
                            </select>
                            <label>Şube</label>
                        </div>
                    </div>
                    <div class="col-md-12 col-12">
                        <input type="file" class="form-control" name="excel" id="excel" accept=".xls,.xlsx">
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

<script type="text/javascript">

    $(document).on("keydown", ".decimal", function(e) {
        if (e.key === "Tab") {
            e.preventDefault();

            let inputs = $(".decimal:visible"); // sadece görünür decimal inputlar
            let index = inputs.index(this);

            let nextIndex = e.shiftKey ? index - 1 : index + 1;
            if (nextIndex >= 0 && nextIndex < inputs.length) {
                inputs.eq(nextIndex).focus().select();
            }
        }
    });

    function fncFiltrele(obj) {
        if($("#sube_id").val() <= 0){
            notyf.error("Şube Seçmelisin")
        }else{
            $("#formFiyatGuncelle").submit();
        }
    }

    function fncTopluFiyatGuncelle(obj) {
        let formData = $("#topluFiyatGuncelle").serializeArray();
        formData.push(
            { name: "controller", value: "urun" },
            { name: "action", value: "toplu_fiyat_guncelle" },
            { name: "sube_id", value: "<?= $_REQUEST['sube_id'] ?>" }
        );

        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: formData,
            dataType: "json",
            success: function(response) {
                $.unblockUI();
                if (response.HATA) {
                    notyf.error(response.ACIKLAMA);
                } else {
                    notyf.success(response.ACIKLAMA);
                    location.reload(true);
                }
            }
        });
    }

    function fncGizle(obj) {
        let kategori = $(obj).data("kategori");
        let targetRow = $(`.kategori${kategori}`);
        
        if (targetRow.is(":visible")) {
            targetRow.fadeOut();
        } else {
            targetRow.fadeIn();
        }
    }

    /*
    function fncTopluFiyatGuncelle(obj){
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: $("#topluFiyatGuncelle").serialize(), 'controller' : "urun", 'action' : "toplu_fiyat_guncelle", 'sube_id' : "sube_id" : "<?=$_REQUEST['sube_id']?>",
            dataType: "json",
            success: function(response) {
                if (response.HATA) {
                    notyf.error(response.ACIKLAMA);
                } else {
                    notyf.success(response.ACIKLAMA);
                    location.reload(true);
                }
            }
        });
    }
    */

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

    $("#fiyatGuncelle").on("submit", function(event) {
        event.preventDefault();
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: $(this).serialize() + "&controller=urun&action=fiyat_guncelle",
            dataType: "json",
            success: function(response) {
                $.unblockUI();
                if (response.HATA) {
                    notyf.error(response.ACIKLAMA);
                } else {
                    notyf.success(response.ACIKLAMA);
                    location.reload(true);
                }
            }
        });
    });

    $("#excelFiyatGuncelle").on("submit", function(event) {
        event.preventDefault();
        var formData = new FormData(this);

        formData.append('controller', 'urun');
        formData.append('action', 'excel_fiyat_guncelle');

        showSpinner();

        $.ajax({
            url: '/router.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
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
    });

    $("#satis_turu_id").on("change", function(event) {
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: {satis_turu_id: $("#satis_turu_id").val(), controller: "urun", action: "sube_doldur"},
            dataType: "json",
            success: function(response) {
                if (response.HATA) {
                    //$("#ilce_id").html('');
                } else {
                    $("#sube_id").html(response.HTML);
                }
            }
        });
    });

    function fncUrunCikart(obj){
        sweatAlert("Çıkartmak istediğinizden emin misiniz?", "Evet, Sil").then(function (result) {
            if (result.value) {
                showSpinner();
                $.ajax({
                    url: "/router.php",
                    type: "POST",
                    data: {id: $(obj).data("id"), sube_id: $(obj).data("sube_id"), controller: "urun", action: "sube_urun_cikart"},
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
</script>



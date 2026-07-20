<?
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
    session_kontrol();
    
    $row            = $cUrun->getUrun($_REQUEST);
    fncTokenKontrol($row);

    $rows_recete    = $cUrun->getUrunRecete(array('urun_id' => $row->ID));    
    $rows_resim     = $cUrun->getStokResimler($_REQUEST);
    $rows_malzeme   = $cUrun->getMalzemeler2($_REQUEST);

    $array_malzeme = array();
    if(!empty($row->MALZEME_IDS)){
        $array_malzeme = explode(',',$row->MALZEME_IDS);
    }

    $rows_recete_index = array();
    foreach ($rows_recete as $key => $row_recete) {
        $rows_recete_index[$row_recete->MALZEME_ID] = $row_recete;
    }

?>
<!Doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title> <?=$row_site->TITLE?> | Ürün Düzenle </title>
        <?=$cTheme->Linkler()?>
    </head>
    <style type="text/css">
        /*.bottom-fixed-save {
          position: fixed;
          bottom: 0;
          left: 0;
          width: 100%;
          text-align: center;
          padding: 10px 0;
        }*/
    </style>
    <body>
        <div class="layout-wrapper layout-content-navbar">
            <div class="layout-container">
                <?=$cTheme->Menu()?>
                <div class="layout-page">
                    <?=$cTheme->Header()?>
                    <div class="content-wrapper">
                        <div class="container-xxl flex-grow-1 container-p-y">
                            <div class="row gy-6 gy-md-0">
                                
                                <div class="col-xl-12">
                                    <div class="card mb-6">
                                        <div class="card-header overflow-hidden">
                                            <ul class="nav nav-tabs" role="tablist">
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link waves-effect active" data-bs-toggle="tab" data-bs-target="#tab_urun" role="tab" aria-selected="true">
                                                    <span class="ri-user-line ri-20px d-sm-none"></span><span class="d-none d-sm-block">Ürün Bilgisi</span></button>
                                                </li>
                                                <!-- <li class="nav-item" role="presentation">
                                                    <button class="nav-link waves-effect" data-bs-toggle="tab" data-bs-target="#tab_malzeme" role="tab" aria-selected="true">
                                                    <span class="ri-user-line ri-20px d-sm-none"></span><span class="d-none d-sm-block">Malzemeler</span></button>
                                                </li> -->
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link waves-effect" data-bs-toggle="tab" data-bs-target="#tab_recete" role="tab" aria-selected="true">
                                                    <span class="ri-user-line ri-20px d-sm-none"></span><span class="d-none d-sm-block">Recete</span></button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link waves-effect" data-bs-toggle="tab" data-bs-target="#tab_resim" role="tab" aria-selected="true">
                                                    <span class="ri-user-line ri-20px d-sm-none"></span><span class="d-none d-sm-block">Resimler</span></button>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="tab-content">

                                            <div class="tab-pane fade active show" id="tab_urun" role="tabpanel">
                                                <form id="urunKaydet">
                                                    <input type="hidden" name="id" id="id" value="<?=$row->ID?>">
                                                    <div class="row g-6">
                                                        <div class="col-md-6">
                                                            <div class="input-group input-group-merge">
                                                                <span class="input-group-text"><i class="ri-restaurant-2-fill"></i></span>
                                                                <div class="form-floating form-floating-outline">
                                                                    <input type="text" id="urun" name="urun" class="form-control" value="<?=$row->URUN?>" onchange="this.value=this.value.turkishToUpper();" placeholder="Ürün">
                                                                    <label>Ürün</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="input-group input-group-merge">
                                                                <span class="input-group-text">₺</span>
                                                                <div class="form-floating form-floating-outline">
                                                                    <input type="text" class="form-control decimal" id="fiyat" name="fiyat" value="<?=FormatSayi::sayi($row->FIYAT)?>" placeholder="6"/>
                                                                    <label>Fiyat</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-floating form-floating-outline">
                                                                <select name="hamur_kullanim_id" id="hamur_kullanim_id" class="select2 form-select" data-style="btn-default">
                                                                    <?=$cUrun->HamurKullanim()->setSecilen($row->HAMUR_KULLANIM_ID)->getSelect("ID", "AD")?>
                                                                </select>
                                                                <label>Hamur Kullanım Türü</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-floating form-floating-outline">
                                                                <select name="durum" id="durum" class="select2 form-select" data-style="btn-default">
                                                                    <?=$cKullanici->Durum()->setSecilen($row->DURUM)->getSelect("ID", "AD")?>
                                                                </select>
                                                                <label>Durum</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-floating form-floating-outline">
                                                                <select name="kategori_id" id="kategori_id" class="select2 form-select" data-style="btn-default">
                                                                    <?=$cUrun->Kategoriler()->setSecilen($row->KATEGORI_ID)->setSeciniz()->getSelect("ID", "AD")?>
                                                                </select>
                                                                <label>Kategori</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="input-group input-group-merge">
                                                                <span class="input-group-text"><i class="ri-chat-4-line"></i></span>
                                                                <div class="form-floating form-floating-outline">
                                                                    <textarea class="form-control" id="aciklama" name="aciklama"  placeholder="Açıklama" style="height: 81px;"><?=$row->ACIKLAMA?></textarea>
                                                                    <label for="basic-icon-default-message">Açıklama</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="text-end pt-6">
                                                        <button type="submit" class="btn btn-primary me-4 waves-effect waves-light">Kaydet</button>
                                                        <button type="reset" class="btn btn-outline-secondary waves-effect">Geri Al</button>
                                                    </div>
                                                </form>
                                            </div>

                                            <!-- <div class="tab-pane fade" id="tab_malzeme" role="tabpanel">
                                                <form id="malzemeKaydet">
                                                    <input type="hidden" name="id" id="id" value="<?=$row->ID?>">
                                                    <div class="row g-6">
                                                        <div class="col-md-10 offset-1 text-center">
                                                            <div class="alert alert-info" role="alert">Malzemeler</div>
                                                        </div>
                                                        <div class="col-md-10 offset-1">
                                                            <table class="table">
                                                                <?foreach ($rows_malzeme as $key => $row_malzeme) {?>
                                                                    <tr>
                                                                        <td><?=$row_malzeme->MALZEME?></td>
                                                                        <td align="center">
                                                                            <label class="switch">
                                                                                <input type="checkbox" class="switch-input is-valid" name="malzeme_id[<?=$row->ID?>]" id="malzeme_id<?=$row->ID?>" value="<?=$row_malzeme->ID?>" <?=in_array($row_malzeme->ID, $array_malzeme) ? 'checked' : ''?>>
                                                                                <span class="switch-toggle-slider"><span class="switch-on"></span><span class="switch-off"></span></span>
                                                                            </label>
                                                                        </td>
                                                                    </tr>
                                                                <?}?>
                                                            </table>
                                                        </div>
                                                        <div class="col-md-12 text-center bottom-fixed-save">
                                                            <button type="submit" class="btn btn-primary text-white mb-5" style="width: 180px; margin-left: 200px;">Kaydet</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div> -->

                                            <div class="tab-pane fade" id="tab_recete" role="tabpanel">
                                                <form id="receteKaydet">
                                                    <input type="hidden" name="urun_id" value="<?=$row->ID?>">

                                                    <div class="row g-6">
                                                        <div class="col-md-10 offset-1 text-center">
                                                            <div class="alert alert-primary" role="alert">
                                                                Ürün Reçetesi (Gramaj Tanımı)
                                                            </div>
                                                        </div>

                                                        <div class="col-md-10 offset-1">
                                                            <div class="table-responsive">
                                                                <table class="table table-hover align-middle">
                                                                    <thead class="table-light">
                                                                        <tr>
                                                                            <th>Malzeme</th>
                                                                            <th width="220">Kullanım (Miktar)</th>
                                                                            <th width="120" class="text-center">Aktif</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?foreach ($rows_malzeme as $row_malzeme) { ?>
                                                                            <tr>
                                                                                <td>
                                                                                    <strong><?=$row_malzeme->MALZEME?></strong>
                                                                                    <?if($row_malzeme->BIRIM_KISA_ADI){?>
                                                                                        <small class="text-muted ms-1">(<?=$row_malzeme->BIRIM_KISA_ADI?>)</small>
                                                                                    <?}?>
                                                                                </td>
                                                                                <td>
                                                                                    <div class="input-group input-group-merge">
                                                                                        <input type="text" class="form-control decimal text-end" name="gramaj[<?=$row_malzeme->ID?>]" value="<?=FormatSayi::sayi($rows_recete_index[$row_malzeme->ID]->MIKTAR, is_numeric($row_malzeme->BIRIM_HASSASIYET) ? $row_malzeme->BIRIM_HASSASIYET : 2)?>" placeholder="0.00">
                                                                                        <?if($row_malzeme->BIRIM_KISA_ADI){?>
                                                                                            <span class="input-group-text"><?=$row_malzeme->BIRIM_KISA_ADI?></span>
                                                                                        <?}?>
                                                                                    </div>
                                                                                </td>
                                                                                <td class="text-center">
                                                                                    <div class="form-check form-switch d-flex justify-content-center">
                                                                                        <input class="form-check-input" type="checkbox" name="aktif[<?=$row_malzeme->ID?>]" value="1" <?=in_array($row_malzeme->ID, $array_malzeme) ? 'checked' : ''?>>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                        <?}?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12 text-center bottom-fixed-save">
                                                            <button type="submit" class="btn btn-success px-5">Reçeteyi Kaydet</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>

                                            <div class="tab-pane fade" id="tab_resim" role="tabpanel">

                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="card mb-6">
                                                                <div class="card-header header-elements bg-primary py-3">
                                                                    <h6 class="mb-0 me-2 text-white"> <i class="ri-image-2-line fs-4 me-2"></i> Resimler</h6>
                                                                </div>
                                                                <div class="card-body mt-2">
                                                                    <div class="card-datatable text-nowrap table-responsive">
                                                                        <table class="table table-hover table-sm">
                                                                            <thead class="thead-themed fw-bold py-0">
                                                                                <tr class="table-primary">
                                                                                    <td align="center">Resim</td>
                                                                                    <td>Resim Adı</td>
                                                                                    <td></td>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <?foreach ($rows_resim as $key => $row_resim) {?>
                                                                                    <tr>
                                                                                        <td align="center">
                                                                                            <?if(is_file(fncImgPathFolder2($row_resim->RESIM_URL, $row_site->IMG_PATH))){?>
                                                                                                <img src="<?=fncImgPath($row_resim->RESIM_URL, $row_site->IMG_PATH)?>" class="rounded-3 fancybox" alt="Stok Resim" height="100"/>
                                                                                            <?}else{?>
                                                                                                <img src="<?=$row_site->RESIM_URL?>" class="fancybox" alt="Menü Yönetim" height="70" width="70"/>
                                                                                            <?}?>
                                                                                        </td>
                                                                                        <td><?=FormatYazi::kisalt2($row_resim->RESIM_ADI_ILK)?></td>
                                                                                        <td align="right">
                                                                                            <?if($row_resim->VITRIN == 0){?>
                                                                                                <a href="javascript:;" class="btn btn-outline-success btn-icon btn-sm" data-id="<?=$row_resim->ID?>" onclick="fncVirtinYap(this)" title="Vitrin Yap"><i class="ri-checkbox-circle-line"></i></a>
                                                                                            <?}?>
                                                                                            <a href="javascript:;" class="btn btn-outline-danger btn-icon btn-sm" data-id="<?=$row_resim->ID?>" onclick="fncResimSil(this)" title="Sil"><i class="ri-delete-bin-5-line"></i></a>
                                                                                        </td>
                                                                                    </tr>
                                                                                <?}?>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="card">
                                                                <div class="card-header header-elements bg-primary py-3">
                                                                    <h6 class="mb-0 me-2 text-white"> <i class="ri-image-2-line fs-4 me-2"></i> Resim Yükleme</h6>
                                                                </div>
                                                                <div class="card-body mt-4">
                                                                    <form action="/upload" method="POST" enctype="multipart/form-data" class="dropzone needsclick" id="formResimYukle">
                                                                        <input type="hidden" name="id" id="id" value="<?=$row->ID?>">
                                                                        <div class="dz-message needsclick">
                                                                            Dosyaları buraya bırakın veya yüklemek için tıklayın
                                                                        </div>
                                                                        <div class="fallback">
                                                                            <input name="files[]" type="file" />
                                                                        </div>
                                                                    </form>
                                                                    <div class="text-end">
                                                                        <button id="resimYukle" class="btn btn-primary mt-3">Yükle</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>  
                                                    </div>
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

<script type="text/javascript">

    $("#urunKaydet").on("submit", function(event) {
        event.preventDefault();
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: $(this).serialize() + "&controller=urun&action=urun_kaydet",
            dataType: "json",
            success: function(response) {
                $.unblockUI();
                if (response.HATA) {
                    notyf.error(response.ACIKLAMA);
                } else {
                    notyf.success(response.ACIKLAMA);
                    //location.href = "/views/urun/urun_listesi.php?route=urun/urun_listesi";
                }
            }
        });
    });

    $("#malzemeKaydet").on("submit", function(event) {
        var ids = [];
        $("input[name^='malzeme_id']:checked").each(function() {
            ids.push($(this).val());
        });
        event.preventDefault();
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: $(this).serialize() + "&controller=urun&action=urun_malzeme_kaydet&malzeme_ids=" + ids,
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

    $("#resimYukle").on("click", function (event) {
        event.preventDefault();

        showSpinner();

        var myDropzone = Dropzone.forElement("#formResimYukle");
        var formData = new FormData();
        formData.append("controller", "urun");
        formData.append("action", "resim_yukle");
        formData.append("id", $("#id").val());

        myDropzone.files.forEach((file, index) => {
            formData.append("files[]", file);
        });

        $.ajax({
            url: "/router.php",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function (response) {
                $.unblockUI();
                if (response.HATA) {
                    notyf.error(response.ACIKLAMA);
                } else {
                    notyf.success(response.ACIKLAMA);
                    location.href = "/views/urun/urun_listesi.php?route=urun/urun_listesi";
                }
            }
        });
    });

    function fncResimSil(obj){
        sweatAlert("Emin Misiniz?", "Evet, Sil").then(function (result) {
            if (result.value) {
                showSpinner();
                $.ajax({
                    url: "/router.php",
                    type: "POST",
                    data: {id: $(obj).data("id"), controller: "urun", action: "resim_sil"},
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

    function fncVirtinYap(obj){
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: {id: $(obj).data("id"), controller: "urun", action: "vitrin_yap"},
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

    function fncSubeFiyatKaydet(obj){
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: $("#subeFiyatKaydet").serialize() + "&controller=urun&action=urun_fiyat_kaydet",
            dataType: "json",
            success: function(response) {
                $.unblockUI();
                if (response.HATA) {
                    notyf.error(response.ACIKLAMA);
                } else {
                    notyf.success(response.ACIKLAMA);
                    //location.reload(true);
                }
            }
        });
    }

    $("#receteKaydet").on("submit", function(event) {

        event.preventDefault();
        showSpinner();

        $.ajax({
            url: "/router.php",
            type: "POST",
            data: $(this).serialize() + "&controller=urun&action=urun_recete_kaydet",
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

</script> 
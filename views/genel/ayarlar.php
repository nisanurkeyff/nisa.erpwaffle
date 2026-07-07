<?
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
    session_kontrol();

    $rows_resim     = $cCari->getCariResimler($_REQUEST);
    //var_dump2($row);die;
?>
<!Doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title> <?=$row_site->TITLE?> | Genel Ayarlar </title>
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
                            <div class="row gy-6 gy-md-0">
                                
                                <div class="col-xl-12">
                                    <div class="card mb-6">
                                        <div class="card-header overflow-hidden">
                                            <ul class="nav nav-tabs" role="tablist">
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link waves-effect active" data-bs-toggle="tab" data-bs-target="#tab_ayarlar" role="tab" aria-selected="true">
                                                    <span class="ri-user-line ri-20px d-sm-none"></span><span class="d-none d-sm-block">Firma</span></button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link waves-effect" data-bs-toggle="tab" data-bs-target="#tab_resim" role="tab" aria-selected="true">
                                                    <span class="ri-user-line ri-20px d-sm-none"></span><span class="d-none d-sm-block">Resimler / Bannerler</span></button>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="tab-content">

                                            <div class="tab-pane fade active show" id="tab_ayarlar" role="tabpanel">
                                                <form id="siteKaydet" enctype="multipart/form-data">
                                                    <input type="hidden" name="id" id="id" value="<?=$row_site->ID?>">
                                                    <div class="row g-6">
                                                        <div class="col-md-4">
                                                            <div class="d-flex align-items-start align-items-sm-center gap-6">
                                                                    <img src="<?=$row_site->LOGO?>" class="rounded-3 fancybox d-block w-px-100" alt="logo"/>
                                                                <div class="button-wrapper">
                                                                    <label for="resim" class="btn btn-primary me-3 mb-4" tabindex="0">
                                                                        <span class="d-none d-sm-block">Logo Yükle</span>
                                                                        <i class="ri-upload-2-line d-block d-sm-none"></i>
                                                                        <input type="file" id="resim" name="resim" class="account-file-input" hidden accept="image/png, image/jpeg" onchange="fncResimYukle(this)" />
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="d-flex align-items-start align-items-sm-center gap-6">
                                                                <img src="<?=$row_site->FAVICON?>" class="rounded-3 fancybox d-block w-px-100" alt="favicon"/>
                                                                <div class="button-wrapper">
                                                                    <label for="favicon" class="btn btn-primary me-3 mb-4" tabindex="0">
                                                                        <span class="d-none d-sm-block">Favicon Yükle</span>
                                                                        <i class="ri-upload-2-line d-block d-sm-none"></i>
                                                                        <input type="file" id="favicon" name="favicon" class="account-file-input" hidden accept="image/png, image/jpeg" onchange="fncResimYukle(this)" />
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="input-group input-group-merge">
                                                                <span class="input-group-text"><i class="ri-user-add-line"></i></span>
                                                                <div class="form-floating form-floating-outline">
                                                                    <input type="text" id="firma" name="firma" class="form-control" value="<?=$row_site->FIRMA?>" onchange="this.value=this.value.turkishToUpper();" placeholder="Firma">
                                                                    <label>Firma</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="input-group input-group-merge">
                                                                <span class="input-group-text"><i class="ri-user-line"></i></span>
                                                                <div class="form-floating form-floating-outline">
                                                                    <input type="text" id="ad" name="ad" class="form-control" value="<?=$row_site->AD?>" onchange="this.value=this.value.turkishToUpper();" placeholder="Ad">
                                                                    <label>Ad</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="input-group input-group-merge">
                                                                <span class="input-group-text"><i class="ri-user-line"></i></span>
                                                                <div class="form-floating form-floating-outline">
                                                                    <input type="text" id="soyad" name="soyad" class="form-control" value="<?=$row_site->SOYAD?>" onchange="this.value=this.value.turkishToUpper();" placeholder="Soyad">
                                                                    <label>Soyad</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="text" id="telefon" name="telefon" class="form-control phone-mask" value="<?=$row_site->TELEFON?>" placeholder="587 563 5478"/>
                                                                <label>Telefon</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="mail" id="mail" name="mail" class="form-control mail-invalid" value="<?=$row_site->MAIL?>" placeholder="info@gmail.com"/>
                                                                <label>Email</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-floating form-floating-outline">
                                                                <select name="il_id" id="il_id" class="select2 form-select" data-style="btn-default">
                                                                    <?=$cKullanici->Iller()->setSecilen($row_site->IL_ID)->setSeciniz()->getSelect("ID", "AD")?>
                                                                </select>
                                                                <label>İl</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-floating form-floating-outline">
                                                                <select name="ilce_id" id="ilce_id" class="btn select2 form-select" data-style="btn-default">
                                                                    <?=$cKullanici->Ilceler(array('il_id' => $row_site->IL_ID))->setSecilen($row_site->ILCE_ID)->setSeciniz()->getSelect("ID", "AD")?>
                                                                </select>
                                                                <label>İlçe</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="input-group input-group-merge">
                                                                <span class="input-group-text"><i class="ri-corner-left-up-line"></i></span>
                                                                <div class="form-floating form-floating-outline">
                                                                    <input type="text" id="title" name="title" class="form-control" value="<?=$row_site->TITLE?>" placeholder="Title">
                                                                    <label>Title</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="input-group input-group-merge">
                                                                <span class="input-group-text"><i class="ri-chrome-fill"></i></span>
                                                                <div class="form-floating form-floating-outline">
                                                                    <input type="text" id="qr_url" name="qr_url" class="form-control" value="<?=$row_site->QR_URL?>" placeholder="QR URL">
                                                                    <label>QR URL</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="input-group input-group-merge">
                                                                <span class="input-group-text"><i class="ri-chrome-fill"></i></span>
                                                                <div class="form-floating form-floating-outline">
                                                                    <input type="text" id="yonetim_url" name="yonetim_url" class="form-control" value="<?=$row_site->YONETIM_URL?>" placeholder="Yönetim URL">
                                                                    <label>Yönetim URL</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="text" id="fiyat_degisim_tarih" name="fiyat_degisim_tarih" class="form-control datepicker active" value="<?=$row_site->FIYAT_DEGISIM_TARIH?>" placeholder="YYYY-MM-DD" readonly="readonly">
                                                                <label>Fiyat Değişim Tarihi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="color" name="tema_renk" id="tema_renk" class="form-control form-control-color w-100" value="<?=$row_site->TEMA_RENK?>" title="Tema Rengi">
                                                                <label>Tema Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="input-group input-group-merge">
                                                                <span class="input-group-text"><i class="ri-map-pin-2-line"></i></span>
                                                                <div class="form-floating form-floating-outline">
                                                                    <textarea class="form-control" id="adres" name="adres"  placeholder="Açıklama" style="height: 81px;"><?=$row_site->ADRES?></textarea>
                                                                    <label>Adres</label>
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
                                                                                                <img src="<?=fncImgPath($row_resim->RESIM_URL, $row_site->IMG_PATH)?>" class="rounded-3 fancybox" alt="Stok Resim" height="100" width="100"/>
                                                                                            <?}else{?>
                                                                                                <img src="<?=$row_site->LOGO?>" class="fancybox" alt="Menü Yönetim" height="50"/>
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
                                                                        <div class="dz-message needsclick"> Dosyaları buraya bırakın veya yüklemek için tıklayın</div>
                                                                        <div class="fallback"><input name="files[]" type="file" /></div>
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

    function fncResimYukle(obj) {
        $("#siteKaydet").submit();
    }

    $("#siteKaydet").on("submit", function(event) {
        event.preventDefault();
        var formData = new FormData(this);

        formData.append("controller", "site");
        formData.append("action", "site_kaydet");

        showSpinner();

        $.ajax({
            url: "/router.php",
            type: "POST",
            data: formData,
            dataType: "json",
            processData: false,
            contentType: false,
            success: function(response) {
                $.unblockUI();
                if (response.HATA) {
                    notyf.error(response.ACIKLAMA);
                } else {
                    notyf.success(response.ACIKLAMA);
                    location.reload(true);
                }
            },
            error: function(xhr, status, error) {
                $.unblockUI();
                console.log("Bir hata oluştu: " + error);
                notyf.error("Bir hata oluştu: " + error);
            }
        });
    });

    $("#resimYukle").on("click", function (event) {
        event.preventDefault();

        var myDropzone = Dropzone.forElement("#formResimYukle");
        var formData = new FormData();
        formData.append("controller", "cari");
        formData.append("action", "resim_yukle");
        formData.append("id", $("#id").val());

        myDropzone.files.forEach((file, index) => {
            formData.append("files[]", file);
        });

        showSpinner();

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
                    location.reload(true);
                }
            }
        });
    });

    function fncVirtinYap(obj){
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: {id: $(obj).data("id"), controller: "cari", action: "vitrin_yap"},
            dataType: "json",
            success: function(response) {
                $.unblockUI();
                if (response.HATA) {
                    notyf.error(response.ACIKLAMA);
                } else {
                    notyf.success(response.ACIKLAMA);
                    location.reload(true)
                }
            }
        });
    }

    function fncResimSil(obj){
        sweatAlert("Emin Misiniz?", "Evet, Sil").then(function (result) {
            if (result.value) {
                showSpinner();
                $.ajax({
                    url: "/router.php",
                    type: "POST",
                    data: {id: $(obj).data("id"), controller: "cari", action: "resim_sil"},
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
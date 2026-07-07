<?
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
    session_kontrol();
?>
<!Doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title> <?=$row_site->TITLE?> | Cari Ekle </title>
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
                                                    <button class="nav-link waves-effect active" data-bs-toggle="tab" data-bs-target="#tab_firma" role="tab" aria-selected="true">
                                                    <span class="ri-user-line ri-20px d-sm-none"></span><span class="d-none d-sm-block">Firma Bilgisi</span></button>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="tab-content">
                                            <div class="tab-pane fade active show" id="tab_firma" role="tabpanel">
                                                <form id="cariEkle" enctype="multipart/form-data">
                                                    <div class="row g-6">
                                                        <div class="col-md-6">
                                                            <div class="input-group input-group-merge">
                                                                <span class="input-group-text"><i class="ri-user-add-line"></i></span>
                                                                <div class="form-floating form-floating-outline">
                                                                    <input type="text" id="cari" name="cari" class="form-control" value="<?=$_REQUEST['cari']?>" onchange="this.value=this.value.turkishToUpper();" placeholder="Firma">
                                                                    <label>Firma</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="input-group input-group-merge">
                                                                <span class="input-group-text"><i class="ri-user-line"></i></span>
                                                                <div class="form-floating form-floating-outline">
                                                                    <input type="text" id="ad" name="ad" class="form-control" value="<?=$_REQUEST['ad']?>" onchange="this.value=this.value.turkishToUpper();" placeholder="Ad">
                                                                    <label>Ad</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="input-group input-group-merge">
                                                                <span class="input-group-text"><i class="ri-user-line"></i></span>
                                                                <div class="form-floating form-floating-outline">
                                                                    <input type="text" id="soyad" name="soyad" class="form-control" value="<?=$_REQUEST['soyad']?>" onchange="this.value=this.value.turkishToUpper();" placeholder="Soyad">
                                                                    <label>Soyad</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="text" id="telefon" name="telefon" class="form-control phone-mask" value="<?=$_REQUEST['telefon']?>" placeholder="587 563 5478"/>
                                                                <label>Telefon</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="mail" id="mail" name="mail" class="form-control mail-invalid" value="<?=$_REQUEST['mail']?>" placeholder="info@gmail.com"/>
                                                                <label>Email</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-floating form-floating-outline">
                                                                <select name="il_id" id="il_id" class="select2 form-select" data-style="btn-default">
                                                                    <?=$cKullanici->Iller()->setSecilen($_REQUEST['il_id'])->setSeciniz()->getSelect("ID", "AD")?>
                                                                </select>
                                                                <label>İl</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-floating form-floating-outline">
                                                                <select name="ilce_id" id="ilce_id" class="btn select2 form-select" data-style="btn-default">
                                                                </select>
                                                                <label>İlçe</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-6">
                                                            <input type="file" class="form-control" name="resim" id="resim">
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="input-group input-group-merge">
                                                                <span class="input-group-text"><i class="ri-map-pin-2-line"></i></span>
                                                                <div class="form-floating form-floating-outline">
                                                                    <textarea class="form-control" id="adres" name="adres"  placeholder="Açıklama" style="height: 81px;"><?=$_REQUEST['adres']?></textarea>
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

    $("#cariEkle").on("submit", function(event) {
        event.preventDefault();
        var formData = new FormData(this);
        formData.append("controller", "cari");
        formData.append("action", "cari_ekle");

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
                    location.href = response.URL;
                }
            },
            error: function(xhr, status, error) {
                notyf.error("Bir hata oluştu: " + error);
            }
        });
    });

</script>
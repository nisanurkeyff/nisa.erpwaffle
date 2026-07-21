<?
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
    session_kontrol();

    if(is_null($_REQUEST['durum'])){
        $_REQUEST['durum'] = 1;
    }
?>
<!Doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title> <?=$row_site->TITLE?> | Ürün Ekle </title>
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
                                                    <button class="nav-link waves-effect active" data-bs-toggle="tab" data-bs-target="#tab_urun" role="tab" aria-selected="true">
                                                    <span class="ri-user-line ri-20px d-sm-none"></span><span class="d-none d-sm-block">Ürün Bilgisi</span></button>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="tab-content">
                                            <div class="tab-pane fade active show" id="tab_urun" role="tabpanel">
                                                <form id="urunEkle">
                                                    <div class="row g-6">
                                                        <div class="col-md-6">
                                                            <div class="input-group input-group-merge">
                                                                <span class="input-group-text"><i class="ri-restaurant-2-fill"></i></span>
                                                                <div class="form-floating form-floating-outline">
                                                                    <input type="text" id="urun" name="urun" class="form-control" value="<?=$_REQUEST['urun']?>" onchange="this.value=this.value.turkishToUpper();" placeholder="Ürün">
                                                                    <label>Ürün</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="input-group input-group-merge">
                                                                <span class="input-group-text">₺</span>
                                                                <div class="form-floating form-floating-outline">
                                                                    <input type="text" class="form-control decimal" id="fiyat_magaza" name="fiyat_magaza" value="<?=$_REQUEST['fiyat_magaza']?>" placeholder="0.00"/>
                                                                    <label>Mağaza Satış Fiyatı</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="input-group input-group-merge">
                                                                <span class="input-group-text">₺</span>
                                                                <div class="form-floating form-floating-outline">
                                                                    <input type="text" class="form-control decimal" id="fiyat_telefon" name="fiyat_telefon" value="<?=$_REQUEST['fiyat_telefon']?>" placeholder="0.00"/>
                                                                    <label>Telefon Sipariş Fiyatı</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="input-group input-group-merge">
                                                                <span class="input-group-text">₺</span>
                                                                <div class="form-floating form-floating-outline">
                                                                    <input type="text" class="form-control decimal" id="fiyat_dis_platform" name="fiyat_dis_platform" value="<?=$_REQUEST['fiyat_dis_platform']?>" placeholder="0.00"/>
                                                                    <label>Dış Platform Satış Fiyatı</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-floating form-floating-outline">
                                                                <select name="kategori_id" id="kategori_id" class="select2 form-select" data-style="btn-default">
                                                                    <?=$cUrun->Kategoriler()->setSecilen($_REQUEST['kategori_id'])->setSeciniz()->getSelect("ID", "AD")?>
                                                                </select>
                                                                <label>Kategori</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-floating form-floating-outline">
                                                                <select name="hamur_kullanim_id" id="hamur_kullanim_id" class="select2 form-select" data-style="btn-default">
                                                                    <?=$cUrun->HamurKullanim()->setSecilen($_REQUEST["hamur_kullanim_id"])->getSelect("ID", "AD")?>
                                                                </select>
                                                                <label>Hamur Kullanım Türü</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-floating form-floating-outline">
                                                                <select name="durum" id="durum" class="select2 form-select" data-style="btn-default">
                                                                    <?=$cKullanici->Durum()->setSecilen($_REQUEST['durum'])->getSelect("ID", "AD")?>
                                                                </select>
                                                                <label>Durum</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="input-group input-group-merge">
                                                                <span class="input-group-text"><i class="ri-chat-4-line"></i></span>
                                                                <div class="form-floating form-floating-outline">
                                                                    <textarea class="form-control" id="aciklama" name="aciklama"  placeholder="Açıklama" style="height: 81px;"><?=$_REQUEST['aciklama']?></textarea>
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


    $("#urunEkle").on("submit", function(event) {
        event.preventDefault();
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: $(this).serialize() + "&controller=urun&action=urun_ekle",
            dataType: "json",
            success: function(response) {
                $.unblockUI();
                if (response.HATA) {
                    notyf.error(response.ACIKLAMA);
                } else {
                    notyf.success(response.ACIKLAMA);
                    location.href = response.URL;
                }
            }
        });
    });

</script>
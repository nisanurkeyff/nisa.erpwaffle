<?
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
    session_kontrol();
    
    $row            = $cSube->getSube($_REQUEST);
    fncTokenKontrol($row);

?>
<!Doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title> <?=$row_site->TITLE?> | Şube Düzenle </title>
        <?=$cTheme->Linkler()?>
        <link rel="stylesheet" href="/assets/css/bootstrap-duallistbox.css" />
    </head>
    <style type="text/css">
        .moveall,
        .removeall {
         border: 1px solid #ccc !important;
        }
        .moveall:hover,
        .removeall:hover {
          background: #efefef;
        }
        .moveall::after {
          content: attr(title);
          
        }
        .removeall::after {
          content: attr(title);
        }
        .form-control option {
            padding: 10px;
            border-bottom: 1px solid #efefef;
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
                            <div class="row gy-6 gy-md-0">
                                
                                <div class="col-xl-12">
                                    <div class="card mb-6">
                                        <div class="card-header overflow-hidden">
                                            <ul class="nav nav-tabs" role="tablist">
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link waves-effect active" data-bs-toggle="tab" data-bs-target="#tab_sube" role="tab" aria-selected="true">
                                                    <span class="ri-user-line ri-20px d-sm-none"></span><span class="d-none d-sm-block">Şube Bilgisi</span></button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link waves-effect" data-bs-toggle="tab" data-bs-target="#tab_menu" role="tab" aria-selected="true">
                                                    <span class="ri-user-line ri-20px d-sm-none"></span><span class="d-none d-sm-block">Menü Bilgisi</span></button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link waves-effect" data-bs-toggle="tab" data-bs-target="#tab_sube_menu_kopyala" role="tab" aria-selected="true">
                                                    <span class="ri-user-line ri-20px d-sm-none"></span><span class="d-none d-sm-block">Şube Menü Kopyala</span></button>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="tab-content">

                                            <div class="tab-pane fade active show" id="tab_sube" role="tabpanel">
                                                <form id="musteriKaydet">
                                                    <input type="hidden" name="id" id="id" value="<?=$row->ID?>">
                                                    <div class="row g-6">
                                                        <div class="col-md-12">
                                                            <div class="input-group input-group-merge">
                                                                <span class="input-group-text"><i class="ri-user-add-line"></i></span>
                                                                <div class="form-floating form-floating-outline">
                                                                    <input type="text" id="sube" name="sube" class="form-control" value="<?=$row->SUBE?>" onchange="this.value=this.value.turkishToUpper();" placeholder="Şube">
                                                                    <label>Şube</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="form-floating form-floating-outline">
                                                                <select name="il_id" id="il_id" class="select2 form-select" data-style="btn-default">
                                                                    <?=$cKullanici->Iller()->setSecilen($row->IL_ID)->setSeciniz()->getSelect("ID", "AD")?>
                                                                </select>
                                                                <label for="country-modern">İl</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="form-floating form-floating-outline">
                                                                <select name="ilce_id" id="ilce_id" class="btn select2 form-select" data-style="btn-default">
                                                                    <?=$cKullanici->Ilceler(array('il_id' => $row->IL_ID))->setSecilen($row->ILCE_ID)->setSeciniz()->getSelect("ID", "AD")?>
                                                                </select>
                                                                <label for="country-modern">İlçe</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-floating form-floating-outline">
                                                                <select name="satis_turu_id" id="satis_turu_id" class="select2 form-select" data-style="btn-default">
                                                                    <?=$cSube->SatisTürleri()->setSeciniz()->setSecilen($row->SATIS_TURU_ID)->getSelect("ID", "AD")?>
                                                                </select>
                                                                <label>Satış Türü</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-floating form-floating-outline">
                                                                <select name="banner_durum" id="banner_durum" class="select2 form-select" data-style="btn-default">
                                                                    <?=$cKullanici->Durum()->setSecilen($row->BANNER_DURUM)->getSelect("ID", "AD")?>
                                                                </select>
                                                                <label>Banner Durum</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="input-group input-group-merge">
                                                                <span class="input-group-text"><i class="ri-corner-left-up-line"></i></span>
                                                                <div class="form-floating form-floating-outline">
                                                                    <input type="text" id="banner_baslik" name="banner_baslik" class="form-control" value="<?=$row->BANNER_BASLIK?>" placeholder="Banner Başlık">
                                                                    <label>Banner Başlık</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="input-group input-group-merge">
                                                                <span class="input-group-text"><i class="ri-corner-left-up-line"></i></span>
                                                                <div class="form-floating form-floating-outline">
                                                                    <input type="text" id="banner_icerik" name="banner_icerik" class="form-control" value="<?=$row->BANNER_ICERIK?>" placeholder="Banner İçerik">
                                                                    <label>Banner İçerik</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="input-group input-group-merge">
                                                                <span class="input-group-text"><?=$row_site->QR_URL?></span>
                                                                <div class="form-floating form-floating-outline">
                                                                    <input type="text" id="url" name="url" class="form-control" value="<?=$row->URL?>" onchange="this.value=this.value.englishToLower();" placeholder="Menü URL">
                                                                    <label>Menü URL</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-floating form-floating-outline">
                                                                <select name="durum" id="durum" class="select2 form-select" data-style="btn-default">
                                                                    <?=$cKullanici->Durum()->setSeciniz()->setSecilen($row->DURUM)->getSelect("ID", "AD")?>
                                                                </select>
                                                                <label>Durum</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="input-group input-group-merge">
                                                                <span class="input-group-text"><i class="ri-chat-4-line"></i></span>
                                                                <div class="form-floating form-floating-outline">
                                                                    <textarea class="form-control" id="adres" name="adres"  placeholder="Adres" style="height: 81px;"><?=$row->ADRES?></textarea>
                                                                    <label for="basic-icon-default-message">Adres</label>
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

                                            <div class="tab-pane fade" id="tab_menu" role="tabpanel">
                                                <form id="menuKaydet">
                                                    <input type="hidden" name="id" id="id" value="<?=$row->ID?>">
                                                        <div class="row g-6">
                                                            <div class="col-md-6 text-center">
                                                                <div class="alert alert-success" role="alert">Üst Kategori Bazlı Ürün Aktarma</div>
                                                            </div>
                                                            <div class="col-md-6 text-center">
                                                                <div class="alert alert-danger" role="alert">Üst Kategori Bazlı Ürün Çıkartma</div>
                                                            </div>
                                                            <div class="col-6 col-md-6 select2-primary">
                                                                <div class="form-floating form-floating-outline">
                                                                    <select name="aktarma_ust_kategori_ids[]" id="aktarma_ust_kategori_ids" class="select2 form-select" data-style="btn-default" multiple>
                                                                        <?=$cSube->UstKategoriler()->setSecilen()->getSelect("ID", "AD")?>
                                                                    </select>
                                                                    <label>Üst Kategoriler</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-6 col-md-6 select2-primary">
                                                                <div class="form-floating form-floating-outline">
                                                                    <select name="cikartma_ust_kategori_ids[]" id="cikartma_ust_kategori_ids" class="select2 form-select" data-style="btn-default" multiple>
                                                                        <?=$cSube->UstKategoriler()->setSecilen()->getSelect("ID", "AD")?>
                                                                    </select>
                                                                    <label>Üst Kategoriler</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-6 col-md-6 text-center">
                                                                <button type="button" class="btn btn-success mt-1 waves-effect waves-light" data-id=<?=$row->ID?> onclick="fncUstKategoriAktar(this)">Üst Kategorileri Aktar</button>
                                                            </div>
                                                            <div class="col-6 col-md-6 text-center">
                                                                <button type="button" class="btn btn-danger mt-1 waves-effect waves-light" data-id=<?=$row->ID?> onclick="fncUstKategoriCikart(this)">Üst Kategorileri Çıkart</button>
                                                            </div>

                                                            <div class="col-md-6 text-center">
                                                                <div class="alert alert-info" role="alert">Kategori Bazlı Ürün Aktarma</div>
                                                            </div>
                                                            <div class="col-md-6 text-center">
                                                                <div class="alert alert-warning" role="alert">Kategori Bazlı Ürün Çıkartma</div>
                                                            </div>
                                                            <div class="col-6 col-md-6 select2-primary">
                                                                <div class="form-floating form-floating-outline">
                                                                    <select name="aktarma_kategori_ids[]" id="aktarma_kategori_ids" class="select2 form-select" data-style="btn-default" multiple>
                                                                        <?=$cSube->Kategoriler()->setSecilen()->getSelect("ID", "AD")?>
                                                                    </select>
                                                                    <label>Kategoriler</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-6 col-md-6 select2-primary">
                                                                <div class="form-floating form-floating-outline">
                                                                    <select name="cikartma_kategori_ids[]" id="cikartma_kategori_ids" class="select2 form-select" data-style="btn-default" multiple>
                                                                        <?=$cSube->Kategoriler()->setSecilen()->getSelect("ID", "AD")?>
                                                                    </select>
                                                                    <label>Kategoriler</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-6 col-md-6 text-center">
                                                                <button type="button" class="btn btn-info mt-1 waves-effect waves-light" data-id=<?=$row->ID?> onclick="fncKategoriAktar(this)">Kategorileri Aktar</button>
                                                            </div>
                                                            <div class="col-6 col-md-6 text-center">
                                                                <button type="button" class="btn btn-warning mt-1 waves-effect waves-light" data-id=<?=$row->ID?> onclick="fncKategoriCikart(this)">Kategorileri Çıkart</button>
                                                            </div>

                                                            

                                                            <div class="col-md-10 offset-1 text-center">
                                                                <div class="alert alert-primary" role="alert">Ürünler</div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <select name="urun_ids[]" id="urun_ids" size="10" multiple="multiple" title="urun_ids[]" style="height: 500px;">
                                                                    <?=$cSube->Urunler(array('satis_turu_id' => $row->SATIS_TURU_ID))->setSecilen($row->URUN_IDS)->getSelect("ID", "AD")?>
                                                                </select>
                                                            </div>
                                                            <div class="text-end pt-6">
                                                                <button type="submit" class="btn btn-primary me-4 waves-effect waves-light">Ürünleri Kaydet</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>

                                                <div class="tab-pane fade" id="tab_sube_menu_kopyala" role="tabpanel">
                                                <form id="menuKaydet">
                                                    <input type="hidden" name="id" id="id" value="<?=$row->ID?>">
                                                        <div class="row g-6">
                                                            <div class="col-md-10 offset-1 text-center">
                                                                <div class="alert alert-warning" role="alert">Şube Bazlı Menü Kopyalama</div>
                                                            </div>
                                                            <div class="col-10 col-md-10 select2-primary">
                                                                <div class="form-floating form-floating-outline">
                                                                    <select name="sube_id" id="sube_id" class="btn select2 form-select" data-style="btn-default">
                                                                        <?=$cSube->Subeler(array('satis_turu_id' => $row->SATIS_TURU_ID))->setSecilen()->setSeciniz()->getSelect("ID", "AD")?>
                                                                    </select>
                                                                    <label>Şube</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-2 col-md-2 mb-6 text-center">
                                                                <button type="button" class="btn btn-warning mt-1 waves-effect waves-light" data-id=<?=$row->ID?> onclick="fncMenuKopyala(this)">Menüyü Kopyala</button>
                                                            </div>
                                                        </div>
                                                    </form>
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
        <script src="/assets/js/jquery.bootstrap-duallistbox.js"></script>
    </body>
</html>

<script type="text/javascript">

    var demo1 = $('select[name="urun_ids[]"]').bootstrapDualListbox({
        nonSelectedListLabel: 'Ürünler',
        selectedListLabel: 'Menüdeki Ürünler',
        moveAllLabel: 'Hepsini Aktar',
        removeAllLabel: 'Hepsini Geri Al'
    });

    $("#musteriKaydet").on("submit", function(event) {
        event.preventDefault();
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: $(this).serialize() + "&controller=sube&action=sube_kaydet",
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
    });

    $("#menuKaydet").on("submit", function(event) {
        event.preventDefault();
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: $(this).serialize() + "&controller=sube&action=sube_urun_kaydet",
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

    function fncKategoriAktar(obj){
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: {id: $(obj).data("id"), kategori_ids: $("#aktarma_kategori_ids").val(), controller: "sube", action: "sube_menu_kategori_sec"},
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

    function fncKategoriCikart(obj){
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: {id: $(obj).data("id"), kategori_ids: $("#cikartma_kategori_ids").val(), controller: "sube", action: "sube_menu_kategori_cikart"},
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

    function fncUstKategoriAktar(obj){
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: {id: $(obj).data("id"), ust_kategori_ids: $("#aktarma_ust_kategori_ids").val(), controller: "sube", action: "sube_menu_ust_kategori_sec"},
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

    function fncUstKategoriCikart(obj){
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: {id: $(obj).data("id"), ust_kategori_ids: $("#cikartma_ust_kategori_ids").val(), controller: "sube", action: "sube_menu_ust_kategori_cikart"},
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

    function fncMenuKopyala(obj){
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: {id: $(obj).data("id"), sube_id: $("#sube_id").val(), controller: "sube", action: "sube_menu_kopyala"},
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

    function turkishToEnglishLower(str) {
        const charMap = {
            'İ': 'i', 'I': 'i', 'Ş': 's', 'ş': 's',
            'Ğ': 'g', 'ğ': 'g', 'Ü': 'u', 'ü': 'u',
            'Ö': 'o', 'ö': 'o', 'Ç': 'c', 'ç': 'c'
        };

        return str
            .replace(/[İIŞşĞğÜüÖöÇç]/g, match => charMap[match])
            .toLowerCase()
            .replace(/\s+/g, '-'); // Boşluk varsa - yap
    }

    document.addEventListener('DOMContentLoaded', function () {
        const urlInput = document.getElementById('url');
        urlInput.addEventListener('change', function () {
            this.value = turkishToEnglishLower(this.value);
        });
    });

</script> 
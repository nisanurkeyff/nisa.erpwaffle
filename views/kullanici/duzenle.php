<?
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
    session_kontrol();
    
    $row                    = $cKullanici->getKullanici($_REQUEST);
    fncTokenKontrol($row);
    $rows_avatar            = $cKullanici->getKullaniciAvatarlar($_REQUEST);
    
?>
<!Doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title> <?=$row_site->TITLE?> | Kullanıcılar </title>
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
                                <!-- User Sidebar -->
                                <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
                                    <!-- User Card -->
                                    <div class="card mb-6">
                                        <div class="card-body pt-12">
                                            <div class="user-avatar-section">
                                                <div class="d-flex align-items-center flex-column">
                                                    <img class="img-fluid rounded-3 mb-4" src="<?=$row->AVATAR?>" height="120" width="120" alt="User avatar" />
                                                    <div class="user-info text-center">
                                                        <h5><?=$row->AD?> <?=$row->SOYAD?></h5>
                                                        <span class="badge bg-label-danger rounded-pill"><?=$row->YETKI?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--
                                            <div class="d-flex justify-content-around flex-wrap my-6 gap-0 gap-md-3 gap-lg-4">
                                                <div class="d-flex align-items-center me-5 gap-4">
                                                    <div class="avatar">
                                                        <div class="avatar-initial bg-label-primary rounded-3">
                                                            <i class="ri-check-line ri-24px"></i>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <h5 class="mb-0">1.23k</h5>
                                                        <span>Task Done</span>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center gap-4">
                                                    <div class="avatar">
                                                        <div class="avatar-initial bg-label-primary rounded-3">
                                                            <i class="ri-briefcase-line ri-24px"></i>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <h5 class="mb-0">568</h5>
                                                        <span>Project Done</span>
                                                    </div>
                                                </div>
                                            </div>
                                            -->
                                            <h5 class="pb-4 border-bottom mb-4">Bilgiler</h5>
                                            <div class="info-container">
                                                <ul class="list-unstyled mb-6">
                                                    <li class="mb-2">
                                                        <span class="fw-medium text-heading me-2">Ad Soyad:</span>
                                                        <span><?=$row->AD?> <?=$row->SOYAD?></span>
                                                    </li>
                                                    <li class="mb-2">
                                                        <span class="fw-medium text-heading me-2">Mail:</span>
                                                        <span><?=$row->MAIL?></span>
                                                    </li>
                                                    <li class="mb-2">
                                                        <span class="fw-medium text-heading me-2">Durum:</span>
                                                        <?=fncDurumSpan($row->DURUM)?>
                                                    </li>
                                                    <li class="mb-2">
                                                        <span class="fw-medium text-heading me-2">Yetki:</span>
                                                        <span><?=$row->YETKI?></span>
                                                    </li>
                                                    <li class="mb-2">
                                                        <span class="fw-medium text-heading me-2">Telefon:</span>
                                                        <span><?=$row->TELEFON?></span>
                                                    </li>
                                                    <li class="mb-2">
                                                        <span class="fw-medium text-heading me-2">İl / İlçe:</span>
                                                        <span><?=$row->IL?> / <?=$row->ILCE?></span>
                                                    </li>
                                                </ul>
                                                <div class="d-flex justify-content-center">
                                                    <a href="javascript:;" class="btn btn-primary me-4" data-id="<?=$row->ID?>" onclick="fncKullaniciBilgisi(this)" data-bs-target="#kullaniciDuzenleModal" data-bs-toggle="modal">Düzenle</a>
                                                    <?if($row->DURUM == 1){?>
                                                        <a href="javascript:;" data-id="<?=$row->ID?>" onclick="fncKullaniciPasifEt(this)" class="btn btn-danger suspend-user">Pasif Et</a>
                                                    <?}else{?>
                                                        <a href="javascript:;" data-id="<?=$row->ID?>" onclick="fncKullaniciAktifEt(this)" class="btn btn-success suspend-user">Aktif Et</a>
                                                    <?}?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-8">
                                    <div class="nav-align-top mb-6">
                                        <ul class="nav nav-pills mb-4 nav-fill" role="tablist">
                                            <li class="nav-item mb-1 mb-sm-0 active">
                                                <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#tab_avatar" aria-controls="tab_avatar" aria-selected="false"><i class="tf-icons ri-user-3-line me-2"></i> Avatar</button>
                                            </li>
                                        </ul>

                                        <div class="tab-content">
                                            <div class="tab-pane fade active show" id="tab_avatar" role="tabpanel">
                                                <form id="avatarKaydet">
                                                    <input type="hidden" name="id" value="<?=$row->ID?>">
                                                    <input type="hidden" name="avatar_id" id="avatar_id" value="">

                                                    <div class="row g-5">
                                                        <div class="col-sm-12 col-md-12">
                                                            <div class="row text-center">
                                                                <?foreach ($rows_avatar as $key => $row_avatar){
                                                                    $row_avatar->ID == $row->AVATAR_ID ? $class = "btn-success" : $class = "btn-label-success";
                                                                    ?>
                                                                    <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3">
                                                                        <img class="<?=$row_avatar->CLASS?>" src="<?=$row_avatar->AVATAR?>" alt="<?=$row_avatar->ALT?>" height="100" width="100"/>
                                                                        <button type="button" id="avatar_sec_button<?=$row_avatar->ID?>" class="btn <?=$class?> mt-2" data-id="<?=$row_avatar->ID?>" onclick="fncAvatarSec(this)">Seç</button>
                                                                    </div>
                                                                <?}?>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 text-end">
                                                            <button type="submit" class="btn btn-primary">Kaydet</button>
                                                        </div>
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

<div class="modal fade" id="kullaniciDuzenleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-edit-user">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-body p-0">
                <div class="text-center mb-6">
                    <h4 class="mb-2">Kullanıcı Düzenle</h4>
                </div>
                <form id="kullaniciKaydet" class="row g-5">
                    <input type="hidden" name="id" value="<?=$row->ID?>">
                    <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="ad" name="ad" class="form-control" maxlength="45"/>
                            <label>Ad</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="soyad" name="soyad" class="form-control" maxlength="45"/>
                            <label>Soyad</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="mail" name="mail" class="form-control" maxlength="45"/>
                            <label>Mail</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline">
                            <select name="durum" id="durum" class="select2 form-select" data-style="btn-default">
                                <?=$cKullanici->Durum()->setSecilen()->setSeciniz()->getSelect("ID", "AD")?>
                            </select>
                            <label>Durum</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline">
                            <select name="yetki_id" id="yetki_id" class="select2 form-select" data-style="btn-default">
                                <?=$cKullanici->Yetkiler()->setSecilen()->setSeciniz()->getSelect("ID", "AD")?>
                            </select>
                            <label>Yetki</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="telefon" name="telefon" class="form-control phone-number-mask"/>
                            <label>Telefon</label>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-floating form-floating-outline">
                            <select name="il_id" id="il_id" class="select2 form-select" data-style="btn-default">
                                <?=$cKullanici->Iller()->setSecilen()->setSeciniz()->getSelect("ID", "AD")?>
                            </select>
                            <label for="country-modern">İl</label>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-floating form-floating-outline">
                            <select name="ilce_id" id="ilce_id" class="btn select2 form-select" data-style="btn-default">
                                <?=$cKullanici->Ilceler()->setSecilen()->setSeciniz()->getSelect("ID", "AD")?>
                            </select>
                            <label for="country-modern">İlçe</label>
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

<script type="text/javascript">

    function fncKullaniciBilgisi(obj){
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: {id: $(obj).data("id"), controller: "kullanici", action: "kullanici_bilgisi"},
            dataType: "json",
            success: function(response) {
                $.unblockUI();
                if (response.HATA) {
                    Swal.fire({title: 'Uyarı!',text: response.ACIKLAMA ,icon: 'warning' ,customClass: {confirmButton: 'btn btn-primary waves-effect waves-light'},buttonsStyling: false});
                } else {
                    $("#kullaniciDuzenleModal #ad").val(response.ROW.AD);
                    $("#kullaniciDuzenleModal #soyad").val(response.ROW.SOYAD);
                    $("#kullaniciDuzenleModal #mail").val(response.ROW.MAIL);
                    $("#kullaniciDuzenleModal #durum").val(response.ROW.DURUM).trigger('change');
                    $("#kullaniciDuzenleModal #yetki_id").val(response.ROW.YETKI_ID).trigger('change');
                    $("#kullaniciDuzenleModal #telefon").val(response.ROW.TELEFON);
                    $("#kullaniciDuzenleModal #il_id").val(response.ROW.IL_ID).trigger('change');
                    $("#kullaniciDuzenleModal #ilce_id").val(response.ROW.ILCE_ID).trigger('change');
                }
            }
        });
    }

    function fncKullaniciPasifEt(obj){
        sweatAlert("Emin Misiniz?", "Evet, Pasif Et").then(function (result) {
            if (result.value) {
                showSpinner();
                $.ajax({
                    url: "/router.php",
                    type: "POST",
                    data: {id: $(obj).data("id"), controller: "kullanici", action: "kullanici_pasif_et"},
                    dataType: "json",
                    success: function(response) {
                        $.unblockUI();
                        if (response.HATA) {
                            Swal.fire({title: 'Uyarı!',text: response.ACIKLAMA ,icon: 'warning' ,customClass: {confirmButton: 'btn btn-primary waves-effect waves-light'},buttonsStyling: false});
                        } else {
                            Swal.fire({title: 'Başarılı!',text: response.ACIKLAMA ,icon: 'success' ,customClass: {confirmButton: 'btn btn-primary waves-effect waves-light'},buttonsStyling: false});
                            location.reload(true);
                        }
                    }
                });
            }
        });
    }

    function fncKullaniciAktifEt(obj){
        sweatAlert("Emin Misiniz?", "Evet, Aktif Et").then(function (result) {
            if (result.value) {
                showSpinner();
                $.ajax({
                    url: "/router.php",
                    type: "POST",
                    data: {id: $(obj).data("id"), controller: "kullanici", action: "kullanici_aktif_et"},
                    dataType: "json",
                    success: function(response) {
                        $.unblockUI();
                        if (response.HATA) {
                            Swal.fire({title: 'Uyarı!',text: response.ACIKLAMA ,icon: 'warning' ,customClass: {confirmButton: 'btn btn-primary waves-effect waves-light'},buttonsStyling: false});
                        } else {
                            Swal.fire({title: 'Başarılı!',text: response.ACIKLAMA ,icon: 'success' ,customClass: {confirmButton: 'btn btn-primary waves-effect waves-light'},buttonsStyling: false});
                            location.reload(true);
                        }
                    }
                });
            }
        });
    }

    $("#kullaniciKaydet").on("submit", function(event) {
        event.preventDefault();
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: $(this).serialize() + "&controller=kullanici&action=kullanici_kaydet",
            dataType: "json",
            success: function(response) {
                $.unblockUI();
                if (response.HATA) {
                    Swal.fire({title: 'Uyarı!',text: response.ACIKLAMA ,icon: 'warning' ,customClass: {confirmButton: 'btn btn-primary waves-effect waves-light'},buttonsStyling: false});
                } else {
                    Swal.fire({title: 'Başarılı!',text: response.ACIKLAMA ,icon: 'success' ,customClass: {confirmButton: 'btn btn-primary waves-effect waves-light'},buttonsStyling: false});
                    location.reload(true);
                }
            }
        });
    });

    $("#avatarKaydet").on("submit", function(event) {
        event.preventDefault();
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: $(this).serialize() + "&controller=kullanici&action=avatar_kaydet",
            dataType: "json",
            success: function(response) {
                $.unblockUI();
                if (response.HATA) {
                    Swal.fire({title: 'Uyarı!',text: response.ACIKLAMA ,icon: 'warning' ,customClass: {confirmButton: 'btn btn-primary waves-effect waves-light'},buttonsStyling: false});
                } else {
                    Swal.fire({title: 'Başarılı!',text: response.ACIKLAMA ,icon: 'success' ,customClass: {confirmButton: 'btn btn-primary waves-effect waves-light'},buttonsStyling: false});
                    location.reload(true);
                }
            }
        });
    });

    $("#musteriKaydet").on("submit", function(event) {
        event.preventDefault();
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: $(this).serialize() + "&controller=kullanici&action=musteri_kaydet",
            dataType: "json",
            success: function(response) {
                $.unblockUI();
                if (response.HATA) {
                    Swal.fire({title: 'Uyarı!',text: response.ACIKLAMA ,icon: 'warning' ,customClass: {confirmButton: 'btn btn-primary waves-effect waves-light'},buttonsStyling: false});
                } else {
                    Swal.fire({title: 'Başarılı!',text: response.ACIKLAMA ,icon: 'success' ,customClass: {confirmButton: 'btn btn-primary waves-effect waves-light'},buttonsStyling: false});
                    location.reload(true);
                }
            }
        });
    });

    function fncAvatarSec (obj){
        $("#avatar_id").val($(obj).data("id"));

        $('button[id^="avatar_sec_button"]').removeClass('btn-success').addClass('btn-label-success');
        $(obj).removeClass('btn-label-success').addClass('btn-success');
    }

</script>
<?
	require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
	session_kontrol();
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
                            <div class="row">
                                <div class="col-12">
                                    <h5><i class="ri-user-line fs-3 me-2"></i>Kullanıcı Ekle</h5>
                                </div>
                                <div class="col-12 mb-6">
                                    <div class="bs-stepper wizard-icons wizard-modern wizard-modern-icons-example mt-2">
                                        <div class="bs-stepper-header">
                                            <div class="step" data-target="#account-details-modern">
                                                <button type="button" class="step-trigger">
                                                    <span class="bs-stepper-icon">
                                                        <svg viewBox="0 0 54 54">
                                                            <use xlink:href="../../assets/svg/icons/form-wizard-account.svg#wizardAccount"></use>
                                                        </svg>
                                                    </span>
                                                    <span class="bs-stepper-label">Hesap Bilgileri</span>
                                                </button>
                                            </div>
                                            <div class="line">
                                                <i class="ri-arrow-right-s-line"></i>
                                            </div>
                                            <div class="step" data-target="#personal-info-modern">
                                                <button type="button" class="step-trigger">
                                                    <span class="bs-stepper-icon">
                                                        <svg viewBox="0 0 58 54">
                                                            <use xlink:href="../../assets/svg/icons/form-wizard-personal.svg#wizardPersonal"></use>
                                                        </svg>
                                                    </span>
                                                    <span class="bs-stepper-label">Kullanıcı Bilgileri</span>
                                                </button>
                                            </div>
                                            <div class="line">
                                                <i class="ri-arrow-right-s-line"></i>
                                            </div>
                                            <div class="step" data-target="#address-modern">
                                                <button type="button" class="step-trigger">
                                                    <span class="bs-stepper-icon">
                                                        <svg viewBox="0 0 54 54">
                                                            <use xlink:href="../../assets/svg/icons/form-wizard-address.svg#wizardAddress"></use>
                                                        </svg>
                                                    </span>
                                                    <span class="bs-stepper-label">Adres</span>
                                                </button>
                                            </div>
                                            <div class="line">
                                                <i class="ri-arrow-right-s-line"></i>
                                            </div>
                                            <div class="step" data-target="#social-links-modern">
                                                <button type="button" class="step-trigger">
                                                    <span class="bs-stepper-icon">
                                                        <svg viewBox="0 0 54 54">
                                                            <use
                                                                xlink:href="../../assets/svg/icons/form-wizard-social-link.svg#wizardSocialLink"></use>
                                                        </svg>
                                                    </span>
                                                    <span class="bs-stepper-label">Sosyal Medya</span>
                                                </button>
                                            </div>
                                            <div class="line">
                                                <i class="ri-arrow-right-s-line"></i>
                                            </div>
                                            <div class="step" data-target="#review-submit-modern">
                                                <button type="button" class="step-trigger">
                                                    <span class="bs-stepper-icon">
                                                        <svg viewBox="0 0 54 54">
                                                            <use xlink:href="../../assets/svg/icons/form-wizard-submit.svg#wizardSubmit"></use>
                                                        </svg>
                                                    </span>
                                                    <span class="bs-stepper-label">Kaydet</span>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="bs-stepper-content">
                                            <form id="kullaniciEkleForm">
                                                <div id="account-details-modern" class="content">
                                                    <div class="content-header mb-4">
                                                        <h6 class="mb-0">Hesap Bilgileri</h6>
                                                    </div>
                                                    <div class="row g-5">
                                                        <?if(in_array($_SESSION['yetki_id'],array(1))){?>
                                                            <div class="col-sm-12">
                                                                <div class="form-floating form-floating-outline">
                                                                    <select name="firma_id" id="firma_id" class="select2 form-select" data-style="btn-default">
                                                                        <?=$cKullanici->Firmalar()->setSecilen($_REQUEST['firma_id'])->setSeciniz()->getSelect("ID", "AD")?>
                                                                    </select>
                                                                    <label>Firma</label>
                                                                </div>
                                                            </div>
                                                        <?}?>
                                                        <div class="col-sm-6">
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="text" id="kullanici" name="kullanici" class="form-control kullanici-invalid" placeholder/>
                                                                <label>Kullanıcı Adı</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="mail" id="mail" name="mail" class="form-control mail-invalid" placeholder="info@gmail.com"/>
                                                                <label>Email</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6 form-password-toggle">
                                                            <div class="input-group input-group-merge">
                                                                <div class="form-floating form-floating-outline">
                                                                    <input type="password" id="sifre" name="sifre" class="form-control" placeholder="......"/>
                                                                    <label>Şifre</label>
                                                                </div>
                                                                <span class="input-group-text cursor-pointer" id="sifre"><i class="ri-eye-off-line"></i></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6 form-password-toggle">
                                                            <div class="input-group input-group-merge">
                                                                <div class="form-floating form-floating-outline">
                                                                    <input type="password" id="sifre_tekrar" name="sifre_tekrar" class="form-control" placeholder="......" />
                                                                    <label>Şifre Tekrar</label>
                                                                </div>
                                                                <span class="input-group-text cursor-pointer" id="sifre_tekrar"><i class="ri-eye-off-line"></i></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 d-flex justify-content-between">
                                                            <button type="button" class="btn btn-outline-secondary btn-prev" disabled>
                                                                <i class="ri-arrow-left-line me-sm-1"></i>
                                                                <span class="align-middle d-sm-inline-block d-none">Önceki</span>
                                                            </button>
                                                            <button type="button" class="btn btn-primary btn-next">
                                                                <span class="align-middle d-sm-inline-block d-none me-sm-1">Sonraki</span>
                                                                <i class="ri-arrow-right-line"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div id="personal-info-modern" class="content">
                                                    <div class="content-header mb-4">
                                                        <h6 class="mb-0">Kullanıcı Bilgileri</h6>
                                                    </div>
                                                    <div class="row g-5">
                                                        <div class="col-sm-6">
                                                            <div class="form-floating form-floating-outline">
                                                                <select name="yetki_id" id="yetki_id" class="select2 form-select" data-style="btn-default">
                                                                    <?=$cKullanici->Yetkiler()->setSecilen($_REQUEST['yetki_id'])->setSeciniz()->getSelect("ID", "AD")?>
                                                                </select>
                                                                <label>Yetki</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="text" id="ad" name="ad" class="form-control" placeholder/>
                                                                <label>İsim</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="text" id="soyad" name="soyad" class="form-control" placeholder/>
                                                                <label>Soyisim</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="text" id="telefon" name="telefon" class="form-control phone-mask" placeholder="587 563 5478"/>
                                                                <label>Telefon</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 d-flex justify-content-between">
                                                            <button type="button" class="btn btn-outline-secondary btn-prev">
                                                                <i class="ri-arrow-left-line me-sm-1"></i>
                                                                <span class="align-middle d-sm-inline-block d-none">Önceki</span>
                                                            </button>
                                                            <button type="button" class="btn btn-primary btn-next">
                                                                <span class="align-middle d-sm-inline-block d-none me-sm-1">Sonraki</span>
                                                                <i class="ri-arrow-right-line"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div id="address-modern" class="content">
                                                    <div class="content-header mb-4">
                                                        <h6 class="mb-0">Adres</h6>
                                                    </div>
                                                    <div class="row g-5">
                                                        <div class="col-sm-6">
                                                            <div class="form-floating form-floating-outline">
                                                                <select name="il_id" id="il_id" class="select2 form-select" data-style="btn-default">
                                                                    <?=$cKullanici->Iller()->setSecilen($_REQUEST['il_id'])->setSeciniz()->getSelect("ID", "AD")?>
                                                                </select>
                                                                <label>İl</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-floating form-floating-outline">
                                                                <select name="ilce_id" id="ilce_id" class="btn select2 form-select" data-style="btn-default">
                                                                </select>
                                                                <label>İlçe</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="text" class="form-control" id="adres" name="adres" placeholder="Kartal/İstanbul" />
                                                                <label>Adres</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 d-flex justify-content-between">
                                                            <button type="button" class="btn btn-outline-secondary btn-prev">
                                                                <i class="ri-arrow-left-line me-sm-1"></i>
                                                                <span class="align-middle d-sm-inline-block d-none">Önceki</span>
                                                            </button>
                                                            <button type="button" class="btn btn-primary btn-next">
                                                                <span class="align-middle d-sm-inline-block d-none me-sm-1">Sonraki</span>
                                                                <i class="ri-arrow-right-line"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div id="social-links-modern" class="content">
                                                    <div class="content-header mb-4">
                                                        <h6 class="mb-0">Sosyal Medya</h6>
                                                    </div>
                                                    <div class="row g-5">
                                                        <div class="col-sm-6">
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="text" id="twitter" name="twitter" class="form-control" placeholder/>
                                                                <label>Twitter</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="text" id="facebook" name="facebook" class="form-control" placeholder/>
                                                                <label>Facebook</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="text" id="instagram" name="instagram" class="form-control" placeholder/>
                                                                <label>İnstagram</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="text" id="linkedin" name="linkedin" class="form-control" placeholder/>
                                                                <label>Linkedin</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 d-flex justify-content-between">
                                                            <button type="button" class="btn btn-outline-secondary btn-prev">
                                                                <i class="ri-arrow-left-line me-sm-1"></i>
                                                                <span class="align-middle d-sm-inline-block d-none">Önceki</span>
                                                            </button>
                                                            <button type="button" class="btn btn-primary btn-next">
                                                                <span class="align-middle d-sm-inline-block d-none me-sm-1">Sonraki</span>
                                                                <i class="ri-arrow-right-line"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="review-submit-modern" class="content">
                                                    <p class="fw-medium mb-2">Hesap Bilgileri</p>
                                                    <ul class="list-unstyled">
                                                        <li id="o_kullanici"></li>
                                                        <li id="o_mail"></li>
                                                        <li id="o_sifre"></li>
                                                        <li id="o_sifre_tekrar"></li>
                                                    </ul>
                                                    <hr/>
                                                    <p class="fw-medium mb-2">Kullanıcı Bilgileri</p>
                                                    <ul class="list-unstyled">
                                                        <li id="o_yetki"></li>
                                                        <li id="o_ad"></li>
                                                        <li id="o_soyad"></li>
                                                        <li id="o_telefon"></li>
                                                    </ul>
                                                    <hr/>
                                                    <p class="fw-medium mb-2">Adres</p>
                                                    <ul class="list-unstyled">
                                                        <li id="o_il"></li>
                                                        <li id="o_ilce"></li>
                                                        <li id="o_adres"></li>
                                                    </ul>
                                                    <hr />
                                                    <p class="fw-medium mb-2">Sosyal Meyda</p>
                                                    <ul class="list-unstyled">
                                                        <li id="o_twitter"></li>
                                                        <li id="o_facebook"></li>
                                                        <li id="o_instagram"></li>
                                                        <li id="o_linkedin"></li>
                                                    </ul>
                                                    <div class="col-12 d-flex justify-content-between">
                                                        <button type="button" class="btn btn-outline-secondary btn-prev">
                                                            <i class="ri-arrow-left-line me-sm-1"></i>
                                                            <span class="align-middle d-sm-inline-block d-none">Önceki</span>
                                                        </button>
                                                        <button type="submit" class="btn btn-primary">Kaydet</button>
                                                    </div>
                                                </div>
                                            </form>
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

    $(document).ready(function() {
        function updateReviewSection() {
            // Hesap Bilgileri
            $("#o_kullanici").text($("#kullanici").val());
            $("#o_mail").text($("#mail").val());
            $("#o_sifre").text($("#sifre").val());
            $("#o_sifre_tekrar").text($("#sifre_tekrar").val());

            // Kullanıcı Bilgileri
            $("#o_yetki").text($("#yetki_id option:selected").text());
            $("#o_ad").text($("#ad").val());
            $("#o_soyad").text($("#soyad").val());
            $("#o_telefon").text($("#telefon").val());

            // Adres
            $("#o_il").text($("#il_id option:selected").text());
            $("#o_ilce").text($("#ilce_id option:selected").text());
            $("#o_adres").text($("#adres").val());

            // Sosyal Medya
            $("#o_twitter").text($("#twitter").val());
            $("#o_facebook").text($("#facebook").val());
            $("#o_instagram").text($("#instagram").val());
            $("#o_linkedin").text($("#linkedin").val());
        }

        // "Sonraki" butonlarına tıklanınca önizleme güncellensin
        $(".btn-next").click(function() {
            updateReviewSection();
        });
    });

    $("#il_id").on("change", function(event) {
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: {il_id: $("#il_id").val(), controller: "kullanici", action: "ilce_doldur"},
            dataType: "json",
            success: function(response) {
                if (response.HATA) {
                    $("#ilce_id").html('');
                } else {
                    $("#ilce_id").html(response.HTML);
                }
            }
        });
    });

    $("#kullaniciEkleForm").on("submit", function(event) {
        event.preventDefault();
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: $(this).serialize() + "&controller=kullanici&action=kullanici_ekle",
            dataType: "json",
            success: function(response) {
                $.unblockUI();
                if (response.HATA) {
                    Swal.fire({title: 'Uyarı!',text: response.ACIKLAMA ,icon: 'warning' ,customClass: {confirmButton: 'btn btn-primary waves-effect waves-light'},buttonsStyling: false});
                } else {
                    Swal.fire({title: 'Başarılı!',text: response.ACIKLAMA ,icon: 'success' ,customClass: {confirmButton: 'btn btn-primary waves-effect waves-light'},buttonsStyling: false});
                    location.href = response.URL;
                }
            }
        });
    });

</script>


<?
    @session_start();
    @session_unset();
    @session_destroy();
    $_SESSION = array();
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');

?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-wide customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title> Menü Yönetim | Giriş</title>
        <meta name="description" content=""/>
        <link rel="icon" type="image/x-icon" href="/img/logo.png" />
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap" rel="stylesheet" />
        <link rel="stylesheet" href="/assets/vendor/fonts/remixicon/remixicon.css" />
        <link rel="stylesheet" href="/assets/vendor/fonts/flag-icons.css" />
        <link rel="stylesheet" href="/assets/vendor/libs/node-waves/node-waves.css" />
        <link rel="stylesheet" href="/assets/vendor/css/rtl/core.css" class="template-customizer-core-css" />
        <link rel="stylesheet" href="/assets/vendor/css/rtl/theme-default.css" class="template-customizer-theme-css" />
        <link rel="stylesheet" href="/assets/css/demo.css" />
        <link rel="stylesheet" href="/assets/css/lightbox.min.css" />
        <link rel="stylesheet" href="/assets/css/notyf.min.css" />
        <link rel="stylesheet" href="/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
        <link rel="stylesheet" href="/assets/vendor/libs/typeahead-js/typeahead.css" />
        <link rel="stylesheet" href="/assets/vendor/libs/bootstrap-maxlength/bootstrap-maxlength.css" />
        <link rel="stylesheet" href="/assets/vendor/libs/apex-charts/apex-charts.css" />
        <link rel="stylesheet" href="/assets/vendor/libs/swiper/swiper.css" />
        <link rel="stylesheet" href="/assets/vendor/css/pages/cards-statistics.css" />
        <link rel="stylesheet" href="/assets/vendor/css/pages/cards-analytics.css" />
        <link rel="stylesheet" href="/assets/vendor/css/pages/page-auth.css" />
        <link rel="stylesheet" href="/assets/vendor/libs/flatpickr/flatpickr.css" />
        <link rel="stylesheet" href="/assets/vendor/libs/select2/select2.css" />
        <link rel="stylesheet" href="/assets/vendor/libs/tagify/tagify.css" />
        <link rel="stylesheet" href="/assets/vendor/libs/@form-validation/form-validation.css" />
        <link rel="stylesheet" href="/assets/vendor/libs/animate-css/animate.css" />
        <link rel="stylesheet" href="/assets/vendor/libs/sweetalert2/sweetalert2.css" />
        <link rel="stylesheet" href="/assets/vendor/libs/bs-stepper/bs-stepper.css" />
        <link rel="stylesheet" href="/assets/vendor/libs/bootstrap-select/bootstrap-select.css" />
        <link rel="stylesheet" href="/assets/vendor/libs/toastr/toastr.css" />
        <link rel="stylesheet" href="/assets/vendor/css/pages/page-misc.css" />
        <link rel="stylesheet" href="/assets/vendor/libs/jkanban/jkanban.css" />
        <link rel="stylesheet" href="/assets/vendor/libs/quill/typography.css" />
        <link rel="stylesheet" href="/assets/vendor/libs/quill/katex.css" />
        <link rel="stylesheet" href="/assets/vendor/libs/quill/editor.css" />
        <link rel="stylesheet" href="/assets/vendor/css/pages/app-kanban.css" />
        <link rel="stylesheet" href="/assets/vendor/css/pages/app-calendar.css" />
        <link rel="stylesheet" href="/assets/vendor/libs/fullcalendar/fullcalendar.css" />
        <link rel="stylesheet" href="/assets/vendor/libs/dropzone/dropzone.css" />
        <link rel="stylesheet" href="/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css" />

        <script src="/assets/vendor/js/helpers.js"></script>
        <script src="/assets/vendor/js/template-customizer.js"></script>
        <script src="/assets/js/config.js"></script>
    </head>
    <body>
        <div class="authentication-wrapper authentication-cover">
            <!-- <a href="/" class="auth-cover-brand d-flex align-items-center gap-2">
                <img src="/img/giris.png" class="auth-cover-illustration" alt="logo" width="150"/>
                <span class="app-brand-text demo text-heading fw-semibold"></span>
            </a> -->
            <div class="authentication-inner row m-0">
                <div class="d-none d-lg-flex col-lg-7 col-xl-8 align-items-center justify-content-center p-12 pb-2">
                    <img src="/img/giris.png" class="auth-cover-illustration" alt="logo" style="max-inline-size: 1100px !important;" />
                </div>
                <div class="d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg position-relative py-sm-12 px-12 py-6">
                    <div class="w-px-400 mx-auto pt-5 pt-lg-0">
                        <h4 class="mb-5">Hoşgeldiniz! 👋</h4>
                        <form id="girisForm" class="mb-5">
                            <div class="form-floating form-floating-outline mb-5">
                                <input type="text" class="form-control" id="kullanici" name="kullanici" placeholder="Kullanınıc Adı" autofocus/>
                                <label for="kullanici">Kullanıcı Adı</label>
                            </div>
                            <div class="mb-5">
                                <div class="form-password-toggle">
                                    <div class="input-group input-group-merge">
                                        <div class="form-floating form-floating-outline">
                                            <input type="password" id="sifre" class="form-control" name="sifre" placeholder="........."/>
                                            <label for="sifre">Şifre</label>
                                        </div>
                                        <span class="input-group-text cursor-pointer"><i class="ri-eye-off-line"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div id="responseMessage"></div>
                            <div class="mb-5 d-flex justify-content-between mt-5">
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="hatirla" />
                                    <label class="form-check-label" for="hatirla"> Beni Hatırla </label>
                                </div>
                                <!--
                                <a href="#" class="float-end mb-1 mt-2"><span>Parolanızı mı unuttunuz?</span></a>
                                -->
                            </div>
                            <button type="submit" class="btn btn-info d-grid w-100">Giriş Yap</button>
                        </form>
                        <!--
                        <p class="text-center">
                            <span>Kaydın Yok mu?</span>
                            <a href="auth-register-cover.html"><span>Kayıt Ol</span></a>
                        </p>
                        <div class="divider my-5">
                            <div class="divider-text">yada</div>
                        </div>
                        <div class="d-flex justify-content-center gap-2">
                            <a href="javascript:;" class="btn btn-icon rounded-circle btn-text-facebook"><i class="tf-icons ri-facebook-fill"></i></a>
                            <a href="javascript:;" class="btn btn-icon rounded-circle btn-text-twitter"><i class="tf-icons ri-twitter-fill"></i></a>
                            <a href="javascript:;" class="btn btn-icon rounded-circle btn-text-github"><i class="tf-icons ri-github-fill"></i></a>
                            <a href="javascript:;" class="btn btn-icon rounded-circle btn-text-google-plus"><i class="tf-icons ri-google-fill"></i></a>
                        </div>
                        -->
                    </div>
                </div>
            </div>
        </div>

        <script src="/assets/vendor/libs/jquery/jquery.js"></script>
        <script src="/assets/vendor/libs/popper/popper.js"></script>
        <script src="/assets/vendor/js/bootstrap.js"></script>
        <script src="/assets/vendor/libs/node-waves/node-waves.js"></script>
        <script src="/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
        <script src="/assets/vendor/libs/hammer/hammer.js"></script>
        <script src="/assets/vendor/libs/i18n/i18n.js"></script>
        <script src="/assets/vendor/libs/typeahead-js/typeahead.js"></script>
        <script src="/assets/vendor/js/menu.js"></script>
        <script src="/assets/vendor/libs/cleavejs/cleave.js"></script>
        <script src="/assets/vendor/libs/cleavejs/cleave-phone.js"></script>
        <script src="/assets/vendor/libs/autosize/autosize.js"></script>
        <script src="/assets/vendor/libs/bootstrap-maxlength/bootstrap-maxlength.js"></script>
        <script src="/assets/vendor/libs/jquery-repeater/jquery-repeater.js"></script>
        <script src="/assets/vendor/libs/apex-charts/apexcharts.js"></script>
        <script src="/assets/vendor/libs/swiper/swiper.js"></script>
        <script src="/assets/vendor/libs/moment/moment.js"></script>
        <script src="/assets/vendor/libs/flatpickr/flatpickr.js"></script>
        <script src="/assets/vendor/libs/select2/select2.js"></script>
        <script src="/assets/vendor/libs/sweetalert2/sweetalert2.js"></script>
        <script src="/assets/vendor/libs/bootstrap-select/bootstrap-select.js"></script>
        <script src="/assets/vendor/libs/toastr/toastr.js"></script>
        <script src="/assets/vendor/libs/bs-stepper/bs-stepper.js"></script>
        <script src="/assets/vendor/libs/@form-validation/popular.js"></script>
        <script src="/assets/vendor/libs/@form-validation/bootstrap5.js"></script>
        <script src="/assets/vendor/libs/@form-validation/auto-focus.js"></script>
        <script src="/assets/vendor/libs/jkanban/jkanban.js"></script>
        <script src="/assets/vendor/libs/quill/katex.js"></script>
        <script src="/assets/vendor/libs/quill/quill.js"></script>
        <script src="/assets/vendor/libs/tagify/tagify.js"></script>
        <script src="/assets/vendor/libs/bloodhound/bloodhound.js"></script>
        <script src="/assets/vendor/libs/fullcalendar/fullcalendar.js"></script>
        <script src="/assets/vendor/libs/chartjs/chartjs.js"></script>
        <script src="/assets/vendor/libs/dropzone/dropzone.js"></script>
        <script src="/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js"></script>

        <script src="/assets/js/main.js"></script>
        <script src="/assets/js/dashboards-analytics.js"></script>
        <script src="/assets/js/form-layouts.js"></script>
        <script src="/assets/js/extended-ui-sweetalert2.js"></script>
        <script src="/assets/js/form-wizard-icons.js"></script>
        <script src="/assets/js/ui-toasts.js"></script>
        <script src="/assets/js/modal-edit-user.js"></script>
        <script src="/assets/js/forms-extras.js"></script>
        <script src="/assets/js/forms-selects.js"></script>
        <script src="/assets/js/notyf.min.js"></script>
        <script src="/assets/js/forms-file-upload.js"></script>
        <script src="/assets/js/input-mask.js"></script>
        <script src="/assets/js/lightbox.min.js"></script>
        <script src="/assets/js/script.js"></script>
    </body>
</html>
<script type="text/javascript">

    $("#girisForm").on("submit", function(event) {
        event.preventDefault();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: $(this).serialize() + "&controller=giris&action=giris_yap",
            dataType: "json",
            success: function(response) {
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

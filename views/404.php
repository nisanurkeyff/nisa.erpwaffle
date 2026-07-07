<?
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
    session_kontrol();
?>
<!Doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title>Dashboard - Analytics | Materialize - Material Design HTML Admin Template</title>
        <?=$cTheme->Linkler()?>
    </head>
    <body>
        <div class="misc-wrapper">
            <h1 class="mb-2 mx-2" style="font-size: 6rem; line-height: 6rem">404</h1>
            <h4 class="mb-2">Sayfa Bulunamadı! ⚠️</h4>
            <p class="mb-3 mx-2">Aradığınız sayfayı bulamadık, Anasayfaya Git!</p>
            <div class="d-flex justify-content-center mt-12">
                <img src="/assets/img/illustrations/misc-not-authorized-object.png" alt="misc-not-authorized" class="img-fluid misc-object d-none d-lg-inline-block" width="190" />
                <img src="/assets/img/illustrations/misc-bg-light.png" alt="misc-not-authorized" class="misc-bg d-none d-lg-inline-block" data-app-light-img="illustrations/misc-bg-light.png" data-app-dark-img="illustrations/misc-bg-dark.png" />
                <div class="d-flex flex-column align-items-center">
                    <img src="/assets/img/illustrations/misc-under-maintenance-illustration.png" alt="misc-not-authorized" class="img-fluid z-1" width="300" />
                    <div>
                        <a href="/views/index_yonetici.php" class="btn btn-primary text-center my-10">Anasayfa</a>
                    </div>
                </div>
            </div>
        </div>
        <?=$cTheme->Scriptler()?>
    </body>
</html>

<?
	require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
	//session_kontrol();
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
        <div class="layout-wrapper layout-content-navbar">
            <div class="layout-container">
                <?=$cTheme->Menu()?>
                <div class="layout-page">
                    <?=$cTheme->Header()?>
                    <div class="content-wrapper">
                        <div class="container-xxl flex-grow-1 container-p-y">
                            
                            <div class="row g-6">
                                <div class="col-md-12 col-xxl-8">
                                    <div class="card">
                                        <div class="d-flex align-items-end row">
                                            <div class="col-md-6 order-2 order-md-1">
                                                <div class="card-body">
                                                    <h4 class="card-title mb-4">Congratulations <span class="fw-bold">John!</span> 🎉</h4>
                                                    <p class="mb-0">You have done 68% 😎 more sales today.</p>
                                                    <p>Check your new badge in your profile.</p>
                                                    <a href="javascript:;" class="btn btn-primary">View Profile</a>
                                                </div>
                                            </div>
                                            <div class="col-md-6 text-center text-md-end order-1 order-md-2">
                                                <div class="card-body pb-0 px-0 pt-2">
                                                    <img src="/admin2/assets/img/illustrations/illustration-john-light.png" height="186" class="scaleX-n1-rtl" alt="View Profile" data-app-light-img="illustrations/illustration-john-light.png" data-app-dark-img="illustrations/illustration-john-dark.png" />
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

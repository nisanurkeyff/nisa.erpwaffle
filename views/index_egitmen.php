<?
	require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
	session_kontrol();

    $row_sayi = $cTalep->getTalepSayisi();

?>
<!Doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title> <?=$row_site->TITLE?> | Dashboard</title>
        <?=$cTheme->Linkler()?>
    </head>
    <style type="text/css">
        .custom-link {
            color: black !important;
            text-decoration: none;
        }

        .custom-link:hover {
            color: black !important;
            text-decoration: none;
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
                            <div class="row g-6">

                                <div class="col-lg-3 col-sm-6">
                                    <div class="card">
                                        <a href="/views/talep/talep_listesi.php?surec_id=3" class="custom-link">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center flex-wrap">
                                                    <div class="avatar me-4">
                                                        <div class="avatar-initial bg-label-success rounded-3">
                                                            <i class="ri-check-double-line ri-24px">
                                                            </i>
                                                        </div>
                                                    </div>
                                                    <div class="card-info">
                                                        <div class="d-flex align-items-center">
                                                            <h5 class="mb-0 me-2"><?=$row_sayi->ONAYLANAN?></h5>
                                                        </div>
                                                        <p class="mb-0">Talepler</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
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

    function fncProfil(obj){
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: {id: $(obj).data("id"), controller: "kullanici", action: "kullanici_duzenle"},
            dataType: "json",
            success: function(response) {
                if (response.HATA) {
                    Swal.fire({title: 'Uyarı!',text: response.ACIKLAMA ,icon: 'warning' ,customClass: {confirmButton: 'btn btn-primary waves-effect waves-light'},buttonsStyling: false});
                } else {
                    location.href = response.URL;
                }
            }
        });
    }
</script>
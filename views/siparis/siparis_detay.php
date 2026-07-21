<?
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
    session_kontrol();

    $row                        = $cSiparis->getSiparis($_REQUEST);
    fncTokenKontrol($row);

    $rows_detay                 = $cSiparis->getSiparisDetay($_REQUEST);
    //var_dump2($rows_detay);die;
    $rows_uye_siparis_sayisi    = $cSiparis->getUyeSiparisSayisi($_REQUEST);
?>
<!Doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title> <?=$row_site->TITLE?> | Sipariş Detay </title>
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
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 gap-6">
                                <div class="d-flex flex-column justify-content-center">
                                    <div class="d-flex align-items-center mb-1">
                                        <h5 class="mb-0">Sipariş Numarası #<?=$row->SIPARIS_NO?></h5>
                                        <span class="badge bg-label-danger me-2 ms-2 rounded-pill"><?=$row->INDIRIM?></span>
                                    </div>
                                    <p class="mb-0">Sipariş Tarihi: <?=FormatTarih::tarih($row->TARIH)?></p>
                                </div>
                                
                            </div>
                            <!-- Order Details Table -->
                            <div class="row">
                                <div class="col-12 col-lg-8">
                                    <div class="card mb-6">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h5 class="card-title m-0">Sipariş Detayı</h5>
                                        </div>
                                        <div class="card-datatable table-responsive pb-5">
                                            <table class="datatables-order-details table">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th class="w-50">Ürün</th>
                                                        <th>Fiyat</th>
                                                        <th>Adet</th>
                                                        <th>Tutar</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?foreach ($rows_detay as $key => $row_detay) {
                                                        $row_toplam->ARA_TOPLAM += $row_detay->FIYAT * $row_detay->ADET;
                                                        $row_toplam->TUTAR      += $row_detay->TUTAR;

                                                        $rows_ekstra     = $cSiparis->getSiparisDetayEkstra(array('siparis_detay_id' => $row_detay->ID));
                                                        $rows_cikarilan  = $cSiparis->getSiparisDetayCikarilan(array('siparis_detay_id' => $row_detay->ID));
                                                        ?>
                                                        <tr>
                                                            <td><?=($key+1)?></td>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <?if(is_file(fncImgPathFolder2($row_detay->RESIM_URL, $row_site->IMG_PATH))){ ?>
                                                                        <img src="<?=fncImgPath($row_detay->RESIM_URL, $row_site->IMG_PATH)?>" class="rounded-3 me-2" width="100" height="100">
                                                                    <?}else{?>
                                                                        <img src="<?=$row_site->LOGO?>" class="rounded-3 me-2" width="100" height="100">
                                                                    <?}?>
                                                                    <div>
                                                                        <div class="fw-semibold">
                                                                            <?=FormatYazi::kisalt2($row_detay->URUN)?>
                                                                            <?if($row_detay->URUN_ID > 0){?>
                                                                                <a href="javascript:;" class="text-primary ms-1 fncMaliyetGoster" data-urun-id="<?=$row_detay->URUN_ID?>" title="Maliyet Detayı"><i class="ri-calculator-line"></i></a>
                                                                            <?}?>
                                                                        </div>
                                                                        <div class="text-muted small"><?=$row_detay->MARKA?></div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td nowrap><?=FormatSayi::sayi($row_detay->FIYAT,2)?> ₺</td>
                                                            <td nowrap><?=FormatSayi::sayi($row_detay->ADET,0)?></td>
                                                            <td nowrap><?=FormatSayi::sayi($row_detay->TUTAR,2)?> ₺</td>
                                                        </tr>
                                                        <?if(!empty($rows_ekstra) || !empty($rows_cikarilan)){?>
                                                            <tr>
                                                                <td></td>
                                                                <td colspan="4" class="bg-light-subtle">
                                                                    <div class="ps-5 py-3">
                                                                        <!-- EKSTRALAR -->
                                                                        <?if(!empty($rows_ekstra)){?>
                                                                            <div class="mb-3">
                                                                                <div class="fw-semibold text-success small mb-2">
                                                                                    <i class="ti ti-plus me-1"></i> Ekstra Malzemeler
                                                                                </div>
                                                                                <?foreach($rows_ekstra as $row_ekstra){
                                                                                    $row_toplam->EKSTRA_TUTAR += $row_ekstra->FIYAT;
                                                                                    ?>
                                                                                    <div class="d-flex justify-content-between align-items-center small mb-1 text-nowrap">
                                                                                        <div>+ <?=$row_ekstra->MALZEME_AD?></div>
                                                                                        <div class="fw-semibold text-success">
                                                                                            <?=FormatSayi::sayi($row_ekstra->FIYAT,2)?> ₺
                                                                                        </div>
                                                                                    </div>
                                                                                <?}?>
                                                                                <div class="border-top mt-2 pt-2 d-flex justify-content-between small text-nowrap">
                                                                                    <div class="text-muted">Ekstra Toplam</div>
                                                                                    <div class="fw-bold text-primary">
                                                                                        <?=FormatSayi::sayi($row_toplam->EKSTRA_TUTAR,2)?> ₺
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        <?}?>
                                                                        <!-- ÇIKARILANLAR -->
                                                                        <?if(!empty($rows_cikarilan)){?>
                                                                            <div>
                                                                                <div class="fw-semibold text-danger small mb-2">
                                                                                    <i class="ti ti-minus me-1"></i> Çıkarılan Malzemeler
                                                                                </div>
                                                                                <?foreach($rows_cikarilan as $row_cikarilan){?>
                                                                                    <div class="small mb-1 text-muted text-decoration-line-through">
                                                                                        - <?=$row_cikarilan->MALZEME_AD?>
                                                                                    </div>
                                                                                <?}?>
                                                                            </div>
                                                                        <?}?>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?}?>
                                                    <?}?>    
                                                </tbody>
                                            </table>
                                            <div class="d-flex justify-content-end align-items-center m-4 p-1 mb-0 pb-0">
                                                <div class="order-calculations">
                                                    <div class="d-flex justify-content-between align-items-center text-nowrap mb-2">
                                                        <span class="text-heading fw-medium">Ara Toplam:</span>
                                                        <h6 class="mb-0"><?=FormatSayi::sayi($row_toplam->ARA_TOPLAM,2)?> ₺</h6>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center text-nowrap mb-2">
                                                        <span class="text-heading fw-medium">Ekstra Toplam:</span>
                                                        <h6 class="mb-0"><?=FormatSayi::sayi($row_toplam->EKSTRA_TUTAR,2)?> ₺</h6>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center text-nowrap mb-2">
                                                        <span class="text-heading fw-medium">İndirim Tutar:</span>
                                                        <h6 class="mb-0"><?=FormatSayi::sayi($row->INDIRIM_TUTAR,2)?> ₺</h6><br>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center text-nowrap border-top pt-2">
                                                        <h6 class="mb-0 me-2 fw-bold">Toplam Tutar: </h6>
                                                        <h6 class="mb-0 text-success">
                                                            <?=FormatSayi::sayi(($row_toplam->ARA_TOPLAM + $row_toplam->EKSTRA_TUTAR) - $row->INDIRIM_TUTAR,2)?> ₺
                                                        </h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <div class="card mb-6">
                                        <div class="card-body">
                                            <h5 class="card-title mb-6">Müşteri Bilgileri</h5>
                                            <div class="d-flex justify-content-start align-items-center mb-6">
                                                <div class="avatar me-3">
                                                    <img src="../../assets/img/avatars/1.png" alt="Avatar" class="rounded-circle" />
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <a href="javascript:void(0)">
                                                        <h6 class="mb-0"><?=$row->MUSTERI?></h6>
                                                    </a>
                                                    <span>Müşteri ID: #<?=$row->MUSTERI_ID?></span>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-start align-items-center mb-6">
                                                <span
                                                    class="avatar rounded-circle bg-label-success me-3 d-flex align-items-center justify-content-center"><i class="ri-shopping-cart-line ri-24px"></i></span>
                                                <h6 class="text-nowrap mb-0"><?=$rows_uye_siparis_sayisi[$row->MUSTERI_ID]->TOPLAM?> Sipariş Sayısı</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card mb-6">
                                        <div class="card-header d-flex justify-content-between pb-0">
                                            <h5 class="card-title mb-1">Sipariş Açıklaması</h5>
                                        </div>
                                        <div class="card-body">
                                            <p class="mb-6"><?=$row->SIPARIS_NOT?></p>
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

    function fncSiparisIptal(obj){
        sweatAlert("Emin Misiniz?", "Evet, İptal Et").then(function (result) {
            if (result.value) {
                showSpinner();
                $.ajax({
                    url: "/router.php",
                    type: "POST",
                    data: {id: $(obj).data("id"), controller: "siparis", action: "siparis_iptal"},
                    dataType: "json",
                    success: function(response) {
                        $.unblockUI();
                        if (response.HATA) {
                            Swal.fire({title: 'Uyarı!',text: response.ACIKLAMA ,icon: 'warning' ,customClass: {confirmButton: 'btn btn-primary waves-effect waves-light'},buttonsStyling: false});
                        } else {
                            notyf.success(response.ACIKLAMA);
                            //location.href = "/views/siparis/siparis_listesi.php?route=siparis/siparis_listesi";
                            location.reload(true);
                        }
                    }
                });
            }
        });
    }

    function fncSiparisHazirlaniyor(obj){
        sweatAlert("Emin Misiniz?", "Evet, Sipariş Hazırlanıyor").then(function (result) {
            if (result.value) {
                showSpinner();
                $.ajax({
                    url: "/router.php",
                    type: "POST",
                    data: {id: $(obj).data("id"), controller: "siparis", action: "siparis_hazirlaniyor"},
                    dataType: "json",
                    success: function(response) {
                        $.unblockUI();
                        if (response.HATA) {
                            notyf.error(response.ACIKLAMA);
                        } else {
                            notyf.success(response.ACIKLAMA);
                            location.reload(true);
                        }
                    }
                });
            }
        });
    }

    function fncSiparisKargoyaVerildi(obj){
        sweatAlert("Emin Misiniz?", "Evet, Kargoya Verdik").then(function (result) {
            if (result.value) {
                showSpinner();
                $.ajax({
                    url: "/router.php",
                    type: "POST",
                    data: {id: $(obj).data("id"), controller: "siparis", action: "siparis_kargoya_verildi"},
                    dataType: "json",
                    success: function(response) {
                        $.unblockUI();
                        if (response.HATA) {
                            notyf.error(response.ACIKLAMA);
                        } else {
                            notyf.success(response.ACIKLAMA);
                            location.reload(true);
                        }
                    }
                });
            }
        });
    }

    function fncSiparisTamamlandi(obj){
        sweatAlert("Emin Misiniz?", "Evet, Sipariş Müşteriye Ulaştı").then(function (result) {
            if (result.value) {
                showSpinner();
                $.ajax({
                    url: "/router.php",
                    type: "POST",
                    data: {id: $(obj).data("id"), controller: "siparis", action: "siparis_tamamlandi"},
                    dataType: "json",
                    success: function(response) {
                        $.unblockUI();
                        if (response.HATA) {
                            notyf.error(response.ACIKLAMA);
                        } else {
                            notyf.success(response.ACIKLAMA);
                            location.reload(true);
                        }
                    }
                });
            }
        });
    }

    function fncSiparisIade(obj){
        sweatAlert("Emin Misiniz?", "Evet, Siparişin İadesini Onaylıyorum").then(function (result) {
            if (result.value) {
                showSpinner();
                $.ajax({
                    url: "/router.php",
                    type: "POST",
                    data: {id: $(obj).data("id"), controller: "siparis", action: "siparis_iade"},
                    dataType: "json",
                    success: function(response) {
                        $.unblockUI();
                        if (response.HATA) {
                            notyf.error(response.ACIKLAMA);
                        } else {
                            notyf.success(response.ACIKLAMA);
                            location.reload(true);
                        }
                    }
                });
            }
        });
    }

    function fncSiparisIadeRed(obj){
        sweatAlert("Emin Misiniz?", "Evet, Siparişin İadesini Reddedini Onaylıyorum").then(function (result) {
            if (result.value) {
                showSpinner();
                $.ajax({
                    url: "/router.php",
                    type: "POST",
                    data: {id: $(obj).data("id"), controller: "siparis", action: "siparis_iade_red"},
                    dataType: "json",
                    success: function(response) {
                        $.unblockUI();
                        if (response.HATA) {
                            notyf.error(response.ACIKLAMA);
                        } else {
                            notyf.success(response.ACIKLAMA);
                            location.reload(true);
                        }
                    }
                });
            }
        });
    }


    function fncKargoBilgisi(obj){
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: {id: $(obj).data("id"), controller: "siparis", action: "kargo_bilgisi"},
            dataType: "json",
            success: function(response) {
                $.unblockUI();
                if (response.HATA) {
                    Swal.fire({title: 'Uyarı!',text: response.ACIKLAMA ,icon: 'warning' ,customClass: {confirmButton: 'btn btn-primary waves-effect waves-light'},buttonsStyling: false});
                } else {
                    $("#kargoBilgileriModal #kargo_firma").val(response.ROW.KARGO_FIRMA);
                    $("#kargoBilgileriModal #kargo_takip_no").val(response.ROW.KARGO_TAKIP_NO);
                    $("#kargoBilgileriModal #iade_kargo_takip_no").val(response.ROW.IADE_KARGO_TAKIP_NO);
                    $("#kargoBilgileriModal").modal("show");
                }
            }
        });
    }

    $("#kargoKaydet").on("submit", function(event) {
        event.preventDefault();
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: $(this).serialize() + "&controller=siparis&action=kargo_kaydet",
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

</script>
</body>
</html>



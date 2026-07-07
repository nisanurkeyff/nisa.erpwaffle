<?
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
    session_kontrol();
?>
<!Doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title> <?=$row_site->TITLE?> | Malzeme Alış Ekle </title>
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
                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <div class="card mb-6">
                                        <div class="card-header header-elements bg-primary py-3">
                                            <h6 class="mb-0 me-2 text-white"> <i class="ri-add-line fs-4 me-2"></i> Malzeme Alış Girişi</h6>
                                        </div>
                                        <div class="card-body mt-4">
                                            <form id="alisKaydet">
                                                <div class="row g-6">
                                                    
                                                    <div class="col-md-6">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="cari_id" id="cari_id" class="select2 form-select" data-style="btn-default">
                                                                <?=$cMalzemeAlis->Cariler()->setSeciniz()->getSelect("ID", "AD")?>
                                                            </select>
                                                            <label>Cari</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="malzeme_id" id="malzeme_id" class="select2 form-select" data-style="btn-default">
                                                                <?=$cMalzemeAlis->Malzemeler()->setSeciniz()->getSelect("ID", "AD")?>
                                                            </select>
                                                            <label>Malzeme</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-floating form-floating-outline">
                                                            <input type="text" id="miktar" name="miktar" class="form-control decimal" placeholder="0.00">
                                                            <label>Miktar</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-floating form-floating-outline">
                                                            <input type="text" id="birim_fiyat" name="birim_fiyat" class="form-control decimal" placeholder="0.00">
                                                            <label>Birim Fiyat (KDV Dahil)</label>
                                                        </div>
                                                    </div>
                                                     <div class="col-md-3">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="kdv" id="kdv" class="select2 form-select" data-style="btn-default">
                                                                <?=$cUrun->Kdv()->setSeciniz()->getSelect("ID", "AD")?>
                                                            </select>
                                                            <label>Kdv</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 col-sm-6">
                                                        <div class="form-floating form-floating-outline">
                                                            <input type="text" id="fatura_tarih" name="fatura_tarih" class="form-control datepicker active" placeholder="YYYY-MM-DD" readonly="readonly">
                                                            <label>Fatura Tarih</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-floating form-floating-outline">
                                                            <textarea class="form-control" id="aciklama" name="aciklama" placeholder="Açıklama" style="height: 100px;"></textarea>
                                                            <label>Açıklama</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="text-end pt-6">
                                                    <button type="submit" class="btn btn-primary me-4 waves-effect waves-light">Kaydet</button>
                                                    <a href="/views/finans/malzeme_alis_listesi.php" class="btn btn-outline-secondary waves-effect">Geri Dön</a>
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
    $("#alisKaydet").on("submit", function(event) {
        event.preventDefault();
        var formData = new FormData(this);
        formData.append("controller", "malzemeAlis");
        formData.append("action", "malzeme_alis_kaydet");
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
                    // Başarılı olursa listeye dön veya formu temizle
                    setTimeout(function(){
                         window.location.href = "/views/finans/malzeme_alis_listesi.php?route=finans/malzeme_alis_listesi";
                    }, 1000);
                }
            },
            error: function(xhr, status, error) {
                $.unblockUI();
                notyf.error("Bir hata oluştu: " + error);
            }
        });
    });
</script>
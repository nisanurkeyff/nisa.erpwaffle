<?
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
    session_kontrol();

    $excel = new excelSayfasi();
    $excel->sutunEkle("Kategori","UST_KATEGORI","");
    $excel->sutunEkle("Kategori","KATEGORI","");
    $excel->sutunEkle("Kategori İngilizce","KATEGORI_ENG","");
    $excel->sutunEkle("Sıra","SIRA","");
    $excel->sutunEkle("Durum","DURUM","");
    $excelOut = $excel->excel();

    $rows_kategori       = $cKategori->getKategoriler($_REQUEST);

    $_SESSION["Table"]  = $result;
    $_SESSION['excel']  = $excelOut;
?>
<!Doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title> <?=$row_site->TITLE?> | Kategori </title>
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
                            <div class="col-md-12">
                                <div class="row">

                                    <div class="col-md-8">
                                        <div class="card mb-6">
                                            <div class="card-header header-elements bg-primary py-1">
                                                <h6 class="mb-0 me-2 text-white"> <i class="ri-folders-line fs-4 me-2"></i>Kategoriler</h6>
                                                <div class="card-header-elements ms-auto">
                                                    <a href="javascript:;" class="btn btn-icon text-white float-right border-white borderd-radius btn-sm" data-bs-target="#kategoriEkleModal" data-bs-toggle="modal"><i class="ri-add-line fs-4"></i></a>
                                                </div>
                                            </div>
                                            <div class="card-body mt-2">
                                                <div class="card-datatable text-nowrap table-responsive">
                                                    <table class="table table-hover table-sm">
                                                        <thead class="thead-themed fw-bold py-0">
                                                            <tr class="table-primary">
                                                                <td>#</td>
                                                                <td>Kategori</td>
                                                                <td>Sıra</td>
                                                                <td>Durum</td>
                                                                <td></td>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?foreach ($rows_kategori as $key => $row_kategori) {?>
                                                                <tr>
                                                                    <td><?=($key+1)?></td>
                                                                    <td><?=$row_kategori->KATEGORI?></td>
                                                                    <td><?=$row_kategori->SIRA?></td>
                                                                    <td><?=fncDurumSpan($row_kategori->DURUM)?></td>
                                                                    <td align="right">
                                                                        <a href="javascript:;" class="btn btn-primary btn-icon btn-sm" data-id="<?=$row_kategori->ID?>" onclick="fncKategoriBilgisi(this)" data-bs-target="#kategoriDuzenleModal" data-bs-toggle="modal"><i class="ri-pencil-line"></i></a>
                                                                        <a href="javascript:;" class="btn btn-danger btn-icon btn-sm" data-id="<?=$row_kategori->ID?>" onclick="fncKategoriSil(this)"><i class="ri-delete-bin-5-line"></i></a>
                                                                    </td>
                                                                </tr>
                                                            <?}?>
                                                        </tbody>
                                                    </table>
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

<div class="modal fade" id="kategoriEkleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-edit-user">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-body p-0">
                <div class="text-center mb-6">
                    <h4 class="mb-2">Kategori Ekle</h4>
                </div>
                <form id="kategoriEkle" class="row g-5">
                    <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="kategori" name="kategori" class="form-control" maxlength="45"/>
                            <label>Kategori</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline">
                            <select name="durum" id="durum" class="select2 form-select" data-style="btn-default">
                                <?=$cKullanici->Durum()->setSecilen(1)->getSelect("ID", "AD")?>
                            </select>
                            <label>Durum</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="number" id="sira" name="sira" class="form-control" maxlength="3"/>
                            <label>Sıra</label>
                        </div>
                    </div>
                    <div class="col-md-6 col-6">
                        <input type="file" class="form-control" name="resim" id="resim">
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

<div class="modal fade" id="kategoriDuzenleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-edit-user">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-body p-0">
                <div class="text-center mb-6">
                    <h4 class="mb-2">Kategori Düzenle</h4>
                </div>
                <form id="kategoriDuzenle" class="row g-5">
                    <input type="hidden" name="id" id="id" value="">
                    <div class="col-md-12 col-12 mb-4 text-center">
                        <img id="mevcutResim" class="rounded-3 fancybox" src="/img/logo.png" alt="Mevcut Resim" height="150">
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="kategori" name="kategori" class="form-control" maxlength="45"/>
                            <label>Kategori</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline">
                            <select name="durum" id="durum2" class="select2 form-select" data-style="btn-default">
                                <?=$cKullanici->Durum()->setSecilen()->getSelect("ID", "AD")?>
                            </select>
                            <label>Durum</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="number" id="sira" name="sira" class="form-control" maxlength="3"/>
                            <label>Sıra</label>
                        </div>
                    </div>
                    <div class="col-md-6 col-6">
                        <input type="file" class="form-control" name="resim" id="resim">
                    </div>
                    <div class="col-12 text-right d-flex flex-wrap justify-content-end gap-4 row-gap-4">
                        <button type="submit" class="btn btn-primary">Kaydet</button>
                        <button type="button" class="btn btn-danger" onclick="fncUstKategoriResimSil(this)">Resmi Sil</button>
                        <button type="reset" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Kapat">Kapat</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    
    $("#kategoriEkle").on("submit", function(event) {
        event.preventDefault();
        var formData = new FormData(this);
        formData.append("controller", "kategori");
        formData.append("action", "kategori_ekle");

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
                    location.reload(true);
                }
            },
            error: function(xhr, status, error) {
                $.unblockUI();
                notyf.error("Bir hata oluştu: " + error);
            }
        });
    });

    function fncKategoriBilgisi(obj){
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: {id: $(obj).data("id"), controller: "kategori", action: "kategori_bilgisi"},
            dataType: "json",
            success: function(response) {
                $.unblockUI();
                if (response.HATA) {
                    Swal.fire({title: 'Uyarı!',text: response.ACIKLAMA ,icon: 'warning' ,customClass: {confirmButton: 'btn btn-primary waves-effect waves-light'},buttonsStyling: false});
                } else {
                    $("#kategoriDuzenleModal #id").val(response.ROW.ID);
                    $("#kategoriDuzenleModal #sira").val(response.ROW.SIRA);
                    $("#kategoriDuzenleModal #kategori").val(response.ROW.KATEGORI);
                    $("#kategoriDuzenleModal #durum2").val(response.ROW.DURUM).trigger('change');
                    $("#kategoriDuzenleModal #mevcutResim").attr("src", response.ROW.RESIM_URL);
                }
            }
        });
    }


    $("#kategoriDuzenle").on("submit", function(event) {
        event.preventDefault();
        var formData = new FormData(this);
        formData.append("controller", "kategori");
        formData.append("action", "kategori_kaydet");

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
                    location.reload(true);
                }
            },
            error: function(xhr, status, error) {
                $.unblockUI();
                notyf.error("Bir hata oluştu: " + error);
            }
        });
    });

    function fncKategoriSil(obj){
        sweatAlert("Emin Misiniz?", "Evet, Sil").then(function (result) {
            if (result.value) {
                showSpinner();
                $.ajax({
                    url: "/router.php",
                    type: "POST",
                    data: {id: $(obj).data("id"), controller: "kategori", action: "kategori_sil"},
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

    function fncKategoriResimSil(obj) {
        sweatAlert("Emin Misiniz?", "Evet, Sil").then(function (result) {
            if (result.value) {
                showSpinner();
                $.ajax({
                    url: "/router.php",
                    type: "POST",
                    data: $("#kategoriDuzenle").serialize() + "&controller=kategori&action=kategori_resim_sil",
                    dataType: 'json',
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

</script>
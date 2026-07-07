<?
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
    session_kontrol();

    $rows     = $cBolge->getBolgeler($_REQUEST);
?>
<!Doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title> <?=$row_site->TITLE?> | Bölge Listesi </title>
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

                                    <div class="col-md-6">
                                        <div class="card mb-6">
                                            <div class="card-header header-elements bg-primary py-1">
                                                <h6 class="mb-0 me-2 text-white"> <i class="ri-home-4-line fs-4 me-2"></i> Bölgeler</h6>
                                                <div class="card-header-elements ms-auto">
                                                    <a href="javascript:;" class="btn btn-icon text-white float-right border-white borderd-radius btn-sm" data-bs-target="#bolgeEkleModal" data-bs-toggle="modal"><i class="ri-add-line fs-4"></i></a>
                                                </div>
                                            </div>
                                            <div class="card-body mt-2">
                                                <div class="card-datatable text-nowrap table-responsive">
                                                    <table class="table table-hover table-sm">
                                                        <thead class="thead-themed fw-bold py-0">
                                                            <tr class="table-primary">
                                                                <td>#</td>
                                                                <td>Bölge</td>
                                                                <td>Durum</td>
                                                                <td></td>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?foreach ($rows as $key => $row) {?>
                                                                <tr>
                                                                    <td><?=($key+1)?></td>
                                                                    <td><?=$row->BOLGE?></td>
                                                                    <td><?=fncDurumSpan($row->DURUM)?></td>
                                                                    <td align="right">
                                                                        <a href="javascript:;" class="btn btn-primary btn-icon btn-sm" data-id="<?=$row->ID?>" onclick="fncBolgeBilgisi(this)" data-bs-target="#bolgeDuzenleModal" data-bs-toggle="modal"><i class="ri-pencil-line"></i></a>
                                                                        <a href="javascript:;" class="btn btn-danger btn-icon btn-sm" data-id="<?=$row->ID?>" onclick="fncBolgeSil(this)"><i class="ri-delete-bin-5-line"></i></a>
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

<div class="modal fade" id="bolgeEkleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-edit-user">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-body p-0">
                <div class="text-center mb-6">
                    <h4 class="mb-2">Bölge Ekle</h4>
                </div>
                <form id="bolgeEkle" class="row g-5">
                    <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="bolge" name="bolge" class="form-control" maxlength="45"/>
                            <label>Bölge</label>
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
                    <div class="col-12 text-right d-flex flex-wrap justify-content-end gap-4 row-gap-4">
                        <button type="submit" class="btn btn-primary">Kaydet</button>
                        <button type="reset" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Kapat">Kapat</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="bolgeDuzenleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-edit-user">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-body p-0">
                <div class="text-center mb-6">
                    <h4 class="mb-2">Bölge Düzenle</h4>
                </div>
                <form id="bolgeDuzenle" class="row g-5">
                    <input type="hidden" name="id" id="id" value="">
                    <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="bolge" name="bolge" class="form-control" maxlength="45"/>
                            <label>Bölge</label>
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

    function fncBolgeBilgisi(obj){
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: {id: $(obj).data("id"), controller: "bolge", action: "bolge_bilgisi"},
            dataType: "json",
            success: function(response) {
                $.unblockUI();
                if (response.HATA) {
                    Swal.fire({title: 'Uyarı!',text: response.ACIKLAMA ,icon: 'warning' ,customClass: {confirmButton: 'btn btn-primary waves-effect waves-light'},buttonsStyling: false});
                } else {
                    $("#bolgeDuzenleModal #id").val(response.ROW.ID);
                    $("#bolgeDuzenleModal #bolge").val(response.ROW.BOLGE);
                    $("#bolgeDuzenleModal #durum2").val(response.ROW.DURUM).trigger('change');
                }
            }
        });
    }

    $("#bolgeEkle").on("submit", function(event) {
        event.preventDefault();
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: $(this).serialize() + "&controller=bolge&action=bolge_ekle",
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
    });
    
    $("#bolgeDuzenle").on("submit", function(event) {
        event.preventDefault();
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: $(this).serialize() + "&controller=bolge&action=bolge_kaydet",
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

    function fncBolgeSil(obj){
        sweatAlert("Emin Misiniz?", "Evet, Sil").then(function (result) {
            if (result.value) {
                showSpinner();
                $.ajax({
                    url: "/router.php",
                    type: "POST",
                    data: {id: $(obj).data("id"), controller: "bolge", action: "bolge_sil"},
                    dataType: "json",
                    success: function(response) {
                        $.unblockUI();
                        if (response.HATA) {
                            notyf.error(response.ACIKLAMA);
                        } else {
                            notyf.success(response.ACIKLAMA);
                            $(obj).closest('tr').fadeOut();
                        }
                    }
                });
            }
        });
    }

</script>



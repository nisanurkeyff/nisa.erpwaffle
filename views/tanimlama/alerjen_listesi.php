<?
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
    session_kontrol();

    $rows     = $cAlerjen->getAlerjenler($_REQUEST);
?>
<!Doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title> <?=$row_site->TITLE?> | Alerjen Listesi </title>
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

                                    <div class="col-md-12">
                                        <div class="card mb-6">
                                            <div class="card-header header-elements bg-primary py-1">
                                                <h6 class="mb-0 me-2 text-white"> <i class="ri-dossier-line fs-4 me-2"></i> Alerjenler</h6>
                                                <div class="card-header-elements ms-auto">
                                                    <a href="javascript:;" class="btn btn-icon text-white float-right border-white borderd-radius btn-sm" data-bs-target="#EkleModal" data-bs-toggle="modal"><i class="ri-add-line fs-4"></i></a>
                                                </div>
                                            </div>
                                            <div class="card-body mt-2">
                                                <div class="card-datatable text-nowrap table-responsive">
                                                    <table class="table table-hover table-sm">
                                                        <thead class="thead-themed fw-bold py-0">
                                                            <tr class="table-primary">
                                                                <td>#</td>
                                                                <td align="center">Resim</td>
                                                                <td>Alerjen</td>
                                                                <td>Alerjen İngilizce</td>
                                                                <td>Sıra</td>
                                                                <td>Durum</td>
                                                                <td></td>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="sortable">
                                                            <?foreach ($rows as $key => $row) {?>
                                                                <tr data-id="<?=$row->ID?>">
                                                                    <td><?=($key+1)?></td>
                                                                    <td align="center">
                                                                        <?if(is_file(fncDocumentRoot($row->RESIM_URL))){?>
                                                                            <img src="<?=$row->RESIM_URL?>" class="rounded-3 fancybox" alt="Alerjen Resim" height="50">
                                                                        <?}else{?>
                                                                            <img src="<?=$row_site->LOGO?>" class="fancybox" alt="Menü Yönetim" height="50"/>
                                                                        <?}?>
                                                                    </td>
                                                                    <td><?=$row->ALERJEN?></td>
                                                                    <td><?=$row->ALERJEN_ENG?></td>
                                                                    <td><?=$row->SIRA?></td>
                                                                    <td><?=fncDurumSpan($row->DURUM)?></td>
                                                                    <td align="right">
                                                                        <a href="javascript:;" class="btn btn-primary btn-icon btn-sm" data-id="<?=$row->ID?>" onclick="fncBilgisi(this)" data-bs-target="#alerjenDuzenleModal" data-bs-toggle="modal"><i class="ri-pencil-line"></i></a>
                                                                        <a href="javascript:;" class="btn btn-danger btn-icon btn-sm" data-id="<?=$row->ID?>" onclick="fncSil(this)"><i class="ri-delete-bin-5-line"></i></a>
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

<div class="modal fade" id="EkleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-edit-user">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-body p-0">
                <div class="text-center mb-6">
                    <h4 class="mb-2">Alerjen Ekle</h4>
                </div>
                <form id="alerjenEkle" class="row g-5">
                    <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="alerjen" name="alerjen" class="form-control" maxlength="45"/>
                            <label>Alerjen</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="alerjen_eng" name="alerjen_eng" class="form-control" maxlength="45"/>
                            <label>Alerjen İngilizce</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="number" id="sira" name="sira" class="form-control" maxlength="3"/>
                            <label>Sıra</label>
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

<div class="modal fade" id="alerjenDuzenleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-edit-user">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-body p-0">
                <div class="text-center mb-6">
                    <h4 class="mb-2">Alerjen Düzenle</h4>
                </div>
                <form id="alerjenDuzenle" class="row g-5">
                    <input type="hidden" name="id" id="id" value="">
                    <div class="col-md-12 col-12 mb-4 text-center">
                        <img id="mevcutResim" class="rounded-3 fancybox" src="/img/logo.png" alt="Mevcut Resim" height="150">
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="alerjen" name="alerjen" class="form-control" maxlength="45"/>
                            <label>Alerjen</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="alerjen_eng" name="alerjen_eng" class="form-control" maxlength="45"/>
                            <label>Alerjen İngilizce</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="number" id="sira" name="sira" class="form-control" maxlength="3"/>
                            <label>Sıra</label>
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
                    <div class="col-md-6 col-6">
                        <input type="file" class="form-control" name="resim" id="resim">
                    </div>
                    <div class="col-12 text-right d-flex flex-wrap justify-content-end gap-4 row-gap-4">
                        <button type="submit" class="btn btn-primary">Kaydet</button>
                        <button type="button" class="btn btn-danger" onclick="fncResimSil(this)">Resmi Sil</button>
                        <button type="reset" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Kapat">Kapat</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    function fncBilgisi(obj){
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: {id: $(obj).data("id"), controller: "alerjen", action: "alerjen_bilgisi"},
            dataType: "json",
            success: function(response) {
                $.unblockUI();
                if (response.HATA) {
                    Swal.fire({title: 'Uyarı!',text: response.ACIKLAMA ,icon: 'warning' ,customClass: {confirmButton: 'btn btn-primary waves-effect waves-light'},buttonsStyling: false});
                } else {
                    $("#alerjenDuzenleModal #id").val(response.ROW.ID);
                    $("#alerjenDuzenleModal #alerjen").val(response.ROW.ALERJEN);
                    $("#alerjenDuzenleModal #alerjen_eng").val(response.ROW.ALERJEN_ENG);
                    $("#alerjenDuzenleModal #sira").val(response.ROW.SIRA);
                    $("#alerjenDuzenleModal #durum2").val(response.ROW.DURUM).trigger('change');
                    $("#alerjenDuzenleModal #mevcutResim").attr("src", response.ROW.RESIM_URL);
                }
            }
        });
    }

    $("#alerjenEkle").on("submit", function(event) {
        event.preventDefault();
        var formData = new FormData(this);
        formData.append("controller", "alerjen");
        formData.append("action", "alerjen_ekle");

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

    $("#alerjenDuzenle").on("submit", function(event) {
        event.preventDefault();
        var formData = new FormData(this);
        formData.append("controller", "alerjen");
        formData.append("action", "alerjen_kaydet");

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

    function fncSil(obj){
        sweatAlert("Emin Misiniz?", "Evet, Sil").then(function (result) {
            if (result.value) {
                showSpinner();
                $.ajax({
                    url: "/router.php",
                    type: "POST",
                    data: {id: $(obj).data("id"), controller: "alerjen", action: "alerjen_sil"},
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

    function fncResimSil(obj) {
        sweatAlert("Emin Misiniz?", "Evet, Sil").then(function (result) {
            if (result.value) {
                showSpinner();
                $.ajax({
                    url: "/router.php",
                    type: "POST",
                    data: $("#alerjenDuzenle").serialize() + "&controller=alerjen&action=alerjen_resim_sil",
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

    $(document).ready(function() {
        $("tbody.sortable").each(function() {
            $(this).sortable({
                handle: "td",
                update: function() {
                    let siralama = [];

                    $(this).children("tr").each(function(index) {
                        siralama.push({
                            id: $(this).data("id"),
                            sira: index + 1
                        });
                    });

                    showSpinner();
                    $.ajax({
                        url: "/router.php",
                        type: "POST",
                        data: {controller: "alerjen", action: "alerjen_sira_kaydet", siralama: JSON.stringify(siralama)},
                        dataType: 'json',
                        success: function(response) {
                            $.unblockUI();
                            if (response.HATA) {
                                notyf.error(response.ACIKLAMA);
                            } else {
                                notyf.success(response.ACIKLAMA);
                                $(".sortable").html(response.HTML);
                            }
                        },
                        error: function() {
                            $.unblockUI();
                            notyf.error("Sunucu hatası oluştu.");
                        }
                    });
                }
            });
        });
    });

</script>



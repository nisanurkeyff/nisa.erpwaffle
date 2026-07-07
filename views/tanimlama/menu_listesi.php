<?
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
    session_kontrol();

    $rows_anamenuler  = $cMenu->getAnaMenuler($_REQUEST);
    $rows_menuler     = $cMenu->getMenuler($_REQUEST);

?>
<!Doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title> <?=$row_site->TITLE?> | Menü Listesi </title>
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
                                    <div class="col-md-6 offset-3">
                                        <div class="card mb-6">
                                            <div class="card-header header-elements bg-primary py-1">
                                                <h6 class="mb-0 me-2 text-white"> <i class="ri-home-4-line fs-4 me-2"></i> Anamenüler</h6>
                                                <div class="card-header-elements ms-auto">
                                                    <a href="javascript:;" class="btn btn-icon text-white float-right border-white borderd-radius btn-sm" data-bs-target="#anamenuEkleModal" data-bs-toggle="modal"><i class="ri-add-line fs-4"></i></a>
                                                </div>
                                            </div>
                                            <div class="card-body mt-2">
                                                <div class="card-datatable text-nowrap table-responsive">
                                                    <table class="table table-hover table-sm">
                                                        <thead class="thead-themed fw-bold py-0">
                                                            <tr class="table-primary">
                                                                <td>#</td>
                                                                <td>İcon</td>
                                                                <td>Anamenü</td>
                                                                <td>Klasör</td>
                                                                <td>Durum</td>
                                                                <td align="center">Sıra</td>
                                                                <td></td>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?foreach ($rows_anamenuler as $key => $row_anamenuler) {?>
                                                                <tr>
                                                                    <td><?=($key+1)?></td>
                                                                    <td><i class="<?=$row_anamenuler->ICON?>"></i></td>
                                                                    <td><?=$row_anamenuler->ANAMENU?></td>
                                                                    <td><?=$row_anamenuler->ROUTE?></td>
                                                                    <td><?=fncDurumSpan($row_anamenuler->DURUM)?></td>
                                                                    <td align="center"><?=$row_anamenuler->SIRA?></td>
                                                                    <td align="right">
                                                                        <a href="javascript:;" class="btn btn-primary btn-icon btn-sm" data-id="<?=$row_anamenuler->ID?>" onclick="fncAnamenuBilgisi(this)" data-bs-target="#anamenuDuzenleModal" data-bs-toggle="modal"><i class="ri-pencil-line"></i></a>
                                                                        <a href="javascript:;" class="btn btn-danger btn-icon btn-sm" data-id="<?=$row_anamenuler->ID?>" onclick="fncAnamenuSil(this)"><i class="ri-delete-bin-5-line"></i></a>
                                                                    </td>
                                                                </tr>
                                                            <?}?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="card mb-6">
                                            <div class="card-header header-elements bg-primary py-1">
                                                <h6 class="mb-0 me-2 text-white"> <i class="ri-home-4-line fs-4 me-2"></i> Menüler</h6>
                                                <div class="card-header-elements ms-auto">
                                                    <a href="javascript:;" class="btn btn-icon text-white float-right border-white borderd-radius btn-sm" data-bs-target="#menuEkleModal" data-bs-toggle="modal"><i class="ri-add-line fs-4"></i></a>
                                                </div>
                                            </div>
                                            <div class="card-body mt-2">
                                                <div class="card-datatable text-nowrap table-responsive">
                                                    <table class="table table-hover table-sm">
                                                        <thead class="thead-themed fw-bold py-0">
                                                            <tr class="table-primary">
                                                                <td>#</td>
                                                                <td>Anamenü</td>
                                                                <td>Menü</td>
                                                                <td>Klasör</td>
                                                                <td>Durum</td>
                                                                <td align="center">Sıra</td>
                                                                <td></td>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?foreach ($rows_menuler as $key => $row_menuler) {?>
                                                                <tr>
                                                                    <td><?=($key+1)?></td>
                                                                    <td><?=$row_menuler->ANAMENU?></td>
                                                                    <td><?=$row_menuler->MENU?></td>
                                                                    <td><?=$row_menuler->ROUTE?></td>
                                                                    <td><?=fncDurumSpan($row_menuler->DURUM)?></td>
                                                                    <td align="center"><?=$row_menuler->SIRA?></td>
                                                                    <td align="right">
                                                                        <a href="javascript:;" class="btn btn-primary btn-icon btn-sm" data-id="<?=$row_menuler->ID?>" onclick="fncMenuBilgisi(this)" data-bs-target="#menuDuzenleModal" data-bs-toggle="modal"><i class="ri-pencil-line"></i></a>
                                                                        <a href="javascript:;" class="btn btn-danger btn-icon btn-sm" data-id="<?=$row_menuler->ID?>" onclick="fncMenuSil(this)"><i class="ri-delete-bin-5-line"></i></a>
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

<div class="modal fade" id="anamenuEkleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-edit-user">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-body p-0">
                <div class="text-center mb-6">
                    <h4 class="mb-2">Anamenü Ekle</h4>
                </div>
                <form id="anamenuEkle" class="row g-5">
                    <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="ad" name="ad" class="form-control" maxlength="45"/>
                            <label>Ad</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="klasor" name="klasor" class="form-control" maxlength="45"/>
                            <label>Klasör</label>
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
                            <input type="text" id="icon" name="icon" class="form-control" maxlength="45"/>
                            <label>İcon</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="number" id="sira" name="sira" class="form-control" maxlength="3"/>
                            <label>Sıra</label>
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

<div class="modal fade" id="anamenuDuzenleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-edit-user">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-body p-0">
                <div class="text-center mb-6">
                    <h4 class="mb-2">Anamenü Düzenle</h4>
                </div>
                <form id="anamenuDuzenle" class="row g-5">
                    <input type="hidden" name="id" id="id" value="">
                    <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="ad" name="ad" class="form-control" maxlength="45"/>
                            <label>Ad</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="klasor" name="klasor" class="form-control" maxlength="45"/>
                            <label>Klasör</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline">
                            <select name="durum" id="durum2" class="select2 form-select" data-style="btn-default">
                                <?=$cKullanici->Durum()->setSecilen(1)->getSelect("ID", "AD")?>
                            </select>
                            <label>Durum</label>
                        </div>
                    </div>
                     <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="icon" name="icon" class="form-control" maxlength="45"/>
                            <label>İcon</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="number" id="sira" name="sira" class="form-control" maxlength="3"/>
                            <label>Sıra</label>
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

<div class="modal fade" id="menuEkleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-edit-user">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-body p-0">
                <div class="text-center mb-6">
                    <h4 class="mb-2">Menü Ekle</h4>
                </div>
                <form id="menuEkle" class="row g-5">
                    <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline">
                            <select name="anamenu_id" id="anamenu_id2" class="select2 form-select" data-style="btn-default">
                                <?=$cMenu->Anamenuler()->setSecilen()->getSelect("ID", "AD")?>
                            </select>
                            <label>Anamenü</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 select2-primary">
                        <div class="form-floating form-floating-outline">
                            <select name="yetki_ids[]" id="yetki_ids" class="select2 form-select" data-style="btn-default" multiple>
                                <?=$cMenu->Yetkiler()->setSecilen()->getSelect("ID", "AD")?>
                            </select>
                            <label>Yetkiler</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="ad" name="ad" class="form-control" maxlength="45"/>
                            <label>Ad</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="klasor" name="klasor" class="form-control" maxlength="45"/>
                            <label>Klasör</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="route" name="route" class="form-control" maxlength="70"/>
                            <label>Route</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline">
                            <select name="durum" id="durum3" class="select2 form-select" data-style="btn-default">
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
                    <div class="col-12 text-right d-flex flex-wrap justify-content-end gap-4 row-gap-4">
                        <button type="submit" class="btn btn-primary">Kaydet</button>
                        <button type="reset" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Kapat">Kapat</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="menuDuzenleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-edit-user">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-body p-0">
                <div class="text-center mb-6">
                    <h4 class="mb-2">Menü Düzenle</h4>
                </div>
                <form id="menuDuzenle" class="row g-5">
                    <input type="hidden" name="id" id="id" value="">
                    <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline">
                            <select name="anamenu_id" id="anamenu_id" class="select2 form-select" data-style="btn-default">
                                <?=$cMenu->Anamenuler()->setSecilen()->getSelect("ID", "AD")?>
                            </select>
                            <label>Anamenü</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 select2-primary">
                        <div class="form-floating form-floating-outline">
                            <select name="yetki_ids[]" id="yetki_ids2" class="select2 form-select" data-style="btn-default" multiple>
                                <?=$cMenu->Yetkiler()->setSecilen()->getSelect("ID", "AD")?>
                            </select>
                            <label>Yetkiler</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="ad" name="ad" class="form-control" maxlength="45"/>
                            <label>Ad</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="klasor" name="klasor" class="form-control" maxlength="45"/>
                            <label>Klasör</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="route" name="route" class="form-control" maxlength="70"/>
                            <label>Route</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline">
                            <select name="durum" id="durum4" class="select2 form-select" data-style="btn-default">
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

    $("#anamenuEkle").on("submit", function(event) {
        event.preventDefault();
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: $(this).serialize() + "&controller=menu&action=anamenu_ekle",
            dataType: "json",
            success: function(response) {
                if (response.HATA) {
                    notyf.error(response.ACIKLAMA);
                } else {
                    notyf.success(response.ACIKLAMA);
                    location.reload(true);
                }
            }
        });
    });

    function fncAnamenuSil(obj){
        sweatAlert("Emin Misiniz?", "Evet, Sil").then(function (result) {
            if (result.value) {
                showSpinner();
                $.ajax({
                    url: "/router.php",
                    type: "POST",
                    data: {id: $(obj).data("id"), controller: "menu", action: "anamenu_sil"},
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

    function fncAnamenuBilgisi(obj){
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: {id: $(obj).data("id"), controller: "menu", action: "anamenu_bilgisi"},
            dataType: "json",
            success: function(response) {
                $.unblockUI();
                if (response.HATA) {
                    Swal.fire({title: 'Uyarı!',text: response.ACIKLAMA ,icon: 'warning' ,customClass: {confirmButton: 'btn btn-primary waves-effect waves-light'},buttonsStyling: false});
                } else {
                    $("#anamenuDuzenleModal #id").val(response.ROW.ID);
                    $("#anamenuDuzenleModal #ad").val(response.ROW.ANAMENU);
                    $("#anamenuDuzenleModal #klasor").val(response.ROW.ROUTE);
                    $("#anamenuDuzenleModal #durum2").val(response.ROW.DURUM).trigger('change');
                    $("#anamenuDuzenleModal #icon").val(response.ROW.ICON);
                    $("#anamenuDuzenleModal #sira").val(response.ROW.SIRA);
                }
            }
        });
    }

    $("#anamenuDuzenle").on("submit", function(event) {
        event.preventDefault();
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: $(this).serialize() + "&controller=menu&action=anamenu_kaydet",
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

    function fncMenuBilgisi(obj){
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: {id: $(obj).data("id"), controller: "menu", action: "menu_bilgisi"},
            dataType: "json",
            success: function(response) {
                $.unblockUI();
                if (response.HATA) {
                    Swal.fire({title: 'Uyarı!',text: response.ACIKLAMA ,icon: 'warning' ,customClass: {confirmButton: 'btn btn-primary waves-effect waves-light'},buttonsStyling: false});
                } else {
                    $("#menuDuzenleModal #id").val(response.ROW.ID);
                    $("#menuDuzenleModal #anamenu_id").val(response.ROW.ANAMENU_ID).trigger('change');
                    $("#menuDuzenleModal #yetki_ids2").val(response.ROW.YETKI_IDS).trigger('change');
                    $("#menuDuzenleModal #ad").val(response.ROW.MENU);
                    $("#menuDuzenleModal #klasor").val(response.ROW.LINK);
                    $("#menuDuzenleModal #route").val(response.ROW.ROUTE);
                    $("#menuDuzenleModal #durum4").val(response.ROW.DURUM).trigger('change');
                    $("#menuDuzenleModal #sira").val(response.ROW.SIRA);
                }
            }
        });
    }

    $("#menuEkle").on("submit", function(event) {
        event.preventDefault();
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: $(this).serialize() + "&controller=menu&action=menu_ekle",
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
    
    $("#menuDuzenle").on("submit", function(event) {
        event.preventDefault();
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: $(this).serialize() + "&controller=menu&action=menu_kaydet",
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

    function fncMenuSil(obj){
        sweatAlert("Emin Misiniz?", "Evet, Sil").then(function (result) {
            if (result.value) {
                showSpinner();
                $.ajax({
                    url: "/router.php",
                    type: "POST",
                    data: {id: $(obj).data("id"), controller: "menu", action: "menu_sil"},
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



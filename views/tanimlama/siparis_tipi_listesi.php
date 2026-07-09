<?
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
    session_kontrol();

    $result = $cSiparis->getSiparisTipleri($_REQUEST);
    $rows = $result['rows'];
?>
<!Doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title> <?=$row_site->TITLE?> | Sipariş Tipleri </title>
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
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card mb-6">
                                        <div class="card-header header-elements bg-primary py-1">
                                            <h6 class="mb-0 me-2 text-white"> <i class="ri-list-check-3 fs-4 me-2"></i> Sipariş Tipleri <small><?=$result["sayfa_araligi"]?></small></h6>
                                            <div class="card-header-elements ms-auto">
                                                <a href="javascript:;" class="btn btn-icon text-white float-right border-white borderd-radius btn-sm" data-bs-target="#tipEkleModal" data-bs-toggle="modal"><i class="ri-add-line fs-4"></i></a>
                                            </div>
                                        </div>
                                        <div class="card-body mt-2">
                                            <div class="card-datatable text-nowrap table-responsive">
                                                <table class="table table-hover table-sm">
                                                    <thead class="thead-themed fw-bold py-0">
                                                        <tr class="table-primary">
                                                            <td>#</td>
                                                            <td>Sipariş Tipi</td>
                                                            <td>Açıklama</td>
                                                            <td align="center">Durum</td>
                                                            <td></td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?if(empty($rows)){?>
                                                            <tr>
                                                                <td colspan="5" class="text-center py-4 text-muted">Kayıt bulunamadı.</td>
                                                            </tr>
                                                        <?}else{?>
                                                            <?foreach ($rows as $key => $row) {?>
                                                                <tr>
                                                                    <td><?=($key+1)?></td>
                                                                    <td class="fw-semibold"><?=$row->SIPARIS_TIPI?></td>
                                                                    <td><?=htmlspecialchars($row->ACIKLAMA)?></td>
                                                                    <td align="center"><?=fncDurumSpan($row->DURUM)?></td>
                                                                    <td align="right">
                                                                        <a href="javascript:;" class="btn btn-primary btn-icon btn-sm" data-id="<?=$row->ID?>" onclick="fncTipBilgisi(this)" data-bs-target="#tipDuzenleModal" data-bs-toggle="modal"><i class="ri-pencil-line"></i></a>
                                                                        <a href="javascript:;" class="btn btn-danger btn-icon btn-sm" data-id="<?=$row->ID?>" onclick="fncTipSil(this)"><i class="ri-delete-bin-5-line"></i></a>
                                                                    </td>
                                                                </tr>
                                                            <?}?>
                                                        <?}?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="pagination d-flex justify-content-center mt-3">
                                            <?=$result['sayfalama']->sayfalamaOlustur();?>
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

<!-- Ekleme Modalı -->
<div class="modal fade" id="tipEkleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sipariş Tipi Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="tipEkle">
                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-md-12">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="tip_adi" name="siparis_tipi" class="form-control" placeholder="Sipariş Tipi" required />
                                <label for="tip_adi">Sipariş Tipi (Örn: Paket Servis)</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-floating form-floating-outline">
                                <select name="durum" id="tip_durum" class="select2 form-select">
                                    <option value="1">Aktif</option>
                                    <option value="0">Pasif</option>
                                </select>
                                <label for="tip_durum">Durum</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-floating form-floating-outline">
                                <textarea name="aciklama" id="tip_aciklama" class="form-control" placeholder="Açıklama (Opsiyonel)" style="height: 80px;"></textarea>
                                <label for="tip_aciklama">Açıklama (Opsiyonel)</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Düzenleme Modalı -->
<div class="modal fade" id="tipDuzenleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sipariş Tipi Düzenle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="tipDuzenle">
                <input type="hidden" name="id" id="edit_id" value="">
                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-md-12">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="edit_tip" name="siparis_tipi" class="form-control" placeholder="Sipariş Tipi" required />
                                <label for="edit_tip">Sipariş Tipi</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-floating form-floating-outline">
                                <select name="durum" id="edit_durum" class="select2 form-select">
                                    <option value="1">Aktif</option>
                                    <option value="0">Pasif</option>
                                </select>
                                <label for="edit_durum">Durum</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-floating form-floating-outline">
                                <textarea name="aciklama" id="edit_aciklama" class="form-control" placeholder="Açıklama (Opsiyonel)" style="height: 80px;"></textarea>
                                <label for="edit_aciklama">Açıklama (Opsiyonel)</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
                    <button type="submit" class="btn btn-primary">Değişiklikleri Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    function fncTipBilgisi(obj){
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: {id: $(obj).data("id"), controller: "siparis", action: "siparis_tipi_bilgisi"},
            dataType: "json",
            success: function(response) {
                $.unblockUI();
                if (response.HATA) {
                    notyf.error(response.ACIKLAMA);
                } else {
                    $("#tipDuzenleModal #edit_id").val(response.ROW.ID);
                    $("#tipDuzenleModal #edit_tip").val(response.ROW.SIPARIS_TIPI);
                    $("#tipDuzenleModal #edit_durum").val(response.ROW.DURUM).trigger('change');
                    $("#tipDuzenleModal #edit_aciklama").val(response.ROW.ACIKLAMA);
                }
            }
        });
    }

    $("#tipEkle").on("submit", function(event) {
        event.preventDefault();
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: $(this).serialize() + "&controller=siparis&action=siparis_tipi_ekle",
            dataType: "json",
            success: function(response) {
                $.unblockUI();
                if (response.HATA) {
                    notyf.error(response.ACIKLAMA);
                } else {
                    notyf.success(response.ACIKLAMA);
                    location.reload();
                }
            }
        });
    });
    
    $("#tipDuzenle").on("submit", function(event) {
        event.preventDefault();
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: $(this).serialize() + "&controller=siparis&action=siparis_tipi_kaydet",
            dataType: "json",
            success: function(response) {
                $.unblockUI();
                if (response.HATA) {
                    notyf.error(response.ACIKLAMA);
                } else {
                    notyf.success(response.ACIKLAMA);
                    location.reload();
                }
            }
        });
    });

    function fncTipSil(obj){
        sweatAlert("Bu sipariş tipini silmek istediğinize emin misiniz?", "Evet, Sil").then(function (result) {
            if (result.value) {
                showSpinner();
                $.ajax({
                    url: "/router.php",
                    type: "POST",
                    data: {id: $(obj).data("id"), controller: "siparis", action: "siparis_tipi_sil"},
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

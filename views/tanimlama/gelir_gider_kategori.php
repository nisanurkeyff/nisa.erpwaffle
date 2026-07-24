<?
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
    session_kontrol();

    $rows_gelir_kategoriler = $cGelirGider->getKategoriler(['tip' => 'GELIR']);
    $rows_gider_kategoriler = $cGelirGider->getKategoriler(['tip' => 'GIDER']);
?>
<!Doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title> <?=$row_site->TITLE?> | Gelir / Gider Kategori Tanımları </title>
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
                                <!-- Gelir Kategorileri (Sol) -->
                                <div class="col-md-6">
                                    <div class="card mb-6">
                                        <div class="card-header header-elements bg-success py-1">
                                            <h6 class="mb-0 me-2 text-white"> <i class="ri-login-circle-line fs-4 me-2"></i> Gelir Kaynakları / Kategorileri</h6>
                                            <div class="card-header-elements ms-auto">
                                                <a href="javascript:;" class="btn btn-icon text-white float-right border-white border-radius btn-sm" onclick="fncYeniKategori('GELIR')"><i class="ri-add-line fs-4"></i></a>
                                            </div>
                                        </div>
                                        <div class="card-body mt-2">
                                            <div class="card-datatable text-nowrap table-responsive">
                                                <table class="table table-hover table-sm">
                                                    <thead class="thead-themed fw-bold py-0">
                                                        <tr class="table-success">
                                                            <td>#</td>
                                                            <td>İkon</td>
                                                            <td>Kategori Adı</td>
                                                            <td align="right">İşlemler</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?foreach ($rows_gelir_kategoriler as $key => $row) {?>
                                                            <tr>
                                                                <td><?=($key+1)?></td>
                                                                <td><i class="<?=$row->ICON ? $row->ICON : 'ri-folder-line'?> fs-4 text-success"></i></td>
                                                                <td class="fw-bold"><?=$row->KATEGORI?></td>
                                                                <td align="right">
                                                                    <a href="javascript:;" class="btn btn-primary btn-icon btn-sm" data-id="<?=$row->ID?>" onclick="fncKategoriBilgisi(this)"><i class="ri-pencil-line"></i></a>
                                                                    <a href="javascript:;" class="btn btn-danger btn-icon btn-sm" data-id="<?=$row->ID?>" onclick="fncKategoriSil(this)"><i class="ri-delete-bin-5-line"></i></a>
                                                                </td>
                                                            </tr>
                                                        <?}?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Gider Kategorileri (Sağ) -->
                                <div class="col-md-6">
                                    <div class="card mb-6">
                                        <div class="card-header header-elements bg-danger py-1">
                                            <h6 class="mb-0 me-2 text-white"> <i class="ri-logout-circle-line fs-4 me-2"></i> Gider Kategorileri</h6>
                                            <div class="card-header-elements ms-auto">
                                                <a href="javascript:;" class="btn btn-icon text-white float-right border-white border-radius btn-sm" onclick="fncYeniKategori('GIDER')"><i class="ri-add-line fs-4"></i></a>
                                            </div>
                                        </div>
                                        <div class="card-body mt-2">
                                            <div class="card-datatable text-nowrap table-responsive">
                                                <table class="table table-hover table-sm">
                                                    <thead class="thead-themed fw-bold py-0">
                                                        <tr class="table-danger">
                                                            <td>#</td>
                                                            <td>İkon</td>
                                                            <td>Kategori Adı</td>
                                                            <td align="right">İşlemler</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?foreach ($rows_gider_kategoriler as $key => $row) {?>
                                                            <tr>
                                                                <td><?=($key+1)?></td>
                                                                <td><i class="<?=$row->ICON ? $row->ICON : 'ri-folder-line'?> fs-4 text-danger"></i></td>
                                                                <td class="fw-bold"><?=$row->KATEGORI?></td>
                                                                <td align="right">
                                                                    <a href="javascript:;" class="btn btn-primary btn-icon btn-sm" data-id="<?=$row->ID?>" onclick="fncKategoriBilgisi(this)"><i class="ri-pencil-line"></i></a>
                                                                    <a href="javascript:;" class="btn btn-danger btn-icon btn-sm" data-id="<?=$row->ID?>" onclick="fncKategoriSil(this)"><i class="ri-delete-bin-5-line"></i></a>
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
<div class="modal fade" id="kategoriEkleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary py-3">
                <h5 class="modal-title text-white">Yeni Kategori Ekle</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="kategoriEkle" class="row g-5">
                    <div class="col-md-12">
                        <div class="form-floating form-floating-outline">
                            <select name="tip" id="tip" class="select2 form-select" required>
                                <option value="GELIR">Gelir</option>
                                <option value="GIDER">Gider</option>
                            </select>
                            <label>İşlem Tipi</label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="kategori" name="kategori" class="form-control" placeholder="Örn: Kira, Yemek Kartı" required maxlength="100"/>
                            <label>Kategori Adı</label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="icon" name="icon" class="form-control" placeholder="Örn: ri-home-line, ri-team-line" value="ri-folder-line"/>
                            <label>İkon Sınıfı (Remix Icon)</label>
                        </div>
                    </div>
                    <div class="col-12 text-end pt-3">
                        <button type="submit" class="btn btn-primary me-2">Kaydet</button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Düzenleme Modalı -->
<div class="modal fade" id="kategoriDuzenleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary py-3">
                <h5 class="modal-title text-white">Kategori Düzenle</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="kategoriDuzenle" class="row g-5">
                    <input type="hidden" name="id" id="id" value="">
                    <div class="col-md-12">
                        <div class="form-floating form-floating-outline">
                            <select name="tip" id="tip" class="select2 form-select" required>
                                <option value="GELIR">Gelir</option>
                                <option value="GIDER">Gider</option>
                            </select>
                            <label>İşlem Tipi</label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="kategori" name="kategori" class="form-control" required maxlength="100"/>
                            <label>Kategori Adı</label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="icon" name="icon" class="form-control" placeholder="Örn: ri-home-line"/>
                            <label>İkon Sınıfı (Remix Icon)</label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-floating form-floating-outline">
                            <select name="durum" id="durum" class="select2 form-select">
                                <option value="1">Aktif</option>
                                <option value="0">Pasif</option>
                            </select>
                            <label>Durum</label>
                        </div>
                    </div>
                    <div class="col-12 text-end pt-3">
                        <button type="submit" class="btn btn-primary me-2">Güncelle</button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function fncYeniKategori(tip) {
        $("#kategoriEkleModal #tip").val(tip).trigger('change');
        $("#kategoriEkleModal #kategori").val('');
        $("#kategoriEkleModal #icon").val('ri-folder-line');
        var myModal = new bootstrap.Modal(document.getElementById('kategoriEkleModal'));
        myModal.show();
    }

    $("#kategoriEkle").on("submit", function(event) {
        event.preventDefault();
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: $(this).serialize() + "&controller=gelirGider&action=kategori_ekle",
            dataType: "json",
            success: function(response) {
                $.unblockUI();
                if (response.HATA) {
                    notyf.error(response.ACIKLAMA);
                } else {
                    notyf.success(response.ACIKLAMA);
                    setTimeout(function(){ location.reload(); }, 1000);
                }
            }
        });
    });

    function fncKategoriBilgisi(obj) {
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: {id: $(obj).data("id"), controller: "gelirGider", action: "kategori_bilgisi"},
            dataType: "json",
            success: function(response) {
                $.unblockUI();
                if (response.HATA) {
                    notyf.error(response.ACIKLAMA);
                } else {
                    $("#kategoriDuzenleModal #id").val(response.ROW.ID);
                    $("#kategoriDuzenleModal #tip").val(response.ROW.TIP).trigger('change');
                    $("#kategoriDuzenleModal #kategori").val(response.ROW.KATEGORI);
                    $("#kategoriDuzenleModal #icon").val(response.ROW.ICON);
                    $("#kategoriDuzenleModal #durum").val(response.ROW.DURUM).trigger('change');
                    var myModal = new bootstrap.Modal(document.getElementById('kategoriDuzenleModal'));
                    myModal.show();
                }
            }
        });
    }

    $("#kategoriDuzenle").on("submit", function(event) {
        event.preventDefault();
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: $(this).serialize() + "&controller=gelirGider&action=kategori_kaydet",
            dataType: "json",
            success: function(response) {
                $.unblockUI();
                if (response.HATA) {
                    notyf.error(response.ACIKLAMA);
                } else {
                    notyf.success(response.ACIKLAMA);
                    setTimeout(function(){ location.reload(); }, 1000);
                }
            }
        });
    });

    function fncKategoriSil(obj) {
        sweatAlert("Bu kategoriyi silmek istediğinize emin misiniz?", "Evet, Sil").then(function (result) {
            if (result.value) {
                showSpinner();
                $.ajax({
                    url: "/router.php",
                    type: "POST",
                    data: {id: $(obj).data("id"), controller: "gelirGider", action: "kategori_sil"},
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

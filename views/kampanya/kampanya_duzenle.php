<?
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
    session_kontrol();
    
    $row            = $cKampanya->getKampanya($_REQUEST);
    fncTokenKontrol($row);

?>
<!Doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title> <?=$row_site->TITLE?> | Şube Düzenle </title>
        <?=$cTheme->Linkler()?>
        <link rel="stylesheet" href="/assets/css/bootstrap-duallistbox.css" />
    </head>
    <style type="text/css">
        .moveall,
        .removeall {
         border: 1px solid #ccc !important;
        }
        .moveall:hover,
        .removeall:hover {
          background: #efefef;
        }
        .moveall::after {
          content: attr(title);
          
        }
        .removeall::after {
          content: attr(title);
        }
        .form-control option {
            padding: 10px;
            border-bottom: 1px solid #efefef;
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
                            <div class="row gy-6 gy-md-0">
                                
                                <div class="col-xl-12">
                                    <div class="card mb-6">
                                        <div class="card-header overflow-hidden">
                                            <ul class="nav nav-tabs" role="tablist">
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link waves-effect active" data-bs-toggle="tab" data-bs-target="#tab_kampanya" role="tab" aria-selected="true">
                                                    <span class="ri-user-line ri-20px d-sm-none"></span><span class="d-none d-sm-block">Kampanya Bilgisi</span></button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link waves-effect" data-bs-toggle="tab" data-bs-target="#tab_sube" role="tab" aria-selected="true">
                                                    <span class="ri-user-line ri-20px d-sm-none"></span><span class="d-none d-sm-block">Şubeler</span></button>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="tab-content">

                                            <div class="tab-pane fade active show" id="tab_kampanya" role="tabpanel">
                                                <form id="musteriKaydet" enctype="multipart/form-data">
                                                    <input type="hidden" name="id" id="id" value="<?=$row->ID?>">
                                                    <div class="row g-6">
                                                        <div class="col-md-12">
                                                            <div class="d-flex align-items-start align-items-sm-center gap-6">
                                                                <img src="<?=$row->RESIM_URL?>" class="d-block w-px-100 h-px-100 rounded-4 fancybox" alt="kampanya resmi"/>
                                                                <div class="button-wrapper">
                                                                    <label for="resim" class="btn btn-primary me-3 mb-4" tabindex="0">
                                                                        <span class="d-none d-sm-block">Resim Yükle</span>
                                                                        <i class="ri-upload-2-line d-block d-sm-none"></i>
                                                                        <input type="file" id="resim" name="resim" class="account-file-input" hidden accept=".jpg, .jpeg, .png, .pdf" />
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="input-group input-group-merge">
                                                                <span class="input-group-text"><i class="ri-user-add-line"></i></span>
                                                                <div class="form-floating form-floating-outline">
                                                                    <input type="text" id="kampanya" name="kampanya" class="form-control" value="<?=$row->KAMPANYA?>" onchange="this.value=this.value.turkishToUpper();" placeholder="Kampanya">
                                                                    <label>Kampanya</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-floating form-floating-outline">
                                                                <select name="kategori_id" id="kategori_id" class="select2 form-select" data-style="btn-default">
                                                                    <?=$cUrun->Kategoriler()->setSecilen($row->KATEGORI_ID)->setSeciniz()->getSelect("ID", "AD")?>
                                                                </select>
                                                                <label>Kategori</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-6">
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="text" id="bas_tarih" name="bas_tarih" class="form-control datepicker active" value="<?=$row->BAS_TARIH?>" placeholder="YYYY-MM-DD" readonly="readonly">
                                                                <label>Başlangıç Tarih</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-6">
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="text" id="bas_saat" name="bas_saat" placeholder="20:00" class="form-control time-mask" value="<?=$row->BAS_SAAT?>" autocomplete="off">
                                                                <label>Başlangıç Saat</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-6">
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="text" id="bit_tarih" name="bit_tarih" class="form-control datepicker active" placeholder="YYYY-MM-DD" value="<?=$row->BIT_TARIH?>" readonly="readonly">
                                                                <label>Bitiş Tarih</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-6">
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="text" id="bit_saat" name="bit_saat" placeholder="20:00" class="form-control time-mask" value="<?=$row->BIT_SAAT?>" autocomplete="off">
                                                                <label>Bitiş Saat</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-6">
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="text" id="kampanya_bas_saat" name="kampanya_bas_saat" placeholder="20:00" class="form-control time-mask" value="<?=$row->KAMPANYA_BAS_SAAT?>" autocomplete="off">
                                                                <label>Kampanya Başlangıç Saat</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-6">
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="text" id="kampanya_bit_saat" name="kampanya_bit_saat" placeholder="20:00" class="form-control time-mask" value="<?=$row->KAMPANYA_BIT_SAAT?>" autocomplete="off">
                                                                <label>Kampanya Bitiş Saat</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="input-group input-group-merge">
                                                                <span class="input-group-text"><i class="ri-chat-4-line"></i></span>
                                                                <div class="form-floating form-floating-outline">
                                                                    <textarea class="form-control" id="aciklama" name="aciklama"  placeholder="Açıklama" style="height: 81px;"><?=$row->ACIKLAMA?></textarea>
                                                                    <label for="basic-icon-default-message">Açıklama</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="text-end pt-6">
                                                        <button type="submit" class="btn btn-primary me-4 waves-effect waves-light">Kaydet</button>
                                                        <button type="reset" class="btn btn-outline-secondary waves-effect">Geri Al</button>
                                                    </div>
                                                </form>
                                            </div>

                                            <div class="tab-pane fade" id="tab_sube" role="tabpanel">

                                                <form id="subeKaydet">
                                                    <input type="hidden" name="id" id="id" value="<?=$row->ID?>">
                                                        <div class="row g-6">
                                                            <div class="col-md-12">
                                                                <select name="sube_ids[]" id="sube_ids" size="10" multiple="multiple" style="height: 500px;">
                                                                    <?=$cKampanya->Subeler()->setSecilen($row->SUBE_IDS)->getSelect("ID", "AD")?>
                                                                </select>
                                                            </div>
                                                            <div class="text-end pt-6">
                                                                <button type="submit" class="btn btn-primary me-4 waves-effect waves-light">Kaydet</button>
                                                            </div>
                                                        </div>
                                                    </form>
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
        <script src="/assets/js/jquery.bootstrap-duallistbox.js"></script>
    </body>
</html>

<script type="text/javascript">

    $("#musteriKaydet").on("submit", function(event) {
        event.preventDefault();
        var formData = new FormData(this);

        formData.append("controller", "kampanya");
        formData.append("action", "kampanya_kaydet");

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

    $("#subeKaydet").on("submit", function(event) {
        event.preventDefault();
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: $(this).serialize() + "&controller=kampanya&action=kampanya_sube_kaydet",
            dataType: "json",
            success: function(response) {
                $.unblockUI();
                if (response.HATA) {
                    notyf.error(response.ACIKLAMA);
                } else {
                    notyf.success(response.ACIKLAMA);
                    location.href = response.URL;
                }
            }
        });
    });

    var demo1 = $('select[name="sube_ids[]"]').bootstrapDualListbox({
        nonSelectedListLabel: 'Şubeler',
        selectedListLabel: 'Seçilen Şubeler',
        moveAllLabel: 'Hepsini Aktar',
        removeAllLabel: 'Hepsini Geri Al'
    });

</script>
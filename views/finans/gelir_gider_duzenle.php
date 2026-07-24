<?
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
    session_kontrol();

    $row = $cGelirGider->getGelirGider($_REQUEST['id']);
    if (is_null($row->ID)) {
        header("Location: /views/finans/gelir_gider_listesi.php");
        exit;
    }

    $rows_kategoriler = $cGelirGider->getKategoriler();
    $rows_dosyalar = $cGelirGider->getFiles($row->ID);

    // Tek Kayıt Prensibi Kilit Kontrolü (SST)
    // ISLEM_KAYNAGI_ID = 1: Manuel (Düzenlenebilir), diğerleri kilitlidir.
    $is_locked = ($row->ISLEM_KAYNAGI_ID != 1);
    
    // İşlem kaynağı adını çekmek için
    $kaynak = DB::getRow("SELECT * FROM FINANS_ISLEM_KAYNAGI WHERE ID = :ID", [':ID' => $row->ISLEM_KAYNAGI_ID]);
    $kaynak_adi = $kaynak ? $kaynak->KAYNAK_ADI : 'Entegrasyon';
?>
<!Doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title> <?=$row_site->TITLE?> | Gelir / Gider Düzenle </title>
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
                                <div class="col-md-10">
                                    <div class="card mb-6">
                                        <div class="card-header header-elements bg-primary py-3">
                                            <h6 class="mb-0 me-2 text-white"> <i class="ri-pencil-line fs-4 me-2"></i> Gelir / Gider Düzenle</h6>
                                        </div>
                                        <div class="card-body mt-4">
                                            
                                            <?if($is_locked){?>
                                                <div class="alert alert-warning d-flex align-items-center" role="alert">
                                                    <i class="ri-error-warning-line me-2 fs-4"></i>
                                                    <div>
                                                        <strong>Uyarı:</strong> Bu finans kaydı <strong><?=$kaynak_adi?></strong> modülü tarafından otomatik oluşturulmuştur. 
                                                        Tek Kayıt Prensibi (SST) gereği bu ekran üzerinden düzenleme veya silme yapılamaz. 
                                                        Değişiklikleri lütfen ilgili kaynak modül ekranından gerçekleştiriniz.
                                                    </div>
                                                </div>
                                            <?}?>

                                            <form id="islemGuncelle" enctype="multipart/form-data">
                                                <input type="hidden" name="id" value="<?=$row->ID?>">
                                                <div class="row g-6">
                                                    
                                                    <div class="col-md-6">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="tip" id="tip" class="select2 form-select" required <?=$is_locked ? 'disabled' : ''?>>
                                                                <option value="">Seçiniz</option>
                                                                <option value="GELIR" <?=($row->TIP == 'GELIR') ? 'selected' : ''?>>Gelir</option>
                                                                <option value="GIDER" <?=($row->TIP == 'GIDER') ? 'selected' : ''?>>Gider</option>
                                                            </select>
                                                            <label>İşlem Tipi</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="kategori_id" id="kategori_id" class="select2 form-select" required data-secilen="<?=$row->KATEGORI_ID?>" <?=$is_locked ? 'disabled' : ''?>>
                                                                <option value="">Seçiniz</option>
                                                                <?foreach($rows_kategoriler as $kat){?>
                                                                    <option value="<?=$kat->ID?>" data-tip="<?=$kat->TIP?>" data-icon="<?=$kat->ICON?>" <?=($row->KATEGORI_ID == $kat->ID) ? 'selected' : ''?>><?=$kat->KATEGORI?></option>
                                                                <?}?>
                                                            </select>
                                                            <label>Kategori</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="cari_id" id="cari_id" class="select2 form-select" required <?=$is_locked ? 'disabled' : ''?>>
                                                                <?=$cGelirGider->Cariler()->setSecilen($row->CARI_ID)->setSeciniz()->getSelect("ID", "AD")?>
                                                            </select>
                                                            <label>Cari</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-floating form-floating-outline">
                                                            <input type="text" id="tutar" name="tutar" class="form-control decimal" value="<?=FormatSayi::sayi($row->TUTAR,2)?>" placeholder="0.00" required <?=$is_locked ? 'readonly' : ''?>>
                                                            <label>Tutar</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-floating form-floating-outline">
                                                            <input type="text" id="fatura_no" name="fatura_no" class="form-control" value="<?=$row->FATURA_NO?>" placeholder="Örn: FT-12345" <?=$is_locked ? 'readonly' : ''?>>
                                                            <label>Fatura / Belge No</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-floating form-floating-outline">
                                                            <input type="text" id="fatura_tarih" name="fatura_tarih" class="form-control datepicker active" value="<?=FormatTarih::tarih($row->FATURA_TARIH)?>" placeholder="YYYY-MM-DD" readonly="readonly" <?=$is_locked ? 'disabled' : ''?>>
                                                            <label>Fatura Tarihi</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="hareket_durumu" id="hareket_durumu" class="select2 form-select" required <?=$is_locked ? 'disabled' : ''?>>
                                                                <option value="BEKLIYOR" <?=($row->HAREKET_DURUMU == 'BEKLIYOR') ? 'selected' : ''?>>Bekliyor</option>
                                                                <option value="TAMAMLANDI" <?=($row->HAREKET_DURUMU == 'TAMAMLANDI') ? 'selected' : ''?>>Tamamlandı</option>
                                                                <option value="IPTAL" <?=($row->HAREKET_DURUMU == 'IPTAL') ? 'selected' : ''?>>İptal</option>
                                                            </select>
                                                            <label>Hareket Durumu</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12" id="divOdemeTarihi" style="<?=($row->HAREKET_DURUMU == 'TAMAMLANDI') ? '' : 'display:none;'?>">
                                                        <div class="form-floating form-floating-outline">
                                                            <input type="text" id="odeme_tarihi" name="odeme_tarihi" class="form-control datepicker active" value="<?=FormatTarih::tarih($row->ODEME_TARIHI)?>" placeholder="YYYY-MM-DD" readonly="readonly" <?=$is_locked ? 'disabled' : ''?>>
                                                            <label>Ödeme / Tahsilat Tarihi</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-floating form-floating-outline">
                                                            <textarea class="form-control" id="aciklama" name="aciklama" placeholder="Açıklama" style="height: 80px;" <?=$is_locked ? 'readonly' : ''?>><?=$row->ACIKLAMA?></textarea>
                                                            <label>Açıklama</label>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Mevcut Dosyalar -->
                                                    <?if(count2($rows_dosyalar) > 0){?>
                                                        <div class="col-md-12 mt-4">
                                                            <label class="form-label text-primary fw-bold"><i class="ri-attachment-line me-1"></i> Yüklü Evraklar</label>
                                                            <div class="list-group">
                                                                <?foreach($rows_dosyalar as $d){?>
                                                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                                                        <div>
                                                                            <i class="ri-file-text-line me-2 text-primary fs-5"></i>
                                                                            <span class="fw-bold"><?=$d->DOSYA_ADI?></span> 
                                                                            <?if(!empty($d->ACIKLAMA)){?>
                                                                                <span class="badge bg-label-info ms-2"><?=$d->ACIKLAMA?></span>
                                                                            <?}?>
                                                                            <small class="text-muted ms-3">(<?=FormatSayi::sayi($d->BOYUT / 1024, 1)?> KB)</small>
                                                                        </div>
                                                                        <div>
                                                                            <a href="/views/finans/dosya_goruntule.php?id=<?=$d->ID?>" target="_blank" class="btn btn-sm btn-outline-primary me-2"><i class="ri-eye-line me-1"></i> Gör / İndir</a>
                                                                            <?if(!$is_locked){?>
                                                                                <button type="button" class="btn btn-sm btn-danger" data-id="<?=$d->ID?>" onclick="fncDosyaSil(this)"><i class="ri-delete-bin-5-line"></i> Sil</button>
                                                                            <?}?>
                                                                        </div>
                                                                    </div>
                                                                <?}?>
                                                            </div>
                                                        </div>
                                                    <?}?>

                                                    <!-- Yeni Dosya Yükleme (Sadece kilitli değilse) -->
                                                    <?if(!$is_locked){?>
                                                        <div class="col-md-12 mt-4">
                                                            <div class="card bg-lighter border">
                                                                <div class="card-body">
                                                                    <h6 class="mb-4 text-primary"><i class="ri-attachment-2 fs-5 me-2"></i> Yeni Evrak Ekle (Çoklu Ekleme)</h6>
                                                                    <div class="table-responsive">
                                                                        <table class="table table-bordered table-sm" id="tblDosyalar">
                                                                            <thead class="table-light">
                                                                                <tr>
                                                                                    <th>Dosya Seçin (.pdf, .png, .jpg, .jpeg)</th>
                                                                                    <th>Açıklama / Belge Türü (Örn: Fatura, Dekont)</th>
                                                                                    <th width="50" class="text-center"></th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td><input type="file" name="evraklar[]" class="form-control form-control-sm" accept=".pdf,.png,.jpg,.jpeg"></td>
                                                                                    <td><input type="text" name="evrak_aciklama[]" class="form-control form-control-sm" placeholder="Fatura, Dekont, Makbuz vb."></td>
                                                                                    <td class="text-center"><button type="button" class="btn btn-sm btn-icon btn-danger" onclick="fncDosyaSatirSil(this)"><i class="ri-delete-bin-5-line"></i></button></td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                    <button type="button" class="btn btn-xs btn-success mt-2" id="btnDosyaEkle"><i class="ri-add-line me-1"></i> Yeni Dosya Satırı Ekle</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?}?>

                                                </div>
                                                <div class="text-end pt-6">
                                                    <?if(!$is_locked){?>
                                                        <button type="submit" class="btn btn-primary me-4 waves-effect waves-light">Güncelle</button>
                                                    <?}?>
                                                    <a href="/views/finans/gelir_gider_listesi.php" class="btn btn-outline-secondary waves-effect">Geri Dön</a>
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
    var tumKategoriler = [];

    $(document).ready(function() {
        // Tüm kategorileri belleğe al
        $("#kategori_id option").each(function() {
            if ($(this).val()) {
                tumKategoriler.push({
                    id: $(this).val(),
                    text: $(this).text(),
                    tip: $(this).data("tip"),
                    icon: $(this).data("icon")
                });
            }
        });

        fncKategoriFiltrele();

        // Hareket durumuna göre ödeme tarihi alanını göster/gizle
        $("#hareket_durumu").on("change", function() {
            if ($(this).val() == 'TAMAMLANDI') {
                $("#divOdemeTarihi").slideDown();
                if(!$("#odeme_tarihi").val()){
                    var today = new Date().toISOString().slice(0, 10);
                    $("#odeme_tarihi").val(today);
                }
            } else {
                $("#divOdemeTarihi").slideUp();
            }
        });

        // Yeni dosya satırı ekleme
        $("#btnDosyaEkle").on("click", function() {
            var satir = `<tr>
                <td><input type="file" name="evraklar[]" class="form-control form-control-sm" accept=".pdf,.png,.jpg,.jpeg"></td>
                <td><input type="text" name="evrak_aciklama[]" class="form-control form-control-sm" placeholder="Fatura, Dekont, Makbuz vb."></td>
                <td class="text-center"><button type="button" class="btn btn-sm btn-icon btn-danger" onclick="fncDosyaSatirSil(this)"><i class="ri-delete-bin-5-line"></i></button></td>
            </tr>`;
            $("#tblDosyalar tbody").append(satir);
        });
    });

    function fncDosyaSatirSil(obj) {
        if ($("#tblDosyalar tbody tr").length > 1) {
            $(obj).closest("tr").remove();
        } else {
            $(obj).closest("tr").find("input").val("");
        }
    }

    function fncKategoriFiltrele() {
        var secilenTip = $("#tip").val();
        var secilenKat = $("#kategori_id").data("secilen");
        
        var filtered = tumKategoriler.filter(function(k) {
            return k.tip == secilenTip;
        });
        
        var html = '<option value="">Seçiniz</option>';
        filtered.forEach(function(k) {
            var isSelected = (k.id == secilenKat) ? 'selected' : '';
            html += '<option value="' + k.id + '" ' + isSelected + '>' + k.text + '</option>';
        });
        $("#kategori_id").html(html);
    }

    $("#tip").on("change", function() {
        // Tip değiştiğinde data-secilen değerini temizle ki eski seçim kalmasın
        $("#kategori_id").data("secilen", "");
        fncKategoriFiltrele();
        $("#kategori_id").trigger('change');
    });

    function fncDosyaSil(obj) {
        sweatAlert("Bu belgeyi silmek istediğinize emin misiniz?", "Evet, Sil").then(function (result) {
            if (result.value) {
                showSpinner();
                $.ajax({
                    url: "/router.php",
                    type: "POST",
                    data: {id: $(obj).data("id"), controller: "gelirGider", action: "dosya_sil"},
                    dataType: "json",
                    success: function(response) {
                        $.unblockUI();
                        if (response.HATA) {
                            notyf.error(response.ACIKLAMA);
                        } else {
                            notyf.success(response.ACIKLAMA);
                            $(obj).closest('.list-group-item').fadeOut();
                        }
                    }
                });
            }
        });
    }

    $("#islemGuncelle").on("submit", function(event) {
        event.preventDefault();
        var formData = new FormData(this);
        formData.append("controller", "gelirGider");
        formData.append("action", "gelir_gider_guncelle");
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
                    setTimeout(function(){
                         window.location.href = "/views/finans/gelir_gider_listesi.php?route=finans/gelir_gider_listesi";
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

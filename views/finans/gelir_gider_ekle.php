<?
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
    session_kontrol();

    $rows_kategoriler = $cGelirGider->getKategoriler();
?>
<!Doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title> <?=$row_site->TITLE?> | Gelir / Gider Ekle </title>
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
                                            <h6 class="mb-0 me-2 text-white"> <i class="ri-add-line fs-4 me-2"></i> Yeni Gelir / Gider Girişi</h6>
                                        </div>
                                        <div class="card-body mt-4">
                                            <form id="islemKaydet" enctype="multipart/form-data">
                                                <div class="row g-6">
                                                    
                                                    <div class="col-md-6">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="tip" id="tip" class="select2 form-select" required>
                                                                <option value="">Seçiniz</option>
                                                                <option value="GELIR">Gelir</option>
                                                                <option value="GIDER">Gider</option>
                                                            </select>
                                                            <label>İşlem Tipi</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="kategori_id" id="kategori_id" class="select2 form-select" required>
                                                                <option value="">Seçiniz</option>
                                                                <?foreach($rows_kategoriler as $kat){?>
                                                                    <option value="<?=$kat->ID?>" data-tip="<?=$kat->TIP?>" data-icon="<?=$kat->ICON?>"><?=$kat->KATEGORI?></option>
                                                                <?}?>
                                                            </select>
                                                            <label>Kategori</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="cari_id" id="cari_id" class="select2 form-select" required>
                                                                <?=$cGelirGider->Cariler()->setSeciniz()->getSelect("ID", "AD")?>
                                                            </select>
                                                            <label>Cari</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-floating form-floating-outline">
                                                            <input type="text" id="tutar" name="tutar" class="form-control decimal" placeholder="0.00" required>
                                                            <label>Tutar</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-floating form-floating-outline">
                                                            <input type="text" id="fatura_no" name="fatura_no" class="form-control" placeholder="Örn: FT-12345">
                                                            <label>Fatura / Belge No</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-floating form-floating-outline">
                                                            <input type="text" id="fatura_tarih" name="fatura_tarih" class="form-control datepicker active" placeholder="YYYY-MM-DD" readonly="readonly">
                                                            <label>Fatura Tarihi</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="hareket_durumu" id="hareket_durumu" class="select2 form-select" required>
                                                                <option value="BEKLIYOR">Bekliyor</option>
                                                                <option value="TAMAMLANDI">Tamamlandı</option>
                                                                <option value="IPTAL">İptal</option>
                                                            </select>
                                                            <label>Hareket Durumu</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12" id="divOdemeTarihi" style="display:none;">
                                                        <div class="form-floating form-floating-outline">
                                                            <input type="text" id="odeme_tarihi" name="odeme_tarihi" class="form-control datepicker active" placeholder="YYYY-MM-DD" readonly="readonly">
                                                            <label>Ödeme / Tahsilat Tarihi</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-floating form-floating-outline">
                                                            <textarea class="form-control" id="aciklama" name="aciklama" placeholder="Açıklama" style="height: 80px;"></textarea>
                                                            <label>Açıklama</label>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Çoklu Dosya Yükleme -->
                                                    <div class="col-md-12 mt-4">
                                                        <div class="card bg-lighter border">
                                                            <div class="card-body">
                                                                <h6 class="mb-4 text-primary"><i class="ri-attachment-2 fs-5 me-2"></i> Evrak ve Belgeler (Çoklu Ekleme)</h6>
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

                                                </div>
                                                <div class="text-end pt-6">
                                                    <button type="submit" class="btn btn-primary me-4 waves-effect waves-light">Kaydet</button>
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
        var filtered = tumKategoriler.filter(function(k) {
            return k.tip == secilenTip;
        });
        
        var html = '<option value="">Seçiniz</option>';
        filtered.forEach(function(k) {
            html += '<option value="' + k.id + '">' + k.text + '</option>';
        });
        $("#kategori_id").html(html).trigger('change');
    }

    $("#tip").on("change", fncKategoriFiltrele);

    $("#islemKaydet").on("submit", function(event) {
        event.preventDefault();
        var formData = new FormData(this);
        formData.append("controller", "gelirGider");
        formData.append("action", "gelir_gider_kaydet");
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

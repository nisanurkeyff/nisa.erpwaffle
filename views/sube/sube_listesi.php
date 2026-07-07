<?
	require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
	session_kontrol();

    if(is_null($_REQUEST['durum'])){
        $_REQUEST['durum'] = 1;
    }

    $excel = new excelSayfasi();
    $excel->sutunEkle("Şube","SUBE","");
    $excel->sutunEkle("İl","IL","");
    $excel->sutunEkle("İlçe","ILCE","");
    $excel->sutunEkle("Satış Türü","SATIS_TURU","");
    $excel->sutunEkle("Güncelleme Tarihi","GTARIH","format1");
    $excel->sutunEkle("Kayıt Yapan","KAYIT_YAPAN","");
    $excel->sutunEkle("Menü Durum","MENU_DURUM_TEXT","");
    $excel->sutunEkle("Şube Durum","DURUM_TEXT","");
    $excelOut = $excel->excel();
    
    $result             = $cSube->getSubeler($_REQUEST);
    $rows               = $result['rows'];

    $_SESSION["Table"]  = $result;
    $_SESSION['excel']  = $excelOut;
?>
<!Doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title> <?=$row_site->TITLE?> | Şube Listesi </title>
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
                                <div class="col-xxl">
                                    <div class="card mb-6">
                                        <div class="card-body">
                                            <form>
                                                <input type="hidden" name="route" value="<?=$_REQUEST['route']?>">
                                                <div class="row">
                                                    <div class="col-md-4 mb-2">
                                                        <div class="input-group input-group-merge">
                                                            <span class="input-group-text"><i class="ri-user-search-line"></i></span>
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="text" id="sube" name="sube" class="form-control" value="<?=$_REQUEST['sube']?>" placeholder="Şube">
                                                                <label>Şube</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 mb-4">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="satis_turu_id" id="satis_turu_id" class="btn select2 form-select" data-style="btn-default">
                                                                <?=$cUrun->SatisTürleri()->setSecilen($_REQUEST['satis_turu_id'])->setSeciniz()->getSelect("ID", "AD")?>
                                                            </select>
                                                            <label>Satış Türü</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-2 mb-4">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="il_id" id="il_id" class="select2 form-select" data-style="btn-default">
                                                                <?=$cKullanici->Iller()->setSecilen($_REQUEST['il_id'])->setSeciniz()->getSelect("ID", "AD")?>
                                                            </select>
                                                            <label for="country-modern">İl</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-2 mb-4">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="ilce_id" id="ilce_id" class="btn select2 form-select" data-style="btn-default">
                                                                <?=$cKullanici->Ilceler(array('il_id' => $_REQUEST['il_id']))->setSecilen($_REQUEST['ilce_id'])->setSeciniz()->getSelect("ID", "AD")?>
                                                            </select>
                                                            <label for="country-modern">İlçe</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 mb-4">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="sayfalama" id="sayfalama" class="btn select2 form-select" data-style="btn-default">
                                                                <?=$cUrun->Sayfalama()->setSecilen($_REQUEST['sayfalama'])->getSelect("ID", "AD")?>
                                                            </select>
                                                            <label>Sayfalama</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 mb-2">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="menu_durum" id="menu_durum" class="select2 form-select" data-style="btn-default">
                                                                <?=$cKullanici->Durum()->setSecilen()->setSeciniz()->getSelect("ID", "AD")?>
                                                            </select>
                                                            <label>Menu Durum</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 mb-2">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="durum" id="durum" class="select2 form-select" data-style="btn-default">
                                                                <?=$cKullanici->Durum()->setSecilen()->setSeciniz()->getSelect("ID", "AD")?>
                                                            </select>
                                                            <label>Şube Durum</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 mt-1">
                                                        <button type="submit" class="btn btn-primary">Filtrele</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-6">
                                <div class="card-header header-elements bg-primary py-1">
                                    <h6 class="mb-0 me-2 text-white"> <i class="ri-group-2-line fs-4 me-2"></i> Şube Listesi <small><?=$result["sayfa_araligi"]?></small></h6>
                                    <div class="card-header-elements ms-auto">
                                        <a href="/views/sube/sube_ekle.php" data-bs-toggle="tooltip" class="btn btn-icon text-white float-right border-white border-radius btn-sm" title="Şube Ekle"><i class="ri-add-line fs-4"></i></a>
                                        <a href="../excel_sql.php" data-bs-toggle="tooltip" title="Excel" class="btn btn-icon text-white float-right border-white borderd-radius btn-sm"> <i class="ri-file-excel-2-line"></i> </a>
                                    </div>
                                </div>
                                <div class="card-body mt-2">
                                    <div class="card-datatable table-responsive">
                                        <table class="table table-hover table-sm">
                                            <thead class="thead-themed fw-bold py-0">
                                                <tr class="table-primary">
                                                    <td nowrap>#</td>
                                                    <td nowrap>Şube</td>
                                                    <td nowrap>İl</td>
                                                    <td nowrap>İlçe</td>
                                                    <td nowrap>Satış Türü</td>
                                                    <td nowrap align="center">Menü QR</td>
                                                    <td nowrap align="center" >Güncelleme Tarih</td>
                                                    <td nowrap>Kayıt Yapan</td>
                                                    <td nowrap align="center">Menü Durum</td>
                                                    <td nowrap align="center">Şube Durum</td>
                                                    <td nowrap ></td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?foreach ($rows as $key => $row) {
                                                    if($row->KAMPANYA_SAYISI > 0){
                                                        $kampanya_class = "btn-success";
                                                    }else{
                                                        $kampanya_class = "btn-outline-success";
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td><?=($key+1)?></td>
                                                        <td class="fw-bold"><?=FormatYazi::kisalt2($row->SUBE,20)?></td>
                                                        <td><?=$row->IL?></td>
                                                        <td><?=$row->ILCE?></td>
                                                        <td><?=$row->SATIS_TURU?></td>
                                                        <td nowrap align="center">
                                                            <?if(is_file(fncImgPathFolder2($row->QR_RESIM, $row_site->IMG_PATH))){?>
                                                                <a href="<?=$row_site->QR_URL?><?=$row->URL?>" data-bs-toggle="tooltip" class="btn btn-label-info btn-icon btn-sm" target="_blank" title="Menü Linke Git"> <i class="ri-link"></i></a>
                                                                <img id="qr-image-<?=$row->ID?>" src="<?=fncImgPath($row->QR_RESIM, $row_site->IMG_PATH)?>" class="fancybox" alt="QR Resim" width="50">
                                                                <button class="btn btn-label-success btn-icon btn-sm" data-bs-toggle="tooltip" title="QR'ı PDF İndir" onclick="fncQRIndir('qr-image-<?=$row->ID?>', '<?=$row->SUBE?>')"><i class="ri-download-cloud-line"></i></button>
                                                            <?}else{?>
                                                                <img src="<?=$row_site->LOGO?>" class="fancybox" alt="Menü Yönetim" width="50"/>
                                                            <?}?>
                                                        </td>
                                                        <td align="center"><?=fncTre(FormatTarih::tarih2($row->GTARIH))?></td>
                                                        <td><?=FormatYazi::kisalt2($row->KAYIT_YAPAN,25)?></td>
                                                        <td align="center">
                                                            <label class="switch">
                                                                <input type="checkbox" class="switch-input is-valid" name="menu_durum[<?=$row->ID?>]" id="menu_durum<?=$row->ID?>" data-id="<?=$row->ID?>" onchange="fncMenuDurumGuncelle(this)" <?=($row->MENU_DURUM == 1) ? 'checked' : ''?>>
                                                                <span class="switch-toggle-slider"><span class="switch-on"></span><span class="switch-off"></span></span>
                                                            </label>
                                                        </td>
                                                        <td align="center">
                                                            <label class="switch">
                                                                <input type="checkbox" class="switch-input is-valid" name="durum[<?=$row->ID?>]" id="durum<?=$row->ID?>" data-id="<?=$row->ID?>" onchange="fncDurumGuncelle(this)" <?=($row->DURUM == 1) ? 'checked' : ''?>>
                                                                <span class="switch-toggle-slider"><span class="switch-on"></span><span class="switch-off"></span></span>
                                                            </label>
                                                        </td>
                                                        <td nowrap align="right">
                                                            <a href="javascript:;" data-bs-toggle="tooltip" class="btn btn-primary btn-icon btn-sm" data-id="<?=$row->ID?>" onclick="fncDuzenle(this)" title="Düzenle"> <i class="ri-pencil-line"></i></a>
                                                            <a href="/views/sube/urun_fiyat_guncelle.php?route=sube/urun_fiyat_guncelle&urun=&sube_id=<?=$row->ID?>&sayfalama=100" data-bs-toggle="tooltip" class="btn btn-info btn-icon btn-sm" title="Şube Menü"> <i class="ri-money-euro-circle-line"></i></a>
                                                            <a href="/views/kampanya/kampanya_listesi.php?route=kampanya/kampanya_listesi&urun=&sube_id=<?=$row->ID?>&sayfalama=100" data-bs-toggle="tooltip" class="btn <?=$kampanya_class?> btn-icon btn-sm" title="Kampanyalar"> <i class="ri-gift-2-line"></i></a>
                                                            <a href="javascript:;" data-bs-toggle="tooltip" class="btn btn-warning btn-icon btn-sm" data-id="<?=$row->ID?>" onclick="fncQrOlustur(this)" title="QR Kod Oluştur"> <i class="ri-qr-code-line"></i></a>
                                                            <a href="javascript:;" class="btn btn-danger btn-icon btn-sm" data-id="<?=$row->ID?>" onclick="fncSubeSil(this)"><i class="ri-delete-bin-5-line"></i></a>
                                                        </td>
                                                    </tr>
                                                <?}?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="pagination d-flex justify-content-center">
                                    <?=$result['sayfalama']->sayfalamaOlustur();?>
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

    function fncDuzenle(obj){
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: {id: $(obj).data("id"), controller: "sube", action: "sube_duzenle"},
            dataType: "json",
            success: function(response) {
                $.unblockUI();
                if (response.HATA) {
                    Swal.fire({title: 'Uyarı!',text: response.ACIKLAMA ,icon: 'warning' ,customClass: {confirmButton: 'btn btn-primary waves-effect waves-light'},buttonsStyling: false});
                } else {
                    location.href = response.URL;
                }
            }
        });
    }

    function fncQrOlustur(obj){
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: {id: $(obj).data("id"), controller: "sube", action: "sube_qr_olustur"},
            dataType: "json",
            success: function(response) {
                $.unblockUI();
                if (response.HATA) {
                    Swal.fire({title: 'Uyarı!',text: response.ACIKLAMA ,icon: 'warning' ,customClass: {confirmButton: 'btn btn-primary waves-effect waves-light'},buttonsStyling: false});
                } else {
                    notyf.success(response.ACIKLAMA);
                    location.reload(true);
                }
            }
        });
    }

    function fncMenuDurumGuncelle(obj) {
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: {"id": $(obj).data("id"), "menu_durum": $(obj).prop("checked") ? 1 : 0, controller: "sube", action: "menu_durum_kaydet" },
            dataType: 'json',
            success: function(response) {
                $.unblockUI();
                if (response.HATA) {
                    notyf.error(response.ACIKLAMA);
                } else {
                    notyf.success(response.ACIKLAMA);
                }
            }
        });
    }

    function fncDurumGuncelle(obj) {
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: {"id": $(obj).data("id"), "durum": $(obj).prop("checked") ? 1 : 0, controller: "sube", action: "durum_kaydet" },
            dataType: 'json',
            success: function(response) {
                $.unblockUI();
                if (response.HATA) {
                    notyf.error(response.ACIKLAMA);
                } else {
                    notyf.success(response.ACIKLAMA);
                }
            }
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        window.fncQRIndir = function (imgId, sube) {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            // PDF boyutlarını al
            const pageWidth = doc.internal.pageSize.getWidth();
            const pageHeight = doc.internal.pageSize.getHeight();

            // QR kod resmi
            const img = document.getElementById(imgId);
            const canvas = document.createElement("canvas");
            const ctx = canvas.getContext("2d");

            // QR kodunun boyutunu belirle
            const qrSize = 100; // QR kodu genişliği ve yüksekliği

            // Canvas boyutunu QR kodunun boyutuna ayarla
            canvas.width = img.naturalWidth;
            canvas.height = img.naturalHeight;
            ctx.drawImage(img, 0, 0);

            // Base64 formatına çevir
            const imgData = canvas.toDataURL("image/png");

            // QR kodunu sayfanın tam ortasına yerleştirme hesaplaması
            const centerX = (pageWidth - qrSize) / 2;  // Sayfanın ortasındaki X koordinatı
            const centerY = (pageHeight - qrSize) / 2; // Sayfanın ortasındaki Y koordinatı

            // QR kodunu PDF'e ekle
            doc.addImage(imgData, "PNG", centerX, centerY, qrSize, qrSize);

            // PDF'i indir
            doc.save(sube + " QR Kod.pdf");
        };
    });

    function fncSubeSil(obj){
        sweatAlert("Emin Misiniz?", "Evet, Sil").then(function (result) {
            if (result.value) {
                showSpinner();
                $.ajax({
                    url: "/router.php",
                    type: "POST",
                    data: {id: $(obj).data("id"), controller: "sube", action: "sube_sil"},
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



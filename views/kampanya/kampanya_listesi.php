<?
	require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
	session_kontrol();

    if(is_null($_REQUEST['durum'])){
        $_REQUEST['durum'] = 1;
    }

    $excel = new excelSayfasi();
    $excel->sutunEkle("Kampanya","KAMPANYA","");
    $excel->sutunEkle("Şube","SUBELER","");
    $excel->sutunEkle("Kategori","KATEGORI","");
    $excel->sutunEkle("Başlangıç Tarihi","BAS_TARIH","format1");
    $excel->sutunEkle("Başlangıç Saat","BAS_SAAT","");
    $excel->sutunEkle("Bitiş Tarihi","BIT_TARIH","format1");
    $excel->sutunEkle("Bitiş Saat","BIT_SAAT","");
    $excel->sutunEkle("Kayıt Yapan","KAYIT_YAPAN","");
    $excel->sutunEkle("Durum","DURUM_TEXT","");
    $excelOut = $excel->excel();
    
    $result             = $cKampanya->getKampanyalar($_REQUEST);
    $rows               = $result['rows'];

    $_SESSION["Table"]  = $result;
    $_SESSION['excel']  = $excelOut;

?>
<!Doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title> <?=$row_site->TITLE?> | Kampanya Listesi </title>
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
                                                            <span class="input-group-text"><i class="ri-gift-2-line"></i></span>
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="text" id="kampanya" name="kampanya" class="form-control" value="<?=$_REQUEST['kampanya']?>" placeholder="Kampanya">
                                                                <label>Kampanya</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-4">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="sube_id" id="sube_id" class="btn select2 form-select" data-style="btn-default">
                                                                <?=$cUrun->Subeler()->setSecilen($_REQUEST['sube_id'])->setSeciniz()->getSelect("ID", "AD")?>
                                                            </select>
                                                            <label>Şube</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 mb-4">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="kategori_id" id="kategori_id" class="btn select2 form-select" data-style="btn-default">
                                                                <?=$cUrun->Kategoriler()->setSecilen($_REQUEST['kategori_id'])->setSeciniz()->getSelect("ID", "AD")?>
                                                            </select>
                                                            <label>Kategoriler</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 mb-4">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="durum" id="durum" class="select2 form-select" data-style="btn-default">
                                                                <?=$cKullanici->Durum()->setSecilen()->setSeciniz()->getSelect("ID", "AD")?>
                                                            </select>
                                                            <label>Durum</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 mb-4">
                                                        <div class="input-group">
                                                            <div class="input-group-text form-check mb-0">
                                                                <input class="form-check-input m-auto" type="checkbox" id="tarih_var" name="tarih_var" <?=($_REQUEST['tarih_var'] == 'on') ? 'checked' : ''?> aria-label="Checkbox for following text input">
                                                            </div>
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="text" name="tarih" id="tarih" class="form-control datepicker_range" value="<?=$_REQUEST['tarih']?>">
                                                                <label for="tarih">Tarih</label>
                                                            </div>
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
                                    <h6 class="mb-0 me-2 text-white"> <i class="ri-gift-2-line fs-4 me-2"></i> Kampanya Listesi  <small><?=$result["sayfa_araligi"]?></small></h6>
                                    <div class="card-header-elements ms-auto">
                                        <a href="/views/kampanya/kampanya_ekle.php" data-bs-toggle="tooltip" class="btn btn-icon text-white float-right border-white border-radius btn-sm" title="Kampanya Ekle"><i class="ri-add-line fs-4"></i></a>
                                        <a href="../excel_sql.php" data-bs-toggle="tooltip" title="Excel" class="btn btn-icon text-white float-right border-white borderd-radius btn-sm"> <i class="ri-file-excel-2-line"></i> </a>
                                    </div>
                                </div>
                                <div class="card-body mt-2">
                                    <div class="card-datatable table-responsive">
                                        <table class="table table-hover table-sm">
                                            <thead class="thead-themed fw-bold py-0">
                                                <tr class="table-primary">
                                                    <td nowrap>#</td>
                                                    <td nowrap>Resim</td>
                                                    <td nowrap>Kampanya</td>
                                                    <td nowrap>Kategori</td>
                                                    <td nowrap align="center">Baş Tarih</td>
                                                    <td nowrap align="center">Bit Tarih</td>
                                                    <td nowrap align="center">Kamp. Baş - Bit Saat</td>
                                                    <td nowrap align="center" >Güncelleme Tarih</td>
                                                    <td nowrap>Kayıt Yapan</td>
                                                    <td nowrap align="center">Kayıt Tarih</td>
                                                    <td nowrap align="center">Durum</td>
                                                    <td nowrap ></td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?foreach ($rows as $key => $row) {?>
                                                    <tr>
                                                        <td><?=($key+1)?></td>
                                                        <td>
                                                            <?if(is_file(fncDocumentRoot($row->RESIM_URL, $row_site->IMG_PATH))){?>
                                                                <img src="<?=$row->RESIM_URL?>" class="rounded-3 fancybox" alt="Kampanya Resim" height="50">
                                                            <?}else{?>
                                                                <img src="<?=$row_site->LOGO?>" class="fancybox" alt="Menü Yönetim" height="50"/>
                                                            <?}?>
                                                        </td>
                                                        <td class="fw-bold"><?=FormatYazi::kisalt2($row->KAMPANYA,35)?></td>
                                                        <td><?=$row->KATEGORI?></td>
                                                        <td align="center"><?=FormatTarih::tarih($row->BAS_TARIH)?> <?=FormatTarih::sadeceSaat($row->BAS_SAAT)?></td>
                                                        <td align="center"><?=FormatTarih::tarih($row->BIT_TARIH)?> <?=FormatTarih::sadeceSaat($row->BIT_SAAT)?></td>
                                                        <td align="center"><?=FormatTarih::sadeceSaat($row->KAMPANYA_BAS_SAAT)?> - <?=FormatTarih::sadeceSaat($row->KAMPANYA_BIT_SAAT)?></td>
                                                        <td align="center"><?=fncTre(FormatTarih::tarih($row->GTARIH))?></td>
                                                        <td><?=FormatYazi::kisalt2($row->KAYIT_YAPAN,25)?></td>
                                                        <td align="center"><?=FormatTarih::tarih($row->TARIH)?></td>
                                                        <td align="center"><?=fncDurumSpan($row->DURUM)?></td>
                                                        <td nowrap>
                                                            <a href="javascript:;" data-bs-toggle="tooltip" class="btn btn-primary btn-icon btn-sm" data-id="<?=$row->ID?>" onclick="fncDuzenle(this)" title="Düzenle"> <i class="ri-pencil-line"></i></a>
                                                            <a href="javascript:;" class="btn btn-danger btn-icon btn-sm" data-id="<?=$row->ID?>" onclick="fncKampanyaSil(this)"><i class="ri-delete-bin-5-line"></i></a>
                                                        </td>
                                                        <tr class="bg-light">
                                                        <td colspan="13">
                                                            <strong>Şubeler:</strong>
                                                            <ul class="m-0 p-0 ps-3">
                                                                <?foreach (explode(',', $row->SUBELER) as $sube){?>
                                                                    <li><?= trim($sube) ?></li>
                                                                <?}?>
                                                            </ul>
                                                        </td>
                                                    </tr>
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

    var start = moment().subtract(29, 'days');
    var end = moment();

    function cb(start, end) {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    }

    $('#tarih').daterangepicker({
        timePicker: false,
        timePicker24Hour: true,
        timePickerIncrement: 30,
        locale: {
            "format": "DD.MM.YYYY",
            "separator": " , ",
            "applyLabel": "Uygula",
            "cancelLabel": "Vazgeç",
            "fromLabel": "Dan",
            "toLabel": "a",
            "customRangeLabel": "Seç",
            "weekLabel": "W",
            "daysOfWeek": [
                "Pa",
                "Pz",
                "Sa",
                "Ça",
                "Pe",
                "Cu",
                "Ct"
            ],
            "monthNames": [
                "Ocak",
                "Şubat",
                "Mart",
                "Nisan",
                "Mayıs",
                "Haziran",
                "Temmuz",
                "Ağustos",
                "Eylül",
                "Ekim",
                "Kasım",
                "Aralık"
            ],
            "firstDay": 1
        },
        ranges: {
            'Bugün': [moment(), moment()],
            'Dün': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Son 7 gün': [moment().subtract(6, 'days'), moment()],
            'Son 30 gün': [moment().subtract(29, 'days'), moment()],
            'Bu Ay': [moment().startOf('month'), moment().endOf('month')],
            'Geçen Ay': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            'Bu Yıl': [moment().startOf('year'), moment().endOf('year')]
        },
    }, cb);

    cb(start, end);

    $('#tarih').on('change', function (e) {
        $(this).closest('.input-group').find(":checkbox").prop("checked", true);
    });


    function fncDuzenle(obj){
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: {id: $(obj).data("id"), controller: "kampanya", action: "kampanya_duzenle"},
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

    $(document).ready(function() {
        $('#excel').on('change', function() {
            var formData = new FormData();
            formData.append('excel', this.files[0]);
            formData.append('controller', 'musteri');
            formData.append('action', 'excel_musteri_ekle');
            showSpinner();
            $.ajax({
                url: '/index.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
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
        });
    });

    function fncKampanyaSil(obj){
        sweatAlert("Emin Misiniz?", "Evet, Sil").then(function (result) {
            if (result.value) {
                showSpinner();
                $.ajax({
                    url: "/router.php",
                    type: "POST",
                    data: {id: $(obj).data("id"), controller: "kampanya", action: "kampanya_sil"},
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



<?
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
    session_kontrol();

    if(is_null($_REQUEST['durum'])){
        $_REQUEST['durum'] = 1;
    }

    $excel = new excelSayfasi();
    $excel->sutunEkle("Fiş No","FIS_NO","");
    $excel->sutunEkle("Cari","CARI","");
    $excel->sutunEkle("Malzeme Sayısı","MALZEME_SAYISI","");
    $excel->sutunEkle("Ara Toplam","ARA_TOPLAM","FormatSayi::virgul2");
    $excel->sutunEkle("Toplam KDV","KDV_TUTAR","FormatSayi::virgul2");
    $excel->sutunEkle("Genel Toplam","TOPLAM_TUTAR","FormatSayi::virgul2");
    $excel->sutunEkle("Ödeme Durum","ODEME_DURUM","");
    $excel->sutunEkle("Alış Tarihi","ALIS_TARIH","");
    $excelOut = $excel->excel();

    $result             = $cMalzemeAlis->getMalzemeAlislar($_REQUEST);
    $rows               = $result['rows'];

    $_SESSION["Table"]  = $result;
    $_SESSION['excel']  = $excelOut;
?>
<!Doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title> <?=$row_site->TITLE?> | Malzeme Alış Listesi </title>
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
                                                    <div class="col-md-3 mb-2">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="cari_id" id="cari_id" class="select2 form-select" data-style="btn-default">
                                                                <?=$cMalzemeAlis->Cariler()->setSecilen($_REQUEST['cari_id'])->setSeciniz()->getSelect("ID", "AD")?>
                                                            </select>
                                                            <label>Cari</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 mb-2">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="malzeme_id" id="malzeme_id" class="select2 form-select" data-style="btn-default">
                                                                <?=$cMalzemeAlis->Malzemeler()->setSecilen($_REQUEST['malzeme_id'])->setSeciniz()->getSelect("ID", "AD")?>
                                                            </select>
                                                            <label>Malzeme</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 mb-2">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="odeme_durum_id" id="odeme_durum_id" class="select2 form-select" data-style="btn-default">
                                                                <?=$cMalzemeAlis->OdemeDurum()->setSecilen($_REQUEST['odeme_durum_id'])->setSeciniz()->getSelect("ID", "AD")?>
                                                            </select>
                                                            <label>Ödeme Durum</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 mb-4">
                                                        <div class="input-group">
                                                            <div class="input-group-text form-check mb-0">
                                                                <input class="form-check-input m-auto" type="checkbox" id="fatura_tarih_var" name="fatura_tarih_var" <?=($_REQUEST['fatura_tarih_var'] == 'on') ? 'checked' : ''?> aria-label="Checkbox for following text input">
                                                            </div>
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="text" name="fatura_tarih" id="fatura_tarih" class="form-control datepicker_range" value="<?=$_REQUEST['fatura_tarih']?>">
                                                                <label for="fatura_tarih">Tarih</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 mb-4">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="sayfalama" id="sayfalama" class="btn select2 form-select" data-style="btn-default">
                                                                <?=$cUrun->Sayfalama()->setSecilen($_REQUEST['sayfalama'])->getSelect("ID", "AD")?>
                                                            </select>
                                                            <label>Sayfalama</label>
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
                                    <h6 class="mb-0 me-2 text-white"> <i class="ri-list-check fs-4 me-2"></i> Malzeme Alış Listesi <small><?=$result["sayfa_araligi"]?></small></h6>
                                    <div class="card-header-elements ms-auto">
                                        <a href="/views/finans/malzeme_alis_ekle.php?route=finans/malzeme_alis_listesi" data-bs-toggle="tooltip" class="btn btn-icon text-white float-right border-white border-radius btn-sm" title="Alış Ekle"><i class="ri-add-line fs-4"></i></a>
                                    </div>
                                </div>
                                <div class="card-body mt-2">
                                    <div class="card-datatable table-responsive">
                                        <table class="table table-hover table-sm">
                                            <thead class="thead-themed fw-bold py-0">
                                                <tr class="table-primary">
                                                    <td nowrap>#</td>
                                                    <td nowrap>Fiş No</td>
                                                    <td nowrap>Cari</td>
                                                    <td nowrap align="center">Malzeme Sayısı</td>
                                                    <td nowrap align="right">Ara Toplam</td>
                                                    <td nowrap align="right">Toplam KDV</td>
                                                    <td nowrap align="right">Genel Toplam</td>
                                                    <td nowrap align="center">Ödeme Durumu</td>
                                                    <td nowrap align="center">Ödeme Tarihi</td>
                                                    <td nowrap align="center">Alış Tarihi</td>
                                                    <td nowrap>Kayıt Yapan</td>
                                                    <td nowrap >İşlemler</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?foreach ($rows as $key => $row) {
                                                    $row_toplam->ARA_TOPLAM         += $row->ARA_TOPLAM;
                                                    $row_toplam->KDV_TUTAR          += $row->KDV_TUTAR;
                                                    $row_toplam->TOPLAM_TUTAR       += $row->TOPLAM_TUTAR;
                                                    ?>
                                                    <tr style="cursor: pointer;" onclick="location.href='/views/finans/malzeme_alis_detay.php?id=<?=$row->ID?>&token=<?=$row->TOKEN?>'">
                                                        <td><?=($key+1)?></td>
                                                        <td nowrap class="fw-semibold">#<?=$row->FIS_NO?></td>
                                                        <td nowrap><?=$row->CARI?></td>
                                                        <td nowrap align="center"><span class="badge bg-label-secondary"><?=$row->MALZEME_SAYISI?></span></td>
                                                        <td nowrap align="right"><?=FormatSayi::sayi($row->ARA_TOPLAM,2)?> ₺</td>
                                                        <td nowrap align="right"><?=FormatSayi::sayi($row->KDV_TUTAR,2)?> ₺</td>
                                                        <td nowrap align="right" class="fw-bold"><?=FormatSayi::sayi($row->TOPLAM_TUTAR,2)?> ₺</td>
                                                        <td nowrap align="center" onclick="event.stopPropagation();"><?=fncOdemeDurumSpan($row->ODEME_DURUM_ID)?></td>
                                                        <td nowrap align="center"><?=($row->ODEME_TARIHI) ? FormatTarih::tarih($row->ODEME_TARIHI) : '-'?></td>
                                                        <td nowrap align="center"><?=FormatTarih::tarih($row->ALIS_TARIH)?></td>
                                                        <td><?=FormatYazi::kisalt2($row->KAYIT_YAPAN,25)?></td>
                                                        <td align="right" nowrap onclick="event.stopPropagation();">
                                                            <a href="javascript:;" data-bs-toggle="tooltip" class="btn btn-success btn-icon btn-sm" data-id="<?=$row->ID?>" onclick="fncOdendi(this)" title="Ödendi Yap"> <i class="ri-check-double-line"></i></a>
                                                            <a href="javascript:;" data-bs-toggle="tooltip" class="btn btn-danger btn-icon btn-sm" data-id="<?=$row->ID?>" onclick="fncOdemeRed(this)" title="Ödemeyi Reddet"> <i class="ri-close-line"></i></a>
                                                            <a href="/views/finans/malzeme_alis_detay.php?id=<?=$row->ID?>&token=<?=$row->TOKEN?>" data-bs-toggle="tooltip" class="btn btn-info btn-icon btn-sm" title="Detay"> <i class="ri-eye-line"></i></a>
                                                            <a href="javascript:;" class="btn btn-danger btn-icon btn-sm" data-id="<?=$row->ID?>" onclick="fncSil(this)" title="Sil"><i class="ri-delete-bin-5-line"></i></a>
                                                        </td>
                                                    </tr>
                                                <?}?>
                                            </tbody>
                                            <tfoot>
                                                <tr class="table-secondary fw-bold">
                                                    <td colspan="5" align="right">Genel Toplam :</td>
                                                    <td align="right"><?=FormatSayi::sayi($row_toplam->ARA_TOPLAM,2)?> ₺</td>
                                                    <td align="right"><?=FormatSayi::sayi($row_toplam->KDV_TUTAR,2)?> ₺</td>
                                                    <td align="right"><?=FormatSayi::sayi($row_toplam->TOPLAM_TUTAR,2)?> ₺</td>
                                                    <td colspan="5"></td>
                                                </tr>
                                            </tfoot>
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

    $('#fatura_tarih').daterangepicker({
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

    $('#fatura_tarih').on('change', function (e) {
        $(this).closest('.input-group').find(":checkbox").prop("checked", true);
    });

    function fncSil(obj){
        sweatAlert("Emin Misiniz?", "Evet, Sil").then(function (result) {
            if (result.value) {
                showSpinner();
                $.ajax({
                    url: "/router.php",
                    type: "POST",
                    data: {id: $(obj).data("id"), controller: "malzemeAlis", action: "malzeme_alis_sil"},
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

    function fncOdendi(obj){
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: {id: $(obj).data("id"), controller: "malzemeAlis", action: "malzeme_alis_odeme_yap"},
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

    function fncOdemeRed(obj){
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: {id: $(obj).data("id"), controller: "malzemeAlis", action: "malzeme_alis_odeme_red"},
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
    
</script>
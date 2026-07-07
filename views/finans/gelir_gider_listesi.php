<?
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
    session_kontrol();

    if(is_null($_REQUEST['durum'])){
        $_REQUEST['durum'] = 1;
    }

    $excel = new excelSayfasi();
    $excel->sutunEkle("Tip","TIP","");
    $excel->sutunEkle("Cari","CARI","");
    $excel->sutunEkle("Açıklama","ACIKLAMA","");
    $excel->sutunEkle("Tutar","TUTAR","FormatSayi::virgul2");
    $excel->sutunEkle("Fatura Tarih","FATURA_TARIH","");
    $excelOut = $excel->excel();

    $result             = $cGelirGider->getGelirGiderler($_REQUEST);
    $rows               = $result['rows'];

    $_SESSION["Table"]  = $result;
    $_SESSION['excel']  = $excelOut;
?>
<!Doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title> <?=$row_site->TITLE?> | Gelir / Gider Listesi </title>
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
                                                            <select name="tip" id="tip" class="select2 form-select" data-style="btn-default">
                                                                <option value="">Tümü</option>
                                                                <option value="GELIR" <?=($_REQUEST['tip'] == 'GELIR') ? 'selected' : ''?>>Gelir</option>
                                                                <option value="GIDER" <?=($_REQUEST['tip'] == 'GIDER') ? 'selected' : ''?>>Gider</option>
                                                            </select>
                                                            <label>Tip</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 mb-2">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="cari_id" id="cari_id" class="select2 form-select" data-style="btn-default">
                                                                <?=$cGelirGider->Cariler()->setSecilen($_REQUEST['cari_id'])->setSeciniz()->getSelect("ID", "AD")?>
                                                            </select>
                                                            <label>Cari</label>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-md-2 mb-2">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="sayfalama" id="sayfalama" class="btn select2 form-select" data-style="btn-default">
                                                                <?=$cUrun->Sayfalama()->setSecilen($_REQUEST['sayfalama'])->getSelect("ID", "AD")?>
                                                            </select>
                                                            <label>Sayfalama</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 mb-4">
                                                        <div class="input-group">
                                                            <div class="input-group-text form-check mb-0">
                                                                <input class="form-check-input m-auto" type="checkbox" id="fatura_tarih_var" name="fatura_tarih_var" <?=($_REQUEST['fatura_tarih_var'] == 'on') ? 'checked' : ''?> aria-label="Checkbox for following text input">
                                                            </div>
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="text" name="fatura_tarih" id="fatura_tarih" class="form-control datepicker_range" value="<?=$_REQUEST['fatura_tarih']?>">
                                                                <label for="fatura_tarih">Fatura Tarihi</label>
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
                                    <h6 class="mb-0 me-2 text-white"> <i class="ri-list-check fs-4 me-2"></i> Gelir / Gider Listesi <small><?=$result["sayfa_araligi"]?></small></h6>
                                    <div class="card-header-elements ms-auto">
                                        <a href="/views/finans/gelir_gider_ekle.php?route=finans/gelir_gider_listesi" data-bs-toggle="tooltip" class="btn btn-icon text-white float-right border-white border-radius btn-sm" title="Yeni Ekle"><i class="ri-add-line fs-4"></i></a>
                                    </div>
                                </div>
                                <div class="card-body mt-2">
                                    <div class="card-datatable table-responsive">
                                        <table class="table table-hover table-sm">
                                            <thead class="thead-themed fw-bold py-0">
                                                <tr class="table-primary">
                                                    <td nowrap>#</td>
                                                    <td nowrap>Tip</td>
                                                    <td nowrap>Cari</td>
                                                    <td nowrap>Açıklama</td>
                                                    <td nowrap align="right">Tutar</td>
                                                    <td nowrap align="center">Fatura Tarih</td>
                                                    <td nowrap>Kayıt Yapan</td>
                                                    <td nowrap ></td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?foreach ($rows as $key => $row) {
                                                    if ($row->TIP == 'GELIR') {
                                                        $row_toplam->GELIR += $row->TUTAR;
                                                        $tip_renk = "text-success";
                                                    } else {
                                                        $row_toplam->GIDER += $row->TUTAR;
                                                        $tip_renk = "text-danger";
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td><?=($key+1)?></td>
                                                        <td nowrap class="fw-bold <?=$tip_renk?>"><?=$row->TIP?></td>
                                                        <td nowrap><?=$row->CARI?></td>
                                                        <td><?=$row->ACIKLAMA?></td>
                                                        <td nowrap align="right" class="fw-bold <?=$tip_renk?>"><?=FormatSayi::sayi($row->TUTAR,2)?> ₺</td>
                                                        <td nowrap align="center"><?=FormatTarih::tarih($row->FATURA_TARIH)?></td>
                                                        <td><?=FormatYazi::kisalt2($row->KAYIT_YAPAN,25)?></td>
                                                        <td align="right" nowrap>
                                                            <a href="/views/finans/gelir_gider_duzenle.php?route=finans/gelir_gider_listesi&id=<?=$row->ID?>" data-bs-toggle="tooltip" class="btn btn-primary btn-icon btn-sm" title="Düzenle"> <i class="ri-pencil-line"></i></a>
                                                            <a href="javascript:;" class="btn btn-danger btn-icon btn-sm" data-id="<?=$row->ID?>" onclick="fncSil(this)" title="Sil"><i class="ri-delete-bin-5-line"></i></a>
                                                        </td>
                                                    </tr>
                                                <?}?>
                                            </tbody>
                                            <tfoot>
                                                <tr class="table-secondary fw-bold">
                                                    <td colspan="4" align="right">Genel Toplam (Gelir / Gider) :</td>
                                                    <td align="right">
                                                        <span class="text-success"><?=FormatSayi::sayi($row_toplam->GELIR,2)?> ₺</span> / 
                                                        <span class="text-danger"><?=FormatSayi::sayi($row_toplam->GIDER,2)?> ₺</span>
                                                    </td>
                                                    <td colspan="3"></td>
                                                </tr>
                                                <tr class="table-secondary fw-bold">
                                                    <td colspan="4" align="right">Bakiye :</td>
                                                    <td align="right">
                                                        <?php
                                                            $bakiye = $row_toplam->GELIR - $row_toplam->GIDER;
                                                            $bakiye_renk = $bakiye >= 0 ? "text-success" : "text-danger";
                                                        ?>
                                                        <span class="<?=$bakiye_renk?>"><?=FormatSayi::sayi($bakiye,2)?> ₺</span>
                                                    </td>
                                                    <td colspan="3"></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                <div class="pagination d-flex justify-content-center">
                                    <?=$result['sayfalama'] ? $result['sayfalama']->sayfalamaOlustur() : ''?>
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
                    data: {id: $(obj).data("id"), controller: "gelirGider", action: "gelir_gider_sil"},
                    dataType: "json",
                    success: function(response) {
                        $.unblockUI();
                        if (response.HATA) {
                            notyf.error(response.ACIKLAMA);
                        } else {
                            notyf.success(response.ACIKLAMA);
                            $(obj).closest('tr').fadeOut();
                            setTimeout(function(){ location.reload(); }, 1000);
                        }
                    }
                });
            }
        });
    }
</script>

<?
	require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
	session_kontrol();

    if(is_null($_REQUEST['durum'])){
        $_REQUEST['durum'] = 1;
    }

    $excel = new excelSayfasi();
    $excel->sutunEkle("Ürün","URUN","");
    $excel->sutunEkle("Üst Kategori","UST_KATEGORI","");
    $excel->sutunEkle("Kategori","KATEGORI","");
    $excel->sutunEkle("Satış Türü","SATIS_TURU","");
    $excel->sutunEkle("Alerjenler","ALERJENLER","");
    $excel->sutunEkle("Açıklama","ACIKLAMA","");
    $excel->sutunEkle("Durum","DURUM_TEXT","");
    $excelOut = $excel->excel();
    
    $result             = $cUrun->getMalzemeler($_REQUEST);
    $rows               = $result['rows'];

    $_SESSION["Table"]  = $result;
    $_SESSION['excel']  = $excelOut;

?>
<!Doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title> <?=$row_site->TITLE?> | Malzeme Listesi </title>
        <?=$cTheme->Linkler()?>
    </head>
    <style type="text/css">
        
    </style>
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
                                                         <div class="input-group input-group-merge">
                                                             <span class="input-group-text"><i class="ri-restaurant-2-fill"></i></span>
                                                             <div class="form-floating form-floating-outline">
                                                                 <input type="text" id="malzeme" name="malzeme" class="form-control" value="<?=$_REQUEST['malzeme']?>" placeholder="Malzeme">
                                                                 <label>Malzeme</label>
                                                             </div>
                                                         </div>
                                                     </div>
                                                     <div class="col-md-2 mb-2">
                                                         <div class="form-floating form-floating-outline">
                                                             <select name="temel_birim_id" id="temel_birim_id" class="select2 form-select" data-style="btn-default">
                                                                 <?=$cUrun->Birimler()->setSeciniz()->setSecilen($_REQUEST['temel_birim_id'])->getSelect("ID", "AD")?>
                                                             </select>
                                                             <label>Birim</label>
                                                         </div>
                                                     </div>
                                                     <div class="col-md-2 mb-2">
                                                         <div class="form-floating form-floating-outline">
                                                             <select name="malzeme_tipi_id" id="malzeme_tipi_id" class="select2 form-select" data-style="btn-default">
                                                                 <?=$cUrun->MalzemeTipleri()->setSeciniz()->setSecilen($_REQUEST['malzeme_tipi_id'])->getSelect("ID", "AD")?>
                                                             </select>
                                                             <label>Malzeme Tipi</label>
                                                         </div>
                                                     </div>
                                                     <div class="col-md-2 mb-2">
                                                         <div class="form-floating form-floating-outline">
                                                             <select name="durum" id="durum" class="select2 form-select" data-style="btn-default">
                                                                 <?=$cKullanici->Durum()->setSecilen($_REQUEST['durum'])->setSeciniz()->getSelect("ID", "AD")?>
                                                             </select>
                                                             <label>Durum</label>
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
                                                     <div class="col-md-1 mt-1">
                                                         <button type="submit" class="btn btn-primary w-100">Filtrele</button>
                                                     </div>
                                                 </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-6">
                                <div class="card-header header-elements bg-primary py-1">
                                    <h6 class="mb-0 me-2 text-white"> <i class="ri-restaurant-2-fill fs-4 me-2"></i> Malzeme Listesi <small><?=$result["sayfa_araligi"]?></small></h6>
                                    <div class="card-header-elements ms-auto">
                                        <a href="/views/urun/malzeme_ekle.php?route=urun/malzeme_listesi" data-bs-toggle="tooltip" class="btn btn-icon text-white float-right border-white border-radius btn-sm" title="Malzeme Ekle"><i class="ri-add-line fs-4"></i></a>
                                        <a href="../excel_sql.php" data-bs-toggle="tooltip" title="Excel" class="btn btn-icon text-white float-right border-white borderd-radius btn-sm"> <i class="ri-file-excel-2-line"></i> </a>
                                    </div>
                                </div>
                                <div class="card-body mt-2">
                                    <div class="card-datatable table-responsive">
                                        <table class="table table-hover table-sm">
                                            <thead class="thead-themed fw-bold py-0">
                                                <tr class="table-primary">
                                                    <td nowrap>#</td>
                                                    <td nowrap>Malzeme</td>
                                                    <td nowrap>Birim</td>
                                                    <td nowrap>Malzeme Tipi</td>
                                                    <td nowrap>Fiyat</td>
                                                    <td nowrap>Ekstra Fiyat</td>
                                                    <td nowrap align="center">Stok Takip</td>
                                                    <td nowrap align="center">Ekstra</td>
                                                    <td nowrap align="center">Durum</td>
                                                    <td nowrap></td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?foreach ($rows as $key => $row) {?>
                                                    <tr>
                                                        <td><?=($key+1)?></td>
                                                        <td><?=FormatYazi::kisalt2($row->MALZEME,25)?></td>
                                                        <td><span class="badge bg-label-info"><?=$row->BIRIM_KISA_ADI ? $row->BIRIM_KISA_ADI : '-'?></span></td>
                                                        <td><span class="badge bg-label-secondary"><?=$row->MALZEME_TIPI_ADI ? $row->MALZEME_TIPI_ADI : '-'?></span></td>
                                                        <td><?=FormatSayi::sayi($row->FIYAT,2)?> ₺</td>
                                                        <td><?=FormatSayi::sayi($row->EKSTRA_FIYAT,2)?> ₺</td>
                                                        <td align="center"><?=($row->STOK_TAKIP == '1') ? '<span class="badge bg-label-success">Takip Var</span>' : '<span class="badge bg-label-warning">Takip Yok</span>'?></td>
                                                        <td align="center"><?=($row->EKSTRA == '1') ? '✅' : '❌'?></td>
                                                        <td align="center"><?=fncDurumSpan($row->DURUM)?></td>
                                                        <td nowrap>
                                                            <a href="/views/urun/malzeme_duzenle.php?route=urun/malzeme_listesi&id=<?=$row->ID?>&token=<?=$row->TOKEN?>" data-bs-toggle="tooltip" class="btn btn-primary btn-icon btn-sm" title="Düzenle"> <i class="ri-pencil-line"></i></a>
                                                            <a href="javascript:;" data-bs-toggle="tooltip" class="btn btn-danger btn-icon btn-sm" data-id="<?=$row->ID?>" onclick="fncMalzemeSil(this)" title="Sil"><i class="ri-delete-bin-5-line"></i></a>
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
            data: {id: $(obj).data("id"), controller: "urun", action: "urun_duzenle"},
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

    function fncMalzemeSil(obj){
        sweatAlert("Sildiğiniz Malzeme Pasife Alınacaktır. Emin Misiniz?", "Evet, Sil").then(function (result) {
            if (result.value) {
                showSpinner();
                $.ajax({
                    url: "/router.php",
                    type: "POST",
                    data: {id: $(obj).data("id"), controller: "urun", action: "malzeme_sil"},
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



<?
	require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
	session_kontrol();

    if(is_null($_REQUEST['durum'])){
        $_REQUEST['durum'] = 1;
    }

    $excel = new excelSayfasi();
    $excel->sutunEkle("Kampanya","KAMPANYA","");
    $excel->sutunEkle("Şube","SUBE","");
    $excel->sutunEkle("Kategori","KATEGORI","");
    $excel->sutunEkle("Başlangıç Tarihi","BAS_TARIH","format1");
    $excel->sutunEkle("Başlangıç Saat","BAS_SAAT","");
    $excel->sutunEkle("Bitiş Tarihi","BIT_TARIH","format1");
    $excel->sutunEkle("Bitiş Saat","BIT_SAAT","");
    $excel->sutunEkle("Kayıt Yapan","KAYIT_YAPAN","");
    $excel->sutunEkle("Durum","DURUM","");
    $excelOut = $excel->excel();
    
    $result             = $cCari->getCariler($_REQUEST);
    $rows               = $result['rows'];

    $_SESSION["Table"]  = $result;
    $_SESSION['excel']  = $excelOut;

?>
<!Doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title> <?=$row_site->TITLE?> | Firma Listesi </title>
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
                                                            <span class="input-group-text"><i class="ri-database-line"></i></span>
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="text" id="cari" name="cari" class="form-control" value="<?=$_REQUEST['cari']?>" placeholder="Firma">
                                                                <label>Firma</label>
                                                            </div>
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
                                    <h6 class="mb-0 me-2 text-white"> <i class="ri-database-line fs-4 me-2"></i> Cari Listesi <small><?=$result["sayfa_araligi"]?></small></h6>
                                    <div class="card-header-elements ms-auto">
                                        <a href="/views/cari/cari_ekle.php" data-bs-toggle="tooltip" class="btn btn-icon text-white float-right border-white border-radius btn-sm" title="Cari Ekle"><i class="ri-add-line fs-4"></i></a>
                                        <a href="../excel_sql.php" data-bs-toggle="tooltip" title="Excel" class="btn btn-icon text-white float-right border-white borderd-radius btn-sm"> <i class="ri-file-excel-2-line"></i> </a>
                                    </div>
                                </div>
                                <div class="card-body mt-2">
                                    <div class="card-datatable table-responsive">
                                        <table class="table table-hover table-sm">
                                            <thead class="thead-themed fw-bold py-0">
                                                <tr class="table-primary">
                                                    <td nowrap>#</td>
                                                    <td nowrap align="center">Resim</td>
                                                    <td nowrap>Cari</td>
                                                    <td nowrap>Yetkili</td>
                                                    <td nowrap>Telefon</td>
                                                    <td nowrap>Mail</td>
                                                    <td nowrap>İl</td>
                                                    <td nowrap>İlçe</td>
                                                    <td nowrap align="center">Güncelleme Tarih</td>
                                                    <td nowrap>Kayıt Yapan</td>
                                                    <td nowrap align="center">Durum</td>
                                                    <td nowrap ></td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?foreach ($rows as $key => $row) {?>
                                                    <tr>
                                                        <td><?=($key+1)?></td>
                                                        <td align="center">
                                                            <?if(is_file(fncDocumentRoot($row->RESIM_URL))){?>
                                                                <img src="<?=$row->RESIM_URL?>" class="rounded-3 fancybox" alt="Cari Resim" height="50">
                                                            <?}else{?>
                                                                <img src="<?=$row_site->LOGO?>" class="fancybox" alt="Menü Yönetim" width="50"/>
                                                            <?}?>
                                                        </td>
                                                        <td nowrap class="fw-bold"><?=FormatYazi::kisalt2($row->CARI,35)?></td>
                                                        <td nowrap><?=$row->AD?> <?=$row->SOYAD?></td>
                                                        <td nowrap><?=$row->TELEFON?></td>
                                                        <td nowrap><?=$row->MAIL?></td>
                                                        <td nowrap><?=$row->IL?></td>
                                                        <td nowrap><?=$row->ILCE?></td>
                                                        <td nowrap align="center"><?=fncTre(FormatTarih::tarih($row->GTARIH))?></td>
                                                        <td><?=FormatYazi::kisalt2($row->KAYIT_YAPAN,25)?></td>
                                                        <td align="center"><?=fncDurumSpan($row->DURUM)?></td>
                                                        <td>
                                                            <a href="/views/cari/cari_duzenle.php?route=cari/cari_listesi&id=<?=$row->ID?>&token=<?=$row->TOKEN?>" data-bs-toggle="tooltip" class="btn btn-primary btn-icon btn-sm" title="Düzenle"> <i class="ri-pencil-line"></i></a>
                                                        </td>
                                                        <tr class="bg-light">
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

</script>



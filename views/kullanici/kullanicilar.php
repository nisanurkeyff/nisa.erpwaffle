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
    $excel->sutunEkle("Durum","DURUM","");
    $excelOut = $excel->excel();
    
    $result             = $cKullanici->getKullanicilar($_REQUEST);
    $rows               = $result['rows'];

    $_SESSION["Table"]  = $result;
    $_SESSION['excel']  = $excelOut;

?>
<!Doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title> <?=$row_site->TITLE?> | Kullanıcılar </title>
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
                                                    <div class="col-md-2 mb-2">
                                                        <div class="input-group input-group-merge">
                                                            <span class="input-group-text"><i class="ri-user-line"></i></span>
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="text" id="isim" name="isim" class="form-control" value="<?=$_REQUEST['isim']?>" placeholder="İsim">
                                                                <label>İsim</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 mb-2">
                                                        <div class="input-group input-group-merge">
                                                            <span class="input-group-text"><i class="ri-phone-fill"></i></span>
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="text" id="telefon" name="telefon" class="form-control phone-mask" value="<?=$_REQUEST['telefon']?>" placeholder="552 587 5962">
                                                                <label>Telefon</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 mb-2">
                                                        <div class="input-group input-group-merge">
                                                            <span class="input-group-text"><i class="ri-mail-line"></i></span>
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="text" id="mail" name="mail" class="form-control" value="<?=$_REQUEST['mail']?>" placeholder="info@gmail.com">
                                                                <label>Mail</label>
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
                                    <h6 class="mb-0 me-2 text-white"> <i class="ri-user-line fs-4 me-2"></i> Kullanıcılar <small><?=$result["sayfa_araligi"]?></small></h6>
                                    <div class="card-header-elements ms-auto">
                                        <a href="/views/kullanici/ekle.php?route=kullanici/kullanicilar" class="btn btn-icon text-white float-right border-white borderd-radius btn-sm"><i class="ri-add-line fs-4"></i></a>
                                    </div>
                                </div>
                                <div class="card-body mt-2">
                                    <div class="card-datatable text-nowrap table-responsive">
                                        <table class="table table-hover table-sm">
                                            <thead class="thead-themed fw-bold py-0">
                                                <tr class="table-primary">
                                                    <td>#</td>
                                                    <td>Resim</td>
                                                    <td>Yetki</td>
                                                    <td>Ad</td>
                                                    <td>Soyad</td>
                                                    <td>Kullanıcı Adı</td>
                                                    <td>Şifre</td>
                                                    <td>Telefon</td>
                                                    <td>Mail</td>
                                                    <td>İl / İlçe</td>
                                                    <td>Tarih</td>
                                                    <td></td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?foreach ($rows as $key => $row) {?>
                                                    <tr>
                                                        <td><?=($key+1)?></td>
                                                        <td>
                                                            <img class="<?=$row->CLASS?> me-6" src="<?=$row->AVATAR?>" alt="<?=$row->ALT?>" height="50" width="50"/>
                                                        </td>
                                                        <td><?=$row->YETKI?></td>
                                                        <td><?=$row->AD?></td>
                                                        <td><?=$row->SOYAD?></td>
                                                        <td><?=$row->KULLANICI?></td>
                                                        <td><?=$row->SIFRE?></td>
                                                        <td><?=$row->TELEFON?></td>
                                                        <td><?=$row->MAIL?></td>
                                                        <td><?=$row->IL?> / <?=$row->ILCE?></td>
                                                        <td><?=FormatTarih::tarih($row->TARIH)?></td>
                                                        <td>
                                                            <a href="javascript:;" data-bs-toggle="tooltip" class="btn btn-primary btn-icon btn-sm" data-id="<?=$row->ID?>" onclick="fncDuzenle(this)" title="Düzenle"> <i class="ri-pencil-line"></i></a>
                                                            <a href="javascript:;" class="btn btn-danger btn-icon btn-sm" data-id="<?=$row->ID?>" onclick="fncKullaniciSil(this)"><i class="ri-delete-bin-5-line"></i></a>
                                                        </td>
                                                    </tr>
                                                <?}?>
                                            </tbody>
                                        </table>
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

    function fncDuzenle(obj){
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: {id: $(obj).data("id"), controller: "kullanici", action: "kullanici_duzenle"},
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
    
    function fncKullaniciSil(obj){
        sweatAlert("Emin Misiniz?", "Evet, Sil").then(function (result) {
            if (result.value) {
                showSpinner();
                $.ajax({
                    url: "/router.php",
                    type: "POST",
                    data: {id: $(obj).data("id"), controller: "kullanici", action: "kullanici_sil"},
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



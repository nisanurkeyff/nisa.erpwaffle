<?
	require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
	session_kontrol();

    $result         = $cBlog->getBloglar($_REQUEST);
    $rows           = $result['rows'];
?>
<!Doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title> <?=$row_site->TITLE?> | Blog Listesi </title>
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
                                                <div class="row">
                                                    <div class="col-md-2 mb-4">
                                                        <div class="input-group input-group-merge">
                                                            <span class="input-group-text"><i class="ri-blogger-line"></i></span>
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="text" id="baslik" name="baslik" class="form-control" value="<?=$_REQUEST['baslik']?>" placeholder="Başlık">
                                                                <label>Başlık</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 mb-4">
                                                        <div class="input-group input-group-merge">
                                                            <span class="input-group-text"><i class="ri-arrow-up-line"></i></span>
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="text" id="ad" name="ad" class="form-control" value="<?=$_REQUEST['ad']?>" placeholder="Ad">
                                                                <label>Adı</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 mb-4">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="durum" id="durum" class="btn select2 form-select" data-style="btn-default">
                                                                <?=$cKullanici->Durum()->setSecilen($_REQUEST['durum'])->setSeciniz()->getSelect("ID", "AD")?>
                                                            </select>
                                                            <label for="country-modern">Durum</label>
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
                                    <h6 class="mb-0 me-2 text-white"> <i class="ri-stock-line fs-4 me-2"></i> Blog Listesi</h6>
                                    <div class="card-header-elements ms-auto">
                                        <a href="/views/blog/blog_ekle.php" class="btn btn-icon text-white float-right border-white borderd-radius btn-sm"><i class="ri-add-line fs-4"></i></a>
                                    </div>
                                </div>
                                <div class="card-body mt-2">
                                    <div class="card-datatable text-nowrap table-responsive">
                                        <table class="table table-hover table-sm">
                                            <thead class="thead-themed fw-bold py-0">
                                                <tr class="table-primary">
                                                    <td>Ad</td>
                                                    <td>Başlık</td>
                                                    <td>Açıklama</td>
                                                    <td>Url</td>
                                                    <td align="center">Güncelleme Tarih</td>
                                                    <td align="center">Kayıt Tarih</td>
                                                    <td>Kayıt Yapan</td>
                                                    <td></td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?foreach ($rows as $key => $row) {?>
                                                    <tr>
                                                        <td><?=FormatYazi::kisalt2($row->AD,20)?></td>
                                                        <td><?=FormatYazi::kisalt2($row->BASLIK,20)?></td>
                                                        <td><?=FormatYazi::kisalt2($row->ACIKLAMA,20)?></td>
                                                        <td><?=FormatYazi::kisalt2($row->URL,20)?></td>
                                                        <td align="center"><?=fncTre(FormatTarih::tarih($row->GTARIH))?></td>
                                                        <td align="center"><?=fncTre(FormatTarih::tarih($row->TARIH))?></td>
                                                        <td><?=$row->KAYIT_YAPAN?></td>
                                                        <td>
                                                            <a href="/views/blog/blog_duzenle.php?id=<?=$row->ID?>&token=<?=$row->TOKEN?>" data-bs-toggle="tooltip" class="btn btn-primary btn-icon btn-sm" title="Düzenle"> <i class="ri-pencil-line"></i></a>
                                                            <a href="javascript:;" data-bs-toggle="tooltip" class="btn btn-danger btn-icon btn-sm" data-id="<?=$row->ID?>" onclick="fncBlogSil(this)" title="Sil"><i class="ri-delete-bin-5-line"></i></a>
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
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: {id: $(obj).data("id"), controller: "blog", action: "blog_duzenle"},
            dataType: "json",
            success: function(response) {
                if (response.HATA) {
                    Swal.fire({title: 'Uyarı!',text: response.ACIKLAMA ,icon: 'warning' ,customClass: {confirmButton: 'btn btn-primary waves-effect waves-light'},buttonsStyling: false});
                } else {
                    location.href = response.URL;
                }
            }
        });
    }

    function fncBlogGit(obj){
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: {id: $(obj).data("id"), controller: "blog", action: "blog_git"},
            dataType: "json",
            success: function(response) {
                if (response.HATA) {
                    Swal.fire({title: 'Uyarı!',text: response.ACIKLAMA ,icon: 'warning' ,customClass: {confirmButton: 'btn btn-primary waves-effect waves-light'},buttonsStyling: false});
                } else {
                    location.href = response.URL;
                }
            }
        });
    }

    function fncBlogSil(obj){
        Swal.fire({
            title: 'Emin misin?',
            icon: 'warning',
            showCancelButton: true,
            cancelButtonText: 'İptal',
            confirmButtonText: 'Evet, Sil!',
            customClass: {
                confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
                cancelButton: 'btn btn-outline-secondary waves-effect'
            },
            buttonsStyling: false
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    url: "/router.php",
                    type: "POST",
                    data: {id: $(obj).data("id"), controller: "blog", action: "blog_sil"},
                    dataType: "json",
                    success: function(response) {
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



<?
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
    session_kontrol();
    
    $row            = $cBlog->getBlog($_REQUEST);
    fncTokenKontrol($row);

    $rows_resimler  = $cBlog->getBlogResimler($_REQUEST);
?>
<!Doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title> <?=$row_site->TITLE?> | Blog Düzenle </title>
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
                            <div class="row gy-6 gy-md-0">
                                
                                <div class="col-xl-12">
                                    <div class="card mb-6">
                                        <div class="card-header overflow-hidden">
                                            <ul class="nav nav-tabs" role="tablist">
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link waves-effect active" data-bs-toggle="tab" data-bs-target="#tab_blog" role="tab" aria-selected="true">
                                                    <span class="ri-user-line ri-20px d-sm-none"></span><span class="d-none d-sm-block">Blog Bilgisi</span></button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link waves-effect" data-bs-toggle="tab" data-bs-target="#tab_resim" role="tab" aria-selected="true">
                                                    <span class="ri-user-line ri-20px d-sm-none"></span><span class="d-none d-sm-block">Blog Resimleri</span></button>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="tab-content">

                                            <div class="tab-pane fade active show" id="tab_blog" role="tabpanel">
                                                <form id="blogKaydet">
                                                    <input type="hidden" name="id" id="id" value="<?=$row->ID?>">
                                                    <div class="row g-6">
                                                        <div class="col-md-6">
                                                            <div class="input-group input-group-merge">
                                                                <span class="input-group-text"><i class="ri-blogger-line"></i></span>
                                                                <div class="form-floating form-floating-outline">
                                                                    <input type="text" id="ad" name="ad" class="form-control" value="<?=$row->AD?>" placeholder="Blog Adı">
                                                                    <label>Adı</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="input-group input-group-merge">
                                                                <span class="input-group-text"><i class="ri-arrow-up-line"></i></span>
                                                                <div class="form-floating form-floating-outline">
                                                                    <input type="text" id="baslik" name="baslik" class="form-control" value="<?=$row->BASLIK?>" placeholder="Blog Başlık">
                                                                    <label>Başlık</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="input-group input-group-merge">
                                                                <span class="input-group-text"><i class="ri-chrome-line"></i></span>
                                                                <div class="form-floating form-floating-outline">
                                                                    <input type="text" id="url" name="url" class="form-control" value="<?=$row->URL?>" placeholder="URL">
                                                                    <label>URL</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-floating form-floating-outline">
                                                                <select name="durum" id="durum" class="select2 form-select" data-style="btn-default">
                                                                    <?=$cKullanici->Durum()->setSecilen($row->DURUM)->getSelect("ID", "AD")?>
                                                                </select>
                                                                <label>Durum</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="input-group input-group-merge">
                                                                <span class="input-group-text"><i class="ri-chat-4-line"></i></span>
                                                                <div class="form-floating form-floating-outline">
                                                                    <textarea class="form-control" id="aciklama" name="aciklama"  placeholder="Açıklama" style="height: 81px;"><?=$row->ACIKLAMA?></textarea>
                                                                    <label for="basic-icon-default-message">Açıklama</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div id="icerik"></div>
                                                            <input type="hidden" name="icerik" id="hidden-input" value="<?=htmlspecialchars(!is_null($row->ICERIK) ? $row->ICERIK : '')?>">
                                                        </div>
                                                    </div>
                                                    <div class="text-end pt-6">
                                                        <button type="submit" class="btn btn-primary me-4 waves-effect waves-light">Kaydet</button>
                                                        <button type="reset" class="btn btn-outline-secondary waves-effect">Geri Al</button>
                                                    </div>
                                                </form>
                                            </div>

                                            <div class="tab-pane fade" id="tab_resim" role="tabpanel">

                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="card mb-6">
                                                                <div class="card-header header-elements bg-primary py-3">
                                                                    <h6 class="mb-0 me-2 text-white"> <i class="ri-image-2-line fs-4 me-2"></i> Resimler</h6>
                                                                </div>
                                                                <div class="card-body mt-2">
                                                                    <div class="card-datatable text-nowrap table-responsive">
                                                                        <table class="table table-hover table-sm">
                                                                            <thead class="thead-themed fw-bold py-0">
                                                                                <tr class="table-primary">
                                                                                    <td>Resim</td>
                                                                                    <td></td>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <?foreach ($rows_resimler as $key => $row_resimler) {?>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <img class="rounded-3 fancybox" src="<?=fncImgPath($row_resimler->RESIM_URL)?>" alt="Stok Resim" height="100" width="100"/>
                                                                                        </td>
                                                                                        <td align="right">
                                                                                            <?if($row_resimler->VITRIN == 0){?>
                                                                                                <a href="javascript:;" class="btn btn-outline-success btn-icon btn-sm" data-id="<?=$row_resimler->ID?>" onclick="fncVirtinYap(this)" title="Vitrin Yap"><i class="ri-checkbox-circle-line"></i></a>
                                                                                            <?}?>
                                                                                            <a href="javascript:;" class="btn btn-outline-danger btn-icon btn-sm" data-id="<?=$row_resimler->ID?>" onclick="fncResimSil(this)" title="Sil"><i class="ri-delete-bin-5-line"></i></a>
                                                                                        </td>
                                                                                    </tr>
                                                                                <?}?>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="card">
                                                                <h5 class="card-header">Resim Yükleme</h5>
                                                                <div class="card-body">
                                                                    <form action="/upload" method="POST" enctype="multipart/form-data" class="dropzone needsclick" id="formResimYukle">
                                                                        <input type="hidden" name="id" id="id" value="<?=$row->ID?>">
                                                                        <div class="dz-message needsclick">
                                                                            Dosyaları buraya bırakın veya yüklemek için tıklayın
                                                                        </div>
                                                                        <div class="fallback">
                                                                            <input name="files[]" type="file" />
                                                                        </div>
                                                                    </form>
                                                                    <div class="text-end">
                                                                        <button id="resimYukle" class="btn btn-primary mt-3">Yükle</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>  
                                                    </div>
                                                </div>
                                            </div>

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

    const fullEditor = new Quill('#icerik', {
        bounds: '#full-editor',
        placeholder: 'İçerik',
        modules: {
            formula: true,
            toolbar: fullToolbar,
        },
        theme: 'snow',
    });

    const hiddenInput = document.querySelector('#hidden-input');
    const initialContent = hiddenInput.value;
    fullEditor.root.innerHTML = initialContent;

    document.querySelector('form').addEventListener('submit', () => {
        hiddenInput.value = fullEditor.root.innerHTML;
    });

    $("#blogKaydet").on("submit", function(event) {
        event.preventDefault();
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: $(this).serialize() + "&controller=blog&action=blog_kaydet",
            dataType: "json",
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

    $("#resimYukle").on("click", function (event) {
        event.preventDefault();

        var myDropzone = Dropzone.forElement("#formResimYukle");
        var formData = new FormData();
        formData.append("controller", "blog");
        formData.append("action", "resim_yukle");
        formData.append("id", $("#id").val());

        myDropzone.files.forEach((file, index) => {
            formData.append("files[]", file);
        });
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function (response) {
                if (response.HATA) {
                    showSpinner();
                    notyf.error(response.ACIKLAMA);
                } else {
                    notyf.success(response.ACIKLAMA);
                    location.reload(true);
                }
            }
        });
    });

    function fncResimSil(obj){
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
                showSpinner();
                $.ajax({
                    url: "/router.php",
                    type: "POST",
                    data: {id: $(obj).data("id"), controller: "blog", action: "resim_sil"},
                    dataType: "json",
                    success: function(response) {
                        if (response.HATA) {
                            $.unblockUI();
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

    function fncAltGuncelle(obj){
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: {id: $(obj).data("id"), alt: $("#alt" + $(obj).data("id")).val(), controller: "blog", action: "alt_guncelle"},
            dataType: "json",
            success: function(response) {
                if (response.HATA) {
                    $.unblockUI();
                    notyf.error(response.ACIKLAMA);
                } else {
                    notyf.success(response.ACIKLAMA);
                    $("#adet" + $(obj).data("id")).val(response.ADET);
                }
            }
        });
    }

    function fncVirtinYap(obj){
        showSpinner();
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: {id: $(obj).data("id"), controller: "blog", action: "vitrin_yap"},
            dataType: "json",
            success: function(response) {
                if (response.HATA) {
                    $.unblockUI();
                    notyf.error(response.ACIKLAMA);
                } else {
                    notyf.success(response.ACIKLAMA);
                }
            }
        });
    }
           

</script> 
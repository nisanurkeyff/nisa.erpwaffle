<?
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
    session_kontrol();

    // Query active materials
    $rows_malzemeler = DB::get("SELECT ID, MALZEME FROM MALZEME WHERE DURUM = '1' ORDER BY MALZEME ASC");

    // Query active KDV rates
    $rows_kdvs = DB::get("SELECT ID, KDV FROM KDV WHERE DURUM = '1' ORDER BY KDV ASC");
?>
<!Doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title> <?=$row_site->TITLE?> | Malzeme Alış Girişi </title>
        <?=$cTheme->Linkler()?>
        <style type="text/css">
            .cart-item-row:hover {
                background-color: rgba(0, 0, 0, 0.02) !important;
            }
            .totals-list-item {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 8px;
            }
            .kdv-toggle-group {
                background: #f1f0f5;
                border-radius: 8px;
                padding: 3px;
                display: flex;
                border: 1px solid #e5e4eb;
            }
            .kdv-toggle-group .btn-check + .btn {
                border: none;
                border-radius: 6px;
                color: #6f6b7d;
                background: transparent;
                font-weight: 500;
                transition: all 0.2s ease-in-out;
                flex: 1;
            }
            .kdv-toggle-group .btn-check:checked + .btn {
                background-color: #7367f0 !important;
                color: #ffffff !important;
                font-weight: 600;
                box-shadow: 0 2px 6px 0 rgba(115, 103, 240, 0.3);
            }
        </style>
    </head>
    <body>
        <div class="layout-wrapper layout-content-navbar">
            <div class="layout-container">
                <?=$cTheme->Menu()?>
                <div class="layout-page">
                    <?=$cTheme->Header()?>
                    <div class="content-wrapper">
                        <div class="container-xxl flex-grow-1 container-p-y">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 gap-6">
                                <div class="d-flex flex-column justify-content-center">
                                    <h4 class="mb-1">Yeni Malzeme Alış Fişi</h4>
                                    <p class="mb-0">ERP Fiş ve Sepet Yapılı Alış Girişi</p>
                                </div>
                            </div>
                            
                            <div class="row">
                                <!-- Sol Kolon: Malzeme Ekleme ve Sepet Tablosu -->
                                <div class="col-12 col-lg-8">
                                    <!-- Malzeme Ekle Formu -->
                                    <div class="card mb-6">
                                        <div class="card-header pb-3">
                                            <h5 class="card-title m-0"><i class="ri-add-circle-line me-2 text-primary"></i>Malzeme Ekle</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-4 align-items-end">
                                                 <div class="col-md-4">
                                                     <div class="form-floating form-floating-outline">
                                                         <select id="select_malzeme" class="select2 form-select" data-style="btn-default">
                                                             <option value="">-- Seçiniz --</option>
                                                             <?foreach ($rows_malzemeler as $m){?>
                                                                 <option value="<?=$m->ID?>">
                                                                     <?=$m->MALZEME?>
                                                                 </option>
                                                             <?}?>
                                                         </select>
                                                         <label for="select_malzeme">Malzeme</label>
                                                     </div>
                                                 </div>
                                                 <div class="col-md-2">
                                                     <div class="form-floating form-floating-outline">
                                                         <input type="number" id="input_miktar" class="form-control" value="1" min="0.01" step="any" placeholder="Miktar">
                                                         <label for="input_miktar">Miktar</label>
                                                     </div>
                                                 </div>
                                                 <div class="col-md-2">
                                                     <div class="form-floating form-floating-outline">
                                                         <select id="select_birim" class="form-select">
                                                             <option value="KG">KG</option>
                                                             <option value="ADET">ADET</option>
                                                             <option value="LİTRE">LİTRE</option>
                                                             <option value="GRAM">GRAM</option>
                                                             <option value="PAKET">PAKET</option>
                                                             <option value="KOLİ">KOLİ</option>
                                                         </select>
                                                         <label for="select_birim">Birim</label>
                                                     </div>
                                                 </div>
                                                 <div class="col-md-2">
                                                     <div class="form-floating form-floating-outline">
                                                         <input type="number" id="input_fiyat" class="form-control" min="0.01" step="any" placeholder="Birim Fiyat">
                                                         <label for="input_fiyat">Birim Fiyat</label>
                                                     </div>
                                                 </div>
                                                 <div class="col-md-2">
                                                     <div class="form-floating form-floating-outline">
                                                         <select id="select_kdv" class="form-select">
                                                             <?foreach ($rows_kdvs as $k){?>
                                                                 <option value="<?=$k->KDV?>">% <?=$k->KDV?></option>
                                                             <?}?>
                                                         </select>
                                                         <label for="select_kdv">KDV %</label>
                                                     </div>
                                                 </div>
                                                <div class="col-12 text-end">
                                                    <button type="button" id="btn_sepet_ekle" class="btn btn-primary"><i class="ri-add-line me-1"></i>Sepete Ekle</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Sepet / Malzeme Listesi -->
                                    <div class="card mb-6">
                                        <div class="card-header">
                                            <h5 class="card-title m-0"><i class="ri-shopping-basket-line me-2 text-primary"></i>Malzeme Sepeti</h5>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-hover table-striped mb-0 text-nowrap">
                                                     <thead>
                                                         <tr>
                                                             <th>Malzeme</th>
                                                             <th class="text-end">Miktar</th>
                                                             <th class="text-center">Birim</th>
                                                             <th class="text-end text-nowrap">Birim F.</th>
                                                             <th class="text-end text-nowrap">KDV'li</th>
                                                             <th class="text-center text-nowrap">KDV %</th>
                                                             <th class="text-end text-nowrap">KDV Tutarı</th>
                                                             <th class="text-end text-nowrap">Ara Toplam</th>
                                                             <th class="text-end text-nowrap">Toplam</th>
                                                             <th class="text-center" style="width: 80px;">İşlem</th>
                                                         </tr>
                                                     </thead>
                                                     <tbody id="cart_tbody">
                                                         <tr id="empty_cart_row">
                                                             <td colspan="10" class="text-center py-5 text-muted">
                                                                <i class="ri-shopping-basket-line ri-32px d-block mb-2"></i>
                                                                Sepetiniz boş. Malzeme ekleyin.
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sağ Kolon: Fiş Bilgileri & Toplamlar -->
                                <div class="col-12 col-lg-4">
                                    <div class="card mb-6">
                                        <div class="card-header pb-3">
                                            <h5 class="card-title m-0"><i class="ri-file-list-3-line me-2 text-primary"></i>Fiş Bilgileri</h5>
                                        </div>
                                        <div class="card-body">
                                            <form id="form_alis_fisi">
                                                <div class="row g-4">
                                                    <div class="col-12">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="cari_id" id="cari_id" class="select2 form-select" data-style="btn-default" required>
                                                                <?=$cMalzemeAlis->Cariler()->setSeciniz()->getSelect("ID", "AD")?>
                                                            </select>
                                                            <label for="cari_id">Cari</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-floating form-floating-outline">
                                                            <input type="text" id="fatura_no" name="fatura_no" class="form-control" placeholder="Fatura No">
                                                            <label for="fatura_no">Fatura No</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-floating form-floating-outline">
                                                            <input type="text" id="fatura_tarih" name="fatura_tarih" class="form-control datepicker active" placeholder="YYYY-MM-DD" readonly="readonly">
                                                            <label for="fatura_tarih">Fatura Tarihi</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-floating form-floating-outline">
                                                            <input type="text" id="alis_tarih" name="alis_tarih" class="form-control datepicker active" placeholder="YYYY-MM-DD" readonly="readonly" value="<?=date('Y-m-d')?>">
                                                            <label for="alis_tarih">Alış Tarihi</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-floating form-floating-outline">
                                                            <input type="text" id="vade_tarih" name="vade_tarih" class="form-control datepicker active" placeholder="YYYY-MM-DD" readonly="readonly">
                                                            <label for="vade_tarih">Vade Tarihi (Opsiyonel)</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="odeme_durum_id" id="odeme_durum_id" class="select2 form-select" data-style="btn-default">
                                                                <?=$cMalzemeAlis->OdemeDurum()->getSelect("ID", "AD")?>
                                                            </select>
                                                            <label for="odeme_durum_id">Ödeme Durumu</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="odeme_turu" id="odeme_turu" class="form-select">
                                                                <option value="Nakit">Nakit</option>
                                                                <option value="Kredi Kartı">Kredi Kartı</option>
                                                                <option value="Havale/EFT">Havale/EFT</option>
                                                            </select>
                                                            <label for="odeme_turu">Ödeme Türü</label>
                                                        </div>
                                                    </div>
                                                     <div class="col-12">
                                                         <label class="form-label mb-1" style="font-size: 13px; font-weight: 500; color: #5d596c;">Fiyat Giriş Tipi</label>
                                                         <div class="kdv-toggle-group w-100" role="group">
                                                             <input type="radio" class="btn-check" name="kdv_tipi" id="kdv_haric" value="haric" autocomplete="off" checked>
                                                             <label class="btn btn-sm py-2" for="kdv_haric">KDV Hariç</label>

                                                             <input type="radio" class="btn-check" name="kdv_tipi" id="kdv_dahil" value="dahil" autocomplete="off">
                                                             <label class="btn btn-sm py-2" for="kdv_dahil">KDV Dahil</label>
                                                         </div>
                                                     </div>
                                                     <div class="col-12">
                                                         <div class="form-floating form-floating-outline">
                                                             <textarea id="aciklama" name="aciklama" class="form-control" placeholder="Açıklama" style="height: 80px;"></textarea>
                                                             <label for="aciklama">Açıklama</label>
                                                         </div>
                                                     </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Toplam Tutarlar -->
                                    <div class="card mb-6">
                                        <div class="card-header pb-2">
                                            <h5 class="card-title m-0"><i class="ri-calculator-line me-2 text-primary"></i>Toplamlar</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="totals-list-item">
                                                <span class="text-muted">Ara Toplam:</span>
                                                <span class="fw-semibold"><span id="lbl_subtotal">0.00</span> ₺</span>
                                            </div>
                                            <div class="totals-list-item">
                                                <span class="text-muted">Toplam KDV:</span>
                                                <span class="fw-semibold"><span id="lbl_vat">0.00</span> ₺</span>
                                            </div>
                                            <hr class="my-2">
                                            <div class="totals-list-item mb-4">
                                                <span class="h6 mb-0 fw-bold">Genel Toplam:</span>
                                                <span class="h5 mb-0 text-primary fw-bold"><span id="lbl_total">0.00</span> ₺</span>
                                            </div>
                                            
                                            <button type="button" id="btn_fiş_kaydet" class="btn btn-success w-100 py-3"><i class="ri-save-line me-1"></i>Fişi Kaydet</button>
                                            <a href="/views/finans/malzeme_alis_listesi.php" class="btn btn-outline-secondary w-100 mt-2">İptal / Geri Dön</a>
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
    // JS variables
    var activeMaterials = {};
    <?
    foreach ($rows_malzemeler as $m) {
        echo "activeMaterials[{$m->ID}] = { id: {$m->ID}, name: '" . addslashes($m->MALZEME) . "' };\n";
    }
    ?>

    // Basket array
    var cart = [];

    // Render cart items in the table
    function renderCart() {
        var tbody = $("#cart_tbody");
        tbody.find("tr:not(#empty_cart_row)").remove();

        if (cart.length === 0) {
            $("#empty_cart_row").show();
            $("#lbl_subtotal").text("0.00");
            $("#lbl_vat").text("0.00");
            $("#lbl_total").text("0.00");
            return;
        }

        $("#empty_cart_row").hide();

        var kdvTipi = $("input[name='kdv_tipi']:checked").val();

        var subtotal = 0;
        var total_vat = 0;
        var total = 0;

        $.each(cart, function(idx, item) {
            var netPrice = 0;
            var grossPrice = 0;
            if (kdvTipi === 'dahil') {
                netPrice = item.price / (1 + item.kdv / 100);
                grossPrice = item.price;
            } else {
                netPrice = item.price;
                grossPrice = item.price * (1 + item.kdv / 100);
            }

            var line_subtotal = item.quantity * netPrice;
            var line_vat = (line_subtotal * item.kdv) / 100;
            var line_total = line_subtotal + line_vat;

            subtotal += line_subtotal;
            total_vat += line_vat;
            total += line_total;

            var rowHtml = `
                <tr class="cart-item-row">
                    <td>${item.name}</td>
                    <td class="text-end">${item.quantity.toFixed(2)}</td>
                    <td class="text-center"><span class="badge bg-label-secondary">${item.unit}</span></td>
                    <td class="text-end">${netPrice.toFixed(2)} ₺</td>
                    <td class="text-end">${grossPrice.toFixed(2)} ₺</td>
                    <td class="text-center text-center">% ${item.kdv}</td>
                    <td class="text-end">${line_vat.toFixed(2)} ₺</td>
                    <td class="text-end">${line_subtotal.toFixed(2)} ₺</td>
                    <td class="text-end fw-semibold">${line_total.toFixed(2)} ₺</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-link text-danger p-0 btn-item-remove" data-idx="${idx}">
                            <i class="ri-delete-bin-line ri-20px"></i>
                        </button>
                    </td>
                </tr>
            `;
            tbody.append(rowHtml);
        });

        $("#lbl_subtotal").text(subtotal.toFixed(2));
        $("#lbl_vat").text(total_vat.toFixed(2));
        $("#lbl_total").text(total.toFixed(2));
    }

    $(document).ready(function() {
        // Sepete Ekle
        $("#btn_sepet_ekle").on("click", function() {
            var selectVal = $("#select_malzeme").val();
            var quantity = parseFloat($("#input_miktar").val()) || 0;
            var unit = $("#select_birim").val();
            var price = parseFloat($("#input_fiyat").val()) || 0;
            var kdv = parseInt($("#select_kdv").val()) || 0;
            var kdvTipi = $("input[name='kdv_tipi']:checked").val();

            if (!selectVal) {
                notyf.error("Lütfen bir malzeme seçin.");
                return;
            }

            if (quantity <= 0) {
                notyf.error("Miktar 0'dan büyük olmalıdır.");
                return;
            }

            if (price <= 0) {
                notyf.error("Birim Fiyat 0'dan büyük olmalıdır.");
                return;
            }

            var mat = activeMaterials[selectVal];
            if (!mat) return;

            // Check if item is already in cart
            var existingIndex = -1;
            for (var i = 0; i < cart.length; i++) {
                if (cart[i].id === mat.id && cart[i].unit === unit && cart[i].kdv === kdv && Math.abs(cart[i].price - price) < 0.0001) {
                    existingIndex = i;
                    break;
                }
            }

            if (existingIndex > -1) {
                cart[existingIndex].quantity += quantity;
            } else {
                cart.push({
                    id: mat.id,
                    name: mat.name,
                    quantity: quantity,
                    unit: unit,
                    price: price,
                    kdv: kdv
                });
            }

            // Reset inputs
            $("#select_malzeme").val("").trigger("change");
            $("#input_miktar").val("1");
            $("#input_fiyat").val("");

            renderCart();
            notyf.success("Malzeme sepete eklendi.");
        });

        // KDV Tipi Değiştiğinde Sepeti Yeniden Çiz
        $("input[name='kdv_tipi']").on("change", function() {
            renderCart();
        });

        // Remove item from cart
        $(document).on("click", ".btn-item-remove", function() {
            var idx = $(this).data("idx");
            cart.splice(idx, 1);
            renderCart();
            notyf.success("Malzeme sepetten çıkarıldı.");
        });

        // Fişi Kaydet
        $("#btn_fiş_kaydet").on("click", function(e) {
            e.preventDefault();

            if (cart.length === 0) {
                notyf.error("Lütfen sepete en az bir malzeme ekleyin.");
                return;
            }

            var cariId = $("#cari_id").val();
            if (!cariId || cariId <= 0) {
                notyf.error("Lütfen bir Cari seçin.");
                return;
            }

            showSpinner();

            var postData = {
                controller: 'malzemeAlis',
                action: 'malzeme_alis_kaydet',
                cari_id: cariId,
                fatura_no: $("#fatura_no").val(),
                fatura_tarih: $("#fatura_tarih").val(),
                alis_tarih: $("#alis_tarih").val(),
                vade_tarih: $("#vade_tarih").val(),
                odeme_durum_id: $("#odeme_durum_id").val(),
                odeme_turu: $("#odeme_turu").val(),
                aciklama: $("#aciklama").val(),
                kdv_tipi: $("input[name='kdv_tipi']:checked").val(),
                cart_data: JSON.stringify(cart)
            };

            $.ajax({
                url: "/router.php",
                type: "POST",
                data: postData,
                dataType: "json",
                success: function(response) {
                    $.unblockUI();
                    if (response.HATA) {
                        notyf.error(response.ACIKLAMA);
                    } else {
                        notyf.success(response.ACIKLAMA);
                        setTimeout(function() {
                            location.href = "/views/finans/malzeme_alis_detay.php?id=" + response.ID + "&token=" + response.TOKEN;
                        }, 1000);
                    }
                },
                error: function() {
                    $.unblockUI();
                    notyf.error("Alış fişi kaydedilirken sunucu hatası oluştu.");
                }
            });
        });
    });
</script>
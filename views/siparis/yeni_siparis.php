<?
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
    session_kontrol();

    // Query active products
    $rows_urunler = DB::get("SELECT ID, URUN, FIYAT FROM URUN WHERE DURUM = '1' ORDER BY URUN ASC");
?>
<!Doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title> <?=$row_site->TITLE?> | Yeni Sipariş </title>
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
                                    <h4 class="mb-1">Yeni Sipariş Oluştur</h4>
                                    <p class="mb-0">Mağaza içi (walk-in) veya diğer kanallar için manuel sipariş paneli</p>
                                </div>
                            </div>
                            
                            <div class="row">
                                <!-- Sol Kolon: Ürün Ekleme ve Sepet Tablosu -->
                                <div class="col-12 col-lg-7">
                                    <div class="card mb-6">
                                        <div class="card-header pb-3">
                                            <h5 class="card-title m-0"><i class="ri-restaurant-2-fill me-2 text-primary"></i>Ürün Ekle</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-4 align-items-end">
                                                <div class="col-md-7">
                                                    <div class="form-floating form-floating-outline">
                                                        <select id="select_urun" class="select2 form-select" data-style="btn-default">
                                                            <option value="" data-fiyat="0">-- Ürün Seçiniz --</option>
                                                            <?foreach ($rows_urunler as $urun){?>
                                                                <option value="<?=$urun->ID?>" data-fiyat="<?=$urun->FIYAT?>">
                                                                    <?=$urun->URUN?> (<?=FormatSayi::sayi($urun->FIYAT)?> ₺)
                                                                </option>
                                                            <?}?>
                                                        </select>
                                                        <label for="select_urun">Ürünler</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="number" id="input_adet" class="form-control" value="1" min="1" placeholder="Adet">
                                                        <label for="input_adet">Adet</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button" id="btn_urun_ekle" class="btn btn-primary w-100 py-3">Ekle</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card mb-6">
                                        <div class="card-header">
                                            <h5 class="card-title m-0"><i class="ri-shopping-cart-2-line me-2 text-primary"></i>Sipariş Detayları (Sepet)</h5>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-hover table-striped mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Ürün</th>
                                                            <th class="text-end">Birim Fiyat</th>
                                                            <th class="text-center" style="width: 150px;">Adet</th>
                                                            <th class="text-end">Tutar</th>
                                                            <th class="text-center" style="width: 80px;">İşlem</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="cart_tbody">
                                                        <tr id="empty_cart_row">
                                                            <td colspan="5" class="text-center py-5 text-muted">
                                                                <i class="ri-shopping-basket-line ri-32px d-block mb-2"></i>
                                                                Henüz ürün eklenmedi.
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sağ Kolon: Sipariş Bilgileri & Özet -->
                                <div class="col-12 col-lg-5">
                                    <div class="card mb-6">
                                        <div class="card-header">
                                            <h5 class="card-title m-0"><i class="ri-file-list-3-line me-2 text-primary"></i>Sipariş Bilgileri</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-4">
                                                <div class="col-md-6">
                                                    <div class="form-floating form-floating-outline">
                                                        <select id="kaynak" class="select2 form-select" data-style="btn-default">
                                                            <option value="Mağaza" selected>Mağaza</option>
                                                            <option value="Trendyol">Trendyol</option>
                                                            <option value="Yemeksepeti">Yemeksepeti</option>
                                                            <option value="Getir">Getir</option>
                                                        </select>
                                                        <label for="kaynak">Kaynak</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating form-floating-outline">
                                                        <select id="odeme" class="select2 form-select" data-style="btn-default">
                                                            <option value="Nakit" selected>Nakit</option>
                                                            <option value="Kredi Kartı">Kredi Kartı</option>
                                                        </select>
                                                        <label for="odeme">Ödeme Yöntemi</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="text" id="musteri" class="form-control" value="Mağaza Müşterisi" placeholder="Müşteri Adı Soyadı">
                                                        <label for="musteri">Müşteri Adı Soyadı</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="text" id="telefon" class="form-control" placeholder="0555 555 5555">
                                                        <label for="telefon">Telefon (Opsiyonel)</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-floating form-floating-outline">
                                                        <textarea id="siparis_not" class="form-control" placeholder="Sipariş Notu (Opsiyonel)" style="height: 100px;"></textarea>
                                                        <label for="siparis_not">Sipariş Notu (Opsiyonel)</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Fiyat Özeti -->
                                    <div class="card mb-6">
                                        <div class="card-header pb-2">
                                            <h5 class="card-title m-0"><i class="ri-calculator-line me-2 text-primary"></i>Sipariş Toplamı</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="totals-list-item">
                                                <span class="text-muted">Ara Toplam:</span>
                                                <span class="fw-semibold"><span id="lbl_subtotal">0.00</span> ₺</span>
                                            </div>
                                            
                                            <div class="totals-list-item align-items-center">
                                                <span class="text-muted">İndirim Tutar (Opsiyonel):</span>
                                                <div style="width: 130px;">
                                                    <div class="input-group input-group-sm">
                                                        <input type="number" id="input_indirim" class="form-control text-end" value="0" min="0" step="0.01">
                                                        <span class="input-group-text">₺</span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <hr class="my-3">
                                            
                                            <div class="d-flex justify-content-between align-items-center mb-4">
                                                <span class="fs-5 fw-bold text-heading">Genel Toplam:</span>
                                                <span class="fs-4 fw-bold text-success"><span id="lbl_total">0.00</span> ₺</span>
                                            </div>

                                            <button type="button" id="btn_siparis_olustur" class="btn btn-success btn-lg w-100 py-3 waves-effect waves-light">
                                                <i class="ri-check-line me-2"></i>Siparişi Tamamla
                                            </button>
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
    // Local Product Registry for quick price lookup
    var activeProducts = {
        <?foreach ($rows_urunler as $u) {
            echo intval($u->ID) . ": { id: " . intval($u->ID) . ", name: '" . addslashes($u->URUN) . "', price: " . floatval($u->FIYAT) . " },\n";
        }?>
    };

    var cart = [];

    function renderCart() {
        var tbody = $("#cart_tbody");
        tbody.find(".cart-item-row").remove();

        if (cart.length === 0) {
            $("#empty_cart_row").show();
            $("#lbl_subtotal").text("0.00");
            $("#lbl_total").text("0.00");
            return;
        }

        $("#empty_cart_row").hide();
        var subtotal = 0;

        cart.forEach(function(item, idx) {
            var itemTotal = item.price * item.quantity;
            subtotal += itemTotal;

            var rowHtml = `
                <tr class="cart-item-row" data-id="${item.id}">
                    <td><strong>${item.name}</strong></td>
                    <td class="text-end">${item.price.toFixed(2)} ₺</td>
                    <td class="text-center">
                        <div class="input-group input-group-sm m-auto" style="max-width: 100px;">
                            <button type="button" class="btn btn-outline-secondary px-2 btn-qty-dec" data-idx="${idx}">-</button>
                            <input type="text" class="form-control text-center input-qty" value="${item.quantity}" data-idx="${idx}" readonly>
                            <button type="button" class="btn btn-outline-secondary px-2 btn-qty-inc" data-idx="${idx}">+</button>
                        </div>
                    </td>
                    <td class="text-end fw-semibold">${itemTotal.toFixed(2)} ₺</td>
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
        updateTotals();
    }

    function updateTotals() {
        var subtotal = parseFloat($("#lbl_subtotal").text()) || 0;
        var discount = parseFloat($("#input_indirim").val()) || 0;
        if (discount < 0) {
            discount = 0;
            $("#input_indirim").val(0);
        }
        var total = subtotal - discount;
        if (total < 0) {
            total = 0;
        }
        $("#lbl_total").text(total.toFixed(2));
    }

    $(document).ready(function() {
        // Add item to cart
        $("#btn_urun_ekle").on("click", function() {
            var selectVal = $("#select_urun").val();
            var quantity = parseInt($("#input_adet").val()) || 1;

            if (!selectVal) {
                notyf.error("Lütfen bir ürün seçin.");
                return;
            }

            if (quantity < 1) {
                notyf.error("Adet en az 1 olmalıdır.");
                return;
            }

            var prod = activeProducts[selectVal];
            if (!prod) return;

            // Check if item is already in cart
            var existingIndex = -1;
            for (var i = 0; i < cart.length; i++) {
                if (cart[i].id === prod.id) {
                    existingIndex = i;
                    break;
                }
            }

            if (existingIndex > -1) {
                cart[existingIndex].quantity += quantity;
            } else {
                cart.push({
                    id: prod.id,
                    name: prod.name,
                    price: prod.price,
                    quantity: quantity
                });
            }

            // Reset inputs
            $("#select_urun").val("").trigger("change");
            $("#input_adet").val("1");

            renderCart();
            notyf.success("Ürün sepete eklendi.");
        });

        // Quantity increase
        $(document).on("click", ".btn-qty-inc", function() {
            var idx = $(this).data("idx");
            cart[idx].quantity += 1;
            renderCart();
        });

        // Quantity decrease
        $(document).on("click", ".btn-qty-dec", function() {
            var idx = $(this).data("idx");
            if (cart[idx].quantity > 1) {
                cart[idx].quantity -= 1;
            } else {
                cart.splice(idx, 1);
            }
            renderCart();
        });

        // Remove item from cart
        $(document).on("click", ".btn-item-remove", function() {
            var idx = $(this).data("idx");
            cart.splice(idx, 1);
            renderCart();
            notyf.success("Ürün sepetten çıkarıldı.");
        });

        // Discount input change
        $("#input_indirim").on("input change", function() {
            updateTotals();
        });

        // Submit order via AJAX
        $("#btn_siparis_olustur").on("click", function(e) {
            e.preventDefault();

            if (cart.length === 0) {
                notyf.error("Lütfen sepete en az bir ürün ekleyin.");
                return;
            }

            showSpinner();

            var postData = {
                controller: 'siparis',
                action: 'siparis_ekle',
                kaynak: $("#kaynak").val(),
                musteri: $("#musteri").val(),
                telefon: $("#telefon").val(),
                odeme: $("#odeme").val(),
                siparis_not: $("#siparis_not").val(),
                indirim_tutar: $("#input_indirim").val(),
                urun_id: cart.map(function(item) { return item.id; }),
                adet: cart.map(function(item) { return item.quantity; })
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
                            location.href = "/views/siparis/siparis_detay.php?route=siparis/siparis_listesi&id=" + response.ID + "&token=" + response.TOKEN;
                        }, 1000);
                    }
                },
                error: function() {
                    $.unblockUI();
                    notyf.error("Sipariş kaydedilirken sunucu hatası oluştu.");
                }
            });
        });
    });
</script>

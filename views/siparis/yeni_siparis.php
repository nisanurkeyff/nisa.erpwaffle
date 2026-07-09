<?
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
    session_kontrol();

    // Query active products
    $rows_urunler = DB::get("SELECT ID, URUN, FIYAT FROM URUN WHERE DURUM = '1' ORDER BY URUN ASC");

    // Query active extra materials
    $rows_ekstralar = DB::get("SELECT ID, MALZEME, EKSTRA_FIYAT FROM MALZEME WHERE DURUM = '1' AND EKSTRA = '1' ORDER BY MALZEME ASC");

    // Query active order sources and types
    $rows_kaynaklar = DB::get("SELECT ID, KAYNAK FROM SIPARIS_KAYNAK WHERE DURUM = '1' ORDER BY ID ASC");
    $rows_tipler = DB::get("SELECT ID, SIPARIS_TIPI FROM SIPARIS_TIPI WHERE DURUM = '1' ORDER BY ID ASC");

    $is_edit = false;
    $order_row = null;
    $cart_js = '[]';

    if (isset($_GET['id']) && intval($_GET['id']) > 0) {
        $order_row = $cSiparis->getSiparis(['id' => $_GET['id']]);
        if ($order_row) {
            fncTokenKontrol($order_row);
            $is_edit = true;
            $details = $cSiparis->getSiparisDetay(['id' => $order_row->ID]);
            $cart_items = [];
            if ($details) {
                foreach ($details as $d) {
                    $extras_rows = $cSiparis->getSiparisDetayEkstra(['siparis_detay_id' => $d->ID]);
                    $extras = [];
                    if ($extras_rows) {
                        foreach ($extras_rows as $ex) {
                            $extras[] = [
                                'id' => intval($ex->MALZEME_ID),
                                'name' => $ex->MALZEME_AD,
                                'price' => floatval($ex->FIYAT)
                            ];
                        }
                    }
                    $cart_items[] = [
                        'id' => intval($d->URUN_ID),
                        'name' => $d->URUN,
                        'price' => floatval($d->FIYAT),
                        'quantity' => intval($d->ADET),
                        'extras' => $extras
                    ];
                }
            }
            $cart_js = json_encode($cart_items);
        }
    }
?>
<!Doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title> <?=$row_site->TITLE?> | <?=($is_edit) ? 'Siparişi Düzenle' : 'Yeni Sipariş'?> </title>
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
            .btn-extra-remove {
                background: none;
                border: none;
                color: #ff3e1d;
                padding: 0 4px;
                font-weight: bold;
                cursor: pointer;
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
                                    <h4 class="mb-1"><?=($is_edit) ? "Sipariş Düzenle (No: #{$order_row->SIPARIS_NO})" : "Yeni Sipariş Oluştur"?></h4>
                                    <p class="mb-0">Manuel sipariş paneli</p>
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
                                                            <?foreach($rows_kaynaklar as $k){?>
                                                                <option value="<?=$k->KAYNAK?>" <?=$is_edit && $order_row->KAYNAK == $k->KAYNAK ? 'selected' : (!$is_edit && $k->KAYNAK == 'Mağaza' ? 'selected' : '')?>><?=$k->KAYNAK?></option>
                                                            <?}?>
                                                        </select>
                                                        <label for="kaynak">Kaynak</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating form-floating-outline">
                                                        <select id="siparis_tipi" class="select2 form-select" data-style="btn-default">
                                                            <?foreach($rows_tipler as $t){?>
                                                                <option value="<?=$t->SIPARIS_TIPI?>" <?=$is_edit && $order_row->SIPARIS_TIPI == $t->SIPARIS_TIPI ? 'selected' : (!$is_edit && $t->SIPARIS_TIPI == 'Gel Al' ? 'selected' : '')?>><?=$t->SIPARIS_TIPI?></option>
                                                            <?}?>
                                                        </select>
                                                        <label for="siparis_tipi">Sipariş Tipi</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating form-floating-outline">
                                                        <select id="odeme" class="select2 form-select" data-style="btn-default">
                                                            <option value="Nakit" <?=$is_edit && $order_row->ODEME == 'Nakit' ? 'selected' : ''?>>Nakit</option>
                                                            <option value="Kredi Kartı" <?=$is_edit && $order_row->ODEME == 'Kredi Kartı' ? 'selected' : ''?>>Kredi Kartı</option>
                                                        </select>
                                                        <label for="odeme">Ödeme Yöntemi</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="number" id="input_hazirlanma" class="form-control" value="<?=$is_edit ? intval($order_row->HAZIRLANMA_SURESI) : ''?>" placeholder="30">
                                                        <label for="input_hazirlanma">Hazırlanma Süresi (Dakika)</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="number" id="input_komisyon_orani" class="form-control" value="<?=$is_edit ? floatval($order_row->KOMISYON_ORANI) : '0'?>" placeholder="0" min="0" max="100" step="0.1">
                                                        <label for="input_komisyon_orani">Komisyon Oranı (%)</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="text" id="musteri" class="form-control" value="<?=$is_edit ? htmlspecialchars($order_row->MUSTERI) : 'Mağaza Müşterisi'?>" placeholder="Müşteri Adı Soyadı">
                                                        <label for="musteri">Müşteri Adı Soyadı</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="text" id="telefon" class="form-control" value="<?=$is_edit ? htmlspecialchars($order_row->TELEFON) : ''?>" placeholder="0555 555 5555">
                                                        <label for="telefon">Telefon (Opsiyonel)</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-floating form-floating-outline">
                                                        <textarea id="siparis_not" class="form-control" placeholder="Sipariş Notu (Opsiyonel)" style="height: 100px;"><?=$is_edit ? htmlspecialchars($order_row->SIPARIS_NOT) : ''?></textarea>
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

                                            <div class="totals-list-item">
                                                <span class="text-muted">Ekstra Toplam:</span>
                                                <span class="fw-semibold"><span id="lbl_extras_total">0.00</span> ₺</span>
                                            </div>
                                            
                                            <div class="totals-list-item align-items-center">
                                                <span class="text-muted">İndirim Tutar (Opsiyonel):</span>
                                                <div style="width: 130px;">
                                                    <div class="input-group input-group-sm">
                                                        <input type="number" id="input_indirim" class="form-control text-end" value="<?=$is_edit ? floatval($order_row->INDIRIM_TUTAR) : 0?>" min="0" step="0.01">
                                                        <span class="input-group-text">₺</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="totals-list-item align-items-center mt-2">
                                                <span class="text-muted">Teslimat Ücreti (Opsiyonel):</span>
                                                <div style="width: 130px;">
                                                    <div class="input-group input-group-sm">
                                                        <input type="number" id="input_teslimat" class="form-control text-end" value="<?=$is_edit ? floatval($order_row->TESLIMAT_UCRETI) : 0?>" min="0" step="0.01">
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
                                                <i class="ri-check-line me-2"></i><?=($is_edit) ? 'Değişiklikleri Kaydet' : 'Siparişi Tamamla'?>
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

        <!-- Ekstra Malzeme Ekleme Modalı -->
        <div class="modal fade" id="extraModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Ekstra Malzeme Ekle</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="modal_item_idx">
                        <div class="form-floating form-floating-outline">
                            <select id="modal_select_extra" class="select2 form-select" data-dropdown-parent="#extraModal">
                                <option value="">-- Malzeme Seçiniz --</option>
                                <?foreach($rows_ekstralar as $ex){?>
                                    <option value="<?=$ex->ID?>" data-fiyat="<?=$ex->EKSTRA_FIYAT?>"><?=$ex->MALZEME?> (<?=FormatSayi::sayi($ex->EKSTRA_FIYAT)?> ₺)</option>
                                <?}?>
                            </select>
                            <label for="modal_select_extra">Ekstra Malzemeler</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
                        <button type="button" id="btn_modal_extra_save" class="btn btn-primary">Ekle</button>
                    </div>
                </div>
            </div>
        </div>

        <?=$cTheme->Scriptler()?>
    </body>
</html>

<script type="text/javascript">
    // Local registries
    var activeProducts = {
        <?foreach ($rows_urunler as $u) {
            echo intval($u->ID) . ": { id: " . intval($u->ID) . ", name: '" . addslashes($u->URUN) . "', price: " . floatval($u->FIYAT) . " },\n";
        }?>
    };

    var activeExtras = {
        <?foreach ($rows_ekstralar as $ex) {
            echo intval($ex->ID) . ": { id: " . intval($ex->ID) . ", name: '" . addslashes($ex->MALZEME) . "', price: " . floatval($ex->EKSTRA_FIYAT) . " },\n";
        }?>
    };

    var cart = <?=$cart_js?>;
    var isEdit = <?=$is_edit ? 'true' : 'false'?>;
    var orderId = <?=$is_edit ? intval($order_row->ID) : 0?>;

    function renderCart() {
        var tbody = $("#cart_tbody");
        tbody.find(".cart-item-row").remove();

        if (cart.length === 0) {
            $("#empty_cart_row").show();
            $("#lbl_subtotal").text("0.00");
            $("#lbl_extras_total").text("0.00");
            $("#lbl_total").text("0.00");
            return;
        }

        $("#empty_cart_row").hide();
        var subtotal = 0;
        var extras_total = 0;

        cart.forEach(function(item, idx) {
            var itemBaseTotal = item.price * item.quantity;
            subtotal += itemBaseTotal;
            
            var itemExtrasTotal = 0;
            var extrasHtml = "";
            if (item.extras && item.extras.length > 0) {
                item.extras.forEach(function(ex, exIdx) {
                    var exTotal = ex.price * item.quantity;
                    extras_total += exTotal;
                    itemExtrasTotal += exTotal;
                    extrasHtml += `
                        <div class="d-flex justify-content-between align-items-center mb-1 text-success text-nowrap">
                            <span>+ ${ex.name}</span>
                            <span>
                                ${ex.price.toFixed(2)} ₺
                                <button type="button" class="btn-extra-remove" data-item-idx="${idx}" data-extra-idx="${exIdx}">&times;</button>
                            </span>
                        </div>
                    `;
                });
            }

            var itemGrandTotal = itemBaseTotal + itemExtrasTotal;

            var rowHtml = `
                <tr class="cart-item-row" data-id="${item.id}">
                    <td>
                        <div><strong>${item.name}</strong></div>
                        <div class="ps-2 mt-1 small">
                            ${extrasHtml}
                            <button type="button" class="btn btn-link text-primary p-0 btn-extra-add mt-1 small" data-idx="${idx}">+ Ekstra Malzeme Ekle</button>
                        </div>
                    </td>
                    <td class="text-end">${item.price.toFixed(2)} ₺</td>
                    <td class="text-center">
                        <div class="input-group input-group-sm m-auto" style="max-width: 100px;">
                            <button type="button" class="btn btn-outline-secondary px-2 btn-qty-dec" data-idx="${idx}">-</button>
                            <input type="text" class="form-control text-center input-qty" value="${item.quantity}" data-idx="${idx}" readonly>
                            <button type="button" class="btn btn-outline-secondary px-2 btn-qty-inc" data-idx="${idx}">+</button>
                        </div>
                    </td>
                    <td class="text-end fw-semibold">${itemGrandTotal.toFixed(2)} ₺</td>
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
        $("#lbl_extras_total").text(extras_total.toFixed(2));
        updateTotals();
    }

    function updateTotals() {
        var subtotal = parseFloat($("#lbl_subtotal").text()) || 0;
        var extras_total = parseFloat($("#lbl_extras_total").text()) || 0;
        var discount = parseFloat($("#input_indirim").val()) || 0;
        if (discount < 0) {
            discount = 0;
            $("#input_indirim").val(0);
        }
        var delivery = parseFloat($("#input_teslimat").val()) || 0;
        if (delivery < 0) {
            delivery = 0;
            $("#input_teslimat").val(0);
        }
        
        var total = (subtotal + extras_total) - discount + delivery;
        if (total < 0) {
            total = 0;
        }
        $("#lbl_total").text(total.toFixed(2));
    }

    $(document).ready(function() {
        // Initial cart render if edit mode
        renderCart();

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
                    quantity: quantity,
                    extras: []
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

        // Extra modal trigger
        $(document).on("click", ".btn-extra-add", function() {
            var idx = $(this).data("idx");
            $("#modal_item_idx").val(idx);
            $("#modal_select_extra").val("").trigger("change");
            
            // Show modal using Bootstrap API
            var extraModal = new bootstrap.Modal(document.getElementById('extraModal'));
            extraModal.show();
        });

        // Save extra from modal
        $("#btn_modal_extra_save").on("click", function() {
            var idx = parseInt($("#modal_item_idx").val());
            var extraId = $("#modal_select_extra").val();
            if (!extraId) {
                notyf.error("Lütfen bir malzeme seçin.");
                return;
            }
            var extra = activeExtras[extraId];
            if (!cart[idx].extras) {
                cart[idx].extras = [];
            }
            // Check if extra already added to this item
            var exists = false;
            for(var i=0; i<cart[idx].extras.length; i++) {
                if(cart[idx].extras[i].id == extra.id) {
                    exists = true;
                    break;
                }
            }
            if(exists) {
                notyf.error("Bu ekstra malzeme zaten eklenmiş.");
                return;
            }

            cart[idx].extras.push({
                id: extra.id,
                name: extra.name,
                price: extra.price
            });

            // Hide modal
            var modalEl = document.getElementById('extraModal');
            var modalInstance = bootstrap.Modal.getInstance(modalEl);
            modalInstance.hide();
            
            renderCart();
            notyf.success("Ekstra malzeme eklendi.");
        });

        // Remove extra material
        $(document).on("click", ".btn-extra-remove", function() {
            var itemIdx = $(this).data("item-idx");
            var extraIdx = $(this).data("extra-idx");
            cart[itemIdx].extras.splice(extraIdx, 1);
            renderCart();
            notyf.success("Ekstra malzeme çıkarıldı.");
        });

        // Inputs changes
        $("#input_indirim, #input_teslimat").on("input change", function() {
            updateTotals();
        });

        // Auto-change commission rate based on kaynak selection
        $("#kaynak").on("change", function() {
            var kaynak = $(this).val();
            var comm = 0;
            if (kaynak === 'Trendyol Go') comm = 38;
            else if (kaynak === 'Yemeksepeti') comm = 35;
            else if (kaynak === 'Getir') comm = 30;
            $("#input_komisyon_orani").val(comm);
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
                id: orderId,
                kaynak: $("#kaynak").val(),
                siparis_tipi: $("#siparis_tipi").val(),
                odeme: $("#odeme").val(),
                musteri: $("#musteri").val(),
                telefon: $("#telefon").val(),
                siparis_not: $("#siparis_not").val(),
                indirim_tutar: $("#input_indirim").val(),
                teslimat_ucreti: $("#input_teslimat").val(),
                hazirlanma_suresi: $("#input_hazirlanma").val(),
                komisyon_orani: $("#input_komisyon_orani").val(),
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

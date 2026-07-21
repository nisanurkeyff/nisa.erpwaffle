<!-- Central Cost Breakdown & Product Financial Analysis Modal (Extensible Architecture) -->
<div class="modal fade" id="maliyetDetayModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary py-3">
                <h5 class="modal-title text-white fw-bold"><i class="ri-calculator-line me-2"></i><span id="maliyet_modal_urun_adi">Ürün Maliyet Detayı</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <ul class="nav nav-tabs nav-fill mb-4" id="maliyetModalTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab_m_hamur" type="button" role="tab">Hamur</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_m_malzeme" type="button" role="tab">Malzemeler</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_m_paketleme" type="button" role="tab">Paketleme</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_m_sarf" type="button" role="tab">Sarf</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_m_genel" type="button" role="tab">Genel Giderler</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_m_ozet" type="button" role="tab">Toplam Özet</button>
                    </li>
                    <!-- Future Extensible Analysis Slot (Komisyon, Kârlılık, Kampanya, Platform) -->
                    <li class="nav-item d-none" id="tab_ext_slot_header" role="presentation">
                        <button class="nav-link text-warning" data-bs-toggle="tab" data-bs-target="#tab_m_ext" type="button" role="tab"><i class="ri-line-chart-line me-1"></i>Analizler</button>
                    </li>
                </ul>

                <div class="tab-content border-0 p-0">
                    <!-- Hamur Tab -->
                    <div class="tab-pane fade show active" id="tab_m_hamur" role="tabpanel">
                        <table class="table table-bordered table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Hamur Tipi</th>
                                    <th class="text-center">Kullanılan Katsayı</th>
                                    <th class="text-end">Tam Hamur Maliyeti</th>
                                    <th class="text-end">Kullanılan Hamur Maliyeti</th>
                                </tr>
                            </thead>
                            <tbody id="maliyet_hamur_body"></tbody>
                        </table>
                    </div>

                    <!-- Malzemeler Tab -->
                    <div class="tab-pane fade" id="tab_m_malzeme" role="tabpanel">
                        <table class="table table-bordered table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Malzeme Adı</th>
                                    <th class="text-end">Kullanılan Miktar</th>
                                    <th class="text-end">Son Alış Fiyatı</th>
                                    <th class="text-end">Satır Toplamı</th>
                                </tr>
                            </thead>
                            <tbody id="maliyet_malzemeler_body"></tbody>
                        </table>
                    </div>

                    <!-- Paketleme Tab -->
                    <div class="tab-pane fade" id="tab_m_paketleme" role="tabpanel">
                        <table class="table table-bordered table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Paketleme Malzemesi</th>
                                    <th class="text-end">Adet</th>
                                    <th class="text-end">Birim Fiyat</th>
                                    <th class="text-end">Toplam</th>
                                </tr>
                            </thead>
                            <tbody id="maliyet_paketleme_body"></tbody>
                        </table>
                    </div>

                    <!-- Sarf Tab -->
                    <div class="tab-pane fade" id="tab_m_sarf" role="tabpanel">
                        <table class="table table-bordered table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Sarf Malzemesi</th>
                                    <th class="text-end">Adet / Miktar</th>
                                    <th class="text-end">Birim Fiyat</th>
                                    <th class="text-end">Toplam</th>
                                </tr>
                            </thead>
                            <tbody id="maliyet_sarf_body"></tbody>
                        </table>
                    </div>

                    <!-- Genel Giderler Tab -->
                    <div class="tab-pane fade" id="tab_m_genel" role="tabpanel">
                        <table class="table table-bordered table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Genel Gider Kalemi</th>
                                    <th class="text-center">Hesap Türü</th>
                                    <th class="text-end">Tutar / Oran</th>
                                    <th class="text-end">Toplam</th>
                                </tr>
                            </thead>
                            <tbody id="maliyet_genel_gider_body"></tbody>
                        </table>
                    </div>

                    <!-- Toplam Özet Tab -->
                    <div class="tab-pane fade" id="tab_m_ozet" role="tabpanel">
                        <table class="table table-striped align-middle">
                            <tbody>
                                <tr>
                                    <td>Hamur Toplamı</td>
                                    <td class="text-end fw-bold" id="maliyet_hamur_toplami">0.00 TL</td>
                                </tr>
                                <tr>
                                    <td>Malzeme Toplamı <span class="badge bg-secondary ms-1" id="maliyet_malzeme_adet">0 adet</span></td>
                                    <td class="text-end fw-bold" id="maliyet_malzeme_toplami">0.00 TL</td>
                                </tr>
                                <tr>
                                    <td>Paketleme Toplamı <span class="badge bg-secondary ms-1" id="maliyet_paketleme_adet">0 adet</span></td>
                                    <td class="text-end fw-bold" id="maliyet_paketleme_toplami">0.00 TL</td>
                                </tr>
                                <tr>
                                    <td>Sarf Malzemesi Toplamı <span class="badge bg-secondary ms-1" id="maliyet_sarf_adet">0 adet</span></td>
                                    <td class="text-end fw-bold" id="maliyet_sarf_toplami">0.00 TL</td>
                                </tr>
                                <tr>
                                    <td>Genel Gider Toplamı</td>
                                    <td class="text-end fw-bold" id="maliyet_genel_gider_toplami">0.00 TL</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Future Extensible Analysis Slot Pane -->
                    <div class="tab-pane fade" id="tab_m_ext" role="tabpanel">
                        <div id="maliyet_ext_body" class="p-3 text-center text-muted">
                            Gelişmiş kârlılık, komisyon ve platform analizleri yakında eklenecektir.
                        </div>
                    </div>
                </div>

                <!-- Grand Total Banner -->
                <div class="card bg-lighter border mt-4">
                    <div class="card-body p-3 text-center">
                        <div class="text-muted small mb-1 fw-semibold">TOPLAM ÜRÜN MALİYETİ</div>
                        <div class="fs-2 text-primary fw-bolder" id="maliyet_genel_toplam">0.00 TL</div>
                    </div>
                </div>

                <!-- Update Metadata Footer -->
                <div class="row g-2 mt-3 pt-3 border-top small text-muted">
                    <div class="col-md-6">Son Hesaplama Tarihi: <span id="maliyet_son_hesaplama" class="fw-bold text-dark">-</span></div>
                    <div class="col-md-6">Son Reçete Güncellemesi: <span id="maliyet_son_recete" class="fw-bold text-dark">-</span></div>
                    <div class="col-md-6">Son Hamur Güncellemesi: <span id="maliyet_son_hamur" class="fw-bold text-dark">-</span></div>
                    <div class="col-md-6">Son Malzeme Fiyat Güncellemesi: <span id="maliyet_son_fiyat" class="fw-bold text-dark">-</span></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
(function() {
    function initMaliyetDetayModal() {
        $(document).on("click", ".fncMaliyetGoster, #anlik_maliyet", function(e) {
            e.preventDefault();
            var $el = $(this);
            var urunId = $el.data("urun-id");
            var context = {
                source: $el.data("source") || "ui_trigger",
                siparis_detay_id: $el.data("siparis-detay-id") || null
            };
            if (urunId) {
                window.fncMaliyetDetayGoster(urunId, context);
            }
        });
    }

    window.fncMaliyetDetayGoster = function(urunId, context) {
        context = context || {};
        if (typeof showSpinner === 'function') {
            showSpinner();
        }
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: {
                controller: "urun",
                action: "getMaliyetDetayi",
                urun_id: urunId,
                context: context
            },
            dataType: "json",
            success: function(response) {
                if (typeof $.unblockUI === 'function') {
                    $.unblockUI();
                }
                if (response.HATA) {
                    if (typeof notyf !== 'undefined') {
                        notyf.error(response.ACIKLAMA);
                    } else {
                        alert(response.ACIKLAMA);
                    }
                    return;
                }

                var formatMoney = function(val) {
                    if (val === null || val === undefined) return "0.00 TL";
                    return parseFloat(val).toFixed(2).replace(".", ",") + " TL";
                };

                // Title (using product metadata node 'urun')
                var urunAdi = (response.urun && response.urun.urun_adi) ? response.urun.urun_adi : (response.urun_adi || "");
                if (urunAdi) {
                    $("#maliyet_modal_urun_adi").text(urunAdi + " - Maliyet Detayı");
                } else {
                    $("#maliyet_modal_urun_adi").text("Ürün Maliyet Detayı");
                }

                // Hamur
                var h = response.hamur || {};
                var hamurHtml = '';
                if (h.hamur_tipi) {
                    hamurHtml += '<tr>' +
                        '<td><strong>' + h.hamur_tipi + '</strong></td>' +
                        '<td class="text-center">× ' + parseFloat(h.kullanilan_katsayi).toFixed(2) + '</td>' +
                        '<td class="text-end">' + formatMoney(h.tam_hamur_maliyet) + '</td>' +
                        '<td class="text-end fw-bold text-primary">' + formatMoney(h.kullanilan_hamur_maliyet) + '</td>' +
                    '</tr>';
                } else {
                    hamurHtml = '<tr><td colspan="4" class="text-center text-muted">Hamur bilgisi bulunmuyor.</td></tr>';
                }
                $("#maliyet_hamur_body").html(hamurHtml);

                // Malzemeler
                var mList = response.malzemeler || [];
                var malzHtml = '';
                if (mList.length > 0) {
                    $.each(mList, function(i, item) {
                        malzHtml += '<tr>' +
                            '<td>' + item.malzeme_adi + '</td>' +
                            '<td class="text-end">' + item.kullanilan_miktar + ' ' + item.birim + '</td>' +
                            '<td class="text-end">' + formatMoney(item.son_alis_fiyati) + '</td>' +
                            '<td class="text-end fw-bold">' + formatMoney(item.satir_toplami) + '</td>' +
                        '</tr>';
                    });
                } else {
                    malzHtml = '<tr><td colspan="4" class="text-center text-muted">Reçetede malzeme bulunmuyor.</td></tr>';
                }
                $("#maliyet_malzemeler_body").html(malzHtml);

                // Paketleme
                var pList = response.paketleme || [];
                var pakHtml = '';
                if (pList.length > 0) {
                    $.each(pList, function(i, item) {
                        pakHtml += '<tr>' +
                            '<td>' + item.malzeme_adi + '</td>' +
                            '<td class="text-end">' + item.kullanilan_miktar + ' ' + item.birim + '</td>' +
                            '<td class="text-end">' + formatMoney(item.son_alis_fiyati) + '</td>' +
                            '<td class="text-end fw-bold">' + formatMoney(item.satir_toplami) + '</td>' +
                        '</tr>';
                    });
                } else {
                    pakHtml = '<tr><td colspan="4" class="text-center text-muted">Henüz paketleme malzemesi tanımlanmamış.</td></tr>';
                }
                $("#maliyet_paketleme_body").html(pakHtml);

                // Sarf
                var sList = response.sarf || [];
                var sarfHtml = '';
                if (sList.length > 0) {
                    $.each(sList, function(i, item) {
                        sarfHtml += '<tr>' +
                            '<td>' + item.malzeme_adi + '</td>' +
                            '<td class="text-end">' + item.kullanilan_miktar + ' ' + item.birim + '</td>' +
                            '<td class="text-end">' + formatMoney(item.son_alis_fiyati) + '</td>' +
                            '<td class="text-end fw-bold">' + formatMoney(item.satir_toplami) + '</td>' +
                        '</tr>';
                    });
                } else {
                    sarfHtml = '<tr><td colspan="4" class="text-center text-muted">Sarf malzemesi bulunmuyor.</td></tr>';
                }
                $("#maliyet_sarf_body").html(sarfHtml);

                // Genel Giderler
                var gList = response.genel_giderler || [];
                var gHtml = '';
                if (gList.length > 0) {
                    $.each(gList, function(i, item) {
                        gHtml += '<tr>' +
                            '<td>' + item.tip + '</td>' +
                            '<td class="text-center">' + item.hesaplama_tipi + '</td>' +
                            '<td class="text-end">' + formatMoney(item.tutar) + '</td>' +
                            '<td class="text-end fw-bold">' + formatMoney(item.toplam) + '</td>' +
                        '</tr>';
                    });
                } else {
                    gHtml = '<tr><td colspan="4" class="text-center text-muted">Genel gider tanımı bulunmuyor.</td></tr>';
                }
                $("#maliyet_genel_gider_body").html(gHtml);

                // Totals
                var ozet = response.ozet || {};
                var t = response.toplam || {};
                $("#maliyet_hamur_toplami").text(formatMoney(ozet.tophamur || ozet.toplam_hamur));
                $("#maliyet_malzeme_toplami").text(formatMoney(ozet.toplam_malzeme));
                $("#maliyet_paketleme_toplami").text(formatMoney(ozet.toplam_paketleme));
                $("#maliyet_sarf_toplami").text(formatMoney(ozet.toplam_sarf));
                $("#maliyet_genel_gider_toplami").text(formatMoney(t.genel_gider_toplami));
                $("#maliyet_genel_toplam").text(formatMoney(parseFloat(ozet.toplam_maliyet || 0) + parseFloat(t.genel_gider_toplami || 0)));

                // Item Counts
                $("#maliyet_malzeme_adet").text((ozet.adet_malzeme || 0) + " adet");
                $("#maliyet_paketleme_adet").text((ozet.adet_paketleme || 0) + " adet");
                $("#maliyet_sarf_adet").text((ozet.adet_sarf || 0) + " adet");

                // Metadata
                var meta = response.son_guncelleme || {};
                $("#maliyet_son_hesaplama").text(meta.son_hesaplama_tarihi || '-');
                $("#maliyet_son_recete").text(meta.son_recete_guncellemesi || '-');
                $("#maliyet_son_hamur").text(meta.son_hamur_guncellemesi || '-');
                $("#maliyet_son_fiyat").text(meta.son_malzeme_fiyat_guncellemesi || '-');

                // Extensible Analysis Slot Handler (Future Extensions)
                if (response.analizler) {
                    $("#tab_ext_slot_header").removeClass("d-none");
                }

                // Show active first tab safely
                if (typeof $.fn.tab === 'function') {
                    $('#maliyetModalTabs button:first').tab('show');
                }
                var modalEl = document.getElementById("maliyetDetayModal");
                if (modalEl) {
                    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        var modalObj = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                        modalObj.show();
                    } else if (typeof $.fn.modal === 'function') {
                        $("#maliyetDetayModal").modal("show");
                    }
                }
            },
            error: function() {
                if (typeof $.unblockUI === 'function') {
                    $.unblockUI();
                }
                if (typeof notyf !== 'undefined') {
                    notyf.error("Maliyet detayı yüklenirken bir hata oluştu.");
                } else {
                    alert("Maliyet detayı yüklenirken bir hata oluştu.");
                }
            }
        });
    };

    // Polling helper to wait for jQuery
    if (window.jQuery) {
        initMaliyetDetayModal();
    } else {
        var interval = setInterval(function() {
            if (window.jQuery) {
                clearInterval(interval);
                initMaliyetDetayModal();
            }
        }, 50);
    }
})();
</script>

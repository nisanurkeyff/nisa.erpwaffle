    
    var notyf = new Notyf();

    $("#il_id").on("change", function(event) {
        $.ajax({
            url: "/router.php",
            type: "POST",
            data: {il_id: $("#il_id").val(), controller: "kullanici", action: "ilce_doldur"},
            dataType: "json",
            success: function(response) {
                if (response.HATA) {
                    $("#ilce_id").html('');
                } else {
                    $("#ilce_id").html(response.HTML);
                }
            }
        });
    });

    String.prototype.turkishToUpper = function(){
        var string = this;
        var letters = { "i": "İ", "ş": "Ş", "ğ": "Ğ", "ü": "Ü", "ö": "Ö", "ç": "Ç", "ı": "I" };
        string = string.replace(/(([iışğüçö]))/g, function(letter){ return letters[letter]; })
        return string.toUpperCase();
    }

    String.prototype.turkishToLower = function(){
        var string = this;
        var letters = { "İ": "i", "I": "ı", "Ş": "ş", "Ğ": "ğ", "Ü": "ü", "Ö": "ö", "Ç": "ç" };
        string = string.replace(/(([İIŞĞÜÇÖ]))/g, function(letter){ return letters[letter]; })
        return string.toLowerCase();
    }

    String.prototype.englishToUpper = function(){
        var string = this;
        var letters = { "i": "I", "ş": "S", "ğ": "G", "ü": "U", "ö": "O", "ç": "C", "ı": "I" };
        string = string.replace(/(([iışğüçö]))/g, function(letter){ return letters[letter]; })
        return string.toUpperCase();
    }
    
    String.prototype.englishToLower = function(){
        var string = this;
        var letters = { "İ": "i", "I": "ı", "Ş": "ş", "Ğ": "ğ", "Ü": "ü", "Ö": "ö", "Ç": "ç" };
        string = string.replace(/(([İIŞĞÜÇÖ]))/g, function(letter){ return letters[letter]; })
        return string.toLowerCase();
    }

    String.prototype.englishToLower = function () {
        var letters = {"ç": "c","Ç": "C","ğ": "g","Ğ": "G","ı": "i","I": "I","İ": "I","ö": "o","Ö": "O","ş": "s","Ş": "S","ü": "u","Ü": "U"};
        return this.replace(/[\u00C7\u00E7\u011F\u011E\u0131\u0049\u0130\u00F6\u00D6\u015F\u015E\u00FC\u00DC]/g, function (c) {
            return letters[c] || c;
        });
    };

    if ($(".datepicker")) {
        $(".datepicker").flatpickr({monthSelectorType: 'static'});
    }

    // if (typeof $(".datepicker_range") != undefined) {
    //     $(".datepicker_range").flatpickr({
    //         mode: 'range'
    //     });
    // }

    $(document).ready(function() {
        $(".phone-mask").inputmask({
            mask: "999 999 99 99",
            placeholder: "_",
            showMaskOnHover: false,
            showMaskOnFocus: false,
            clearMaskOnLostFocus: true,
        });
    });
    
    $(document).ready(function() {
        $(".time-mask").inputmask({
            mask: "99:99",
            placeholder: "_",
            showMaskOnHover: false,
            showMaskOnFocus: false,
            clearMaskOnLostFocus: true,
        });
    });

    $('.number-mask').on('keypress', function (e) {
        var charCode = e.which || e.keyCode;

        // Sadece sayısal karakterlere izin ver (0-9)
        if(charCode >= 48 && charCode <= 57) {
            return true;
        } else {
            e.preventDefault();
            return false;
        }
    }).on('input', function () {
        var $input = $(this);
        var content = $input.val();

        // Sadece sayısal karakterlere izin ver
        if(!/^[0-9]*$/.test(content)) {
            $input.val(content.replace(/[^0-9]/g, ''));
        }
    });

    $(document).ready(function() {
        if($(".decimal").length) {
            $(".decimal").inputmask({
                alias: "decimal",
                radixPoint: ",",
                groupSeparator: ".",
                autoGroup: true,
                rightAlign: false
            });
        }
    });
    /*
    $(document).ready(function () {
        $(".price-mask").on("input", function () {
            let value = $(this).val();

            // Sadece rakam ve virgül izin ver
            value = value.replace(/[^0-9,]/g, "");

            // Eğer virgül varsa, sadece ilk virgül kalır
            let parts = value.split(",");
            if (parts.length > 2) {
                value = parts[0] + "," + parts.slice(1).join("");
            }

            // Ondalık kısmı 2 basamak ile sınırla
            if (value.includes(",")) {
                let [whole, decimal] = value.split(",");
                decimal = decimal.substring(0, 2);
                value = whole + "," + decimal;
            }

            // Binlik ayracı ekle
            let [whole, decimal] = value.split(",");
            whole = whole.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

            // Yeniden inputa yaz
            $(this).val(decimal ? whole + "," + decimal : whole);
        });
    });
    */
    const purpleColor = '#836AF9',
    yellowColor = '#ffe800',
    cyanColor = '#28dac6',
    orangeColor = '#FF8132',
    orangeLightColor = '#ffcf5c',
    oceanBlueColor = '#299AFF',
    greyColor = '#4F5D70',
    greyLightColor = '#EDF1F4',
    blueColor = '#2B9AFF',
    blueLightColor = '#84D0FF';

    let cardColor, headingColor, labelColor, borderColor, legendColor;

    if (isDarkStyle) {
        cardColor = config.colors_dark.cardColor;
        headingColor = config.colors_dark.headingColor;
        labelColor = config.colors_dark.textMuted;
        legendColor = config.colors_dark.bodyColor;
        borderColor = config.colors_dark.borderColor;
    } else {
        cardColor = config.colors.cardColor;
        headingColor = config.colors.headingColor;
        labelColor = config.colors.textMuted;
        legendColor = config.colors.bodyColor;
        borderColor = config.colors.borderColor;
    }

    function fncBarChart(labels, data) {
        const barChart = document.getElementById('barChart');
        if (barChart) {
            const maxValue = Math.max(...data);
            const dynamicMax = Math.ceil(maxValue * 1.1);

            const barChartVar = new Chart(barChart, {
              type: 'bar',
              data: {
                labels: labels,
                datasets: [
                  {
                    data: data,
                    backgroundColor: orangeLightColor,
                    borderColor: 'transparent',
                    maxBarThickness: 15,
                    borderRadius: {
                      topRight: 15,
                      topLeft: 15
                    }
                  }
                ]
              },
              options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                  duration: 500
                },
                plugins: {
                  tooltip: {
                    rtl: isRtl,
                    backgroundColor: cardColor,
                    titleColor: headingColor,
                    bodyColor: legendColor,
                    borderWidth: 1,
                    borderColor: borderColor
                  },
                  legend: {
                    display: false
                  }
                },
                scales: {
                  x: {
                    grid: {
                      color: borderColor,
                      drawBorder: false,
                      borderColor: borderColor
                    },
                    ticks: {
                      color: labelColor,
                      maxRotation: 45,
                      minRotation: 45
                    }
                  },
                  y: 
                    {
                        min: 0,
                        max: dynamicMax,
                        grid: {
                            color: borderColor,
                            drawBorder: false,
                            borderColor: borderColor
                        },
                        ticks: {
                            stepSize: Math.ceil(dynamicMax / 4),
                            color: labelColor
                        }
                    }
                }
              }
            });
        }
    }

    $(document).ready(function() {
        $(".mail-invalid").on("input", function() {
            validateEmail($(this));
        });

        function validateEmail($input) {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Geçerli email regex

            if (!emailPattern.test($input.val()) && $input.val() !== "") {
                $input.addClass("is-invalid"); // Bootstrap hata class'ı ekle
                if ($input.next(".invalid-feedback").length === 0) {
                    $input.after('<span class="invalid-feedback d-block text-danger">Geçerli bir e-posta adresi giriniz.</span>');
                }
            } else {
                $input.removeClass("is-invalid").addClass("is-valid"); // Hata class'ını kaldır
                $input.next(".invalid-feedback").remove(); // Hata mesajını kaldır
            }
        }
    });

    $(document).ready(function() {
        $(".kullanici-invalid").on("input", function() {
            validateUsername($(this));
        });

        function validateUsername($input) {
            const usernamePattern = /^[a-z0-9]+$/; // Küçük harf ve rakam kontrolü

            if (!usernamePattern.test($input.val()) && $input.val() !== "") {
                $input.addClass("is-invalid").removeClass("is-valid"); // Hata class'ı ekle
                if ($input.next(".invalid-feedback").length === 0) {
                    $input.after('<span class="invalid-feedback d-block text-danger">Sadece küçük harf ve rakam kullanabilirsiniz. Boşluk ve Türkçe karakterler yasaktır.</span>');
                }
            } else {
                $input.removeClass("is-invalid").addClass("is-valid"); // Başarılı class'ı ekle
                $input.next(".invalid-feedback").remove(); // Hata mesajını kaldır
            }
        }
    });

    $(document).ready(function () {
        if ($(".nav-link").length) {
            let activeTab = sessionStorage.getItem("activeTab");

            if (activeTab && $(activeTab).length > 0) {
                $(".tab-pane").removeClass("show active");
                $(".nav-link").removeClass("active");

                let $activeTabPane = $(activeTab);
                let $activeNavLink = $(`[data-bs-target="${activeTab}"]`);

                if ($activeTabPane.length > 0 && $activeNavLink.length > 0) {
                    $activeTabPane.addClass("show active");
                    $activeNavLink.addClass("active");
                } else {
                    sessionStorage.removeItem("activeTab"); // Geçersiz değerleri temizle
                }
            }

            $(".nav-link").on("click", function () {
                let tabId = $(this).attr("data-bs-target");
                sessionStorage.setItem("activeTab", tabId);
            });
        }
    });

    function sweatAlert(title, confirmButtonText) {
        return Swal.fire({
            title: title,
            icon: 'warning',
            showCancelButton: true,
            cancelButtonText: 'İptal',
            confirmButtonText: confirmButtonText,
            customClass: {
                confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
                cancelButton: 'btn btn-outline-secondary waves-effect'
            },
            buttonsStyling: false
        });
    }

    function showSpinner() {
        $.blockUI({
            message: '<div class="d-flex justify-content-center"><p class="mb-0 me-3">Lütfen Bekleyiniz...</p> <div class="sk-wave m-0"><div class="sk-rect sk-wave-rect"></div> <div class="sk-rect sk-wave-rect"></div> <div class="sk-rect sk-wave-rect"></div> <div class="sk-rect sk-wave-rect"></div> <div class="sk-rect sk-wave-rect"></div></div> </div>',
            timeout: 100000,
            css: {
                backgroundColor: 'transparent',
                color: '#fff',
                border: '0'
            },
            overlayCSS: {
                opacity: 0.5
            }
        });
    }

    function getCheck(selector) {
        let selectedValues = [];
        $(selector + ":checked").each(function() {
            selectedValues.push($(this).val());
        });
        return selectedValues;
    }

    Fancybox.bind(".fancybox", {
        Thumbs: {
            autoStart: true,
        },
    });

    const fullToolbar = [
        [{
                font: []
            },
            {
                size: []
            }
        ],
        ['bold', 'italic', 'underline', 'strike'],
        [{
                color: []
            },
            {
                background: []
            }
        ],
        [{
                script: 'super'
            },
            {
                script: 'sub'
            }
        ],
        [{
                header: '1'
            },
            {
                header: '2'
            },
            'blockquote',
            'code-block'
        ],
        [{
                list: 'ordered'
            },
            {
                list: 'bullet'
            },
            {
                indent: '-1'
            },
            {
                indent: '+1'
            }
        ],
        [{
            direction: 'rtl'
        }],
        ['link', 'image', 'video', 'formula'],
        ['clean']
    ];


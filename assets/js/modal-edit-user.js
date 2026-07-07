/**
 * Edit User
 */

'use strict';

// Select2 (jquery)
$(function () {
  const select2 = $('.select2');
  // Select2 Country
  if (select2.length) {
    select2.each(function () {
      var $this = $(this);
      select2Focus($this);
      $this.wrap('<div class="position-relative"></div>').select2({
        placeholder: 'Seçiniz',
        dropdownParent: $this.parent()
      });
    });
  }
});

document.addEventListener('DOMContentLoaded', function (e) {
  (function () {
    const modalEditUserPhone = document.querySelector('.phone-number-mask');

    // Phone Number Input Mask
    if (modalEditUserPhone) {
      new Cleave(modalEditUserPhone, {
        phone: true,
        phoneRegionCode: 'US'
      });
    }
  })(); // Added closing parentheses for the self-invoking function
});

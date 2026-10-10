/* Tambahan: kirim salinan booking ke server PHP (api/booking.php).
   Tidak mengubah script.js -- handler ini berjalan BERSAMAAN dengan handler
   lama, jadi WhatsApp tetap terbuka seperti biasa. */
(function ($) {
  $(function () {
    $('#bookingForm').on('submit', function () {
      var nama = $.trim($('#nama').val());
      var layanan = $('#pilihLayanan').val();
      if (!nama || !layanan) return; // validasi sama seperti script.js

      $.ajax({
        url: 'api/booking.php',
        method: 'POST',
        dataType: 'json',
        data: {
          nama: nama,
          layanan: layanan,
          perangkat: $.trim($('#perangkat').val()),
          catatan: $.trim($('#catatan').val())
        }
      }).fail(function () {
        console.warn('Booking belum tersimpan di server, tetapi pesan WhatsApp tetap terkirim.');
      });
    });
  });
})(jQuery);

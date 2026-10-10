/* ==========================================================
   DAFTAR HARGA CAHAYA COMPUTER  --  EDIT HARGA DI SINI SAJA
   ----------------------------------------------------------
   Cukup ubah tulisan di dalam tanda kutip, lalu simpan.
   "harga"   = harga yang tampil besar
   "catatan" = keterangan kecil di sebelahnya (boleh dikosongkan '')
   Kalau "harga" dikosongkan (''), website menampilkan "Tanya harga".
   ========================================================== */
var HARGA = {
  // ----- LAYANAN -----
  'layanan-windows':    { harga: 'Rp 50.000 – Rp 100.000',                              catatan: '' },
  'layanan-ssd':        { harga: 'Rp 450.000 – Rp 1.500.000',     catatan: 'Tergantung kapasitas SSD' },
  'layanan-ram':        { harga: 'Rp 150.000 – Rp 950.000',       catatan: 'Tergantung frekuensi dan kapasitas RAM' },
  'layanan-keyboard':   { harga: 'Rp 150.000 – Rp 350.000',       catatan: 'Tergantung jenis keyboard dan ada tidaknya backlight' },
  'layanan-engsel':     { harga: 'Rp 150.000',                    catatan: 'Laptop gaming: Rp 350.000 – Rp 550.000' },
  'layanan-speaker':    { harga: 'Rp 250.000 – Rp 450.000',       catatan: '' },
  'layanan-baterai':    { harga: 'Rp 250.000 – Rp 550.000',       catatan: 'Tergantung tipe laptop' },
  'layanan-pasta':      { harga: 'Rp 100.000 – Rp 200.000',       catatan: 'Tergantung jenis laptop' },
  'layanan-antigores':  { harga: 'Rp 50.000 – Rp 200.000',        catatan: 'Tergantung ukuran, dari yang terkecil sampai terbesar' },
  'layanan-stiker':     { harga: 'Rp 50.000 – Rp 150.000',        catatan: 'Tergantung motif dan jumlah pcs' },

  // ----- PRODUK -----
  'produk-mouse':       { harga: 'Rp 15.000 – Rp 65.000',         catatan: 'Mouse kabel standar Rp 15.000, wireless Rp 65.000' },
  'produk-pelindung':   { harga: 'Rp 10.000 – Rp 25.000',         catatan: 'Tergantung ukuran (inci) laptop' },
  'produk-flashdisk':   { harga: 'Rp 100.000 – Rp 350.000',                              catatan: '' },
  'produk-boxhdd':      { harga: 'Rp 85.000 – Rp 150.000',                              catatan: '' },
  'produk-tas':         { harga: 'Rp 35.000 – Rp 80.000',         catatan: 'Tas standar Rp 35.000, ransel Rp 80.000' },
  'produk-ram':         { harga: 'Rp 150.000 – Rp 950.000',       catatan: 'Tergantung frekuensi dan kapasitas RAM' },
  'produk-ssd':         { harga: 'Rp 450.000 – Rp 1.500.000',     catatan: 'Tergantung kapasitas SSD' },
  'produk-baterai':     { harga: 'Rp 250.000 – Rp 550.000',       catatan: 'Tergantung tipe laptop' },
  'produk-adaptor':     { harga: 'Rp 150.000 – Rp 350.000',                              catatan: '' },
  'produk-antigores':   { harga: 'Rp 50.000 – Rp 200.000',        catatan: 'Tergantung ukuran, dari yang terkecil sampai terbesar' },
  'produk-garskin':     { harga: 'Rp 50.000 – Rp 150.000',        catatan: 'Tergantung motif dan jumlah pcs' }
};


/* ==========================================================
   KODE WEBSITE (tidak perlu diubah)
   ========================================================== */
$(function () {
  // GANTI nomor WhatsApp toko (format internasional, tanpa + atau spasi)
  var WA_NUMBER = '6282211511611';

  $('#year').text(new Date().getFullYear());

  // Isi harga dari harga.js
  if (typeof HARGA !== 'undefined') {
    $('.harga').each(function () { var d = HARGA[$(this).data('key')]; if (d && d.harga) $(this).text(d.harga); });
    $('.harga-note').each(function () { var d = HARGA[$(this).data('key')]; if (d && d.catatan) $(this).text(d.catatan); });
  }

  // Navbar berubah warna saat di-scroll + tombol kembali ke atas
  function onScroll() {
    var y = $(window).scrollTop();
    $('#mainNav').toggleClass('scrolled', y > 30);
    $('#toTop').css('display', y > 600 ? 'grid' : 'none');
  }
  $(window).on('scroll', onScroll);
  onScroll();

  // Tahun di footer sudah diisi di atas.

  // Tutup menu mobile setelah link diklik
  $('#menu .nav-link, #menu .btn').on('click', function () {
    $('#menu').collapse('hide');
  });

  // Pilih layanan otomatis dari link ?layanan=... (dari halaman Layanan/Produk)
  var q = new URLSearchParams(window.location.search).get('layanan');
  if (q && $('#pilihLayanan').length) { $('#pilihLayanan').val(q); }

  // (opsional) Klik "Pesan layanan ini" -> otomatis memilih layanan di form
  $('.pick').on('click', function () {
    $('#pilihLayanan').val($(this).data('service')).removeClass('is-invalid');
  });

  // Kirim form ke WhatsApp
  $('#bookingForm').on('submit', function (e) {
    e.preventDefault();
    var nama = $.trim($('#nama').val());
    var layanan = $('#pilihLayanan').val();
    var perangkat = $.trim($('#perangkat').val());
    var catatan = $.trim($('#catatan').val());

    $('#nama').toggleClass('is-invalid', !nama);
    $('#pilihLayanan').toggleClass('is-invalid', !layanan);
    if (!nama || !layanan) return;

    var pesan = 'Halo Cahaya Computer, saya ingin booking.\n' +
      'Nama: ' + nama + '\n' +
      'Layanan: ' + layanan + '\n' +
      (perangkat ? 'Perangkat: ' + perangkat + '\n' : '') +
      (catatan ? 'Catatan: ' + catatan : '');

    window.open('https://wa.me/' + WA_NUMBER + '?text=' + encodeURIComponent(pesan), '_blank');
  });

  $('#nama, #pilihLayanan').on('input change', function () { $(this).removeClass('is-invalid'); });
});

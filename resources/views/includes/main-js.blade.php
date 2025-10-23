<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
@vite('resources/js/app.js')
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script defer src="https://cdn.datatables.net/v/bs4/jszip-3.10.1/dt-1.13.5/b-2.4.1/b-html5-2.4.1/b-print-2.4.1/sl-1.7.0/datatables.min.js"></script>
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/jquery.perfect-scrollbar/1.4.0/perfect-scrollbar.js"></script>
<script defer src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>

@include('sweetalert::alert')
<script>
(function() {
  const key = 'ob:sidebar:minimized';
  const body = document.body;

  // Persist minimize
  if (localStorage.getItem(key) === '1') body.classList.add('c-sidebar-minimized');
  document.addEventListener('click', function(e) {
    const btn = e.target.closest('.c-sidebar-minimizer');
    if (!btn) return;
    setTimeout(function() {
      localStorage.setItem(key, body.classList.contains('c-sidebar-minimized') ? '1' : '0');
    }, 0);
  });

  // Inisialisasi PerfectScrollbar (OPSIONAL SAJA)
  // Hanya jalan kalau <ul class="c-sidebar-nav" data-perfect-scrollbar> dipasang.
  const nav = document.querySelector('#sidebar .c-sidebar-nav');
  if (nav && nav.hasAttribute('data-perfect-scrollbar') && window.PerfectScrollbar) {
    new PerfectScrollbar(nav, { wheelPropagation: false, suppressScrollX: true });
  }
})();
</script>

@yield('third_party_scripts')

<!-- Dropezone CSS -->
<link rel="stylesheet" href="{{ asset('css/dropzone.css') }}">
<!-- CoreUI CSS -->
@vite('resources/sass/app.scss')
<link href="https://cdn.datatables.net/v/bs4/jszip-3.10.1/dt-1.13.5/b-2.4.1/b-html5-2.4.1/b-print-2.4.1/sl-1.7.0/datatables.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/@coreui/icons@2.0.1/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">

@yield('third_party_stylesheets')

@stack('page_css')


<style>
    div.dataTables_wrapper div.dataTables_length select {
        width: 65px;
        display: inline-block;
    }
    .select2-container--default .select2-selection--single {
        background-color: #fff;
        border: 1px solid #D8DBE0;
        border-radius: 4px;
    }
    .select2-container--default .select2-selection--multiple {
        background-color: #fff;
        border: 1px solid #D8DBE0;
        border-radius: 4px;
    }
    .select2-container .select2-selection--multiple {
        height: 35px;
    }
    .select2-container .select2-selection--single {
        height: 35px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 33px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow b {
        margin-top: 2px;
    }

    /* === Sidebar Polish (Omah Ban) === */
#sidebar.c-sidebar { background-color: #20222b; }
#sidebar .c-sidebar-brand { border-bottom: 1px solid rgba(255,255,255,.06); }

#sidebar .c-sidebar-nav-title {
  text-transform: uppercase;
  font-size: .72rem;
  letter-spacing: .08em;
  opacity: .6;
  padding: .75rem 1rem .25rem;
}

#sidebar .c-sidebar-nav-link,
#sidebar .c-sidebar-nav-dropdown-toggle {
  display: flex;
  align-items: center;
  gap: .5rem;
  border-radius: .5rem;
  margin: 2px 8px;
  padding: .5rem .75rem;
  transition: background-color .15s ease, color .15s ease, box-shadow .15s ease;
}

#sidebar .c-sidebar-nav-link .bi,
#sidebar .c-sidebar-nav-dropdown-toggle .bi {
  font-size: 1rem;
  width: 1.25rem;
  text-align: center;
}

/* Active / current route */
#sidebar .c-sidebar-nav-link.c-active,
#sidebar .c-sidebar-nav-link.active,
#sidebar .c-sidebar-nav-dropdown.c-show > .c-sidebar-nav-dropdown-toggle {
  background: linear-gradient(135deg, #5a67d8, #805ad5);
  color: #fff !important;
  box-shadow: 0 2px 8px rgba(90,103,216,.35);
}

/* Submenu guide line */
#sidebar .c-sidebar-nav-dropdown-items {
  border-left: 1px dashed rgba(255,255,255,.12);
  margin-left: 1.75rem;
}

/* Hover state halus */
#sidebar .c-sidebar-nav-link:hover,
#sidebar .c-sidebar-nav-dropdown-toggle:hover {
  background-color: rgba(255,255,255,.06);
}

/* Unfoldable width on hover (CoreUI) */
.c-sidebar-minimized.c-sidebar-unfoldable:hover .c-sidebar {
  width: 260px;
}

</style>
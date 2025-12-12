<script>
    $(document).ready(function() {
        // Default DataTables Configuration for Flowbite (Enhanced)
        $.extend(true, $.fn.dataTable.defaults, {
            // DOM Layout: 
            // - Mobile: Stacked elements
            // - Desktop: Buttons & Length (Left), Search (Right)
            // - Responsive table in middle
            // - Info & Pagination at bottom
            dom: "<'flex flex-col md:flex-row items-center justify-between p-4 space-y-4 md:space-y-0'<'flex items-center space-x-3 w-full md:w-auto overflow-x-auto pb-1 md:pb-0'lB><'w-full md:w-auto'f>>" +
                 "<'overflow-x-auto relative w-full'tr>" +
                 "<'flex flex-col md:flex-row justify-between items-center p-4 space-y-4 md:space-y-0 border-t border-gray-200 dark:border-gray-700'i p>",
            
            // Auto-save user preferences (sorting, page length) for 7 days
            stateSave: true,
            stateDuration: 60 * 60 * 24 * 7,
            stateSaveCallback: function(settings, data) {
                try {
                    localStorage.setItem('DataTables_' + settings.sInstance, JSON.stringify(data));
                } catch (e) {}
            },
            stateLoadCallback: function(settings) {
                try {
                    return JSON.parse(localStorage.getItem('DataTables_' + settings.sInstance));
                } catch (e) { return null; }
            },

            // Export Buttons Configuration
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="bi bi-file-earmark-excel"></i> Excel',
                    className: 'dt-button',
                    exportOptions: { columns: ':visible:not(.no-export)' }
                },
                {
                    extend: 'pdf',
                    text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
                    className: 'dt-button',
                    orientation: 'landscape',
                    exportOptions: { columns: ':visible:not(.no-export)' }
                },
                {
                    extend: 'print',
                    text: '<i class="bi bi-printer"></i> Print',
                    className: 'dt-button',
                    exportOptions: { columns: ':visible:not(.no-export)' }
                },
                {
                    extend: 'colvis',
                    text: '<i class="bi bi-eye"></i> Kolom',
                    className: 'dt-button'
                }
            ],

            language: {
                search: "",
                searchPlaceholder: "Cari data... (/)",
                lengthMenu: "Tampilkan _MENU_ baris",
                info: "Menampilkan <span class='font-bold text-gray-900 dark:text-white'>_START_</span> - <span class='font-bold text-gray-900 dark:text-white'>_END_</span> dari <span class='font-bold text-gray-900 dark:text-white'>_TOTAL_</span> data",
                infoEmpty: "Menampilkan 0 data",
                infoFiltered: "<span class='text-gray-500'>(disaring dari <span class='font-semibold'>_MAX_</span> total)</span>",
                zeroRecords: "Tidak ada data yang cocok dengan pencarian",
                processing: `<div class="flex items-center gap-3 px-4 py-2">
                    <div class="animate-spin inline-block w-5 h-5 border-2 border-current border-t-transparent text-blue-600 rounded-full" role="status" aria-label="loading">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <span class="text-gray-900 font-semibold dark:text-white">Memproses...</span>
                </div>`,
                emptyTable: `<div class="flex flex-col items-center justify-center py-12 text-center">
                    <div class="p-4 rounded-full bg-blue-50 dark:bg-gray-800 mb-3">
                        <i class="bi bi-inbox-fill text-4xl text-blue-500 dark:text-blue-400"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Belum ada data</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Silakan tambahkan data baru untuk memulai.</p>
                </div>`,
                paginate: {
                    first: '<i class="bi bi-chevron-double-left"></i>',
                    last: '<i class="bi bi-chevron-double-right"></i>',
                    next: '<i class="bi bi-chevron-right"></i>',
                    previous: '<i class="bi bi-chevron-left"></i>'
                },
                buttons: {
                    colvis: 'Kolom'
                }
            },

            initComplete: function () {
                var api = this.api();
                var wrapper = $(api.table().container());

                // 1. Debounced Search (Performance Optimization)
                var searchInput = wrapper.find('.dataTables_filter input');
                var timeout = null;
                
                searchInput.off('keyup.DT input.DT'); // Unbind default events
                searchInput.on('keyup input', function(e) {
                    clearTimeout(timeout);
                    var value = this.value;
                    timeout = setTimeout(function() {
                        api.search(value).draw();
                    }, 300); // 300ms delay
                });

                // 2. Clear Search Button
                if (wrapper.find('.dt-search-clear').length === 0) {
                    var clearBtn = $('<i class="bi bi-x-circle-fill dt-search-clear" title="Clear search"></i>');
                    searchInput.after(clearBtn);
                    
                    clearBtn.on('click', function() {
                        searchInput.val('').trigger('input');
                        api.search('').draw();
                        $(this).removeClass('visible');
                    });

                    searchInput.on('input', function() {
                        if (this.value.length > 0) {
                            clearBtn.addClass('visible');
                        } else {
                            clearBtn.removeClass('visible');
                        }
                    });
                }

                // 3. Accessibility Enhancements
                searchInput.attr('aria-label', 'Search data');
                wrapper.find('.dataTables_length select').attr('aria-label', 'Rows per page');
                
                // 4. Styling injections for non-standard elements
                wrapper.addClass('bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700');
                
                // 5. Keyboard Shortcuts
                $(document).on('keydown', function(e) {
                    // Press '/' to focus search
                    if (e.key === '/' && !$(e.target).is('input, textarea')) {
                        e.preventDefault();
                        searchInput.focus();
                    }
                    // Ctrl + Right Arrow for Next Page
                    if (e.ctrlKey && e.key === 'ArrowRight') {
                        e.preventDefault();
                        api.page('next').draw('page');
                    }
                    // Ctrl + Left Arrow for Prev Page
                    if (e.ctrlKey && e.key === 'ArrowLeft') {
                        e.preventDefault();
                        api.page('previous').draw('page');
                    }
                });
            },

            drawCallback: function () {
                var api = this.api();
                var wrapper = $(api.table().container());
                
                // Ensure pagination buttons have consistent rounded styles
                wrapper.find('.paginate_button').removeClass('dt-button');
                
                // Add scroll hint if table is wider than wrapper
                var scrollBody = wrapper.find('.dataTables_scrollBody');
                if (scrollBody.length && scrollBody[0].scrollWidth > scrollBody.width()) {
                   wrapper.addClass('has-scroll');
                }
            }
        });
    });
</script>

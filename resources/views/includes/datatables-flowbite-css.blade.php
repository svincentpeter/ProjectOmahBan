<style>
    /* 
     * DataTables Styling for Flowbite (Enhanced)
     * Mobile Optimized + Smooth Animations + Modern UI
     */

    /* --- 1. Animation & Transitions --- */
    .dataTables_wrapper * {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* --- 2. Length Menu & Search --- */
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        padding: 0;
        color: #3f3f46; /* zinc-700 */
        margin-bottom: 1rem;
    }

    .dark .dataTables_wrapper .dataTables_length,
    .dark .dataTables_wrapper .dataTables_filter {
        color: #a1a1aa; /* zinc-400 */
    }
    
    .dataTables_wrapper .dataTables_filter {
        position: relative;
    }

    .dataTables_wrapper .dataTables_filter input {
        padding: 0.625rem 1rem 0.625rem 2.5rem; /* Space for search icon */
        border-radius: 0.75rem;
        border: 1px solid #e4e4e7; /* zinc-200 */
        font-size: 0.875rem;
        width: 250px;
        background-color: #ffffff;
        color: #18181b;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%239ca3af' class='bi bi-search' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: 0.75rem center;
        transition: width 0.3s ease;
    }

    .dataTables_wrapper .dataTables_filter input:focus {
        width: 300px;
        outline: none;
        border-color: #6366f1; /* indigo-500 */
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }
    
    /* Search clear button */
    .dt-search-clear {
        position: absolute;
        right: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #9ca3af;
        opacity: 0;
        pointer-events: none;
    }
    .dt-search-clear.visible {
        opacity: 1;
        pointer-events: auto;
    }

    .dark .dataTables_wrapper .dataTables_filter input {
        background-color: #1f2937; /* gray-800 */
        border-color: #374151; /* gray-700 */
        color: #ffffff;
    }

    .dataTables_wrapper .dataTables_length select {
        padding: 0.625rem 2.5rem 0.625rem 1rem;
        border-radius: 0.75rem;
        border: 1px solid #e4e4e7;
        font-size: 0.875rem;
        background-color: #ffffff;
        color: #18181b;
        cursor: pointer;
    }

    .dark .dataTables_wrapper .dataTables_length select {
        background-color: #1f2937;
        border-color: #374151;
        color: #ffffff;
    }

    /* --- 3. Table Styling --- */
    table.dataTable {
        border-collapse: separate;
        border-spacing: 0;
        width: 100% !important;
        margin-top: 0.5rem !important;
        margin-bottom: 0.5rem !important;
    }

    table.dataTable thead th {
        padding: 0.875rem 1.5rem;
        background-color: #f8fafc; /* slate-50 */
        color: #0f172a; /* slate-900 (Black-ish) */
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid #e2e8f0; /* slate-200 */
        white-space: nowrap;
    }

    .dark table.dataTable thead th {
        background-color: #1e293b; /* slate-800 */
        color: #f1f5f9; /* slate-100 */
        border-bottom-color: #334155; /* slate-700 */
        font-weight: 700;
    }

    table.dataTable tbody td {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #f1f5f9; /* slate-100 */
        color: #334155; /* slate-700 */
        vertical-align: middle;
        font-size: 0.875rem;
    }

    .dark table.dataTable tbody td {
        border-bottom-color: #334155; /* slate-700 */
        color: #cbd5e1; /* slate-300 */
    }

    /* Row Hover Effect */
    table.dataTable tbody tr {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    table.dataTable tbody tr:hover {
        background-color: #f1f5f9; /* slate-100 (Slightly darker hover) */
        /* transform: translateY(-2px); Removed to prevent stacking context issues with fixed dropdowns */
        /* box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); */
        /* z-index: 10; */
        /* position: relative; */
    }

    .dark table.dataTable tbody tr:hover {
        background-color: #334155; /* slate-700 */
    }

    /* --- 4. Pagination (Enhanced) --- */
    .dataTables_wrapper .dataTables_paginate {
        padding-top: 1rem;
        display: flex !important;
        justify-content: flex-end !important;
        align-items: center !important;
        gap: 0.25rem;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        border: 1px solid #e4e4e7;
        background: white;
        color: #3f3f46 !important;
        cursor: pointer;
        font-size: 0.875rem;
        font-weight: 500;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        text-decoration: none !important;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled) {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        background: #f4f4f5;
        border-color: #d4d4d8;
        color: #18181b !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #4f46e5 !important;
        color: white !important;
        border-color: #4f46e5 !important;
        box-shadow: 0 4px 6px rgba(79, 70, 229, 0.2);
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
        opacity: 0.5;
        cursor: not-allowed;
        box-shadow: none;
    }

    /* Dark Mode Pagination */
    .dark .dataTables_wrapper .dataTables_paginate .paginate_button {
        background: #1f2937;
        border-color: #374151;
        color: #9ca3af !important;
    }

    .dark .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled) {
        background: #374151;
        border-color: #4b5563;
        color: white !important;
    }

    .dark .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #6366f1 !important; /* indigo-500 */
        border-color: #6366f1 !important;
        color: white !important;
    }

    /* --- 5. Export Buttons --- */
    .dt-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .dt-button {
        padding: 0.625rem 1rem;
        background: white;
        border: 1px solid #e4e4e7;
        border-radius: 0.75rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: #3f3f46;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .dt-button:hover {
        background: #f8fafc;
        border-color: #d4d4d8;
        color: #18181b;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .dark .dt-button {
        background: #1f2937;
        border-color: #374151;
        color: #d1d5db;
    }

    .dark .dt-button:hover {
        background: #374151;
        border-color: #6b7280;
        color: white;
    }

    /* --- 6. Mobile Optimization --- */
    @media (max-width: 640px) {
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            width: 100% !important;
            text-align: left;
        }
        
        .dataTables_wrapper .dataTables_filter input {
            width: 100%;
            margin-left: 0;
            padding-right: 2rem;
        }
        
        .dataTables_wrapper .dataTables_filter input:focus {
            width: 100%;
        }

        .dataTables_wrapper .dataTables_paginate {
            justify-content: center !important;
            flex-wrap: wrap;
        }
        
        /* Larger touch targets */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.75rem 1rem;
            min-width: 44px;
            min-height: 44px;
        }

        .dt-buttons {
            width: 100%;
            overflow-x: auto;
            padding-bottom: 0.5rem;
        }
    }

    /* --- 7. Loading & Empty States --- */
    /* Shimmer Effect for Loading */
    @keyframes shimmer {
        0% { background-position: -1000px 0; }
        100% { background-position: 1000px 0; }
    }
    
    .dataTables_processing {
        position: absolute !important;
        top: 50% !important;
        left: 50% !important;
        transform: translate(-50%, -50%) !important;
        z-index: 100 !important;
        background: white;
        padding: 0.5rem;
        border-radius: 9999px;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        border: 1px solid #e5e7eb;
        min-width: max-content;
        margin: 0 !important; /* Reset default margins */
        height: auto !important; /* Reset default height */
    }

    .dark .dataTables_processing {
        background: #1f2937;
        border: 1px solid #374151;
    }

    /* Empty State */
    .dataTables_empty {
        text-align: center;
        padding: 3rem 1rem !important;
        color: #64748b;
    }
    
    /* Scroll Hint */
    .table-scroll-hint {
        position: absolute;
        right: 0;
        top: 50%;
        transform: translateY(-50%);
        background: linear-gradient(to left, rgba(255,255,255,0.9), transparent);
        padding: 2rem 1rem;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.3s;
    }
    
    .table-responsive:hover .table-scroll-hint {
        opacity: 0; /* Hide on interaction */
    }
</style>

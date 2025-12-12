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
        padding: 0.625rem 1rem 0.625rem 2.5rem;
        border-radius: 0.5rem;
        border: 1px solid #d1d5db;
        font-size: 0.875rem;
        width: 250px;
        background-color: #f9fafb;
        color: #111827;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%239ca3af' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: 0.75rem center;
        transition: width 0.3s ease, border-color 0.2s;
    }

    .dataTables_wrapper .dataTables_filter input:focus {
        width: 300px;
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        background-color: #ffffff;
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
        background-color: #374151;
        border-color: #4b5563;
        color: #ffffff;
    }

    .dataTables_wrapper .dataTables_length select {
        padding: 0.625rem 2.5rem 0.625rem 1rem;
        border-radius: 0.5rem;
        border: 1px solid #d1d5db;
        font-size: 0.875rem;
        background-color: #ffffff;
        color: #111827;
        cursor: pointer;
    }

    .dark .dataTables_wrapper .dataTables_length select {
        background-color: #374151;
        border-color: #4b5563;
        color: #ffffff;
    }

    /* --- 3. Table Styling --- */
    table.dataTable {
        border-collapse: separate;
        border-spacing: 0;
        width: 100% !important;
        margin-top: 0 !important;
        margin-bottom: 0 !important;
    }

    table.dataTable thead th {
        padding: 0.75rem 1rem;
        background-color: #f9fafb;
        color: #111827;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        border-bottom: 1px solid #e5e7eb;
        white-space: nowrap;
    }

    .dark table.dataTable thead th {
        background-color: #374151;
        color: #d1d5db;
        border-bottom-color: #4b5563;
    }

    table.dataTable tbody td {
        padding: 1rem;
        border-bottom: 1px solid #e5e7eb;
        color: #6b7280;
        vertical-align: middle;
        font-size: 0.875rem;
    }

    .dark table.dataTable tbody td {
        border-bottom-color: #374151;
        color: #d1d5db;
    }

    /* Row Hover Effect */
    table.dataTable tbody tr {
        background-color: white;
        transition: background-color 0.15s;
    }

    table.dataTable tbody tr:hover {
        background-color: #f9fafb;
    }

    .dark table.dataTable tbody tr {
        background-color: #1f2937;
    }

    .dark table.dataTable tbody tr:hover {
        background-color: #374151;
    }

    /* --- 4. Pagination --- */
    .dataTables_wrapper .dataTables_paginate {
        padding-top: 1rem;
        display: flex !important;
        justify-content: flex-end !important;
        align-items: center !important;
        gap: 0.25rem;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.5rem 0.875rem;
        border-radius: 0.5rem;
        border: 1px solid #d1d5db;
        background: white;
        color: #374151 !important;
        cursor: pointer;
        font-size: 0.875rem;
        font-weight: 500;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        text-decoration: none !important;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled):not(.current) {
        background: #f3f4f6;
        border-color: #d1d5db;
        color: #111827 !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #3b82f6 !important;
        color: white !important;
        border-color: #3b82f6 !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Dark Mode Pagination */
    .dark .dataTables_wrapper .dataTables_paginate .paginate_button {
        background: #374151;
        border-color: #4b5563;
        color: #d1d5db !important;
    }

    .dark .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled):not(.current) {
        background: #4b5563;
        color: white !important;
    }

    .dark .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #3b82f6 !important;
        border-color: #3b82f6 !important;
        color: white !important;
    }

    /* --- 5. Export Buttons --- */
    .dt-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .dt-button {
        padding: 0.5rem 1rem;
        background: white;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .dt-button:hover {
        background: #f3f4f6;
        border-color: #9ca3af;
        color: #111827;
    }
    
    .dark .dt-button {
        background: #374151;
        border-color: #4b5563;
        color: #d1d5db;
    }

    .dark .dt-button:hover {
        background: #4b5563;
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
        }
        
        .dataTables_wrapper .dataTables_filter input:focus {
            width: 100%;
        }

        .dataTables_wrapper .dataTables_paginate {
            justify-content: center !important;
            flex-wrap: wrap;
        }
        
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
    .dataTables_processing {
        position: absolute !important;
        top: 50% !important;
        left: 50% !important;
        transform: translate(-50%, -50%) !important;
        z-index: 100 !important;
        background: white;
        padding: 1rem 1.5rem;
        border-radius: 0.75rem;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        border: 1px solid #e5e7eb;
    }

    .dark .dataTables_processing {
        background: #1f2937;
        border-color: #374151;
    }

    .dataTables_empty {
        text-align: center;
        padding: 3rem 1rem !important;
        color: #6b7280;
    }
</style>

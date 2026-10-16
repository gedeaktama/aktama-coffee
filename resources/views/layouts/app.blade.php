<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Kopi Distro - Sistem Distribusi & Penjualan Kopi Kemasan')</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --coffee-dark: #1e130c;
            --coffee-espresso: #2b1c12;
            --coffee-roast: #3e271a;
            --coffee-caramel: #b86b35;
            --coffee-amber: #e09f58;
            --coffee-cream: #f9f7f4;
            --coffee-surface: #ffffff;
            --coffee-border: #ede4dc;
            
            /* Admin Theme Palette (Slate & Amber) */
            --admin-sidebar-bg: #18181b;
            --admin-sidebar-dark: #09090b;
            --admin-sidebar-hover: #27272a;
            --admin-sidebar-text: #d4d4d8;
            --admin-sidebar-muted: #a1a1aa;
            --admin-accent: #f59e0b;
            --admin-accent-dark: #b45309;
            --admin-body-bg: #f8fafc;
            
            --sidebar-width: 260px;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--admin-body-bg);
            color: #1e293b;
            min-height: 100vh;
        }

        /* ========================================================
           ADMIN / ERP LAYOUT (MENU SEBELAH KIRI - MODERN ENTERPRISE)
           ======================================================== */
        .admin-layout {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* Sidebar Styling - Enforce Deep Dark Slate */
        .admin-sidebar,
        .admin-sidebar.offcanvas,
        .admin-sidebar.offcanvas-lg,
        .offcanvas-lg.admin-sidebar {
            width: var(--sidebar-width) !important;
            background-color: var(--admin-sidebar-bg) !important;
            --bs-offcanvas-bg: var(--admin-sidebar-bg) !important;
            color: #ffffff !important;
            display: flex;
            flex-direction: column;
            border-right: 1px solid rgba(255, 255, 255, 0.08) !important;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.25) !important;
            z-index: 1040;
        }

        @media (min-width: 992px) {
            .admin-sidebar {
                position: fixed !important;
                top: 0 !important;
                bottom: 0 !important;
                left: 0 !important;
                height: 100vh !important;
                transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), margin-left 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
            }
            .admin-main-wrapper {
                margin-left: var(--sidebar-width);
                width: calc(100% - var(--sidebar-width));
                transition: margin-left 0.3s cubic-bezier(0.16, 1, 0.3, 1), width 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
            }

            /* Mode Full Canvas (Ketika Sidebar Ditutup / Collapsed di Desktop) */
            .admin-layout.sidebar-collapsed .admin-sidebar {
                transform: translateX(-100%) !important;
            }
            .admin-layout.sidebar-collapsed .admin-main-wrapper {
                margin-left: 0 !important;
                width: 100% !important;
            }
        }

        @media (max-width: 991.98px) {
            .admin-sidebar {
                position: fixed !important;
                top: 0 !important;
                bottom: 0 !important;
                left: 0 !important;
                width: 280px !important;
                max-width: 85vw !important;
                height: 100vh !important;
                transform: translateX(-100%) !important;
                visibility: hidden !important;
                transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.3s ease !important;
                z-index: 1055 !important;
            }
            .admin-sidebar.show {
                transform: translateX(0) !important;
                visibility: visible !important;
            }
            .admin-main-wrapper {
                margin-left: 0 !important;
                width: 100% !important;
                min-width: 100% !important;
            }
            .admin-topbar {
                height: 56px !important;
                padding-left: 0.75rem !important;
                padding-right: 0.75rem !important;
            }
        }

        /* Pastikan Seluruh Konten Halaman Admin Mengisi Canvas 100% Full-Width */
        .admin-main-wrapper .container,
        .admin-main-wrapper .container-fluid {
            max-width: 100% !important;
            width: 100% !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        .sidebar-brand-box {
            background-color: var(--admin-sidebar-dark) !important;
            padding: 1.15rem 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .sidebar-user-card {
            background-color: #202024 !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            padding: 0.9rem 1.25rem;
        }

        .sidebar-nav-scroll {
            padding: 0.85rem 0.75rem;
            overflow-y: auto;
            flex-grow: 1;
            background-color: var(--admin-sidebar-bg) !important;
        }

        .sidebar-nav-scroll::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar-nav-scroll::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 4px;
        }

        /* ========================================================
           SIDEBAR QUICK SEARCH & ACCORDION DROPDOWN STYLES
           ======================================================== */
        .sidebar-search-container {
            padding: 0.65rem 0.75rem 0.45rem;
            background-color: rgba(9, 9, 11, 0.75) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            flex-shrink: 0;
            transition: all 0.25s ease;
        }

        .sidebar-search-bar {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .sidebar-search-icon {
            position: absolute;
            left: 0.65rem;
            color: var(--admin-sidebar-muted);
            font-size: 0.8rem;
            pointer-events: none;
            z-index: 1;
        }

        .sidebar-search-input {
            width: 100%;
            background-color: #202024;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 0.4rem 1.6rem 0.4rem 2rem;
            font-size: 0.76rem;
            color: #ffffff;
            transition: all 0.2s ease;
            outline: none;
            font-family: inherit;
        }

        .sidebar-search-input:focus {
            border-color: var(--admin-accent);
            box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.25);
            background-color: #27272a;
        }

        .sidebar-search-input::placeholder {
            color: var(--admin-sidebar-muted);
            font-size: 0.74rem;
        }

        .sidebar-search-clear {
            position: absolute;
            right: 0.45rem;
            background: transparent;
            border: none;
            color: var(--admin-sidebar-muted);
            cursor: pointer;
            font-size: 0.72rem;
            padding: 2px 5px;
            border-radius: 4px;
            line-height: 1;
            transition: color 0.15s ease;
        }

        .sidebar-search-clear:hover {
            color: #ffffff;
        }

        .sidebar-quick-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 0.35rem;
            padding: 0 0.15rem;
        }

        .sidebar-role-indicator {
            font-size: 0.63rem;
            color: var(--admin-sidebar-muted);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 130px;
        }

        .sidebar-toggle-all-btn {
            background: transparent;
            border: none;
            color: var(--admin-sidebar-muted);
            font-size: 0.68rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.3rem;
            padding: 2px 6px;
            border-radius: 4px;
            transition: all 0.18s ease;
            font-family: inherit;
        }

        .sidebar-toggle-all-btn:hover {
            color: #ffffff;
            background-color: var(--admin-sidebar-hover);
        }

        /* ─── SIDEBAR ACCORDION GROUP & TITLES ─── */
        .sidebar-nav-group-wrapper {
            list-style: none;
            margin-bottom: 0.25rem;
            position: relative;
            transition: margin 0.2s ease;
        }

        .sidebar-group-title {
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: var(--admin-sidebar-muted) !important;
            padding: 0.48rem 0.65rem;
            margin: 0.15rem 0;
            border-radius: 8px;
            white-space: nowrap;
            overflow: hidden;
            cursor: pointer;
            user-select: none;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.4rem;
            transition: all 0.18s ease;
            border: 1px solid transparent;
        }

        .sidebar-group-title:hover {
            color: #ffffff !important;
            background-color: var(--admin-sidebar-hover) !important;
        }

        .sidebar-group-title:focus-visible {
            outline: 2px solid var(--admin-accent);
            outline-offset: -1px;
        }

        .sidebar-nav-group-wrapper.has-active > .sidebar-group-title {
            color: #ffffff !important;
            font-weight: 800;
            background: rgba(245, 158, 11, 0.12) !important;
            border-left: 3px solid var(--admin-accent);
            border-radius: 4px 8px 8px 4px;
        }

        .sidebar-group-title-left {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            flex: 1;
        }

        .sidebar-group-title-right {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            flex-shrink: 0;
        }

        .sidebar-group-count {
            font-size: 0.62rem;
            font-weight: 700;
            font-family: inherit;
            color: var(--admin-sidebar-muted);
            background: rgba(255, 255, 255, 0.08);
            padding: 1px 5px;
            border-radius: 6px;
            line-height: 1.2;
            transition: all 0.18s ease;
        }

        .sidebar-nav-group-wrapper.has-active .sidebar-group-count {
            color: var(--admin-accent);
            background: rgba(245, 158, 11, 0.2);
        }

        .sidebar-group-chevron {
            font-size: 0.72rem;
            transition: transform 0.22s cubic-bezier(0.4, 0, 0.2, 1), color 0.18s ease;
            color: var(--admin-sidebar-muted);
            display: inline-block;
            flex-shrink: 0;
        }

        .sidebar-nav-group-wrapper.is-collapsed .sidebar-group-chevron {
            transform: rotate(-90deg);
        }

        .sidebar-nav-group-wrapper:not(.is-collapsed) .sidebar-group-chevron {
            transform: rotate(0deg);
        }

        .sidebar-group-title:hover .sidebar-group-chevron {
            color: #ffffff;
        }

        .sidebar-subnav-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            padding-left: 0;
            margin-bottom: 0;
            gap: 2px;
            overflow: hidden;
            max-height: 1200px;
            opacity: 1;
            transition: max-height 0.25s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.2s ease, margin 0.2s ease;
        }

        .sidebar-nav-group-wrapper.is-collapsed .sidebar-subnav-list {
            max-height: 0 !important;
            opacity: 0 !important;
            pointer-events: none !important;
            margin-top: 0 !important;
            margin-bottom: 0 !important;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
        }

        /* Empty search state */
        .sidebar-no-results {
            padding: 1.25rem 0.75rem;
            text-align: center;
            color: var(--admin-sidebar-muted);
            font-size: 0.78rem;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 8px;
            margin: 0.5rem 0;
            border: 1px dashed rgba(255, 255, 255, 0.12);
        }

        .sidebar-no-results button {
            margin-top: 0.5rem;
            padding: 0.3rem 0.65rem;
            border-radius: 6px;
            background: var(--admin-sidebar-hover);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #ffffff;
            font-size: 0.72rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .sidebar-no-results button:hover {
            background: var(--admin-accent);
            color: #000000;
            border-color: transparent;
        }

        .sidebar-item-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.55rem 0.75rem;
            border-radius: 8px;
            color: var(--admin-sidebar-text) !important;
            text-decoration: none;
            font-size: 0.86rem;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
            margin-bottom: 2px;
        }

        .sidebar-item-link i {
            font-size: 1.05rem;
            width: 20px;
            text-align: center;
            color: var(--admin-accent);
            transition: transform 0.2s;
        }

        .sidebar-item-link:hover {
            color: #ffffff !important;
            background-color: var(--admin-sidebar-hover) !important;
            transform: translateX(3px);
        }

        .sidebar-item-link:hover i {
            transform: scale(1.15);
        }

        .sidebar-item-link.active {
            color: #ffffff !important;
            background: linear-gradient(90deg, #92400e 0%, var(--admin-accent-dark) 100%) !important;
            font-weight: 600;
            border-left: 3px solid var(--admin-accent);
            box-shadow: 0 4px 14px rgba(180, 83, 9, 0.35);
        }

        .sidebar-item-link.active i {
            color: #ffffff;
        }

        .sidebar-bottom-bar {
            background-color: var(--admin-sidebar-dark) !important;
            padding: 0.85rem 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        /* Admin Main Content Wrapper */
        .admin-main-wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: var(--admin-body-bg);
            transition: all 0.3s ease;
        }

        /* Admin Topbar Header */
        .admin-topbar {
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
            z-index: 1020;
            height: 64px;
        }

        /* ========================================================
           STOREFRONT / PUBLIC LAYOUT (TOPBAR HEADLINE - NO SIDEBAR)
           ======================================================== */
        .store-navbar {
            background-color: var(--coffee-dark);
            box-shadow: 0 4px 20px rgba(30, 19, 12, 0.2);
            padding: 0.75rem 0;
        }

        .store-navbar .nav-link {
            color: rgba(255, 255, 255, 0.85);
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: color 0.2s;
        }

        .store-navbar .nav-link:hover,
        .store-navbar .nav-link.active {
            color: var(--coffee-amber);
        }

        /* ========================================================
           SHARED COMPONENTS & BUTTONS
           ======================================================== */
        .btn-coffee {
            background-color: var(--coffee-caramel);
            color: #ffffff;
            border: none;
            font-weight: 600;
            padding: 0.55rem 1.25rem;
            border-radius: 8px;
            transition: all 0.2s ease-in-out;
        }

        .btn-coffee:hover {
            background-color: var(--coffee-roast);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(62, 39, 26, 0.25);
        }

        .btn-outline-coffee {
            background-color: transparent;
            color: var(--coffee-amber);
            border: 1.5px solid var(--coffee-amber);
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.2s ease-in-out;
        }

        .btn-outline-coffee:hover {
            background-color: var(--coffee-amber);
            color: var(--coffee-dark);
        }

        .card-custom {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card-custom:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.07);
        }

        /* Role Badges */
        .badge-role {
            font-size: 0.72rem;
            font-weight: 700;
            padding: 0.3rem 0.6rem;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-role-owner { background-color: #7b1fa2; color: #fff; }
        .badge-role-admin { background-color: #1976d2; color: #fff; }
        .badge-role-sales { background-color: #2e7d32; color: #fff; }
        .badge-role-warehouse { background-color: #ea580c; color: #fff; }
        .badge-role-finance { background-color: #0891b2; color: #fff; }
        .badge-role-driver { background-color: #d97706; color: #fff; }
        .badge-role-customer { background-color: #64748b; color: #fff; }

        /* ========================================================
           KOPI SEARCH SELECT (SMART SEARCHABLE COMBOBOX)
           ======================================================== */
        .kopi-search-select-wrapper {
            position: relative;
            width: 100%;
        }

        .kopi-search-select-wrapper.is-hidden-native select {
            position: absolute !important;
            opacity: 0 !important;
            width: 1px !important;
            height: 1px !important;
            margin: -1px !important;
            padding: 0 !important;
            overflow: hidden !important;
            clip: rect(0, 0, 0, 0) !important;
            border: 0 !important;
            pointer-events: none !important;
        }

        .kopi-select-trigger {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            min-height: 40px;
            padding: 0.45rem 0.85rem;
            font-size: 0.875rem;
            color: #1e293b;
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            cursor: pointer;
            user-select: none;
            transition: all 0.15s ease-in-out;
        }

        .kopi-select-trigger:hover {
            border-color: #94a3b8;
            background-color: #f8fafc;
        }

        .kopi-select-trigger.is-open {
            border-color: var(--admin-accent, #f59e0b);
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2);
            background-color: #ffffff;
        }

        .kopi-select-trigger.is-invalid {
            border-color: #ef4444 !important;
            background-color: #fef2f2 !important;
        }

        .kopi-select-trigger.is-disabled {
            background-color: #f1f5f9;
            border-color: #e2e8f0;
            color: #94a3b8;
            cursor: not-allowed;
            pointer-events: none;
        }

        .kopi-select-value {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-grow: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .kopi-select-placeholder {
            color: #94a3b8;
            font-style: normal;
        }

        .kopi-select-actions {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            margin-left: 0.5rem;
        }

        .kopi-select-clear {
            display: none;
            cursor: pointer;
            color: #94a3b8;
            padding: 2px 4px;
            border-radius: 4px;
            font-size: 0.8rem;
            line-height: 1;
            transition: all 0.15s ease;
        }

        .kopi-select-trigger:hover .kopi-select-clear.is-visible {
            display: inline-block;
        }

        .kopi-select-clear:hover {
            color: #ef4444;
            background: #fee2e2;
        }

        .kopi-select-chevron {
            color: #64748b;
            font-size: 0.75rem;
            transition: transform 0.2s ease;
        }

        .kopi-select-trigger.is-open .kopi-select-chevron {
            transform: rotate(180deg);
        }

        /* Floating Dropdown Panel */
        .kopi-select-dropdown {
            position: fixed;
            z-index: 10050;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            display: none;
            flex-direction: column;
            overflow: hidden;
            animation: kopiSelectPop 0.15s ease-out;
        }

        @keyframes kopiSelectPop {
            from { opacity: 0; transform: translateY(-3px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .kopi-select-dropdown.is-open {
            display: flex;
        }

        .kopi-select-search-box {
            padding: 0.5rem 0.65rem;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .kopi-select-search-input {
            width: 100%;
            padding: 0.4rem 0.65rem;
            font-size: 0.85rem;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            outline: none;
            background: #ffffff;
        }

        .kopi-select-search-input:focus {
            border-color: var(--admin-accent, #f59e0b);
            box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.2);
        }

        .kopi-select-options-list {
            max-height: 270px;
            overflow-y: auto;
            padding: 0.35rem;
            margin: 0;
            list-style: none;
        }

        .kopi-select-group-header {
            padding: 0.4rem 0.65rem;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #92400e;
            background: #fef3c7;
            border-radius: 5px;
            margin: 0.35rem 0 0.2rem 0;
        }

        .kopi-select-option {
            padding: 0.45rem 0.65rem;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.85rem;
            color: #1e293b;
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
            transition: background 0.12s ease;
            margin-bottom: 2px;
        }

        .kopi-select-option:hover,
        .kopi-select-option.is-highlighted {
            background-color: #fffbeb;
            color: #92400e;
        }

        .kopi-select-option.is-selected {
            background-color: #fef3c7;
            color: #78350f;
            font-weight: 600;
        }

        .kopi-select-option-main {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
        }

        .kopi-select-option-sub {
            font-size: 0.75rem;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            flex-wrap: wrap;
        }

        .kopi-select-match-highlight {
            background-color: #fde68a;
            color: #78350f;
            font-weight: 700;
            padding: 0 2px;
            border-radius: 2px;
        }

        .kopi-select-empty {
            padding: 1.5rem 0.75rem;
            text-align: center;
            color: #94a3b8;
            font-size: 0.85rem;
        }

        /* Quick Catalog Modal & Cards */
        .variant-pick-card {
            border-radius: 10px;
            transition: all 0.2s ease;
            background: #ffffff;
        }
        .variant-pick-card:hover {
            border-color: #f59e0b !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(245, 158, 11, 0.12) !important;
        }
        .filter-chip {
            border-radius: 20px;
            padding: 0.3rem 0.8rem;
            font-size: 0.8rem;
            font-weight: 500;
            transition: all 0.15s ease;
        }

        /* Print Media Styles */
        @media print {
            .admin-sidebar,
            .admin-topbar,
            .store-navbar,
            footer,
            .no-print,
            .btn,
            button {
                display: none !important;
            }
            .admin-main-wrapper {
                margin-left: 0 !important;
                width: 100% !important;
                background-color: #ffffff !important;
            }
            body {
                background-color: #ffffff !important;
            }
            .container, .container-fluid {
                max-width: 100% !important;
                padding: 0 !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

@php
    // Cek apakah halaman yang dibuka adalah modul internal backend / dashboard ERP
    $isAdminMode = request()->is('dashboard*') || 
                   request()->is('master*') || 
                   request()->is('inventory*') || 
                   request()->is('sales*') || 
                   request()->is('logistics*') || 
                   request()->is('finance*') || 
                   request()->is('reports*') ||
                   request()->is('settings*') ||
                   request()->is('driver*') ||
                   request()->is('maps*') ||
                   request()->is('consignments*') ||
                   request()->is('my-orders*');
@endphp

@if($isAdminMode)
    <!-- ============================================================== -->
    <!-- MODE 1: ADMIN & ERP MANAGEMENT (LEFT SIDEBAR NAVIGATION LAYOUT) -->
    <!-- ============================================================== -->
    <div class="admin-layout">
        <!-- SIDEBAR SEBELAH KIRI (FIXED DESKTOP / OFFCANVAS MOBILE) -->
        <aside class="admin-sidebar offcanvas-lg offcanvas-start" id="sidebarOffcanvas" tabindex="-1" aria-labelledby="sidebarOffcanvasLabel">
            <!-- Sidebar Header Brand -->
            <div class="sidebar-brand-box d-flex align-items-center justify-content-between">
                <a class="d-flex align-items-center gap-2 text-decoration-none text-white" href="{{ route('dashboard') }}">
                    <div class="bg-warning text-dark rounded-3 p-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 36px; height: 36px;">
                        <i class="bi bi-cup-hot-fill fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-white fs-5 lh-1">Kopi Distro</div>
                        <small class="text-white-50" style="font-size: 0.68rem;">ERP &amp; Bisnis Distribusi</small>
                    </div>
                </a>
                <!-- Tombol Tutup Khusus Mobile -->
                <button type="button" class="btn-close btn-close-white d-lg-none" data-bs-dismiss="offcanvas" data-bs-target="#sidebarOffcanvas" aria-label="Close"></button>
            </div>

            <!-- Kartu Profil Pengguna di Sidebar -->
            @auth
                <div class="sidebar-user-card">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-warning bg-opacity-25 text-warning border border-warning border-opacity-50 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px;">
                            <i class="bi bi-person-fill fs-5"></i>
                        </div>
                        <div class="overflow-hidden">
                            <div class="fw-bold text-white text-truncate" style="font-size: 0.88rem;">{{ auth()->user()->name }}</div>
                            <span class="badge badge-role badge-role-{{ auth()->user()->role }}" style="font-size: 0.65rem;">
                                {{ strtoupper(auth()->user()->role) }}
                            </span>
                        </div>
                    </div>
                </div>
            @endauth

            <!-- Sidebar Quick Search & Accordion Controls -->
            <div class="sidebar-search-container" id="sidebarSearchContainer">
                <div class="sidebar-search-bar">
                    <i class="bi bi-search sidebar-search-icon"></i>
                    <input type="text" id="sidebarMenuSearch" class="sidebar-search-input" placeholder="Cari menu ERP cepat..." autocomplete="off" spellcheck="false" title="Ketik untuk mencari menu cepat">
                    <button type="button" id="sidebarSearchClear" class="sidebar-search-clear" title="Bersihkan pencarian" style="display:none;">✕</button>
                </div>
                <div class="sidebar-quick-actions">
                    <span class="sidebar-role-indicator">
                        @auth
                            {{ strtoupper(auth()->user()->role ?? 'ERP') }}
                        @else
                            MENU ERP
                        @endauth
                    </span>
                    <button type="button" id="sidebarToggleAllBtn" class="sidebar-toggle-all-btn" title="Buka / Ciutkan Semua Kategori">
                        <span id="sidebarToggleAllIcon">⊟</span>
                        <span id="sidebarToggleAllText">Ciutkan</span>
                    </button>
                </div>
            </div>

            <!-- Navigasi Menu Vertikal (Scrollable) -->
            <div class="sidebar-nav-scroll" id="sidebarNavScroll">
                <ul class="nav flex-column" id="kopiSidebarNav">
                    <!-- LINK CEPAT KE TOKO RETAIL -->
                    <li class="sidebar-group-title">Akses Cepat</li>
                    <li class="nav-item">
                        <a class="sidebar-item-link" href="{{ route('home') }}" target="_blank">
                            <i class="bi bi-box-arrow-up-right"></i>
                            <span>Lihat Web Toko</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="sidebar-item-link {{ request()->routeIs('store.catalog') ? 'active' : '' }}" href="{{ route('store.catalog') }}">
                            <i class="bi bi-shop"></i>
                            <span>Katalog Kopi Ritel</span>
                        </a>
                    </li>

                    @auth
                        @if(auth()->user()->isCustomer())
                            <li class="sidebar-group-title">Akun Pelanggan</li>
                            <li class="nav-item">
                                <a class="sidebar-item-link {{ request()->routeIs('store.my_orders') ? 'active' : '' }}" href="{{ route('store.my_orders') }}">
                                    <i class="bi bi-bag-check"></i>
                                    <span>Pesanan Saya</span>
                                </a>
                            </li>
                        @endif

                        @if(auth()->user()->canAccessDashboard())
                            <li class="sidebar-group-title">Dashboard Utama</li>
                            <li class="nav-item">
                                <a class="sidebar-item-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                    <i class="bi bi-speedometer2"></i>
                                    <span>Dashboard ERP</span>
                                </a>
                            </li>

                            <!-- MASTER DATA -->
                            <li class="sidebar-group-title">Master Data</li>
                            @if(auth()->user()->hasRole(['owner', 'admin']))
                                <li class="nav-item">
                                    <a class="sidebar-item-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                                        <i class="bi bi-tags"></i>
                                        <span>Kategori Produk</span>
                                    </a>
                                </li>
                            @endif
                            <li class="nav-item">
                                <a class="sidebar-item-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                                    <i class="bi bi-box-seam"></i>
                                    <span>Produk &amp; Varian SKU</span>
                                </a>
                            </li>
                            @if(auth()->user()->hasRole(['owner', 'admin', 'warehouse']))
                                <li class="nav-item">
                                    <a class="sidebar-item-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}" href="{{ route('suppliers.index') }}">
                                        <i class="bi bi-truck-front"></i>
                                        <span>Pemasok (Supplier)</span>
                                    </a>
                                </li>
                            @endif
                            @if(auth()->user()->hasRole(['owner', 'admin', 'sales', 'finance']))
                                <li class="nav-item">
                                    <a class="sidebar-item-link {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}">
                                        <i class="bi bi-buildings"></i>
                                        <span>Pelanggan B2B</span>
                                    </a>
                                </li>
                            @endif

                            <!-- GUDANG & PERSEDIAAN -->
                            @if(auth()->user()->hasRole(['owner', 'admin', 'warehouse', 'sales', 'finance']))
                                <li class="sidebar-group-title">Gudang &amp; Persediaan</li>
                                <li class="nav-item">
                                    <a class="sidebar-item-link {{ request()->routeIs('inventory.index') ? 'active' : '' }}" href="{{ route('inventory.index') }}">
                                        <i class="bi bi-boxes"></i>
                                        <span>Monitoring Stok &amp; Alert</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="sidebar-item-link {{ request()->routeIs('inventory.movements') ? 'active' : '' }}" href="{{ route('inventory.movements') }}">
                                        <i class="bi bi-journal-text"></i>
                                        <span>Kartu Mutasi Stok</span>
                                    </a>
                                </li>
                                @if(auth()->user()->hasRole(['owner', 'admin', 'warehouse', 'finance']))
                                    <li class="nav-item">
                                        <a class="sidebar-item-link {{ request()->routeIs('purchases.*') ? 'active' : '' }}" href="{{ route('purchases.index') }}">
                                            <i class="bi bi-cart-check"></i>
                                            <span>Pembelian Supplier (PO)</span>
                                        </a>
                                    </li>
                                @endif
                            @endif

                            <!-- PENJUALAN (SALES) -->
                            <li class="sidebar-group-title">Penjualan (Sales)</li>
                            <li class="nav-item">
                                <a class="sidebar-item-link {{ request()->routeIs('orders.index') || (request()->routeIs('orders.show') && !request()->routeIs('orders.create')) ? 'active' : '' }}" href="{{ route('orders.index') }}">
                                    <i class="bi bi-receipt"></i>
                                    <span>Semua Pesanan</span>
                                </a>
                            </li>
                            @if(auth()->user()->hasRole(['owner', 'admin', 'sales']))
                                <li class="nav-item">
                                    <a class="sidebar-item-link {{ request()->routeIs('orders.create') ? 'active' : '' }}" href="{{ route('orders.create') }}">
                                        <i class="bi bi-plus-circle"></i>
                                        <span>Input Order Grosir B2B</span>
                                    </a>
                                </li>
                            @endif

                            <!-- KONSINYASI (TITIP JUAL) -->
                            @if(auth()->user()->hasRole(['owner', 'admin', 'sales', 'warehouse', 'finance']))
                                <li class="sidebar-group-title">Konsinyasi (Titip Jual)</li>
                                <li class="nav-item">
                                    <a class="sidebar-item-link {{ request()->routeIs('consignments.index') || (request()->routeIs('consignments.show') && !request()->routeIs('consignments.create')) ? 'active' : '' }}" href="{{ route('consignments.index') }}">
                                        <i class="bi bi-shop-window"></i>
                                        <span>Stok Titipan Toko</span>
                                    </a>
                                </li>
                                @if(auth()->user()->hasRole(['owner', 'admin', 'sales', 'warehouse']))
                                    <li class="nav-item">
                                        <a class="sidebar-item-link {{ request()->routeIs('consignments.create') ? 'active' : '' }}" href="{{ route('consignments.create') }}">
                                            <i class="bi bi-box-arrow-right"></i>
                                            <span>Kirim Titipan Baru</span>
                                        </a>
                                    </li>
                                @endif
                                <li class="nav-item">
                                    <a class="sidebar-item-link {{ request()->routeIs('consignments.audits.*') ? 'active' : '' }}" href="{{ route('consignments.audits.index') }}">
                                        <i class="bi bi-clipboard2-check"></i>
                                        <span>Survei &amp; Tagih Laku</span>
                                    </a>
                                </li>
                            @endif

                            <!-- LOGISTIK & EKSPEDISI -->
                            @if(auth()->user()->hasRole(['owner', 'admin', 'warehouse', 'sales']))
                                <li class="sidebar-group-title">Logistik &amp; Pengiriman</li>
                                <li class="nav-item">
                                    <a class="sidebar-item-link {{ request()->routeIs('delivery.*') ? 'active' : '' }}" href="{{ route('delivery.index') }}">
                                        <i class="bi bi-truck"></i>
                                        <span>Surat Jalan &amp; Ekspedisi</span>
                                    </a>
                                </li>
                            @endif

                            <!-- DISTRIBUSI & ARMADA LAPANGAN -->
                            @if(auth()->user()->hasRole(['owner', 'admin', 'sales', 'warehouse', 'driver']))
                                <li class="sidebar-group-title">Distribusi &amp; Armada Toko</li>
                                <li class="nav-item">
                                    <a class="sidebar-item-link {{ request()->routeIs('driver.*') ? 'active' : '' }}" href="{{ route('driver.dashboard') }}">
                                        <i class="bi bi-qr-code-scan text-warning"></i>
                                        <span>Portal Sopir &amp; Scan Toko</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="sidebar-item-link {{ request()->routeIs('maps.stores') ? 'active' : '' }}" href="{{ route('maps.stores') }}">
                                        <i class="bi bi-geo-alt-fill text-success"></i>
                                        <span>Peta Sebaran Toko</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="sidebar-item-link {{ request()->routeIs('maps.driver_tracking') ? 'active' : '' }}" href="{{ route('maps.driver_tracking') }}">
                                        <i class="bi bi-bicycle text-info"></i>
                                        <span>Peta Jejak Sopir</span>
                                    </a>
                                </li>
                            @endif

                            <!-- KEUANGAN & PIUTANG -->
                            @if(auth()->user()->hasRole(['owner', 'admin', 'sales', 'finance']))
                                <li class="sidebar-group-title">Keuangan &amp; Piutang</li>
                                <li class="nav-item">
                                    <a class="sidebar-item-link {{ request()->routeIs('finance.invoices.*') ? 'active' : '' }}" href="{{ route('finance.invoices.index') }}">
                                        <i class="bi bi-file-earmark-text"></i>
                                        <span>Faktur Penjualan (Invoices)</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="sidebar-item-link {{ request()->routeIs('finance.receivables.*') ? 'active' : '' }}" href="{{ route('finance.receivables.index') }}">
                                        <i class="bi bi-wallet2"></i>
                                        <span>Kontrol Piutang Tempo</span>
                                    </a>
                                </li>
                            @endif

                            <!-- LAPORAN & ANALISIS -->
                            @if(auth()->user()->hasRole(['owner', 'admin', 'warehouse', 'sales', 'finance']))
                                <li class="sidebar-group-title">Laporan &amp; Analisis</li>
                                <li class="nav-item">
                                    <a class="sidebar-item-link {{ request()->routeIs('reports.index') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                                        <i class="bi bi-grid-1x2"></i>
                                        <span>Hub Semua Laporan</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="sidebar-item-link {{ request()->routeIs('reports.sales') ? 'active' : '' }}" href="{{ route('reports.sales') }}">
                                        <i class="bi bi-graph-up"></i>
                                        <span>Laporan Penjualan</span>
                                    </a>
                                </li>
                                @if(auth()->user()->hasRole(['owner', 'admin', 'finance']))
                                    <li class="nav-item">
                                        <a class="sidebar-item-link {{ request()->routeIs('reports.profit') ? 'active' : '' }}" href="{{ route('reports.profit') }}">
                                            <i class="bi bi-cash-coin"></i>
                                            <span>Laba Kotor &amp; HPP</span>
                                        </a>
                                    </li>
                                @endif
                                <li class="nav-item">
                                    <a class="sidebar-item-link {{ request()->routeIs('reports.inventory') ? 'active' : '' }}" href="{{ route('reports.inventory') }}">
                                        <i class="bi bi-box2"></i>
                                        <span>Valuasi Aset Stok</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="sidebar-item-link {{ request()->routeIs('reports.receivables') ? 'active' : '' }}" href="{{ route('reports.receivables') }}">
                                        <i class="bi bi-hourglass-split"></i>
                                        <span>Rekap Aging Piutang</span>
                                    </a>
                                </li>
                            @endif

                            <!-- PENGATURAN SISTEM (Owner & Admin) -->
                            @if(auth()->user()->hasRole(['owner', 'admin']))
                                <li class="sidebar-group-title">Sistem &amp; Pengaturan</li>
                                <li class="nav-item">
                                    <a class="sidebar-item-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}">
                                        <i class="bi bi-sliders"></i>
                                        <span>Pengaturan Sistem</span>
                                        @if($isDevMode)
                                            <span class="badge bg-danger ms-auto" style="font-size: 0.65rem;">DEV ON</span>
                                        @endif
                                    </a>
                                </li>
                            @endif
                        @endif
                    @endauth
                </ul>
            </div>

            <!-- Instant Pre-render for Kopi Sidebar Accordion (Zero Flash) -->
            <script>
                (function() {
                    try {
                        var scrollEl = document.getElementById('sidebarNavScroll') || document.querySelector('.sidebar-nav-scroll');
                        var listEl = document.getElementById('kopiSidebarNav') || (scrollEl ? scrollEl.querySelector('ul.nav') : null);
                        if (!listEl) return;

                        // 1. Restore scroll instantly
                        var sp = sessionStorage.getItem('kopi_sidebar_scroll');
                        if (scrollEl && sp !== null) {
                            scrollEl.scrollTop = parseInt(sp, 10);
                        }

                        // 2. Auto-group items under .sidebar-group-title into .sidebar-nav-group-wrapper
                        var titles = Array.from(listEl.querySelectorAll(':scope > .sidebar-group-title'));
                        if (titles.length === 0) return;

                        var userRole = '{{ auth()->user()->role ?? "guest" }}';
                        var storageKey = 'kopi_sidebar_accordion_' + userRole;
                        var savedRaw = localStorage.getItem(storageKey);
                        var savedState = savedRaw ? JSON.parse(savedRaw) : null;
                        var hasAnySaved = savedState && typeof savedState === 'object';
                        var anyActiveInNav = listEl.querySelector('.sidebar-item-link.active') !== null;

                        titles.forEach(function(title, idx) {
                            var items = [];
                            var next = title.nextElementSibling;
                            while (next && next.classList.contains('nav-item')) {
                                items.push(next);
                                next = next.nextElementSibling;
                            }

                            // Create wrapper li
                            var wrapper = document.createElement('li');
                            wrapper.className = 'sidebar-nav-group-wrapper';
                            listEl.insertBefore(wrapper, title);
                            wrapper.appendChild(title);

                            var subnav = document.createElement('ul');
                            subnav.className = 'sidebar-subnav-list';
                            items.forEach(function(it) {
                                subnav.appendChild(it);
                            });
                            wrapper.appendChild(subnav);

                            // Check active status
                            var hasActive = subnav.querySelector('.sidebar-item-link.active') !== null;
                            var titleKey = title.innerText.replace(/[\n\r]+/g, ' ').replace(/\s+/g, ' ').trim().toUpperCase();

                            if (hasActive) {
                                wrapper.classList.add('has-active', 'is-open');
                                wrapper.classList.remove('is-collapsed');
                            } else if (hasAnySaved) {
                                if (savedState[titleKey] === true) {
                                    wrapper.classList.add('is-open');
                                    wrapper.classList.remove('is-collapsed');
                                } else {
                                    wrapper.classList.add('is-collapsed');
                                    wrapper.classList.remove('is-open');
                                }
                            } else {
                                // Default first visit:
                                // If any active item exists in the sidebar, only open active section!
                                // Else open first section, collapse rest!
                                if (!anyActiveInNav && idx === 0) {
                                    wrapper.classList.add('is-open');
                                    wrapper.classList.remove('is-collapsed');
                                } else {
                                    wrapper.classList.add('is-collapsed');
                                    wrapper.classList.remove('is-open');
                                }
                            }
                        });
                    } catch(e) {}
                })();
            </script>

            <!-- Footer Sidebar: Tombol Keluar -->
            <div class="sidebar-bottom-bar">
                @auth
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100 d-flex align-items-center justify-content-center gap-2 py-2">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Keluar Sistem</span>
                        </button>
                    </form>
                @endauth
            </div>
        </aside>

        <!-- AREA KONTEN UTAMA ERP (KANAN) -->
        <div class="admin-main-wrapper">
            <!-- TOPBAR HEADER ERP -->
            <header class="admin-topbar sticky-top d-flex align-items-center justify-content-between px-3 px-lg-4">
                <div class="d-flex align-items-center gap-3">
                    <!-- Tombol Toggle Sidebar & Mode Full Canvas (Desktop & Mobile) -->
                    <button class="btn btn-light border p-2 shadow-sm d-flex align-items-center justify-content-center" id="sidebarToggleBtn" type="button" title="Buka/Tutup Menu Sidebar (Mode Full Canvas)">
                        <i class="bi bi-layout-sidebar-inset fs-5 text-dark" id="sidebarToggleIcon"></i>
                    </button>

                    <!-- Breadcrumbs / Judul Halaman -->
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-dark text-white-50 fw-normal px-2 py-1 d-none d-sm-inline">ERP Kopi</span>
                        <span class="text-muted small d-none d-md-inline">&bull;</span>
                        <h2 class="h6 mb-0 fw-bold text-dark">@yield('title', 'Dashboard')</h2>
                    </div>
                </div>

                <!-- Aksi Kanan Topbar -->
                <div class="d-flex align-items-center gap-2 gap-md-3">
                    <!-- Quick Toggle Mode Pengembang (Owner & Admin) -->
                    @auth
                        @if(auth()->user()->hasRole(['owner', 'admin']))
                            <form method="POST" action="{{ route('settings.dev_mode.toggle') }}" class="d-inline m-0">
                                @csrf
                                <input type="hidden" name="state" value="{{ $isDevMode ? '0' : '1' }}">
                                <button type="submit" class="btn btn-sm {{ $isDevMode ? 'btn-danger' : 'btn-outline-secondary' }} d-flex align-items-center gap-1 shadow-sm px-2 py-1" title="{{ $isDevMode ? 'Mode Pengembang Aktif (Pembelian Dikunci). Klik untuk matikan.' : 'Mode Normal. Klik untuk aktifkan mode pengembang.' }}">
                                    <i class="bi {{ $isDevMode ? 'bi-shield-lock-fill' : 'bi-shield-check' }}"></i>
                                    <span class="d-none d-md-inline" style="font-size: 0.78rem;">Mode Pengembang:</span>
                                    <span class="badge {{ $isDevMode ? 'bg-white text-danger fw-bold' : 'bg-secondary text-white' }}" style="font-size: 0.65rem;">
                                        {{ $isDevMode ? 'ON' : 'OFF' }}
                                    </span>
                                </button>
                            </form>
                        @endif
                    @endauth

                    <!-- Shortcut Web Toko -->
                    <a href="{{ route('store.catalog') }}" class="btn btn-sm btn-outline-secondary d-none d-sm-inline-flex align-items-center gap-1" title="Lihat Toko Online">
                        <i class="bi bi-shop"></i>
                        <span>Etalase Toko</span>
                    </a>

                    <!-- Dropdown Profil -->
                    @auth
                        <div class="dropdown">
                            <button class="btn btn-light border dropdown-toggle d-flex align-items-center gap-2 py-1 px-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="badge rounded-circle bg-warning text-dark p-2">
                                    <i class="bi bi-person-fill"></i>
                                </span>
                                <div class="text-start d-none d-sm-block">
                                    <span class="d-block fw-bold text-dark lh-1" style="font-size: 0.82rem;">{{ auth()->user()->name }}</span>
                                    <small class="text-muted text-uppercase" style="font-size: 0.65rem;">{{ auth()->user()->role }}</small>
                                </div>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 py-2">
                                <li class="px-3 py-2 border-bottom">
                                    <div class="fw-bold text-dark">{{ auth()->user()->name }}</div>
                                    <small class="text-muted">{{ auth()->user()->email }}</small>
                                    <div class="mt-1">
                                        <span class="badge badge-role badge-role-{{ auth()->user()->role }}">
                                            {{ strtoupper(auth()->user()->role) }}
                                        </span>
                                    </div>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('dashboard') }}">
                                        <i class="bi bi-speedometer2 me-2 text-primary"></i> Dashboard ERP
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('store.catalog') }}">
                                        <i class="bi bi-shop me-2 text-warning"></i> Etalase Toko Ritel
                                    </a>
                                </li>
                                @if(auth()->user()->hasRole(['owner', 'admin']))
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('settings.index') }}">
                                            <i class="bi bi-sliders me-2 text-primary"></i> Pengaturan &amp; Dev Mode
                                        </a>
                                    </li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                                        @csrf
                                        <button type="submit" class="dropdown-item py-2 text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i> Keluar
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endauth
                </div>
            </header>

            <!-- Notifikasi Global (Alerts) -->
            <div class="px-3 px-lg-4 pt-3">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow-sm mb-3" role="alert">
                        <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                        <div>{{ session('success') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center shadow-sm mb-3" role="alert">
                        <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                        <div>{{ session('error') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            </div>

            <!-- Konten Halaman ERP -->
            <main class="flex-grow-1 px-3 px-lg-4 pb-4">
                @yield('content')
            </main>

            <!-- Footer ERP -->
            <footer class="bg-white border-top py-3 px-3 px-lg-4 mt-auto">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 small text-muted text-center text-md-start">
                    <div>
                        &copy; {{ date('Y') }} <strong class="text-dark">Kopi Distro</strong> &mdash; Sistem Distribusi &amp; Penjualan Kopi Kemasan
                    </div>
                    <div>
                        <span>PHP {{ PHP_VERSION }}</span> &middot; <span>Laravel v{{ Illuminate\Foundation\Application::VERSION }}</span> &middot; <span class="badge bg-success bg-opacity-10 text-success">Lokal Windows XAMPP</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

@else
    <!-- ============================================================== -->
    <!-- MODE 2: STOREFRONT & PUBLIK (FULL-WIDTH LUXURY COFFEE THEME)   -->
    <!-- ============================================================== -->
    @if($isDevMode)
        <!-- BANNER PERINGATAN MODE PENGEMBANG PADA ETALASE TOKO -->
        <div class="bg-danger text-white py-2 px-3 text-center shadow-sm" style="font-size: 0.85rem; border-bottom: 2px solid rgba(0,0,0,0.2);">
            <div class="container d-flex flex-wrap align-items-center justify-content-center gap-2">
                <i class="bi bi-shield-lock-fill fs-6"></i>
                <span><strong>MODE PENGEMBANG SEDANG AKTIF:</strong> Seluruh transaksi pembelian etalase toko dinonaktifkan sementara untuk pengujian &amp; pemeliharaan.</span>
                @auth
                    @if(auth()->user()->hasRole(['owner', 'admin']))
                        <form method="POST" action="{{ route('settings.dev_mode.toggle') }}" class="d-inline m-0 ms-2">
                            @csrf
                            <input type="hidden" name="state" value="0">
                            <button type="submit" class="btn btn-sm btn-light py-0 px-2 fw-bold text-danger border-0 shadow-sm" style="font-size: 0.75rem;">
                                <i class="bi bi-toggle-on"></i> Matikan Mode Sekarang
                            </button>
                        </form>
                    @endif
                @endauth
            </div>
        </div>
    @endif

    <div class="d-flex flex-column min-height-100vh" style="min-height: 100vh;">
        <!-- Top Navbar untuk Pengunjung & Pelanggan Toko -->
        <nav class="navbar navbar-expand-lg navbar-dark store-navbar sticky-top">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
                    <div class="bg-warning text-dark rounded-3 p-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 38px; height: 38px;">
                        <i class="bi bi-cup-hot-fill fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold tracking-tight text-white fs-5 lh-1">Kopi Distro</div>
                        <small class="text-white-50" style="font-size: 0.7rem;">Distribusi &amp; Retail Kopi</small>
                    </div>
                </a>

                <div class="d-flex align-items-center gap-2 d-lg-none">
                    <a class="nav-link position-relative p-2 text-white" href="{{ route('store.cart') }}" title="Keranjang Belanja">
                        <i class="bi bi-cart3 fs-5"></i>
                        @if(session('cart') && count(session('cart')) > 0)
                            <span class="position-absolute top-1 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                                {{ count(session('cart')) }}
                            </span>
                        @endif
                    </a>
                    <button class="navbar-toggler border-0 p-2" type="button" data-bs-toggle="collapse" data-bs-target="#storeNavbarContent" aria-label="Buka Menu">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>

                <div class="collapse navbar-collapse" id="storeNavbarContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4 gap-1">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                                <i class="bi bi-house me-1"></i> Beranda
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('store.catalog') ? 'active' : '' }}" href="{{ route('store.catalog') }}">
                                <i class="bi bi-shop me-1"></i> Katalog Kopi
                            </a>
                        </li>
                        @auth
                            @if(auth()->user()->isCustomer())
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('store.my_orders') ? 'active' : '' }}" href="{{ route('store.my_orders') }}">
                                        <i class="bi bi-bag-check me-1"></i> Pesanan Saya
                                    </a>
                                </li>
                            @endif
                        @endauth
                    </ul>

                    <ul class="navbar-nav ms-auto align-items-lg-center gap-2">
                        <!-- Keranjang Belanja -->
                        <li class="nav-item">
                            <a class="nav-link position-relative px-2 text-white" href="{{ route('store.cart') }}" title="Keranjang Belanja">
                                <i class="bi bi-cart3 fs-5"></i>
                                @if(session('cart') && count(session('cart')) > 0)
                                    <span class="position-absolute top-1 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                                        {{ count(session('cart')) }}
                                    </span>
                                @endif
                            </a>
                        </li>

                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">
                                    <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="btn btn-warning btn-sm px-3 fw-bold text-dark shadow-sm" href="{{ route('register') }}">
                                    <i class="bi bi-person-plus me-1"></i> Daftar
                                </a>
                            </li>
                        @else
                            @if(auth()->user()->canAccessDashboard())
                                <li class="nav-item">
                                    <a class="btn btn-warning btn-sm px-3 fw-bold text-dark shadow-sm d-flex align-items-center gap-1" href="{{ route('dashboard') }}">
                                        <i class="bi bi-speedometer2"></i>
                                        <span>Buka Dashboard ERP</span>
                                    </a>
                                </li>
                            @endif

                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 text-white" href="#" id="storeUserDropdown" role="button" data-bs-toggle="dropdown">
                                    <span class="badge rounded-circle bg-warning text-dark p-2">
                                        <i class="bi bi-person-fill"></i>
                                    </span>
                                    <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                                    @if(auth()->user()->isCustomer())
                                        @if(auth()->user()->isToko())
                                            <span class="badge bg-success text-white shadow-sm" style="font-size: 0.7rem;">
                                                <i class="bi bi-shop me-1"></i> Mitra Toko
                                            </span>
                                        @elseif(auth()->user()->isKafe())
                                            <span class="badge bg-warning text-dark shadow-sm" style="font-size: 0.7rem;">
                                                <i class="bi bi-cup-hot-fill me-1"></i> Mitra Kafe
                                            </span>
                                        @else
                                            <span class="badge bg-secondary text-white" style="font-size: 0.7rem;">
                                                Retail
                                            </span>
                                        @endif
                                    @else
                                        <span class="badge badge-role badge-role-{{ auth()->user()->role }}">
                                            {{ auth()->user()->role }}
                                        </span>
                                    @endif
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="storeUserDropdown">
                                    <li class="px-3 py-2 border-bottom">
                                        <div class="fw-bold text-dark">{{ auth()->user()->name }}</div>
                                        <small class="text-muted">{{ auth()->user()->email }}</small>
                                        @if(auth()->user()->isCustomer())
                                            <div class="mt-1">
                                                <span class="badge {{ auth()->user()->isToko() ? 'bg-success' : (auth()->user()->isKafe() ? 'bg-warning text-dark' : 'bg-secondary') }}">
                                                    Skema: {{ strtoupper(auth()->user()->getCustomerType()) }}
                                                </span>
                                            </div>
                                        @endif
                                    </li>
                                    @if(auth()->user()->canAccessDashboard())
                                        <li>
                                            <a class="dropdown-item py-2" href="{{ route('dashboard') }}">
                                                <i class="bi bi-speedometer2 me-2 text-primary"></i> Dashboard ERP
                                            </a>
                                        </li>
                                    @endif
                                    @if(auth()->user()->isCustomer())
                                        <li>
                                            <a class="dropdown-item py-2" href="{{ route('store.my_orders') }}">
                                                <i class="bi bi-bag-check me-2 text-success"></i> Pesanan Saya
                                            </a>
                                        </li>
                                    @endif
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                                            @csrf
                                            <button type="submit" class="dropdown-item py-2 text-danger">
                                                <i class="bi bi-box-arrow-right me-2"></i> Keluar
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Global Alerts Publik -->
        <div class="container mt-3">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>

        <!-- Main Content Halaman Toko/Publik -->
        <main class="flex-grow-1">
            @yield('content')
        </main>

        <!-- Footer Toko Publik -->
        <footer class="py-4 text-white mt-auto" style="background-color: var(--coffee-dark); border-top: 1px solid rgba(255, 255, 255, 0.08);">
            <div class="container text-center">
                <p class="mb-1 fw-semibold text-white">&copy; {{ date('Y') }} Kopi Distro &mdash; Sistem Distribusi &amp; Penjualan Kopi Kemasan</p>
                <small class="text-white-50">Dijalankan pada Lingkungan Windows XAMPP &middot; PHP {{ PHP_VERSION }} &middot; Laravel v{{ Illuminate\Foundation\Application::VERSION }}</small>
            </div>
        </footer>
    </div>
@endif

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('sidebarToggleBtn');
            const adminLayout = document.querySelector('.admin-layout');
            const toggleIcon = document.getElementById('sidebarToggleIcon');
            const offcanvasEl = document.getElementById('sidebarOffcanvas');

            if (toggleBtn && adminLayout) {
                // Pulihkan preferensi pengguna sebelumnya di layar desktop
                if (window.innerWidth >= 992 && localStorage.getItem('erp_sidebar_collapsed') === 'true') {
                    adminLayout.classList.add('sidebar-collapsed');
                    if (toggleIcon) {
                        toggleIcon.classList.replace('bi-layout-sidebar-inset', 'bi-layout-sidebar');
                    }
                    toggleBtn.setAttribute('title', 'Tampilkan Menu Sidebar');
                }

                toggleBtn.addEventListener('click', function() {
                    if (window.innerWidth >= 992) {
                        // Layar Desktop / Laptop: Beralih ke Mode Full Canvas
                        adminLayout.classList.toggle('sidebar-collapsed');
                        const isCollapsed = adminLayout.classList.contains('sidebar-collapsed');
                        localStorage.setItem('erp_sidebar_collapsed', isCollapsed ? 'true' : 'false');
                        if (toggleIcon) {
                            if (isCollapsed) {
                                toggleIcon.classList.replace('bi-layout-sidebar-inset', 'bi-layout-sidebar');
                                toggleBtn.setAttribute('title', 'Tampilkan Menu Sidebar');
                            } else {
                                toggleIcon.classList.replace('bi-layout-sidebar', 'bi-layout-sidebar-inset');
                                toggleBtn.setAttribute('title', 'Sembunyikan Menu Sidebar (Mode Full Canvas)');
                            }
                        }
                    } else if (offcanvasEl && typeof bootstrap !== 'undefined') {
                        // Layar Tablet / Ponsel: Toggle Offcanvas Drawer
                        const bsOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
                        bsOffcanvas.toggle();
                    }
                });
            }
        });

        // ═══════════════════════════════════════════════════════════
        // KOPI DISTRO — COLLAPSIBLE SIDEBAR ACCORDION & MENU SEARCH
        // ═══════════════════════════════════════════════════════════
        (function() {
            const scrollEl = document.getElementById('sidebarNavScroll') || document.querySelector('.sidebar-nav-scroll');
            const listEl = document.getElementById('kopiSidebarNav') || (scrollEl ? scrollEl.querySelector('ul.nav') : null);
            if (!scrollEl || !listEl) return;

            const userRole = '{{ auth()->user()->role ?? "guest" }}';
            const storageKey = 'kopi_sidebar_accordion_' + userRole;
            const searchInput = document.getElementById('sidebarMenuSearch');
            const searchClearBtn = document.getElementById('sidebarSearchClear');
            const toggleAllBtn = document.getElementById('sidebarToggleAllBtn');
            const toggleAllIcon = document.getElementById('sidebarToggleAllIcon');
            const toggleAllText = document.getElementById('sidebarToggleAllText');

            function ensureGrouping() {
                let wrappers = listEl.querySelectorAll(':scope > .sidebar-nav-group-wrapper');
                if (wrappers.length > 0) return wrappers;

                const titles = Array.from(listEl.querySelectorAll(':scope > .sidebar-group-title'));
                titles.forEach(function(title) {
                    const items = [];
                    let next = title.nextElementSibling;
                    while (next && next.classList.contains('nav-item')) {
                        items.push(next);
                        next = next.nextElementSibling;
                    }
                    const wrapper = document.createElement('li');
                    wrapper.className = 'sidebar-nav-group-wrapper';
                    listEl.insertBefore(wrapper, title);
                    wrapper.appendChild(title);

                    const subnav = document.createElement('ul');
                    subnav.className = 'sidebar-subnav-list';
                    items.forEach(it => subnav.appendChild(it));
                    wrapper.appendChild(subnav);
                });

                return listEl.querySelectorAll(':scope > .sidebar-nav-group-wrapper');
            }

            function initKopiAccordion() {
                const wrappers = ensureGrouping();
                if (!wrappers || wrappers.length === 0) return;

                wrappers.forEach(function(wrapper) {
                    const titleEl = wrapper.querySelector('.sidebar-group-title');
                    const subnav = wrapper.querySelector('.sidebar-subnav-list');
                    if (!titleEl || !subnav) return;

                    const items = subnav.querySelectorAll('.nav-item');
                    const itemCount = items.length;
                    const hasActive = subnav.querySelector('.sidebar-item-link.active') !== null;

                    if (hasActive) {
                        wrapper.classList.add('has-active', 'is-open');
                        wrapper.classList.remove('is-collapsed');
                    }

                    // Injeksi chevron dan badge count jika belum ada
                    if (!titleEl.querySelector('.sidebar-group-chevron')) {
                        const originalHTML = titleEl.innerHTML;
                        titleEl.innerHTML = `
                            <div class="sidebar-group-title-left">${originalHTML}</div>
                            <div class="sidebar-group-title-right">
                                <span class="sidebar-group-count" title="${itemCount} menu">${itemCount}</span>
                                <i class="bi bi-chevron-down sidebar-group-chevron"></i>
                            </div>
                        `;
                    }

                    // Aksesibilitas
                    titleEl.setAttribute('role', 'button');
                    titleEl.setAttribute('tabindex', '0');
                    const isCollapsed = wrapper.classList.contains('is-collapsed');
                    titleEl.setAttribute('aria-expanded', isCollapsed ? 'false' : 'true');

                    // Event klik
                    titleEl.addEventListener('click', function(e) {
                        if (e.target.closest('a')) return;

                        const currentlyCollapsed = wrapper.classList.contains('is-collapsed');
                        if (currentlyCollapsed) {
                            wrapper.classList.remove('is-collapsed');
                            wrapper.classList.add('is-open');
                            titleEl.setAttribute('aria-expanded', 'true');
                        } else {
                            wrapper.classList.add('is-collapsed');
                            wrapper.classList.remove('is-open');
                            titleEl.setAttribute('aria-expanded', 'false');
                        }

                        saveAccordionState();
                        updateToggleAllState();
                    });

                    // Keyboard enter / space
                    titleEl.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter' || e.key === ' ') {
                            e.preventDefault();
                            titleEl.click();
                        }
                    });
                });

                updateToggleAllState();
            }

            function saveAccordionState() {
                try {
                    const state = {};
                    listEl.querySelectorAll('.sidebar-nav-group-wrapper').forEach(function(wrp) {
                        const titleEl = wrp.querySelector('.sidebar-group-title-left') || wrp.querySelector('.sidebar-group-title');
                        if (!titleEl) return;
                        const key = titleEl.innerText.replace(/[\n\r]+/g, ' ').replace(/\s+/g, ' ').trim().toUpperCase();
                        state[key] = !wrp.classList.contains('is-collapsed');
                    });
                    localStorage.setItem(storageKey, JSON.stringify(state));
                } catch(e) {}
            }

            function updateToggleAllState() {
                if (!toggleAllBtn || !toggleAllIcon || !toggleAllText) return;
                const wrappers = Array.from(listEl.querySelectorAll('.sidebar-nav-group-wrapper'));
                const nonActiveOpen = wrappers.some(w => !w.classList.contains('has-active') && !w.classList.contains('is-collapsed'));

                if (nonActiveOpen) {
                    toggleAllIcon.textContent = '⊟';
                    toggleAllText.textContent = 'Ciutkan Semua';
                } else {
                    toggleAllIcon.textContent = '⊞';
                    toggleAllText.textContent = 'Buka Semua';
                }
            }

            if (toggleAllBtn) {
                toggleAllBtn.addEventListener('click', function() {
                    const wrappers = Array.from(listEl.querySelectorAll('.sidebar-nav-group-wrapper'));
                    const nonActiveOpen = wrappers.some(w => !w.classList.contains('has-active') && !w.classList.contains('is-collapsed'));

                    wrappers.forEach(function(w) {
                        const titleEl = w.querySelector('.sidebar-group-title');
                        if (nonActiveOpen) {
                            if (!w.classList.contains('has-active')) {
                                w.classList.add('is-collapsed');
                                w.classList.remove('is-open');
                                if (titleEl) titleEl.setAttribute('aria-expanded', 'false');
                            }
                        } else {
                            w.classList.remove('is-collapsed');
                            w.classList.add('is-open');
                            if (titleEl) titleEl.setAttribute('aria-expanded', 'true');
                        }
                    });

                    saveAccordionState();
                    updateToggleAllState();
                });
            }

            // Quick Menu Filter
            if (searchInput) {
                let searchTimeout;

                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    const q = this.value.trim().toLowerCase();

                    if (searchClearBtn) {
                        searchClearBtn.style.display = q ? 'block' : 'none';
                    }

                    searchTimeout = setTimeout(function() {
                        filterKopiMenu(q);
                    }, 50);
                });

                searchInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        searchInput.value = '';
                        if (searchClearBtn) searchClearBtn.style.display = 'none';
                        filterKopiMenu('');
                        searchInput.blur();
                    }
                });

                if (searchClearBtn) {
                    searchClearBtn.addEventListener('click', function() {
                        searchInput.value = '';
                        searchClearBtn.style.display = 'none';
                        filterKopiMenu('');
                        searchInput.focus();
                    });
                }
            }

            function filterKopiMenu(query) {
                const wrappers = listEl.querySelectorAll('.sidebar-nav-group-wrapper');
                let totalMatches = 0;

                const existingEmpty = scrollEl.querySelector('.sidebar-no-results');
                if (existingEmpty) existingEmpty.remove();

                if (!query) {
                    wrappers.forEach(function(wrp) {
                        wrp.style.display = '';
                        const titleEl = wrp.querySelector('.sidebar-group-title');
                        const countEl = wrp.querySelector('.sidebar-group-count');
                        const subnav = wrp.querySelector('.sidebar-subnav-list');
                        if (!subnav) return;

                        const items = subnav.querySelectorAll('.nav-item');
                        items.forEach(it => it.style.display = '');

                        if (countEl) countEl.textContent = items.length;

                        try {
                            const raw = localStorage.getItem(storageKey);
                            const saved = raw ? JSON.parse(raw) : {};
                            const leftEl = wrp.querySelector('.sidebar-group-title-left') || titleEl;
                            const key = leftEl ? leftEl.innerText.replace(/[\n\r]+/g, ' ').replace(/\s+/g, ' ').trim().toUpperCase() : '';
                            const hasActive = wrp.classList.contains('has-active');

                            if (hasActive || saved[key] === true) {
                                wrp.classList.remove('is-collapsed');
                                wrp.classList.add('is-open');
                                if (titleEl) titleEl.setAttribute('aria-expanded', 'true');
                            } else {
                                wrp.classList.add('is-collapsed');
                                wrp.classList.remove('is-open');
                                if (titleEl) titleEl.setAttribute('aria-expanded', 'false');
                            }
                        } catch(e) {}
                    });
                    updateToggleAllState();
                    return;
                }

                wrappers.forEach(function(wrp) {
                    const subnav = wrp.querySelector('.sidebar-subnav-list');
                    const titleEl = wrp.querySelector('.sidebar-group-title');
                    const countEl = wrp.querySelector('.sidebar-group-count');
                    if (!subnav) return;

                    let sectionMatches = 0;
                    const items = subnav.querySelectorAll('.nav-item');

                    items.forEach(function(it) {
                        const linkText = (it.querySelector('span')?.textContent || it.textContent || '').toLowerCase();
                        if (linkText.includes(query)) {
                            it.style.display = '';
                            sectionMatches++;
                            totalMatches++;
                        } else {
                            it.style.display = 'none';
                        }
                    });

                    if (sectionMatches > 0) {
                        wrp.style.display = '';
                        wrp.classList.remove('is-collapsed');
                        wrp.classList.add('is-open');
                        if (titleEl) titleEl.setAttribute('aria-expanded', 'true');
                        if (countEl) countEl.textContent = `${sectionMatches}/${items.length}`;
                    } else {
                        wrp.style.display = 'none';
                    }
                });

                if (totalMatches === 0) {
                    const emptyDiv = document.createElement('div');
                    emptyDiv.className = 'sidebar-no-results';
                    emptyDiv.innerHTML = `
                        <div style="font-size:1.1rem; margin-bottom:0.3rem;">🔍</div>
                        <div>Menu "<strong>${escapeHtml(query)}</strong>" tidak ditemukan.</div>
                        <button type="button" onclick="document.getElementById('sidebarSearchClear').click()">Reset Filter</button>
                    `;
                    scrollEl.appendChild(emptyDiv);
                }
            }

            function escapeHtml(str) {
                return str.replace(/[&<>'"]/g, tag => ({
                    '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;'
                }[tag] || tag));
            }

            // Scroll retention
            function restoreSidebarScroll() {
                const savedScroll = sessionStorage.getItem('kopi_sidebar_scroll');
                if (savedScroll !== null) {
                    scrollEl.scrollTop = parseInt(savedScroll, 10);
                }
                const activeLink = listEl.querySelector('.sidebar-item-link.active');
                if (activeLink) {
                    const navRect = scrollEl.getBoundingClientRect();
                    const linkRect = activeLink.getBoundingClientRect();
                    const isOutOfView = (linkRect.top < navRect.top - 10 || linkRect.bottom > navRect.bottom + 10);
                    if (savedScroll === null || isOutOfView) {
                        activeLink.scrollIntoView({ block: 'center', inline: 'nearest', behavior: 'instant' });
                        sessionStorage.setItem('kopi_sidebar_scroll', scrollEl.scrollTop);
                    }
                }
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    initKopiAccordion();
                    restoreSidebarScroll();
                });
            } else {
                initKopiAccordion();
                restoreSidebarScroll();
            }

            scrollEl.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                if (link) {
                    sessionStorage.setItem('kopi_sidebar_scroll', scrollEl.scrollTop);
                }
            });

            window.addEventListener('beforeunload', function() {
                sessionStorage.setItem('kopi_sidebar_scroll', scrollEl.scrollTop);
            });

            let scrollTimer;
            scrollEl.addEventListener('scroll', function() {
                clearTimeout(scrollTimer);
                scrollTimer = setTimeout(function() {
                    sessionStorage.setItem('kopi_sidebar_scroll', scrollEl.scrollTop);
                }, 60);
            }, { passive: true });
        })();
    </script>

    <!-- ═══════════════════════════════════════════════════════════
         KOPI DISTRO — SMART SEARCHABLE COMBOBOX ENGINE (KopiSearchSelect)
         ═══════════════════════════════════════════════════════════ -->
    <script>
        window.KopiSearchSelect = class {
            constructor(selectEl, customOptions = {}) {
                if (!selectEl || selectEl._kopiSearchSelect) return selectEl ? selectEl._kopiSearchSelect : null;
                this.selectEl = selectEl;
                selectEl._kopiSearchSelect = this;

                this.placeholder = selectEl.getAttribute('data-placeholder') ||
                                   (selectEl.options[0] && !selectEl.options[0].value ? selectEl.options[0].text : 'Ketik untuk mencari...');
                this.searchPlaceholder = selectEl.getAttribute('data-search-placeholder') || 'Ketik nama, SKU, atau kata kunci...';
                
                this.isOpen = false;
                this.highlightedIndex = -1;
                this.visibleOptions = [];
                this.items = [];

                this.buildUI();
                this.syncFromSelect();
                this.bindEvents();
            }

            buildUI() {
                // Wrapper
                this.wrapper = document.createElement('div');
                this.wrapper.className = 'kopi-search-select-wrapper is-hidden-native';
                this.selectEl.parentNode.insertBefore(this.wrapper, this.selectEl);
                this.wrapper.appendChild(this.selectEl);

                // Trigger Button
                this.trigger = document.createElement('div');
                this.trigger.className = 'kopi-select-trigger';
                this.trigger.setAttribute('tabindex', '0');
                this.trigger.setAttribute('role', 'combobox');
                this.trigger.setAttribute('aria-expanded', 'false');

                if (this.selectEl.disabled) {
                    this.trigger.classList.add('is-disabled');
                }
                if (this.selectEl.classList.contains('is-invalid')) {
                    this.trigger.classList.add('is-invalid');
                }

                this.valueContainer = document.createElement('div');
                this.valueContainer.className = 'kopi-select-value';
                
                this.actionsContainer = document.createElement('div');
                this.actionsContainer.className = 'kopi-select-actions';

                this.clearBtn = document.createElement('span');
                this.clearBtn.className = 'kopi-select-clear';
                this.clearBtn.title = 'Hapus pilihan';
                this.clearBtn.innerHTML = '&times;';

                this.chevron = document.createElement('i');
                this.chevron.className = 'bi bi-chevron-down kopi-select-chevron';

                this.actionsContainer.appendChild(this.clearBtn);
                this.actionsContainer.appendChild(this.chevron);

                this.trigger.appendChild(this.valueContainer);
                this.trigger.appendChild(this.actionsContainer);
                this.wrapper.appendChild(this.trigger);

                // Floating Dropdown Panel
                this.dropdown = document.createElement('div');
                this.dropdown.className = 'kopi-select-dropdown';
                
                // Search Box
                const searchBox = document.createElement('div');
                searchBox.className = 'kopi-select-search-box';

                const searchIcon = document.createElement('i');
                searchIcon.className = 'bi bi-search text-muted small ms-1';

                this.searchInput = document.createElement('input');
                this.searchInput.type = 'text';
                this.searchInput.className = 'kopi-select-search-input';
                this.searchInput.placeholder = this.searchPlaceholder;
                this.searchInput.autocomplete = 'off';

                searchBox.appendChild(searchIcon);
                searchBox.appendChild(this.searchInput);
                this.dropdown.appendChild(searchBox);

                // Options List
                this.optionsList = document.createElement('ul');
                this.optionsList.className = 'kopi-select-options-list';
                this.dropdown.appendChild(this.optionsList);

                // Empty state
                this.emptyState = document.createElement('div');
                this.emptyState.className = 'kopi-select-empty d-none';
                this.emptyState.innerHTML = '<i class="bi bi-search text-muted d-block mb-1"></i> Tidak ditemukan hasil yang cocok';
                this.dropdown.appendChild(this.emptyState);

                document.body.appendChild(this.dropdown);
            }

            parseOptions() {
                this.items = [];
                const children = Array.from(this.selectEl.children);

                children.forEach(child => {
                    if (child.tagName === 'OPTGROUP') {
                        const groupLabel = child.getAttribute('label') || '';
                        const groupItems = [];
                        Array.from(child.children).forEach(opt => {
                            if (opt.tagName === 'OPTION') {
                                groupItems.push(this.createItemData(opt, groupLabel));
                            }
                        });
                        this.items.push({
                            isGroup: true,
                            label: groupLabel,
                            items: groupItems
                        });
                    } else if (child.tagName === 'OPTION') {
                        this.items.push(this.createItemData(child, null));
                    }
                });
            }

            createItemData(optionEl, groupLabel) {
                return {
                    isGroup: false,
                    optionEl: optionEl,
                    value: optionEl.value,
                    text: optionEl.textContent.trim(),
                    disabled: optionEl.disabled,
                    groupLabel: groupLabel,
                    sku: optionEl.getAttribute('data-sku') || '',
                    stock: optionEl.getAttribute('data-stock') !== null ? parseInt(optionEl.getAttribute('data-stock'), 10) : null,
                    price: optionEl.getAttribute('data-price') !== null ? parseFloat(optionEl.getAttribute('data-price')) : null,
                    pic: optionEl.getAttribute('data-pic') || '',
                    phone: optionEl.getAttribute('data-phone') || '',
                    type: optionEl.getAttribute('data-type') || optionEl.getAttribute('data-customer-type') || '',
                    role: optionEl.getAttribute('data-role') || '',
                    searchTerms: (
                        optionEl.textContent + ' ' +
                        (optionEl.getAttribute('data-sku') || '') + ' ' +
                        (optionEl.getAttribute('data-pic') || '') + ' ' +
                        (optionEl.getAttribute('data-phone') || '') + ' ' +
                        (optionEl.getAttribute('data-type') || '') + ' ' +
                        (groupLabel || '')
                    ).toLowerCase()
                };
            }

            renderOptions(filterText = '') {
                this.parseOptions();
                this.optionsList.innerHTML = '';
                this.visibleOptions = [];
                this.highlightedIndex = -1;

                const q = filterText.trim().toLowerCase();
                let totalRendered = 0;

                const renderOptionItem = (itemData) => {
                    if (q && !itemData.searchTerms.includes(q)) {
                        return null;
                    }

                    const li = document.createElement('li');
                    li.className = 'kopi-select-option';
                    if (itemData.value === this.selectEl.value) {
                        li.classList.add('is-selected');
                    }
                    if (itemData.disabled) {
                        li.classList.add('is-disabled');
                        li.style.opacity = '0.5';
                        li.style.pointerEvents = 'none';
                    }

                    // Main row
                    const mainRow = document.createElement('div');
                    mainRow.className = 'kopi-select-option-main';

                    const titleSpan = document.createElement('span');
                    titleSpan.className = 'fw-medium';
                    titleSpan.innerHTML = this.highlightMatch(itemData.text, q);
                    mainRow.appendChild(titleSpan);

                    // Right badges if any
                    if (itemData.sku) {
                        const skuBadge = document.createElement('span');
                        skuBadge.className = 'badge bg-dark-subtle text-dark border font-monospace small';
                        skuBadge.textContent = itemData.sku;
                        mainRow.appendChild(skuBadge);
                    } else if (itemData.role) {
                        const roleBadge = document.createElement('span');
                        roleBadge.className = 'badge bg-primary-subtle text-primary border small';
                        roleBadge.textContent = itemData.role;
                        mainRow.appendChild(roleBadge);
                    } else if (itemData.type) {
                        const typeBadge = document.createElement('span');
                        typeBadge.className = 'badge bg-warning-subtle text-warning-emphasis border small';
                        typeBadge.textContent = itemData.type;
                        mainRow.appendChild(typeBadge);
                    }

                    li.appendChild(mainRow);

                    // Sub row (stock, price, phone, PIC)
                    const subItems = [];
                    if (itemData.stock !== null) {
                        if (itemData.stock > 0) {
                            subItems.push(`<span class="text-success fw-semibold"><i class="bi bi-check2"></i> Stok: ${itemData.stock} pack</span>`);
                        } else {
                            subItems.push(`<span class="text-danger fw-semibold"><i class="bi bi-x"></i> Stok Habis</span>`);
                        }
                    }
                    if (itemData.price !== null && itemData.price > 0) {
                        subItems.push(`<span class="text-dark fw-bold">Rp ${itemData.price.toLocaleString('id-ID')}</span>`);
                    }
                    if (itemData.pic) {
                        subItems.push(`<span><i class="bi bi-person text-muted"></i> PIC: ${itemData.pic}</span>`);
                    }
                    if (itemData.phone) {
                        subItems.push(`<span><i class="bi bi-telephone text-muted"></i> ${itemData.phone}</span>`);
                    }

                    if (subItems.length > 0) {
                        const subRow = document.createElement('div');
                        subRow.className = 'kopi-select-option-sub';
                        subRow.innerHTML = subItems.join('<span class="text-muted">&bull;</span>');
                        li.appendChild(subRow);
                    }

                    li.addEventListener('click', (e) => {
                        e.stopPropagation();
                        this.selectItem(itemData);
                    });

                    this.visibleOptions.push({ element: li, itemData: itemData });
                    totalRendered++;
                    return li;
                };

                this.items.forEach(item => {
                    if (item.isGroup) {
                        const matchingChildren = [];
                        item.items.forEach(child => {
                            const el = renderOptionItem(child);
                            if (el) matchingChildren.push(el);
                        });

                        if (matchingChildren.length > 0) {
                            const groupLi = document.createElement('li');
                            groupLi.className = 'kopi-select-group-header';
                            groupLi.textContent = item.label;
                            this.optionsList.appendChild(groupLi);
                            matchingChildren.forEach(el => this.optionsList.appendChild(el));
                        }
                    } else {
                        const el = renderOptionItem(item);
                        if (el) this.optionsList.appendChild(el);
                    }
                });

                if (totalRendered === 0) {
                    this.emptyState.classList.remove('d-none');
                    this.optionsList.classList.add('d-none');
                } else {
                    this.emptyState.classList.add('d-none');
                    this.optionsList.classList.remove('d-none');
                }
            }

            highlightMatch(text, query) {
                if (!query) return text;
                const reg = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
                return text.replace(reg, '<mark class="kopi-select-match-highlight">$1</mark>');
            }

            selectItem(itemData) {
                this.selectEl.value = itemData.value;
                this.syncFromSelect();
                this.close();

                // Trigger native change & input events
                this.selectEl.dispatchEvent(new Event('change', { bubbles: true }));
                this.selectEl.dispatchEvent(new Event('input', { bubbles: true }));
                if (typeof this.selectEl.onchange === 'function') {
                    this.selectEl.onchange();
                }
            }

            clearSelection() {
                this.selectEl.value = '';
                this.syncFromSelect();
                this.close();

                this.selectEl.dispatchEvent(new Event('change', { bubbles: true }));
                this.selectEl.dispatchEvent(new Event('input', { bubbles: true }));
                if (typeof this.selectEl.onchange === 'function') {
                    this.selectEl.onchange();
                }
            }

            syncFromSelect() {
                const val = this.selectEl.value;
                const selectedOpt = this.selectEl.selectedOptions[0];

                if (selectedOpt && val !== '') {
                    const text = selectedOpt.textContent.trim();
                    const sku = selectedOpt.getAttribute('data-sku');
                    const stock = selectedOpt.getAttribute('data-stock');
                    const type = selectedOpt.getAttribute('data-type');
                    const pic = selectedOpt.getAttribute('data-pic');

                    let html = '';
                    if (sku) {
                        html = `<span class="badge bg-secondary font-monospace me-1">${sku}</span> <strong>${text.split('[')[0].split('—')[0].trim()}</strong>`;
                        if (stock !== null) {
                            const stInt = parseInt(stock, 10);
                            html += ` <span class="badge ${stInt > 0 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger'} ms-auto">Stok: ${stInt}</span>`;
                        }
                    } else if (type || pic) {
                        html = `<strong>${text.split('(')[0].trim()}</strong>`;
                        if (type) html += ` <span class="badge bg-warning-subtle text-warning-emphasis border ms-1">${type}</span>`;
                        if (pic) html += ` <small class="text-muted ms-auto"><i class="bi bi-person me-1"></i>${pic}</small>`;
                    } else {
                        html = `<strong>${text}</strong>`;
                    }

                    this.valueContainer.innerHTML = html;
                    this.clearBtn.classList.add('is-visible');
                } else {
                    this.valueContainer.innerHTML = `<span class="kopi-select-placeholder">${this.placeholder}</span>`;
                    this.clearBtn.classList.remove('is-visible');
                }

                if (this.selectEl.classList.contains('is-invalid')) {
                    this.trigger.classList.add('is-invalid');
                } else {
                    this.trigger.classList.remove('is-invalid');
                }
            }

            open() {
                if (this.isOpen || this.selectEl.disabled) return;

                // Close any other open KopiSelect dropdowns
                document.querySelectorAll('.kopi-select-dropdown.is-open').forEach(dd => {
                    dd.classList.remove('is-open');
                });
                document.querySelectorAll('.kopi-select-trigger.is-open').forEach(tr => {
                    tr.classList.remove('is-open');
                });

                this.isOpen = true;
                this.trigger.classList.add('is-open');
                this.trigger.setAttribute('aria-expanded', 'true');
                this.dropdown.classList.add('is-open');

                this.positionDropdown();
                this.searchInput.value = '';
                this.renderOptions('');

                setTimeout(() => {
                    this.searchInput.focus();
                }, 30);
            }

            close() {
                if (!this.isOpen) return;
                this.isOpen = false;
                this.trigger.classList.remove('is-open');
                this.trigger.setAttribute('aria-expanded', 'false');
                this.dropdown.classList.remove('is-open');
            }

            positionDropdown() {
                const rect = this.trigger.getBoundingClientRect();
                const dropdownHeight = 320;
                const spaceBelow = window.innerHeight - rect.bottom;
                const openUpwards = spaceBelow < dropdownHeight && rect.top > dropdownHeight;

                this.dropdown.style.width = Math.max(rect.width, 260) + 'px';
                this.dropdown.style.left = rect.left + 'px';

                if (openUpwards) {
                    this.dropdown.style.top = 'auto';
                    this.dropdown.style.bottom = (window.innerHeight - rect.top + 4) + 'px';
                } else {
                    this.dropdown.style.top = (rect.bottom + 4) + 'px';
                    this.dropdown.style.bottom = 'auto';
                }
            }

            bindEvents() {
                this.trigger.addEventListener('click', (e) => {
                    if (e.target.closest('.kopi-select-clear')) {
                        this.clearSelection();
                        return;
                    }
                    if (this.isOpen) {
                        this.close();
                    } else {
                        this.open();
                    }
                });

                this.trigger.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ' || e.key === 'ArrowDown') {
                        e.preventDefault();
                        this.open();
                    }
                });

                this.searchInput.addEventListener('input', (e) => {
                    this.renderOptions(e.target.value);
                });

                this.searchInput.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') {
                        this.close();
                        this.trigger.focus();
                    } else if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        this.moveHighlight(1);
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        this.moveHighlight(-1);
                    } else if (e.key === 'Enter') {
                        e.preventDefault();
                        if (this.highlightedIndex >= 0 && this.visibleOptions[this.highlightedIndex]) {
                            this.selectItem(this.visibleOptions[this.highlightedIndex].itemData);
                            this.trigger.focus();
                        }
                    }
                });

                // Click outside closes
                document.addEventListener('pointerdown', (e) => {
                    if (this.isOpen && !this.trigger.contains(e.target) && !this.dropdown.contains(e.target)) {
                        this.close();
                    }
                });

                // Reposition on window resize or scroll
                window.addEventListener('resize', () => {
                    if (this.isOpen) this.positionDropdown();
                }, { passive: true });

                window.addEventListener('scroll', () => {
                    if (this.isOpen) this.positionDropdown();
                }, { passive: true });

                // Listen for external change on select
                this.selectEl.addEventListener('change', () => {
                    this.syncFromSelect();
                });
            }

            moveHighlight(direction) {
                if (this.visibleOptions.length === 0) return;
                this.highlightedIndex += direction;
                if (this.highlightedIndex < 0) this.highlightedIndex = 0;
                if (this.highlightedIndex >= this.visibleOptions.length) {
                    this.highlightedIndex = this.visibleOptions.length - 1;
                }

                this.visibleOptions.forEach((opt, idx) => {
                    if (idx === this.highlightedIndex) {
                        opt.element.classList.add('is-highlighted');
                        opt.element.scrollIntoView({ block: 'nearest' });
                    } else {
                        opt.element.classList.remove('is-highlighted');
                    }
                });
            }

            destroy() {
                if (this.dropdown && this.dropdown.parentNode) {
                    this.dropdown.parentNode.removeChild(this.dropdown);
                }
                if (this.wrapper && this.wrapper.parentNode) {
                    this.wrapper.parentNode.insertBefore(this.selectEl, this.wrapper);
                    this.wrapper.parentNode.removeChild(this.wrapper);
                }
                delete this.selectEl._kopiSearchSelect;
            }
        };

        window.initKopiSearchableSelect = function(container = document) {
            const selects = container.querySelectorAll('select.form-select-searchable, select.select-searchable, select[data-searchable="true"]');
            selects.forEach(sel => {
                if (!sel._kopiSearchSelect) {
                    new window.KopiSearchSelect(sel);
                }
            });
        };

        document.addEventListener('DOMContentLoaded', function() {
            window.initKopiSearchableSelect();
        });
    </script>
    @stack('scripts')
</body>
</html>

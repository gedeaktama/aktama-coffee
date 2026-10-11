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
            }
            .admin-main-wrapper {
                margin-left: var(--sidebar-width);
                width: calc(100% - var(--sidebar-width));
            }
        }

        @media (max-width: 991.98px) {
            .admin-sidebar {
                width: 280px !important;
            }
            .admin-main-wrapper {
                margin-left: 0;
                width: 100%;
            }
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

        .sidebar-group-title {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--admin-sidebar-muted) !important;
            padding: 0.85rem 0.65rem 0.35rem 0.65rem;
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
        .badge-role-customer { background-color: #64748b; color: #fff; }

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

            <!-- Navigasi Menu Vertikal (Scrollable) -->
            <div class="sidebar-nav-scroll">
                <ul class="nav flex-column">
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
                        @endif
                    @endauth
                </ul>
            </div>

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
                    <!-- Tombol Hamburger Mobile -->
                    <button class="btn btn-light border d-lg-none p-2 shadow-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas" aria-label="Buka Menu">
                        <i class="bi bi-list fs-5"></i>
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

                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#storeNavbarContent">
                    <span class="navbar-toggler-icon"></span>
                </button>

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
                                    <span class="badge badge-role badge-role-{{ auth()->user()->role }}">
                                        {{ auth()->user()->role }}
                                    </span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="storeUserDropdown">
                                    <li class="px-3 py-2 border-bottom">
                                        <div class="fw-bold text-dark">{{ auth()->user()->name }}</div>
                                        <small class="text-muted">{{ auth()->user()->email }}</small>
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
    @stack('scripts')
</body>
</html>

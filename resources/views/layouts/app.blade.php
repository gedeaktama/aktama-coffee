<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Distribusi & Penjualan Kopi')</title>
    
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
            --sidebar-width: 260px;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--coffee-cream);
            color: #2b2b2b;
            min-height: 100vh;
        }

        /* App Layout Container */
        .app-layout {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* Sidebar Styling */
        .app-sidebar {
            width: var(--sidebar-width);
            background-color: var(--coffee-dark);
            color: #ffffff;
            display: flex;
            flex-direction: column;
            border-right: 1px solid rgba(255, 255, 255, 0.07);
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.15);
            z-index: 1040;
        }

        @media (min-width: 992px) {
            .app-sidebar {
                position: fixed;
                top: 0;
                bottom: 0;
                left: 0;
                height: 100vh;
            }
            .main-wrapper {
                margin-left: var(--sidebar-width);
                width: calc(100% - var(--sidebar-width));
            }
        }

        @media (max-width: 991.98px) {
            .app-sidebar {
                width: 280px;
            }
            .main-wrapper {
                margin-left: 0;
                width: 100%;
            }
        }

        .sidebar-brand-wrapper {
            background-color: #170d07;
            padding: 1.15rem 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .sidebar-user-box {
            background: linear-gradient(180deg, rgba(255,255,255,0.04) 0%, rgba(255,255,255,0.01) 100%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            padding: 0.9rem 1.25rem;
        }

        .sidebar-nav-list {
            padding: 0.85rem 0.75rem;
            overflow-y: auto;
            flex-grow: 1;
        }

        .sidebar-nav-list::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar-nav-list::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
        }

        .sidebar-section-title {
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: rgba(255, 255, 255, 0.45);
            padding: 0.75rem 0.6rem 0.35rem 0.6rem;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.52rem 0.75rem;
            border-radius: 8px;
            color: rgba(255, 255, 255, 0.76);
            text-decoration: none;
            font-size: 0.86rem;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
            margin-bottom: 2px;
        }

        .sidebar-link i {
            font-size: 1.05rem;
            width: 20px;
            text-align: center;
            color: var(--coffee-amber);
            transition: transform 0.2s;
        }

        .sidebar-link:hover {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.09);
            transform: translateX(3px);
        }

        .sidebar-link:hover i {
            transform: scale(1.15);
        }

        .sidebar-link.active {
            color: #ffffff;
            background-color: var(--coffee-caramel);
            font-weight: 600;
            box-shadow: 0 4px 14px rgba(184, 107, 53, 0.38);
        }

        .sidebar-link.active i {
            color: #ffffff;
        }

        .sidebar-footer {
            background-color: #170d07;
            padding: 0.85rem 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        /* Main Wrapper */
        .main-wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: var(--coffee-cream);
            transition: all 0.3s ease;
        }

        /* Topbar Header */
        .app-topbar {
            background-color: #ffffff;
            border-bottom: 1px solid var(--coffee-border);
            box-shadow: 0 2px 10px rgba(35, 22, 16, 0.03);
            z-index: 1020;
        }

        /* Custom UI Components */
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
            color: var(--coffee-caramel);
            border: 1.5px solid var(--coffee-caramel);
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.2s ease-in-out;
        }

        .btn-outline-coffee:hover {
            background-color: var(--coffee-caramel);
            color: #ffffff;
        }

        .card-custom {
            background: #ffffff;
            border: 1px solid var(--coffee-border);
            border-radius: 12px;
            box-shadow: 0 3px 14px rgba(35, 22, 16, 0.03);
            transition: transform 0.2s, box-shadow 0.2s;
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
        .badge-role-warehouse { background-color: #f57c00; color: #fff; }
        .badge-role-finance { background-color: #0097a7; color: #fff; }
        .badge-role-customer { background-color: #616161; color: #fff; }

        /* Footer */
        .app-footer {
            background-color: #ffffff;
            border-top: 1px solid var(--coffee-border);
            color: #6c757d;
            font-size: 0.825rem;
            padding: 1rem 0;
            margin-top: auto;
        }

        /* Print Media Styles */
        @media print {
            .app-sidebar,
            .app-topbar,
            .app-footer,
            .no-print,
            .btn,
            button {
                display: none !important;
            }
            .main-wrapper {
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

    <div class="app-layout">
        <!-- ========================================== -->
        <!-- SIDEBAR NAVIGASI SEBELAH KIRI (BOOTSTRAP)  -->
        <!-- ========================================== -->
        <aside class="app-sidebar offcanvas-lg offcanvas-start" id="sidebarOffcanvas" tabindex="-1" aria-labelledby="sidebarOffcanvasLabel">
            <!-- Sidebar Brand Header -->
            <div class="sidebar-brand-wrapper d-flex align-items-center justify-content-between">
                <a class="d-flex align-items-center gap-2 text-decoration-none text-white" href="{{ route('home') }}">
                    <div class="bg-warning text-dark rounded-3 p-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 36px; height: 36px;">
                        <i class="bi bi-cup-hot-fill fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold tracking-tight text-white fs-5 lh-1">Kopi Distro</div>
                        <small class="text-white-50" style="font-size: 0.68rem;">Sistem Grosir &amp; Ritel</small>
                    </div>
                </a>
                <!-- Tombol Tutup Khusus Mobile/Tablet -->
                <button type="button" class="btn-close btn-close-white d-lg-none" data-bs-dismiss="offcanvas" data-bs-target="#sidebarOffcanvas" aria-label="Close"></button>
            </div>

            <!-- User Mini Profile Card di Sidebar -->
            @auth
                <div class="sidebar-user-box">
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

            <!-- Daftar Menu Navigasi (Scrollable) -->
            <div class="sidebar-nav-list">
                <ul class="nav flex-column">
                    <!-- GRUP: UTAMA -->
                    <li class="sidebar-section-title">Navigasi Utama</li>
                    
                    <li class="nav-item">
                        <a class="sidebar-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            <i class="bi bi-house-door"></i>
                            <span>Beranda</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="sidebar-link {{ request()->routeIs('store.catalog') ? 'active' : '' }}" href="{{ route('store.catalog') }}">
                            <i class="bi bi-shop"></i>
                            <span>Katalog Kopi</span>
                        </a>
                    </li>

                    @auth
                        @if(auth()->user()->isCustomer())
                            <li class="nav-item">
                                <a class="sidebar-link {{ request()->routeIs('store.my_orders') ? 'active' : '' }}" href="{{ route('store.my_orders') }}">
                                    <i class="bi bi-bag-check"></i>
                                    <span>Pesanan Saya</span>
                                </a>
                            </li>
                        @endif

                        @if(auth()->user()->canAccessDashboard())
                            <li class="nav-item">
                                <a class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                    <i class="bi bi-speedometer2"></i>
                                    <span>Dashboard ERP</span>
                                </a>
                            </li>

                            <!-- GRUP: MASTER DATA -->
                            <li class="sidebar-section-title">Master Data</li>
                            @if(auth()->user()->hasRole(['owner', 'admin']))
                                <li class="nav-item">
                                    <a class="sidebar-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                                        <i class="bi bi-tags"></i>
                                        <span>Kategori Produk</span>
                                    </a>
                                </li>
                            @endif
                            <li class="nav-item">
                                <a class="sidebar-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                                    <i class="bi bi-box-seam"></i>
                                    <span>Produk &amp; SKU</span>
                                </a>
                            </li>
                            @if(auth()->user()->hasRole(['owner', 'admin', 'warehouse']))
                                <li class="nav-item">
                                    <a class="sidebar-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}" href="{{ route('suppliers.index') }}">
                                        <i class="bi bi-truck-front"></i>
                                        <span>Pemasok (Supplier)</span>
                                    </a>
                                </li>
                            @endif
                            @if(auth()->user()->hasRole(['owner', 'admin', 'sales', 'finance']))
                                <li class="nav-item">
                                    <a class="sidebar-link {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}">
                                        <i class="bi bi-buildings"></i>
                                        <span>Pelanggan B2B</span>
                                    </a>
                                </li>
                            @endif

                            <!-- GRUP: GUDANG & PERSEDIAAN -->
                            @if(auth()->user()->hasRole(['owner', 'admin', 'warehouse', 'sales', 'finance']))
                                <li class="sidebar-section-title">Gudang &amp; Persediaan</li>
                                <li class="nav-item">
                                    <a class="sidebar-link {{ request()->routeIs('inventory.index') ? 'active' : '' }}" href="{{ route('inventory.index') }}">
                                        <i class="bi bi-boxes"></i>
                                        <span>Monitoring Stok</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="sidebar-link {{ request()->routeIs('inventory.movements') ? 'active' : '' }}" href="{{ route('inventory.movements') }}">
                                        <i class="bi bi-journal-text"></i>
                                        <span>Kartu Mutasi Stok</span>
                                    </a>
                                </li>
                                @if(auth()->user()->hasRole(['owner', 'admin', 'warehouse', 'finance']))
                                    <li class="nav-item">
                                        <a class="sidebar-link {{ request()->routeIs('purchases.*') ? 'active' : '' }}" href="{{ route('purchases.index') }}">
                                            <i class="bi bi-cart-check"></i>
                                            <span>Pembelian Supplier (PO)</span>
                                        </a>
                                    </li>
                                @endif
                            @endif

                            <!-- GRUP: PENJUALAN & ORDER -->
                            <li class="sidebar-section-title">Penjualan (Sales)</li>
                            <li class="nav-item">
                                <a class="sidebar-link {{ request()->routeIs('orders.index') || (request()->routeIs('orders.show') && !request()->routeIs('orders.create')) ? 'active' : '' }}" href="{{ route('orders.index') }}">
                                    <i class="bi bi-receipt"></i>
                                    <span>Semua Pesanan</span>
                                </a>
                            </li>
                            @if(auth()->user()->hasRole(['owner', 'admin', 'sales']))
                                <li class="nav-item">
                                    <a class="sidebar-link {{ request()->routeIs('orders.create') ? 'active' : '' }}" href="{{ route('orders.create') }}">
                                        <i class="bi bi-plus-circle"></i>
                                        <span>Input Order B2B</span>
                                    </a>
                                </li>
                            @endif

                            <!-- GRUP: LOGISTIK & PENGIRIMAN -->
                            @if(auth()->user()->hasRole(['owner', 'admin', 'warehouse', 'sales']))
                                <li class="sidebar-section-title">Logistik &amp; Kirim</li>
                                <li class="nav-item">
                                    <a class="sidebar-link {{ request()->routeIs('delivery.*') ? 'active' : '' }}" href="{{ route('delivery.index') }}">
                                        <i class="bi bi-truck"></i>
                                        <span>Surat Jalan &amp; Ekspedisi</span>
                                    </a>
                                </li>
                            @endif

                            <!-- GRUP: KEUANGAN & PIUTANG -->
                            @if(auth()->user()->hasRole(['owner', 'admin', 'sales', 'finance']))
                                <li class="sidebar-section-title">Keuangan &amp; Piutang</li>
                                <li class="nav-item">
                                    <a class="sidebar-link {{ request()->routeIs('finance.invoices.*') ? 'active' : '' }}" href="{{ route('finance.invoices.index') }}">
                                        <i class="bi bi-file-earmark-text"></i>
                                        <span>Faktur Penjualan</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="sidebar-link {{ request()->routeIs('finance.receivables.*') ? 'active' : '' }}" href="{{ route('finance.receivables.index') }}">
                                        <i class="bi bi-wallet2"></i>
                                        <span>Kontrol Piutang Tempo</span>
                                    </a>
                                </li>
                            @endif

                            <!-- GRUP: LAPORAN & ANALISIS -->
                            @if(auth()->user()->hasRole(['owner', 'admin', 'warehouse', 'sales', 'finance']))
                                <li class="sidebar-section-title">Laporan &amp; Analisis</li>
                                <li class="nav-item">
                                    <a class="sidebar-link {{ request()->routeIs('reports.index') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                                        <i class="bi bi-grid-1x2"></i>
                                        <span>Hub Semua Laporan</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="sidebar-link {{ request()->routeIs('reports.sales') ? 'active' : '' }}" href="{{ route('reports.sales') }}">
                                        <i class="bi bi-graph-up"></i>
                                        <span>Laporan Penjualan</span>
                                    </a>
                                </li>
                                @if(auth()->user()->hasRole(['owner', 'admin', 'finance']))
                                    <li class="nav-item">
                                        <a class="sidebar-link {{ request()->routeIs('reports.profit') ? 'active' : '' }}" href="{{ route('reports.profit') }}">
                                            <i class="bi bi-cash-coin"></i>
                                            <span>Laba Kotor &amp; HPP</span>
                                        </a>
                                    </li>
                                @endif
                                <li class="nav-item">
                                    <a class="sidebar-link {{ request()->routeIs('reports.inventory') ? 'active' : '' }}" href="{{ route('reports.inventory') }}">
                                        <i class="bi bi-box2"></i>
                                        <span>Valuasi Aset Stok</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="sidebar-link {{ request()->routeIs('reports.receivables') ? 'active' : '' }}" href="{{ route('reports.receivables') }}">
                                        <i class="bi bi-hourglass-split"></i>
                                        <span>Rekap Aging Piutang</span>
                                    </a>
                                </li>
                            @endif
                        @endif
                    @else
                        <!-- UNTUK TAMU (GUEST) -->
                        <li class="sidebar-section-title">Autentikasi</li>
                        <li class="nav-item">
                            <a class="sidebar-link {{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right"></i>
                                <span>Masuk (Login)</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="sidebar-link {{ request()->routeIs('register') ? 'active' : '' }}" href="{{ route('register') }}">
                                <i class="bi bi-person-plus"></i>
                                <span>Daftar Akun Ritel</span>
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>

            <!-- Footer Sidebar: Logout Button -->
            <div class="sidebar-footer">
                @auth
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100 d-flex align-items-center justify-content-center gap-2 py-2">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Keluar Sistem</span>
                        </button>
                    </form>
                @else
                    <div class="text-center text-white-50 small py-1">
                        <i class="bi bi-shield-lock me-1"></i> Mode Publik
                    </div>
                @endauth
            </div>
        </aside>

        <!-- ========================================== -->
        <!-- AREA KONTEN UTAMA (HEADER + ISI + FOOTER)  -->
        <!-- ========================================== -->
        <div class="main-wrapper">
            <!-- TOPBAR HEADER -->
            <header class="app-topbar sticky-top py-2 px-3 px-lg-4 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2 gap-md-3">
                    <!-- Tombol Hamburger Menu (Mobile & Tablet) -->
                    <button class="btn btn-light border d-lg-none p-2 shadow-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas" aria-label="Buka Menu">
                        <i class="bi bi-list fs-5"></i>
                    </button>

                    <!-- Judul Halaman / Konteks -->
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="d-none d-md-inline-block text-muted small fw-semibold">Kopi Distro &middot;</span>
                            <span class="fw-bold text-dark fs-6">@yield('title', 'Sistem Distribusi Kopi')</span>
                        </div>
                    </div>
                </div>

                <!-- Aksi Cepat Sisi Kanan Topbar -->
                <div class="d-flex align-items-center gap-2 gap-md-3">
                    <!-- Shortcut Etalase Ritel -->
                    <a href="{{ route('store.catalog') }}" class="btn btn-sm btn-outline-coffee d-none d-sm-inline-flex align-items-center gap-1" title="Lihat Toko Kopi Online">
                        <i class="bi bi-shop"></i>
                        <span>Etalase Toko</span>
                    </a>

                    <!-- Keranjang Belanja -->
                    <a href="{{ route('store.cart') }}" class="btn btn-light border position-relative p-2" title="Keranjang Belanja">
                        <i class="bi bi-cart3 fs-5"></i>
                        @if(session('cart') && count(session('cart')) > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                                {{ count(session('cart')) }}
                            </span>
                        @endif
                    </a>

                    @auth
                        <!-- Dropdown Profil Pengguna -->
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
                                @if(auth()->user()->canAccessDashboard())
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('dashboard') }}">
                                            <i class="bi bi-speedometer2 me-2 text-primary"></i> Dashboard ERP
                                        </a>
                                    </li>
                                @endif
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('store.catalog') }}">
                                        <i class="bi bi-shop me-2 text-warning"></i> Katalog Kopi Ritel
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
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm px-3">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-coffee btn-sm px-3 text-white">
                            <i class="bi bi-person-plus me-1"></i> Daftar
                        </a>
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

            <!-- Konten Halaman yang Dirender -->
            <main class="flex-grow-1 px-3 px-lg-4 pb-4">
                @yield('content')
            </main>

            <!-- Footer Aplikasi -->
            <footer class="app-footer px-3 px-lg-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 text-center text-md-start">
                    <div>
                        &copy; {{ date('Y') }} <strong class="text-dark">Kopi Distro</strong> &mdash; Distribusi &amp; Penjualan Kopi Kemasan
                    </div>
                    <div>
                        <span class="text-muted">PHP {{ PHP_VERSION }} &middot; Laravel v{{ Illuminate\Foundation\Application::VERSION }}</span> &middot; <span class="badge bg-success bg-opacity-10 text-success">Windows XAMPP Lokal</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>

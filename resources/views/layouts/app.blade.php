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
            --coffee-dark: #231610;
            --coffee-espresso: #382218;
            --coffee-roast: #4a3023;
            --coffee-caramel: #c07d4b;
            --coffee-amber: #d4a373;
            --coffee-cream: #faf7f2;
            --coffee-surface: #ffffff;
            --coffee-border: #ede4db;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--coffee-cream);
            color: #2b2b2b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar-coffee {
            background-color: var(--coffee-dark);
            box-shadow: 0 4px 20px rgba(35, 22, 16, 0.15);
        }

        .navbar-coffee .navbar-brand {
            color: #ffffff;
            font-weight: 700;
            letter-spacing: -0.3px;
        }

        .navbar-coffee .nav-link {
            color: rgba(255, 255, 255, 0.82);
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: color 0.2s;
        }

        .navbar-coffee .nav-link:hover,
        .navbar-coffee .nav-link.active {
            color: var(--coffee-amber);
        }

        .btn-coffee {
            background-color: var(--coffee-caramel);
            color: #ffffff;
            border: none;
            font-weight: 600;
            padding: 0.6rem 1.4rem;
            border-radius: 8px;
            transition: all 0.2s ease-in-out;
        }

        .btn-coffee:hover {
            background-color: var(--coffee-roast);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(74, 48, 35, 0.25);
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
            border: 1px solid var(--coffee-border);
            border-radius: 14px;
            box-shadow: 0 4px 16px rgba(40, 24, 18, 0.04);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .badge-role {
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.35rem 0.65rem;
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

        footer {
            margin-top: auto;
            background-color: var(--coffee-dark);
            color: rgba(255, 255, 255, 0.7);
            padding: 1.5rem 0;
            font-size: 0.875rem;
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Header & Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-coffee sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
                <span class="fs-4"><i class="bi bi-cup-hot-fill text-warning"></i></span>
                <div>
                    <span class="fw-bold tracking-tight">Kopi Distro</span>
                    <small class="d-block text-white-50" style="font-size: 0.68rem; line-height: 1;">Distribusi & Penjualan Kopi</small>
                </div>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
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

                        @if(auth()->user()->canAccessDashboard())
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                    <i class="bi bi-speedometer2 me-1"></i> Dashboard
                                </a>
                            </li>

                            <!-- Master Data -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ request()->is('master*') ? 'active' : '' }}" href="#" id="masterDropdown" role="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-database me-1"></i> Master Data
                                </a>
                                <ul class="dropdown-menu shadow border-0" aria-labelledby="masterDropdown">
                                    @if(auth()->user()->hasRole(['owner', 'admin']))
                                        <li><a class="dropdown-item py-2" href="{{ route('categories.index') }}"><i class="bi bi-tags me-2 text-warning"></i> Kategori Produk</a></li>
                                    @endif
                                    <li><a class="dropdown-item py-2" href="{{ route('products.index') }}"><i class="bi bi-box-seam me-2 text-primary"></i> Produk & Varian SKU</a></li>
                                    @if(auth()->user()->hasRole(['owner', 'admin', 'warehouse']))
                                        <li><a class="dropdown-item py-2" href="{{ route('suppliers.index') }}"><i class="bi bi-truck me-2 text-info"></i> Pemasok / Supplier</a></li>
                                    @endif
                                    @if(auth()->user()->hasRole(['owner', 'admin', 'sales', 'finance']))
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item py-2" href="{{ route('customers.index') }}"><i class="bi bi-people me-2 text-success"></i> Pelanggan Grosir (B2B)</a></li>
                                    @endif
                                </ul>
                            </li>

                            <!-- Persediaan & Pembelian -->
                            @if(auth()->user()->hasRole(['owner', 'admin', 'warehouse', 'sales', 'finance']))
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle {{ request()->is('inventory*') ? 'active' : '' }}" href="#" id="inventoryDropdown" role="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-boxes me-1"></i> Persediaan & Pembelian
                                    </a>
                                    <ul class="dropdown-menu shadow border-0" aria-labelledby="inventoryDropdown">
                                        <li><a class="dropdown-item py-2" href="{{ route('inventory.index') }}"><i class="bi bi-box-seam me-2 text-primary"></i> Monitoring Stok & Alert</a></li>
                                        <li><a class="dropdown-item py-2" href="{{ route('inventory.movements') }}"><i class="bi bi-journal-text me-2 text-secondary"></i> Kartu & Mutasi Stok</a></li>
                                        @if(auth()->user()->hasRole(['owner', 'admin', 'warehouse', 'finance']))
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item py-2" href="{{ route('purchases.index') }}"><i class="bi bi-cart-check me-2 text-success"></i> Pembelian Supplier (PO)</a></li>
                                            @if(auth()->user()->hasRole(['owner', 'admin', 'warehouse']))
                                                <li><a class="dropdown-item py-2" href="{{ route('purchases.create') }}"><i class="bi bi-plus-circle me-2 text-info"></i> Buat PO Baru</a></li>
                                            @endif
                                        @endif
                                    </ul>
                                </li>
                            @endif

                            <!-- Penjualan & Pesanan -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ request()->is('sales*') ? 'active' : '' }}" href="#" id="salesDropdown" role="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-receipt me-1"></i> Penjualan & Order
                                </a>
                                <ul class="dropdown-menu shadow border-0" aria-labelledby="salesDropdown">
                                    <li><a class="dropdown-item py-2" href="{{ route('orders.index') }}"><i class="bi bi-list-check me-2 text-primary"></i> Daftar Semua Pesanan</a></li>
                                    @if(auth()->user()->hasRole(['owner', 'admin', 'sales']))
                                        <li><a class="dropdown-item py-2" href="{{ route('orders.create') }}"><i class="bi bi-plus-circle me-2 text-success"></i> Input Order B2B Baru</a></li>
                                    @endif
                                </ul>
                            </li>

                            <!-- Keuangan & Piutang (Tahap 5) -->
                            @if(auth()->user()->hasRole(['owner', 'admin', 'sales', 'finance']))
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle {{ request()->is('finance*') ? 'active' : '' }}" href="#" id="financeDropdown" role="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-wallet2 me-1"></i> Keuangan & Piutang
                                    </a>
                                    <ul class="dropdown-menu shadow border-0" aria-labelledby="financeDropdown">
                                        <li><a class="dropdown-item py-2" href="{{ route('finance.invoices.index') }}"><i class="bi bi-file-earmark-text me-2 text-primary"></i> Faktur Penjualan (Invoices)</a></li>
                                        <li><a class="dropdown-item py-2" href="{{ route('finance.receivables.index') }}"><i class="bi bi-graph-up-arrow me-2 text-danger"></i> Kontrol & Monitoring Piutang</a></li>
                                    </ul>
                                </li>
                            @endif
                        @endif
                    @endauth
                </ul>

                <ul class="navbar-nav ms-auto align-items-lg-center gap-2">
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
                            <a class="btn btn-outline-coffee btn-sm px-3" href="{{ route('register') }}">
                                <i class="bi bi-person-plus me-1"></i> Daftar
                            </a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 text-white" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <span class="badge rounded-circle bg-warning text-dark p-2">
                                    <i class="bi bi-person-fill"></i>
                                </span>
                                <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                                <span class="badge badge-role badge-role-{{ auth()->user()->role }}">
                                    {{ auth()->user()->role }}
                                </span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="userDropdown">
                                <li class="px-3 py-2 border-bottom">
                                    <div class="fw-bold text-dark">{{ auth()->user()->name }}</div>
                                    <small class="text-muted">{{ auth()->user()->email }}</small>
                                </li>
                                @if(auth()->user()->canAccessDashboard())
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('dashboard') }}">
                                            <i class="bi bi-speedometer2 me-2 text-primary"></i> Dashboard
                                        </a>
                                    </li>
                                @endif
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
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

    <!-- Global Alerts -->
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

    <!-- Main Content -->
    <main class="flex-grow-1 py-4">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer>
        <div class="container text-center">
            <p class="mb-1 fw-semibold text-white">&copy; {{ date('Y') }} Kopi Distro &mdash; Sistem Distribusi & Penjualan Kopi Berbasis Laravel</p>
            <small class="text-white-50">Dijalankan pada Lingkungan Lokal Windows XAMPP &middot; PHP {{ PHP_VERSION }} &middot; Laravel v{{ Illuminate\Foundation\Application::VERSION }}</small>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>

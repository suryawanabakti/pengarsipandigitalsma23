<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - SMAN 23 Makassar</title>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>

<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="user-avatar">23</div>
                <div class="sidebar-brand">SMAN 23 MKS</div>
            </div>

            <nav class="sidebar-menu">
                <div class="menu-label">Menu Utama</div>
                <div class="menu-item">
                    <a href="{{ route('admin.dashboard') }}"
                        class="menu-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-th-large"></i> Dashboard
                    </a>
                </div>

                <div class="menu-label">Dokumen</div>
                <div class="menu-item">
                    <a href="{{ route('documents.index') }}"
                        class="menu-link {{ request()->routeIs('documents.index*') ? 'active' : '' }}">
                        <i class="fas fa-file-alt"></i> Semua Dokumen
                    </a>
                </div>
                
                @if (in_array(auth()->user()->role->name, ['Admin', 'Tata Usaha']))
                <div class="menu-item">
                    <a href="{{ route('documents.create') }}"
                        class="menu-link {{ request()->routeIs('documents.create') ? 'active' : '' }}">
                        <i class="fas fa-upload"></i> Unggah Dokumen
                    </a>
                </div>
                @endif

                @if (in_array(auth()->user()->role->name, ['Admin', 'Kepala Sekolah']))
                <div class="menu-item">
                    <a href="{{ route('approvals.index') }}"
                        class="menu-link {{ request()->routeIs('approvals.index*') ? 'active' : '' }}">
                        <i class="fas fa-check-circle"></i> Persetujuan @if (\App\Models\Document::where('status', 'diajukan')->count() > 0)
                            <span
                                style="background: var(--danger); color: white; border-radius: 10px; padding: 2px 6px; font-size: 10px;">{{ \App\Models\Document::where('status', 'diajukan')->count() }}</span>
                        @endif
                    </a>
                </div>
                @endif

                @if (auth()->user()->role->name == 'Admin')
                <div class="menu-label">Klasifikasi</div>
                <div class="menu-item">
                    <a href="{{ route('categories.index') }}"
                        class="menu-link {{ request()->routeIs('categories.index*') ? 'active' : '' }}">
                        <i class="fas fa-folder"></i> Kategori
                    </a>
                </div>
                <div class="menu-item">
                    <a href="{{ route('units.index') }}"
                        class="menu-link {{ request()->routeIs('units.index*') ? 'active' : '' }}">
                        <i class="fas fa-sitemap"></i> Unit Kerja
                    </a>
                </div>
                <div class="menu-item">
                    <a href="{{ route('tags.index') }}"
                        class="menu-link {{ request()->routeIs('tags.index*') ? 'active' : '' }}">
                        <i class="fas fa-tags"></i> Kata Kunci (Tags)
                    </a>
                </div>
                @endif

                <div class="menu-label">Sistem</div>
                @if (auth()->user()->role->name == 'Admin')
                    <div class="menu-item">
                        <a href="{{ route('users.index') }}"
                            class="menu-link {{ request()->routeIs('users.index*') ? 'active' : '' }}">
                            <i class="fas fa-users"></i> Manajemen User
                        </a>
                    </div>
                @endif

                @if (in_array(auth()->user()->role->name, ['Admin', 'Tata Usaha']))
                    <div class="menu-item">
                        <a href="{{ route('reports.index') }}"
                            class="menu-link {{ request()->routeIs('reports.index*') ? 'active' : '' }}">
                            <i class="fas fa-file-invoice"></i> Laporan Pengarsipan
                        </a>
                    </div>
                @endif

                @if (auth()->user()->role->name == 'Admin')
                <div class="menu-item">
                    <a href="{{ route('logs.index') }}"
                        class="menu-link {{ request()->routeIs('logs.index*') ? 'active' : '' }}">
                        <i class="fas fa-history"></i> Log Aktivitas
                    </a>
                </div>
                <div class="menu-item">
                    <a href="{{ route('backups.index') }}"
                        class="menu-link {{ request()->routeIs('backups.index*') ? 'active' : '' }}">
                        <i class="fas fa-database"></i> Backup & Restore
                    </a>
                </div>
                @endif

            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Topbar -->
            <header class="topbar">
                <div class="page-title">@yield('title', 'Dashboard')</div>

                <div class="topbar-right">
                    <div class="user-profile">
                        <div class="user-avatar" style="background-color: var(--secondary);">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="user-info">
                            <span class="user-name">{{ auth()->user()->name }}</span>
                            <span class="user-role">{{ auth()->user()->role->name }}</span>
                        </div>
                    </div>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary" style="background-color: var(--danger);">
                            <i class="fas fa-sign-out-alt"></i> Keluar
                        </button>
                    </form>
                </div>
            </header>

            <!-- Page Body -->
            <div class="content-body">
                @if (session('success'))
                    <div class="badge badge-success"
                        style="width: 100%; padding: 15px; margin-bottom: 20px; text-align: center;">
                        {{ session('success') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>

</html>

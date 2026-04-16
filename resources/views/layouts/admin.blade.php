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
                        <i class="fas fa-th-large"></i> <span>Dashboard</span>
                    </a>
                </div>

                <div class="menu-label">Manajemen File</div>

                @php
                    $sidebarCategories = \App\Models\DocumentCategory::has('documents')->get();
                    $isDocumentActive = request()->routeIs('documents.index*');
                @endphp

                <div class="menu-item has-submenu">
                    <a href="#" class="menu-link toggle-submenu {{ $isDocumentActive ? 'active' : '' }}"
                        onclick="event.preventDefault(); this.nextElementSibling.style.display = this.nextElementSibling.style.display === 'none' ? 'block' : 'none'; this.querySelector('.fa-chevron-down').style.transform = this.nextElementSibling.style.display === 'none' ? 'rotate(0deg)' : 'rotate(180deg)';">
                        <i class="fas fa-folder-open"></i> <span>Dokumen</span>
                        <i class="fas fa-chevron-down ms-auto"
                            style="margin-left: auto; transition: transform 0.3s; {{ $isDocumentActive ? 'transform: rotate(180deg);' : '' }}"></i>
                    </a>
                    <div class="submenu-items" style="{{ $isDocumentActive ? 'display: block;' : 'display: none;' }}">
                        <a href="{{ route('documents.index') }}"
                            class="menu-link {{ request()->routeIs('documents.index*') && !request()->has('category_id') ? 'active' : '' }}"
                            style="padding-left: 45px; font-size: 13px;">
                            <i class="fas fa-list" style="width: 15px; font-size: 12px;"></i> <span>Semua</span>
                        </a>
                        @foreach ($sidebarCategories as $category)
                            <a href="{{ route('documents.index', ['category_id' => $category->id]) }}"
                                class="menu-link {{ request('category_id') == $category->id ? 'active' : '' }}"
                                style="padding-left: 45px; font-size: 13px;">
                                <i class="fas fa-angle-right" style="width: 15px; font-size: 12px;"></i>
                                <span>{{ $category->name }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                @if (in_array(auth()->user()->role->name, ['Admin', 'Tata Usaha']))
                    <div class="menu-item">
                        <a href="{{ route('documents.create') }}"
                            class="menu-link {{ request()->routeIs('documents.create') ? 'active' : '' }}">
                            <i class="fas fa-upload"></i> <span>Unggah Dokumen</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a href="{{ route('documents.bulk') }}"
                            class="menu-link {{ request()->routeIs('documents.bulk') ? 'active' : '' }}">
                            <i class="fas fa-folder-plus"></i> <span>Unggah Bulk</span>
                        </a>
                    </div>
                @endif

                @if (in_array(auth()->user()->role->name, ['Admin', 'Kepala Sekolah', 'Tata Usaha']))
                    <div class="menu-item">
                        <a href="{{ route('approvals.index') }}"
                            class="menu-link {{ request()->routeIs('approvals.index*') ? 'active' : '' }}">
                            <i class="fas fa-check-circle"></i> <span>Persetujuan</span> @if (\App\Models\Document::where('status', 'diajukan')->count() > 0)
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
                            <i class="fas fa-folder"></i> <span>Kategori</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a href="{{ route('units.index') }}"
                            class="menu-link {{ request()->routeIs('units.index*') ? 'active' : '' }}">
                            <i class="fas fa-sitemap"></i> <span>Unit Kerja</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a href="{{ route('tags.index') }}"
                            class="menu-link {{ request()->routeIs('tags.index*') ? 'active' : '' }}">
                            <i class="fas fa-tags"></i> <span>Kata Kunci (Tags)</span>
                        </a>
                    </div>
                @endif

                <div class="menu-label">Sistem</div>
                @if (auth()->user()->role->name == 'Admin')
                    <div class="menu-item">
                        <a href="{{ route('users.index') }}"
                            class="menu-link {{ request()->routeIs('users.index*') ? 'active' : '' }}">
                            <i class="fas fa-users"></i> <span>Manajemen User</span>
                        </a>
                    </div>
                @endif

                @if (in_array(auth()->user()->role->name, ['Admin', 'Tata Usaha']))
                    <div class="menu-item">
                        <a href="{{ route('reports.index') }}"
                            class="menu-link {{ request()->routeIs('reports.index*') ? 'active' : '' }}">
                            <i class="fas fa-file-invoice"></i> <span>Laporan Pengarsipan</span>
                        </a>
                    </div>
                @endif

                @if (auth()->user()->role->name == 'Admin')
                    <div class="menu-item">
                        <a href="{{ route('logs.index') }}"
                            class="menu-link {{ request()->routeIs('logs.index*') ? 'active' : '' }}">
                            <i class="fas fa-history"></i> <span>Log Aktivitas</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a href="{{ route('backups.index') }}"
                            class="menu-link {{ request()->routeIs('backups.index*') ? 'active' : '' }}">
                            <i class="fas fa-database"></i> <span>Backup & Restore</span>
                        </a>
                    </div>
                @endif

            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Topbar -->
            <header class="topbar">
                <div style="display: flex; align-items: center; gap: 20px;">
                    <button id="sidebarToggle" style="background: none; border: none; font-size: 20px; cursor: pointer; color: var(--text-main); display: flex; align-items: center; justify-content: center; padding: 5px; border-radius: 4px; transition: background 0.2s;">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="page-title">@yield('title', 'Dashboard')</div>
                </div>

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

    <script>
        (function() {
            const initSidebar = () => {
                const toggleBtn = document.getElementById('sidebarToggle');
                const sidebar = document.querySelector('.sidebar');
                const mainContent = document.querySelector('.main-content');
                
                if (!toggleBtn || !sidebar || !mainContent) return;

                // Fungsi untuk menerapkan state
                const setSidebarState = (isCollapsed) => {
                    if (isCollapsed) {
                        sidebar.classList.add('collapsed');
                        mainContent.classList.add('sidebar-collapsed');
                    } else {
                        sidebar.classList.remove('collapsed');
                        mainContent.classList.remove('sidebar-collapsed');
                    }
                };

                // Cek status awal
                const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
                setSidebarState(isCollapsed);

                // Event Listener
                toggleBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const currentState = sidebar.classList.contains('collapsed');
                    const newState = !currentState;
                    
                    setSidebarState(newState);
                    localStorage.setItem('sidebar-collapsed', newState);
                });

                // Hover effect
                toggleBtn.style.transition = 'background 0.2s';
                toggleBtn.addEventListener('mouseenter', () => toggleBtn.style.background = 'rgba(0,0,0,0.05)');
                toggleBtn.addEventListener('mouseleave', () => toggleBtn.style.background = 'none');
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initSidebar);
            } else {
                initSidebar();
            }
        })();
    </script>
    @stack('scripts')
</body>

</html>

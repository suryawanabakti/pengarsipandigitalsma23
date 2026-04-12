@extends('layouts.admin')

@section('title', 'Manajemen Pengguna')

@section('content')
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Daftar Pengguna</h2>
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Tambah Pengguna
            </a>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Terdaftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div class="user-avatar" style="width: 32px; height: 32px; font-size: 12px; background-color: var(--secondary);">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <span style="font-weight: 600;">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <div style="display: flex; flex-direction: column; gap: 4px;">
                                    <span class="badge badge-info" style="align-self: flex-start;">{{ $user->role->name }}</span>
                                    <div style="display: flex; gap: 4px; flex-wrap: wrap;">
                                        @if($user->can_view) <span title="Lihat" style="font-size: 10px; color: #059669; background: #ecfdf5; padding: 2px 5px; border-radius: 4px;">L</span> @endif
                                        @if($user->can_upload) <span title="Unggah" style="font-size: 10px; color: #2563eb; background: #eff6ff; padding: 2px 5px; border-radius: 4px;">U</span> @endif
                                        @if($user->can_edit) <span title="Edit" style="font-size: 10px; color: #d97706; background: #fffbeb; padding: 2px 5px; border-radius: 4px;">E</span> @endif
                                        @if($user->can_delete) <span title="Hapus" style="font-size: 10px; color: #dc2626; background: #fef2f2; padding: 2px 5px; border-radius: 4px;">H</span> @endif
                                        @if($user->can_download) <span title="Unduh" style="font-size: 10px; color: #7c3aed; background: #f5f3ff; padding: 2px 5px; border-radius: 4px;">D</span> @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $user->created_at->format('d M Y') }}</td>
                            <td>
                                <div style="display: flex; gap: 8px;">
                                    <a href="{{ route('users.edit', $user) }}" class="btn" style="padding: 6px; font-size: 12px; background: var(--warning); color: white;" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn" style="padding: 6px; font-size: 12px; background: var(--danger); color: white;" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div style="padding: 20px; border-top: 1px solid var(--border);">
                {{ $users->links() }}
            </div>
        @endif
    </div>
@endsection

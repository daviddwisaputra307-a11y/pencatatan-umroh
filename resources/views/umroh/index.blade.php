@extends('layouts.app')

@section('title', 'Pencatatan Umroh')

@section('content')
<div class="glass rounded-3xl p-6 md:p-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold tracking-tight">Pencatatan Umroh</h1>
            <p class="hint mt-1">Data: NIK, Nama, Tanggal umroh, Jenis umroh.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('umroh.create') }}"
               class="btn-primary px-4 py-2 rounded-xl font-semibold">
               + Tambah Data
            </a>
        </div>
    </div>

    {{-- Search --}}
    <form method="GET" action="{{ route('umroh.index') }}" class="mt-6 flex gap-2">
        <input type="text" name="q" value="{{ request('q') }}"
               placeholder="Cari NIK / Nama..."
               class="w-full rounded-xl px-4 py-2 outline-none" />
        <button class="btn px-4 py-2 rounded-xl font-semibold">Cari</button>
        @if(request('q'))
            <a href="{{ route('umroh.index') }}" class="btn px-4 py-2 rounded-xl font-semibold">Reset</a>
        @endif
    </form>

    {{-- Flash --}}
    @if(session('success'))
        <div class="mt-4 soft-border rounded-xl p-3 bg-green-500/10 border-green-500/30">
            <span class="text-green-200 font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Table --}}
    <div class="mt-6 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-white/70">
                <tr>
                    <th class="text-left py-3 px-3">NIK</th>
                    <th class="text-left py-3 px-3">Nama</th>
                    <th class="text-left py-3 px-3">Tanggal Umroh</th>
                    <th class="text-left py-3 px-3">Jenis</th>
                    <th class="text-left py-3 px-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="space-y-2">
                @forelse($rows as $row)
                    <tr class="table-row">
                        <td class="py-3 px-3">
                            <span class="pill inline-flex px-3 py-1 rounded-full font-semibold">
                                {{ $row->NIK }}
                            </span>
                        </td>
                        <td class="py-3 px-3">
                            <span class="font-semibold">{{ $row->Nama ?? '-' }}</span>
                        </td>
                        <td class="py-3 px-3">
                            {{ \Carbon\Carbon::parse($row->tgl_umroh)->format('d-m-Y') }}
                        </td>
                        <td class="py-3 px-3">
                            <span class="pill inline-flex px-3 py-1 rounded-full">
                                {{ $row->jenis_umroh }}
                            </span>
                        </td>
                        <td class="py-3 px-3">
                            <div class="flex gap-2">
                                <a href="{{ route('umroh.edit', $row->id) }}"
                                   class="btn px-3 py-1.5 rounded-lg font-semibold">Edit</a>

                                <form action="{{ route('umroh.destroy', $row->id) }}" method="POST"
                                      onsubmit="return confirm('Yakin hapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-danger px-3 py-1.5 rounded-lg font-semibold">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-10 text-center text-white/60">
                            Belum ada data.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $rows->links() }}
    </div>
</div>
@endsection

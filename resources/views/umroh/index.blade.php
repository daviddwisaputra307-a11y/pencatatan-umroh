@extends('layouts.app')

@section('title', 'Pencatatan Umroh')

@section('content')
<div class="glass rounded-3xl p-6 md:p-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold tracking-tight">Pencatatan Umroh</h1>
            <p class="hint mt-1">Cari bisa pakai NIK / Nama / Tanggal (YYYY-MM-DD) / Jenis (Pribadi/RSI).</p>
        </div>

        <a href="{{ route('umroh.create') }}"
           class="btn-primary px-4 py-2 rounded-xl font-semibold">
            + Tambah Data
        </a>
    </div>

    {{-- Search --}}
    <form method="GET" action="{{ route('umroh.index') }}" class="mt-6 flex flex-col md:flex-row gap-2">
        <input type="text" name="q" value="{{ $q ?? '' }}"
               placeholder="Cari NIK / Nama / 2025-12-15 / Pribadi / RSI..."
               class="w-full rounded-xl px-4 py-2 outline-none" />
        <div class="flex gap-2">
            <button class="btn px-4 py-2 rounded-xl font-semibold" type="submit">Cari</button>
            <a class="btn px-4 py-2 rounded-xl font-semibold" href="{{ route('umroh.index') }}">Reset</a>
        </div>
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
                    <th class="text-left py-3 px-3">Tgl Awal</th>
                    <th class="text-left py-3 px-3">Tgl Akhir</th>
                    <th class="text-left py-3 px-3">Jenis</th>
                    <th class="text-left py-3 px-3">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($rows as $r)
                    <tr class="table-row">
                        <td class="py-3 px-3">
                            <span class="pill inline-flex px-3 py-1 rounded-full font-semibold">
                                {{ $r->NIK }}
                            </span>
                        </td>

                        <td class="py-3 px-3">
                            <span class="font-semibold">{{ $r->Nama ?? '-' }}</span>
                        </td>

                        <td class="py-3 px-3">
                            {{ $r->tgl_awal ? \Carbon\Carbon::parse($r->tgl_awal)->format('d-m-Y') : '-' }}
                        </td>

                        <td class="py-3 px-3">
                            {{ $r->tgl_akhir ? \Carbon\Carbon::parse($r->tgl_akhir)->format('d-m-Y') : '-' }}
                        </td>

                        <td class="py-3 px-3">
                            <span class="pill inline-flex px-3 py-1 rounded-full">
                                {{ $r->jenis_umroh }}
                            </span>
                        </td>

                        <td class="py-3 px-3">
                            <div class="flex gap-2">
                                <a href="{{ route('umroh.edit', $r->id) }}"
                                   class="btn px-3 py-1.5 rounded-lg font-semibold">
                                    Edit
                                </a>

                                <form action="{{ route('umroh.destroy', $r->id) }}" method="POST"
                                      onsubmit="return confirm('Yakin hapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-danger px-3 py-1.5 rounded-lg font-semibold" type="submit">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-10 text-center text-white/60">
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

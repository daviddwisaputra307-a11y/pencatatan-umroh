@extends('layouts.app')

@section('title', 'Pencatatan Umroh')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8 text-white">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold tracking-tight">Pencatatan Umroh</h1>
            <p class="text-white/60 text-sm mt-1">
                Filter berdasarkan NIK, Nama, Tgl Awal/Akhir, dan Jenis Umroh.
            </p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('umroh.create') }}"
               class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 font-semibold">
                + Tambah Data
            </a>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="mt-4 p-3 rounded-xl bg-green-500/10 border border-green-500/30 text-green-200 font-semibold">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mt-4 p-3 rounded-xl bg-red-500/10 border border-red-500/30 text-red-200 font-semibold">
            {{ session('error') }}
        </div>
    @endif

    {{-- Form Filter/Search --}}
    <form method="GET" action="{{ route('umroh.index') }}"
          class="mt-6 grid grid-cols-1 md:grid-cols-5 gap-3 p-4 rounded-2xl bg-white/5 border border-white/10">

        <div>
            <label class="text-white/70 text-sm">NIK</label>
            <input type="text" name="nik" value="{{ $nik ?? '' }}"
                   placeholder="search"
                   class="w-full mt-2 rounded-xl px-4 py-2 bg-slate-800 text-slate-100 outline-none border border-white/10 focus:border-white/30" />
        </div>

        <div>
            <label class="text-white/70 text-sm">Nama</label>
            <input type="text" name="nama" value="{{ $nama ?? '' }}"
                   placeholder="search"
                   class="w-full mt-2 rounded-xl px-4 py-2 bg-slate-800 text-slate-100 outline-none border border-white/10 focus:border-white/30" />
        </div>

        <div>
            <label class="text-white/70 text-sm">Tgl Awal</label>
            <input type="date" name="tgl_awal" value="{{ $awal ?? '' }}"
                   class="w-full mt-2 rounded-xl px-4 py-2 bg-slate-800 text-slate-100 outline-none border border-white/10 focus:border-white/30" />
        </div>

        <div>
            <label class="text-white/70 text-sm">Tgl Akhir</label>
            <input type="date" name="tgl_akhir" value="{{ $akhir ?? '' }}"
                   class="w-full mt-2 rounded-xl px-4 py-2 bg-slate-800 text-slate-100 outline-none border border-white/10 focus:border-white/30" />
        </div>

        <div>
            <label class="text-white/70 text-sm">Jenis Umroh</label>
            <select name="jenis"
                    class="w-full mt-2 rounded-xl px-4 py-2 bg-slate-800 text-slate-100 outline-none border border-white/10 focus:border-white/30">
                <option value="">Semua</option>
                <option value="Pribadi" @selected(($jenis ?? '') === 'Pribadi')>Pribadi</option>
                <option value="RSI" @selected(($jenis ?? '') === 'RSI')>RSI</option>
            </select>
        </div>

        @php
            $hasFilter = !empty($nik) || !empty($nama) || !empty($awal) || !empty($akhir) || !empty($jenis);
        @endphp

        <div class="md:col-span-5 flex flex-wrap gap-2">
            <button type="submit"
                    class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 font-semibold">
                Cari
            </button>

            <a href="{{ route('umroh.index') }}"
               class="px-5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 font-semibold border border-white/10">
                Reset
            </a>

            {{-- Tombol Export PDF: ngikut query filter --}}
            <a href="{{ route('umroh.export.pdf', request()->query()) }}"
               class="px-5 py-2 rounded-xl font-semibold border border-white/10
                      {{ $hasFilter ? 'bg-emerald-600 hover:bg-emerald-500 text-white' : 'bg-slate-700 text-white/50 cursor-not-allowed pointer-events-none' }}">
                Export PDF
            </a>

            @if(!$hasFilter)
                <span class="text-white/40 text-sm self-center">
                    (isi dulu biar PDF-nya sesuai pencarian)
                </span>
            @endif
        </div>
    </form>

    {{-- Table --}}
    <div class="mt-6 overflow-x-auto rounded-2xl bg-white/5 border border-white/10">
        <table class="w-full text-sm">
            <thead class="text-white/70">
                <tr>
                    <th class="text-left py-3 px-4">NIK</th>
                    <th class="text-left py-3 px-4">Nama</th>
                    <th class="text-left py-3 px-4">Tgl Awal</th>
                    <th class="text-left py-3 px-4">Tgl Akhir</th>
                    <th class="text-left py-3 px-4">Jenis</th>
                    <th class="text-left py-3 px-4">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($rows as $r)
                    <tr class="border-t border-white/10 hover:bg-white/5 transition">
                        <td class="py-3 px-4 font-semibold">{{ $r->NIK }}</td>
                        <td class="py-3 px-4">{{ $r->Nama ?? '-' }}</td>

                        <td class="py-3 px-4">
                            {{ $r->tgl_awal ? \Carbon\Carbon::parse($r->tgl_awal)->format('d-m-Y') : '-' }}
                        </td>

                        <td class="py-3 px-4">
                            {{ $r->tgl_akhir ? \Carbon\Carbon::parse($r->tgl_akhir)->format('d-m-Y') : '-' }}
                        </td>

                        <td class="py-3 px-4">
                            <span class="inline-flex px-3 py-1 rounded-full bg-slate-800 border border-white/10">
                                {{ $r->jenis_umroh }}
                            </span>
                        </td>

                        <td class="py-3 px-4">
                            <div class="flex gap-2">
                                <a href="{{ route('umroh.edit', $r->id) }}"
                                   class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 font-semibold border border-white/10">
                                    Edit
                                </a>

                                <form action="{{ route('umroh.destroy', $r->id) }}" method="POST"
                                      onsubmit="return confirm('Yakin hapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="px-3 py-1.5 rounded-lg bg-red-600 hover:bg-red-500 font-semibold">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="border-t border-white/10">
                        <td colspan="6" class="py-10 px-4 text-center text-white/60">
                            Data tidak ditemukan.
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

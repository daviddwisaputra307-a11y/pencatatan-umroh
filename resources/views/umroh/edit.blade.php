@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-950 text-slate-100">
    <div class="max-w-3xl mx-auto px-4 py-10">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Edit Data Umroh</h1>
                <p class="text-slate-400 text-sm mt-1">Perbarui tanggal & jenis umroh.</p>
            </div>

            <a href="{{ route('umroh.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-100 text-sm">
                ← Kembali
            </a>
        </div>

        {{-- Error box --}}
        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-red-500/40 bg-red-500/10 p-4">
                <div class="font-semibold text-red-300 mb-2">Ada error:</div>
                <ul class="list-disc list-inside text-sm text-red-200 space-y-1">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Card --}}
        <div class="rounded-2xl border border-slate-800 bg-slate-900/40 p-6 shadow-lg">
            <form method="POST" action="{{ route('umroh.update', $row->id) }}" class="space-y-5">
                @csrf
                @method('PUT')

                {{-- NIK --}}
                <div>
                    <label class="text-white/70 text-sm">NIK </label>

                    {{-- tampil --}}
                    <input
                        value="{{ $row->NIK }}"
                        disabled
                        class="w-full mt-2 rounded-xl px-4 py-2 bg-slate-800 text-slate-200 opacity-80 cursor-not-allowed outline-none"
                    />

                    {{-- tetap dikirim --}}
                    <input type="hidden" name="nik" value="{{ $row->NIK }}">
                </div>

                {{-- Nama --}}
                <div>
                    <label class="text-white/70 text-sm">Nama</label>

                    {{-- tampil --}}
                    <input
                        value="{{ $row->Nama ?? '' }}"
                        disabled
                        class="w-full mt-2 rounded-xl px-4 py-2 bg-slate-800 text-slate-200 opacity-80 cursor-not-allowed outline-none"
                    />

                    {{-- tetap dikirim (kalau controller butuh) --}}
                    <input type="hidden" name="nama" value="{{ $row->Nama ?? '' }}">
                </div>

                {{-- Grid tanggal + jenis --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Tanggal Awal --}}
                    <div>
                        <label class="text-white/70 text-sm">Tanggal Awal</label>
                        <input
                            type="date"
                            name="tgl_awal"
                            value="{{ old('tgl_awal', \Carbon\Carbon::parse($row->tgl_awal)->format('Y-m-d')) }}"
                            class="w-full mt-2 rounded-xl px-4 py-2 bg-slate-800 text-slate-100 outline-none focus:ring-2 focus:ring-slate-600"
                        />
                        @error('tgl_awal')
                            <div class="text-red-300 text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tanggal Akhir --}}
                    <div>
                        <label class="text-white/70 text-sm">Tanggal Akhir</label>
                        <input
                            type="date"
                            name="tgl_akhir"
                            value="{{ old('tgl_akhir', \Carbon\Carbon::parse($row->tgl_akhir)->format('Y-m-d')) }}"
                            class="w-full mt-2 rounded-xl px-4 py-2 bg-slate-800 text-slate-100 outline-none focus:ring-2 focus:ring-slate-600"
                        />
                        @error('tgl_akhir')
                            <div class="text-red-300 text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Jenis Umroh --}}
                <div>
                    <label class="text-white/70 text-sm">Jenis Umroh</label>

                    @php
                        $val = old('jenis_umroh', $row->jenis_umroh);
                    @endphp

                    <select
                        name="jenis_umroh"
                        class="w-full mt-2 rounded-xl px-4 py-2 bg-slate-800 text-slate-100 outline-none focus:ring-2 focus:ring-slate-600"
                    >
                        <option value="Pribadi" @selected($val === 'Pribadi')>Pribadi</option>
                        <option value="RSI" @selected($val === 'RSI')>RSI</option>
                    </select>

                    @error('jenis_umroh')
                        <div class="text-red-300 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Buttons --}}
                <div class="flex gap-2 pt-2">
                    <button
                        type="submit"
                        class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 font-semibold"
                    >
                        Update
                    </button>

                    <a
                        href="{{ route('umroh.index') }}"
                        class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 font-semibold"
                    >
                        Batal
                    </a>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Tambah Data Umroh')

@section('content')
<div class="max-w-3xl mx-auto p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Tambah Data Umroh</h1>
        <a href="{{ route('umroh.index') }}"
           class="px-4 py-2 rounded-xl bg-slate-700 hover:bg-slate-600 border border-white/10">
            ← Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-4 p-4 rounded-xl border border-red-500/30 bg-red-500/10">
            <div class="font-semibold mb-2">Ada error nih:</div>
            <ul class="list-disc ml-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="glass rounded-2xl p-6 border border-white/10">
        <form action="{{ route('umroh.store') }}" method="POST" class="space-y-4">
            @csrf

            {{-- Dropdown Personel (NIK + Nama) --}}
            <div>
                <label class="block text-sm mb-1">Pilih ( NIK + Nama )</label>
                <select id="personel_select"
                        class="w-full px-4 py-3 rounded-xl bg-slate-800 border border-white/10 focus:outline-none">
                    <option value="">-- pilih personel --</option>
                    @foreach($personel as $p)
                        <option
                            value="{{ $p->NIK }}"
                            data-nama="{{ $p->NM_PERSON }}"
                            {{ old('nik') == $p->NIK ? 'selected' : '' }}
                        >
                            {{ $p->NIK }} - {{ $p->NM_PERSON }}
                        </option>
                    @endforeach
                </select>
                <p class="text-xs opacity-70 mt-1">Pilih salah satu, NIK & Nama auto keisi.</p>
            </div>

            {{-- NIK (auto) --}}
            <div>
                <label class="block text-sm mb-1">NIK (auto)</label>
                <input type="text"
                       id="nik"
                       name="nik"
                       value="{{ old('nik') }}"
                       readonly
                       class="w-full px-4 py-3 rounded-xl bg-slate-800 border border-white/10 focus:outline-none opacity-80" />
            </div>

            {{-- Nama (auto) --}}
            <div>
                <label class="block text-sm mb-1">Nama (auto)</label>
                <input type="text"
                       id="nama"
                       name="nama"
                       value="{{ old('nama') }}"
                       readonly
                       class="w-full px-4 py-3 rounded-xl bg-slate-800 border border-white/10 focus:outline-none opacity-80" />
            </div>

            {{-- Tgl Awal --}}
            <div>
                <label class="block text-sm mb-1">Tanggal Awal</label>
                <input type="date"
                       name="tgl_awal"
                       value="{{ old('tgl_awal') }}"
                       class="w-full px-4 py-3 rounded-xl bg-slate-800 border border-white/10 focus:outline-none" />
            </div>

            {{-- Tgl Akhir --}}
            <div>
                <label class="block text-sm mb-1">Tanggal Akhir</label>
                <input type="date"
                       name="tgl_akhir"
                       value="{{ old('tgl_akhir') }}"
                       class="w-full px-4 py-3 rounded-xl bg-slate-800 border border-white/10 focus:outline-none" />
            </div>

            {{-- Jenis --}}
            <div>
                <label class="block text-sm mb-1">Jenis Umroh</label>
                <select name="jenis_umroh"
                        class="w-full px-4 py-3 rounded-xl bg-slate-800 border border-white/10 focus:outline-none">
                    <option value="">-- pilih jenis --</option>
                    @foreach($jenisList as $j)
                        <option value="{{ $j }}" {{ old('jenis_umroh') == $j ? 'selected' : '' }}>
                            {{ $j }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="px-5 py-3 rounded-xl bg-sky-600 hover:bg-sky-500 font-semibold">
                    Simpan
                </button>
                <a href="{{ route('umroh.index') }}"
                   class="px-5 py-3 rounded-xl bg-slate-700 hover:bg-slate-600 border border-white/10">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

{{-- JS auto isi NIK & Nama --}}
<script>
    const select = document.getElementById('personel_select');
    const nikInput = document.getElementById('nik');
    const namaInput = document.getElementById('nama');

    function fillFromSelect() {
        const opt = select.options[select.selectedIndex];
        const nik = opt.value || '';
        const nama = opt.getAttribute('data-nama') || '';

        nikInput.value = nik;
        namaInput.value = nama;
    }

    select.addEventListener('change', fillFromSelect);

    // biar kalau ada old('nik') (habis error validate) tetap keisi
    window.addEventListener('DOMContentLoaded', fillFromSelect);
</script>
@endsection

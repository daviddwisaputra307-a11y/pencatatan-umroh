@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6">
  <div class="rounded-2xl bg-slate-900/40 border border-white/10 p-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Tambah Data Umroh</h1>
      <a href="{{ route('umroh.index') }}" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/15">
        ← Kembali
      </a>
    </div>

    @if ($errors->any())
      <div class="mt-4 rounded-xl border border-red-500/40 bg-red-500/10 p-4">
        <div class="font-semibold mb-2">Ada error:</div>
        <ul class="list-disc ml-6 text-red-200">
          @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form class="mt-6 space-y-4" method="POST" action="{{ route('umroh.store') }}">
      @csrf

      {{-- NIK --}}
      <div>
        <label class="text-white/70 text-sm">NIK</label>
        <input
          type="text"
          name="nik"
          value="{{ old('nik') }}"
          inputmode="numeric"
          class="w-full mt-2 rounded-xl px-4 py-2 bg-slate-950/40 border border-white/10 outline-none"
          placeholder="Masukkan 10 digit"
        >
        <div class="text-xs text-white/50 mt-1">* Wajib angka 10 digit.</div>
      </div>

      {{-- Nama --}}
      <div>
        <label class="text-white/70 text-sm">Nama</label>
        <input
          type="text"
          name="nama"
          value="{{ old('nama') }}"
          class="w-full mt-2 rounded-xl px-4 py-2 bg-slate-950/40 border border-white/10 outline-none"
          placeholder="Masukkan nama"
        >
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- Tanggal --}}
        <div>
          <label class="text-white/70 text-sm">Tanggal Umroh</label>
          <input
            type="date"
            name="tgl_umroh"
            value="{{ old('tgl_umroh') }}"
            class="w-full mt-2 rounded-xl px-4 py-2 bg-slate-950/40 border border-white/10 outline-none"
          >
        </div>

        {{-- Jenis --}}
        <div>
          <label class="text-white/70 text-sm">Jenis Umroh</label>
          @php $val = old('jenis_umroh','Pribadi'); @endphp
          <select
            name="jenis_umroh"
            class="w-full mt-2 rounded-xl px-4 py-2 bg-slate-950/40 border border-white/10 outline-none"
          >
            <option value="Pribadi" @selected($val==='Pribadi')>Pribadi</option>
            <option value="RSI" @selected($val==='RSI')>RSI</option>
          </select>
        </div>
      </div>

      <div class="flex gap-2 pt-2">
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 font-semibold">
          Simpan
        </button>
        <a href="{{ route('umroh.index') }}" class="px-5 py-2.5 rounded-xl bg-white/10 hover:bg-white/15 font-semibold">
          Batal
        </a>
      </div>

    </form>
  </div>
</div>
@endsection

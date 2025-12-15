@extends('layouts.app')

@section('title', 'Edit Data Umroh')

@section('content')
<div class="glass rounded-3xl p-6 md:p-8 max-w-3xl mx-auto">
    <div class="flex items-center justify-between gap-3">
        <h1 class="text-2xl font-bold">Edit Data Umroh</h1>
        <a href="{{ route('umroh.index') }}" class="btn px-4 py-2 rounded-xl font-semibold">← Kembali</a>
    </div>

    @if($errors->any())
        <div class="mt-4 soft-border rounded-xl p-4 bg-red-500/10 border-red-500/30">
            <div class="font-semibold text-red-200 mb-2">Ada error:</div>
            <ul class="list-disc ml-5 err">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        // biar old() kepake kalau validasi gagal
        $valJenis = old('jenis_umroh', $row->jenis_umroh);
        $valTgl   = old('tgl_umroh', \Carbon\Carbon::parse($row->tgl_umroh)->format('Y-m-d'));
    @endphp

    <form method="POST" action="{{ route('umroh.update', $row->id) }}" class="mt-6 space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label class="text-white/70 text-sm">NIK</label>
            <input value="{{ $row->NIK }}" disabled
                   class="w-full mt-2 rounded-xl px-4 py-2 outline-none opacity-70 cursor-not-allowed" />
        </div>

        <div>
            <label class="text-white/70 text-sm">Nama</label>
            <input value="{{ $row->Nama }}" disabled
                   class="w-full mt-2 rounded-xl px-4 py-2 outline-none opacity-70 cursor-not-allowed" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-white/70 text-sm">Tanggal Umroh (boleh diubah)</label>
                <input type="date" name="tgl_umroh"
                       value="{{ $valTgl }}"
                       class="w-full mt-2 rounded-xl px-4 py-2 outline-none" />
            </div>

            <div>
                <label class="text-white/70 text-sm">Jenis Umroh</label>
                <select name="jenis_umroh"
                        class="select w-full mt-2 rounded-xl px-4 py-2 outline-none">
                    <option value="Pribadi" @selected($valJenis == 'Pribadi')>Pribadi</option>
                    <option value="RSI"     @selected($valJenis == 'RSI')>RSI</option>
                </select>
            </div>
        </div>

        <div class="flex gap-2 pt-2">
            <button class="btn-primary px-5 py-2.5 rounded-xl font-semibold">Update</button>
            <a href="{{ route('umroh.index') }}" class="btn px-5 py-2.5 rounded-xl font-semibold">Batal</a>
        </div>
    </form>
</div>
@endsection

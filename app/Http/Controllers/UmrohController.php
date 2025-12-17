<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UmrohController extends Controller
{
    protected $conn;

    public function __construct()
    {
        // sesuaikan kalau nama connection kamu beda
        // kalau default, bisa: $this->conn = DB::connection();
        $this->conn = DB::connection('sqlsrv');
    }

    public function index(Request $request)
    {
        $q          = trim((string) $request->get('q', ''));
        $jenis      = trim((string) $request->get('jenis', ''));
        $tglAwalIn  = trim((string) $request->get('tgl_awal', ''));
        $tglAkhirIn = trim((string) $request->get('tgl_akhir', ''));

        // Normalisasi input tanggal bebas format untuk search "q"
        $qDate = $this->normalizeDate($q); // bisa null

        // Normalisasi filter range (dari input form)
        $tglAwal = $this->normalizeDate($tglAwalIn);   // bisa null
        $tglAkhir = $this->normalizeDate($tglAkhirIn); // bisa null

        $query = $this->conn->table('dbo.UMROH as u')
            ->leftJoin('dbo.PERSONEL as p', 'p.NIK', '=', 'u.NIK')
            ->select(
                'u.id',
                'u.NIK',
                'p.Nama',
                'u.tgl_awal',
                'u.tgl_akhir',
                'u.jenis_umroh'
            );

        // Filter dropdown jenis (kalau ada)
        if ($jenis !== '') {
            $query->where('u.jenis_umroh', $jenis);
        }

        // Search bebas: NIK / Nama / Jenis / Tanggal (tgl_awal atau tgl_akhir)
        if ($q !== '') {
            $query->where(function ($w) use ($q, $qDate) {
                $w->where('u.NIK', 'like', "%{$q}%")
                  ->orWhere('p.Nama', 'like', "%{$q}%")
                  ->orWhere('u.jenis_umroh', 'like', "%{$q}%");

                if ($qDate) {
                    // Aman buat SQL Server walaupun kolom DATE/DATETIME atau VARCHAR
                    $w->orWhereRaw("TRY_CONVERT(date, u.tgl_awal) = ?", [$qDate])
                      ->orWhereRaw("TRY_CONVERT(date, u.tgl_akhir) = ?", [$qDate]);
                }
            });
        }

        // Filter range (opsional):
        // Kalau user isi tgl_awal dan/atau tgl_akhir di form,
        // ini akan filter data berdasarkan tgl_awal / tgl_akhir
        if ($tglAwal) {
            $query->whereRaw("TRY_CONVERT(date, u.tgl_awal) >= ?", [$tglAwal]);
        }

        if ($tglAkhir) {
            $query->whereRaw("TRY_CONVERT(date, u.tgl_akhir) <= ?", [$tglAkhir]);
        }

        $rows = $query
            ->orderByDesc('u.id')
            ->paginate(10)
            ->withQueryString();

        return view('umroh.index', [
            'rows'      => $rows,
            'q'         => $q,
            'jenis'     => $jenis,
            'tgl_awal'  => $tglAwalIn,
            'tgl_akhir' => $tglAkhirIn,
        ]);
    }

    public function create()
    {
        return view('umroh.create');
    }

    public function store(Request $request)
    {
        // Validasi
        $request->validate([
            'nik'        => ['required', 'digits:10'],
            'nama'       => ['required', 'string', 'max:100'],
            'tgl_awal'   => ['required', 'date'],
            'tgl_akhir'  => ['required', 'date', 'after_or_equal:tgl_awal'],
            'jenis_umroh'=> ['required', 'in:Pribadi,RSI'],
        ]);

        $nik   = (string) $request->nik;
        $nama  = (string) $request->nama;

        // Pastikan format YYYY-MM-DD biar aman
        $tgl_awal  = Carbon::parse($request->tgl_awal)->format('Y-m-d');
        $tgl_akhir = Carbon::parse($request->tgl_akhir)->format('Y-m-d');
        $jenis     = (string) $request->jenis_umroh;

        // 1) UPSERT PERSONEL (kalau NIK sudah ada -> update Nama, kalau belum -> insert)
        $exists = $this->conn->table('dbo.PERSONEL')->where('NIK', $nik)->exists();

        if ($exists) {
            $this->conn->table('dbo.PERSONEL')
                ->where('NIK', $nik)
                ->update(['Nama' => $nama]);
        } else {
            $this->conn->table('dbo.PERSONEL')->insert([
                'NIK'  => $nik,
                'Nama' => $nama,
            ]);
        }

        // 2) INSERT UMROH
        $this->conn->table('dbo.UMROH')->insert([
            'NIK'        => $nik,
            'tgl_awal'   => $tgl_awal,
            'tgl_akhir'  => $tgl_akhir,
            'jenis_umroh'=> $jenis,
        ]);

        return redirect()->route('umroh.index')->with('success', 'Data umroh berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $row = $this->conn->table('dbo.UMROH as u')
            ->leftJoin('dbo.PERSONEL as p', 'p.NIK', '=', 'u.NIK')
            ->select(
                'u.id',
                'u.NIK',
                'p.Nama',
                'u.tgl_awal',
                'u.tgl_akhir',
                'u.jenis_umroh'
            )
            ->where('u.id', $id)
            ->first();

        if (!$row) {
            abort(404);
        }

        return view('umroh.edit', compact('row'));
    }

    public function update(Request $request, $id)
    {
        // Validasi (NIK & nama biasanya read-only, tapi tetap validasi)
        $request->validate([
            'nik'        => ['required', 'digits:10'],
            'nama'       => ['required', 'string', 'max:100'],
            'tgl_awal'   => ['required', 'date'],
            'tgl_akhir'  => ['required', 'date', 'after_or_equal:tgl_awal'],
            'jenis_umroh'=> ['required', 'in:Pribadi,RSI'],
        ]);

        $nik   = (string) $request->nik;
        $nama  = (string) $request->nama;

        $tgl_awal  = Carbon::parse($request->tgl_awal)->format('Y-m-d');
        $tgl_akhir = Carbon::parse($request->tgl_akhir)->format('Y-m-d');
        $jenis     = (string) $request->jenis_umroh;

        // Update PERSONEL biar nama tetap sinkron
        $this->conn->table('dbo.PERSONEL')
            ->where('NIK', $nik)
            ->update(['Nama' => $nama]);

        // Update UMROH
        $updated = $this->conn->table('dbo.UMROH')
            ->where('id', $id)
            ->update([
                'tgl_awal'   => $tgl_awal,
                'tgl_akhir'  => $tgl_akhir,
                'jenis_umroh'=> $jenis,
            ]);

        if (!$updated) {
            return back()->with('error', 'Gagal update: data tidak ditemukan / tidak berubah.');
        }

        return redirect()->route('umroh.index')->with('success', 'Data umroh berhasil diupdate.');
    }

    public function destroy($id)
    {
        $this->conn->table('dbo.UMROH')->where('id', $id)->delete();
        return redirect()->route('umroh.index')->with('success', 'Data umroh berhasil dihapus.');
    }

    /**
     * Normalize date string into 'Y-m-d'
     * Accepts: Y-m-d, d-m-Y, d/m/Y
     * Returns null if invalid.
     */
    private function normalizeDate(?string $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') return null;

        $formats = ['Y-m-d', 'd-m-Y', 'd/m/Y'];

        foreach ($formats as $fmt) {
            try {
                return Carbon::createFromFormat($fmt, $value)->format('Y-m-d');
            } catch (\Exception $e) {
                // continue
            }
        }

        // fallback (kalau user input aneh tapi masih bisa di-parse Carbon)
        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}

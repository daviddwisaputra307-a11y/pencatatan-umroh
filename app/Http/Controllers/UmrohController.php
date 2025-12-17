<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UmrohController extends Controller
{
    private $conn;

    public function __construct()
    {
        $this->conn = DB::connection('sqlsrv');
    }

    public function index(Request $request)
{
    $conn = $this->conn;

    $nik   = trim($request->get('nik', ''));
    $nama  = trim($request->get('nama', ''));
    $awal  = $request->get('tgl_awal', '');
    $akhir = $request->get('tgl_akhir', '');
    $jenis = $request->get('jenis', '');

    $q = $conn->table('dbo.UMROH as U')
        ->leftJoin('dbo.PERSONEL as P', 'P.NIK', '=', 'U.NIK')
        ->select([
            'U.id',
            'U.NIK',
            'U.Nama',
            'U.tgl_awal',
            'U.tgl_akhir',
            'U.jenis_umroh',
            // kalau Nama di UMROH null, ambil dari PERSONEL
            \DB::raw("COALESCE(U.Nama, P.Nama) as Nama"),
        ])
        ->orderByDesc('U.id');

    // 1) Filter NIK
    if ($request->filled('nik')) {
        $q->where('U.NIK', 'like', "%{$nik}%");
    }

    // 2) Filter Nama
    if ($request->filled('nama')) {
        $q->where(\DB::raw("COALESCE(U.Nama, P.Nama)"), 'like', "%{$nama}%");
    }

    // 3) Filter Jenis
    if ($request->filled('jenis')) {
        $q->where('U.jenis_umroh', $jenis);
    }

    /**
     * 4) Filter Tanggal:
     * - kalau dua-duanya diisi => range (awal..akhir)
     * - kalau cuma tgl_awal => exact match tgl_awal
     * - kalau cuma tgl_akhir => exact match tgl_akhir
     */
    if ($request->filled('tgl_awal') && $request->filled('tgl_akhir')) {
        $q->whereBetween('U.tgl_awal', [$awal, $akhir])
          ->orWhereBetween('U.tgl_akhir', [$awal, $akhir]); 
        // kalau ini bikin kamu bingung, bilang: gue bisa bikin versi range yang lebih “strict”
    } elseif ($request->filled('tgl_awal')) {
        $q->where('U.tgl_awal', $awal);
    } elseif ($request->filled('tgl_akhir')) {
        $q->where('U.tgl_akhir', $akhir);
    }

    $rows = $q->paginate(10)->withQueryString();

    return view('umroh.index', compact('rows', 'nik', 'nama', 'awal', 'akhir', 'jenis'));
}

    public function create()
    {
        $jenisList = ['Pribadi', 'RSI'];
        return view('umroh.create', compact('jenisList'));
    }

    /**
     * STORE:
     * - simpan/updates PERSONEL (NIK + Nama)
     * - insert UMROH (NIK, tgl_awal, tgl_akhir, jenis_umroh)
     */
    public function store(Request $request)
    {
        $request->validate([
            'nik'        => ['required', 'digits:10'],
            'nama'       => ['required', 'string', 'max:100'],
            'tgl_awal'   => ['required', 'date'],
            'tgl_akhir'  => ['required', 'date', 'after_or_equal:tgl_awal'],
            'jenis_umroh'=> ['required', 'in:Pribadi,RSI'],
        ]);

        $nik   = (string) $request->nik;
        $nama  = (string) $request->nama;
        $awal  = Carbon::parse($request->tgl_awal)->format('Y-m-d');
        $akhir = Carbon::parse($request->tgl_akhir)->format('Y-m-d');
        $jenis = (string) $request->jenis_umroh;

        // UPSERT PERSONEL
        $exists = $this->conn->table('dbo.PERSONEL')->where('NIK', $nik)->exists();
        if ($exists) {
            $this->conn->table('dbo.PERSONEL')->where('NIK', $nik)->update(['Nama' => $nama]);
        } else {
            $this->conn->table('dbo.PERSONEL')->insert(['NIK' => $nik, 'Nama' => $nama]);
        }

        // INSERT UMROH
        $this->conn->table('dbo.UMROH')->insert([
            'NIK'        => $nik,
            'Nama'       => $nama,
            'tgl_awal'   => $awal,
            'tgl_akhir'  => $akhir,
            'jenis_umroh'=> $jenis,
        ]);

        return redirect()->route('umroh.index')->with('success', 'Data umroh berhasil ditambahkan.');
    }

    /**
     * EDIT: ambil data UMROH + Nama dari PERSONEL
     */
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

        abort_if(!$row, 404);

        $jenisList = ['Pribadi', 'RSI'];
        return view('umroh.edit', compact('row', 'jenisList'));
    }

    /**
     * UPDATE:
     * - update Nama di PERSONEL (biar sinkron)
     * - update tgl_awal/tgl_akhir/jenis_umroh di UMROH
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nik'        => ['required', 'digits:10'],
            'nama'       => ['required', 'string', 'max:100'],
            'tgl_awal'   => ['required', 'date'],
            'tgl_akhir'  => ['required', 'date', 'after_or_equal:tgl_awal'],
            'jenis_umroh'=> ['required', 'in:Pribadi,RSI'],
        ]);

        $nik   = (string) $request->nik;
        $nama  = (string) $request->nama;
        $awal  = Carbon::parse($request->tgl_awal)->format('Y-m-d');
        $akhir = Carbon::parse($request->tgl_akhir)->format('Y-m-d');
        $jenis = (string) $request->jenis_umroh;

        // Update PERSONEL Nama
        $this->conn->table('dbo.PERSONEL')->where('NIK', $nik)->update(['Nama' => $nama]);

        // Update UMROH
        $this->conn->table('dbo.UMROH')->where('id', $id)->update([
            'tgl_awal'   => $awal,
            'tgl_akhir'  => $akhir,
            'jenis_umroh'=> $jenis,
        ]);

        return redirect()->route('umroh.index')->with('success', 'Data umroh berhasil diupdate.');
    }

    public function destroy($id)
    {
        $this->conn->table('dbo.UMROH')->where('id', $id)->delete();
        return redirect()->route('umroh.index')->with('success', 'Data umroh berhasil dihapus.');
    }
}

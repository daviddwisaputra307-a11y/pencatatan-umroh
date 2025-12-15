<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UmrohController extends Controller
{
    private function conn()
    {
        return DB::connection('sqlsrv');
    }

    public function index(Request $request)
    {
        $q = trim($request->query('q', ''));

        $rows = $this->conn()
            ->table('dbo.UMROH as u')
            ->leftJoin('dbo.PERSONEL as p', 'p.NIK', '=', 'u.NIK')
            ->select([
                'u.id',
                'u.NIK',
                'p.Nama',
                'u.tgl_umroh',
                'u.jenis_umroh',
            ])
            ->when($q !== '', function ($query) use ($q) {
                $query->where('u.NIK', 'like', "%{$q}%")
                      ->orWhere('p.Nama', 'like', "%{$q}%");
            })
            ->orderByDesc('u.id')
            ->paginate(10)
            ->withQueryString();

        return view('umroh.index', compact('rows', 'q'));
    }

    public function create()
    {
        return view('umroh.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            // NIK 10 digit
            'nik'        => ['required', 'digits:10'],
            'nama'       => ['required', 'string', 'max:100'],
            'tgl_umroh'  => ['required', 'date'],
            'jenis_umroh'=> ['required', 'in:Pribadi,RSI'],
        ]);

        // upsert ke PERSONEL (biar NIK + Nama ke-sync)
        $exists = $this->conn()->table('dbo.PERSONEL')->where('NIK', $request->nik)->exists();

        if ($exists) {
            $this->conn()->table('dbo.PERSONEL')
                ->where('NIK', $request->nik)
                ->update(['Nama' => $request->nama]);
        } else {
            $this->conn()->table('dbo.PERSONEL')
                ->insert(['NIK' => $request->nik, 'Nama' => $request->nama]);
        }

        // insert UMROH
        $this->conn()->table('dbo.UMROH')->insert([
            'NIK'        => $request->nik,
            'tgl_umroh'  => $request->tgl_umroh,
            'jenis_umroh'=> $request->jenis_umroh,
        ]);

        return redirect()->route('umroh.index')->with('success', 'Data berhasil ditambah.');
    }

    public function edit($id)
    {
        $row = $this->conn()
            ->table('dbo.UMROH as u')
            ->leftJoin('dbo.PERSONEL as p', 'p.NIK', '=', 'u.NIK')
            ->select([
                'u.id',
                'u.NIK',
                'p.Nama',
                'u.tgl_umroh',
                'u.jenis_umroh',
            ])
            ->where('u.id', $id)
            ->first();

        abort_if(!$row, 404);

        return view('umroh.edit', compact('row'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            // di edit: NIK & Nama tampil tapi readonly (nggak divalidasi wajib update)
            'tgl_umroh'   => ['required', 'date'],
            'jenis_umroh' => ['required', 'in:Pribadi,RSI'],
        ]);

        $this->conn()->table('dbo.UMROH')
            ->where('id', $id)
            ->update([
                'tgl_umroh'   => $request->tgl_umroh,
                'jenis_umroh' => $request->jenis_umroh,
            ]);

        return redirect()->route('umroh.index')->with('success', 'Data berhasil diupdate.');
    }

    public function destroy($id)
    {
        $this->conn()->table('dbo.UMROH')->where('id', $id)->delete();
        return redirect()->route('umroh.index')->with('success', 'Data berhasil dihapus.');
    }
}

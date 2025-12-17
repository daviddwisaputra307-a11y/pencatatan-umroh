<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Laporan Pencatatan Umroh</title>

  <style>
    * { font-family: DejaVu Sans, sans-serif; }
    body { font-size: 12px; color: #111; }

    .header{
      width: 100%;
      margin-bottom: 14px;
      padding-bottom: 10px;
      border-bottom: 2px solid #222;
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
    }
    .title{
      font-size: 18px;
      font-weight: 700;
      letter-spacing: .2px;
    }
    .badge{
      font-size: 11px;
      padding: 4px 10px;
      border: 1px solid #999;
      border-radius: 999px;
      color: #444;
    }

    .meta{
      margin: 10px 0 12px;
      color: #333;
      font-size: 11px;
    }

    table{
      width: 100%;
      border-collapse: collapse;
    }
    thead th{
      background: #f2f2f2;
      border: 1px solid #444;
      padding: 8px 6px;
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: .4px;
      text-align: left;
    }
    tbody td{
      border: 1px solid #666;
      padding: 7px 6px;
      vertical-align: top;
    }

    .right { text-align: right; }
    .center { text-align: center; }

    .footer{
      margin-top: 14px;
      font-size: 10px;
      color: #666;
      border-top: 1px solid #ccc;
      padding-top: 8px;
    }
  </style>
</head>

<body>

  <div class="header">
    <div class="title">Laporan Pencatatan Umroh</div>
    <div class="badge">Export PDF</div>
  </div>

  <div class="meta">
    <b>Dicetak:</b> {{ $printedAt ?? now()->format('d-m-Y H:i') }}
  </div>

  <table>
    <thead>
      <tr>
        <th style="width:20%;">NIK</th>
        <th style="width:30%;">Nama</th>
        <th style="width:17%;">Tgl Awal</th>
        <th style="width:17%;">Tgl Akhir</th>
        <th style="width:16%;">Jenis</th>
      </tr>
    </thead>

    <tbody>
      @forelse($rows as $r)
        <tr>
          <td>{{ $r->nik ?? '-' }}</td>
          <td>{{ $r->nama ?? '-' }}</td>
          <td>{{ $r->tgl_awal ?? '-' }}</td>
          <td>{{ $r->tgl_akhir ?? '-' }}</td>
          <td>{{ $r->jenis_umroh ?? ($r->jenis ?? '-') }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="5" class="center" style="padding:14px;">
            Data tidak ditemukan.
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <div class="footer">
    Dicetak dari sistem Pencatatan Umroh.
  </div>

</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Forum Diskusi - FJB Tel-U</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; line-height: 1.6; }
        .container { width: 100%; margin: auto; }
        .header { text-align: center; border-bottom: 3px solid #b91d47; padding-bottom: 10px; margin-bottom: 30px; }
        .header h2 { color: #b91d47; margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; text-transform: uppercase; font-size: 12px; }
        tr:nth-child(even) { background-color: #fdfdfd; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 11px; background: #eee; }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="container">
        <div class="header">
            <h2>LAPORAN DATA FORUM DISKUSI</h2>
            <p>FJB Telkom University - {{ date('d F Y') }}</p>
        </div>

        <button class="no-print" onclick="window.print()" style="margin-bottom: 20px; padding: 10px 15px; cursor: pointer;">
            Cetak Laporan / Simpan PDF
        </button>

        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 35%;">Judul Topik</th>
                    <th style="width: 15%;">Kategori</th>
                    <th style="width: 20%;">Penulis</th>
                    <th style="width: 25%;">Tanggal Dibuat</th>
                </tr>
            </thead>
            <tbody>
                @foreach($forums as $index => $forum)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $forum->title }}</strong></td>
                    <td><span class="badge">{{ $forum->category }}</span></td>
                    <td>{{ $forum->user->name ?? 'Anonim' }}</td>
                    <td>{{ $forum->created_at->translatedFormat('d F Y, H:i') }} WIB</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <p style="text-align: right; font-size: 12px; margin-top: 50px;">
            Dicetak secara otomatis oleh sistem FJB Tel-U pada {{ date('d/m/Y H:i') }}
        </p>
    </div>

</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pengarsipan - SMAN 23 Makassar</title>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Lexend', sans-serif;
            margin: 0;
            padding: 40px;
            color: #000;
            background: #fff;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0;
            font-size: 14px;
        }
        .report-title {
            text-align: center;
            margin-bottom: 30px;
        }
        .report-title h2 {
            font-size: 18px;
            margin-bottom: 10px;
            text-decoration: underline;
        }
        .filter-info {
            font-size: 13px;
            margin-bottom: 20px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .table th, .table td {
            border: 1px solid #000;
            padding: 10px;
            text-align: left;
            font-size: 12px;
        }
        .table th {
            background-color: #f2f2f2;
            font-weight: 700;
        }
        .footer {
            margin-top: 50px;
            display: flex;
            justify-content: flex-end;
        }
        .signature {
            text-align: center;
            width: 250px;
        }
        .signature p {
            margin-bottom: 80px;
        }
        @media print {
            .no-print { display: none; }
            body { padding: 20px; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="margin-bottom: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer; background: #2563eb; color: #fff; border: none; border-radius: 5px;">
            Cetak Sekarang
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; cursor: pointer; background: #64748b; color: #fff; border: none; border-radius: 5px;">
            Tutup
        </button>
    </div>

    <div class="header">
        <h1>PEMERINTAH PROVINSI SULAWESI SELATAN</h1>
        <h1>DINAS PENDIDIKAN</h1>
        <h1>SMAN 23 MAKASSAR</h1>
        <p>Jl. Perintis Kemerdekaan KM. 10, Makassar, Sulawesi Selatan</p>
        <p>Email: info@sma23.sch.id | Website: www.sma23.sch.id</p>
    </div>

    <div class="report-title">
        <h2>LAPORAN REKAPITULASI PENGARSIPAN DIGITAL</h2>
    </div>

    <div class="filter-info">
        <table>
            <tr>
                <td style="border:none; padding: 2px 20px 2px 0;">Periode</td>
                <td style="border:none; padding: 2px;">: {{ $filters['start_date'] ?? '-' }} s/d {{ $filters['end_date'] ?? '-' }}</td>
            </tr>
            <tr>
                <td style="border:none; padding: 2px 20px 2px 0;">Kategori</td>
                <td style="border:none; padding: 2px;">: {{ $categoryName }}</td>
            </tr>
            <tr>
                <td style="border:none; padding: 2px 20px 2px 0;">Unit Kerja</td>
                <td style="border:none; padding: 2px;">: {{ $unitName }}</td>
            </tr>
            <tr>
                <td style="border:none; padding: 2px 20px 2px 0;">Status Arsip</td>
                <td style="border:none; padding: 2px;">: {{ ucfirst($filters['status'] ?? 'Semua Status') }}</td>
            </tr>
        </table>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Tgl Dokumen</th>
                <th>Judul & Nomor Dokumen</th>
                <th width="15%">Kategori</th>
                <th width="15%">Unit Kerja</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($documents as $index => $doc)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($doc->document_date)->format('d/m/Y') }}</td>
                    <td>
                        <strong>{{ $doc->title }}</strong><br>
                        <small>{{ $doc->document_number ?? '-' }}</small>
                    </td>
                    <td>{{ $doc->category->name }}</td>
                    <td>{{ $doc->unit->name }}</td>
                    <td>{{ ucfirst($doc->status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div class="signature">
            <p>Makassar, {{ date('d F Y') }}<br>Kepala Tata Usaha,</p>
            <strong>( ........................................ )</strong><br>
            <span>NIP. ........................................</span>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Perjanjian Magang - {{ $application->user->name }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            line-height: 1.6;
            margin: 0;
            padding: 40px;
            color: #000;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .title {
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
        }
        .subtitle {
            font-size: 14px;
            margin: 5px 0 0 0;
        }
        .content {
            font-size: 14px;
            text-align: justify;
        }
        .party {
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .party-table {
            width: 100%;
        }
        .party-table td {
            vertical-align: top;
            padding: 5px 0;
        }
        .party-table td:first-child {
            width: 30%;
        }
        .party-table td:nth-child(2) {
            width: 2%;
        }
        .articles {
            margin-top: 30px;
        }
        .article {
            margin-bottom: 15px;
        }
        .article-title {
            font-weight: bold;
            text-align: center;
            margin-bottom: 10px;
        }
        .signatures {
            margin-top: 60px;
            width: 100%;
        }
        .signatures td {
            text-align: center;
            width: 50%;
            vertical-align: bottom;
        }
        .signature-line {
            display: inline-block;
            width: 80%;
            border-bottom: 1px solid #000;
            margin-top: 80px;
            margin-bottom: 5px;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
        .print-btn {
            display: block;
            width: 200px;
            margin: 0 auto 30px;
            padding: 10px 20px;
            background-color: #2563eb;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            font-family: sans-serif;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <button class="no-print print-btn" onclick="window.print()">Cetak / Simpan PDF</button>

        <div class="header">
            <h1 class="title">SURAT PERJANJIAN MAGANG</h1>
            <p class="subtitle">Nomor: SP-{{ str_pad($application->id, 5, '0', STR_PAD_LEFT) }}/{{ date('Y') }}</p>
        </div>

        <div class="content">
            <p>Pada hari ini, <strong>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</strong>, kami yang bertanda tangan di bawah ini:</p>

            <div class="party">
                <table class="party-table">
                    <tr>
                        <td>Nama Perusahaan</td>
                        <td>:</td>
                        <td><strong>{{ $application->internship->company->name ?? 'PT. InternHub Teknologi' }}</strong></td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td>{{ $application->internship->company->address ?? 'Menara InternHub, Lt. 15, Jl. Jend. Sudirman, Jakarta' }}</td>
                    </tr>
                </table>
                <p>Dalam hal ini bertindak untuk dan atas nama Perusahaan, yang selanjutnya disebut sebagai <strong>PIHAK PERTAMA</strong>.</p>
            </div>

            <div class="party">
                <table class="party-table">
                    <tr>
                        <td>Nama Peserta</td>
                        <td>:</td>
                        <td><strong>{{ $application->user->name }}</strong></td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td>:</td>
                        <td>{{ $application->user->email }}</td>
                    </tr>
                    <tr>
                        <td>Posisi Magang</td>
                        <td>:</td>
                        <td><strong>{{ $application->internship->title }}</strong></td>
                    </tr>
                </table>
                <p>Dalam hal ini bertindak untuk dan atas nama diri sendiri, yang selanjutnya disebut sebagai <strong>PIHAK KEDUA</strong>.</p>
            </div>

            <p>PIHAK PERTAMA dan PIHAK KEDUA secara bersama-sama sepakat untuk mengadakan Perjanjian Magang dengan ketentuan dan syarat-syarat sebagai berikut:</p>

            <div class="articles">
                <div class="article">
                    <div class="article-title">Pasal 1<br>MAKSUD DAN TUJUAN</div>
                    <ol>
                        <li>PIHAK PERTAMA menerima PIHAK KEDUA untuk melaksanakan program magang di perusahaan PIHAK PERTAMA pada posisi {{ $application->internship->title }}.</li>
                        <li>PIHAK KEDUA sepakat untuk melaksanakan magang dengan penuh tanggung jawab dan mematuhi seluruh peraturan yang berlaku di perusahaan PIHAK PERTAMA.</li>
                    </ol>
                </div>

                <div class="article">
                    <div class="article-title">Pasal 2<br>HAK DAN KEWAJIBAN</div>
                    <ol>
                        <li>PIHAK PERTAMA berkewajiban memberikan bimbingan dan fasilitas yang wajar untuk mendukung kelancaran magang PIHAK KEDUA.</li>
                        <li>PIHAK KEDUA berkewajiban menjaga nama baik PIHAK PERTAMA dan merahasiakan segala informasi perusahaan yang bersifat rahasia (<em>Confidentiality Agreement</em>).</li>
                    </ol>
                </div>

                <div class="article">
                    <div class="article-title">Pasal 3<br>PENUTUP</div>
                    <p>Demikian Surat Perjanjian Magang ini dibuat rangkap 2 (dua), bermeterai cukup, dan ditandatangani oleh kedua belah pihak tanpa ada paksaan dari pihak mana pun.</p>
                </div>
            </div>

            <table class="signatures">
                <tr>
                    <td>
                        <strong>PIHAK PERTAMA</strong><br>
                        {{ $application->internship->company->name ?? 'PT. InternHub Teknologi' }}<br>
                        <span class="signature-line"></span><br>
                        ( Perwakilan HR / Manajemen )
                    </td>
                    <td>
                        <strong>PIHAK KEDUA</strong><br>
                        Peserta Magang<br>
                        <span class="signature-line"></span><br>
                        ( {{ $application->user->name }} )<br>
                        <em>Materai Rp.10.000,-</em>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <script>
        // Optional: Auto print when page loads
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Rekap Kategori SE</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        table th,
        table td {
            border: 1px solid #333;
            padding: 5px;
            text-align: left;
        }

        th {
            background-color: #eeeeee;
        }

        .font-dejavu {
            font-family: 'DejaVu Sans', sans-serif;
        }

        .f12 {
            font-size: 14px;
        }

        h2,
        h3,
        h4,
        {
        margin: 0;
        padding: 0;
        }




        h4 {
            margin-bottom: 2px;
            padding-bottom: 0;
        }

        ol {
            margin-top: 0;
            padding-top: 0;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .underline {
            text-decoration: underline;
        }

        @page {
            margin: 170px 50px 70px 50px;
        }


        .header {
            position: fixed;
            top: -120px;
            left: 0px;
            right: 5px;
            text-align: left;
        }

        .header img.tlp {
            position: absolute;
            top: 0;
            right: -30;
            width: 150px;
        }

        .header .subs {
            margin-top: -5px;
            line-height: 1.2;
            font-size: 0.9em;
            margin-right: 100px;
            /* ruang kosong supaya teks turun & tidak timpa gambar */
        }

        .header h2,
        .header h3 {
            margin: 6px 0;
        }

        .matik-list ul {
            margin: 0;
            padding-left: 1.2rem;
            /* default untuk nested */
        }

        /* level pertama */
        .matik-list>ul {
            padding-left: 1em;
            /* mepet kiri */
            list-style-type: disc;
            /* bullet bulat */
        }

        /* level kedua */
        .matik-list>ul>li>ul {
            padding-left: 1.5rem;
            list-style-type: square;
            font-size: 0.8em;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ public_path('images/tlp/tlp_teaser_green.png') }}" alt="TLP:GREEN" class="tlp">
        {{-- <p style="font-weight:bold; color:#FFBF00; margin:0;">TLP:AMBER+STRICT</p> --}}
        <h2>Rekap Kategori SE :: Tahun {{ $tahunAktifGlobal ?? '-' }}</h2>
        <h3>Pemilik Risiko: {{ strtoupper($namaOpd) }}</h3>

        <div class="subs">
            Pemilik Risiko bertanggungjawab terhadap proses bisnis/layanannya dengan cara pengelolaan aset yaitu dari
            mulai
            melakukan Pengukuran Nilai Aset, Kategorisasi SE termasuk pemenuhan standar, Pemetaan Risiko, Analisa
            Risiko,
            pembuatan Rencana Tindak Lanjut dan implementasi mitigasi risiko, sampai menjadi <i>Lead Auditee</i> dalam
            Audit Keamanan
            {{-- {{ $subs->pluck('subklasifikasiaset')->implode(', ') ?: '-' }} --}}
        </div>
    </div>


    <table class="table table-bordered text-center" style="width:100%">
        <thead class="font-weight-bold">
            <tr>
                <th>Nama Aset</th>
                <th>Sub Klasifikasi</th>
                <th>Lokasi</th>
                <th>Skor Kategori</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $aset)
                <tr>
                    <td>{{ $aset->nama_aset }}</td>
                    <td>{{ $aset->subklasifikasiaset->subklasifikasiaset ?? '-' }}</td>
                    <td>{{ $aset->lokasi }}</td>
                    <td>
                        {{-- {{ $aset->kategoriSe->skor_total ?? 'BELUM DINILAI' }} --}}
                        @php
                            $skor = $aset->kategoriSe->skor_total ?? null;

                            if ($skor === null) {
                                echo '<span class="badge badge-secondary">BELUM DINILAI</span>';
                            } else {
                                $range = $rangeSes->first(function ($r) use ($skor) {
                                    return $skor >= $r->nilai_bawah && $skor <= $r->nilai_atas;
                                });

                                $warna = $range->warna_hexa ?? '#999';
                                $label = $range->nilai_akhir_aset ?? 'TIDAK DIKETAHUI';

                                echo '<span class="badge" style="background-color: ' .
                                    $warna .
                                    '; color: #fff;">' .
                                    $skor .
                                    ' (' .
                                    strtoupper($label) .
                                    ')</span>';
                            }
                        @endphp
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <BR><BR><BR>
    <h4>Catatan</h4>
    <ol>
        <li>Kode TLP (Traffic Light Protocol) dipakai untuk mengklasifikasikan sensitivitas informasi, supaya jelas
            sejauh mana informasi boleh dibagikan.
            <b>Kode TLP:GREEN berarti boleh dibagikan di dalam komunitas/sektor (misalnya antar instansi pemerintah),
                tapi tidak untuk publik luas.</b>
        </li>
        <li><b>PERISAI</b> adalah sistem elektronik untuk melakukan <b>PE</b>ngelolaan <b>RIS</b>iko <b>A</b>set
            <b>I</b>nformasi di lingkup Pemerintah Provinsi Bali. PERISAI dikelola oleh
            Dinas Kominfos Provinsi Bali (Contact: Bidang Persandian)
        </li>
        <li>Aset dalam PERISAI adalah <strong>ASET INFORMASI yang mendukung kinerja organisasi dalam menjalakan proses
                bisnis/layanannya.</strong>
        </li>
        <li>SE adalah sistem elektronik yaitu dalam PERISAI adalah <strong>aset dengan klasifikasi [PL] Perangkat
                Lunak.</strong> Contoh SE adalah
            website, aplikasi
            berbasis web, mobile, sistem operasi dan utility.</li>
        <li>Semua informasi tentang aset ini dapat berubah sesuai dengan review dan pemutahiran data PERISAI <b>yang
                dilakukan minimal sekali setahun oleh Pemilik Risiko. Pemutahiran akan dilakukan serempak, menunggu
                informasi dari Diskominfos Prov Bali.</b> </li>
    </ol>
</body>

</html>

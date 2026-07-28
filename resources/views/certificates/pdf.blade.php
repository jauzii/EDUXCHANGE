<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sertifikat - {{ $enrollment->course->nama_kursus }}</title>
    <style>
        @page {
            margin: 0;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
        }

        .frame {
            border: 14px solid #4338ca;
            padding: 50px 60px;
            text-align: center;
            height: 100%;
        }

        .brand {
            font-size: 16px;
            letter-spacing: 4px;
            color: #4338ca;
            font-weight: bold;
            margin-bottom: 30px;
        }

        .title {
            font-size: 34px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 5px;
        }

        .subtitle {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 30px;
        }

        .recipient {
            font-size: 28px;
            font-weight: bold;
            color: #4338ca;
            border-bottom: 1px solid #d1d5db;
            display: inline-block;
            padding: 0 20px 8px 20px;
            margin-bottom: 25px;
        }

        .description {
            font-size: 14px;
            color: #374151;
            line-height: 1.6;
            width: 80%;
            margin: 0 auto 35px auto;
        }

        .course-name {
            font-weight: bold;
            color: #1f2937;
        }

        .footer-table {
            width: 100%;
            margin-top: 40px;
        }

        .footer-table td {
            text-align: center;
            font-size: 12px;
            color: #374151;
            vertical-align: bottom;
        }

        .footer-line {
            border-top: 1px solid #9ca3af;
            margin: 0 30px 6px 30px;
            padding-top: 6px;
        }

        .kode {
            margin-top: 35px;
            font-size: 11px;
            color: #9ca3af;
        }
    </style>
</head>
<body>

    <div class="frame">

        <div class="brand">EDUXCHANGE</div>

        <div class="title">SERTIFIKAT PENYELESAIAN</div>
        <div class="subtitle">Certificate of Completion</div>

        <p style="font-size:14px; color:#374151;">Diberikan kepada</p>

        <div class="recipient">{{ $enrollment->user->name }}</div>

        <div class="description">
            Atas keberhasilannya menyelesaikan kursus
            <span class="course-name">"{{ $enrollment->course->nama_kursus }}"</span>
            di platform EDUXCHANGE, dengan masa belajar mulai
            {{ $enrollment->started_at->translatedFormat('d F Y') }}
            hingga {{ $enrollment->ends_at->translatedFormat('d F Y') }},
            dan memperoleh nilai akhir kuis sebesar
            <span class="course-name">{{ $enrollment->score }}</span>.
        </div>

        <table class="footer-table">
            <tr>
                <td style="width: 50%;">
                    <div class="footer-line">
                        {{ $enrollment->course->tutor_display_name }}<br>
                        Tutor Pengajar
                    </div>
                </td>
                <td style="width: 50%;">
                    <div class="footer-line">
                        {{ $certificate->diterbitkan_pada->translatedFormat('d F Y') }}<br>
                        Tanggal Diterbitkan
                    </div>
                </td>
            </tr>
        </table>

        <div class="kode">
            Kode Verifikasi Sertifikat: {{ $certificate->kode_sertifikat }}
        </div>

    </div>

</body>
</html>

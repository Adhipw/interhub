<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internship Certificate - {{ $student_name }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Playfair+Display:ital,wght@0,700;1,700&display=swap');

        body {
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
            font-family: 'Outfit', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .certificate-container {
            width: 1000px;
            height: 700px;
            background: white;
            padding: 40px;
            position: relative;
            box-shadow: 0 20px 50px rgba(0,0,0,0.1);
            overflow: hidden;
            border: 20px solid #f8fafc;
        }

        /* Decorative Borders */
        .certificate-container::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            border: 2px solid #0f172a;
            margin: 15px;
            pointer-events: none;
        }

        .corner-decoration {
            position: absolute;
            width: 150px;
            height: 150px;
            border: 15px solid #3b82f6;
            z-index: 1;
        }

        .top-left { top: -40px; left: -40px; border-radius: 50%; }
        .bottom-right { bottom: -40px; right: -40px; border-radius: 50%; opacity: 0.5; }

        .content {
            position: relative;
            z-index: 2;
            text-align: center;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .header {
            margin-top: 40px;
        }

        .company-logo {
            max-height: 60px;
            margin-bottom: 20px;
        }

        .certificate-title {
            font-family: 'Playfair Display', serif;
            font-size: 56px;
            color: #0f172a;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .subtitle {
            font-size: 18px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 5px;
            margin-top: 10px;
        }

        .presented-to {
            margin-top: 50px;
            font-size: 20px;
            color: #64748b;
        }

        .student-name {
            font-family: 'Playfair Display', serif;
            font-size: 48px;
            color: #1e293b;
            margin: 10px 0;
            border-bottom: 2px solid #e2e8f0;
            display: inline-block;
            padding: 0 40px;
        }

        .description {
            font-size: 20px;
            color: #334155;
            line-height: 1.6;
            max-width: 700px;
            margin: 30px auto;
        }

        .footer {
            display: flex;
            justify-content: space-around;
            align-items: flex-end;
            margin-bottom: 40px;
        }

        .signature-block {
            text-align: center;
            width: 250px;
        }

        .signature-line {
            border-top: 1px solid #94a3b8;
            margin-top: 10px;
            padding-top: 10px;
        }

        .signature-name {
            font-weight: 600;
            color: #1e293b;
            font-size: 16px;
        }

        .signature-title {
            color: #64748b;
            font-size: 14px;
        }

        .certificate-id {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 12px;
            color: #94a3b8;
            font-family: monospace;
        }

        .seal {
            position: absolute;
            bottom: 60px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 100px;
            background: #3b82f6;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-weight: bold;
            font-size: 12px;
            text-align: center;
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);
            border: 4px double white;
        }

        @media print {
            body { background: none; }
            .certificate-container { box-shadow: none; border: none; }
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="corner-decoration top-left"></div>
        <div class="corner-decoration bottom-right"></div>

        <div class="content">
            <div class="header">
                @if($company_logo)
                    <img src="{{ $company_logo }}" alt="{{ $company_name }}" class="company-logo">
                @else
                    <div style="font-weight: 800; font-size: 24px; color: #3b82f6; margin-bottom: 20px;">{{ $company_name }}</div>
                @endif
                <h1 class="certificate-title">Certificate</h1>
                <div class="subtitle">of Completion</div>
            </div>

            <div class="main-content">
                <p class="presented-to">This certificate is proudly presented to</p>
                <h2 class="student-name">{{ $student_name }}</h2>
                <p class="description">
                    for successfully completing the <strong>{{ $internship_title }}</strong> program 
                    at <strong>{{ $company_name }}</strong>. 
                    From {{ $start_date }} to {{ $end_date }}.
                </p>
            </div>

            <div class="footer">
                <div class="signature-block">
                    <div class="signature-name">{{ $mentor_name }}</div>
                    <div class="signature-line">
                        <div class="signature-title">Company Mentor</div>
                    </div>
                </div>

                <div class="seal">
                    OFFICIAL<br>INTERNHUB<br>VERIFIED
                </div>

                <div class="signature-block">
                    <div class="signature-name">InternHub AI</div>
                    <div class="signature-line">
                        <div class="signature-title">System Administrator</div>
                    </div>
                </div>
            </div>

            <div class="certificate-id">Verification Code: {{ $certificate_id }} | Verify at intern-hub.com/verify</div>
        </div>
    </div>
</body>
</html>

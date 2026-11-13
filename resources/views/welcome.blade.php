<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EMS - Event Management System</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f8fafc;
            color: #0f172a;
        }

        nav {
            height: 76px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 8%;
            background: white;
            border-bottom: 1px solid #e2e8f0;
        }

        .brand {
            font-size: 25px;
            font-weight: 800;
            color: #2563eb;
            letter-spacing: -1px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        a {
            text-decoration: none;
        }

        .login {
            color: #475569;
            font-weight: 600;
        }

        .register {
            background: #2563eb;
            color: white;
            padding: 12px 20px;
            border-radius: 9px;
            font-weight: 600;
        }

        .hero {
            max-width: 1100px;
            margin: auto;
            padding: 110px 25px 85px;
            text-align: center;
        }

        .tag {
            display: inline-block;
            background: #dbeafe;
            color: #1d4ed8;
            padding: 9px 16px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 25px;
        }

        h1 {
            font-size: clamp(36px, 6vw, 58px);
            line-height: 1.15;
            letter-spacing: -2px;
            max-width: 850px;
            margin: auto;
        }

        h1 span {
            color: #2563eb;
        }

        .description {
            color: #64748b;
            font-size: 17px;
            line-height: 1.8;
            max-width: 650px;
            margin: 25px auto 32px;
        }

        .hero-buttons {
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .primary {
            background: #2563eb;
            color: white;
            padding: 15px 25px;
            border-radius: 9px;
            font-weight: 700;
        }

        .secondary {
            background: white;
            color: #334155;
            border: 1px solid #cbd5e1;
            padding: 15px 25px;
            border-radius: 9px;
            font-weight: 700;
        }

        .features {
            max-width: 1050px;
            margin: 0 auto;
            padding: 20px 25px 90px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .feature {
            background: white;
            padding: 28px;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
        }

        .feature-icon {
            width: 45px;
            height: 45px;
            background: #eff6ff;
            color: #2563eb;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 20px;
        }

        .feature h3 {
            font-size: 17px;
            margin-bottom: 10px;
        }

        .feature p {
            color: #64748b;
            font-size: 14px;
            line-height: 1.7;
        }

        footer {
            border-top: 1px solid #e2e8f0;
            background: white;
            padding: 25px;
            text-align: center;
            color: #94a3b8;
            font-size: 13px;
        }

        @media (max-width: 700px) {
            nav {
                padding: 0 5%;
            }

            .features {
                grid-template-columns: 1fr;
            }

            .hero {
                padding-top: 75px;
            }

            .description {
                font-size: 15px;
            }
        }

        @media (max-width: 400px) {
            .brand {
                font-size: 21px;
            }

            .nav-links {
                gap: 12px;
                font-size: 13px;
            }

            .register {
                padding: 10px 12px;
            }
        }
    </style>
</head>

<body>

    <nav>
        <div class="brand">EMS.</div>

        <div class="nav-links">
            <a href="{{ route('login') }}" class="login">Masuk</a>
            <a href="{{ route('register') }}" class="register">Daftar</a>
        </div>
    </nav>

    <section class="hero">
        <div class="tag">EVENT MANAGEMENT SYSTEM</div>

        <h1>
            Kelola Acara Lebih Mudah,
            <span>Dalam Satu Platform.</span>
        </h1>

        <p class="description">
            Platform pengelolaan acara untuk membantu organisasi
            mengatur kegiatan, mengelola sumber daya, berkoordinasi
            dengan tim, serta memantau pelaksanaan acara.
        </p>

        <div class="hero-buttons">
            <a href="{{ route('register') }}" class="primary">
                Mulai Sekarang
            </a>

            <a href="{{ route('login') }}" class="secondary">
                Saya Sudah Punya Akun
            </a>
        </div>
    </section>

    <section class="features">
        <div class="feature">
            <div class="feature-icon">▦</div>
            <h3>Manajemen Acara</h3>
            <p>
                Buat dan kelola acara, jadwal kegiatan,
                konsep acara, serta rundown dalam satu tempat.
            </p>
        </div>

        <div class="feature">
            <div class="feature-icon">♙</div>
            <h3>Kolaborasi Tim</h3>
            <p>
                Kelola anggota organisasi, atur peran,
                dan undang staf maupun vendor untuk bekerja sama.
            </p>
        </div>

        <div class="feature">
            <div class="feature-icon">▤</div>
            <h3>Pengelolaan Sumber Daya</h3>
            <p>
                Pantau venue, peralatan, vendor,
                transaksi keuangan, dan evaluasi acara.
            </p>
        </div>
    </section>

    <footer>
        &copy; {{ date('Y') }} EMS - Event Management System.
        All rights reserved.
    </footer>

</body>
</html>

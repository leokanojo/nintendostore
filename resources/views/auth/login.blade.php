<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin Nintendo Shop</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:'Poppins',sans-serif;
        }

        body{
            min-height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            background:linear-gradient(135deg,#0F172A,#1E3A8A,#2563EB);
            padding:20px;
        }

        .container{
            width:100%;
            max-width:1200px;
            min-height:650px;
            background:rgba(255,255,255,.08);
            backdrop-filter:blur(12px);
            border-radius:25px;
            overflow:hidden;
            display:flex;
            box-shadow:0 20px 40px rgba(0,0,0,.25);
        }

        .left{
            width:45%;
            background:white;
            display:flex;
            justify-content:center;
            align-items:center;
            padding:50px;
        }

        .login-card{
            width:100%;
            max-width:400px;
        }

        .avatar{
            width:100px;
            height:100px;
            border-radius:50%;
            background:linear-gradient(135deg,#2563EB,#60A5FA);
            display:flex;
            justify-content:center;
            align-items:center;
            color:white;
            font-size:42px;
            margin:0 auto 25px;
            box-shadow:0 10px 25px rgba(37,99,235,.35);
        }

        h2{
            text-align:center;
            color:#0F172A;
            margin-bottom:8px;
            font-size:32px;
        }

        .subtitle-form{
            text-align:center;
            color:#64748B;
            margin-bottom:30px;
        }

        .form-group{
            margin-bottom:18px;
        }

        .form-control{
            width:100%;
            padding:15px 18px;
            border:none;
            border-radius:15px;
            background:#F1F5F9;
            font-size:14px;
            transition:.3s;
        }

        .form-control:focus{
            outline:none;
            background:white;
            box-shadow:0 0 0 3px rgba(37,99,235,.2);
        }

        .btn-login{
            width:100%;
            border:none;
            padding:15px;
            border-radius:15px;
            background:linear-gradient(135deg,#2563EB,#3B82F6);
            color:white;
            font-size:15px;
            font-weight:600;
            cursor:pointer;
            transition:.3s;
            margin-top:10px;
        }

        .btn-login:hover{
            transform:translateY(-2px);
            box-shadow:0 10px 20px rgba(37,99,235,.35);
        }

        .right{
            width:55%;
            display:flex;
            flex-direction:column;
            justify-content:center;
            align-items:center;
            color:white;
            padding:50px;
            text-align:center;
        }

        .welcome{
            font-size:48px;
            font-weight:700;
            line-height:1.1;
            margin-bottom:15px;
        }

        .description{
            font-size:18px;
            color:#BFDBFE;
            margin-bottom:40px;
        }

        @media(max-width:900px){
            .container{
                flex-direction:column;
            }

            .left,.right{
                width:100%;
            }

            .welcome{
                font-size:32px;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <!-- LEFT SIDE (FORM LOGIN) -->
    <div class="left">
        <div class="login-card">

            <div class="avatar">🎮</div>

            <h2>Login Admin</h2>
            <div class="subtitle-form">Nintendo Shop Inventory System</div>

            <form action="{{ route('login.process') }}" method="POST">
    @csrf

    @if(session('success'))
    <div style="background:#dcfce7;color:#166534;padding:12px;border-radius:10px;margin-bottom:15px;">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div style="background:#fee2e2;color:#991b1b;padding:12px;border-radius:10px;margin-bottom:15px;">
        {{ session('error') }}
    </div>
@endif

    <div class="form-group">
        <input type="email"
               name="email"
               class="form-control"
               placeholder="Email"
               required>
    </div>

    <div class="form-group">
        <input type="password"
               name="password"
               class="form-control"
               placeholder="Password"
               required>
    </div>

    <button type="submit" class="btn-login">
        Login
    </button>

    <div style="text-align:center; margin-top:15px; font-size:14px; color:#64748B;">
        Belum punya akun?
        <a href="{{ route('register') }}"
           style="color:#2563EB; font-weight:600; text-decoration:none;">
            Register sekarang
        </a>
    </div>

</form>

        </div>
    </div>

    <!-- RIGHT SIDE (WELCOME) -->
    <div class="right">
        <div class="welcome">Welcome Back!</div>
        <div class="description">
            Sistem Inventory Nintendo Shop<br>
            Kelola stok barang dengan mudah dan cepat
        </div>
    </div>

</div>

</body>
</html>
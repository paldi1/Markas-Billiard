<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Markas Biliar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { 
            background-color: #0f172a; 
            height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.5);
            color: #fff;
        }
        .form-control {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            padding: 12px;
            border-radius: 10px;
        }
        .form-control:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: #3b82f6;
            color: #fff;
            box-shadow: none;
        }
        .btn-custom {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-custom:hover {
            background: #2563eb;
            transform: translateY(-2px);
        }
        .logo-img {
            width: 80px; 
            height: 80px; 
            border-radius: 50%; 
            margin-bottom: 20px; 
            border: 3px solid rgba(255,255,255,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                
                @if(session('error'))
                    <div class="alert alert-danger text-center shadow-sm border-0">{{ session('error') }}</div>
                @endif
                @if(session('success'))
                    <div class="alert alert-success text-center shadow-sm border-0">{{ session('success') }}</div>
                @endif

                <div class="glass-card">
                    <div class="text-center">
                        <img src="{{ asset('image/markas-logo.jpg') }}" alt="Logo" class="logo-img">
                        <h4 class="mb-4 fw-bold">Admin Login</h4>
                    </div>
                    
                    <form action="{{ route('admin.login.submit') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label text-secondary">Username</label>
                            <input type="text" name="username" class="form-control" value="{{ old('username') }}" placeholder="Masukkan username" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-secondary">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                            
                            @if($errors->has('login_error'))
                                <div class="text-danger small mt-2">
                                    <i class="bi bi-exclamation-triangle-fill"></i> <strong>{{ $errors->first('login_error') }}</strong>
                                </div>
                            @endif
                        </div>

                        <button type="submit" class="btn btn-custom w-100 shadow">
                            <i class="bi bi-box-arrow-in-right"></i> Masuk Masbro
                        </button>
                    </form>
                </div>

                <div class="text-center mt-4">
                    <a href="/" class="text-white-50 text-decoration-none">
                        <i class="bi bi-arrow-left"></i> Kembali ke Form Pelanggan
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
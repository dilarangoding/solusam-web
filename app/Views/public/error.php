<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons@1.0.0/icons-sprite.svg" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .error-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .error-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }

        .error-icon {
            font-size: 5rem;
            color: #dc3545;
        }
    </style>
</head>

<body>
    <div class="error-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="error-card p-5 text-center">
                        <div class="error-icon mb-4">
                            <i class="ti ti-alert-triangle"></i>
                        </div>
                        <h1 class="text-danger fw-bold mb-3"><?= $title; ?></h1>
                        <p class="text-muted fs-5 mb-4"><?= $message; ?></p>
                        <div class="mt-4">
                            <button onclick="history.back()" class="btn btn-primary me-2">
                                <i class="ti ti-arrow-left me-2"></i>
                                Kembali
                            </button>
                            <button onclick="window.close()" class="btn btn-outline-secondary">
                                <i class="ti ti-x me-2"></i>
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Montink ERP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <i class="fas fa-shopping-cart me-2"></i>
                Montink ERP
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text">
                    Sistema de Gestão
                </span>
            </div>
        </div>
    </nav>
    
    <main class="py-5">
        <div class="container">
            @livewire('produto-manager')
        </div>
    </main>
    
    <footer class="py-4 mt-5">
        <div class="container text-center">
            <small>&copy; 2024 Montink ERP. Todos os direitos reservados.</small>
        </div>
    </footer>
    
    @livewireScripts
</body>
</html>

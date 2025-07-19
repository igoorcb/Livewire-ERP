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
                <a href="#" class="nav-link" onclick="showSection('produtos')">Produtos</a>
                <a href="#" class="nav-link" onclick="showSection('carrinho')">Carrinho</a>
                <span class="navbar-text">
                    Sistema de Gestão
                </span>
            </div>
        </div>
    </nav>
    
    <main class="py-5">
        <div class="container">
            <div id="produtos-section">
                @livewire('produto-manager')
            </div>
            <div id="carrinho-section" style="display: none;">
                @livewire('carrinho')
            </div>
        </div>
    </main>
    
    <footer class="py-4 mt-5">
        <div class="container text-center">
            <small>&copy; 2024 Montink ERP. Todos os direitos reservados.</small>
        </div>
    </footer>
    
    <script>
        function showSection(section) {
            if (section === 'produtos') {
                document.getElementById('produtos-section').style.display = 'block';
                document.getElementById('carrinho-section').style.display = 'none';
            } else {
                document.getElementById('produtos-section').style.display = 'none';
                document.getElementById('carrinho-section').style.display = 'block';
            }
        }
    </script>
    
    @livewireScripts
</body>
</html>

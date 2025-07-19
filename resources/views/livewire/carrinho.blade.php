<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">Carrinho de Compras</h2>
            <p class="text-muted mb-0">Adicione produtos e finalize sua compra</p>
        </div>
        <div class="text-end">
            <span class="badge bg-primary">{{ count($carrinho) }} itens</span>
        </div>
    </div>

    @if(session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-shopping-bag me-2 text-primary"></i>
                        Produtos Disponíveis
                    </h5>
                </div>
                <div class="card-body">
                    @if($produtos->count() > 0)
                        <div class="row g-3">
                            @foreach($produtos as $produto)
                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100">
                                        <h6 class="fw-medium mb-2">{{ $produto->nome }}</h6>
                                        <p class="text-success fw-bold mb-2">R$ {{ number_format($produto->preco, 2, ',', '.') }}</p>
                                        
                                        @if($produto->variacoes->count() > 0)
                                            <div class="mb-3">
                                                <small class="text-muted d-block mb-2">Variações:</small>
                                                @foreach($produto->variacoes as $variacao)
                                                    @php
                                                        $estoque = $produto->estoques->where('variacao_id', $variacao->id)->first();
                                                        $quantidade = $estoque ? $estoque->quantidade : 0;
                                                    @endphp
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <span class="small">{{ $variacao->nome }}</span>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <span class="small text-muted">Estoque: {{ $quantidade }}</span>
                                                            <button 
                                                                wire:click="adicionarAoCarrinho({{ $produto->id }}, {{ $variacao->id }})"
                                                                class="btn btn-outline-primary btn-sm"
                                                                {{ $quantidade <= 0 ? 'disabled' : '' }}
                                                            >
                                                                <i class="fas fa-plus"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            @php
                                                $estoque = $produto->estoques->where('variacao_id', null)->first();
                                                $quantidade = $estoque ? $estoque->quantidade : 0;
                                            @endphp
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="small text-muted">Estoque: {{ $quantidade }}</span>
                                                <button 
                                                    wire:click="adicionarAoCarrinho({{ $produto->id }})"
                                                    class="btn btn-outline-primary btn-sm"
                                                    {{ $quantidade <= 0 ? 'disabled' : '' }}
                                                >
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-box-open fa-2x mb-2"></i>
                            <p class="mb-0">Nenhum produto disponível</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-shopping-cart me-2 text-primary"></i>
                        Seu Carrinho
                    </h5>
                </div>
                <div class="card-body">
                    @if(count($carrinho) > 0)
                        <div class="mb-3">
                            @foreach($carrinho as $chave => $item)
                                <div class="border-bottom pb-2 mb-2">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $item['nome'] }}</h6>
                                            @if($item['variacao_nome'])
                                                <small class="text-muted">{{ $item['variacao_nome'] }}</small>
                                            @endif
                                            <div class="text-success fw-bold">R$ {{ number_format($item['preco'], 2, ',', '.') }}</div>
                                        </div>
                                        <button wire:click="removerDoCarrinho('{{ $chave }}')" class="btn btn-outline-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mt-2">
                                        <label class="small mb-0">Qtd:</label>
                                        <input 
                                            type="number" 
                                            wire:change="atualizarQuantidade('{{ $chave }}', $event.target.value)"
                                            value="{{ $item['quantidade'] }}"
                                            min="1"
                                            class="form-control form-control-sm"
                                            style="width: 60px;"
                                        >
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span>R$ {{ number_format($subtotal, 2, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Frete:</span>
                                <span>{{ $frete == 0 ? 'Grátis' : 'R$ ' . number_format($frete, 2, ',', '.') }}</span>
                            </div>
                            @if($desconto > 0)
                                <div class="d-flex justify-content-between mb-2 text-success">
                                    <span>Desconto:</span>
                                    <span>-R$ {{ number_format($desconto, 2, ',', '.') }}</span>
                                </div>
                            @endif
                            <div class="d-flex justify-content-between fw-bold border-top pt-2">
                                <span>Total:</span>
                                <span>R$ {{ number_format($total, 2, ',', '.') }}</span>
                            </div>
                        </div>

                        <button wire:click="limparCarrinho" class="btn btn-outline-secondary btn-sm w-100 mt-3">
                            <i class="fas fa-trash me-1"></i>Limpar Carrinho
                        </button>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                            <p class="mb-0">Carrinho vazio</p>
                        </div>
                    @endif
                </div>
            </div>

            @if(count($carrinho) > 0)
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-tag me-2 text-primary"></i>
                            Cupom de Desconto
                        </h5>
                    </div>
                    <div class="card-body">
                        @if(!$cupom_aplicado)
                            <div class="input-group">
                                <input type="text" wire:model="cupom_codigo" placeholder="Digite o código" class="form-control">
                                <button wire:click="aplicarCupom" class="btn btn-outline-primary">Aplicar</button>
                            </div>
                        @else
                            <div class="alert alert-success mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $cupom_aplicado->codigo }}</strong>
                                        <br>
                                        <small>{{ $cupom_aplicado->tipo === 'percentual' ? $cupom_aplicado->desconto . '%' : 'R$ ' . number_format($cupom_aplicado->desconto, 2, ',', '.') }}</small>
                                    </div>
                                    <button wire:click="removerCupom" class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-shipping-fast me-2 text-primary"></i>
                            Dados de Entrega
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">CEP</label>
                            <div class="input-group">
                                <input type="text" wire:model="cep" wire:blur="buscarCep" placeholder="00000000" class="form-control" maxlength="8">
                                @if($loading_cep)
                                    <span class="input-group-text">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Endereço</label>
                            <input type="text" wire:model="endereco" class="form-control" placeholder="Endereço completo">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nome Completo</label>
                            <input type="text" wire:model="cliente" class="form-control" placeholder="Seu nome completo">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">E-mail</label>
                            <input type="email" wire:model="email" class="form-control" placeholder="seu@email.com">
                        </div>

                        <button wire:click="finalizarPedido" class="btn btn-primary w-100">
                            <i class="fas fa-check me-1"></i>Finalizar Pedido
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

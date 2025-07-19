<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">Gestão de Produtos</h2>
            <p class="text-muted mb-0">Cadastre e gerencie seus produtos e estoques</p>
        </div>
        <div class="text-end">
            <span class="badge bg-primary">{{ $produtos->count() }} produtos</span>
        </div>
    </div>

    @if(session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-plus-circle me-2 text-primary"></i>
                {{ $produto_id ? 'Editar Produto' : 'Novo Produto' }}
            </h5>
        </div>
        <div class="card-body">
            <form wire:submit.prevent="salvarProduto">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Nome do Produto</label>
                        <input type="text" wire:model="nome" class="form-control" placeholder="Digite o nome do produto">
                        @error('nome') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Preço Base</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="number" step="0.01" wire:model="preco" class="form-control" placeholder="0,00">
                        </div>
                        @error('preco') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                </div>
                
                <div class="mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <label class="form-label fw-medium mb-0">Variações do Produto</label>
                        <button type="button" wire:click="addVariacao" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>Adicionar Variação
                        </button>
                    </div>
                    
                    @if(count($variacoes) > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nome</th>
                                        <th>Preço Extra</th>
                                        <th>Estoque</th>
                                        <th width="50"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($variacoes as $i => $var)
                                        <tr>
                                            <td>
                                                <input type="text" wire:model="variacoes.{{$i}}.nome" placeholder="Ex: Tamanho M" class="form-control form-control-sm">
                                                @error('variacoes.'.$i.'.nome') <span class="text-danger small">{{ $message }}</span> @enderror
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">R$</span>
                                                    <input type="number" step="0.01" wire:model="variacoes.{{$i}}.preco_extra" placeholder="0,00" class="form-control">
                                                </div>
                                            </td>
                                            <td>
                                                <input type="number" wire:model="variacoes.{{$i}}.estoque" placeholder="0" class="form-control form-control-sm">
                                                @error('variacoes.'.$i.'.estoque') <span class="text-danger small">{{ $message }}</span> @enderror
                                            </td>
                                            <td>
                                                <button type="button" wire:click="removeVariacao({{$i}})" class="btn btn-outline-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-box-open fa-2x mb-2"></i>
                            <p class="mb-0">Nenhuma variação adicionada</p>
                        </div>
                    @endif
                </div>
                
                <div class="d-flex gap-2 mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>
                        {{ $produto_id ? 'Atualizar' : 'Salvar' }}
                    </button>
                    @if($produto_id)
                        <button type="button" wire:click="cancelarEdicao" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if($produtos->count() > 0)
        <div class="card shadow-sm border-0">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2 text-primary"></i>
                    Produtos Cadastrados
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Produto</th>
                                <th class="border-0 text-center">Preço</th>
                                <th class="border-0">Variações</th>
                                <th class="border-0 text-center" width="100">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($produtos as $produto)
                                <tr>
                                    <td>
                                        <div class="fw-medium">{{ $produto->nome }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold text-success">R$ {{ number_format($produto->preco,2,',','.') }}</span>
                                    </td>
                                    <td>
                                        @if($produto->variacoes->count() > 0)
                                            @foreach($produto->variacoes as $v)
                                                @php
                                                    $estoque = $produto->estoques->where('variacao_id', $v->id)->first();
                                                    $quantidade = $estoque ? $estoque->quantidade : 0;
                                                @endphp
                                                <div class="d-flex align-items-center mb-1">
                                                    <span class="badge bg-light text-dark me-2">{{ $v->nome }}</span>
                                                    <small class="text-muted">
                                                        Estoque: <span class="fw-medium">{{ $quantidade }}</span>
                                                    </small>
                                                </div>
                                            @endforeach
                                        @else
                                            <span class="text-muted small">Sem variações</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <button wire:click="editar({{ $produto->id }})" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-5">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhum produto cadastrado</h5>
                <p class="text-muted mb-0">Comece cadastrando seu primeiro produto acima.</p>
            </div>
        </div>
    @endif
</div>

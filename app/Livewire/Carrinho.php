<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Produto;
use App\Models\Cupom;
use App\Models\Pedido;
use App\Models\ItemPedido;
use App\Models\Estoque;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class Carrinho extends Component
{
    public $produtos;
    public $carrinho = [];
    public $cep = '';
    public $endereco = '';
    public $cliente = '';
    public $email = '';
    public $cupom_codigo = '';
    public $cupom_aplicado = null;
    public $subtotal = 0;
    public $frete = 0;
    public $desconto = 0;
    public $total = 0;
    public $loading_cep = false;

    protected $rules = [
        'cliente' => 'required|string',
        'email' => 'required|email',
        'cep' => 'required|string|size:8',
        'endereco' => 'required|string',
    ];

    public function mount()
    {
        $this->produtos = Produto::with(['variacoes', 'estoques'])->get();
        $this->carrinho = session('carrinho', []);
        $this->calcularTotais();
    }

    public function render()
    {
        return view('livewire.carrinho');
    }

    public function adicionarAoCarrinho($produto_id, $variacao_id = null)
    {
        $produto = Produto::with(['variacoes', 'estoques'])->find($produto_id);
        
        if (!$produto) return;

        $preco = $produto->preco;
        $variacao_nome = '';
        
        if ($variacao_id) {
            $variacao = $produto->variacoes->find($variacao_id);
            if ($variacao) {
                $preco += $variacao->preco_extra;
                $variacao_nome = $variacao->nome;
            }
        }

        $estoque = $produto->estoques->where('variacao_id', $variacao_id)->first();
        $quantidade_disponivel = $estoque ? $estoque->quantidade : 0;

        if ($quantidade_disponivel <= 0) {
            session()->flash('error', 'Produto sem estoque disponível.');
            return;
        }

        $chave = $produto_id . '_' . ($variacao_id ?? 'sem');
        
        if (isset($this->carrinho[$chave])) {
            if ($this->carrinho[$chave]['quantidade'] >= $quantidade_disponivel) {
                session()->flash('error', 'Quantidade máxima disponível no estoque.');
                return;
            }
            $this->carrinho[$chave]['quantidade']++;
        } else {
            $this->carrinho[$chave] = [
                'produto_id' => $produto_id,
                'variacao_id' => $variacao_id,
                'nome' => $produto->nome,
                'variacao_nome' => $variacao_nome,
                'preco' => $preco,
                'quantidade' => 1
            ];
        }

        session(['carrinho' => $this->carrinho]);
        $this->calcularTotais();
        session()->flash('success', 'Produto adicionado ao carrinho!');
    }

    public function removerDoCarrinho($chave)
    {
        unset($this->carrinho[$chave]);
        session(['carrinho' => $this->carrinho]);
        $this->calcularTotais();
        session()->flash('success', 'Produto removido do carrinho!');
    }

    public function atualizarQuantidade($chave, $quantidade)
    {
        if ($quantidade <= 0) {
            $this->removerDoCarrinho($chave);
            return;
        }

        $item = $this->carrinho[$chave];
        $produto = Produto::with(['estoques'])->find($item['produto_id']);
        $estoque = $produto->estoques->where('variacao_id', $item['variacao_id'])->first();
        $quantidade_disponivel = $estoque ? $estoque->quantidade : 0;

        if ($quantidade > $quantidade_disponivel) {
            session()->flash('error', 'Quantidade não disponível no estoque.');
            return;
        }

        $this->carrinho[$chave]['quantidade'] = $quantidade;
        session(['carrinho' => $this->carrinho]);
        $this->calcularTotais();
    }

    public function buscarCep()
    {
        if (strlen($this->cep) !== 8) return;

        $this->loading_cep = true;
        
        try {
            $response = Http::get("https://viacep.com.br/ws/{$this->cep}/json/");
            
            if ($response->successful() && !$response->json('erro')) {
                $data = $response->json();
                $this->endereco = "{$data['logradouro']}, {$data['bairro']}, {$data['localidade']} - {$data['uf']}";
            } else {
                session()->flash('error', 'CEP não encontrado.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao buscar CEP.');
        }
        
        $this->loading_cep = false;
    }

    public function aplicarCupom()
    {
        if (empty($this->cupom_codigo)) return;

        $cupom = Cupom::where('codigo', $this->cupom_codigo)
                     ->where('validade', '>=', now()->toDateString())
                     ->first();

        if (!$cupom) {
            session()->flash('error', 'Cupom inválido ou expirado.');
            return;
        }

        if ($this->subtotal < $cupom->valor_minimo) {
            session()->flash('error', "Valor mínimo para este cupom: R$ " . number_format($cupom->valor_minimo, 2, ',', '.'));
            return;
        }

        $this->cupom_aplicado = $cupom;
        $this->calcularTotais();
        session()->flash('success', 'Cupom aplicado com sucesso!');
    }

    public function removerCupom()
    {
        $this->cupom_aplicado = null;
        $this->cupom_codigo = '';
        $this->calcularTotais();
        session()->flash('success', 'Cupom removido!');
    }

    public function calcularTotais()
    {
        $this->subtotal = 0;
        
        foreach ($this->carrinho as $item) {
            $this->subtotal += $item['preco'] * $item['quantidade'];
        }

        $this->calcularFrete();
        $this->calcularDesconto();
        $this->total = $this->subtotal + $this->frete - $this->desconto;
    }

    public function calcularFrete()
    {
        if ($this->subtotal >= 200) {
            $this->frete = 0;
        } elseif ($this->subtotal >= 52 && $this->subtotal <= 166.59) {
            $this->frete = 15;
        } else {
            $this->frete = 20;
        }
    }

    public function calcularDesconto()
    {
        if (!$this->cupom_aplicado) {
            $this->desconto = 0;
            return;
        }

        if ($this->cupom_aplicado->tipo === 'percentual') {
            $this->desconto = ($this->subtotal * $this->cupom_aplicado->desconto) / 100;
        } else {
            $this->desconto = $this->cupom_aplicado->desconto;
        }

        if ($this->desconto > $this->subtotal) {
            $this->desconto = $this->subtotal;
        }
    }

    public function finalizarPedido()
    {
        $this->validate();

        if (empty($this->carrinho)) {
            session()->flash('error', 'Carrinho vazio.');
            return;
        }

        try {
            $pedido = Pedido::create([
                'cliente' => $this->cliente,
                'email' => $this->email,
                'cep' => $this->cep,
                'endereco' => $this->endereco,
                'subtotal' => $this->subtotal,
                'frete' => $this->frete,
                'total' => $this->total,
                'cupom_id' => $this->cupom_aplicado ? $this->cupom_aplicado->id : null,
            ]);

            foreach ($this->carrinho as $item) {
                ItemPedido::create([
                    'pedido_id' => $pedido->id,
                    'produto_id' => $item['produto_id'],
                    'variacao_id' => $item['variacao_id'],
                    'quantidade' => $item['quantidade'],
                    'preco_unitario' => $item['preco'],
                ]);

                $estoque = Estoque::where('produto_id', $item['produto_id'])
                                 ->where('variacao_id', $item['variacao_id'])
                                 ->first();
                
                if ($estoque) {
                    $estoque->decrement('quantidade', $item['quantidade']);
                }
            }

            session()->forget('carrinho');
            $this->carrinho = [];
            $this->calcularTotais();

            session()->flash('success', "Pedido #{$pedido->id} finalizado com sucesso! Um e-mail foi enviado para {$this->email}");

        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao finalizar pedido. Tente novamente.');
        }
    }

    public function limparCarrinho()
    {
        session()->forget('carrinho');
        $this->carrinho = [];
        $this->calcularTotais();
        session()->flash('success', 'Carrinho limpo!');
    }
}

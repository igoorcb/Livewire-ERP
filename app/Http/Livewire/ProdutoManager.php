<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Produto;
use App\Models\Variacao;
use App\Models\Estoque;

class ProdutoManager extends Component
{
    public $produtos;
    public $produto_id, $nome, $preco;
    public $variacoes = [];

    protected $rules = [
        'nome' => 'required|string',
        'preco' => 'required|numeric|min:0',
        'variacoes.*.nome' => 'required|string',
        'variacoes.*.preco_extra' => 'nullable|numeric|min:0',
        'variacoes.*.estoque' => 'required|integer|min:0',
    ];

    public function mount()
    {
        $this->loadProdutos();
    }

    public function loadProdutos()
    {
        $this->produtos = Produto::with(['variacoes', 'estoques'])->get();
    }

    public function render()
    {
        return view('livewire.produto-manager');
    }

    public function addVariacao()
    {
        $this->variacoes[] = ['nome' => '', 'preco_extra' => 0, 'estoque' => 0];
    }

    public function removeVariacao($index)
    {
        unset($this->variacoes[$index]);
        $this->variacoes = array_values($this->variacoes);
    }

    public function salvarProduto()
    {
        $this->validate();

        $produto = Produto::updateOrCreate(
            ['id' => $this->produto_id],
            ['nome' => $this->nome, 'preco' => $this->preco]
        );

        if ($this->produto_id) {
            $produto->variacoes()->delete();
            $produto->estoques()->delete();
        }

        foreach ($this->variacoes as $var) {
            $variacao = $produto->variacoes()->create([
                'nome' => $var['nome'],
                'preco_extra' => $var['preco_extra'] ?? 0,
            ]);
            $produto->estoques()->create([
                'variacao_id' => $variacao->id,
                'quantidade' => $var['estoque'] ?? 0,
            ]);
        }

        $this->reset(['produto_id', 'nome', 'preco', 'variacoes']);
        $this->loadProdutos();
        session()->flash('success', 'Produto salvo com sucesso!');
    }

    public function editar($id)
    {
        $produto = Produto::with(['variacoes', 'estoques'])->findOrFail($id);
        $this->produto_id = $produto->id;
        $this->nome = $produto->nome;
        $this->preco = $produto->preco;
        $this->variacoes = [];

        foreach ($produto->variacoes as $variacao) {
            $estoque = $produto->estoques->where('variacao_id', $variacao->id)->first();
            $this->variacoes[] = [
                'nome' => $variacao->nome,
                'preco_extra' => $variacao->preco_extra,
                'estoque' => $estoque ? $estoque->quantidade : 0,
            ];
        }
    }

    public function cancelarEdicao()
    {
        $this->reset(['produto_id', 'nome', 'preco', 'variacoes']);
    }
}

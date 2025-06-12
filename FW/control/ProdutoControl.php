<?php
ini_set('display_errors', 1);
ini_set('display_startup_erros', 1);
error_reporting(E_ALL);
require_once("model/Produto.php");
require_once("DAO/ProdutoDao.php");

class ProdutoControl
{
    private $produto;
    private $acao;
    private $dao;

    public function __construct()
    {
        $this->produto = new Produto();
        $this->dao = new ProdutoDao();
        $this->acao = isset($_GET["a"]) ? $_GET["a"] : null;
        $this->verificaAcao();
    }

    function verificaAcao()
    {
        switch ($this->acao) {
            case 'inserir':
                $this->inserir();
                break;
            case 'excluir':
                $this->excluir();
                break;
            case 'alterar':
                $this->alterar();
                break;
            case 'buscarId':
                $this->buscarId();
                break;
            case 'buscarTodos':
                $this->buscaTodos();
                break;
            default:
                echo "Ação inválida!";
        }
    }

    function inserir()
    {
        $this->produto->setNome($_POST['nome']);
        $this->produto->setDescricao($_POST['descricao']);
        $this->produto->setPreco($_POST['preco']);
        if ($this->dao->inserir($this->produto)) {
            echo "Produto inserido com sucesso!";
        } else {
            echo "Erro ao inserir produto!";
        }
    }

    function excluir()
    {
        $id = $_GET['id'] ?? 0;
        if ($this->dao->excluir($id)) {
            echo "Produto excluído com sucesso!";
        } else {
            echo "Erro ao excluir produto!";
        }
    }

    function alterar()
    {
        $this->produto->setId($_POST['id']);
        $this->produto->setNome($_POST['nome']);
        $this->produto->setDescricao($_POST['descricao']);
        $this->produto->setPreco($_POST['preco']);
        if ($this->dao->alterar($this->produto)) {
            echo "Produto alterado com sucesso!";
        } else {
            echo "Erro ao alterar produto!";
        }
    }

    function buscarId()
    {
        $id = $_GET['id'] ?? 0;
        $produto = $this->dao->buscarId($id);
        if ($produto) {
            var_dump($produto);
        } else {
            echo "Produto não encontrado!";
        }
    }

    function buscaTodos()
    {
        $produtos = $this->dao->buscaTodos();
        foreach ($produtos as $produto) {
            echo "ID: " . $produto->getId() . " - Nome: " . $produto->getNome() . "<br>";
        }
    }
}

new ProdutoControl();

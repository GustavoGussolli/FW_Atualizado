<?php
require_once("model/Produto.php");

class ProdutoDao
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = new PDO('mysql:host=localhost;dbname=tb_pessoa', 'root', 'bancodedados');
    }

    public function inserir(Produto $produto)
    {
        $sql = "INSERT INTO produtos (nome, descricao, preco) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$produto->getNome(), $produto->getDescricao(), $produto->getPreco()]);
    }

    public function excluir($id)
    {
        $sql = "DELETE FROM produtos WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function alterar(Produto $produto)
    {
        $sql = "UPDATE produtos SET nome = ?, descricao = ?, preco = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$produto->getNome(), $produto->getDescricao(), $produto->getPreco(), $produto->getId()]);
    }

    public function buscarId($id)
    {
        $sql = "SELECT * FROM produtos WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $produto = new Produto();
            $produto->setId($row['id']);
            $produto->setNome($row['nome']);
            $produto->setDescricao($row['descricao']);
            $produto->setPreco($row['preco']);
            return $produto;
        }
        return null;
    }

    public function buscaTodos()
    {
        $sql = "SELECT * FROM produtos";
        $stmt = $this->pdo->query($sql);
        $produtos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $produto = new Produto();
            $produto->setId($row['id']);
            $produto->setNome($row['nome']);
            $produto->setDescricao($row['descricao']);
            $produto->setPreco($row['preco']);
            $produtos[] = $produto;
        }
        return $produtos;
    }
}

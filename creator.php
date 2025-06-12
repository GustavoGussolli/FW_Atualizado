<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Creator {
    private $con;
    private $servidor;
    private $banco;
    private $usuario;
    private $senha;
    private $tabelas;

    function __construct() {
        if (!isset($_POST["servidor"], $_POST["banco"], $_POST["usuario"], $_POST["senha"])) {
            die("Parâmetros do banco de dados não foram informados corretamente.");
        }

        $this->servidor = $_POST["servidor"];
        $this->banco = $_POST["banco"];
        $this->usuario = $_POST["usuario"];
        $this->senha = $_POST["senha"];

        $this->criaDiretorios();
        $this->conectar();
        $this->buscaTabelas();
        $this->gerarClassesModel();
        $this->gerarClasseConexao();
    }

    private function criaDiretorios() {
        $dirs = [
            "sistema",
            "sistema/model",
            "sistema/control",
            "sistema/view",
            "sistema/dao"
        ];

        foreach ($dirs as $dir) {
            if (!file_exists($dir)) {
                if (!mkdir($dir, 0777, true)) {
                    header("Location:index.php?msg=0");
                    exit;
                }
            }
        }
    }

    private function conectar() {
        try {
            $this->con = new PDO(
                "mysql:host=" . $this->servidor . ";dbname=" . $this->banco,
                $this->usuario,
                $this->senha
            );
        } catch (Exception $e) {
            header("Location:index.php?msg=1");
            exit;
        }
    }

    private function buscaTabelas() {
        try {
            $sql = "SHOW TABLES";
            $query = $this->con->query($sql);
            $this->tabelas = $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            header("Location:index.php?msg=2");
            exit;
        }
    }

    private function buscaAtributos($nomeTabela) {
        $sql = "SHOW COLUMNS FROM " . $nomeTabela;
        return $this->con->query($sql)->fetchAll(PDO::FETCH_OBJ);
    }

    private function gerarClassesModel() {
        foreach ($this->tabelas as $tabela) {
            $nomeTabela = array_values($tabela)[0];
            $atributos = $this->buscaAtributos($nomeTabela);
            $declaracoes = "";
            $getSet = "";

            foreach ($atributos as $atributo) {
                $campo = $atributo->Field;
                $declaracoes .= "\tprivate \${$campo};\n";

                $metodo = ucfirst($campo);
                $getSet .= "\tfunction get{$metodo}() {\n";
                $getSet .= "\t\treturn \$this->{$campo};\n\t}\n";
                $getSet .= "\tfunction set{$metodo}(\${$campo}) {\n";
                $getSet .= "\t\t\$this->{$campo} = \${$campo};\n\t}\n";
            }

            $nomeClasse = ucfirst($nomeTabela);
            $conteudo = <<<EOT
<?php
class {$nomeClasse} {
{$declaracoes}
{$getSet}
}
?>
EOT;
            file_put_contents("sistema/model/{$nomeClasse}.php", $conteudo);
        }
    }

    private function gerarClasseConexao() {
        $conteudo = <<<EOT
<?php
class Conexao {
    private \$server;
    private \$banco;
    private \$usuario;
    private \$senha;

    function __construct() {
        \$this->server = '{$this->servidor}';
        \$this->banco = '{$this->banco}';
        \$this->usuario = '{$this->usuario}';
        \$this->senha = '{$this->senha}';
    }

    function conectar() {
        try {
            \$conn = new PDO(
                "mysql:host=" . \$this->server . ";dbname=" . \$this->banco,
                \$this->usuario,
                \$this->senha
            );
            return \$conn;
        } catch (Exception \$e) {
            echo "Erro ao conectar com o Banco de dados: " . \$e->getMessage();
            return null;
        }
    }
}
?>
EOT;

        file_put_contents("sistema/model/conexao.php", $conteudo);
    }
}

new Creator();
?>

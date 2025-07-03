<?php
ini_set('display_errors', 1);
ini_set('display_startup_erros', 1);
error_reporting(E_ALL);

class Creator {
    private $con;
    private $servidor;
    private $banco;
    private $usuario;
    private $senha;
    private $tabelas;

    function __construct() {
        $this->criaDiretorios();
        $this->criaCss();
        $this->conectar();
        $this->buscaTabelas();
        $this->ClassesModel();
        $this->ClasseConexao();
        $this->ClassesControl();
        $this->classesView();
        $this->compactar();
        header("Location:index.php?msg=2");
    }

    function criaDiretorios() {
        $dirs = [
            "sistema",
            "sistema/model",
            "sistema/control",
            "sistema/view",
            "sistema/dao",
            "sistema/css"
        ];

        foreach ($dirs as $dir) {
            if (!file_exists($dir)) {
                if (!mkdir($dir, 0777, true)) {
                    header("Location:index.php?msg=0");
                }
            }
        }
    }

    function criaCss() {
        $css = <<<CSS
body {
    font-family: Arial, sans-serif;
    background-color: #f2f2f2;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}
form {
    background-color: white;
    padding: 20px 30px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    width: 300px;
}
.container {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;
}
#mensagem {
    background-color: #00c3fe;
    color: #0000bf;
    border: 1px solid #0000bf;
    padding: 10px 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    width: 100%;
    text-align: center;
    box-shadow: 0 0 5px rgba(0,0,0,0.1);
}
.mensagem_erro {
    background-color: #ffe0e0;
    color: #a70000;
    border: 1px solid #ffaaaa;
    padding: 10px 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    width: 100%;
    text-align: center;
    box-shadow: 0 0 5px rgba(0,0,0,0.1);
}
h2 {
    text-align: center;
    margin-bottom: 20px;
}
label {
    display: block;
    margin-bottom: 5px;
    margin-top: 15px;
}
input[type="text"],
input[type="password"] {
    width: 100%;
    padding: 8px;
    box-sizing: border-box;
}
button {
    margin-top: 20px;
    width: 100%;
    padding: 10px;
    background-color: #4CAF50;
    border: none;
    color: white;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
}
button:hover {
    background-color: #45a049;
}
CSS;
        file_put_contents("sistema/css/estilos.css", $css);
    }

    function conectar() {
        $this->servidor = $_POST["servidor"];
        $this->banco = $_POST["banco"];
        $this->usuario = $_POST["usuario"];
        $this->senha = $_POST["senha"];
        try {
            $this->con = new PDO(
                "mysql:host=" . $this->servidor . ";dbname=" . $this->banco,
                $this->usuario,
                $this->senha
            );
        } catch (Exception $e) {
            header("Location:index.php?msg=1");
        }
    }

    function buscaTabelas() {
        $sql = "SHOW TABLES";
        $query = $this->con->query($sql);
        $this->tabelas = $query->fetchAll(PDO::FETCH_ASSOC);
    }

    function buscaAtributos($nomeTabela) {
        $sql = "SHOW COLUMNS FROM " . $nomeTabela;
        return $this->con->query($sql)->fetchAll(PDO::FETCH_OBJ);
    }

    function ClassesModel() {
        foreach ($this->tabelas as $tabela) {
            $nomeTabela = array_values($tabela)[0];
            $atributos = $this->buscaAtributos($nomeTabela);
            $campos = "";
            $metodos = "";

            foreach ($atributos as $att) {
                $campo = $att->Field;
                $campos .= "\tprivate \${$campo};\n";
                $metodo = ucfirst($campo);
                $metodos .= "\tfunction get{$metodo}() {\n\t\treturn \$this->{$campo};\n\t}\n";
                $metodos .= "\tfunction set{$metodo}(\${$campo}) {\n\t\t\$this->{$campo} = \${$campo};\n\t}\n";
            }

            $classe = ucfirst($nomeTabela);
            $conteudo = "<?php\nclass {$classe} {\n{$campos}\n{$metodos}}\n?>";
            file_put_contents("sistema/model/{$classe}.php", $conteudo);
        }
    }

    function ClasseConexao() {
        $conteudo = <<<PHP
<?php
class Conexao {
    private \$server;
    private \$banco;
    private \$usuario;
    private \$senha;
    function __construct() {
        \$this->server = '[Informe aqui o servidor]';
        \$this->banco = '[Informe aqui o seu Banco de dados]';
        \$this->usuario = '[Informe aqui o usuário do banco de dados]';
        \$this->senha = '[Informe aqui a senha do banco de dados]';
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
            echo "Erro ao conectar: " . \$e->getMessage();
        }
    }
}
?>
PHP;
        file_put_contents("sistema/model/conexao.php", $conteudo);
    }

    function ClassesControl() {
        foreach ($this->tabelas as $tabela) {
            $nomeTabela = array_values($tabela)[0];
            $classe = ucfirst($nomeTabela);
            $conteudo = <<<PHP
<?php
require_once("../model/{$classe}.php");
require_once("../dao/{$classe}Dao.php");
class {$classe}Control {
    private \${$nomeTabela};
    private \$acao;
    private \$dao;
    public function __construct() {
        \$this->{$nomeTabela} = new {$classe}();
        \$this->dao = new {$classe}Dao();
        \$this->acao = \$_GET["a"];
        \$this->verificaAcao();
    }
    function verificaAcao(){}
    function inserir(){}
    function excluir(){}
    function alterar(){}
    function buscarId({$classe} \${$nomeTabela}){}
    function buscaTodos(){}
}
new {$classe}Control();
?>
PHP;
            file_put_contents("sistema/control/{$nomeTabela}Control.php", $conteudo);
        }
    }

    function classesView() {
        foreach ($this->tabelas as $tabela) {
            $nomeTabela = array_values($tabela)[0];
            $atributos = $this->buscaAtributos($nomeTabela);
            $campos = "";
    
            foreach ($atributos as $att) {
                if (strtolower($att->Key) === 'pri') {
                    continue;
                }
    
                $nome = $att->Field;
    
                $tipoInput = stripos($att->Type, 'int') !== false ? 'number' : 'text';
    
                $campos .= "<label for='{$nome}'>{$nome}</label>\n";
                $campos .= "<input type='{$tipoInput}' name='{$nome}' id='{$nome}'><br>\n";
            }
    
            $conteudo = <<<HTML
    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>Cadastro de {$nomeTabela}</title>
        <link rel="stylesheet" href="../css/estilos.css">
    </head>
    <body>
        <form method="post" action="#">
            {$campos}
            <button type="submit">Salvar</button>
        </form>
    </body>
    </html>
    HTML;
    
            file_put_contents("sistema/view/{$nomeTabela}.php", $conteudo);
        }
    }
    

    function compactar() {
        $folderToZip = 'sistema';
        $outputZip = 'sistema.zip';
        $zip = new ZipArchive();

        if ($zip->open($outputZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) return false;

        $folderPath = realpath($folderToZip);
        if (!is_dir($folderPath)) return false;

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($folderPath),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($folderPath) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();
        return true;
    }
}

new Creator();
?>

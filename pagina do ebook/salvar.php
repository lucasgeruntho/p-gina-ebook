<?php
date_default_timezone_set('America/Sao_Paulo'); // Define o fuso horário de Brasília

require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require 'phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Conexão com o banco (Hostinger)
$host = "localhost";
$usuario = "u229005482_lucasgeruntho";
$senha = "Vitorebety1@";
$banco = "u229005482_receitas";

$conexao = new mysqli($host, $usuario, $senha, $banco);
if ($conexao->connect_error) {
    die("Erro de conexão: " . $conexao->connect_error);
}

if (isset($_POST['nome'], $_POST['sobrenome'], $_POST['email'], $_POST['whatsapp'])) {
    $nome = trim($_POST['nome']);
    $sobrenome = trim($_POST['sobrenome']);
    $email = trim($_POST['email']);
    $whatsapp = trim($_POST['whatsapp']);
    $dataCadastro = date('Y-m-d H:i:s'); // Pega a data atual com horário de Brasília

    // Verifica se o e-mail já está cadastrado
    $verifica = $conexao->prepare("SELECT id FROM leads WHERE email = ?");
    $verifica->bind_param("s", $email);
    $verifica->execute();
    $verifica->store_result();

    if ($verifica->num_rows > 0) {
        echo "ja_cadastrado"; // <-- resposta simplificada para o JavaScript
    } else {
        // Prepara o INSERT com data_cadastro
        $sql = $conexao->prepare("INSERT INTO leads (nome, sobrenome, email, whatsapp, data_cadastro) VALUES (?, ?, ?, ?, ?)");
        $sql->bind_param("sssss", $nome, $sobrenome, $email, $whatsapp, $dataCadastro);

        if ($sql->execute()) {
            $mail = new PHPMailer(true);

            try {
                // Configuração do SMTP (Hostinger)
                $mail->CharSet = 'UTF-8';
                $mail->isSMTP();
                $mail->Host = 'smtp.hostinger.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'contato@receitasdechocolate.shop';
                $mail->Password = 'Vitorebety1@';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port = 465;

                $mail->setFrom('contato@receitasdechocolate.shop', 'Equipe Receitas de Chocolate');
                $mail->addAddress($email, "$nome $sobrenome");

                $mail->isHTML(true);
                $mail->Subject = ' Bem-vindo(a) ao Receitas de Chocolate!';
                $mail->Body = "
                    <html>
                    <body style='font-family: Arial, sans-serif; color: #333;'>
                        <h2>Olá, $nome!</h2>
                        <p>Obrigado por se cadastrar em nosso site. </p>
                        <p>Um abraço da equipe <strong>100 Receitas de Chocolate</strong>! </p>
                    </body>
                    </html>
                ";

                $mail->AltBody = "Olá, $nome! Obrigado por se cadastrar.";

                $mail->send();
                echo "sucesso"; // <-- resposta que o JS usa para continuar o fluxo
            } catch (Exception $e) {
                echo "sucesso"; // Mesmo se der erro no e-mail, ainda consideramos sucesso no cadastro
            }
        } else {
            echo "erro"; // Erro no INSERT
        }

        $sql->close();
    }

    $verifica->close();
} else {
    echo "campos_vazios"; // Se algum campo não vier
}

$conexao->close();
?>

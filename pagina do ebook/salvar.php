<?php
date_default_timezone_set('America/Sao_Paulo');

require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require 'phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Conexão com o banco
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
    $dataCadastro = date('Y-m-d H:i:s');

    // Verifica se o e-mail já está cadastrado
    $verifica = $conexao->prepare("SELECT id FROM leads WHERE email = ?");
    $verifica->bind_param("s", $email);
    $verifica->execute();
    $verifica->store_result();

    if ($verifica->num_rows > 0) {
        echo "ja_cadastrado";
    } else {
        // Insere na tabela leads
        $sql = $conexao->prepare("INSERT INTO leads (nome, sobrenome, email, whatsapp, data_cadastro) VALUES (?, ?, ?, ?, ?)");
        $sql->bind_param("sssss", $nome, $sobrenome, $email, $whatsapp, $dataCadastro);

        if ($sql->execute()) {
            $idLead = $conexao->insert_id;

            // Envia e-mail de boas-vindas
            $mail = new PHPMailer(true);
            try {
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
                $mail->Subject = 'Bem-vindo(a) ao Receitas de Chocolate!';
                $mail->Body = "
                    <html>
                    <body style='font-family: Arial, sans-serif; color: #333;'>
                        <h2>Olá, $nome!</h2>
                        <p>Obrigado por se cadastrar em nosso site.</p>
                        <p>Um abraço da equipe <strong>100 Receitas de Chocolate</strong>!</p>
                    </body>
                    </html>
                ";
                $mail->AltBody = "Olá, $nome! Obrigado por se cadastrar.";

                $mail->send();
            } catch (Exception $e) {
                // Continua mesmo se o e-mail falhar
            }

           

            // Agendar lembretes de 2, 5 e 10 minutos
$dataLembrete2 = date('Y-m-d H:i:s', strtotime('+2 minutes', strtotime($dataCadastro)));
$dataLembrete5 = date('Y-m-d H:i:s', strtotime('+5 minutes', strtotime($dataCadastro)));
$dataLembrete10 = date('Y-m-d H:i:s', strtotime('+10 minutes', strtotime($dataCadastro)));

$stmtLembrete = $conexao->prepare("INSERT INTO lembretes_whatsapp (lead_id, nome, telefone, data_cadastro, lembrete_2, lembrete_5, lembrete_10) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmtLembrete->bind_param("issssss", $idLead, $nome, $numeroComDDI, $dataCadastro, $dataLembrete2, $dataLembrete5, $dataLembrete10);
$stmtLembrete->execute();
$stmtLembrete->close();

            echo "sucesso";
        } else {
            echo "erro";
        }

        $sql->close();
    }

    $verifica->close();
} else {
    echo "campos_vazios";
}

$conexao->close();
?>

<?php
date_default_timezone_set('America/Sao_Paulo');

require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require 'phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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

    $verifica = $conexao->prepare("SELECT id FROM leads WHERE email = ?");
    $verifica->bind_param("s", $email);
    $verifica->execute();
    $verifica->store_result();

    if ($verifica->num_rows > 0) {
        echo "ja_cadastrado";
    } else {
        $sql = $conexao->prepare("INSERT INTO leads (nome, sobrenome, email, whatsapp, data_cadastro) VALUES (?, ?, ?, ?, ?)");
        $sql->bind_param("sssss", $nome, $sobrenome, $email, $whatsapp, $dataCadastro);

        if ($sql->execute()) {
            $idLead = $conexao->insert_id;

            // Envio de e-mail
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
            } catch (Exception $e) {}

            // Agendamento dos lembretes
            $numeroComDDI = '55' . preg_replace('/\D/', '', $whatsapp);

            $dataLembrete5min        = date('Y-m-d H:i:s', strtotime('+5 minutes', strtotime($dataCadastro)));
            $dataLembrete15min       = date('Y-m-d H:i:s', strtotime('+15 minutes', strtotime($dataCadastro)));
            $dataLembrete30min       = date('Y-m-d H:i:s', strtotime('+30 minutes', strtotime($dataCadastro)));
            $dataLembrete2000        = date('Y-m-d 20:00:00');
   

            $stmtLembrete = $conexao->prepare("INSERT INTO lembretes_whatsapp 
                (lead_id, nome, telefone, data_cadastro, 
                 lembrete_saudacao, lembrete_15min, lembrete_30min, 
                 lembrete_2000
                 ) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmtLembrete->bind_param(
                "isssssss",
                $idLead, $nome, $numeroComDDI, $dataCadastro,
                $dataLembrete5min, $dataLembrete15min, $dataLembrete30min,
                $dataLembrete2000
                
            );
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

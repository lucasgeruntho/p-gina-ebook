<?php

require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require 'phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

date_default_timezone_set('America/Sao_Paulo');

// Conexão com o banco
$host = "localhost";
$usuario = "u229005482_lucasgeruntho";
$senha = "Vitorebety1@";
$banco = "u229005482_receitas";

$conexao = new mysqli($host, $usuario, $senha, $banco);
if ($conexao->connect_error) {
    die("Erro: " . $conexao->connect_error);
}
$conexao->set_charset("utf8");

// Buscar leads que ainda não receberam o lembrete e se cadastraram há pelo menos 10 minutos
$sql = "SELECT id, nome, email FROM leads 
        WHERE email_lembrete_enviado = 0 
        AND data_cadastro <= NOW() - INTERVAL 10 MINUTE";

$result = $conexao->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $nome = $row['nome'];
        $email = $row['email'];

        $mail = new PHPMailer(true);

        try {
            $mail->CharSet = 'UTF-8';
            $mail->isSMTP();
            $mail->Host = 'smtp.hostinger.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'contato@receitasdechocolate.shop';
            $mail->Password = 'Vitorebety1@';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            $mail->setFrom('contato@receitasdechocolate.shop', 'Equipe Receitas de Chocolate');
            $mail->addAddress($email, $nome);

            $mail->isHTML(true);
            $mail->Subject = 'Não esqueça de garantir suas receitas! ';

            $mail->Body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; padding: 20px; background-color: #fff; color: #333;'>
                <h2 style='color: #6b3e26;'>Olá, $nome!</h2>
                <p style='font-size: 16px;'>Percebemos que você ainda não garantiu suas receitas exclusivas de chocolate...</p>
                <p style='font-size: 16px;'>Elas estão te esperando e vão adoçar seus dias de forma incrível!</p>
                
            
                <p style='font-size: 16px; text-align: center;'>Um abraço da <strong>Equipe Receitas de Chocolate</strong>!</p>
            </div>
            ";

            $mail->send();

            // Marcar como enviado
            $stmt = $conexao->prepare("UPDATE leads SET email_lembrete_enviado = 1 WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();

        } catch (Exception $e) {
            error_log("Erro ao enviar lembrete para $email: " . $mail->ErrorInfo);
        }
    }
}

$conexao->close();
?>

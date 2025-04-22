<?php
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require 'phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

date_default_timezone_set('America/Sao_Paulo');

// Conexão com banco
$host = "localhost";
$usuario = "u229005482_lucasgeruntho";
$senha = "Vitorebety1@";
$banco = "u229005482_receitas";

$conexao = new mysqli($host, $usuario, $senha, $banco);
if ($conexao->connect_error) {
    die("Erro: " . $conexao->connect_error);
}
$conexao->set_charset("utf8");

// Buscar leads cadastrados ontem e que ainda não receberam o e-mail da manhã
$sql = "SELECT id, nome, email FROM leads 
        WHERE email_manha_enviado = 0 
        AND DATE(data_cadastro) = CURDATE() - INTERVAL 1 DAY";

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
            $mail->Subject = 'Bom dia! Não perca essa chance!';
            $mail->Body = "
                <h2>Olá, $nome!</h2>
                <p>Passou bem a noite? Temos uma delícia te esperando!</p>
          
                <a href='https://www.youtube.com/watch?v=jojJWCpf5j4'>Gostaria de ver mais </a>
            ";

            $mail->send();

            // Atualizar campo
            $stmt = $conexao->prepare("UPDATE leads SET email_manha_enviado = 1 WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();

        } catch (Exception $e) {
            error_log("Erro ao enviar e-mail da manhã para $email: " . $mail->ErrorInfo);
        }
    }
}

$conexao->close();
?>

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

// Buscar leads que ainda não receberam o 2º e-mail
$sql = "SELECT id, nome, email FROM leads WHERE email_venda_enviado = 0 AND data_cadastro <= NOW() - INTERVAL 30 MINUTE";
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

            $mail->setFrom('contato@receitasdechocolate.shop', 'Equipe receitas de chocolate');
            $mail->addAddress($email, $nome);

            // Caminho absoluto da imagem no servidor
            $imagemPath = '/home/u229005482/domains/receitasdechocolate.shop/public_html/fotos_ebook_capa_e_etc/mandando_o_cliente_pra_finalizar_a_compra_oficial.png';

            if (file_exists($imagemPath)) {
                $mail->addEmbeddedImage($imagemPath, 'imgfinal');
            }

            $mail->isHTML(true);
            $mail->Subject = 'Sua próxima delícia te espera!';

            $mail->Body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; padding: 20px; color: #333; background-color: #fff;'>
                <h2 style='color: #6b3e26;'>Olá, $nome!</h2>
                <p style='font-size: 16px;'>Você está a um passo de receber as melhores receitas de chocolate que vão transformar sua cozinha!</p>
                
                <img src='cid:imgfinal'
                    alt='img-ebook'
                    style='width: 100%; max-width: 100%; height: auto; margin-top: 20px; border-radius: 8px; display: block;'>

                <p style='text-align: center; margin: 30px 0;'>
                    <a href='https://www.youtube.com/watch?v=jojJWCpf5j4'
                        style='display: inline-block; padding: 14px 28px; font-size: 16px; background-color: #6b3e26; color: white; text-decoration: none; border-radius: 6px;'>
                        Gostaria de não ver mais!
                    </a>
                </p>

                <p style='font-size: 16px; text-align: center;'>Atenciosamente Equipe</p>
            </div>
            ";

            $mail->send();

            $stmt = $conexao->prepare("UPDATE leads SET email_venda_enviado = 1 WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();

        } catch (Exception $e) {
            error_log("Erro ao enviar e-mail para $email: " . $mail->ErrorInfo);
        }
    }
}

$conexao->close();
?>

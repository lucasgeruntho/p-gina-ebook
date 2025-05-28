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

            // === WhatsApp formatado ===
            $numeroComDDI = '55' . preg_replace('/\D/', '', $whatsapp);

            // === Saudação (5 min) ===
            $base5 = date('Y-m-d H:i:s', strtotime('+5 minutes', strtotime($dataCadastro)));
            $verifica5 = $conexao->prepare("SELECT COUNT(*) as total FROM lembretes_whatsapp WHERE lembrete_saudacao = ?");
            $verifica5->bind_param("s", $base5);
            $verifica5->execute();
            $qtde5 = $verifica5->get_result()->fetch_assoc()['total'] ?? 0;
            $verifica5->close();
            $dataLembrete5 = date('Y-m-d H:i:s', strtotime("+". ($qtde5 * 2) ." seconds", strtotime($base5)));

            // === 15 min ===
            $base15 = date('Y-m-d H:i:s', strtotime('+15 minutes', strtotime($dataCadastro)));
            $verifica15 = $conexao->prepare("SELECT COUNT(*) as total FROM lembretes_whatsapp WHERE lembrete_15min = ?");
            $verifica15->bind_param("s", $base15);
            $verifica15->execute();
            $qtde15 = $verifica15->get_result()->fetch_assoc()['total'] ?? 0;
            $verifica15->close();
            $dataLembrete15 = date('Y-m-d H:i:s', strtotime("+". ($qtde15 * 2) ." seconds", strtotime($base15)));

            // === 30 min ===
            $base30 = date('Y-m-d H:i:s', strtotime('+30 minutes', strtotime($dataCadastro)));
            $verifica30 = $conexao->prepare("SELECT COUNT(*) as total FROM lembretes_whatsapp WHERE lembrete_30min = ?");
            $verifica30->bind_param("s", $base30);
            $verifica30->execute();
            $qtde30 = $verifica30->get_result()->fetch_assoc()['total'] ?? 0;
            $verifica30->close();
            $dataLembrete30 = date('Y-m-d H:i:s', strtotime("+". ($qtde30 * 2) ." seconds", strtotime($base30)));

            // === 90 min ===
            $base90 = date('Y-m-d H:i:s', strtotime('+90 minutes', strtotime($dataCadastro)));
            $verifica90 = $conexao->prepare("SELECT COUNT(*) as total FROM lembretes_whatsapp WHERE lembrete_90min = ?");
            $verifica90->bind_param("s", $base90);
            $verifica90->execute();
            $qtde90 = $verifica90->get_result()->fetch_assoc()['total'] ?? 0;
            $verifica90->close();
            $dataLembrete90 = date('Y-m-d H:i:s', strtotime("+". ($qtde90 * 2) ." seconds", strtotime($base90)));

            // === 20:00 (hoje) ===
            $base2000 = date('Y-m-d 20:00:00');
            $verifica2000 = $conexao->prepare("SELECT COUNT(*) as total FROM lembretes_whatsapp WHERE lembrete_2000 = ?");
            $verifica2000->bind_param("s", $base2000);
            $verifica2000->execute();
            $qtde2000 = $verifica2000->get_result()->fetch_assoc()['total'] ?? 0;
            $verifica2000->close();
            $dataLembrete2000 = date('Y-m-d H:i:s', strtotime("+". ($qtde2000 * 2) ." seconds", strtotime($base2000)));

            // === 11:30 (dia seguinte) ===
            $base1130 = date('Y-m-d 11:30:00', strtotime('+1 day'));
            $verifica1130 = $conexao->prepare("SELECT COUNT(*) as total FROM lembretes_whatsapp WHERE lembrete_1130_dia2 = ?");
            $verifica1130->bind_param("s", $base1130);
            $verifica1130->execute();
            $qtde1130 = $verifica1130->get_result()->fetch_assoc()['total'] ?? 0;
            $verifica1130->close();
            $dataLembrete1130 = date('Y-m-d H:i:s', strtotime("+". ($qtde1130 * 2) ." seconds", strtotime($base1130)));

            // === 16:30 (hoje) ===
            $base1630 = date('Y-m-d 16:30:00');
            $verifica1630 = $conexao->prepare("SELECT COUNT(*) as total FROM lembretes_whatsapp WHERE lembrete_1630 = ?");
            $verifica1630->bind_param("s", $base1630);
            $verifica1630->execute();
            $qtde1630 = $verifica1630->get_result()->fetch_assoc()['total'] ?? 0;
            $verifica1630->close();
            $dataLembrete1630 = date('Y-m-d H:i:s', strtotime("+". ($qtde1630 * 2) ." seconds", strtotime($base1630)));

            // === Inserção ===
            $stmt = $conexao->prepare("INSERT INTO lembretes_whatsapp 
                (lead_id, nome, telefone, data_cadastro,
                 lembrete_saudacao, lembrete_15min, lembrete_30min,
                 lembrete_90min, lembrete_2000, lembrete_1130_dia2, lembrete_1630
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param(
                "issssssssss",
                $idLead, $nome, $numeroComDDI, $dataCadastro,
                $dataLembrete5, $dataLembrete15, $dataLembrete30,
                $dataLembrete90, $dataLembrete2000, $dataLembrete1130, $dataLembrete1630
            );
            $stmt->execute();
            $stmt->close();

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

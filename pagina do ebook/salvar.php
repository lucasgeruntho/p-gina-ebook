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

if (isset($_POST['nome'], $_POST['whatsapp'])) {
    $nome = trim($_POST['nome']);
    $whatsapp = trim($_POST['whatsapp']);
    $dataCadastro = date('Y-m-d H:i:s');

    $verifica = $conexao->prepare("SELECT id FROM leads WHERE whatsapp = ?");
    $verifica->bind_param("s", $whatsapp);
    $verifica->execute();
    $verifica->store_result();

    if ($verifica->num_rows > 0) {
        echo "ja_cadastrado";
    } else {
        $sql = $conexao->prepare("INSERT INTO leads (nome, whatsapp, data_cadastro) VALUES (?, ?, ?)");
        $sql->bind_param("sss", $nome, $whatsapp, $dataCadastro);

        if ($sql->execute()) {
            $idLead = $conexao->insert_id;

            // (continua normalmente com as variáveis de lembretes...)


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
            $delay2000 = $idLead * 2;
            $dataLembrete2000 = date('Y-m-d H:i:s', strtotime("+$delay2000 seconds", strtotime($base2000)));

            // === 11:30 (dia seguinte) ===
            $base1130 = date('Y-m-d 11:30:00', strtotime('+1 day'));
            $delay1130 = $idLead * 2;
            $dataLembrete1130 = date('Y-m-d H:i:s', strtotime("+$delay1130 seconds", strtotime($base1130)));

           // === LEMBRETE 15:30 (dia seguinte)
            $base1530 = date('Y-m-d 15:30:00', strtotime('+1 day'));
            $delay1530 = $idLead * 2;
            $dataLembrete1530 = date('Y-m-d H:i:s', strtotime("+$delay1530 seconds", strtotime($base1530)));
            
            // === LEMBRETE 20:00 (dia seguinte)
            $base2000_dia2 = date('Y-m-d 20:00:00', strtotime('+1 day'));
            $delay2000_dia2 = $idLead * 2;
            $dataLembrete2000_dia2 = date('Y-m-d H:i:s', strtotime("+$delay2000_dia2 seconds", strtotime($base2000_dia2)));

            // === LEMBRETE 22:30 (dia seguinte)
            $base2230_dia2 = date('Y-m-d 22:30:00', strtotime('+1 day'));
            $delay2230_dia2 = $idLead * 2;
            $dataLembrete2230_dia2 = date('Y-m-d H:i:s', strtotime("+$delay2230_dia2 seconds", strtotime($base2230_dia2)));
            
            // === LEMBRETE 09:20 (3º dia contando o cadastro)
            $base0920_dia3 = date('Y-m-d 09:20:00', strtotime('+2 days'));
            $delay0920_dia3 = $idLead * 2;
            $dataLembrete0920_dia3 = date('Y-m-d H:i:s', strtotime("+$delay0920_dia3 seconds", strtotime($base0920_dia3)));
           
           
            // === LEMBRETE 15:45 (dia 3 após cadastro)
            $base1545_dia3 = date('Y-m-d 15:45:00', strtotime('+2 days'));
            $delay1545_dia3 = $idLead * 2;
            $dataLembrete1545_dia3 = date('Y-m-d H:i:s', strtotime("+$delay1545_dia3 seconds", strtotime($base1545_dia3)));

            // === LEMBRETE 20:00 (dia 3 após cadastro)
            $base2000_dia3 = date('Y-m-d 20:00:00', strtotime('+2 days')); 
            $delay2000_dia3 = $idLead * 2; 
            $dataLembrete2000_dia3 = date('Y-m-d H:i:s', strtotime("+$delay2000_dia3 seconds", strtotime($base2000_dia3)));

          
            // === LEMBRETE 11:00 (dia 4 após cadastro)
            $base1100_dia4 = date('Y-m-d 11:00:00', strtotime('+3 days'));
            $delay1100_dia4 = $idLead * 2;
            $dataLembrete1100_dia4 = date('Y-m-d H:i:s', strtotime("+$delay1100_dia4 seconds", strtotime($base1100_dia4)));

            // === LEMBRETE 18:00 (dia 4 após cadastro)
            $base1800_dia4 = date('Y-m-d 18:00:00', strtotime('+3 days'));
            $delay1800_dia4 = $idLead * 2;
            $dataLembrete1800_dia4 = date('Y-m-d H:i:s', strtotime("+$delay1800_dia4 seconds", strtotime($base1800_dia4)));

            // === LEMBRETE 21:20 (dia 4 após cadastro)
            $base2120_dia4 = date('Y-m-d 21:20:00', strtotime('+3 days'));
            $delay2120_dia4 = $idLead * 2;
            $dataLembrete2120_dia4 = date('Y-m-d H:i:s', strtotime("+$delay2120_dia4 seconds", strtotime($base2120_dia4)));

            // === LEMBRETE 10:00 (dia 5 após cadastro)
            $base1000_dia5 = date('Y-m-d 10:00:00', strtotime('+4 days')); 
            $delay1000_dia5 = $idLead * 2;
            $dataLembrete1000_dia5 = date('Y-m-d H:i:s', strtotime("+$delay1000_dia5 seconds", strtotime($base1000_dia5)));



            // === LEMBRETE 15:45 (dia 5 após cadastro)
            $base1545_dia5 = date('Y-m-d 15:45:00', strtotime('+4 days')); 
            $delay1545_dia5 = $idLead * 2;
            $dataLembrete1545_dia5 = date('Y-m-d H:i:s', strtotime("+$delay1545_dia5 seconds", strtotime($base1545_dia5)));


            // === LEMBRETE 18:22 (dia 5 após cadastro)
            $base1822_dia5 = date('Y-m-d 18:22:00', strtotime('+4 days'));
            $delay1822_dia5 = $idLead * 2;
            $dataLembrete1822_dia5 = date('Y-m-d H:i:s', strtotime("+$delay1822_dia5 seconds", strtotime($base1822_dia5)));

            // === LEMBRETE 21:22 (dia 5 após cadastro)
            $base2122_dia5 = date('Y-m-d 21:22:00', strtotime('+4 days'));
            $delay2122_dia5 = $idLead * 2;
            $dataLembrete2122_dia5 = date('Y-m-d H:i:s', strtotime("+$delay2122_dia5 seconds", strtotime($base2122_dia5)));

            // === LEMBRETE 15:45 (dia 6 após cadastro)
            $base1545_dia6 = date('Y-m-d 15:45:00', strtotime('+5 days'));
            $delay1545_dia6 = $idLead * 2;
            $dataLembrete1545_dia6 = date('Y-m-d H:i:s', strtotime("+$delay1545_dia6 seconds", strtotime($base1545_dia6)));

            // === LEMBRETE 18:20 (dia 6 após cadastro)
            $base1820_dia6 = date('Y-m-d 18:20:00', strtotime('+5 days'));
            $delay1820_dia6 = $idLead * 2;
            $dataLembrete1820_dia6 = date('Y-m-d H:i:s', strtotime("+$delay1820_dia6 seconds", strtotime($base1820_dia6)));


            // === Inserção ===
            $stmt = $conexao->prepare("INSERT INTO lembretes_whatsapp 
                (lead_id, nome, telefone, data_cadastro,
                 lembrete_saudacao, lembrete_15min, lembrete_30min,
                 lembrete_90min, lembrete_2000, lembrete_1130_dia2, lembrete_1530_dia2, lembrete_2000_dia2, lembrete_2230_dia2, lembrete_0920_dia3,
                 lembrete_1545_dia3, lembrete_2000_dia3, lembrete_1100_dia4, lembrete_1800_dia4, lembrete_2120_dia4, lembrete_1000_dia5,
                 lembrete_1545_dia5, lembrete_1822_dia5, lembrete_2122_dia5, lembrete_1545_dia6, lembrete_1820_dia6
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param(
                "issssssssssssssssssssssss",
                $idLead, $nome, $numeroComDDI, $dataCadastro,
                $dataLembrete5, $dataLembrete15, $dataLembrete30,
                $dataLembrete90, $dataLembrete2000, $dataLembrete1130, $dataLembrete1530, $dataLembrete2000_dia2, $dataLembrete2230_dia2, $dataLembrete0920_dia3,
                $dataLembrete1545_dia3, $dataLembrete2000_dia3, $dataLembrete1100_dia4, $dataLembrete1800_dia4,
                $dataLembrete2120_dia4, $dataLembrete1000_dia5, $dataLembrete1545_dia5, $dataLembrete1822_dia5, $dataLembrete2122_dia5,
                $dataLembrete1545_dia6, $dataLembrete1820_dia6
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

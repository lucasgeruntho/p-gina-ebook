<?php
date_default_timezone_set('America/Sao_Paulo');

// Conexão com o banco
$host = "localhost";
$usuario = "u229005482_lucasgeruntho";
$senha = "Vitorebety1@";
$banco = "u229005482_receitas";

$conexao = new mysqli($host, $usuario, $senha, $banco);
if ($conexao->connect_error) {
    die("Erro de conexão: " . $conexao->connect_error);
}

$agora = date('Y-m-d H:i:s');

// Busca os leads agendados para agora ou antes, e ainda não enviados
$sql = "SELECT * FROM lembretes_whatsapp WHERE lembrete_saudacao <= ? AND enviado_saudacao = 0";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("s", $agora);
$stmt->execute();
$result = $stmt->get_result();

// Envia para cada lead
while ($row = $result->fetch_assoc()) {
    $numero = $row['telefone'];
    $nome = $row['nome'];

    $mensagem = "🍫 Olá $nome! Bem-vindo(a) ao 100 Receitas de Chocolate! Temos uma surpresa deliciosa esperando por você. 👉 https://receitasdechocolate.shop";

    $url = "https://api.z-api.io/instances/3E068112EFBD7038B6087AC1D8277FBB/token/7395858EE9E120B3607D4943/send-text";
    $clientToken = 'F7c6fe46c0fc44bd6a2fc3fc298b23a52S';

    $dados = [
        "phone" => $numero,
        "message" => $mensagem
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Client-Token: ' . $clientToken
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);

    // Marca como enviado
    $update = $conexao->prepare("UPDATE lembretes_whatsapp SET enviado_saudacao = 1 WHERE id = ?");
    $update->bind_param("i", $row['id']);
    $update->execute();
    $update->close();

    // Delay entre os envios
    sleep(2);
}

$stmt->close();
$conexao->close();
?>

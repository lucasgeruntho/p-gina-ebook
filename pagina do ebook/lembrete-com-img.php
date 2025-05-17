<?php
date_default_timezone_set('America/Sao_Paulo');

$host = "localhost";
$usuario = "u229005482_lucasgeruntho";
$senha = "Vitorebety1@";
$banco = "u229005482_receitas";

$conexao = new mysqli($host, $usuario, $senha, $banco);
if ($conexao->connect_error) {
    die("Erro: " . $conexao->connect_error);
}

$agora = date('Y-m-d H:i:s');

// Buscar leads cujo lembrete de 3 minutos já passou e ainda não foi enviado
$sql = "SELECT * FROM lembretes_whatsapp 
        WHERE lembrete_3min <= ? AND enviado_3min = 0";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("s", $agora);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $numero = $row['telefone'];
    $nome = $row['nome'];

    // Mensagem de texto acima
    $mensagem = "🎁 Olá $nome! Olha só o que preparamos para você:";

    // URL da imagem (substitua por sua imagem real)
    $imagemUrl = "https://receitasdechocolate.shop/fotos_ebook_capa_e_etc/mandando_o_cliente_pra_finalizar_a_compra_oficial.png";

    // API Z-API
    $url = "https://api.z-api.io/instances/3E068112EFBD7038B6087AC1D8277FBB/token/7395858EE9E120B3607D4943/send-image";
    $clientToken = 'F7c6fe46c0fc44bd6a2fc3fc298b23a52S';

    $dados = [
        "phone" => $numero,
        "image" => $imagemUrl,
        "caption" => $mensagem
    ];

    // Enviar imagem via curl
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

    // Marcar como enviado
    $update = $conexao->prepare("UPDATE lembretes_whatsapp SET enviado_3min = 1 WHERE id = ?");
    $update->bind_param("i", $row['id']);
    $update->execute();
    $update->close();
}

$stmt->close();
$conexao->close();
?>

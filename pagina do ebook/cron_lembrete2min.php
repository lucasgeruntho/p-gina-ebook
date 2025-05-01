<?php
date_default_timezone_set('America/Sao_Paulo');

// Conexão com o banco
$host = "localhost";
$usuario = "u229005482_lucasgeruntho";
$senha = "Vitorebety1@";
$banco = "u229005482_receitas";

$conexao = new mysqli($host, $usuario, $senha, $banco);
if ($conexao->connect_error) {
    die("Erro na conexão: " . $conexao->connect_error);
}

$agora = date('Y-m-d H:i:s');

// Seleciona os lembretes que devem ser enviados agora
$sql = "SELECT id, nome, telefone FROM lembretes_whatsapp
        WHERE lembrete_2 <= ? AND enviado_2 = 0";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("s", $agora);
$stmt->execute();
$resultado = $stmt->get_result();

while ($row = $resultado->fetch_assoc()) {
    $numero = preg_replace('/\D/', '', $row['telefone']);
    $mensagem = "*{$row['nome']}, olha essa receita maravilhosa!* 🍫\nClique abaixo e confira!";
    $imagemUrl = "https://receitasdechocolate.shop/fotos_ebook_capa_e_etc/mandando_o_cliente_pra_finalizar_a_compra_oficial.png"; // Substitua pela sua imagem real
    $link = "https://receitasdechocolate.shop";

    $dados = [
        "phone" => $numero,
        "message" => $mensagem . "\n\n" . $link,
        "image" => $imagemUrl
    ];

    $url = "https://api.z-api.io/instances/3E068112EFBD7038B6087AC1D8277FBB/token/7395858EE9E120B3607D4943/send-image";
    $clientToken = 'F7c6fe46c0fc44bd6a2fc3fc298b23a52S';

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Client-Token: ' . $clientToken
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $resposta = curl_exec($ch);
    curl_close($ch);

    // Marca como enviado
    $update = $conexao->prepare("UPDATE lembretes_whatsapp SET enviado_2 = 1 WHERE id = ?");
    $update->bind_param("i", $row['id']);
    $update->execute();
    $update->close();
}

$stmt->close();
$conexao->close();
?>

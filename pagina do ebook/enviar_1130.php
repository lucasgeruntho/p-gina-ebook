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

$sql = "SELECT * FROM lembretes_whatsapp 
        WHERE lembrete_1130 <= ? AND enviado_1130 = 0";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("s", $agora);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $numero = $row['telefone'];
    $nome = $row['nome'];

    $mensagem = "☀️ Bom dia, $nome! 🍫 Sobremesa Especial para o Almoço? Que tal um Sorvete de Chocolate Trufado caseiro? 😍
    Nada melhor que finalizar o almoço com uma sobremesa geladinha, cremosa e feita com muito sabor! 💖 Essa receita é perfeita para hoje: prática, deliciosa e com ingredientes simples que você provavelmente já tem em casa.
    Confira o que você vai precisar para preparar essa tentação de chocolate trufado agora mesmo:

🛒 Ingredientes:

2 caixas de creme de leite (400g)

1 lata de leite condensado

1 xícara de leite (240ml)

1/2 xícara de cacau em pó 50%

100g de chocolate meio amargo derretido

1 colher de sopa de essência de baunilha
    ";

    $imagemUrl = "https://receitasdechocolate.shop/fotos_ebook_capa_e_etc/receitas_sorvete_de_chocolate_trufado.jpg"; 

    $url = "https://api.z-api.io/instances/3E068112EFBD7038B6087AC1D8277FBB/token/7395858EE9E120B3607D4943/send-image";
    $clientToken = 'F7c6fe46c0fc44bd6a2fc3fc298b23a52S';

    $dados = [
        "phone" => $numero,
        "image" => $imagemUrl,
        "caption" => $mensagem
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

    $update = $conexao->prepare("UPDATE lembretes_whatsapp SET enviado_1130 = 1 WHERE id = ?");
    $update->bind_param("i", $row['id']);
    $update->execute();
    $update->close();
}

$stmt->close();
$conexao->close();
?>

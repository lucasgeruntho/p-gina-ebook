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
        WHERE lembrete_1630 <= ? AND enviado_1630 = 0";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("s", $agora);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $numero = $row['telefone'];
    $nome = $row['nome'];

    $mensagem = "🍫 Olá $nome! 🍫☕ Café da Tarde com Sabor de Chocolate! Que tal um Brownie de Chocolate com Nutella e Nozes? 😍

    Aquela pausa merecida da tarde pode ficar ainda mais gostosa com esse brownie irresistível: por fora macio, por dentro cremoso, com cobertura de Nutella e o crocante das nozes. Uma explosão de sabor que combina perfeitamente com um café!

🛒 Anota aí os ingredientes:

150g de manteiga sem sal

200g de chocolate meio amargo picado

3 ovos grandes

150g de açúcar refinado

75g de farinha de trigo

30g de cacau em pó sem açúcar

1/2 colher de chá de sal

200g de Nutella

100g de nozes picadas grosseiramente
    
Uma sobremesa geladinha, cremosa e irresistível, que derrete na boca e conquista no primeiro pedaço! ❄️🍫

    ";
    $imagemUrl = "https://receitasdechocolate.shop/fotos_ebook_capa_e_etc/receita_brownie_de_chocolate_com_nozes.jpg"; 

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

    // Marca como enviado
    $update = $conexao->prepare("UPDATE lembretes_whatsapp SET enviado_1630 = 1 WHERE id = ?");
    $update->bind_param("i", $row['id']);
    $update->execute();
    $update->close();
}

$stmt->close();
$conexao->close();
?>

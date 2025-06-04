<?php
date_default_timezone_set('America/Sao_Paulo');

$host = "localhost";
$usuario = "u229005482_lucasgeruntho";
$senha = "Vitorebety1@";
$banco = "u229005482_receitas";

$conexao = new mysqli($host, $usuario, $senha, $banco);
if ($conexao->connect_error) {
    die("Erro de conexão: " . $conexao->connect_error);
}

$agora = date('Y-m-d H:i:s');

$sql = "SELECT * FROM lembretes_whatsapp WHERE lembrete_1545_dia3 <= ? AND enviado_1545_dia3 = 0";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("s", $agora);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $numero = $row['telefone'];
    $nome = $row['nome'];

    $mensagem = "🍫 Olá $nome! seu Café da Tarde te espera! lá vai uma receita: ☕
    
Que tal deixar a tarde ainda mais gostosa com um brownie super macio, coberto com Nutella cremosa e o toque crocante das nozes? Uma combinação perfeita para acompanhar aquele café quentinho ou um chá especial! 

🛒 Ingredientes:

150g de manteiga sem sal

200g de chocolate meio amargo picado

3 ovos grandes

150g de açúcar refinado

75g de farinha de trigo

30g de cacau em pó sem açúcar

1/2 colher de chá de sal

200g de Nutella

100g de nozes picadas grosseiramente";

    $imagem = "https://receitasdechocolate.shop/fotos_ebook_capa_e_etc/brownie_de_chocolate_com_cobertura_de_nutella_e_nozes.jpg";

    $url = "https://api.z-api.io/instances/3E068112EFBD7038B6087AC1D8277FBB/token/7395858EE9E120B3607D4943/send-image";
    $clientToken = 'F7c6fe46c0fc44bd6a2fc3fc298b23a52S';

    $dados = [
        "phone" => $numero,
        "caption" => $mensagem,
        "image" => $imagem
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

    $update = $conexao->prepare("UPDATE lembretes_whatsapp SET enviado_1545_dia3 = 1 WHERE id = ?");
    $update->bind_param("i", $row['id']);
    $update->execute();
    $update->close();

    sleep(2);
}

$stmt->close();
$conexao->close();
?>

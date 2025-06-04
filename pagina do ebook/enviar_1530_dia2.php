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

// Busca todos os leads com lembrete agendado para 15:30 do dia seguinte e ainda não enviado
$sql = "SELECT * FROM lembretes_whatsapp 
        WHERE lembrete_1530_dia2 <= ? 
        AND enviado_1530_dia2 = 0";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("s", $agora);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $numero = $row['telefone'];
    $nome = $row['nome'];

    // Mensagem personalizada com imagem
    $mensagem = "🍫 Olá $nome!

🍪🍫 Vontade de um docinho caseiro? Então anota essa: Cookies de Chocolate Trufado! 🍪

Crocantes por fora, macios por dentro e com um recheio trufado que derrete na boca... esses cookies são perfeitos para acompanhar um café, surpreender alguém ou simplesmente se mimar com algo delicioso!

🛒 Ingredientes:

🥣 Para os Cookies:

115g de manteiga sem sal (em temperatura ambiente)

100g de açúcar granulado

75g de açúcar mascavo claro

1 ovo grande

1 colher de chá de extrato de baunilha

175g de farinha de trigo

30g de cacau em pó sem açúcar

1/2 colher de chá de bicarbonato de sódio

1/4 colher de chá de sal

🍫 Para o Recheio Trufado:

100g de chocolate meio amargo picado

50ml de creme de leite fresco (35% de gordura)";

    $imagem = "https://receitasdechocolate.shop/fotos_ebook_capa_e_etc/cookies_de_chocolate_trufado.jpg";

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

    // Marcar como enviado
    $update = $conexao->prepare("UPDATE lembretes_whatsapp SET enviado_1530_dia2 = 1 WHERE id = ?");
    $update->bind_param("i", $row['id']);
    $update->execute();
    $update->close();

    sleep(2); // Delay de envio para evitar sobrecarga
}

$stmt->close();
$conexao->close();
?>

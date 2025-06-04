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

$sql = "SELECT * FROM lembretes_whatsapp WHERE lembrete_1000_dia5 <= ? AND enviado_1000_dia5 = 0";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("s", $agora);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $numero = $row['telefone'];
    $nome = $row['nome'];

    $mensagem = "☀️ Bom dia, $nome! 🍫🥧 Sugestão de sobremesa para o almoço de hoje: Tartelete de Chocolate! 😍
    
🥧 Crocante por fora, cremosa por dentro e com aquele sabor intenso de chocolate que derrete na boca… ✨

🛒 Ingredientes:

🥧 Para a Massa (Pâte Sablée):

1-2 xícara (95g) de farinha de trigo

1-2 xícara (50g) de açúcar de confeiteiro

1 colher de chá de sal

100g de manteiga sem sal

1 ovo grande

1 a 2 colheres de sopa de água gelada (se necessário)

🍫 Para o Recheio de Chocolate:

200g de chocolate meio amargo ou amargo (de boa qualidade), picado finamente

1 xícara (240ml) de creme de leite fresco (35% de gordura)

2 colheres de sopa de manteiga sem sal

1 colher de sopa de açúcar (opcional, dependendo do chocolate usado)

1 colher de chá de extrato de baunilha (opcional)

Uma pitada de sal

💡 Prepare pela manhã e deixe na geladeira até a hora de servir — elas ficam ainda mais gostosas bem geladinhas!";


    $imagem = "https://receitasdechocolate.shop/fotos_ebook_capa_e_etc/tartelete-de-chocolate.jpg";

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

    $update = $conexao->prepare("UPDATE lembretes_whatsapp SET enviado_1000_dia5 = 1 WHERE id = ?");
    $update->bind_param("i", $row['id']);
    $update->execute();
    $update->close();

    sleep(2);
}

$stmt->close();
$conexao->close();
?>

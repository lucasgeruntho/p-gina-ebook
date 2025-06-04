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

$sql = "SELECT * FROM lembretes_whatsapp WHERE lembrete_1130_dia2 <= ? AND enviado_1130_dia2 = 0";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("s", $agora);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $numero = $row['telefone'];
    $nome = $row['nome'];

    $mensagem = "Olá $nome!
    
🍫🌰 Sobremesa cremosa e irresistível: Sorvete de Chocolate com Creme de Avelã! 😍🍫

Se você ama chocolate com aquele toque sofisticado de avelã, essa receita é perfeita! Super fácil de fazer.

Confira os ingredientes e já separa tudo aí! 👇

🛒 Ingredientes:

🍦 Para o sorvete:

2 xícaras (chá) de creme de leite fresco

1 xícara (chá) de leite integral

¾ xícara (chá) de açúcar

1 colher (chá) de essência de baunilha

🍫 Para a cobertura:

½ xícara (chá) de creme de avelã (Nutella ou similar)

2 colheres (sopa) de leite quente
    
✨ Fica uma delícia servido em taças com cobertura extra ou com raspinhas de chocolate por cima!✨";

    $imagem = "https://receitasdechocolate.shop/fotos_ebook_capa_e_etc/receita_sorvete_de_chocolate_com_creme_de_avela.jpg";

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

    $update = $conexao->prepare("UPDATE lembretes_whatsapp SET enviado_1130_dia2 = 1 WHERE id = ?");
    $update->bind_param("i", $row['id']);
    $update->execute();
    $update->close();

    sleep(2);
}

$stmt->close();
$conexao->close();
?>

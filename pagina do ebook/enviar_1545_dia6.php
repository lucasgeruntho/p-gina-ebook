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

$sql = "SELECT * FROM lembretes_whatsapp WHERE lembrete_1545_dia6 <= ? AND enviado_1545_dia6 = 0";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("s", $agora);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $numero = $row['telefone'];
    $nome = $row['nome'];

    $mensagem = "🍰 Olá $nome! 🍓🍫 Dica rápida e deliciosa pro café da tarde!

Que tal preparar um Wrap com Nutella, morangos e chocolate?

🛒 Ingredientes:
1 wrap grande (tortilla de trigo, pão folha ou similar)
2 colheres de sopa de Nutella (ou outro creme de avelã de sua preferência)
4-5 morangos médios, lavados e fatiados
20-30g de chocolate ao leite ou meio amargo, picado em pedaços pequenos (ou raspas)
  
✨ Sirva enroladinho ou dobrado como panqueca, e se quiser, finalize com açúcar de confeiteiro por cima. Vai bem com café, chá ou até com suco gelado!";

    $imagem = "https://receitasdechocolate.shop/fotos_ebook_capa_e_etc/wrap-tradicional-com-nutella-morango-e-chocolate.jpg";

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

    $update = $conexao->prepare("UPDATE lembretes_whatsapp SET enviado_1545_dia6 = 1 WHERE id = ?");
    $update->bind_param("i", $row['id']);
    $update->execute();
    $update->close();

    sleep(2);
}

$stmt->close();
$conexao->close();
?>

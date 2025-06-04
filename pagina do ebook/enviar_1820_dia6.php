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

$sql = "SELECT * FROM lembretes_whatsapp WHERE lembrete_1820_dia6 <= ? AND enviado_1820_dia6 = 0";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("s", $agora);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $numero = $row['telefone'];
    $nome = $row['nome'];

    $mensagem = "$nome, 🎉🍫 Tem novidade saindo do forno… literalmente!

Chegou a Pizza de Chocolate com Prestígio! Uma explosão de sabor pra quem AMA chocolate com coco! 😍

Imagina uma massa fofinha com base de chocolate, coberta com creme de coco, chocolate derretido e pedacinhos de Prestígio por cima... Simplesmente irresistível! 🤤

📌 Ideal pra inovar na sobremesa, surpreender no café da tarde ou adoçar o fim de semana com algo diferente e super fácil de fazer!

🛒 Ingredientes como:

Para a Massa de Chocolate:

2 xícaras (250g) de farinha de trigo
1/4 xícara (25g) de cacau em pó 100%
1/4 xícara (50g) de açúcar
1/2 colher de chá de sal
1 pacote (10g) de fermento biológico seco
1 xícara (240ml) de leite morno
2 colheres de sopa de manteiga sem sal derretida
1 ovo grande
1 colher de chá de extrato de baunilha (opcional)

Para a Cobertura de Prestígio:

1 lata (395g) de leite condensado
100g de coco ralado seco (ou fresco ralado)
1 colher de sopa de manteiga sem sal
1/2 xícara (120ml) de creme de leite 
Cobertura de chocolate ao leite derretido (aproximadamente 150-200g) para finalizar

💬 Ficou com água na boca? Essa delícia vai conquistar seu paladar e deixar sua cozinha ainda mais doce! 🍫🍕

Clique e finalize sua compra agora mesmo.
👉 https://pay.kiwify.com.br/ETvzGbe";
    
    $imagem = "https://receitasdechocolate.shop/fotos_ebook_capa_e_etc/pizza-de-chocolate-com-prestigio.jpg";

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

    $update = $conexao->prepare("UPDATE lembretes_whatsapp SET enviado_1820_dia6 = 1 WHERE id = ?");
    $update->bind_param("i", $row['id']);
    $update->execute();
    $update->close();

    sleep(2);
}

$stmt->close();
$conexao->close();
?>

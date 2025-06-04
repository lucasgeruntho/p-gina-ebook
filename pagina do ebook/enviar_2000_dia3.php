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

$sql = "SELECT * FROM lembretes_whatsapp WHERE lembrete_2000_dia3 <= ? AND enviado_2000_dia3 = 0";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("s", $agora);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $numero = $row['telefone'];
    $nome = $row['nome'];

    $mensagem = "🍫✨ Sobremesa para o Jantar? Que tal um Pudim de Chocolate com Oreo? 😍
    
Depois de um jantar caprichado, nada melhor que fechar a noite com uma sobremesa cremosa e com toque crocante do Oreo! Uma combinação surpreendente que vai conquistar todos à mesa! 💖
   
🛒 Ingredientes:

🍮 Para o Pudim:

1 lata de leite condensado (395g)

2 caixas de creme de leite (400g)

1 xícara (200ml) de leite integral

3 colheres de sopa de chocolate em pó 50% ou 100%

1 colher de sopa de amido de milho

3 ovos inteiros

1 colher de chá de essência de baunilha (opcional)

🍯 Para a Calda (opcional):

1 xícara (200g) de açúcar

½ xícara (120ml) de água

🍪 Para Montar:

1 pacote de biscoito Oreo (14 unidades)

✨ Ideal para preparar com antecedência e servir geladinho. Uma explosão de sabor!";
    $imagem = "https://receitasdechocolate.shop/fotos_ebook_capa_e_etc/pudim_de_chocolate_com_oreo.jpg";

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

    $update = $conexao->prepare("UPDATE lembretes_whatsapp SET enviado_2000_dia3 = 1 WHERE id = ?");
    $update->bind_param("i", $row['id']);
    $update->execute();
    $update->close();

    sleep(2);
}

$stmt->close();
$conexao->close();
?>

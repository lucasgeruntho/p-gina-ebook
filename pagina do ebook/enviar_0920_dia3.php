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

$sql = "SELECT * FROM lembretes_whatsapp WHERE lembrete_0920_dia3 <= ? AND enviado_0920_dia3 = 0";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("s", $agora);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $numero = $row['telefone'];
    $nome = $row['nome'];

    $mensagem = "☀️☕ Bom dia, $nome! que tal preparar um Mil-Folhas de Chocolate? 🥐✨
    
Um café da manhã assim transforma qualquer dia comum em um momento especial! ☕💛

🛒 Ingredientes:

🥐 Para a Massa Folhada:

500g de massa folhada pronta (de boa qualidade)

Farinha de trigo para polvilhar

🍫 Para o Creme de Chocolate:

500ml de leite integral

150g de açúcar

50g de amido de milho

4 gemas de ovo peneiradas

100g de chocolate meio amargo picado

50g de manteiga sem sal

✨ Para a Cobertura:

200g de chocolate meio amargo picado

50g de creme de leite fresco (opcional, para um brilho extra)";

    $imagem = "https://receitasdechocolate.shop/fotos_ebook_capa_e_etc/mil-folhas-de-chocolate.jpg";

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

    $update = $conexao->prepare("UPDATE lembretes_whatsapp SET enviado_0920_dia3 = 1 WHERE id = ?");
    $update->bind_param("i", $row['id']);
    $update->execute();
    $update->close();

    sleep(2);
}

$stmt->close();
$conexao->close();
?>

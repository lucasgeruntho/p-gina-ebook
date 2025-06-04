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

$sql = "SELECT * FROM lembretes_whatsapp WHERE lembrete_1100_dia4 <= ? AND enviado_1100_dia4 = 0";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("s", $agora);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $numero = $row['telefone'];
    $nome = $row['nome'];

    $mensagem = "Bom dia, $nome! 🍫 ADOCE SEU DIA COM DESCONTO ESPECIAL! 🍫
    
Prepare-se para mergulhar no delicioso mundo do chocolate com o nosso Livro '100 Receitas de Chocolate' uma seleção irresistível de sobremesas. todas com um ingrediente em comum: CHOCOLATE!
    
🎁 E o melhor: você acaba de ganhar 10% OFF pra aproveitar agora mesmo!
Use o cupom: CHOCOLATE10 para finalizar a compra.

💬 Já imaginou surpreender em momentos especiais ou simplesmente matar aquela vontade de Chocolate! Agora você pode!


Clique e finalize sua compra agora mesmo.
👉 https://pay.kiwify.com.br/ETvzGbe";

    $imagem = "https://receitasdechocolate.shop/fotos_ebook_capa_e_etc/mandando_o_cliente_pra_finalizar_a_compra.jpg";

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

    $update = $conexao->prepare("UPDATE lembretes_whatsapp SET enviado_1100_dia4 = 1 WHERE id = ?");
    $update->bind_param("i", $row['id']);
    $update->execute();
    $update->close();

    sleep(2);
}

$stmt->close();
$conexao->close();
?>

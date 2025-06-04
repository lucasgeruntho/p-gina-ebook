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

$sql = "SELECT * FROM lembretes_whatsapp WHERE lembrete_1822_dia5 <= ? AND enviado_1822_dia5 = 0";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("s", $agora);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $numero = $row['telefone'];
    $nome = $row['nome'];

    $mensagem = "🍫 $nome,  Tem até hoje pra garantir o seu eBook com desconto!⏳

Se você ama chocolate, não pode deixar passar essa oportunidade deliciosa! 😍

💡 O eBook “100 Receitas de Chocolate” está com 10% de desconto, mas só até hoje!⏳
São receitas incríveis que vão transformar seus momentos deixando mais perfeitas para adoçar sua rotina ou surpreender quem você ama!💡

📘 Se você já estava de olho, esse é o momento!
Corre e finalize sua compra agora para aproveitar o desconto antes que acabe! ⏳

👉 Garanta seu Ebook agora!
https://pay.kiwify.com.br/ETvzGbe

📩 Qualquer dúvida, me chama aqui! Estou à disposição.";
    
    $imagem = "https://receitasdechocolate.shop/fotos_ebook_capa_e_etc/mmandando_o_cliente_pra_finalizar_a_compra.jpg";


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

    $update = $conexao->prepare("UPDATE lembretes_whatsapp SET enviado_1822_dia5 = 1 WHERE id = ?");
    $update->bind_param("i", $row['id']);
    $update->execute();
    $update->close();

    sleep(2);
}

$stmt->close();
$conexao->close();
?>

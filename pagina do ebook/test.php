php
<?php
$numero = '5551997355794'; // Troca pelo número de destino
$mensagem = 'Mensagem de teste via Z-API!';

$dados = [
    "phone" => $numero,
    "message" => $mensagem
];

$url = "https://api.z-api.io/instances/3E068112EFBD7038B6087AC1D8277FBB/token/7395858EE9E120B3607D4943/send-text"; // Troca SUA_INSTANCIA e SEU_TOKEN

$clientToken = 'F7c6fe46c0fc44bd6a2fc3fc298b23a52S';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Client-Token: ' . $clientToken
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$resposta = curl_exec($ch);

if ($resposta === false) {
    echo 'Erro cURL: ' . curl_error($ch);
} else {
    echo $resposta;
}

curl_close($ch);
?>
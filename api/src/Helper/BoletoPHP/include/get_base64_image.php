<?php
// Caminho do arquivo contendo o código base64 images/boletoBase64/logosicrediBase64.php
$logoBase64_file_path = 'images/logoBase64.php';

$logosicrediBase64_file_path = 'images/boletoBase64/logosicrediBase64.php';
$logoItauBase64_file_path = 'images/boletoBase64/logoItauBase64.php';
$logoCaixaBase64_file_path = 'images/boletoBase64/logoCaixaBase64.php';
$logoBradescoBase64_file_path = 'images/boletoBase64/logoBradescoBase64.php';
$logoBBBase64_file_path = 'images/boletoBase64/logoBBBase64.php';


$Base64_1_file_path = 'images/boletoBase64/1-base64.php';
$Base64_2_file_path = 'images/boletoBase64/2-base64.php';
$Base64_3_file_path = 'images/boletoBase64/3-base64.php';
$Base64_6_file_path = 'images/boletoBase64/6-base64.php';
$Base64_b_file_path = 'images/boletoBase64/b-base64.php';
$Base64_p_file_path = 'images/boletoBase64/p-base64.php';

// Verificando se o arquivo existe
if (file_exists($logoBase64_file_path)) {
    // Lendo o conteúdo do arquivo
    $logoBase64_image = file_get_contents($logoBase64_file_path);
}
if (file_exists($logosicrediBase64_file_path)) {
    // Lendo o conteúdo do arquivo
    $logoSicredBase64_image = file_get_contents($logosicrediBase64_file_path);
}

if (file_exists($Base64_1_file_path)) {
    // Lendo o conteúdo do arquivo
    $Base64_1_image = file_get_contents($Base64_1_file_path);
}
if (file_exists($Base64_2_file_path)) {
    // Lendo o conteúdo do arquivo
    $Base64_2_image = file_get_contents($Base64_2_file_path);
}
if (file_exists($Base64_3_file_path)) {
    // Lendo o conteúdo do arquivo
    $Base64_3_image = file_get_contents($Base64_3_file_path);
}
if (file_exists($Base64_6_file_path)) {
    // Lendo o conteúdo do arquivo
    $Base64_6_image = file_get_contents($Base64_6_file_path);
}
if (file_exists($Base64_b_file_path)) {
    // Lendo o conteúdo do arquivo
    $Base64_b_image = file_get_contents($Base64_b_file_path);
}
if (file_exists($Base64_p_file_path)) {
    // Lendo o conteúdo do arquivo
    $Base64_p_image = file_get_contents($Base64_p_file_path);
}
if (file_exists($logoBBBase64_file_path)) {
    // Lendo o conteúdo do arquivo
    $logoBBBase64_image = file_get_contents($logoBBBase64_file_path);
}
if (file_exists($logoBradescoBase64_file_path)) {
    // Lendo o conteúdo do arquivo
    $logoBradescoBase64_image = file_get_contents($logoBradescoBase64_file_path);
}
if (file_exists($logoCaixaBase64_file_path)) {
    // Lendo o conteúdo do arquivo
    $logoCaixaBase64_image = file_get_contents($logoCaixaBase64_file_path);
}
if (file_exists($logoItauBase64_file_path)) {
    // Lendo o conteúdo do arquivo
    $logoItauBase64_image = file_get_contents($logoItauBase64_file_path);
}
?>
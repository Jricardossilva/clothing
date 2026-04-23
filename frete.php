<?php

require './config/conexao.php';

function getDistanciasSP()
{
    return [
        'SP' => 50,
        'RJ' => 430,
        'MG' => 500,
        'ES' => 850,
        'PR' => 400,
        'SC' => 700,
        'RS' => 1100,
        'BA' => 1500,
        'PE' => 2100,
        'CE' => 2600,
        'PB' => 2800,
        'RN' => 2900,
        'GO' => 900,
        'DF' => 1000,
        'MT' => 1400,
        'MS' => 1000,
        'AM' => 3900,
        'PA' => 3000,
        'MA' => 2600,
        'PI' => 2400,
        'AL' => 2200,
        'SE' => 2000,
        'RO' => 2800,
        'AC' => 3500,
        'AP' => 3300,
        'RR' => 4500,
        'TO' => 1800,
    ];
}

function calcularFreteSimulado($estadoDestino, $peso)
{
    $distancias = getDistanciasSP();

    $distancia = $distancias[$estadoDestino] ?? null;

    $valorBase = 10.00;
    $custoPorKm = 0.02;
    $custoPorKg = 5.00;

    $valor = $valorBase + ($distancia * $custoPorKm) + ($peso * $custoPorKg);

    $prazo = ceil($distancia / 500);

    return [
        'estado' => $estadoDestino,
        'valor_numerico' => $valor,
        'valor' => number_format($valor, 2, ',', '.'),
        'prazo' => $prazo
    ];
}

$estado = $_POST['estado'] ?? '';
$peso = $_POST['peso'] ?? 1;

if (!$estado || $peso <= 0) {
    echo json_encode(['erro' => 'Dados inválidos']);
    exit;
}

echo json_encode(calcularFreteSimulado($estado, $peso));
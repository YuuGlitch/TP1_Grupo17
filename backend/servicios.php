<?php
require_once 'config.php';

//La idea era usar esta función para verificar que en el campo opcional se ingrese un pokemon real
//quedo pendiente por falta de tiempo
function existePokemon(string $nombre): bool
{
    $url = "https://pokeapi.co/api/v2/pokemon/" . urlencode(strtolower($nombre));

    $ch = curl_init($url);
    if ($ch === false) return false;

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_NOBODY => true
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 10,
    ]);

    curl_exec($ch);

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    return $httpCode === 200;
}

// Queriamos usar resend como en clase para recibir un mail cada vez que se registre un usuario
//tambien pendiente a implementar
function pedirJSON($url, $metodo = "GET", $cuerpo = null, $cabeceras = []) {
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
    curl_setopt($curl, CURLOPT_TIMEOUT, 5);            
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $metodo);  
    curl_setopt($curl, CURLOPT_SSL_OPTIONS, CURLSSLOPT_NATIVE_CA);

    if ($cuerpo !== null) {                      
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($cuerpo));
        $cabeceras[] = "Content-Type: application/json";  
    }
    curl_setopt($curl, CURLOPT_HTTPHEADER, $cabeceras); 

    $respuesta = curl_exec($curl);                     
    $estado = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if ($respuesta === false) {
        return [0, null];
    }
    return [$estado, json_decode($respuesta, true)];
}


function enviarAviso($usuario) {
    $texto = "Se registró" . $usuario["email"];

    if (RESEND_API_KEY === "") {
        file_put_contents(__DIR__ . "/avisos.log",
            date("Y-m-d H:i:s") . " " . $texto . PHP_EOL, FILE_APPEND);
        return true;
    }

    [$estado, $datos] = pedirJSON(
        "https://api.resend.com/emails",
        "POST",
        [
            "from" => "App notas <onboarding@resend.dev>",
            "to" => [EMAIL_AVISOS],
            "subject" => "Nuevo Usuario",
            "html" => "<p>" . htmlspecialchars($texto) . "</p>"
        ],
        ["Authorization: Bearer " . RESEND_API_KEY]
    );

    if ($estado !== 200) {
        error_log("No se pudo enviar el aviso (" . $estado . "): " . json_encode($datos));
        return false;
    }
    return true;
}

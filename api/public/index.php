<?php

use App\Kernel;
use Symfony\Component\Debug\Debug;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;

require __DIR__ . '/../vendor/autoload.php';

/**
 * Função global para fazer dumps de forma mais prática
 */
function dd () {
    $numargs = func_num_args();
    for ($i = 0; $i < $numargs; $i++) {
        var_dump(func_get_arg($i));
    }

    die;
}

// The check is to ensure we don't use .env in production
if (! isset($_SERVER['APP_ENV'])) {
    if (! class_exists(Dotenv::class)) {
        throw new \RuntimeException('APP_ENV environment variable is not defined. You need to define environment variables for configuration or add "symfony/dotenv" as a Composer dependency to load variables from a .env file.');
    }
    (new Dotenv())->load(__DIR__ . '/../.env');
}

// $env = 'prod';

$env = $_SERVER['APP_ENV'] ?? 'dev';
// $debug = (bool) ($_SERVER['APP_DEBUG'] ?? ('prod' !== $env));
$debug = (bool) ($_SERVER['APP_DEBUG'] );


    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: *');
    header('Access-Control-Allow-Headers: *');
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        header("HTTP/1.1 200 OK");
        return;
    }



if ($debug) {
    umask(0000);

    Debug::enable();
}

if ($trustedProxies = $_SERVER['TRUSTED_PROXIES'] ?? false) {
    Request::setTrustedProxies(explode(',', $trustedProxies), Request::HEADER_X_FORWARDED_ALL ^ Request::HEADER_X_FORWARDED_HOST);
}

if ($trustedHosts = $_SERVER['TRUSTED_HOSTS'] ?? false) {
    Request::setTrustedHosts(explode(',', $trustedHosts));
}
// use apenas no local não abilitar em prod que da ruim
// ini_set('memory_limit', '-1');


// ini_set('error_reporting', E_ALL);
// $debug = true;
$kernel = new Kernel($env, $debug);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);

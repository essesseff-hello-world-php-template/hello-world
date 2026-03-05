<?php

declare(strict_types=1);

use Slim\Factory\AppFactory;
use Slim\Psr7\Response as SlimResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . '/vendor/autoload.php';

// Configure logging (to stderr, consistent with 12-factor / container conventions)
function logInfo(string $message): void
{
    $timestamp = (new DateTimeImmutable())->format('Y-m-d\TH:i:s.u');
    fwrite(STDERR, "{$timestamp} - hello-world - INFO - {$message}" . PHP_EOL);
}

// Read environment variables with defaults
$port        = getenv('PORT')        ?: '8080';
$environment = getenv('ENVIRONMENT') ?: 'unknown';
$version     = getenv('VERSION')     ?: 'unknown';

function getHostname(): string
{
    $hostname = gethostname();
    return ($hostname !== false) ? $hostname : 'unknown';
}

$app = AppFactory::create();

// ── Routes ────────────────────────────────────────────────────────────────────

/**
 * GET /
 * Main page with version information (HTML response)
 */
$app->get('/', function (Request $request, Response $response) use ($environment, $version): Response {
    $timestamp = (new DateTimeImmutable())->format('c');
    $hostname  = getHostname();

    $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <title>Hello World - {$environment}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .container { max-width: 600px; margin: 0 auto; }
        .info { background: #f0f0f0; padding: 20px; border-radius: 5px; }
        h1 { color: #333; }
        .env { color: #0066cc; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Hello World PHP (Slim)!</h1>
        <div class="info">
            <p><strong>Environment:</strong> <span class="env">{$environment}</span></p>
            <p><strong>Version:</strong> {$version}</p>
            <p><strong>Timestamp:</strong> {$timestamp}</p>
            <p><strong>Hostname:</strong> {$hostname}</p>
        </div>
    </div>
</body>
</html>
HTML;

    $response->getBody()->write($html);
    return $response->withHeader('Content-Type', 'text/html; charset=utf-8');
});

/**
 * GET /health
 * Health check endpoint (JSON response)
 */
$app->get('/health', function (Request $request, Response $response) use ($environment, $version): Response {
    $payload = json_encode([
        'status'      => 'healthy',
        'environment' => $environment,
        'version'     => $version,
    ]);
    $response->getBody()->write($payload);
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});

/**
 * GET /ready
 * Readiness check endpoint (JSON response)
 */
$app->get('/ready', function (Request $request, Response $response) use ($environment, $version): Response {
    $payload = json_encode([
        'status'      => 'ready',
        'environment' => $environment,
        'version'     => $version,
    ]);
    $response->getBody()->write($payload);
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});

// ── Startup logging ───────────────────────────────────────────────────────────

logInfo("Starting Hello World PHP Slim server on port {$port}");
logInfo("Environment: {$environment}");
logInfo("Version: {$version}");

$app->run();

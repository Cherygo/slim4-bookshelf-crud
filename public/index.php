<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Flash\Messages;
use Slim\Views\TwigMiddleware;;
use DI\Container;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Slim\App;

define('APP_ROOT', dirname(__DIR__));

require APP_ROOT . '/vendor/autoload.php';

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions([
    'settings' => [
        'twig' => [
            'path' => APP_ROOT . '/templates', 
            'options' => [
                'cache' => false,
                'auto_reload' => true,
            ],
        ],
        'db' => [
            'host' => '127.0.0.1',
            'dbname' => 'users',
            'user' => 'cherygo',
            'pass' => '210519796',
            'charset' => 'utf8mb4',
        ]
    ],

    Twig::class => function (Psr\Container\ContainerInterface $c) {
        $settings = $c->get('settings')['twig'];
        return Twig::create($settings['path'], $settings['options']);
    },
    'view' => DI\get(Twig::class),

    'db' => function (ContainerInterface $c) {
        $settings = $c->get('settings')['db'];

        $host = $settings['host'];
        $dbname = $settings['dbname'];
        $user = $settings['user'];
        $pass = $settings['pass'];
        $charset = $settings['charset'];

        $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
             return new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
             throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    },
]);

try {
    $container = $containerBuilder->build();
} catch(Exception $e) {
    die('Failed to build container: ' . $e->getMessage());
};


AppFactory::setContainer($container);

// Instantiate app
$app = AppFactory::create();

$app->addErrorMiddleware(true, true, true);

$app->add(TwigMiddleware::createFromContainer($app, Twig::class));

$app->add(function ($request, $handler) use($container) {
   $twig = $container->get('view');
    $environment = $twig->getEnvironment();
    $environment->addGlobal('session', $_SESSION);
    return $handler->handle($request);
});


$app->add(function ($request, $handler) use($app) {
    $session_path = APP_ROOT . '/var/sessions';
    session_save_path($session_path);

    if(session_status() == PHP_SESSION_NONE) {
        session_start([
            'cookie_httponly' => true,
            'cookie_secure' => false,
            'cookie_samesite' => 'Lax',
        ]);
    }

$flash = $_SESSION['flash'] ?? null;
if($flash) {
    unset($_SESSION['flash']);
}

    $container = $app->getContainer();
    $view = $container->get(Twig::class);
    $view->getEnvironment()->addGlobal('flash', $flash);

    $basePath = $app->getBasePath();
    $view->getEnvironment()->addGlobal('base_path',rtrim($basePath , '/'));

    $view->getEnvironment()->addGlobal('session', $_SESSION ?? []);

    return $handler->handle($request);
});


// Add Error Handling Middleware
$app->addErrorMiddleware(true, false, false);

// Run application
(require __DIR__ . '/../src/Routes.php')($app);

$app->run();
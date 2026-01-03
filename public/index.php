<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BookController as AdminBookController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookmarkController;
use App\Middlewares\AdminMiddleware;
use App\Repositories\BookRepository;
use DI\Container;
use Slim\Exception\HttpForbiddenException;
use Slim\Factory\AppFactory;
use Slim\Flash\Messages;
use Slim\Middleware\MethodOverrideMiddleware;
use Slim\Psr7\Response;
use Slim\Routing\RouteCollectorProxy;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();


session_start();

$container = new Container();

$container->set(Twig::class, function () {
    return Twig::create(__DIR__  . '/../templates', ['cache' => false]);
});

$container->set('flash', function () {
    return new Messages();
});

$container->set(PDO::class, function () {
    try {
        $host = $_ENV['DB_HOST'];
        $port = $_ENV['DB_PORT'] ?? 5432;
        $dbname = $_ENV['DB_NAME'];
        $user = $_ENV['DB_USER'];
        $pass = $_ENV['DB_PASS'];

        $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $pass);

        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        return $conn;
    } catch (PDOException $e) {
        die("Database error " . $e->getMessage());
//        return $e;
    }
});

$app = AppFactory::createFromContainer($container);

// flash messages
$app->add(function ($request, $handler) use ($container) {
    $container->get(Twig::class)->getEnvironment()->addGlobal('flash', $container->get('flash')->getMessages());
    return $handler->handle($request);
});

// session
$app->add(function ($request, $handler) use ($container) {
   if(isset($_SESSION)) {
       $this->get(Twig::class)->getEnvironment()->addGlobal('session', $_SESSION);
   }
   return $handler->handle($request);
});

$app->add(TwigMiddleware::create($app, $container->get(Twig::class)));
$app->addRoutingMiddleware();
$app->add(MethodOverrideMiddleware::class);

$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setErrorHandler(
    HttpForbiddenException::class,
    function() use($container)
    {
        $twig = $container->get(Twig::class);
        $response = new Response();
        return $twig->render($response, "errors/403.twig")->withStatus(403);
    }
);


// Routes
$app->get('/', function ($request, $response) {
    $view = $this->get(Twig::class);
    $newestBooks = $this->get(BookRepository::class)->getLastBooks();
    $totalBookCount = $this->get(BookRepository::class)->getBooksCount();

    return $view->render($response, 'home.twig', [
        'pageTitle' => 'Home Page',
        'newest_books' => $newestBooks,
        'total_book_count' => $totalBookCount,
    ]);
});

// AUTH
$app->get('/register', AuthController::class . ':registerPage');

$app->post('/register', AuthController::class . ':store');


$app->get('/login', AuthController::class . ':loginPage');

$app->post('/login', AuthController::class . ':login');


$app->post('/logout', AuthController::class . ':logout')->setName('logout');

// Catalog
$app->get('/catalog', BookController::class . ':catalogPage');

// Bookmark
$app->get('/bookmarks', BookmarkController::class . ':myBookmarksPage')
    ->setName('bookmarks.index');

$app->post('/bookmarks', BookmarkController::class . ':addBookmark')
    ->setName('bookmarks.store');

$app->delete('/my_bookmarks/{id}', BookmarkController::class . ':deleteBookmark')
    ->setName("bookmarks.destroy");


// Admin
$app->group('/admin', function (RouteCollectorProxy $group) {
    $group->get('', AdminController::class);

//    BOOKS
    $group->get('/books', AdminBookController::class . ':index')->setName('books.index');

    $group->get('/books/create', AdminBookController::class . ':create')
        ->setName('books.create');
    $group->post('/books/create', AdminBookController::class . ':store')
        ->setName('books.store');

    $group->post('/books/{id}/destroy', AdminBookController::class . ':destroy')
        ->setName('books.destroy');

//    USERS
    $group->get('/users/index', AdminuserController::class . ':index')->setName('users.index');
    $group->post('/users/{id}/change-role', AdminuserController::class . ':changeUserRole')
        ->setName('users.change-role');

})->add(new AdminMiddleware());


$app->run();
<?php
use Slim\App;
use Slim\Views\Twig;
use PSR\Http\Message\ResponseInterface as Response;
use PSR\Http\Message\ServerRequestInterface as Request;

function flash(string $message, string $type = 'danger') {
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

return function(App $app) {
    $app->get('/', function (Request $request, Response $response, array $args) use($app) {
        $db = $app->getContainer()->get('db');
        $view = $app->getContainer()->get('view');
        $totalBookCount = 0;

        try{
            $stmt = $db->prepare('SELECT * FROM books ORDER BY id DESC LIMIT 3');
            $stmt->execute();
            $newestBooks = $stmt->fetchAll();
            $countStmt = $db->prepare('SELECT COUNT(id) AS total FROM books');
            $countStmt->execute();
            $totalBookCount = $countStmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Home Page DB Error: " . $e->getMessage());
            $newestBooks = [];
        }

        return $view->render($response, 'home.twig', [
            'pageTitle' => 'Home Page',
            'newest_books' => $newestBooks,
            'total_book_count' => $totalBookCount,
            'current_path' => $request->getAttribute('current_path')
        ]);
});

    $app->get('/login', function (Request $request, Response $response, array $args) use($app) {
        $view = $app->getContainer()->get('view');
        return $view->render($response, 'login.twig', [
            'pageTitle' => 'Login',
            'current_path' => $request->getAttribute('current_path')
        ]);
    });
    $app->post('/login', function(Request $request, Response $response, array $args) use($app) {
        $params = (array)$request->getParsedBody();
        $identifier = $params['identifier'] ?? '';
        $password = $params['password'] ?? '';

        $db = $app->getContainer()->get('db');

        $stmt = $db->prepare('SELECT * FROM user WHERE email = ? OR username = ?');
        $stmt->execute([$identifier, $identifier]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            session_write_close();
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        flash('Invalid username or password.', 'danger');
        return $response->withHeader('Location', '/login')->withStatus(302);

        // --- DEBUG CODE ---
//        $params= (array)$request->getParsedBody();
//        $identifier = $params['identifier'] ?? '';
//        $password = $params['password'] ?? '';
//
//        // --- DEBUG 1: CHECK FORM VALUES ---
//        if (empty($identifier) || empty($password)) {
//            die("DEBUG: Form fields are empty. Did you set the 'name' attributes in your .twig file?");
//        }
//
//        $db = $app->getContainer()->get('db');
//
//        $stmt = $db->prepare('SELECT * FROM user WHERE email = ? OR username = ?');
//        $stmt -> execute([$identifier, $identifier]);
//        $user = $stmt->fetch();
//
//        // --- DEBUG 2: CHECK IF USER WAS FOUND ---
//        if (!$user) {
//            die("DEBUG: User not found. No user with email or username '" . htmlspecialchars($identifier) . "' exists.");
//        }
//
//        // --- DEBUG 3: CHECK THE PASSWORD ---
//        $isPasswordCorrect = password_verify($password, $user['password_hash']);
//
//        if (!$isPasswordCorrect) {
//            die("DEBUG: Password verification failed. <br>
//              Form Password: '" . htmlspecialchars($password) . "' <br>
//              DB Hash: '" . htmlspecialchars($user['password_hash']) . "' <br>
//              (Does the hash in the DB look truncated or like a hash of an empty string?)");
//        }
//
//        // --- If you see this, the login is successful ---
//        die("DEBUG: Login successful! (You can now remove the debug code)");
//    });
    });


    $app->get('/register', function (Request $request, Response $response, array $args) use($app) {
    $view = $app->getContainer()->get('view');
    return $view->render($response, 'register.twig', [
        'pageTitle' => 'Register',
        'current_path' => $request->getAttribute('current_path')
    ]);
    });
    $app->post('/register', function(Request $request, Response $response, array $args) use($app) {
        $params= (array)$request->getParsedBody();
        $username = $params['username'] ?? '';
        $email = $params['email'] ?? '';
        $password = $params['password'] ?? '';

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $db = $app->getContainer()->get('db');
        $sql = 'INSERT INTO user (username, email, password_hash) VALUES (?, ?, ?)';

        try {
            $stmt = $db->prepare($sql);
            $stmt->execute([$username, $email, $passwordHash]);
        } catch(\PDOExcpetion $e) {
            $response->getBody()->write('An account with this email or username already exists.');
            return $response->withStatus(500);
        }

        return $response->withHeader('Location', '/login')->withStatus(302);
    });

    $app->get('/logout', function (Request $request, Response $response, array $args) use($app) {
        session_unset();
        session_destroy();
        return $response->withHeader('Location', '/')->withStatus(302);
    });

    $app->add(function ($request, $handler) {
        $uri = $request->getUri();
        $path = $uri->getPath();

        $request = $request->withAttribute('current_path', $path);

        return $handler->handle($request);
    });

    $app->get('/my_books', function(Request $request, Response $response, array $args) use($app) {

        if (!isset($_SESSION['user_id'])) {
            flash('Please login to view this page', 'info');
            return $response->withHeader('Location', '/login')->withStatus(302);
        }
        $userId = $_SESSION['user_id'];

        $db = $app->getContainer()->get('db');
        $stmt = $db->prepare("
                SELECT 
                    b.*, 
                    bm.id AS bookmark_id,
                    1 AS is_bookmarked
                FROM books b
                JOIN bookmarked bm ON b.id = bm.book_id
                WHERE bm.user_id = ?
                ORDER BY b.title ASC
            ");
        $stmt->execute([$userId]);
        $books = $stmt->fetchAll();

        $view = $app->getContainer()->get('view');

        return $view->render($response, 'my_books.twig', [
            'page_title' => 'My Bookmarked Shelf',
            'books' => $books,
            'current_path' => $request->getAttribute('current_path'),
        ]);
    });

    $app->get('/books_catalog', function(Request $request, Response $response, array $args) use($app) {

        // Check Authentication
        if (!isset($_SESSION['user_id'])) {
            flash('Please login to view this page', 'info');
            return $response->withHeader('Location', '/login')->withStatus(302);
        }

        $userId = $_SESSION['user_id'];
        $db = $app->getContainer()->get('db');
        $view = $app->getContainer()->get('view');

        try {
            $sql = "
                SELECT b.*, 
                    IF(bm.book_id IS NOT NULL, 1, 0) AS is_bookmarked,
                    bm.id AS bookmark_id
                FROM books b
                LEFT JOIN bookmarked bm ON b.id = bm.book_id AND bm.user_id = ?
                ORDER BY b.title ASC
            ";

            $stmt = $db->prepare($sql);
            $stmt->execute([$userId]);
            $books = $stmt->fetchAll();

        } catch (PDOException $e) {
            flash('Database Error: Could not load books.', 'danger');
            $books = [];
        }

        return $view->render($response, 'books_catalog.twig', [
            'page_title' => 'Book Catalog',
            'books' => $books,
            'current_path' => $request->getAttribute('current_path') // PASS IT HERE
        ]);
    });

    // Route to ADD a bookmark
    $app->post('/bookmark/add/{bookId}', function(Request $request, Response $response, array $args) use($app) {
        if (!isset($_SESSION['user_id'])) {
            return $response->withHeader('Location', '/login')->withStatus(302);
        }

        $bookId = $args['bookId'] ?? 0;
        $userId = $_SESSION['user_id'];
        $db = $app->getContainer()->get('db');

        $params = $request->getParsedBody();
        $redirectPath = $params['redirect_to'] ?? '/books_catalog';

        error_log("DEBUG ADD: User $userId attempting to add Book $bookId. Redirect: $redirectPath");

        try {
            if ($bookId > 0) {
                $stmt = $db->prepare("INSERT INTO bookmarked (user_id, book_id) VALUES (?, ?)");
                $stmt->execute([$userId, $bookId]);
                flash('Book added to your shelf!', 'success');
            } else {
                flash('Invalid book ID provided.', 'danger');
            }

            $stmt = $db->prepare("INSERT INTO bookmarked (user_id, book_id) VALUES (?, ?)");
            $stmt->execute([$userId, $bookId]);

            flash('Book added to your shelf!', 'success');
        } catch (PDOException $e) {
            //duplicate entry
            if ($e->getCode() == '23000') {
                flash('This book is now in your shelf.', 'info');
            } else {
                error_log("Bookmark Add Error: " . $e->getMessage());
                flash('Could not add bookmark: Database Error.', 'danger');
            }
        }

        return $response->withHeader('Location', $redirectPath)->withStatus(302);
    });


    // Route to DELETE a bookmark
    $app->post('/bookmark/delete/{bookmarkId}', function(Request $request, Response $response, array $args) use($app) {
        if (!isset($_SESSION['user_id'])) {
            return $response->withHeader('Location', '/login')->withStatus(302);
        }

        $bookmarkId = $args['bookmarkId'] ?? 0;
        $userId = $_SESSION['user_id'];
        $db = $app->getContainer()->get('db');


        $params = $request->getParsedBody();
        $redirectPath = $params['redirect_to'] ?? '/books_catalog';

        error_log("DEBUG ADD: User $userId attempting to add Book $bookmarkId. Redirect: $redirectPath");

        try {
            if ($bookmarkId > 0) {
                $stmt = $db->prepare("DELETE FROM bookmarked WHERE id = ? AND user_id = ?");
                $stmt->execute([$bookmarkId, $userId]);

                flash('Book added to your shelf!', 'success');
            } else {
                flash('Invalid book ID provided.', 'danger');
            }


            flash('Bookmark removed successfully.', 'success');
        } catch (PDOException $e) {
            error_log("Bookmark Delete Error: " . $e->getMessage());
            flash('Could not remove bookmark: Database Error.', 'danger');
        }

        return $response->withHeader('Location', $redirectPath)->withStatus(302);
    });

    $app->get('books_catalog', function(Request $request, Response $response, array $args) use($app) {

        if (!isset($_SESSION['user_id'])) {
            flash('Please login to view the full book catalog.', 'info');
            return $response->withHeader('Location', '/login')->withStatus(302);
        }

        $userId = $_SESSION['user_id'];
        $db = $app->getContainer()->get('db');
        $view = $app->getContainer()->get('view');

        try {
            $sql = "
                SELECT 
                    b.*, 
                    IF(bm.book_id IS NOT NULL, 1, 0) AS is_bookmarked,
                    bm.id AS bookmark_id
                FROM books b
                LEFT JOIN bookmarked bm ON b.id = bm.book_id AND bm.user_id = ?
                ORDER BY b.title ASC
            ";

            $stmt = $db->prepare($sql);
            $stmt->execute([$userId]);
            $books = $stmt->fetchAll();

        } catch (PDOException $e) {
            error_log("Catalog DB Error: " . $e->getMessage());
            flash('Database Error: Could not load the book catalog.', 'danger');
            $books = [];
        }

        return $view->render($response, 'books_catalog.twig', [
            'page_title' => 'Global Book Catalog',
            'books' => $books,
            'current_path' => $request->getAttribute('current_path'),
        ]);
    });


    // Admin
    function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }
    $app->get('/admin', function(Request $request, Response $response, array $args) use($app) {
        if(!isAdmin()) {
            flash('You must be logged in as an admin to view this page.', 'danger');
           return $response->withHeader('Location', '/')->withStatus(302);
       }

       $view = $app->getContainer()->get('view');

       return $view->render($response, 'admin/admin_index.twig', [
           'page_title' => 'Admin Dashboard',
           'current_path' => $request->getAttribute('current_path')
       ]);
    });

    $app->get('/admin/admin_users', function(Request $request, Response $response, array $args) use($app) {
        if(!isAdmin()) {
            flash('You must be logged in as an admin to view this page.', 'danger');
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $db = $app->getContainer()->get('db');
        $view = $app->getContainer()->get('view');
        try{
        $stmt = $db->prepare("SELECT * FROM user ORDER BY username ASC");
        $stmt->execute();
        $users = $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Admin DB Error: " . $e->getMessage());
            flash('Database Error: Could not load users.', 'danger');
            $users = [];
        }
        return $view->render($response, 'admin/admin_users.twig', [
            'page_title' => 'Admin Users',
            'users' => $users,
            'current_path' => $request->getAttribute('current_path')
        ]);
    });

    $app->post('/admin/admin_users/role/{id}', function(Request $request, Response $response, array $args) use($app) {
        if (!isAdmin()) {
            flash('Access Denied.', 'danger');
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $targetUserId = $args['id'] ?? 0;
        $db = $app->getContainer()->get('db');
        $params = $request->getParsedBody();

        $newRole = $params['new_role'] ?? null;
        $redirectPath = $params['redirect_to'] ?? '/admin/admin_users';

        if (!in_array($newRole, ['admin', 'user'])) {
            flash('Invalid role specified.', 'danger');
            return $response->withHeader('Location', $redirectPath)->withStatus(302);
        }

        if ($targetUserId == $_SESSION['user_id']) {
            flash('Cannot change your own role via this form.', 'danger');
            return $response->withHeader('Location', $redirectPath)->withStatus(302);
        }

        try {
            if ($targetUserId > 0) {
                $stmt = $db->prepare("UPDATE user SET role = ? WHERE id = ?");
                $stmt->execute([$newRole, $targetUserId]);

                $action = ($newRole === 'admin') ? 'Promoted' : 'Demoted';
                flash("User (ID: {$targetUserId}) {$action} successfully.", 'success');
            } else {
                flash('Invalid user ID provided.', 'danger');
            }
        } catch (PDOException $e) {
            error_log("SQL ERROR ROLE CHANGE: " . $e->getMessage());
            flash('Database error during role update.', 'danger');
        }

        return $response->withHeader('Location', $redirectPath)->withStatus(302);
    });

    $app->get('/admin/admin_books', function(Request $request, Response $response, array $args) use($app) {
       if(!isAdmin()) {
            flash('You must be logged in as an admin to view this page.', 'danger');
            return $response->withHeader('Location', '/')->withStatus(302);
        }
       $db = $app->getContainer()->get('db');
       $view = $app->getContainer()->get('view');
       try{
        $stmt = $db->prepare("SELECT * FROM books ORDER BY id ASC");
        $stmt->execute();
        $books = $stmt->fetchAll();
       } catch (PDOException $e) {
           error_log("Admin DB Error: " . $e->getMessage());
           flash('Database Error: Could not load books.', 'danger');
           $books = [];
       }
       return $view->render($response, 'admin/admin_books.twig', [
           'page_title' => 'Admin Books',
           'books' => $books,
           'current_path' => $request->getAttribute('current_path')
       ]);
    });

    $app->get('/admin/admin_add_book', function(Request $request, Response $response, array $args) use($app) {
        if(!isAdmin()) {
            flash('You must be logged in as an admin to view this page.', 'danger');
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $view = $app->getContainer()->get('view');
        return $view->render($response, 'admin/admin_add_book.twig', [
            'page_title' => 'Add Book',
            'current_path' => $request->getAttribute('current_path')
        ]);
    });
    $app->post('/admin/admin_add_book', function(Request $request, Response $response, array $args) use($app) {
        if (!isAdmin()) {
            flash('Access Denied.', 'danger');
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $db = $app->getContainer()->get('db');
        $params = $request->getParsedBody();
        $redirectPath = $params['redirect_to'] ?? '/admin/admin_books';

        $title = trim($params['title'] ?? '');
        $author = trim($params['author'] ?? '');
        $isbn = trim($params['isbn'] ?? null);
        $publish_date = $params['publish_date'] ?? null;
        $cover_image_url = $params['cover_image_url'] ?? null;

        if (empty($title) || empty($author)) {
            flash('Title and Author are required fields.', 'danger');
            return $response->withHeader('Location', '/admin/admin_add_book')->withStatus(302);
        }

        $sql = "INSERT INTO books 
            (title, author, isbn, publish_date, cover_image_url) 
            VALUES (?, ?, ?, ?, ?)";

        try {
            $stmt = $db->prepare($sql);
            $stmt->execute([
                $title,
                $author,
                $isbn,
                $publish_date,
                $cover_image_url
            ]);
            flash('Book "' . htmlspecialchars($title) . '" successfully added to the catalog!', 'success');

        } catch (PDOException $e) {
            error_log("SQL ERROR ADD BOOK ADMIN: " . $e->getMessage());
            flash('A database error occurred while trying to save the book.', 'danger');
            return $response->withHeader('Location', '/admin/admin_add_book')->withStatus(302);
        }

        return $response->withHeader('Location', $redirectPath)->withStatus(302);
    });

    $app->post('/admin/admin_books/delete/{bookId}', function(Request $request, Response $response, array $args) use($app) {
        if(!isAdmin()) {
            flash('Access Denied.', 'danger');
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $bookId = $args['bookId'] ?? 0;
        $db = $app->getContainer()->get('db');
        $params = $request->getParsedBody();
        $redirectPath = $params['redirect_to'] ?? '/admin/admin_books';

        try {
            if ($bookId > 0) {
                $stmt = $db->prepare("DELETE FROM books WHERE id = ?");
                $stmt->execute([$bookId]);
                flash('Book deleted successfully.', 'success');
            } else {
                flash('Invalid book ID provided.', 'danger');
            }

        } catch (PDOException $e) {
            error_log("SQL ERROR DELETE ADMIN: Code " . $e->getCode() . " Message: " . $e->getMessage());
            flash('Could not delete book: Database Error.', 'danger');
        }

        return $response->withHeader('Location', $redirectPath)->withStatus(302);
    });

};

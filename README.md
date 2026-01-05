# slim4-bookshelf-crud
### What the project does
The BookShelf application is a full-stack web project trying to demonstrate modern PHP architecture and data modelling.
It provides an environment to browse and choose books from a catalog, in which they can can be bookmarked and then, accessed in the My books page.

### Key features
- Role-Based Access Control (RBAC): Implementation of admin and suer roles stored on the central user table. Access to the /admin panel is only available if the user's role is set to admin.
- Authentication: User login and registration use PHP Sessions for state management and password_hash() for secure password storage.
- Data modeling: Manages the relationships between users and books using a bookmarked pivot table, allowing user to bookmark books.
- UI & Catalog: Displays a carousel of the newest books on the homepage and a catalog of all books.
- Admin Panel (CRUD): Administrators can manage users and books from the admin panel.

### Tech Stack
- Backend: PHP 8.4
- Framework: Slim 4
- Templating: Twig
- Database: MySQL
- Frontend: Bootstrap

### Get Started
Follow these steps to clone and run the application locally.

What you need:
- PHP 8.4 or higher
- MySQL
- Composer (package manager for PHP)

1. Cloning the Repository
   git clone [https://github.com/Cherygo/slim4-bookshelf-crud.git]
   cd slim4-bookshelf-crud

2. Installing Dependencies
   composer install
   Use Composer to install the project dependencies (Slim, Twig, PHP-DI).

3. Database Setup And Configuration
    1. Create a new, empty database (in my case "users") on MySQL server.
    2. Import Schema And Data: The dbDump.sql file contains all necessary code you will require to create the database and populate it with sample data.
        mysql -u [your_db_username] -p [your_db_name] < dbDump.sql
    3. Update Config: Edit the public/index.php file on line 27 to match your local databse credentials.
        // public/index.php (Snippet)
       'db' => [
       'host' => '127.0.0.1',
       'dbname' => 'your_db_name', // <- UPDATE THIS
       'user' => 'your_db_username', // <- UPDATE THIS
       'pass' => 'your_db_password', // <- UPDATE THIS
       'charset' => 'utf8mb4',
       ]
    4. Running the Application: Run the following command to start the built-in PHP server:
        php -S localhost:8000 -t public
   5. Access the application at http://localhost:8000

### Usage And Test Credentials
Role: admin
Username: test
Password: test

Role: user
Username: test2
Password: test2

### Maintainer and Contributes
    Maintainer: Cherygo
    Contributions are welcome. Please open an issue or a PR for any bug fixes or improvements.
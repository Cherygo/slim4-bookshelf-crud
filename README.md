# slim4-bookshelf-crud
### What the project does
The BookShelf application is a full-stack web project trying to demonstrate modern PHP architecture and data modelling.
It provides an environment to browse and choose books from a catalog, in which they can be bookmarked and then, accessed in the My books page.

### Key features
- Role-Based Access Control (RBAC): Implementation of admin and user roles stored on the central user table. Access to the /admin panel is only available if the user's role is set to admin.
- Authentication: User login and registration use PHP Sessions for state management and password_hash() for secure password storage.
- Data modeling: Manages the relationships between users and books using a bookmarked pivot table, allowing user to bookmark books.
- UI & Catalog: Displays a carousel of the newest books on the homepage and a catalog of all books.
- Admin Panel (CRUD): Administrators can manage users and books from the admin panel.

### Tech Stack
- Backend: PHP 8.4
- Framework: Slim 4
- Templating: Twig
- Database: PostgreSQL 18
- Frontend: Bootstrap

### Get Started
Follow these steps to clone and run the application locally.

What you need:
- PHP 8.4 or higher
- PostgreSQL 18
- Composer (package manager for PHP)

## Method 1: Docker
1. Cloning the Repository
   git clone [https://github.com/Cherygo/slim4-bookshelf-crud.git]<br/>
   cd slim4-bookshelf-crud

2. Installing Dependencies (Slim, Twig, PHP-DI):  
   composer install   

3. Update Config: Create a .env file in which you should have the following variables:  
        DB_HOST=db  
        DB_PORT=5432  
        DB_NAME=Bookshelf  
        DB_USER=root  
        DB_PASS=rootpassword  

4. Build and Run:<br/>
   docker-compose up -d --build

5. Import DB tables and data:  
   cat dbDump.sql | docker-compose exec -T db psql -U root -d Bookshelf  
   (Replace "db" with your database container if it differs)

## Method 2: WSL/Linux Terminal
1. Cloning the Repository:
   git clone [https://github.com/Cherygo/slim4-bookshelf-crud.git]<br/>
   cd slim4-bookshelf-crud

3. Installing Dependencies (Slim, Twig, PHP-DI):  
   composer install

4. Database Setup And Configuration
    1. Create a new, empty database (e.g. Bookshelf) on PostgreSQL.
    2. Import Schema And Data: The dbDump.sql file contains all necessary code you will require to create the database and populate it with sample data.
        psql -U [your_db_username] -d [your_db_name] < dbDump.sql
    3. Update Config: Create a .env file in which you should have the following variables:
        DB_HOST=localhost
        DB_PORT=5432
        DB_NAME=Bookshelf
        DB_USER=root   // OR YOUR DB_USERNAME
        DB_PASS=rootpassword   // OR YOUR DB_PASSWORD

5. Running the Application: Run the following command to start the built-in PHP server:  
        php -S localhost:8000 -t public
6. Access the application at: http://localhost:8000

### Usage And Test Credentials
Role: admin  
Username: root  
Password: root  

### Maintainer
   #### Cherygo

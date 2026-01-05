    CREATE TABLE users (
                            id SERIAL PRIMARY KEY,
                            username VARCHAR(100) NOT NULL UNIQUE,
                            email VARCHAR(255) NOT NULL UNIQUE,
                            password VARCHAR(255) NOT NULL,
                            role VARCHAR(255) NOT NULL DEFAULT 'user',
                            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE books (
                           id SERIAL PRIMARY KEY,
                           title VARCHAR(255) NOT NULL,
                           author VARCHAR(255),
                           url VARCHAR(10000),
                           created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE bookmarked (
                                id SERIAL PRIMARY KEY,
                                user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
                                book_id INT NOT NULL REFERENCES books(id) ON DELETE CASCADE,
                                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                UNIQUE (user_id, book_id)
    );


    INSERT INTO users (username, email, password, role)
    VALUES ('root', 'root@gmail.com', '$2y$12$s5dQM8OY3f7EDd.lWtGCuexdA5o42lrd7h/7D/94mIzkel1vctbXm', 'admin');

    INSERT INTO books (title, author, url)
    VALUES
        ('The Great Gatsby', 'F. Scott Fitzgerald', 'https://covers.openlibrary.org/b/isbn/9780743273565-L.jpg'),
        ('To Kill a Mockingbird', 'Harper Lee', 'https://covers.openlibrary.org/b/isbn/9780446310789-L.jpg'),
        ('1984', 'George Orwell', 'https://covers.openlibrary.org/b/isbn/9780451524935-L.jpg'),
        ('Pride and Prejudice', 'Jane Austen', 'https://covers.openlibrary.org/b/isbn/9780141439518-L.jpg'),
        ('The Catcher in the Rye', 'J.D. Salinger', 'https://covers.openlibrary.org/b/isbn/9780316769480-L.jpg'),
        ('The Hobbit', 'J.R.R. Tolkien', 'https://covers.openlibrary.org/b/isbn/9780547928227-L.jpg'),
        ('Fahrenheit 451', 'Ray Bradbury', 'https://covers.openlibrary.org/b/isbn/9781451673319-L.jpg'),
        ('Moby Dick', 'Herman Melville', 'https://covers.openlibrary.org/b/isbn/9781503280786-L.jpg'),
        ('War and Peace', 'Leo Tolstoy', 'https://covers.openlibrary.org/b/isbn/9781400079988-L.jpg'),
        ('The Odyssey', 'Homer', 'https://covers.openlibrary.org/b/isbn/9780140268867-L.jpg'),
        ('Crime and Punishment', 'Fyodor Dostoevsky', 'https://covers.openlibrary.org/b/isbn/9780140449136-L.jpg'),
        ('Alice''s Adventures in Wonderland', 'Lewis Carroll', 'https://covers.openlibrary.org/b/isbn/9780141439761-L.jpg'),
        ('The Divine Comedy', 'Dante Alighieri', 'https://covers.openlibrary.org/b/isbn/9780142437223-L.jpg'),
        ('Brave New World', 'Aldous Huxley', 'https://covers.openlibrary.org/b/isbn/9780060850524-L.jpg'),
        ('The Brothers Karamazov', 'Fyodor Dostoevsky', 'https://covers.openlibrary.org/b/isbn/9780374528379-L.jpg');
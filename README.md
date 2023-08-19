# Laravel Vue Chat

Laravel Vue Chat is a demo project used originally for an interview task. It uses Laravel 10 for the backend, Vue 3 for the frontend, and marries the two using Inertia.js. This project provides features of a holistic chat application, with an optional AI chatbot powered by OpenAI's GPT.

Laravel is a robust MVC PHP framework, Vue.js is a progressive JavaScript framework for building user interfaces, and Inertia.js is a library that combines the best of both to create a single-page application.

## Prerequisites
Before you begin, ensure you have the following installed on your local development machine:

- PHP 8.1 or later
- Composer - A PHP dependency management tool.
- Node.js and NPM - Node.js is a server-side JavaScript runtime environment. NPM is a package manager for Node.js.

## Installation
Follow these steps to setup the Laravel Vue Chat:

1. Clone the repository:
    ```bash
    git clone https://github.com/treyan94/chat-laravel.git
    ```

2. Navigate into the project directory:
    ```bash
    cd chat-laravel
    ```

3. Install the Composer dependencies:
    ```bash
    composer install
    ```

4. Install the NPM dependencies:
    ```bash
    npm install
    ```

5. Create a copy of your `.env.example` file and rename it to `.env`. This file houses all your environment variables.
    ```bash
    cp .env.example .env
    ```
   Obtain your API keys for [Pusher](https://pusher.com/) which is used for enabling real-time bidirectional communication and optionally for [OpenAI](https://openai.com/) if you plan on using the AI Chatbot.

   **Note**: Be sure you enter your Pusher API keys **BEFORE** proceeding to the next step.

6. Build your frontend assets:
    ```bash
    npm run build
    ```

7. Generate and set your application key:
    ```bash
    php artisan key:generate
    ```

8. Update the database settings in the `.env` file. Here's an example if you're using SQLite:
    ```plaintext
    DB_CONNECTION=sqlite
    DB_DATABASE=/absolute/path/to/database.sqlite
    ```
   Remember to replace `/absolute/path/to/database.sqlite` with your actual `sqlite` database file's path.

9. Execute the database migrations and seeding:
    ```bash
    php artisan migrate --seed
    ```

10. Bootstrap the queue worker:
    ```bash
    php artisan queue:work
    ```

11. Fire up the application:
    ```bash
    php artisan serve
    ```

Upon successful setup, you will be able to access the application from your specified port, usually: `http://localhost:8000`.

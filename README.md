# Laravel Vue Chat

Laravel Vue Chat is a test task that was created during an interview process. This project uses Laravel 10, Vue 3, and Inertia.js to create a comprehensive chat application. Additionally, it features an optional chat bot that is powered by OpenAI GPT.

## Installation

Before you install Laravel Vue Chat, ensure you have the following installed:
- PHP 8.1
- Composer
- Node.js and NPM

To install Laravel Vue Chat, follow these steps:

1. Clone the repository:
    ```bash
    git clone https://github.com/treyan94/chat-laravel.git
    ```

2. Navigate into the project directory:
    ```bash
    cd chat-laravel
    ```

3. Install Composer dependencies:
    ```bash
    composer install
    ```

4. Install NPM dependencies:
    ```bash
    npm install
    ```

5. Build frontend assets:
    ```bash
    npm run build
    ```

6. Copy `.env.example` to `.env`, and configure your environment variables:
    ```bash
    cp .env.example .env
    ```
   You will need to get API keys for [Pusher](https://pusher.com/) and optionally [OpenAI](https://openai.com/) to use the chatbot.

7. Generate a new application key:
    ```bash
    php artisan key:generate
    ```

8. Migrate and seed the database:
    ```bash
    php artisan migrate --seed
    ```

9. Start the queue worker:
    ```bash
    php artisan queue:work
    ```

10. Start the application:
    ```bash
    php artisan serve
    ```

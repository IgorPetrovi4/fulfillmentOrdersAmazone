# PHP Amazon FBA Integration

Этот проект представляет собой сервис для отправки заказов в сеть выполнения заказов Amazon (FBA) и получения
трекингового номера.

## Установка

1. Клонируйте репозиторий:
    ```sh
    git clone https://github.com/IgorPetrovi4/php-app.git
    cd php-app
    ```

2. Запустите контейнеры Docker:
    ```sh
     docker-compose up -d
     docker-compose build --no-cache
    ```

3. Установите зависимости Composer:
    ```sh
    docker-compose exec app composer install
    chmod +x bin/console
    chmod +x bin/phpunit
    ```

## Использование

  ```sh
docker-compose exec app php bin/console app:ship_order 16400 29664
    ```
## Тестирование
Для запуска тестов используйте команду:
```sh
docker-compose exec app bin/phpunit tests/Service/AmazonShippingServiceTest.php
docker-compose exec app bin/phpunit --testdox
```
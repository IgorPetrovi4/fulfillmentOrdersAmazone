# PHP Amazon FBA Integration

This project is a service for sending orders to the Amazon Fulfillment Network (FBA) and receiving a tracking number.

## Installation

1. Run the Docker containers:
    ```sh
     docker-compose up -d
     docker-compose build --no-cache
    ```

2. Install Composer dependencies:
    ```sh
    docker-compose exec app composer install
    chmod +x bin/console
    chmod +x bin/phpunit
    ```

3. Used:
    - change in AmazonApiConfig.php
         'access_key' => 'your-access-key',
         'secret_key' => 'your-secret-key',
         'region' => 'eu-west-1',
    and run the following command:
    ```sh
    docker-compose exec app php bin/console app:ship_order 16400 29664
    ```
    or testing:
    ```sh
    docker-compose exec app bin/phpunit --testdox
    ```
# shopify-app-laravel-skeleton
This is the skeleton for the shopify app using laravel. used: osiset/laravel-shopify


# Steps to setup
1. clone this repo
2. composer install
3. php artisan key:generate
4. create .env file if not exists
5. Configure your database connection
6. Set shopify env variables
```
SHOPIFY_API_KEY="<YOUR SHOPIFY KEY>"
SHOPIFY_API_SECRET="YOUR SHOPIFY SECRET"
SHOPIFY_API_SCOPES="read_products,write_products,write_script_tags"
SHOPIFY_API_VERSION="2021-07"
```
7. php artisan vendor:publish & select the shopify-config
8. setup ngrok in your local
9. php artisan serve
10. in second window you have to start ngrok with port 8000 `/ngrok http 8000`
11. You have to set the authenticate url in your shopify app as `<YOUR NGROK  URL>/authenticate`

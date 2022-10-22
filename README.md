## Instructions

create .env file with context of .env.example
put your Database Credintals in

``` 
DB_DATABASE=test
DB_USERNAME=root
DB_PASSWORD=
```

add your email smtp details or set the MAIL_MAILER as log


run 
```
 composer install
  ``` 

  run
   ```
 php artisan migrate 
  ``` 
  run
   ```
 php artisan db::seed 
  ``` 
## Usage

create order using CURL request eg: 

curl --location --request POST 'http://127.0.0.1:8000/api/order' \
--header 'Content-Type: application/json' \
--header 'Accept: application/json' \
--data-raw '{
"products": [
{
"product_id": 1,
"quantity": 1
}
]
}'





### Testing

``` 
php artisan test 
```






## Credits

- [yazeed ayyash](https://github.com/yzedayyash)




<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

## Об этом Телеграмм-боте

Этот бот написан на Ларавел, он использует Докер для развертывания. 

``` 
cp .env.example .env
```
Заполнить в нем ID бота и ID Jira 
```
make start
```
Входим внутрь контейнера
```
make app
```
внутри контейнера:
```
php artisan migrate
php artisan telebot:webhook --setup 
```
Вебхук Жиры нужно направить 
https://domain/jira

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 1500 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

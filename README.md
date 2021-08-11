<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

## Телеграмм-бот для Жиры

### Установка

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

### Что умеет бот
* рассылать сообщения 
  * при создании, изменении, комментировании задачи
  * при записи ворклога в задачу
  * при удалении комментария, ворклога или самой задачи

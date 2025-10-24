# Tile Expert API тестовое задание

## Небольшое приложение на php 8.2

### Архитектура

- **PHP 8.2** - основной язык приложения
- **MySql** - база данных
- **Nginx** - веб-сервер
- **Manticore** - поисковая система и база данных
- **Redis** - кэширование
- **Docker Compose** - оркестрация контейнеров

### Возможности

- Получение цен товаров с сайта tile.expert
- Получение списка заказов с пагинацией и группировкой по дате
- Получение одного заказа
- SOAP API для создания заказов
- Покрытие основными тестами

### Локальная развертка и запуск проекта

1. Клонируйте репозиторий git clone https://github.com/ilyachausov1999/tile-expert-test-task
2. env.local специально убрал из gitignore чтобы не было путаницы при копировании и тд, так что он будет в репе все равно проект тестовый :)

### Выполните следующие команды

```bash
docker-compose build
docker-compose up -d
docker-compose exec php bash

В контейнере выполните
composer install
bin/console doctrine:migrations:migrate
bin/console doctrine:fixtures:load
bin/console app:index-articles
```

```bash
Если в Dockerfile не сработает установка прав попробуйте выполнить
docker-compose exec php bash
chmod -R 775 var/
chmod -R 775 /var/www/html/var
```
Если все сработает правильно приложение, будет доступно на http://localhost

### После можно запустить тесты
```bash
docker-compose exec php bash
./bin/phpunit
```

### Документацию для API можно глянуть тут http://localhost/api/doc
Анализ БД и ее новый дамб можно найти в корне проекта в папке db_analysis

Там есть все пункты кроме создания заказа через SOAP, не хватило времени :(

Его можно протестировать в Postman POST http://localhost/soap/order , не забудьте настроить заголовки под XML

Файл с тестовыми данными есть в корне проекта `createOrderTest.xml`


P.S ввиду ограниченного количества времени и объема проекта увы есть небольшие шероховатости, стоило бы немного еще его причесать, но пока отправляю как есть

P.P.S вероятно мог забыть тут что-то указать, тк делал его постепенно...

$(eval env=$(shell sh -c "cat ./.env | grep -v ^# | xargs -0"))
$(eval user=$(shell sh -c "echo $$(id -u)"))

.PHONY: start stop update app nginx es exec

start:
	env $(env) UID=$(user) docker-compose --file ./docker/fpm/docker-compose.yml up --build -d

stop:
	env $(env) UID=$(user) docker-compose --file ./docker/fpm/docker-compose.yml down

update:
	env $(env) UID=$(user) docker-compose --file ./docker/fpm/docker-compose.yml build --no-cache

app:
	env $(env) UID=$(user) docker-compose --file ./docker/fpm/docker-compose.yml exec fpm sh

app-root:
	docker exec -it -u root fpm_fpm_1 sh

fix:
	env $(env) UID=$(user) docker-compose --file ./docker/fpm/docker-compose.yml exec fpm php ./vendor/bin/php-cs-fixer fix

nginx:
	env $(env) UID=$(user) docker-compose --file ./docker/fpm/docker-compose.yml exec nginx sh

es:
	env $(env) UID=$(user) docker-compose --file ./docker/fpm/docker-compose.yml exec laravel_echo_server sh

listen:
	docker exec -it -u root fpm_fpm_1 sh 'php artisan telebot:polling'

exec:
	env $(env) UID=$(user) docker-compose --file ./docker/fpm/docker-compose.yml run fpm $(COMMAND)

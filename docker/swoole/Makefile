$(eval env=$(shell sh -c "cat ../../.env | grep -v ^# | xargs -0"))
$(eval user=$(shell sh -c "echo $$(id -u)"))

start:
	env $(env) UID=$(user) docker-compose up --build -d

stop:
	env $(env) UID=$(user) docker-compose down

update:
	env $(env) UID=$(user) docker-compose build --no-cache

app:
	env $(env) UID=$(user) docker-compose exec swoole sh

app-as-user:
	env $(env) UID=$(user) docker-compose exec -u $(user) swoole sh

es:
	env $(env) UID=$(user) docker-compose exec laravel_echo_server sh

exec:
	env $(env) UID=$(user) docker-compose run swoole $(COMMAND)

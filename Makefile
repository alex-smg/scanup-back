start:
	@docker-compose up

down:
	@docker-compose down

logs:
	@docker-compose logs

bash:
	@docker exec -it scanup-php bash

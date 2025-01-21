#!/usr/bin/make
include .env
export $(shell sed 's/=.*//' .env)
compose=docker compose

.DEFAULT_GOAL := help

.PHONY: init
init: build start ci

.PHONY: clean
clean:
		$(compose) down
		sudo rm -rf ./.docker/data ./var ./vendor

.PHONY: start
start:
		$(compose) up -d

.PHONY: down
down:
		$(compose) down

.PHONY: build
build:
		$(compose) build

.PHONY: ci
ci:
		$(compose) exec -it php-fpm sh


#!/usr/bin/env bash

cd ../laradock
docker-compose up -d nginx mysql phpmyadmin  workspace
##docker-compose up -d nginx mysql workspace

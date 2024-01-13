#!/bin/sh

gcc -o tempsensor tempsensor.c -lwiringPi -lwiringPiDev -lmysqlclient -I/usr/include/mariadb


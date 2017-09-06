#!/bin/sh
# Apache start script

# Change owner
exec usermod -u 1000 www-data
exec usermod -G staff www-dat
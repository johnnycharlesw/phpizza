#!/bin/bash
# Step 1: apply updates to the container
apt update
apt upgrade

# Step 2: install Lighttpd and PHP
sudo apt install lighttpd php-fpm php

# Step 3: install SQLite
sudo apt install sqlite3 php-sqlite

# Step 4: create the 
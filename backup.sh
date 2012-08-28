#!/bin/bash
# this script is just show  you how to backup the databases; use it if you need.
mysqldump --opt --databases fastem > ./bak.sql --user=fastem --password=fastem --host=127.0.0.1

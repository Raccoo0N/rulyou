#!/bin/bash 
rsync -rvza --exclude '.*' --exclude '_*' --exclude '*.html' --exclude 'TODO*' --exclude '*.sh' /Users/l0cust/rsync/TOTAL/PROJECTS/FL/userapi locust@locust:/home/locust
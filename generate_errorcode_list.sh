#!/bin/bash

#Generate list of error codes used in IDDQD
#If a rare but anticipated error happens somewhere, the errorcode will be reported.
#The file errorcodes.lst lists where these errors originate.
#This file generates that list.
#
#For example, error code 1 happens in login.php (on line 44 as of now) when there 
#is a problem generating and storing a user token in the database during login.

maxerrcode=100
if [ -e errorcodes.lst ];then
    rm errorcodes.lst
fi
touch errorcodes.lst
for i in $(seq $maxerrcode);do
    line=$(grep -in returnhome\($i\) public/* cgi-bin/*)
    if [ "$line" ];then
        echo $i" "$line >> errorcodes.lst
    fi
done

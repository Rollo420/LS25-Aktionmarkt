#!/bin/sh 

cp -r /app/node_modules /build/node_modules 
cp -r /app/vendor/ /build/vendor/

chmod -R 777 /build/node_modules 
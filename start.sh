
sail='./vendor/bin/sail'

git pull

docker-compose -f docker-compose.yml -f docker-compose.proxy.yml up -d 
$sail artisan config:clear
$sail npm run build
#$sail npm run dev 

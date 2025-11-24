
sail='./vendor/bin/sail'

git pull

$sail up -d
$sail artisan config:clear
$sail npm run build
#$sail npm run dev 

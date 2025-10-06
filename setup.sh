docker compose -f docker-compose-setup.yml up --build

sail='./vendor/bin/sail'

php artisan env:decrypt --key="base64:tuVKEBcQpMuBo6bcttk0LaPLNjZB4NV1cy7yKFO2JR0"

$sail up -d
$sail artisan config:clear
$sail artisan key:generate
$sail artisan migrate:fresh --seed

#$sail npm run build
#$sail npm run dev

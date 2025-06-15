docker compose -f docker-compose-setup.yml up --build

sail='./vendor/bin/sail'

$sail up -d
$sail artisan config:clear
$sail artisan key:generate
$sail artisan migrate:fresh --seed

$sail npm run build
$sail npm run dev

docker compose -f docker-compose-setup.yml up --build

sail='./vendor/bin/sail'

$sail up -d
$sail artisan key:generate
$sail artisan migrate --seed

$sail npm run build
$sail npm run dev

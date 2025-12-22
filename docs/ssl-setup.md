# SSL / RapidSSL Setup (Kurzreferenz)

Schritte zum Integrieren deines RapidSSL-Zertifikats in dieses Projekt.

1) CSR & Key erzeugen (lokal oder auf dem Server)

```bash
openssl genrsa -out domain.key 2048
openssl req -new -key domain.key -out domain.csr
```

2) CSR bei RapidSSL/No‑IP einreichen, Zertifikat herunterladen

3) Zertifikate vorbereiten

Lege folgende Dateien im Projektordner `certs/` ab:

- `fullchain.pem` (dein Zertifikat + Chain)
- `domain.key` (privater Schlüssel)

Beispiel zum Erstellen von `fullchain.pem`:

```bash
cat your_domain.crt rapidssl_chain.crt > fullchain.pem
```

4) Lokale Docker-Nutzung / Entwicklung

- Datei `docker-compose.ssl.yml` ist ein Override, das die Zertifikate in den
  Container mountet und die nginx-Konfiguration `docker/nginx/laravel_ssl.conf`
  bereitstellt.
- Starte mit: `docker compose -f docker-compose.yml -f docker-compose.ssl.yml up -d`

5) Deployment auf Server oder Nutzung vorhandener Pi-Zertifikate

- Wenn dein Zertifikat bereits auf dem Pi liegt (z. B. `/etc/ssl/no-ip/woodlypi.crt`),
  kannst du es direkt in den Proxy-Container mounten. Beispiel-Compose (`docker-compose.proxy.yml`) nutzt diese Pfade:

```yaml
    volumes:
      - /etc/ssl/no-ip/woodlypi.crt:/etc/ssl/certs/woodlypi.crt:ro
      - /etc/ssl/no-ip/woodlypi.key:/etc/ssl/private/woodlypi.key:ro
```

- Alternativ kopiere die Dateien nach `/etc/ssl/certs/` und `/etc/ssl/private/` und
  passe die nginx-Konfiguration an.
- Prüfe Konfiguration und reload nginx im Host oder Proxy-Container:

```bash
sudo nginx -t
sudo systemctl reload nginx
# or if using the nginx container:
docker compose -f docker-compose.proxy.yml up -d --no-deps --build proxy
```

6) Laravel-Einstellungen

- Setze `APP_URL=https://example.com` in `.env`.
- In Produktion: `SESSION_SECURE_COOKIE=true` und ggf. `URL::forceScheme('https')`

Sicherheitshinweis: Teile niemals deine No‑IP-Login-Daten. Lade nur die
Zertifikatdateien (`fullchain.pem`, `domain.key`) hoch — diese sind notwendig
für die Konfiguration, nicht jedoch deine Anmeldedaten.

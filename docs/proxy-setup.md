# Reverse-Proxy mit nginx — Mehrere "Maps" / Backends

Dieses Beispiel zeigt, wie nginx als Reverse-Proxy mehrere Backends (je eine
"Map") verwaltet und einen Default-Container bereitstellt.

1) Ablauf
- Lege die gewünschten Backends als Docker-Services an (siehe
  `docker-compose.proxy.yml`). Die Service-Namen `default_app`, `app_map1`,
  `app_map2` sind Platzhalter und müssen deinen Services entsprechen.
- Passe `docker/nginx/proxy.conf` an: füge `upstream`-Blöcke für neue Backends
  hinzu und erweitere die `map $host $backend`-Zuordnung mit deinem Hostnamen.

2) Beispiel: Hostnamen zuordnen

In `docker/nginx/proxy.conf`:

- `example1.com map1_backend;` → Anfragen an `example1.com` landen bei
  `map1_backend` (Upstream `app_map1`).

3) Starten

```bash
docker compose -f docker-compose.yml -f docker-compose.proxy.yml up -d
```

4) Hinzufügen neuer Maps

- Ergänze einen neuen `upstream mymap_backend { server mymap_service:80; }`.
- Ergänze `mymap.example.com mymap_backend;` in der `map $host $backend`.
- Starte/Reload den Proxy: `docker compose -f docker-compose.proxy.yml up -d --no-deps --build proxy`

5) SSL / Zertifikate

- Die Proxy-Config erwartet `fullchain.pem` und `domain.key` im Projekt-`certs/`.
- Für mehrere Domains verwende SNI mit einem gemeinsamen Zertifikat oder
  verwalte separate Server-Blöcke pro Hostnamen (erweiterbar).

6) Sicherheit

- Lege die privaten Schlüssel nicht ins Git-Repository (`certs/README.md`).
- Verwende Firewalls und begrenze öffentliche Ports auf `80`/`443`.

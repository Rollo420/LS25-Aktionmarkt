# TODO: Configure Xdebug for Laravel Sail Container Debugging

- [ ] Edit `docker-compose.yml` to change default `XDEBUG_CONFIG` from `client_host=host.docker.internal` to `client_host=localhost`
- [ ] Create `docker/php/conf.d/xdebug.ini` with Xdebug settings: mode=debug, start_with_request=yes, client_port=9003, client_host=localhost

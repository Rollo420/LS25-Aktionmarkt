<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></**a**>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).






## 🚀 Einführung

Diese Anleitung beschreibt, wie du **Bootstrap SCSS** und **Vite** in einem **Laravel 11**-Projekt mit **Sail** einrichtest, um eine moderne Frontend-Entwicklung mit automatischer Neuladung (LiveReload) und SCSS-Unterstützung zu ermöglichen.

---
### Erklärung der Struktur:
1. **Einführung**: Eine kurze Einführung zu diesem Dokument.
2. **1️⃣ Laravel 11 mit Sail aufsetzen**: Schritt 1 beschreibt die Installation von Laravel und Sail.
3. **2️⃣ Vite für Laravel installieren**: Dieser Abschnitt erklärt, wie du Vite installierst und konfigurierst.
4. **3️⃣ Bootstrap SCSS installieren**: Zeigt, wie du Bootstrap SCSS und Sass installierst.
5. **4️⃣ SCSS in JavaScript einbinden**: Hier wird gezeigt, wie du SCSS in deine JavaScript-Datei einbindest.
6. **5️⃣ Vite mit Laravel verbinden**: Erklärt, wie du die Vite-Assets in das Blade-Template integrierst und den Entwicklungsserver startest.
7. **Zusammenfassung**: Fasst die wichtigsten Schritte zusammen.

Diese strukturierte Anleitung hilft dir, die Installation und Konfiguration schnell und effizient umzusetzen.

## 1️⃣ Laravel 11 mit Sail aufsetzen

### 1.1 Laravel-Projekt erstellen

Falls noch kein Laravel-Projekt existiert, erstelle ein neues Projekt mit Sail:

```bash
curl -s "https://laravel.build/meinprojekt" | bash
cd meinprojekt
```

Falls du bereits ein Laravel-Projekt hast, stelle sicher, dass **Sail** installiert ist:

```bash
composer require laravel/sail --dev
php artisan sail:install
```

Starte Sail im Hintergrund mit:

```bash
sail up -d
```

---

## 2️⃣ Vite für Laravel installieren

### 2.1 Vite-Paket installieren

Installiere das **Vite**-Paket für Laravel, das die Integration von Vite in das Laravel-Projekt ermöglicht:

```bash
./vendor/bin/sail composer require innologica/laravel-vite
```

---

### 2.2 Vite konfigurieren

Erstelle die Datei `vite.config.js` im Wurzelverzeichnis deines Projekts. Diese Datei verbindet Vite mit deinem Laravel-Projekt und ermöglicht das Verarbeiten von SCSS-Dateien und das Verwenden des LiveReload-Plugins.

```javascript
import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import ViteLiveReload from 'vite-plugin-live-reload';

export default defineConfig({
  plugins: [vue(), ViteLiveReload()],
  css: {
    preprocessorOptions: {
      scss: {
        additionalData: `@import "resources/scss/bootstrap";`
      }
    }
  }
});
```

- **LiveReload** sorgt dafür, dass der Browser automatisch neu lädt, wenn Änderungen an den SCSS-Dateien vorgenommen werden.

---

## 3️⃣ Bootstrap SCSS installieren

### 3.1 Bootstrap und Sass installieren

Installiere **Bootstrap** und **Sass** (für SCSS-Unterstützung) über npm:

```bash
sail npm install bootstrap sass
```

Diese Installation stellt sicher, dass die SCSS-Dateien von Bootstrap in deinem Projekt verfügbar sind.

---

### 3.2 SCSS-Datei erstellen

Erstelle die Datei `resources/scss/app.scss`, in der du **Bootstrap SCSS** importierst:

```scss
@import 'bootstrap/scss/bootstrap';
```

Dies ermöglicht es dir, die Bootstrap-Stile in deinem SCSS-Stylesheet zu verwenden.

---

## 4️⃣ SCSS in JavaScript einbinden

### 4.1 SCSS in `app.js` importieren

Füge in der Datei `resources/js/app.js` den Import für die SCSS-Datei hinzu, um sie in deinem Projekt zu verwenden:

```javascript
import '../scss/app.scss';
```

Dieser Import sorgt dafür, dass Vite die SCSS-Datei beim Build-Prozess berücksichtigt.

---

## 5️⃣ Vite mit Laravel verbinden

### 5.1 Vite-Assets in Blade-Template einfügen

Füge in deinem Blade-Template (z. B. `resources/views/layouts/app.blade.php`) das Vite-Asset-Tag hinzu, um die SCSS- und JavaScript-Dateien zu laden:

```php
@vite(['resources/js/app.js', 'resources/css/app.scss'])
```

Dies sorgt dafür, dass Vite beim Laden der Seite die SCSS- und JS-Dateien korrekt einbindet.

---

### 5.2 Vite-Entwicklungsserver starten

Starte den Vite-Entwicklungsserver, um die Assets zu überwachen und bei Änderungen die Seite automatisch neu zu laden:

```bash
sail npm run dev
```

Der Entwicklungsserver wird die Seite automatisch neu laden, sobald Änderungen an den SCSS-Dateien vorgenommen werden.

---

## 📑 Zusammenfassung

Mit den oben beschriebenen Schritten hast du **Bootstrap SCSS** und **Vite** erfolgreich in deinem Laravel 11-Projekt mit **Sail** eingerichtet. Jetzt kannst du:

- **Bootstrap SCSS** in deinem Projekt verwenden,
- **Vite** als modernen Build-Prozessor nutzen,
- **LiveReload** aktivieren, um automatische Browser-Neuladevorgänge bei Änderungen an den SCSS-Dateien zu haben.

Viel Spaß bei der Entwicklung deines Laravel-Projekts! 🚀


 docker compose -f docker-compose-setup.yml --build
alias sail=./vendor/bin/sail
 sail up -d
  sail npm run build
  sail npm run dev

 sail artisan key:generate
 1263  sail artisan migrate --seed
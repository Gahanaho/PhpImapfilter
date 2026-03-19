# IMAPFilter Web UI

Eine moderne, dunkle Web-Oberfläche für **imapfilter** — gebaut in PHP 8.x.

## Voraussetzungen

- PHP 8.0+ mit Session-Unterstützung
- imapfilter installiert (z.B. `apt install imapfilter` oder `brew install imapfilter`)
- Webserver (Apache / nginx / `php -S`)

## Schnellstart

```bash
# 1. Dateien deployen
cp -r imapfilter-web/ /var/www/html/imapfilter/

# 2. Passwort anpassen (includes/auth.php)
# Ersetze ADMIN_PASS_HASH mit eigenem bcrypt-Hash:
php -r "echo password_hash('dein_passwort', PASSWORD_BCRYPT);"

# 3. Rechte setzen
chmod 700 /var/www/html/imapfilter/data/

# 4. Dev-Server starten (optional)
cd /var/www/html/imapfilter
php -S 0.0.0.0:8080
```

## Konfiguration (`includes/config.php`)

| Konstante           | Standard                           | Beschreibung                  |
|---------------------|------------------------------------|-------------------------------|
| `IMAPFILTER_BIN`    | `/usr/bin/imapfilter`              | Pfad zum Binary               |
| `IMAPFILTER_CONFIG` | `~/.imapfilter/config.lua`         | Pfad zur config.lua           |
| `IMAPFILTER_LOG`    | `~/.imapfilter/imapfilter.log`     | Log-Datei                     |

## Login

Standard-Zugangsdaten (unbedingt ändern!):
- **User:** `admin`
- **Passwort:** `changeme`

## Funktionen

| Seite        | Funktion                                                  |
|--------------|-----------------------------------------------------------|
| Dashboard    | Übersicht Accounts, Regeln, Config-Status, Quick-Actions  |
| Accounts     | IMAP-Konten hinzufügen / entfernen (gespeichert als JSON) |
| Filter Rules | GUI-Editor für Filterregeln (From/Subject/Action etc.)    |
| Config.lua   | Direkter Editor + Auto-Generator aus Accounts & Regeln    |
| Run / Log    | imapfilter manuell starten, Ausgabe & Log einsehen        |

## Sicherheitshinweise

- `data/` enthält Passwörter im Klartext → außerhalb des Webroot lagern oder `.htaccess` schützen
- Nur über HTTPS betreiben (TLS-Zertifikat / Reverse Proxy)
- `ADMIN_PASS_HASH` immer durch eigenen bcrypt-Hash ersetzen
- Für Produktionsbetrieb: PHP `open_basedir` und `disable_functions` für `exec` prüfen

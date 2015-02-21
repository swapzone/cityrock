# CityRock Kursverwaltung
Die Kletterkursverwaltung für den CityRock Stuttgart.

## Inhaltsverzeichnis

1. [Abhängigkeiten](#abhängigkeiten) 
2. [Dokumentation](#dokumentation)
3. [Integration](#integration)

## Abhängigkeiten
* [FPDF Printer](http://www.fpdf.org/) class for PHP
* [Config_Lite](https://github.com/pear/Config_Lite/blob/master/docs/examples)
* [Susy](http://susy.readthedocs.org/en/latest/)

## Dokumentation

### Technologien
Die Applikation besteht im Kern aus einem PHP Backend, das durch Javascript im Frontend erweitert wird. Für Styling wurde das Susy SASS Framework verwendet. Für die Entwicklung kommen Gulp, Node.js, Bower und Typescript zum Einsatz. Als Datenbank wird MySQL verwendet, das Schema für die Datenbank befindet sich auch im Source Code Repository.

### Build
Die Styling Dateien befinden sich in `$ROOT/styles` und werden durch Gulp in ein finales CSS kompiliert. Die TypeScript Dateien befinden sich in `$ROOT/scripts` und werden ebenfalls von Gulp in Javascript kompiliert. Das Ergebnis dieses Kompiliervorgangs landet in `$ROOT/site/styles` bzw. `$ROOT/site/scripts`. 

### Deployment

#### Datenbank
Die Datenbank befindet sich als Schema unter `$ROOT/database/schema.mwb`. Mit Hilfe des Programms "MySQL Workbench" lässt sich aus dieser Schemadatei ein Datenbank Script erstellen, das man in die Datenbank auf dem Webserver importieren kann. Eine Anleitung hierzu gibt es unter [MySQL Schema nach SQL](http://dev.mysql.com/doc/workbench/en/wb-reverse-engineer-create-script.html).

#### Applikation
Um die Applikation auf den Webserver zu deployen, muss das komplette `$ROOT/site/` Verzeichnis auf den Webserver kopiert werden. Davor muss sichergestellt werden, dass Gulp alle notwendigen Dateien an die richtigen Stellen kompiliert hat. Dies geschieht mit dem folgenden Kommando:
```
$ cd $ROOT
$ gulp 
```

Danach sollten sich in `$ROOT/site/` die Unterverzeichnisse `scripts` und `styles` befinden.
Nachdem man alle Dateien auf den Webserver kopiert hat, muss man die Datei `htaccess` in `.htaccess` umbenennen. In `$ROOT/site/_init.php` und `$ROOT/site/_func.php` muss die Variable `$root_directory` auf den richtigen Wert gesetzt werden. Wurde der Inhalt von `$ROOT/site/` direkt in das Root-Verzeichnis des Webservers kopiert, muss die Variable auf `''` gesetzt werden. Wurden die Dateien hingegen in ein Unterverzeichnis kopiert, muss die Variable auf den Pfad dieses Unterverzeichnisses gesetzt werden. Beispiel:
```
$root_directory = "/pfad/zum/unterverzeichnis";
```

Danach müssen die Zugangsdaten für die Datenbank in `$ROOT/site/_func.php` in der Funktion `createConnection()` eingetragen werden
Jetzt ist die Applikation unter der entsprechenden URL des Webservers erreichbar.

## Integration
Sämtliche Integrationsdateien befinden sich unter `$ROOT/integration`. Diese Dateien sind direkt Bestandteil der aktuellen Cityrock Website und können nicht ohne weiteres verwendet werden, falls sich die Cityrock Website in der Zwischenzeit verändert hat. 

Sämtliche Integrationsdateien sind aber ausreichend im Code dokumentiert. Die Datei `cron.php` beinhaltet die Logik für die Erinnerungsmails an die Teilnehmer und für die automatischen Mails an die Verwaltung. Sie wird als sogenannter Cronjob jeden Tag ausgeführt. 

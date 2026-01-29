@echo off
echo Starting Laravel with XAMPP PHP and forced INI...
C:\xampp\php\php.exe -c C:\xampp\php\php.ini artisan serve --port=8003 --host=127.0.0.1
pause

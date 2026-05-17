@echo off
title Menjalankan Aplikasi Absensi MJA Lokal
echo ====================================================================
echo   Menjalankan Aplikasi Absensi MJA (Terhubung ke TiDB Cloud)
echo ====================================================================
echo.
echo Server lokal sedang dinyalakan...
echo Silakan buka browser Anda dan akses:
echo.
echo        👉 http://127.0.0.1:8000
echo.
echo ====================================================================
echo Jangan tutup jendela CMD ini selama Anda menggunakan aplikasi!
echo ====================================================================
echo.
cd /d "%~dp0"
..\php83\php.exe artisan serve --port=8000
pause

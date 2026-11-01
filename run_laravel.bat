@echo off
title SIMKERMA UNIPAR - Laravel 11 Server
color 0A

echo =====================================================================
echo       UNIVERSITAS PARNA RAYA (UNIPAR) MANADO
echo   SISTEM INFORMASI DOKUMEN KERJA SAMA (SIMKERMA) - LARAVEL 11
echo =====================================================================
echo.
echo [1/3] Memeriksa status basis data MySQL (Port 3306)...
netstat -ano | findstr :3306 >nul
if %ERRORLEVEL% EQU 0 (
    echo [OK] Layanan MySQL aktif pada port 3306.
) else (
    echo [PERINGATAN] Layanan MySQL belum terdeteksi aktif di port 3306!
    echo Mencoba mengaktifkan MySQL XAMPP...
    if exist "C:\xampp\mysql\bin\mysqld.exe" (
        start "" /B "C:\xampp\mysql\bin\mysqld.exe" --defaults-file="C:\xampp\mysql\bin\my.ini" --standalone
        timeout /t 2 >nul
        echo [OK] Layanan MySQL XAMPP telah diluncurkan.
    ) else (
        echo [INFO] Silakan pastikan modul MySQL pada XAMPP Control Panel sudah berstatus 'Running'.
    )
)

echo.
echo [2/3] Membuka browser default ke http://127.0.0.1:8000 ...
start "" "http://127.0.0.1:8000"

echo.
echo [3/3] Menjalankan server aplikasi Laravel 11 (PHP 8.2)...
echo Tekan [Ctrl + C] untuk menghentikan server aplikasi sewaktu-waktu.
echo =====================================================================
echo.

if exist "C:\xampp\php\php.exe" (
    "C:\xampp\php\php.exe" artisan serve --host=127.0.0.1 --port=8000
) else (
    php artisan serve --host=127.0.0.1 --port=8000
)

pause

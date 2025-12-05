@echo off
echo ====================================
echo   Seedowanie bazy danych
echo ====================================
echo.

echo [1/4] Seedowanie rol i uprawnien...
php artisan db:seed --class=RolesAndPermissionsSeeder
if %ERRORLEVEL% NEQ 0 (
    echo BLAD: Nie udalo sie zaseedowac rol!
    pause
    exit /b 1
)

echo.
echo [2/4] Seedowanie danych testowych...
php artisan db:seed --class=MembersSeeder
if %ERRORLEVEL% NEQ 0 (
    echo BLAD: Nie udalo sie zaseedowac danych!
    pause
    exit /b 1
)

echo.
echo [3/4] Resetowanie cache uprawnien...
php artisan permission:cache-reset

echo.
echo [4/4] Czyszczenie cache aplikacji...
php artisan cache:clear
php artisan config:clear

echo.
echo ====================================
echo   Seedowanie zakonczone!
echo ====================================
echo.
echo Dane logowania:
echo   Admin: admin@opwzdronem.pl / P@ssw0rd
echo   Koordynator: angelo1997@wp.pl / Pssw0rd
echo   Instruktor 1: jan.kowalski@example.com / Haslo1234
echo   Instruktor 2: anna.nowak@example.com / Haslo1234
echo   Wychowawca: piotr.wisniewski@example.com / Haslo1234
echo   Studenci: student1@example.com - student30@example.com / Haslo1234
echo.
pause

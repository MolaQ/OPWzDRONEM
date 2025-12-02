Write-Host "====================================" -ForegroundColor Cyan
Write-Host "  Seedowanie bazy danych" -ForegroundColor Cyan
Write-Host "====================================" -ForegroundColor Cyan
Write-Host ""

Write-Host "[1/4] Seedowanie rol i uprawnien..." -ForegroundColor Yellow
php artisan db:seed --class=RolesAndPermissionsSeeder
if ($LASTEXITCODE -ne 0) {
    Write-Host "BLAD: Nie udalo sie zaseedowac rol!" -ForegroundColor Red
    Read-Host "Nacisnij Enter aby zakonczyc"
    exit 1
}

Write-Host ""
Write-Host "[2/4] Seedowanie danych testowych..." -ForegroundColor Yellow
php artisan db:seed --class=MembersSeeder
if ($LASTEXITCODE -ne 0) {
    Write-Host "BLAD: Nie udalo sie zaseedowac danych!" -ForegroundColor Red
    Read-Host "Nacisnij Enter aby zakonczyc"
    exit 1
}

Write-Host ""
Write-Host "[3/4] Resetowanie cache uprawnien..." -ForegroundColor Yellow
php artisan permission:cache-reset

Write-Host ""
Write-Host "[4/4] Czyszczenie cache aplikacji..." -ForegroundColor Yellow
php artisan cache:clear
php artisan config:clear

Write-Host ""
Write-Host "====================================" -ForegroundColor Green
Write-Host "  Seedowanie zakonczone!" -ForegroundColor Green
Write-Host "====================================" -ForegroundColor Green
Write-Host ""
Write-Host "Dane logowania:" -ForegroundColor White
Write-Host "  Admin: admin@exaple.com / Haslo1234" -ForegroundColor Cyan
Write-Host "  Instruktor 1: jan.kowalski@example.com / Haslo1234" -ForegroundColor Cyan
Write-Host "  Instruktor 2: anna.nowak@example.com / Haslo1234" -ForegroundColor Cyan
Write-Host "  Wychowawca: piotr.wisniewski@example.com / Haslo1234" -ForegroundColor Cyan
Write-Host "  Studenci: student1@example.com - student30@example.com / Haslo1234" -ForegroundColor Cyan
Write-Host ""
Read-Host "Nacisnij Enter aby zakonczyc"

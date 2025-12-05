# Plan Rozwoju Systemu OPW z Dronem

## ✅ Ukończone
- [x] Instalacja i konfiguracja Spatie Permission
- [x] Zdefiniowanie granularnych uprawnień (`module.action`)
- [x] Stworzenie ról: admin, koordynator, instructor, wychowawca, nauczyciel, student, guest
- [x] Seedowanie kont demo z odpowiednimi rolami
- [x] Migracja z kolumny `users.role` na system Spatie
- [x] Aktualizacja middleware i widoków do nowego systemu
- [x] Logowanie i autoryzacja użytkowników
- [x] Layout ustawień (profile, password, appearance)

---

## 🔨 Krótkoterminowe (1-2 dni) — PRIORITY

### 1. Testy funkcjonalne autoryzacji
- [ ] Dostęp do `/admin` dla admin, koordynator, instructor (powinien mieć dostęp)
- [ ] Dostęp do `/admin` dla student, guest (powinien być zablokowany → redirect na `/home`)
- [ ] Menu sidebar dynamicznie pokazuje sekcje na podstawie `@canany(['permission1', 'permission2'])`
- [ ] Każda strona admina wczytuje się bez błędów dla autoryzowanych użytkowników

### 2. UI Panel zarządzania rolami i uprawnieniami
- [ ] Komponenty Livewire: `Admin\Roles\Index` (lista ról)
- [ ] Komponenty Livewire: `Admin\Roles\Show` (edycja roli, macierz uprawnień)
- [ ] Macierz uprawnień (kategorie: Panel, Users, Equipment, Content, Settings)
- [ ] Przełączniki zaznaczania/odznaczania uprawnień dla każdej roli
- [ ] Blokada edycji ról krytycznych (admin, koordynator)
- [ ] Integracja z komponentem `Members` – przypisywanie ról użytkownikom

### 3. Zatwierdzanie materiałów kursu
- [ ] Zakończyć logikę `course-materials.approve` w `Admin/Courses.php` i `Admin/CourseMaterials.php`
- [ ] UI do zatwierdzania/odrzucania materiałów (modal z polem na powód odrzucenia)
- [ ] Notyfikacja dla autora materiału (zatwierdzony/odrzucony)

---

## 📊 Średnioterminowe (1 tydzień)

### 4. Audit Logging – śledzenie zmian
- [ ] Model `AuditLog` do rejestracji akcji
- [ ] Hook w `RolesAndPermissionsSeeder`, `Members`, `Groups` etc.
- [ ] Co logować: użytkownik, akcja (create/update/delete), model, stara/nowa wartość, czas
- [ ] Widok historii zmian w panelu admin

### 5. Ulepszenia system wypożyczeń (Equipment Rentals)
- [ ] Reguły biznesowe: kto może wypożyczać co, na jak długo
- [ ] Notyfikacje o wygasających wypożyczeniach (email/alert w panelu)
- [ ] Raport szkód/zwrotów
- [ ] Stan sprzętu przy wypożyczeniu (fotografia, notatka o stanie)

### 6. Eksport danych (readonly endpoints)
- [ ] API endpoints do eksportu dla raportów (CSV/JSON)
- [ ] Permissje do eksportów: `exports.run`, `exports.download`

---

## 🚀 Długoterminowe (2-4 tygodnie)

### 7. Mobilna aplikacja
- [ ] Dostęp do skanera kodów kreskowych (barcode reader)
- [ ] Status wypożyczeń w realtime
- [ ] Offline mode (sync gdy dostęp do internetu)

### 8. Integracje systemowe
- [ ] Synchronizacja z systemem szkolnym (uczniowie, grupy, rozkład zajęć)
- [ ] E-mailowe powiadomienia dla instruktorów/wychowawców
- [ ] SMS alerts dla spóźnionych zwrotów (Twilio/SMS API)
- [ ] Webhook do logów zewnętrznych systemów

### 9. Optymalizacja i scaling
- [ ] Cache dla permissionów i ról (Redis)
- [ ] Indexowanie baz danych (indeksy na foreign keys, email, barcode)
- [ ] Testy obciążeniowe (JMeter/Apache Bench)
- [ ] CDN dla statycznych zasobów (zdjęcia, CSS)

---

## 📝 Notatki
- **Baza**: MySQL, Laravel 12, Livewire 3, Spatie Permission v6
- **Layout user**: `components.layouts.user` (sidebar + content)
- **Layout admin**: `components.layouts.app.sidebar` (admin menu + content)
- **Style**: Tailwind CSS + Flux components
- **Konta demo**:
  - Admin: `admin@opwzdronem.pl` / `P@ssw0rd`
  - Koordynator: `angelo1997@wp.pl` / `Pssw0rd`
  - Instruktor: `jan.kowalski@example.com` / `Haslo1234`
  - Studenci: `student1@example.com` ... `student30@example.com` / `Haslo1234`

@echo off
cd c:\xampp\htdocs\CTFxD
php artisan db:seed --class=MembersSeeder

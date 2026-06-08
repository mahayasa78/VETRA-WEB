@echo off
echo A | php artisan config:clear
echo A | php artisan cache:clear  
echo A | php artisan route:clear
echo A | php artisan view:clear
echo Cache cleared successfully!
pause

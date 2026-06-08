@echo off
echo ========================================
echo Vetra API - Swagger Installation
echo ========================================
echo.

echo Step 1: Installing L5-Swagger package...
call composer require darkaonline/l5-swagger
echo.

echo Step 2: Publishing configuration...
call php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider"
echo.

echo Step 3: Generating Swagger documentation...
call php artisan l5-swagger:generate
echo.

echo ========================================
echo Installation Complete!
echo ========================================
echo.
echo Access Swagger UI at:
echo http://127.0.0.1:8000/api/documentation
echo.
echo Make sure Laravel server is running:
echo php artisan serve
echo.
pause

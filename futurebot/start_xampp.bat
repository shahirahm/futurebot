@echo off
echo Starting XAMPP Services...
echo.

echo Starting MySQL...
cd /d C:\xampp\mysql\bin
start /B mysqld.exe --console

echo Starting Apache...
cd /d C:\xampp\apache\bin
start /B httpd.exe

echo.
echo Services started! Please wait a moment for them to fully initialize.
echo.
echo You can now access your application at: http://localhost/futurebot/
echo.
pause 
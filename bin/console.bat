@ECHO OFF
SET BIN_TARGET=%~dp0/console
php -d display_errors=1 "%BIN_TARGET%" %*

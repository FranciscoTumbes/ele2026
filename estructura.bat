@echo off
chcp 65001 >nul
title Crear Estructura Sistema Electoral

echo ==================================================
echo Creando estructura del sistema...
echo ==================================================


mkdir sistema_electoral\config 2>nul
mkdir sistema_electoral\core 2>nul
mkdir sistema_electoral\models 2>nul
mkdir sistema_electoral\repositories 2>nul


type nul > sistema_electoral\config\database.php
type nul > sistema_electoral\core\Database.php
type nul > sistema_electoral\core\Model.php
type nul > sistema_electoral\core\AppException.php
type nul > sistema_electoral\models\Ubicacion.php
type nul > sistema_electoral\models\Eleccion.php
type nul > sistema_electoral\models\AgrupacionPolitica.php
type nul > sistema_electoral\models\Candidato.php
type nul > sistema_electoral\models\Elector.php
type nul > sistema_electoral\models\Voto.php
type nul > sistema_electoral\models\ActaSufragio.php
type nul > sistema_electoral\models\Usuario.php
type nul > sistema_electoral\repositories\ResultadosRepository.php
type nul > sistema_electoral\index.php

echo.
echo Estructura creada correctamente en:
echo %cd%\sistema_electoral\
echo.
pause
exit
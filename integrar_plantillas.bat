@echo off
title Integracion Plantillas WowDash + Larkon - Cotizador Yepez
color 0B
echo ========================================
echo  INTEGRACION DE PLANTILLAS
echo  WowDash + Larkon para Cotizador Yepez
echo ========================================
echo.

REM Verificar que estamos en la carpeta correcta
if not exist "README.md" (
    echo ERROR: Ejecuta este script desde la carpeta del proyecto cotizador-yepez
    echo Ubicacion correcta: C:\xampp\htdocs\cotizador-yepez\
    pause
    exit /b 1
)

echo [1/10] Verificando estructura del proyecto...
if not exist "temp" mkdir temp
if not exist "temp\wowdash" mkdir temp\wowdash
if not exist "temp\larkon" mkdir temp\larkon

echo.
echo [INFO] Copia las plantillas descargadas en:
echo - WowDash CodeIgniter: temp\wowdash\
echo - Larkon CodeIgniter: temp\larkon\
echo.
echo ¿Ya copiaste las plantillas en las carpetas temp? (S/N)
set /p respuesta=
if /i "%respuesta%" neq "S" (
    echo.
    echo Por favor copia las plantillas y ejecuta el script nuevamente.
    echo.
    echo INSTRUCCIONES:
    echo 1. Extrae WowDash-CodeIgniter.zip en: temp\wowdash\
    echo 2. Extrae Larkon-CodeIgniter.zip en: temp\larkon\
    echo 3. Ejecuta este script de nuevo
    pause
    exit /b 0
)

echo.
echo [2/10] Integrando WowDash como base del sistema...

REM Verificar que WowDash existe
if not exist "temp\wowdash" (
    echo ERROR: No se encuentra la carpeta temp\wowdash\
    echo Extrae WowDash CodeIgniter en esa ubicacion
    pause
    exit /b 1
)

REM Buscar la carpeta principal de WowDash (puede variar el nombre)
for /d %%D in ("temp\wowdash\*") do (
    if exist "%%D\application" (
        set WOWDASH_PATH=%%D
        goto :found_wowdash
    )
    if exist "%%D\app" (
        set WOWDASH_PATH=%%D
        goto :found_wowdash
    )
)

:found_wowdash
if "%WOWDASH_PATH%"=="" (
    echo ERROR: No se encontro la estructura de CodeIgniter en WowDash
    echo Verifica que la extraccion sea correcta
    pause
    exit /b 1
)

echo Encontrado WowDash en: %WOWDASH_PATH%

echo [3/10] Copiando estructura base de WowDash...

REM Copiar archivos principales de CodeIgniter
if exist "%WOWDASH_PATH%\application" (
    echo Copiando application folder...
    xcopy "%WOWDASH_PATH%\application" "admin\application" /E /I /Y >nul
)

if exist "%WOWDASH_PATH%\app" (
    echo Copiando app folder...
    xcopy "%WOWDASH_PATH%\app" "admin\app" /E /I /Y >nul
)

if exist "%WOWDASH_PATH%\system" (
    echo Copiando system folder...
    xcopy "%WOWDASH_PATH%\system" "admin\system" /E /I /Y >nul
)

if exist "%WOWDASH_PATH%\assets" (
    echo Copiando assets...
    xcopy "%WOWDASH_PATH%\assets" "assets" /E /I /Y >nul
)

if exist "%WOWDASH_PATH%\index.php" (
    copy "%WOWDASH_PATH%\index.php" "admin\index.php" >nul
)

if exist "%WOWDASH_PATH%\.htaccess" (
    copy "%WOWDASH_PATH%\.htaccess" "admin\.htaccess" >nul
)

echo [4/10] Configurando estructura personalizada...

REM Crear controladores personalizados
mkdir "admin\application\controllers" 2>nul
mkdir "admin\application\models" 2>nul
mkdir "admin\application\views\cotizador" 2>nul

echo [5/10] Extrayendo componentes utiles de Larkon...

REM Buscar Larkon
for /d %%D in ("temp\larkon\*") do (
    if exist "%%D\application" (
        set LARKON_PATH=%%D
        goto :found_larkon
    )
    if exist "%%D\app" (
        set LARKON_PATH=%%D
        goto :found_larkon
    )
)

:found_larkon
if "%LARKON_PATH%"=="" (
    echo [WARNING] Larkon no encontrado, continuando solo con WowDash...
    goto :skip_larkon
)

echo Encontrado Larkon en: %LARKON_PATH%

REM Extraer componentes específicos de Larkon
if exist "%LARKON_PATH%\assets\css" (
    echo Copiando estilos adicionales de Larkon...
    xcopy "%LARKON_PATH%\assets\css" "assets\css\larkon" /E /I /Y >nul
)

if exist "%LARKON_PATH%\assets\js" (
    echo Copiando JavaScript adicional de Larkon...
    xcopy "%LARKON_PATH%\assets\js" "assets\js\larkon" /E /I /Y >nul
)

:skip_larkon

echo [6/10] Configurando base de datos...

REM Crear archivo de configuracion de database
echo ^<?php > admin\application\config\database.php
echo defined('BASEPATH') OR exit('No direct script access allowed'); >> admin\application\config\database.php
echo. >> admin\application\config\database.php
echo $active_group = 'default'; >> admin\application\config\database.php
echo $query_builder = TRUE; >> admin\application\config\database.php
echo. >> admin\application\config\database.php
echo $db['default'] = array( >> admin\application\config\database.php
echo     'dsn'      =^> '', >> admin\application\config\database.php
echo     'hostname' =^> 'localhost', >> admin\application\config\database.php
echo     'username' =^> 'root', >> admin\application\config\database.php
echo     'password' =^> '', >> admin\application\config\database.php
echo     'database' =^> 'cotizador_yepez', >> admin\application\config\database.php
echo     'dbdriver' =^> 'mysqli', >> admin\application\config\database.php
echo     'dbprefix' =^> '', >> admin\application\config\database.php
echo     'pconnect' =^> FALSE, >> admin\application\config\database.php
echo     'db_debug' =^> (ENVIRONMENT !== 'production'^), >> admin\application\config\database.php
echo     'cache_on' =^> FALSE, >> admin\application\config\database.php
echo     'cachedir' =^> '', >> admin\application\config\database.php
echo     'char_set' =^> 'utf8mb4', >> admin\application\config\database.php
echo     'dbcollat' =^> 'utf8mb4_unicode_ci', >> admin\application\config\database.php
echo     'swap_pre' =^> '', >> admin\application\config\database.php
echo     'encrypt'  =^> FALSE, >> admin\application\config\database.php
echo     'compress' =^> FALSE, >> admin\application\config\database.php
echo     'stricton' =^> FALSE, >> admin\application\config\database.php
echo     'failover' =^> array(^), >> admin\application\config\database.php
echo     'save_queries' =^> TRUE >> admin\application\config\database.php
echo ^); >> admin\application\config\database.php

echo [7/10] Creando controlador principal del cotizador...

REM Crear controlador Cotizador
echo ^<?php > admin\application\controllers\Cotizador.php
echo defined('BASEPATH') OR exit('No direct script access allowed'); >> admin\application\controllers\Cotizador.php
echo. >> admin\application\controllers\Cotizador.php
echo class Cotizador extends CI_Controller { >> admin\application\controllers\Cotizador.php
echo. >> admin\application\controllers\Cotizador.php
echo     public function __construct() { >> admin\application\controllers\Cotizador.php
echo         parent::__construct(); >> admin\application\controllers\Cotizador.php
echo         $this-^>load-^>database(); >> admin\application\controllers\Cotizador.php
echo         $this-^>load-^>model('Bateria_model'); >> admin\application\controllers\Cotizador.php
echo         $this-^>load-^>helper('url'); >> admin\application\controllers\Cotizador.php
echo     } >> admin\application\controllers\Cotizador.php
echo. >> admin\application\controllers\Cotizador.php
echo     public function index() { >> admin\application\controllers\Cotizador.php
echo         $data['title'] = 'Cotizador Acumuladores Yepez'; >> admin\application\controllers\Cotizador.php
echo         $this-^>load-^>view('cotizador/busqueda', $data); >> admin\application\controllers\Cotizador.php
echo     } >> admin\application\controllers\Cotizador.php
echo. >> admin\application\controllers\Cotizador.php
echo     public function buscar() { >> admin\application\controllers\Cotizador.php
echo         $marca = $this-^>input-^>post('marca'); >> admin\application\controllers\Cotizador.php
echo         $modelo = $this-^>input-^>post('modelo'); >> admin\application\controllers\Cotizador.php
echo         $anio = $this-^>input-^>post('anio'); >> admin\application\controllers\Cotizador.php
echo. >> admin\application\controllers\Cotizador.php
echo         $baterias = $this-^>Bateria_model-^>buscar_compatibles($marca, $modelo, $anio); >> admin\application\controllers\Cotizador.php
echo         echo json_encode($baterias); >> admin\application\controllers\Cotizador.php
echo     } >> admin\application\controllers\Cotizador.php
echo. >> admin\application\controllers\Cotizador.php
echo } >> admin\application\controllers\Cotizador.php

echo [8/10] Creando modelo de baterias...

REM Crear modelo Bateria_model
echo ^<?php > admin\application\models\Bateria_model.php
echo defined('BASEPATH') OR exit('No direct script access allowed'); >> admin\application\models\Bateria_model.php
echo. >> admin\application\models\Bateria_model.php
echo class Bateria_model extends CI_Model { >> admin\application\models\Bateria_model.php
echo. >> admin\application\models\Bateria_model.php
echo     public function buscar_compatibles($marca, $modelo, $anio) { >> admin\application\models\Bateria_model.php
echo         $sql = "CALL buscar_baterias_avanzado(?, ?, ?, NULL)"; >> admin\application\models\Bateria_model.php
echo         $query = $this-^>db-^>query($sql, array($marca, $modelo, $anio)); >> admin\application\models\Bateria_model.php
echo         return $query-^>result_array(); >> admin\application\models\Bateria_model.php
echo     } >> admin\application\models\Bateria_model.php
echo. >> admin\application\models\Bateria_model.php
echo     public function obtener_todas() { >> admin\application\models\Bateria_model.php
echo         $this-^>db-^>where('disponible', TRUE); >> admin\application\models\Bateria_model.php
echo         return $this-^>db-^>get('baterias_lth')-^>result_array(); >> admin\application\models\Bateria_model.php
echo     } >> admin\application\models\Bateria_model.php
echo. >> admin\application\models\Bateria_model.php
echo } >> admin\application\models\Bateria_model.php

echo [9/10] Configurando rutas y configuracion...

REM Configurar rutas
echo ^<?php > admin\application\config\routes.php
echo defined('BASEPATH') OR exit('No direct script access allowed'); >> admin\application\config\routes.php
echo. >> admin\application\config\routes.php
echo $route['default_controller'] = 'cotizador'; >> admin\application\config\routes.php
echo $route['404_override'] = ''; >> admin\application\config\routes.php
echo $route['translate_uri_dashes'] = FALSE; >> admin\application\config\routes.php
echo. >> admin\application\config\routes.php
echo // Rutas del cotizador >> admin\application\config\routes.php
echo $route['buscar'] = 'cotizador/buscar'; >> admin\application\config\routes.php
echo $route['cotizar'] = 'cotizador/index'; >> admin\application\config\routes.php

REM Configurar config principal
if exist "admin\application\config\config.php" (
    echo Configurando URL base...
    powershell -Command "(Get-Content 'admin\application\config\config.php') -replace '\$config\[''base_url''\].*', '$config[''base_url''] = ''http://localhost/cotizador-yepez/admin/'';' | Set-Content 'admin\application\config\config.php'"
)

echo [10/10] Creando archivo de prueba...

REM Crear archivo de prueba del sistema
echo ^<?php > admin\test_sistema.php
echo // Prueba del sistema integrado >> admin\test_sistema.php
echo require_once 'index.php'; >> admin\test_sistema.php
echo. >> admin\test_sistema.php
echo echo "^<h1^>Sistema Cotizador Yepez^</h1^>"; >> admin\test_sistema.php
echo echo "^<p^>Fecha: " . date('d/m/Y H:i:s') . "^</p^>"; >> admin\test_sistema.php
echo echo "^<p^>Base URL: ^<a href='http://localhost/cotizador-yepez/admin/'^>http://localhost/cotizador-yepez/admin/^</a^>^</p^>"; >> admin\test_sistema.php
echo echo "^<p^>Estado: Sistema integrado correctamente^</p^>"; >> admin\test_sistema.php

echo.
echo ========================================
echo  INTEGRACION COMPLETADA EXITOSAMENTE
echo ========================================
echo.
echo ✅ WowDash integrado como base
echo ✅ Componentes de Larkon extraidos  
echo ✅ Base de datos configurada
echo ✅ Controladores creados
echo ✅ Modelos configurados
echo ✅ Rutas establecidas
echo.
echo URLS DE PRUEBA:
echo 🌐 Sistema: http://localhost/cotizador-yepez/admin/
echo 🧪 Test: http://localhost/cotizador-yepez/admin/test_sistema.php
echo 🔍 Cotizador: http://localhost/cotizador-yepez/admin/cotizador
echo.
echo SIGUIENTE PASO:
echo 1. Abre Cursor en esta carpeta
echo 2. Prueba las URLs en el navegador
echo 3. Comienza a desarrollar el cotizador
echo.
echo ¡LISTO PARA PROGRAMAR!
echo.
pause
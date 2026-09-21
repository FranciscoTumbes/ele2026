<?php
declare(strict_types=1);

/** @var Router $router */

// === Rutas públicas ===
$router->get('/', 'FrontendController@index');
$router->get('/login', 'FrontendController@login');
$router->get('/dashboard', 'FrontendController@dashboard');
$router->get('/digitacion', 'FrontendController@digitacion');

$router->post('/api/auth/login', 'AuthController@login');

// === Rutas protegidas (requieren autenticación) ===
$router->group('api', ['AuthMiddleware'], function($router) {

    // Auth
    $router->post('/auth/logout', 'AuthController@logout');
    $router->get('/auth/me',    'AuthController@me');

    // Ubicaciones geográficas
    $router->get('/provincias', 'UbicacionController@provincias');
    $router->get('/distritos',  'UbicacionController@distritos');
    $router->get('/centros',    'UbicacionController@centros');
    $router->get('/mesas',      'UbicacionController@mesas');

    // Candidatos
    $router->get('/candidatos',  'CandidatoController@listar');
    $router->post('/candidatos', 'CandidatoController@crear');

    // Actas (digitación)
    $router->post('/actas',              'ActaController@registrar');
    $router->get('/actas/pendientes',    'ActaController@pendientes');
    $router->put('/actas/{id}/verificar','ActaController@verificar');

    // Resultados
    $router->get('/resultados/totalizacion', 'ResultadosController@totalizacion');
    $router->get('/resultados/avance',       'ResultadosController@avance');
    $router->get('/resultados/resumen',      'ResultadosController@resumen');
});
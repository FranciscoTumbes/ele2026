<?php
declare(strict_types=1);

class FrontendController extends Controller
{
    public function index()
    {
        // Redirect to login if not authenticated, otherwise dashboard
        Response::redirect('/ele2026/public/login');
    }

    public function login()
    {
        Response::html('layout', [
            'title' => 'Iniciar Sesión - ELE2026',
            'hideLayout' => true,
            'contentView' => 'login'
        ]);
    }

    public function dashboard()
    {
        Response::html('layout', [
            'title' => 'Dashboard Electoral',
            'activeMenu' => 'dashboard',
            'contentView' => 'resultados'
        ]);
    }

    public function digitacion()
    {
        Response::html('layout', [
            'title' => 'Digitación de Actas',
            'activeMenu' => 'digitacion',
            'contentView' => 'digitacion'
        ]);
    }
}

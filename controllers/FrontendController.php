<?php
declare(strict_types=1);

class FrontendController extends Controller
{
    public function index()
    {
        $target = $this->currentUser()
            ? '/ele2026/public/dashboard'
            : '/ele2026/public/login';

        Response::redirect($target);
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
        if (!$this->currentUser()) {
            Response::redirect('/ele2026/public/login');
        }

        Response::html('layout', [
            'title' => 'Dashboard Electoral',
            'activeMenu' => 'dashboard',
            'contentView' => 'resultados'
        ]);
    }

    public function digitacion()
    {
        if (!$this->currentUser()) {
            Response::redirect('/ele2026/public/login');
        }

        Response::html('layout', [
            'title' => 'Digitación de Actas',
            'activeMenu' => 'digitacion',
            'contentView' => 'digitacion'
        ]);
    }
}

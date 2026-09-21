<?php
declare(strict_types=1);

class ResultadosController extends Controller
{
    /** GET /api/resultados/totalizacion?eleccion_id=1&cargo_id=1 */
    public function totalizacion(Request $request): void
    {
        $eleccionId = (int)$request->input('eleccion_id');
        $cargoId    = (int)$request->input('cargo_id');
        $ambitoId   = $request->input('ambito_id');

        $repo = new ResultadosRepository();
        $data = $repo->totalizacionCargo($eleccionId, $cargoId, $ambitoId ? (int)$ambitoId : null);
        Response::success($data);
    }

    /** GET /api/resultados/avance */
    public function avance(Request $request): void
    {
        $eleccionId = (int)$request->input('eleccion_id', 1);
        $repo = new ResultadosRepository();
        Response::success($repo->avancePorProvincia($eleccionId));
    }

    /** GET /api/resultados/resumen */
    public function resumen(Request $request): void
    {
        $eleccionId = (int)$request->input('eleccion_id', 1);
        $repo = new ResultadosRepository();
        Response::success($repo->resumenEleccion($eleccionId));
    }
}
<?php
declare(strict_types=1);

class UbicacionController extends Controller
{
    /** GET /api/provincias?region_id=1 */
    public function provincias(Request $request): void
    {
        $regionId = (int) $request->input('region_id', 1);
        $model = new Provincia();
        Response::success($model->porRegion($regionId));
    }

    /** GET /api/distritos?provincia_id=X */
    public function distritos(Request $request): void
    {
        $provinciaId = (int) $request->input('provincia_id');
        if (!$provinciaId) Response::error('provincia_id requerido', 400);

        $model = new Distrito();
        Response::success($model->porProvincia($provinciaId));
    }

    /** GET /api/centros?distrito_id=X */
    public function centros(Request $request): void
    {
        $distritoId = (int) $request->input('distrito_id');
        if (!$distritoId) Response::error('distrito_id requerido', 400);

        $model = new CentroVotacion();
        Response::success($model->listarCompleto($distritoId));
    }

    /** GET /api/mesas?centro_id=X */
    public function mesas(Request $request): void
    {
        $centroId = (int) $request->input('centro_id');
        if (!$centroId) Response::error('centro_id requerido', 400);

        $model = new MesaSufragio();
        Response::success($model->porCentro($centroId));
    }
}
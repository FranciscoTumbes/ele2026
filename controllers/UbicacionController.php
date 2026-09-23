<?php
declare(strict_types=1);

class UbicacionController extends Controller
{
    /** GET /api/provincias?region_id=1 (si se omite, usa la primera región registrada) */
    public function provincias(Request $request): void
    {
        $raw = $request->input('region_id');
        $regionId = ($raw === null || $raw === '') ? null : (int) $raw;
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

    /** GET /api/mesas/buscar?numero=XXXX&eleccion_id=Y */
    public function buscar(Request $request): void
    {
        $numero = trim((string) $request->input('numero', ''));
        if ($numero === '') {
            Response::error('El parámetro "numero" es requerido', 400);
        }

        // eleccion_id es opcional pero recomendado: sin él, el estado del acta
        // mostraría el de cualquier elección, no el de la que se está digitando.
        $eleccionId = (int) $request->input('eleccion_id');

        $model = new MesaSufragio();
        $mesa = $model->buscarPorNumero($numero, $eleccionId ?: null);

        if (!$mesa) {
            Response::error('Mesa no encontrada con el número: ' . $numero, 404);
        }

        // Si el acta ya fue digitada, avisar al cliente
        if (!empty($mesa['acta_estado']) && $mesa['acta_estado'] !== 'PENDIENTE') {
            $mesa['acta_ya_digitada'] = true;
        } else {
            $mesa['acta_ya_digitada'] = false;
        }

        Response::success($mesa, 'Mesa encontrada');
    }
}
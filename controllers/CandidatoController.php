<?php
declare(strict_types=1);

class CandidatoController extends Controller
{
    /** GET /api/candidatos?eleccion_id=1&cargo_id=1&ambito_id=X */
    public function listar(Request $request): void
    {
        $eleccionId = (int) $request->input('eleccion_id');
        $cargoId    = (int) $request->input('cargo_id');
        $ambitoId   = $request->input('ambito_id');

        if (!$eleccionId || !$cargoId) {
            Response::error('eleccion_id y cargo_id son obligatorios', 400);
        }

        $model = new Candidato();
        $data = $model->porEleccionYCargo($eleccionId, $cargoId, $ambitoId ? (int)$ambitoId : null);
        Response::success($data);
    }

    /** POST /api/candidatos (solo ADMIN) */
    public function crear(Request $request): void
    {
        $data = $this->validate($request->input(), [
            'agrupacion_id'   => 'required|numeric',
            'cargo_id'        => 'required|numeric',
            'dni'             => 'required|max:8',
            'nombres'         => 'required|max:150',
            'apellido_paterno'=> 'required|max:100',
            'apellido_materno'=> 'required|max:100'
        ]);

        if (!Candidato::validarDni($data['dni'])) {
            Response::error('DNI inválido (debe tener 8 dígitos)', 422);
        }

        $model = new Candidato();
        $id = $model->create($data);
        Response::success(['id' => $id], 'Candidato registrado', 201);
    }
}
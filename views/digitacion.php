<div class="grid grid-cols-3">
    <!-- Panel Izquierdo: Selección de Acta -->
    <div class="card" style="grid-column: span 1; align-self: start;">
        <h2 style="margin-bottom: 1.5rem; font-size: 1.25rem;">Buscar Acta</h2>
        
        <form id="searchActaForm">
            <div class="form-group">
                <label class="form-label">Número de Mesa</label>
                <div style="display: flex; gap: 0.5rem;">
                    <input type="text" id="mesaSearchInput" class="form-control" placeholder="Ej. 004561" required>
                    <button type="submit" class="btn btn-primary" id="btnSearchMesa">
                        🔍 Buscar
                    </button>
                </div>
            </div>
        </form>

        <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 1.5rem 0;">

        <div id="mesaInfoContainer" style="display: none;">
            <h3 class="text-secondary" style="font-size: 0.875rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 1rem;">Detalles de la Mesa</h3>
            <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.75rem;">
                <li style="display: flex; justify-content: space-between;">
                    <span class="text-muted">Mesa:</span>
                    <strong id="infoMesaNum">---</strong>
                </li>
                <li style="display: flex; justify-content: space-between;">
                    <span class="text-muted">Centro:</span>
                    <strong id="infoCentro">---</strong>
                </li>
                <li style="display: flex; justify-content: space-between;">
                    <span class="text-muted">Distrito:</span>
                    <strong id="infoDistrito">---</strong>
                </li>
                <li style="display: flex; justify-content: space-between;">
                    <span class="text-muted">Electores Hábiles:</span>
                    <strong id="infoElectores">---</strong>
                </li>
            </ul>
        </div>
    </div>

    <!-- Panel Derecho: Digitación -->
    <div class="card" style="grid-column: span 2;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 style="font-size: 1.25rem;">Digitación de Votos</h2>
            <span class="text-muted" style="font-size: 0.875rem;" id="actaStatus">Esperando selección...</span>
        </div>

        <div id="digitacionOverlay" style="text-align: center; padding: 4rem 2rem; border: 2px dashed var(--border-color); border-radius: var(--border-radius); color: var(--text-secondary);">
            Busca y selecciona una mesa en el panel izquierdo para comenzar la digitación del acta.
        </div>

        <form id="actaForm" style="display: none;">
            <!-- Contenedor dinámico de candidatos -->
            <div id="candidatosDigitacionList" class="grid grid-cols-2" style="margin-bottom: 2rem;">
                <!-- JS inyectará los inputs aquí -->
            </div>

            <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 1.5rem 0;">

            <div class="grid grid-cols-2">
                <div class="form-group">
                    <label class="form-label text-warning">Votos en Blanco</label>
                    <input type="number" id="votosBlanco" class="form-control" min="0" value="0" required>
                </div>
                <div class="form-group">
                    <label class="form-label text-danger">Votos Nulos</label>
                    <input type="number" id="votosNulos" class="form-control" min="0" value="0" required>
                </div>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color);">
                <div>
                    <span class="text-muted">Total Votos Emitidos: </span>
                    <strong style="font-size: 1.25rem; margin-left: 0.5rem;" id="totalVotosCalc">0</strong>
                </div>
                <button type="submit" class="btn btn-success" id="btnGuardarActa">
                    💾 Registrar Acta
                </button>
            </div>
        </form>
    </div>
</div>

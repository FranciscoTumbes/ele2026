<div class="grid grid-cols-4" style="margin-bottom: 2rem;">
    <!-- Stat Cards -->
    <div class="card">
        <h3 class="text-muted" style="font-size: 0.875rem;">Mesas Procesadas</h3>
        <div style="font-size: 2rem; font-weight: 700; color: var(--accent-secondary);" id="statMesas">0 / 0</div>
        <div style="font-size: 0.875rem;" class="text-success" id="statMesasPct">0%</div>
    </div>
    <div class="card">
        <h3 class="text-muted" style="font-size: 0.875rem;">Votos Válidos</h3>
        <div style="font-size: 2rem; font-weight: 700;" id="statValidos">0</div>
        <div style="font-size: 0.875rem; color: var(--text-secondary);" id="statValidosPct">0% del total</div>
    </div>
    <div class="card">
        <h3 class="text-muted" style="font-size: 0.875rem;">Votos Blancos/Nulos</h3>
        <div style="font-size: 2rem; font-weight: 700; color: var(--accent-warning);" id="statBlancosNulos">0</div>
        <div style="font-size: 0.875rem; color: var(--text-secondary);" id="statBlancosNulosPct">0% del total</div>
    </div>
    <div class="card">
        <h3 class="text-muted" style="font-size: 0.875rem;">Ausentismo</h3>
        <div style="font-size: 2rem; font-weight: 700; color: var(--accent-danger);" id="statAusentismo">0</div>
        <div style="font-size: 0.875rem; color: var(--text-secondary);" id="statAusentismoPct">0% del padrón</div>
    </div>
</div>

<div class="grid grid-cols-3">
    <!-- Main Chart -->
    <div class="card" style="grid-column: span 2;">
        <h2 style="margin-bottom: 1.5rem; font-size: 1.25rem;">Resultados Presidenciales</h2>
        <div style="height: 400px; position: relative;">
            <canvas id="mainChart"></canvas>
            <div id="chartLoader" style="position: absolute; inset: 0; display: flex; justify-content: center; align-items: center; background: rgba(30, 33, 48, 0.5);">
                <div class="loader"></div>
            </div>
        </div>
    </div>
    
    <!-- Leaderboard -->
    <div class="card">
        <h2 style="margin-bottom: 1.5rem; font-size: 1.25rem;">Top Candidatos</h2>
        <div id="leaderboardList" style="display: flex; flex-direction: column; gap: 1rem;">
            <!-- Inyectado por JS -->
            <div style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                Cargando datos...
            </div>
        </div>
    </div>
</div>

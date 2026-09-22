/**
 * app.js - Lógica de interfaz del Sistema Electoral
 * Integración completa con la API REST
 */

document.addEventListener('DOMContentLoaded', () => {

    // ==========================================
    // UTILS
    // ==========================================
    const $ = (sel) => document.querySelector(sel);
    const $$ = (sel) => document.querySelectorAll(sel);
    const show = (el) => { if (el) el.style.display = ''; };
    const hide = (el) => { if (el) el.style.display = 'none'; };
    const showById = (id) => show(document.getElementById(id));
    const hideById = (id) => hide(document.getElementById(id));
    const setText = (id, text) => { const el = document.getElementById(id); if (el) el.textContent = text; };

    /** Muestra una notificación tipo toast */
    const showToast = (message, type = 'success') => {
        const existing = document.getElementById('appToast');
        if (existing) existing.remove();

        const toast = document.createElement('div');
        toast.id = 'appToast';
        toast.style.cssText = `
            position: fixed; top: 1.5rem; right: 1.5rem; z-index: 9999;
            padding: 1rem 1.5rem; border-radius: 10px; font-weight: 600;
            color: #fff; max-width: 400px; font-size: 0.9rem;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
            animation: slideInRight 0.3s ease;
            background: ${type === 'success' ? 'linear-gradient(135deg, #10b981, #059669)' :
                         type === 'error'   ? 'linear-gradient(135deg, #ef4444, #dc2626)' :
                                              'linear-gradient(135deg, #f59e0b, #d97706)'};
        `;
        toast.textContent = message;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.3s';
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    };

    // ==========================================
    // VARIABLES DE ESTADO
    // ==========================================
    let mesaActual = null;       // datos de la mesa seleccionada
    let candidatosActuales = []; // candidatos cargados desde la API
    const ELECCION_ID = 1;       // ID de la elección activa
    const CARGO_ID = 1;          // Gobernador Regional

    // ==========================================
    // 1. INIT
    // ==========================================
    const initApp = async () => {
        setupLogin();
        setupLogout();

        if (document.getElementById('mainChart')) {
            setupDashboard();
        }

        if (document.getElementById('searchActaForm')) {
            setupDigitacion();
        }
    };

    // ==========================================
    // 2. LOGIN
    // ==========================================
    const setupLogin = () => {
        const loginForm = document.getElementById('loginForm');
        if (!loginForm) return;

        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('btnLoginSubmit');
            const errorDiv = document.getElementById('loginError');
            const username = document.getElementById('username')?.value;
            const password = document.getElementById('password')?.value;

            btn.innerHTML = '<div class="loader" style="width:16px;height:16px;"></div> Verificando...';
            btn.disabled = true;
            hideById('loginError');

            try {
                const res = await api.login(username, password);

                showToast('¡Bienvenido al sistema!', 'success');

                setTimeout(() => {
                    window.location.href = '/ele2026/public/dashboard';
                }, 600);

            } catch (err) {
                if (errorDiv) {
                    errorDiv.textContent = err.message || 'Credenciales inválidas';
                    showById('loginError');
                }
                btn.innerHTML = 'Ingresar al Sistema';
                btn.disabled = false;
            }
        });
    };

    const setupLogout = () => {
        const btnLogout = document.getElementById('btnLogout');
        if (!btnLogout) return;

        btnLogout.addEventListener('click', async (e) => {
            e.preventDefault();
            try {
                await api.logout();
            } catch (e) { /* ignorar */ }
            window.location.href = '/ele2026/public/login';
        });
    };

    // ==========================================
    // 3. DASHBOARD
    // ==========================================
    const setupDashboard = async () => {
        try {
            // Usar totalizacion para obtener votos reales por candidato
            const res = await api.getTotalizacion();
            renderDashboardData(res.data);
        } catch (err) {
            console.warn('No se pudieron cargar resultados reales, usando datos de demostración.', err);
            renderDashboardDemo();
        }
    };

    const renderDashboardDemo = () => {
        const labels = ['APP - Garcia Mendoza', 'FPT - Torres Vasquez', 'ACT - Ramirez Soto'];
        const data = [45000, 32000, 15000];
        const colors = ['#3b82f6', '#f59e0b', '#ef4444'];
        renderChart(labels, data, colors);
        renderLeaderboard(labels, data, colors);
        setText('statMesas', '0 / 10');
        setText('statMesasPct', '0% escrutado');
        setText('statValidos', '0');
        setText('statValidosPct', 'Sin datos aún');
        setText('statBlancosNulos', '0');
        setText('statBlancosNulosPct', 'Sin datos aún');
        setText('statAusentismo', '0');
        setText('statAusentismoPct', 'Sin datos aún');
    };

    const renderDashboardData = (data) => {
        if (!data || !data.candidatos) {
            renderDashboardDemo();
            return;
        }
        
        // Actualizar estadísticas generales si están disponibles
        if (data.total_votos_validos !== undefined) {
            setText('statValidos', data.total_votos_validos.toLocaleString());
        }
        
        const labels = data.candidatos.map(c => `${c.siglas} - ${c.apellido_paterno} ${c.apellido_materno}, ${c.nombres}`);
        const votos = data.candidatos.map(c => c.total_votos || 0);
        const colors = data.candidatos.map(c => c.color_hex || '#666');
        renderChart(labels, votos, colors);
        renderLeaderboard(labels, votos, colors);
    };

    const renderChart = (labels, data, colors) => {
        const canvas = document.getElementById('mainChart');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Votos',
                    data,
                    backgroundColor: colors,
                    borderWidth: 0,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.1)' }, ticks: { color: '#949db1' } },
                    x: { grid: { display: false }, ticks: { color: '#949db1', maxRotation: 45 } }
                }
            }
        });
        hideById('chartLoader');
    };

    const renderLeaderboard = (labels, data, colors) => {
        const lbList = document.getElementById('leaderboardList');
        if (!lbList) return;

        // Ordenar por votos descendente
        const sorted = labels.map((lbl, i) => ({ lbl, votos: data[i], color: colors[i] }))
                             .sort((a, b) => b.votos - a.votos);

        lbList.innerHTML = sorted.map((item, idx) => `
            <div style="display:flex;align-items:center;justify-content:space-between;padding:1rem;background:var(--bg-base);border-radius:8px;transition:transform 0.2s;" onmouseover="this.style.transform='translateX(4px)'" onmouseout="this.style.transform=''">
                <div style="display:flex;align-items:center;gap:1rem;">
                    <div style="font-weight:bold;font-size:1.25rem;color:${item.color}">#${idx + 1}</div>
                    <div>${item.lbl}</div>
                </div>
                <div style="font-weight:700;">${item.votos.toLocaleString()} <span class="text-muted" style="font-size:0.8rem;font-weight:400;">votos</span></div>
            </div>
        `).join('');
    };

    // ==========================================
    // 4. DIGITACIÓN DE ACTAS (FLUJO PRINCIPAL)
    // ==========================================
    const setupDigitacion = () => {
        const searchForm = document.getElementById('searchActaForm');

        // --- Buscar Mesa ---
        searchForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const input = document.getElementById('mesaSearchInput');
            const numero = input.value.trim();
            const btn = document.getElementById('btnSearchMesa');

            if (!numero) return;

            btn.innerHTML = '⏳ Buscando...';
            btn.disabled = true;

            try {
                // 1) Buscar la mesa en la API
                const res = await api.buscarMesa(numero);
                mesaActual = res.data;

                // 2) Mostrar info de la mesa
                setText('infoMesaNum', mesaActual.numero_mesa);
                setText('infoCentro', mesaActual.centro_nombre);
                setText('infoDistrito', mesaActual.distrito_nombre);
                setText('infoElectores', String(mesaActual.electores_habilitados));
                showById('mesaInfoContainer');

                // 3) ¿Ya fue digitada?
                if (mesaActual.acta_ya_digitada) {
                    document.getElementById('actaStatus').innerHTML = 
                        '<span style="color: var(--warning);">⚠️</span> Acta ya digitada (' + mesaActual.acta_estado + ')';
                    hideById('actaForm');
                    showById('digitacionOverlay');
                    document.getElementById('digitacionOverlay').innerHTML = `
                        <div style="padding: 2rem;">
                            <span style="font-size: 3rem;">⚠️</span>
                            <h3 style="margin-top: 1rem; color: var(--warning);">Acta Ya Registrada</h3>
                            <p style="margin-top: 0.5rem;">Esta mesa ya tiene un acta en estado <strong>${mesaActual.acta_estado}</strong>.</p>
                            <p class="text-muted">Para modificarla, contacte al administrador del sistema.</p>
                        </div>
                    `;
                    showToast('Esta mesa ya tiene un acta registrada', 'warning');
                    return;
                }

                // 4) Cargar candidatos
                await cargarCandidatos();

                // 5) Mostrar formulario
                hideById('digitacionOverlay');
                showById('actaForm');
                document.getElementById('actaStatus').innerHTML = '<span style="color: var(--success);">●</span> Editando Acta';

                showToast(`Mesa ${mesaActual.numero_mesa} cargada correctamente`, 'success');

            } catch (err) {
                showToast(err.message || 'Mesa no encontrada', 'error');
                hideById('mesaInfoContainer');
                hideById('actaForm');
                showById('digitacionOverlay');
                document.getElementById('digitacionOverlay').innerHTML = `
                    <div style="padding: 2rem;">
                        <span style="font-size: 3rem;">❌</span>
                        <h3 style="margin-top: 1rem; color: var(--danger);">Mesa No Encontrada</h3>
                        <p class="text-muted" style="margin-top: 0.5rem;">Verifica el número de mesa e intenta nuevamente.</p>
                    </div>
                `;
            } finally {
                btn.innerHTML = '🔍 Buscar';
                btn.disabled = false;
            }
        });

        // --- Enviar Acta ---
        const actaForm = document.getElementById('actaForm');
        if (actaForm) {
            actaForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await enviarActa();
            });
        }
    };

    // ==========================================
    // 4.1 CARGAR CANDIDATOS DINÁMICAMENTE
    // ==========================================
    const cargarCandidatos = async () => {
        const list = document.getElementById('candidatosDigitacionList');
        list.innerHTML = '<div style="text-align:center;padding:2rem;grid-column:span 2;"><div class="loader"></div><p class="text-muted" style="margin-top:1rem;">Cargando candidatos...</p></div>';

        try {
            const res = await api.getCandidatos(ELECCION_ID, CARGO_ID);
            candidatosActuales = res.data || [];

            if (candidatosActuales.length === 0) {
                list.innerHTML = '<p class="text-muted" style="text-align:center;grid-column:span 2;">No se encontraron candidatos para esta elección.</p>';
                return;
            }

            list.innerHTML = candidatosActuales.map((c, i) => `
                <div class="form-group" style="background:var(--bg-base);padding:1rem;border-radius:8px;border-left:4px solid ${c.color_hex || '#666'};transition:transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform=''">
                    <label class="form-label" style="display:flex;align-items:center;gap:0.5rem;">
                        <span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:${c.color_hex || '#666'}"></span>
                        <strong>${c.siglas}</strong> - ${c.apellido_paterno} ${c.apellido_materno}, ${c.nombres}
                    </label>
                    <input type="number" 
                           class="form-control voto-cand-input" 
                           min="0" 
                           value="0" 
                           data-candidato-id="${c.id}" 
                           data-index="${i}"
                           required
                           placeholder="Votos">
                </div>
            `).join('');

            // Conectar calculadoras de votos
            attachVotoCalculators();

        } catch (err) {
            list.innerHTML = '<p style="color:var(--danger);text-align:center;grid-column:span 2;">Error al cargar candidatos: ' + (err.message || 'Error desconocido') + '</p>';
        }
    };

    // ==========================================
    // 4.2 CÁLCULO EN VIVO DE VOTOS
    // ==========================================
    const attachVotoCalculators = () => {
        const recalcular = () => {
            // Suma votos de candidatos (= votos válidos)
            let sumaValidos = 0;
            document.querySelectorAll('.voto-cand-input').forEach(input => {
                sumaValidos += parseInt(input.value || 0, 10);
            });

            const blancos = parseInt(document.getElementById('votosBlanco')?.value || 0, 10);
            const nulos = parseInt(document.getElementById('votosNulos')?.value || 0, 10);
            const totalEmitidos = sumaValidos + blancos + nulos;

            // Mostrar totales
            setText('totalVotosCalc', totalEmitidos.toString());

            // Validación visual: ¿total > electores habilitados?
            const totalEl = document.getElementById('totalVotosCalc');
            const btnGuardar = document.getElementById('btnGuardarActa');
            
            if (mesaActual && totalEmitidos > mesaActual.electores_habilitados) {
                totalEl.style.color = 'var(--danger)';
                totalEl.title = `¡Excede los ${mesaActual.electores_habilitados} electores habilitados!`;
                btnGuardar.disabled = true;
                btnGuardar.title = 'El total de votos excede los electores habilitados';
            } else if (totalEmitidos === 0) {
                totalEl.style.color = '';
                btnGuardar.disabled = true;
                btnGuardar.title = 'Ingrese al menos un voto';
            } else {
                totalEl.style.color = 'var(--success)';
                totalEl.title = '';
                btnGuardar.disabled = false;
                btnGuardar.title = '';
            }
        };

        // Escuchar todos los inputs de votos
        document.querySelectorAll('.voto-cand-input').forEach(i => i.addEventListener('input', recalcular));
        document.getElementById('votosBlanco')?.addEventListener('input', recalcular);
        document.getElementById('votosNulos')?.addEventListener('input', recalcular);

        // Calcular estado inicial
        recalcular();
    };

    // ==========================================
    // 4.3 ENVÍO DEL ACTA
    // ==========================================
    const enviarActa = async () => {
        if (!mesaActual) {
            showToast('No hay mesa seleccionada', 'error');
            return;
        }

        const btn = document.getElementById('btnGuardarActa');
        btn.innerHTML = '<div class="loader" style="width:16px;height:16px;"></div> Registrando...';
        btn.disabled = true;

        // Recopilar votos de candidatos
        const detalles = [];
        let sumaValidos = 0;
        document.querySelectorAll('.voto-cand-input').forEach(input => {
            const votos = parseInt(input.value || 0, 10);
            sumaValidos += votos;
            detalles.push({
                candidato_id: parseInt(input.dataset.candidatoId, 10),
                votos_obtenidos: votos
            });
        });

        const votosBlancos = parseInt(document.getElementById('votosBlanco')?.value || 0, 10);
        const votosNulos = parseInt(document.getElementById('votosNulos')?.value || 0, 10);
        const totalVotantes = sumaValidos + votosBlancos + votosNulos;

        // Validación de consistencia (duplica la del backend como capa extra)
        if (totalVotantes > mesaActual.electores_habilitados) {
            showToast(`Error: El total de votos (${totalVotantes}) supera los electores habilitados (${mesaActual.electores_habilitados})`, 'error');
            btn.innerHTML = '💾 Registrar Acta';
            btn.disabled = false;
            return;
        }

        if (totalVotantes === 0) {
            showToast('Error: Debe ingresar al menos un voto', 'error');
            btn.innerHTML = '💾 Registrar Acta';
            btn.disabled = false;
            return;
        }

        // Armar payload
        const payload = {
            mesa_id: mesaActual.id,
            eleccion_id: ELECCION_ID,
            electores_habilitados: mesaActual.electores_habilitados,
            votos_validos: sumaValidos,
            votos_blancos: votosBlancos,
            votos_nulos: votosNulos,
            votos_impugnados: 0,
            total_votantes: totalVotantes,
            observaciones: null,
            detalles: detalles
        };

        try {
            const res = await api.registrarActa(payload);
            showToast(`✅ Acta registrada exitosamente (ID: ${res.data?.acta_id})`, 'success');

            // Bloquear formulario tras éxito
            document.getElementById('actaStatus').innerHTML = 
                '<span style="color: var(--success);">✅</span> Acta Registrada';
            
            document.querySelectorAll('.voto-cand-input, #votosBlanco, #votosNulos').forEach(i => i.disabled = true);
            btn.innerHTML = '✅ Acta Guardada';
            btn.disabled = true;

            // Marcar la mesa como ya digitada
            mesaActual.acta_ya_digitada = true;

        } catch (err) {
            showToast(err.message || 'Error al registrar el acta', 'error');
            btn.innerHTML = '💾 Registrar Acta';
            btn.disabled = false;
        }
    };

    // ==========================================
    // CSS para animación de toast
    // ==========================================
    if (!document.getElementById('toastStyles')) {
        const style = document.createElement('style');
        style.id = 'toastStyles';
        style.textContent = `
            @keyframes slideInRight {
                from { transform: translateX(100%); opacity: 0; }
                to   { transform: translateX(0);    opacity: 1; }
            }
        `;
        document.head.appendChild(style);
    }

    // ==========================================
    // RUN
    // ==========================================
    initApp();
});

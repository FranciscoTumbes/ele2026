/**
 * app.js - Lógica de interfaz del Sistema Electoral
 */

document.addEventListener('DOMContentLoaded', () => {
    
    // --- Utils ---
    const showElement = (id) => { const el = document.getElementById(id); if (el) el.style.display = 'block'; };
    const hideElement = (id) => { const el = document.getElementById(id); if (el) el.style.display = 'none'; };
    
    // --- 1. Init App (Auth Check & Setup) ---
    const initApp = async () => {
        // En un entorno real validaríamos con api.me()
        // Aquí si estamos en dashboard o digitacion, asignamos info del usuario
        const userNameDisplay = document.getElementById('userNameDisplay');
        if (userNameDisplay) {
            try {
                // const user = await api.me();
                // userNameDisplay.textContent = user.data.nombre;
            } catch (e) {
                // Not authenticated handled by api.js
            }
        }
        
        setupLogout();
        setupLogin();
        
        if (document.getElementById('mainChart')) {
            setupDashboard();
        }
        
        if (document.getElementById('searchActaForm')) {
            setupDigitacion();
        }
    };

    // --- 2. Login ---
    const setupLogin = () => {
        const loginForm = document.getElementById('loginForm');
        if (!loginForm) return;

        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('btnLoginSubmit');
            const errorDiv = document.getElementById('loginError');
            
            btn.innerHTML = '<div class="loader" style="width: 16px; height: 16px;"></div> Verificando...';
            btn.disabled = true;
            hideElement('loginError');

            try {
                // Simulación de delay para UI (en prod usar api.login(...))
                await new Promise(r => setTimeout(r, 800));
                
                // Redirigir si success
                window.location.href = '/ele2026/public/dashboard';
            } catch (err) {
                errorDiv.textContent = err.message || 'Credenciales inválidas';
                showElement('loginError');
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
                // await api.logout();
                api.setToken(null);
                window.location.href = '/ele2026/public/login';
            } catch (err) {
                console.error(err);
            }
        });
    };

    // --- 3. Dashboard ---
    const setupDashboard = async () => {
        // Simulando datos para Chart.js ya que /resultados/resumen podría no estar devolviendo info estructurada para gráficos aún
        const mockData = {
            labels: ['Partido A (Juan Pérez)', 'Partido B (Ana Gómez)', 'Partido C (Carlos Ruiz)'],
            data: [45000, 32000, 15000],
            colors: ['#3b82f6', '#f59e0b', '#ef4444']
        };

        // Render Chart
        const ctx = document.getElementById('mainChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: mockData.labels,
                datasets: [{
                    label: 'Votos',
                    data: mockData.data,
                    backgroundColor: mockData.colors,
                    borderWidth: 0,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(255, 255, 255, 0.1)' },
                        ticks: { color: '#949db1' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#949db1' }
                    }
                }
            }
        });
        
        hideElement('chartLoader');

        // Leaderboard
        const lbList = document.getElementById('leaderboardList');
        lbList.innerHTML = mockData.labels.map((lbl, idx) => `
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 1rem; background: var(--bg-base); border-radius: 8px;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="font-weight: bold; font-size: 1.25rem; color: ${mockData.colors[idx]}">#${idx + 1}</div>
                    <div>${lbl}</div>
                </div>
                <div style="font-weight: 700;">${mockData.data[idx].toLocaleString()} <span class="text-muted" style="font-size: 0.8rem; font-weight: 400;">votos</span></div>
            </div>
        `).join('');

        // Stats update
        document.getElementById('statMesas').textContent = '1,245 / 8,000';
        document.getElementById('statMesasPct').textContent = '15.5% escrutado';
        document.getElementById('statValidos').textContent = '92,000';
        document.getElementById('statValidosPct').textContent = '91.2% del total emitido';
        document.getElementById('statBlancosNulos').textContent = '8,800';
        document.getElementById('statBlancosNulosPct').textContent = '8.8% del total emitido';
        document.getElementById('statAusentismo').textContent = '45,000';
        document.getElementById('statAusentismoPct').textContent = '30.8% del padrón electoral';
    };

    // --- 4. Digitación ---
    const setupDigitacion = () => {
        const searchForm = document.getElementById('searchActaForm');
        
        searchForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const mesaId = document.getElementById('mesaSearchInput').value;
            
            // Simulación
            document.getElementById('infoMesaNum').textContent = mesaId;
            document.getElementById('infoCentro').textContent = 'I.E. 2045 SAN JUAN';
            document.getElementById('infoDistrito').textContent = 'LIMA / LIMA / COMAS';
            document.getElementById('infoElectores').textContent = '300';
            
            showElement('mesaInfoContainer');
            hideElement('digitacionOverlay');
            showElement('actaForm');
            document.getElementById('actaStatus').innerHTML = '<span class="text-success">●</span> Editando Acta';

            // Inyectar Candidatos simulados
            const list = document.getElementById('candidatosDigitacionList');
            const cands = ['Partido A', 'Partido B', 'Partido C'];
            list.innerHTML = cands.map((c, i) => `
                <div class="form-group" style="background: var(--bg-base); padding: 1rem; border-radius: 8px;">
                    <label class="form-label">${c}</label>
                    <input type="number" class="form-control voto-cand-input" min="0" value="0" data-cand="${i}" required>
                </div>
            `).join('');

            attachVotoCalculators();
        });

        document.getElementById('actaForm')?.addEventListener('submit', (e) => {
            e.preventDefault();
            const btn = document.getElementById('btnGuardarActa');
            btn.innerHTML = '<div class="loader" style="width: 16px; height: 16px;"></div> Registrando...';
            btn.disabled = true;

            setTimeout(() => {
                alert('¡Acta registrada exitosamente!');
                window.location.reload();
            }, 1000);
        });
    };

    const attachVotoCalculators = () => {
        const inputs = document.querySelectorAll('.voto-cand-input, #votosBlanco, #votosNulos');
        const calcTotal = () => {
            let total = 0;
            inputs.forEach(i => total += parseInt(i.value || 0, 10));
            document.getElementById('totalVotosCalc').textContent = total;
        };

        inputs.forEach(i => i.addEventListener('input', calcTotal));
    };

    // Run
    initApp();
});

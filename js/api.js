/**
 * api.js - Cliente centralizado para la API REST del Sistema Electoral
 */
const API_BASE_URL = '/api';

class ApiClient {
    async request(endpoint, options = {}) {
        const url = `${API_BASE_URL}${endpoint}`;
        const headers = {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        };

        const config = {
            ...options,
            credentials: 'same-origin',
            headers: {
                ...headers,
                ...options.headers
            }
        };

        if (config.body && typeof config.body === 'object') {
            config.body = JSON.stringify(config.body);
        }

        try {
            const response = await fetch(url, config);
            const data = await response.json();

            if (!response.ok || !data.success) {
                if (response.status === 401) {
                    window.location.href = '/login';
                }
                throw data;
            }
            return data;
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    }

    // ==========================================
    // AUTH
    // ==========================================

    login(username, password) {
        return this.request('/auth/login', {
            method: 'POST',
            body: { username, password }
        });
    }

    logout() {
        return this.request('/auth/logout', { method: 'POST' });
    }

    me() {
        return this.request('/auth/me');
    }

    // ==========================================
    // MESAS
    // ==========================================

    /**
     * Busca una mesa por su número.
     * Retorna info geográfica + estado del acta.
     */
    buscarMesa(numero) {
        return this.request(`/mesas/buscar?numero=${encodeURIComponent(numero)}`);
    }

    // ==========================================
    // CANDIDATOS
    // ==========================================

    /**
     * Lista candidatos filtrados por elección y cargo.
     * @param {number} eleccionId
     * @param {number} cargoId
     */
    getCandidatos(eleccionId = 1, cargoId = 1) {
        return this.request(`/candidatos?eleccion_id=${eleccionId}&cargo_id=${cargoId}`);
    }

    // ==========================================
    // ACTAS
    // ==========================================

    /**
     * Registra un acta con cabecera y detalle de votos por candidato.
     * @param {object} payload - { mesa_id, eleccion_id, electores_habilitados, votos_validos, votos_blancos, votos_nulos, total_votantes, detalles: [{candidato_id, votos_obtenidos}] }
     */
    registrarActa(payload) {
        return this.request('/actas', {
            method: 'POST',
            body: payload
        });
    }

    // ==========================================
    // RESULTADOS
    // ==========================================

    getResumenResultados() {
        return this.request('/resultados/resumen');
    }

    getTotalizacion(eleccionId = 1, cargoId = 1) {
        return this.request(`/resultados/totalizacion?eleccion_id=${eleccionId}&cargo_id=${cargoId}`);
    }

    getAvance() {
        return this.request('/resultados/avance');
    }
}

// Instancia global
window.api = new ApiClient();

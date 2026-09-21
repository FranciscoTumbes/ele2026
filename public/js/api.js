/**
 * api.js - Cliente centralizado para la API REST del Sistema Electoral
 */
const API_BASE_URL = '/ele2026/public/api';

class ApiClient {
    constructor() {
        this.token = localStorage.getItem('ele2026_token');
    }

    setToken(token) {
        this.token = token;
        if (token) {
            localStorage.setItem('ele2026_token', token);
        } else {
            localStorage.removeItem('ele2026_token');
        }
    }

    async request(endpoint, options = {}) {
        const url = `${API_BASE_URL}${endpoint}`;
        const headers = {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        };

        if (this.token) {
            // Note: Normally Authorization: Bearer {token}, but we assume session or token based on backend
            // In Ele2026, AuthController might just use $_SESSION, but we send it anyway if needed.
            headers['Authorization'] = `Bearer ${this.token}`;
        }

        const config = {
            ...options,
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
                // If 401 Unauthorized, handle logout redirect
                if (response.status === 401) {
                    this.setToken(null);
                    window.location.href = '/ele2026/public/login';
                }
                throw data;
            }
            return data;
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    }

    // --- Endpoints ---

    login(username, password) {
        return this.request('/auth/login', {
            method: 'POST',
            body: { username, password } // Adjust based on AuthController payload
        });
    }

    logout() {
        return this.request('/auth/logout', { method: 'POST' });
    }

    me() {
        return this.request('/auth/me');
    }

    getResumenResultados() {
        return this.request('/resultados/resumen');
    }

    getCandidatos() {
        return this.request('/candidatos');
    }

    buscarMesa(idMesa) {
        // En ele2026, puede que la busqueda sea por /mesas?id=... o similar
        // Simularemos o ajustaremos al endpoint correcto
        return this.request(`/mesas?id=${idMesa}`);
    }

    registrarActa(payload) {
        return this.request('/actas', {
            method: 'POST',
            body: payload
        });
    }
}

// Global instance
window.api = new ApiClient();

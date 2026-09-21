<div style="display: flex; justify-content: center; align-items: center; min-height: 100vh; background: url('https://images.unsplash.com/photo-1555849894-f446411516fb?auto=format&fit=crop&q=80') center/cover; position: relative;">
    <div style="position: absolute; inset: 0; background: rgba(15, 17, 26, 0.85);"></div>
    
    <div class="card glass-panel" style="width: 100%; max-width: 420px; z-index: 10; padding: 2.5rem; text-align: center;">
        <div style="font-size: 2rem; font-weight: 700; margin-bottom: 2rem;">
            <span style="color: var(--accent-secondary);">ELE</span>2026
            <div style="font-size: 1rem; color: var(--text-secondary); font-weight: 400; margin-top: 0.5rem;">Sistema Electoral Nacional</div>
        </div>

        <form id="loginForm" style="text-align: left;">
            <div id="loginError" class="text-danger" style="margin-bottom: 1rem; text-align: center; display: none;">
                Credenciales inválidas
            </div>
            
            <div class="form-group">
                <label class="form-label">DNI u Operador ID</label>
                <input type="text" id="username" class="form-control" required placeholder="Ingresa tu documento">
            </div>
            
            <div class="form-group">
                <label class="form-label">Contraseña</label>
                <input type="password" id="password" class="form-control" required placeholder="••••••••">
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;" id="btnLoginSubmit">
                Ingresar al Sistema
            </button>
        </form>
    </div>
</div>

const CONFIG = {
    baseURL: window.location.origin + '/programacion-web/proyecto_integrador/',
    apiURL: window.location.origin + '/programacion-web/proyecto_integrador/api/'
};

const API = {
    async get(endpoint) {
        try {
            const response = await fetch(CONFIG.apiURL + endpoint, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            return await response.json();
        } catch (error) {
            console.error('Error en GET:', error);
            return { exito: false, mensaje: 'Error de conexión' };
        }
    },
    
    async post(endpoint, data) {
        try {
            const response = await fetch(CONFIG.apiURL + endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });
            return await response.json();
        } catch (error) {
            console.error('Error en POST:', error);
            return { exito: false, mensaje: 'Error de conexión' };
        }
    },
    
    async put(endpoint, data) {
        try {
            const response = await fetch(CONFIG.apiURL + endpoint, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });
            return await response.json();
        } catch (error) {
            console.error('Error en PUT:', error);
            return { exito: false, mensaje: 'Error de conexión' };
        }
    },
    
    async delete(endpoint) {
        try {
            const response = await fetch(CONFIG.apiURL + endpoint, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            return await response.json();
        } catch (error) {
            console.error('Error en DELETE:', error);
            return { exito: false, mensaje: 'Error de conexión' };
        }
    }
};

const Notificacion = {
    mostrar(mensaje, tipo = 'info') {
        const toast = document.createElement('div');
        toast.className = `alert alert-${tipo} alert-dismissible fade show position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        toast.innerHTML = `
            ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 5000);
    },
    
    exito(mensaje) {
        this.mostrar(mensaje, 'success');
    },
    
    error(mensaje) {
        this.mostrar(mensaje, 'danger');
    },
    
    advertencia(mensaje) {
        this.mostrar(mensaje, 'warning');
    },
    
    info(mensaje) {
        this.mostrar(mensaje, 'info');
    }
};

const Formato = {
    fecha(fechaStr, incluirHora = false) {
        const fecha = new Date(fechaStr);
        const opciones = {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        };
        
        if (incluirHora) {
            opciones.hour = '2-digit';
            opciones.minute = '2-digit';
        }
        
        return fecha.toLocaleString('es-MX', opciones);
    },
    
    calificacion(numero) {
        return parseFloat(numero).toFixed(2);
    },
    
    claseCalificacion(numero) {
        if (numero >= 90) return 'excelente';
        if (numero >= 80) return 'bien';
        if (numero >= 70) return 'regular';
        return 'reprobado';
    },
    
    diasRestantes(fechaLimite) {
        const ahora = new Date();
        const limite = new Date(fechaLimite);
        const diferencia = limite - ahora;
        const dias = Math.ceil(diferencia / (1000 * 60 * 60 * 24));
        
        if (dias < 0) return 'Vencida';
        if (dias === 0) return 'Hoy';
        if (dias === 1) return 'Mañana';
        return `${dias} días`;
    }
};

function confirmar(mensaje) {
    return new Promise((resolve) => {
        if (confirm(mensaje)) {
            resolve(true);
        } else {
            resolve(false);
        }
    });
}

const Loader = {
    mostrar() {
        if (document.getElementById('globalLoader')) return;
        
        const loader = document.createElement('div');
        loader.id = 'globalLoader';
        loader.innerHTML = `
            <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
                        background: rgba(0,0,0,0.5); z-index: 99999; 
                        display: flex; align-items: center; justify-content: center;">
                <div class="spinner-border text-light" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </div>
        `;
        document.body.appendChild(loader);
    },
    
    ocultar() {
        const loader = document.getElementById('globalLoader');
        if (loader) {
            loader.remove();
        }
    }
};

document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

window.API = API;
window.Notificacion = Notificacion;
window.Formato = Formato;
window.Loader = Loader;
window.confirmar = confirmar;
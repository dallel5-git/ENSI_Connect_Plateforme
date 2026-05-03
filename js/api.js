// Configuration API
const API_URL = 'http://localhost/ensi_website/backend/api';

// Classe API client
class API {
    
    // Headers avec token
    static getHeaders(isJSON = true) {
        const headers = {};
        if (isJSON) headers['Content-Type'] = 'application/json';
        return headers;
    }
    
    // Méthode générique
    static async request(endpoint, options = {}) {
        let responseText = '';
        try {
            const response = await fetch(`${API_URL}${endpoint}`, {
                ...options,
                headers: { ...this.getHeaders(!options.isFormData), ...options.headers }
            });
            
            responseText = await response.text();
            
            try {
                return JSON.parse(responseText);
            } catch (e) {
                console.error('Erreur de parsing JSON:', e);
                console.error('Réponse brute du serveur:', responseText);
                return { success: false, message: 'Erreur du serveur (réponse non valide)' };
            }
        } catch (error) {
            console.error('Erreur réseau API:', error);
            return { success: false, message: 'Impossible de contacter le serveur' };
        }
    }
    
    // Auth
    static login(email, password, role) {
        return this.request('/auth/login.php', {
            method: 'POST',
            body: JSON.stringify({ email, password, role })
        });
    }
    
    static register(userData) {
        return this.request('/auth/register.php', {
            method: 'POST',
            body: JSON.stringify(userData)
        });
    }
    
    static verifyToken() {
        return this.request('/auth/verify.php');
    }
    
    // Professeurs
    static getProfessors(filters = {}) {
        const params = new URLSearchParams(filters).toString();
        return this.request(`/professors/list.php?${params}`);
    }
    
    static addProfessor(data) {
        return this.request('/professors/add.php', {
            method: 'POST',
            body: JSON.stringify(data)
        });
    }
    
    static deleteProfessor(id) {
        return this.request(`/professors/delete.php?id=${id}`, {
            method: 'DELETE'
        });
    }
    
    // Étudiants
    static getStudents(filters = {}) {
        const params = new URLSearchParams(filters).toString();
        return this.request(`/students/list.php?${params}`);
    }
    
    // Ressources
    static getResources(filters = {}) {
        const params = new URLSearchParams(filters).toString();
        return this.request(`/resources/list.php?${params}`);
    }
    
    static uploadResource(formData) {
        return this.request('/resources/upload.php', {
            method: 'POST',
            body: formData,
            isFormData: true,
            headers: {} // Pas de Content-Type pour FormData
        });
    }
    
    static deleteResource(id) {
        return this.request(`/resources/delete.php?id=${id}`, {
            method: 'DELETE'
        });
    }
    
    static getDownloadUrl(id) {
        return `${API_URL}/resources/download.php?id=${id}`;
    }
    
    static getSubjects() {
        return this.request('/subjects/list.php');
    }
    
    // Contact
    static sendContact(data) {
        return this.request('/contact/send.php', {
            method: 'POST',
            body: JSON.stringify(data)
        });
    }
    
    // Stats
    static getStats() {
        return this.request('/stats/dashboard.php');
    }
}

// Helpers d'authentification
const Auth = {
    isLoggedIn() {
        return !!localStorage.getItem('user');
    },
    
    getUser() {
        const user = localStorage.getItem('user');
        return user ? JSON.parse(user) : null;
    },
    
    getRole() {
        const user = this.getUser();
        return user ? user.role : null;
    },
    
    login(user) {
        localStorage.setItem('user', JSON.stringify(user));
    },
    
    logout() {
        localStorage.removeItem('user');
        window.location.href = 'login.html';
    },
    
    requireAuth() {
        if (!this.isLoggedIn()) {
            window.location.href = 'login.html';
        }
    },
    
    requireRole(roles) {
        this.requireAuth();
        const userRole = this.getRole();
        if (!roles.includes(userRole)) {
            alert('Accès refusé');
            window.location.href = 'index.html';
        }
    }
};



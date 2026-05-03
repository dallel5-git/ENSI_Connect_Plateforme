let allResources = [];

async function loadResources(filters = {}) {
    const result = await API.getResources(filters);
    
    if (result.success) {
        allResources = result.data.resources;
        renderResources(allResources);
    } else {
        document.getElementById('resourcesTable').innerHTML = 
            `<tr><td colspan="6" style="text-align:center;color:var(--danger);">❌ ${result.message}</td></tr>`;
    }
}

const icons = {
    cours: { icon: 'fa-book', color: '#1db6a3', label: '📖 Cours' },
    td: { icon: 'fa-pen', color: '#10b981', label: '📝 TD' },
    tp: { icon: 'fa-laptop-code', color: '#f39c12', label: '💻 TP' },
    devoir: { icon: 'fa-file-alt', color: '#ef4444', label: '📚 Devoir' }
};

function renderResources(list) {
    const tbody = document.getElementById('resourcesTable');
    if (!tbody) return;
    
    if (list.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;padding:2rem;">Aucune ressource trouvée</td></tr>`;
        return;
    }
    
    const user = Auth.getUser();
    
    tbody.innerHTML = list.map(r => {
        const canDelete = user && (user.role === 'admin' || user.id == r.professor_id);
        return `
            <tr>
                <td><i class="fas ${icons[r.type].icon}" style="color:${icons[r.type].color};"></i> <strong>${r.title}</strong></td>
                <td>${r.subject_name}</td>
                <td><span class="badge" style="background:${icons[r.type].color}22;color:${icons[r.type].color};">${icons[r.type].label}</span></td>
                <td>${r.professor_name}</td>
                <td>${new Date(r.created_at).toLocaleDateString('fr-FR')}</td>
                <td style="display:flex;gap:0.5rem;">
                    <a href="${API.getDownloadUrl(r.id)}" class="btn btn-primary" style="padding:0.5rem 1rem;font-size:0.875rem;" download>
                        <i class="fas fa-download"></i>
                    </a>
                    ${canDelete ? `<button onclick="deleteResource(${r.id})" class="btn" style="padding:0.5rem 1rem;font-size:0.875rem;background:var(--danger);color:white;"><i class="fas fa-trash"></i></button>` : ''}
                </td>
            </tr>
        `;
    }).join('');
}

// Gestion de l'upload
function toggleUploadModal(show) {
    const modal = document.getElementById('uploadModal');
    modal.style.display = show ? 'flex' : 'none';
    if (show) loadSubjects();
}

async function loadSubjects() {
    const select = document.getElementById('resSubject');
    if (!select || select.options.length > 1) return;
    
    const result = await API.getSubjects();
    if (result.success) {
        select.innerHTML = '<option value="">Choisir une matière...</option>' + 
            result.data.subjects.map(s => `<option value="${s.id}">${s.name} (${s.code})</option>`).join('');
    }
}

document.getElementById('uploadForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const user = Auth.getUser();
    if (!user) return alert('Veuillez vous connecter');

    const formData = new FormData(e.target);
    formData.append('professor_id', user.id); // On ajoute l'ID du prof manuellement (mode simple)

    const btn = e.target.querySelector('button');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi...';

    const result = await API.uploadResource(formData);
    
    if (result.success) {
        alert('✅ Fichier ajouté avec succès !');
        toggleUploadModal(false);
        e.target.reset();
        loadResources();
    } else {
        alert('❌ Erreur : ' + result.message);
    }
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-cloud-upload-alt"></i> Envoyer le fichier';
});

// Vérifier si on affiche le bouton d'ajout (Toujours visible pour le projet)
function checkPermissions() {
    document.getElementById('uploadAction').style.display = 'block';
}

async function deleteResource(id) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cette ressource ?')) return;
    const result = await API.deleteResource(id);
    if (result.success) loadResources();
}

// Initialisation
loadResources();
checkPermissions();

// Filtres et Recherche
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
        document.querySelectorAll('.tab-btn').forEach(b => {
            b.classList.remove('active', 'btn-primary');
            b.classList.add('btn-outline');
        });
        e.target.classList.remove('btn-outline');
        e.target.classList.add('active', 'btn-primary');
        loadResources(e.target.dataset.tab === 'all' ? {} : { type: e.target.dataset.tab });
    });
});

let searchTimeout;
document.getElementById('searchRes')?.addEventListener('input', (e) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => loadResources({ search: e.target.value }), 300);
});
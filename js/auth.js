console.log('✅ auth.js chargé');

document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ DOM prêt');
    
    const form = document.getElementById('loginForm');
    
    if (!form) {
        console.error('❌ Formulaire loginForm introuvable !');
        return;
    }
    
    console.log('✅ Formulaire trouvé');
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        console.log('🔐 Tentative de connexion...');
        
        const role = document.getElementById('role').value;
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const alertBox = document.getElementById('alertBox');
        
        console.log('📝 Données:', { email, role, password: '***' });
        
        // Vérification des champs
        if (!role || !email || !password) {
            alertBox.className = 'alert alert-danger';
            alertBox.innerHTML = '❌ Veuillez remplir tous les champs';
            alertBox.style.display = 'block';
            return;
        }
        
        // Désactiver le bouton
        const btn = form.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Connexion...';
        
        try {
            const result = await API.login(email, password, role);
            console.log('📥 Résultat:', result);
            
            if (result.success) {
                Auth.login(result.data.user);
                
                alertBox.className = 'alert alert-success';
                alertBox.innerHTML = `✅ Bienvenue ${result.data.user.fullname} ! Redirection...`;
                alertBox.style.display = 'block';
                
                setTimeout(() => {
                    const userRole = result.data.user.role;
                    if (userRole === 'admin') {
                        window.location.href = 'index.html';
                    } else if (userRole === 'professor') {
                        window.location.href = 'resources.html';
                    } else {
                        window.location.href = 'students.html';
                    }
                }, 1500);
            } else {
                alertBox.className = 'alert alert-danger';
                alertBox.innerHTML = '❌ ' + result.message;
                alertBox.style.display = 'block';
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        } catch (error) {
            console.error('❌ Erreur:', error);
            alertBox.className = 'alert alert-danger';
            alertBox.innerHTML = '❌ Erreur : ' + error.message;
            alertBox.style.display = 'block';
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });
});
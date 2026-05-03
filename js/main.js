// Menu mobile
function toggleMenu() {
    document.getElementById('navMenu').classList.toggle('active');
}



// Observer pour déclencher animations
const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            if (entry.target.classList.contains('stats')) animateCounters();
        }
    });
}, { threshold: 0.3 });

document.querySelectorAll('.stats').forEach(el => observer.observe(el));

// Active link
document.querySelectorAll('.nav-menu a').forEach(link => {
    if (link.href === window.location.href) link.classList.add('active');
});
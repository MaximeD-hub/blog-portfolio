const filterButtons = document.querySelectorAll('.filter-btn');
const articles = document.querySelectorAll('.article-card');

filterButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        // Récupération de la catégorie du bouton cliqué
        const category = btn.dataset.category;

        // Affichage/masquage des articles selon la catégorie
        articles.forEach(article => {
            article.style.display =
                category === 'all' || article.dataset.category === category
                    ? 'block'   // Afficher si catégorie correspond
                    : 'none';   // Masquer sinon
        });

        // Mise à jour du bouton actif
        filterButtons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
    });
});

const commentForm = document.querySelector('#comment-form');

if (commentForm) { // Vérification : formulaire présent uniquement sur article.php
    commentForm.addEventListener('submit', (e) => {
        const name = commentForm.querySelector('#author_name').value.trim();
        const message = commentForm.querySelector('#content').value.trim();

        // Blocage de l'envoi si champs vides
        if (!name || !message) {
            e.preventDefault();
            alert('Veuillez remplir tous les champs.');
        }
    });
}
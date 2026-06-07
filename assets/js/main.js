// Filtrage des articles par catégorie
const filterButtons = document.querySelectorAll('.filter-btn');
const articles = document.querySelectorAll('.article-card');

filterButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        const category = btn.dataset.category;

        articles.forEach(article => {
            article.style.display =
                category === 'all' || article.dataset.category === category
                    ? 'block'
                    : 'none';
        });

        filterButtons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
    });
});
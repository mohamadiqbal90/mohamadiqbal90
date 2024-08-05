document.addEventListener('DOMContentLoaded', () => {
    const subjectCards = document.querySelectorAll('.subject-card');
    const contentSections = document.querySelectorAll('.content');

    subjectCards.forEach(card => {
        card.addEventListener('click', () => {
            // Hide all content sections
            contentSections.forEach(section => {
                section.classList.remove('active');
            });

            // Show the clicked content section
            const subject = card.getAttribute('data-subject');
            const targetSection = document.getElementById(subject);
            if (targetSection) {
                targetSection.classList.add('active');
            }
        });
    });
});

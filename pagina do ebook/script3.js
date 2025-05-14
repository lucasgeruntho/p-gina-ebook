

document.querySelectorAll('.faq-list .faq-toggle').forEach(toggle => {
  toggle.addEventListener('click', () => {
    const question = toggle.parentElement;
    question.classList.toggle('active');
  });
});

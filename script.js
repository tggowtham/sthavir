window.addEventListener('scroll', () => {
  document.querySelectorAll('section').forEach((section) => {
    const rect = section.getBoundingClientRect();
    if (rect.top < window.innerHeight && rect.bottom >= 0) {
      section.style.opacity = '1';
      section.style.transform = 'translateY(0)';
    }
  });
});

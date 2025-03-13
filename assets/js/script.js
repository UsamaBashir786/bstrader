    // Mobile menu toggle
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');

    mobileMenuButton.addEventListener('click', () => {
      mobileMenu.classList.toggle('hidden');
    });

    // Close mobile menu when clicking outside
    document.addEventListener('click', (event) => {
      if (!mobileMenuButton.contains(event.target) && !mobileMenu.contains(event.target)) {
        mobileMenu.classList.add('hidden');
      }
    });

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        e.preventDefault();

        const targetId = this.getAttribute('href');
        if (targetId === '#') return;

        const targetElement = document.querySelector(targetId);
        if (targetElement) {
          targetElement.scrollIntoView({
            behavior: 'smooth'
          });

          // Close mobile menu after clicking a link
          mobileMenu.classList.add('hidden');
        }
      });
    });
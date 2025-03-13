<style>
  /* Custom color variables - adjust these to match your brand colors */
  :root {
    --primary-600: #1565C0;
    --primary-700: #0D47A1;
    --primary-900: #0A2472;
  }

  .text-primary-600 {
    color: var(--primary-600);
  }

  .text-primary-700 {
    color: var(--primary-700);
  }

  .text-primary-900 {
    color: var(--primary-900);
  }

  .bg-primary-600 {
    background-color: var(--primary-600);
  }

  .bg-primary-700 {
    background-color: var(--primary-700);
  }

  .hover\:text-primary-600:hover {
    color: var(--primary-600);
  }

  .hover\:bg-primary-700:hover {
    background-color: var(--primary-700);
  }

  /* Smooth transition for mobile menu */
  #mobile-menu {
    transition: all 0.3s ease-in-out;
    max-height: 0;
    overflow: hidden;
  }

  #mobile-menu.active {
    max-height: 380px;
  }

  /* Responsive adjustments */
  @media (max-width: 640px) {
    .logo-text {
      font-size: 1.5rem;
      /* Smaller font on very small screens */
    }

    .action-buttons {
      flex-direction: column;
      align-items: stretch;
      gap: 0.5rem;
    }

    .action-buttons a {
      text-align: center;
    }
  }
</style>

<!-- Header/Navigation - Fixed Top and Responsive -->
<header class="bg-white shadow-md fixed top-0 left-0 right-0 z-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center h-16 sm:h-20">
      <!-- Logo area -->
      <div class="flex items-center">
        <div class="flex-shrink-0 flex items-center">
          <span class="text-2xl sm:text-3xl font-bold text-primary-700 logo-text">BS Traders</span>
        </div>
        <!-- Desktop Navigation -->
        <nav class="hidden md:ml-10 md:flex md:space-x-4 lg:space-x-8">
          <a href="#" class="text-primary-900 font-medium hover:text-primary-600 px-3 py-2 rounded-md">Home</a>
          <a href="#products" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md">Products</a>
          <a href="#services" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md">Services</a>
          <a href="#about" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md">About Us</a>
          <a href="#contact" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md">Contact</a>
        </nav>
      </div>

      <!-- Action buttons and mobile menu toggle -->
      <div class="flex items-center action-buttons">
        <div class="hidden sm:flex sm:items-center sm:space-x-4">
          <a href="login.php" class="text-gray-700 hover:text-primary-600 font-medium whitespace-nowrap">
            <i class="fas fa-sign-in-alt mr-1"></i> Login
          </a>
          <a href="register.php" class="bg-primary-600 text-white px-4 py-2 rounded-md font-medium hover:bg-primary-700 shadow-sm whitespace-nowrap">
            Register
          </a>
        </div>

        <!-- No mobile buttons here anymore as they will be in the mobile menu -->

        <!-- Mobile menu button -->
        <div class="ml-4 md:hidden">
          <button id="mobile-menu-button" class="text-gray-500 hover:text-gray-900 focus:outline-none p-2">
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" id="menu-icon-open" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" id="menu-icon-close" class="hidden" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile menu -->
    <div id="mobile-menu" class="md:hidden overflow-hidden">
      <div class="flex flex-col space-y-2 pb-6 pt-2">
        <a href="#" class="text-primary-900 font-medium hover:bg-gray-100 px-3 py-2 rounded-md">Home</a>
        <a href="#products" class="text-gray-700 hover:bg-gray-100 px-3 py-2 rounded-md">Products</a>
        <a href="#services" class="text-gray-700 hover:bg-gray-100 px-3 py-2 rounded-md">Services</a>
        <a href="#about" class="text-gray-700 hover:bg-gray-100 px-3 py-2 rounded-md">About Us</a>
        <a href="#contact" class="text-gray-700 hover:bg-gray-100 px-3 py-2 rounded-md">Contact</a>

        <!-- Login and Register buttons moved here for mobile -->
        <div class="flex flex-col space-y-2 pt-4 border-t border-gray-200 mt-2">
          <a href="login.php" class="text-primary-700 hover:bg-gray-100 px-3 py-2 rounded-md font-medium flex items-center">
            <i class="fas fa-sign-in-alt mr-2"></i> Login
          </a>
          <a href="register.php" class="bg-primary-600 text-white hover:bg-primary-700 px-3 py-2 rounded-md font-medium mx-3 text-center shadow-sm">
            Register
          </a>
        </div>
      </div>
    </div>
  </div>
</header>

<!-- Add padding to body or main content to account for fixed header -->
<div class="pt-16 sm:pt-20">
  <!-- Your page content goes here -->
</div>

<!-- JavaScript for mobile menu toggle with improved functionality -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    const menuIconOpen = document.getElementById('menu-icon-open');
    const menuIconClose = document.getElementById('menu-icon-close');

    // Toggle mobile menu and menu icons
    mobileMenuButton.addEventListener('click', function() {
      mobileMenu.classList.toggle('active');

      // Toggle visibility of menu icons
      menuIconOpen.classList.toggle('hidden');
      menuIconClose.classList.toggle('hidden');

      if (mobileMenu.classList.contains('active')) {
        mobileMenu.classList.remove('hidden');
      } else {
        // Use setTimeout to delay adding 'hidden' class until after transition completes
        setTimeout(() => {
          if (!mobileMenu.classList.contains('active')) {
            mobileMenu.classList.add('hidden');
          }
        }, 300); // Match transition duration
      }
    });

    // Close mobile menu when clicking on a menu item
    const mobileMenuItems = mobileMenu.querySelectorAll('a');
    mobileMenuItems.forEach(item => {
      item.addEventListener('click', function() {
        mobileMenu.classList.remove('active');
        menuIconOpen.classList.remove('hidden');
        menuIconClose.classList.add('hidden');

        // Use setTimeout to delay adding 'hidden' class
        setTimeout(() => {
          if (!mobileMenu.classList.contains('active')) {
            mobileMenu.classList.add('hidden');
          }
        }, 300);
      });
    });

    // Close mobile menu when resizing to desktop
    window.addEventListener('resize', function() {
      if (window.innerWidth >= 768) { // 768px is the md breakpoint in Tailwind
        mobileMenu.classList.remove('active');
        mobileMenu.classList.add('hidden');
        menuIconOpen.classList.remove('hidden');
        menuIconClose.classList.add('hidden');
      }
    });
  });
</script>
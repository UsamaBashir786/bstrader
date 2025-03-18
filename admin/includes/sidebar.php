<aside class="hidden md:flex md:flex-shrink-0">
  <div class="flex flex-col w-64">
    <div class="flex flex-col flex-grow pt-5 overflow-y-auto bg-primary-700 border-r">
      <div class="flex items-center flex-shrink-0 px-4">
        <span class="text-2xl font-bold text-white">BS Traders</span>
      </div>
      <div class="mt-5 flex-grow flex flex-col">
        <nav class="flex-1 px-2 space-y-1 bg-primary-700">
          <a href="index.php" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
            <i class="fas fa-home mr-3 h-6 w-6"></i>
            Dashboard
          </a>
          <a href="employee.php" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
            <i class="fas fa-users mr-3 h-6 w-6"></i>
            Employee Management
          </a>
          <a href="user.php" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
            <i class="fas fa-user-shield mr-3 h-6 w-6"></i>
            User Management
          </a>
          <a href="salary.php" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
            <i class="fas fa-money-bill-wave mr-3 h-6 w-6"></i>
            Salary Management
          </a>
          <a href="expense.php" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
            <i class="fas fa-file-invoice-dollar mr-3 h-6 w-6"></i>
            Expense Management
          </a>
          <a href="task.php" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-white bg-primary-800">
            <i class="fas fa-tasks mr-3 h-6 w-6"></i>
            Task Management
          </a>
          <a href="reports.php" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
            <i class="fas fa-chart-bar mr-3 h-6 w-6"></i>
            Reports
          </a>
          <a href="settings.php" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
            <i class="fas fa-cog mr-3 h-6 w-6"></i>
            Settings
          </a>
        </nav>
      </div>
      <div class="flex-shrink-0 flex border-t border-primary-800 p-4">
        <a href="profile.php" class="flex-shrink-0 group block">
          <div class="flex items-center">
            <div>
              <svg class="inline-block h-10 w-10 rounded-full" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200">
                <!-- Background Circle -->
                <circle cx="100" cy="100" r="90" fill="#3f51b5" />

                <!-- Admin Icon - Stylized "A" with shield/dashboard elements -->
                <path d="M100 30 L150 100 L130 140 H70 L50 100 Z" fill="none" stroke="white" stroke-width="6" stroke-linejoin="round" />

                <!-- Horizontal bars - representing dashboard/admin panel -->
                <line x1="70" y1="80" x2="130" y2="80" stroke="white" stroke-width="6" stroke-linecap="round" />
                <line x1="80" y1="100" x2="120" y2="100" stroke="white" stroke-width="6" stroke-linecap="round" />
                <line x1="90" y1="120" x2="110" y2="120" stroke="white" stroke-width="6" stroke-linecap="round" />

                <!-- Crown element suggesting admin authority -->
                <path d="M70 60 L85 45 L100 60 L115 45 L130 60" fill="none" stroke="white" stroke-width="6" stroke-linecap="round" stroke-linejoin="round" />

                <!-- Outer ring for polish -->
                <circle cx="100" cy="100" r="90" fill="none" stroke="white" stroke-width="2" opacity="0.3" />
                <circle cx="100" cy="100" r="85" fill="none" stroke="white" stroke-width="1" opacity="0.2" />
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-base font-medium text-white">Admin User</p>
              <p class="text-sm font-medium text-primary-200 group-hover:text-primary-100">View profile</p>
            </div>
          </div>
        </a>
      </div>
    </div>
  </div>
</aside>
  <!-- JavaScript for interactivity -->
  <script>
    // User dropdown toggle
    const userMenuButton = document.getElementById('user-menu-button');
    const userDropdown = document.getElementById('user-dropdown');

    userMenuButton.addEventListener('click', () => {
      userDropdown.classList.toggle('hidden');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', (event) => {
      if (!userMenuButton.contains(event.target) && !userDropdown.contains(event.target)) {
        userDropdown.classList.add('hidden');
      }
    });

    // Mobile sidebar toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mobileSidebar = document.getElementById('mobile-sidebar');
    const closeSidebar = document.getElementById('closeSidebar');

    sidebarToggle.addEventListener('click', () => {
      mobileSidebar.classList.remove('hidden');
    });

    closeSidebar.addEventListener('click', () => {
      mobileSidebar.classList.add('hidden');
    });
  </script>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BS Traders - Building Supplies and Construction Materials</title>
  <link rel="stylesheet" href="src/output.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: {
              50: '#e6f1ff',
              100: '#cce3ff',
              200: '#99c7ff',
              300: '#66aaff',
              400: '#338eff',
              500: '#0072ff',
              600: '#005bcc',
              700: '#004499',
              800: '#002e66',
              900: '#001733',
            }
          }
        }
      }
    }
  </script>

</head>

<body class="bg-gray-50">
  <!-- header -->
  <?php include 'includes/navbar.php' ?>
  <!-- Hero Section -->
  <section class="bg-gradient-to-r from-blue-600 to-blue-900 text-white py-20">
    <div class="max-w-7xl mx-auto px-6 lg:px-8 flex flex-col md:flex-row items-center gap-12">
      <div class="text-center md:text-left max-w-2xl">
        <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold leading-tight">
          High-Quality Building Materials for Every Project
        </h1>
        <p class="mt-6 text-lg sm:text-xl">
          From durable construction materials to reliable hardware, we provide top-quality products for professionals and homeowners alike.
        </p>
        <div class="mt-8 flex flex-col sm:flex-row sm:space-x-4 space-y-4 sm:space-y-0">
          <a href="#products" class="px-6 py-3 text-lg font-semibold bg-white text-blue-700 rounded-lg shadow-md transition hover:bg-gray-100">
            Explore Products
          </a>
          <a href="#contact" class="px-6 py-3 text-lg font-semibold border-2 border-white rounded-lg transition hover:bg-white hover:text-blue-700">
            Contact Us
          </a>
        </div>
      </div>
      <div class="w-full md:w-1/2">

      </div>
    </div>
  </section>

  <!-- Features Section -->
  <section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center">
        <h2 class="text-3xl font-bold text-gray-900">Why Choose BS Traders?</h2>
        <p class="mt-4 text-lg text-gray-600 max-w-3xl mx-auto">
          We provide comprehensive solutions for all your construction and building material needs.
        </p>
      </div>

      <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Feature 1 -->
        <div class="bg-gray-50 rounded-lg p-8 shadow-sm hover:shadow-md transition-shadow">
          <div class="h-12 w-12 rounded-md bg-primary-100 text-primary-700 flex items-center justify-center mb-5">
            <i class="fas fa-medal text-2xl"></i>
          </div>
          <h3 class="text-xl font-bold text-gray-900 mb-3">Premium Quality</h3>
          <p class="text-gray-600">
            We source the highest quality materials from trusted manufacturers and suppliers across the country.
          </p>
        </div>

        <!-- Feature 2 -->
        <div class="bg-gray-50 rounded-lg p-8 shadow-sm hover:shadow-md transition-shadow">
          <div class="h-12 w-12 rounded-md bg-primary-100 text-primary-700 flex items-center justify-center mb-5">
            <i class="fas fa-truck-fast text-2xl"></i>
          </div>
          <h3 class="text-xl font-bold text-gray-900 mb-3">Fast Delivery</h3>
          <p class="text-gray-600">
            Our efficient logistics network ensures timely delivery of your orders to any location in the country.
          </p>
        </div>

        <!-- Feature 3 -->
        <div class="bg-gray-50 rounded-lg p-8 shadow-sm hover:shadow-md transition-shadow">
          <div class="h-12 w-12 rounded-md bg-primary-100 text-primary-700 flex items-center justify-center mb-5">
            <i class="fas fa-headset text-2xl"></i>
          </div>
          <h3 class="text-xl font-bold text-gray-900 mb-3">Expert Support</h3>
          <p class="text-gray-600">
            Our team of experienced professionals is always ready to provide guidance and advice for your projects.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- Product Categories Section -->
  <section id="products" class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center">
        <h2 class="text-3xl font-bold text-gray-900">Our Product Categories</h2>
        <p class="mt-4 text-lg text-gray-600 max-w-3xl mx-auto">
          Browse through our extensive range of construction and building materials.
        </p>
      </div>

      <div class="mt-16 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
        <!-- Category 1 -->
        <div class="bg-white rounded-lg shadow overflow-hidden group">
          <div class="aspect-w-4 aspect-h-3 bg-gray-200">
            <img src="images/steel-category.jpg" alt="Steel Products" class="object-cover h-60 w-full group-hover:scale-105 transition-transform">
          </div>
          <div class="p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-2">Steel Products</h3>
            <p class="text-gray-600 mb-4">Steel bars, sheets, pipes, and more for structural applications.</p>
            <a href="#" class="text-primary-600 font-medium hover:text-primary-700 flex items-center">
              View Products <i class="fas fa-arrow-right ml-2"></i>
            </a>
          </div>
        </div>

        <!-- Category 2 -->
        <div class="bg-white rounded-lg shadow overflow-hidden group">
          <div class="aspect-w-4 aspect-h-3 bg-gray-200">
            <img src="images/cement-category.jpg" alt="Cement & Concrete" class="object-cover h-60 w-full group-hover:scale-105 transition-transform">
          </div>
          <div class="p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-2">Cement & Concrete</h3>
            <p class="text-gray-600 mb-4">High-quality cement products for all your construction needs.</p>
            <a href="#" class="text-primary-600 font-medium hover:text-primary-700 flex items-center">
              View Products <i class="fas fa-arrow-right ml-2"></i>
            </a>
          </div>
        </div>

        <!-- Category 3 -->
        <div class="bg-white rounded-lg shadow overflow-hidden group">
          <div class="aspect-w-4 aspect-h-3 bg-gray-200">
            <img src="images/electrical-category.jpg" alt="Electrical Supplies" class="object-cover h-60 w-full group-hover:scale-105 transition-transform">
          </div>
          <div class="p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-2">Electrical Supplies</h3>
            <p class="text-gray-600 mb-4">Wiring, switches, fixtures, and electrical components.</p>
            <a href="#" class="text-primary-600 font-medium hover:text-primary-700 flex items-center">
              View Products <i class="fas fa-arrow-right ml-2"></i>
            </a>
          </div>
        </div>

        <!-- Category 4 -->
        <div class="bg-white rounded-lg shadow overflow-hidden group">
          <div class="aspect-w-4 aspect-h-3 bg-gray-200">
            <img src="images/paint-category.jpg" alt="Paints & Finishes" class="object-cover h-60 w-full group-hover:scale-105 transition-transform">
          </div>
          <div class="p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-2">Paints & Finishes</h3>
            <p class="text-gray-600 mb-4">Interior and exterior paints, primers, and surface finishes.</p>
            <a href="#" class="text-primary-600 font-medium hover:text-primary-700 flex items-center">
              View Products <i class="fas fa-arrow-right ml-2"></i>
            </a>
          </div>
        </div>
      </div>

      <div class="mt-12 text-center">
        <a href="#" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700">
          View All Categories
        </a>
      </div>
    </div>
  </section>

  <!-- Services Section -->
  <section id="services" class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center">
        <h2 class="text-3xl font-bold text-gray-900">Our Services</h2>
        <p class="mt-4 text-lg text-gray-600 max-w-3xl mx-auto">
          We offer a range of services to support your construction and building needs.
        </p>
      </div>

      <div class="mt-16 grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Service 1 -->
        <div class="bg-gray-50 rounded-lg p-8 border border-gray-100 flex">
          <div class="flex-shrink-0 mr-6">
            <div class="h-14 w-14 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center">
              <i class="fas fa-truck-container text-2xl"></i>
            </div>
          </div>
          <div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">Bulk Ordering & Delivery</h3>
            <p class="text-gray-600">
              We specialize in bulk orders for commercial projects with reliable delivery schedules. Our fleet of vehicles ensures your materials arrive on time, every time.
            </p>
          </div>
        </div>

        <!-- Service 2 -->
        <div class="bg-gray-50 rounded-lg p-8 border border-gray-100 flex">
          <div class="flex-shrink-0 mr-6">
            <div class="h-14 w-14 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center">
              <i class="fas fa-clipboard-list text-2xl"></i>
            </div>
          </div>
          <div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">Project Estimation</h3>
            <p class="text-gray-600">
              Our experts can help you estimate material quantities and costs for your projects. We provide detailed quotes and help you optimize your budget.
            </p>
          </div>
        </div>

        <!-- Service 3 -->
        <div class="bg-gray-50 rounded-lg p-8 border border-gray-100 flex">
          <div class="flex-shrink-0 mr-6">
            <div class="h-14 w-14 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center">
              <i class="fas fa-handshake text-2xl"></i>
            </div>
          </div>
          <div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">Contractor Partnerships</h3>
            <p class="text-gray-600">
              We offer special rates and dedicated support for contractors and construction companies. Join our partner program for exclusive benefits.
            </p>
          </div>
        </div>

        <!-- Service 4 -->
        <div class="bg-gray-50 rounded-lg p-8 border border-gray-100 flex">
          <div class="flex-shrink-0 mr-6">
            <div class="h-14 w-14 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center">
              <i class="fas fa-drafting-compass text-2xl"></i>
            </div>
          </div>
          <div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">Technical Consultation</h3>
            <p class="text-gray-600">
              Our technical team provides consultation on material selection, application methods, and compliance with building codes and standards.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Testimonials Section -->
  <section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center">
        <h2 class="text-3xl font-bold text-gray-900">What Our Customers Say</h2>
        <p class="mt-4 text-lg text-gray-600 max-w-3xl mx-auto">
          Hear from contractors and builders who trust BS Traders for their material needs.
        </p>
      </div>

      <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Testimonial 1 -->
        <div class="bg-white rounded-lg p-8 shadow-sm">
          <div class="flex items-center mb-4">
            <div class="text-yellow-400 flex">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
            </div>
          </div>
          <blockquote class="text-gray-600 mb-4">
            "BS Traders has been our go-to supplier for all construction materials for the past 3 years. Their quality is consistently excellent, and their delivery is always on time."
          </blockquote>
          <div class="flex items-center">
            <svg alt="Customer" class="h-12 w-12 rounded-full object-cover" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 300">
              <!-- Background -->
              <rect width="200" height="300" fill="#f5f7fa" rx="10" ry="10" />

              <!-- Person Silhouette -->
              <g>
                <!-- Head/Face -->
                <circle cx="100" cy="70" r="40" fill="#ffccbc" />

                <!-- Hair -->
                <path d="M70,50 Q100,25 130,50 L130,70 Q100,85 70,70 Z" fill="#5d4037" />

                <!-- Eyes -->
                <ellipse cx="85" cy="65" rx="5" ry="3" fill="#424242" />
                <ellipse cx="115" cy="65" rx="5" ry="3" fill="#424242" />

                <!-- Eyebrows -->
                <path d="M80,58 Q85,55 90,58" fill="none" stroke="#5d4037" stroke-width="2" stroke-linecap="round" />
                <path d="M110,58 Q115,55 120,58" fill="none" stroke="#5d4037" stroke-width="2" stroke-linecap="round" />

                <!-- Nose -->
                <path d="M100,70 L103,78 L97,78 Z" fill="#ffab91" />

                <!-- Mouth -->
                <path d="M90,85 Q100,90 110,85" fill="none" stroke="#d32f2f" stroke-width="2" stroke-linecap="round" />

                <!-- Body - Business Suit -->
                <path d="M70,110 L60,250 L140,250 L130,110 Z" fill="#263238" />

                <!-- Shirt Collar -->
                <path d="M85,110 L100,140 L115,110 Z" fill="white" />

                <!-- Tie -->
                <path d="M100,140 L95,170 L100,200 L105,170 Z" fill="#c62828" />

                <!-- Arms -->
                <path d="M70,110 L40,180" fill="none" stroke="#263238" stroke-width="18" stroke-linecap="round" />
                <path d="M130,110 L160,180" fill="none" stroke="#263238" stroke-width="18" stroke-linecap="round" />

                <!-- Hands -->
                <circle cx="40" cy="180" r="10" fill="#ffccbc" />
                <circle cx="160" cy="180" r="10" fill="#ffccbc" />

                <!-- Legs -->
                <rect x="70" y="250" width="20" height="50" fill="#455a64" />
                <rect x="110" y="250" width="20" height="50" fill="#455a64" />
              </g>

              <!-- Bottom Tag -->
              <rect x="40" y="260" width="120" height="30" fill="#1565c0" rx="5" ry="5" />
              <text x="100" y="280" font-family="Arial" font-size="12" fill="white" text-anchor="middle" font-weight="bold">CONTRACTOR</text>
            </svg>
            <div class="ml-4">
              <h4 class="text-lg font-bold text-gray-900">Imran Ahmed</h4>
              <p class="text-gray-500">Sky Construction, CEO</p>
            </div>
          </div>
        </div>

        <!-- Testimonial 2 -->
        <div class="bg-white rounded-lg p-8 shadow-sm">
          <div class="flex items-center mb-4">
            <div class="text-yellow-400 flex">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
            </div>
          </div>
          <blockquote class="text-gray-600 mb-4">
            "The technical support team at BS Traders helped us select the right materials for our challenging commercial project. Their expertise saved us time and money."
          </blockquote>
          <div class="flex items-center">
            <svg class="h-12 w-12 rounded-full object-cover" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 300">
              <!-- Background -->
              <rect width="200" height="300" fill="#f5f7fa" rx="10" ry="10" />

              <!-- Person Silhouette -->
              <g>
                <!-- Head/Face -->
                <circle cx="100" cy="70" r="40" fill="#ffe0b2" />

                <!-- Hair -->
                <path d="M60,70 Q60,30 100,30 Q140,30 140,70 Q130,75 120,70 L120,55 Q100,45 80,55 L80,70 Q70,75 60,70 Z" fill="#8d6e63" />

                <!-- Eyes -->
                <ellipse cx="85" cy="65" rx="5" ry="3" fill="#424242" />
                <ellipse cx="115" cy="65" rx="5" ry="3" fill="#424242" />

                <!-- Glasses -->
                <circle cx="85" cy="65" r="10" fill="none" stroke="#424242" stroke-width="2" />
                <circle cx="115" cy="65" r="10" fill="none" stroke="#424242" stroke-width="2" />
                <line x1="95" y1="65" x2="105" y2="65" stroke="#424242" stroke-width="2" />
                <line x1="75" y1="60" x2="65" y2="55" stroke="#424242" stroke-width="2" />
                <line x1="125" y1="60" x2="135" y2="55" stroke="#424242" stroke-width="2" />

                <!-- Eyebrows -->
                <path d="M80,53 Q85,50 90,53" fill="none" stroke="#8d6e63" stroke-width="2" stroke-linecap="round" />
                <path d="M110,53 Q115,50 120,53" fill="none" stroke="#8d6e63" stroke-width="2" stroke-linecap="round" />

                <!-- Nose -->
                <path d="M100,70 L103,78 L97,78 Z" fill="#ffcc80" />

                <!-- Mouth -->
                <path d="M90,85 Q100,90 110,85" fill="none" stroke="#f57c00" stroke-width="2" stroke-linecap="round" />

                <!-- Beard Stubble -->
                <path d="M85,90 Q100,100 115,90" fill="none" stroke="#8d6e63" stroke-width="1" stroke-linecap="round" stroke-dasharray="1,2" />

                <!-- Body - Professional Attire -->
                <path d="M70,110 L60,250 L140,250 L130,110 Z" fill="#01579b" />

                <!-- Shirt Collar -->
                <path d="M85,110 L100,125 L115,110 Z" fill="white" />

                <!-- Pencil in Pocket -->
                <rect x="120" y="130" width="3" height="20" fill="#ffd54f" transform="rotate(10, 120, 130)" />
                <polygon points="121,130 123,130 122,125" fill="#e53935" transform="rotate(10, 120, 130)" />

                <!-- Arms -->
                <path d="M70,110 L40,180" fill="none" stroke="#01579b" stroke-width="18" stroke-linecap="round" />
                <path d="M130,110 L160,180" fill="none" stroke="#01579b" stroke-width="18" stroke-linecap="round" />

                <!-- Hands -->
                <circle cx="40" cy="180" r="10" fill="#ffe0b2" />
                <circle cx="160" cy="180" r="10" fill="#ffe0b2" />

                <!-- Blueprint Roll -->
                <rect x="150" y="170" width="20" height="10" fill="#bbdefb" rx="5" ry="5" transform="rotate(-45, 160, 180)" />
                <rect x="150" y="175" width="20" height="1" fill="#1565c0" transform="rotate(-45, 160, 180)" />
                <rect x="150" y="177" width="20" height="1" fill="#1565c0" transform="rotate(-45, 160, 180)" />

                <!-- Legs -->
                <rect x="70" y="250" width="20" height="50" fill="#37474f" />
                <rect x="110" y="250" width="20" height="50" fill="#37474f" />
              </g>

              <!-- Bottom Tag -->
              <rect x="40" y="260" width="120" height="30" fill="#1565c0" rx="5" ry="5" />
              <text x="100" y="280" font-family="Arial" font-size="12" fill="white" text-anchor="middle" font-weight="bold">ARCHITECT</text>
            </svg>
            <div class="ml-4">
              <h4 class="text-lg font-bold text-gray-900">Fatima Khalid</h4>
              <p class="text-gray-500">Modern Builders, Project Manager</p>
            </div>
          </div>
        </div>

        <!-- Testimonial 3 -->
        <div class="bg-white rounded-lg p-8 shadow-sm">
          <div class="flex items-center mb-4">
            <div class="text-yellow-400 flex">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star-half-alt"></i>
            </div>
          </div>
          <blockquote class="text-gray-600 mb-4">
            "As a small contractor, I appreciate BS Traders' willingness to accommodate custom orders and provide flexible payment terms. Their customer service is outstanding."
          </blockquote>
          <div class="flex items-center">
            <svg class="h-12 w-12 rounded-full object-cover" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 300">
              <!-- Background -->
              <rect width="200" height="300" fill="#f5f7fa" rx="10" ry="10" />

              <!-- Person Silhouette -->
              <g>
                <!-- Head/Face -->
                <circle cx="100" cy="70" r="40" fill="#ffdbac" />

                <!-- Hair -->
                <path d="M70,50 Q70,30 100,30 Q130,30 130,50 Q130,60 120,65 L120,45 Q100,35 80,45 L80,65 Q70,60 70,50 Z" fill="#a1887f" />

                <!-- Eyes -->
                <ellipse cx="85" cy="65" rx="5" ry="3" fill="#424242" />
                <ellipse cx="115" cy="65" rx="5" ry="3" fill="#424242" />

                <!-- Eyebrows -->
                <path d="M80,58 Q85,55 90,58" fill="none" stroke="#8d6e63" stroke-width="2" stroke-linecap="round" />
                <path d="M110,58 Q115,55 120,58" fill="none" stroke="#8d6e63" stroke-width="2" stroke-linecap="round" />

                <!-- Nose -->
                <path d="M100,70 L103,78 L97,78 Z" fill="#ffcc80" />

                <!-- Mouth -->
                <path d="M90,85 Q100,92 110,85" fill="none" stroke="#e65100" stroke-width="2" stroke-linecap="round" />

                <!-- Body - Casual Attire -->
                <rect x="70" y="110" width="60" height="90" fill="#4caf50" />

                <!-- Tool Belt -->
                <rect x="65" y="190" width="70" height="15" fill="#795548" rx="2" ry="2" />
                <circle cx="100" cy="197" r="5" fill="#ffca28" />
                <rect x="75" y="192" width="10" height="5" fill="#8d6e63" />
                <rect x="115" y="192" width="10" height="5" fill="#8d6e63" />

                <!-- Arms -->
                <path d="M70,120 L40,180" fill="none" stroke="#4caf50" stroke-width="18" stroke-linecap="round" />
                <path d="M130,120 L160,180" fill="none" stroke="#4caf50" stroke-width="18" stroke-linecap="round" />

                <!-- Hands -->
                <circle cx="40" cy="180" r="10" fill="#ffdbac" />
                <circle cx="160" cy="180" r="10" fill="#ffdbac" />

                <!-- Hammer in Hand -->
                <rect x="25" y="165" width="5" height="30" fill="#5d4037" rx="2" ry="2" transform="rotate(-45, 40, 180)" />
                <path d="M20,160 L35,145 L45,155 L30,170 Z" fill="#90a4ae" transform="rotate(-45, 40, 180)" />

                <!-- Legs -->
                <rect x="75" y="205" width="20" height="95" fill="#1976d2" />
                <rect x="105" y="205" width="20" height="95" fill="#1976d2" />

                <!-- Safety Hat -->
                <path d="M60,40 Q100,20 140,40 L130,55 Q100,40 70,55 Z" fill="#ffe082" />
                <path d="M75,54 Q100,46 125,54" fill="none" stroke="#ffa000" stroke-width="2" />
                <ellipse cx="100" cy="35" rx="15" ry="5" fill="#ffa000" />
              </g>

              <!-- Bottom Tag -->
              <rect x="40" y="260" width="120" height="30" fill="#1565c0" rx="5" ry="5" />
              <text x="100" y="280" font-family="Arial" font-size="12" fill="white" text-anchor="middle" font-weight="bold">HOMEOWNER</text>
            </svg>
            <div class="ml-4">
              <h4 class="text-lg font-bold text-gray-900">Zain Malik</h4>
              <p class="text-gray-500">ZM Contractors, Owner</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Call To Action Section -->
  <section class="py-20 bg-primary-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
      <h2 class="text-3xl font-bold text-white mb-6">Ready to Start Your Project?</h2>
      <p class="text-xl text-primary-100 max-w-3xl mx-auto mb-10">
        Create an account today and get access to our full catalog, exclusive pricing, and personalized service.
      </p>
      <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-6">
        <a href="register.php" class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-primary-700 bg-white hover:bg-gray-50 shadow-lg">
          Register Now
        </a>
        <a href="#contact" class="inline-flex items-center justify-center px-8 py-3 border-2 border-white text-base font-medium rounded-md text-white hover:bg-primary-600">
          Contact Us
        </a>
      </div>
    </div>
  </section>

  <!-- About Section -->
  <section id="about" class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        <div>
          <h2 class="text-3xl font-bold text-gray-900 mb-6">About BS Traders</h2>
          <p class="text-lg text-gray-600 mb-6">
            Founded in 2010, BS Traders has grown to become one of the leading suppliers of construction materials and building supplies in Pakistan. We serve contractors, builders, architects, and homeowners with premium products and exceptional service.
          </p>
          <p class="text-lg text-gray-600 mb-6">
            Our mission is to provide quality building materials that meet the highest standards of durability, efficiency, and sustainability. We work with trusted manufacturers and suppliers to ensure our customers receive only the best products.
          </p>
          <p class="text-lg text-gray-600">
            With a team of experienced professionals and a robust distribution network, we are equipped to handle projects of any size and complexity across the country.
          </p>
          <div class="mt-8">
            <a href="#" class="text-primary-600 font-medium hover:text-primary-700 flex items-center">
              Learn More About Our Story <i class="fas fa-arrow-right ml-2"></i>
            </a>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 400">
            <!-- Background -->
            <rect width="500" height="400" fill="#f5f5f5" />

            <!-- Warehouse Building -->
            <g>
              <!-- Main Structure -->
              <rect x="50" y="100" width="400" height="230" fill="#e0e0e0" stroke="#bdbdbd" stroke-width="2" />

              <!-- Roof -->
              <polygon points="50,100 250,50 450,100" fill="#90a4ae" stroke="#78909c" stroke-width="2" />

              <!-- Front Wall Details -->
              <rect x="80" y="150" width="120" height="180" fill="#d0d0d0" stroke="#bdbdbd" stroke-width="1" />
              <rect x="230" y="150" width="120" height="180" fill="#d0d0d0" stroke="#bdbdbd" stroke-width="1" />

              <!-- Entrance -->
              <rect x="350" y="200" width="80" height="130" fill="#42a5f5" stroke="#1e88e5" stroke-width="2" />
              <rect x="370" y="210" width="40" height="120" fill="#bbdefb" stroke="#90caf9" stroke-width="1" />

              <!-- Company Sign -->
              <rect x="200" y="80" width="100" height="40" fill="#1565c0" rx="5" ry="5" />
              <text x="250" y="105" font-family="Arial" font-size="16" fill="white" text-anchor="middle" font-weight="bold">BS TRADERS</text>

              <!-- Windows -->
              <g>
                <rect x="100" y="180" width="80" height="40" fill="#bbdefb" stroke="#90caf9" stroke-width="1" />
                <rect x="100" y="240" width="80" height="40" fill="#bbdefb" stroke="#90caf9" stroke-width="1" />
                <rect x="250" y="180" width="80" height="40" fill="#bbdefb" stroke="#90caf9" stroke-width="1" />
                <rect x="250" y="240" width="80" height="40" fill="#bbdefb" stroke="#90caf9" stroke-width="1" />
              </g>
            </g>

            <!-- Foreground Elements -->
            <g>
              <!-- Parking Area -->
              <rect x="50" y="330" width="400" height="50" fill="#9e9e9e" stroke="#757575" stroke-width="1" />
              <g stroke="white" stroke-width="2" stroke-dasharray="10,5">
                <line x1="100" y1="355" x2="130" y2="355" />
                <line x1="160" y1="355" x2="190" y2="355" />
                <line x1="220" y1="355" x2="250" y2="355" />
                <line x1="280" y1="355" x2="310" y2="355" />
                <line x1="340" y1="355" x2="370" y2="355" />
              </g>

              <!-- Car Silhouettes -->
              <rect x="70" y="335" width="40" height="20" fill="#424242" rx="5" ry="5" />
              <rect x="230" y="335" width="40" height="20" fill="#616161" rx="5" ry="5" />
              <rect x="390" y="335" width="40" height="20" fill="#424242" rx="5" ry="5" />
            </g>

            <!-- Trees and Landscape -->
            <g>
              <circle cx="40" cy="330" r="15" fill="#66bb6a" />
              <circle cx="460" cy="330" r="15" fill="#66bb6a" />
              <circle cx="30" cy="325" r="12" fill="#66bb6a" />
              <circle cx="470" cy="325" r="12" fill="#66bb6a" />

              <rect x="39" y="330" width="2" height="20" fill="#5d4037" />
              <rect x="459" y="330" width="2" height="20" fill="#5d4037" />
              <rect x="29" y="325" width="2" height="20" fill="#5d4037" />
              <rect x="469" y="325" width="2" height="20" fill="#5d4037" />
            </g>

            <!-- Delivery Truck -->
            <g transform="translate(150, 310) scale(0.8)">
              <rect x="0" y="0" width="80" height="30" fill="#1565c0" rx="5" ry="5" />
              <rect x="80" y="10" width="40" height="20" fill="#1565c0" />
              <circle cx="20" cy="30" r="7" fill="#424242" stroke="#212121" stroke-width="1" />
              <circle cx="100" cy="30" r="7" fill="#424242" stroke="#212121" stroke-width="1" />
              <rect x="5" y="15" width="15" height="8" fill="#bbdefb" />
              <text x="40" y="20" font-family="Arial" font-size="8" fill="white" text-anchor="middle" font-weight="bold">BS</text>
            </g>
          </svg>
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 400">
            <!-- Background -->
            <rect width="500" height="400" fill="#f9f9f9" />

            <!-- Material Showcase Area -->
            <g>
              <!-- Shelving Units -->
              <g>
                <!-- Left Shelf -->
                <rect x="50" y="80" width="180" height="280" fill="#e0e0e0" stroke="#bdbdbd" stroke-width="2" />

                <!-- Shelf Dividers -->
                <line x1="50" y1="150" x2="230" y2="150" stroke="#bdbdbd" stroke-width="2" />
                <line x1="50" y1="220" x2="230" y2="220" stroke="#bdbdbd" stroke-width="2" />
                <line x1="50" y1="290" x2="230" y2="290" stroke="#bdbdbd" stroke-width="2" />

                <!-- Right Shelf -->
                <rect x="270" y="80" width="180" height="280" fill="#e0e0e0" stroke="#bdbdbd" stroke-width="2" />

                <!-- Shelf Dividers -->
                <line x1="270" y1="150" x2="450" y2="150" stroke="#bdbdbd" stroke-width="2" />
                <line x1="270" y1="220" x2="450" y2="220" stroke="#bdbdbd" stroke-width="2" />
                <line x1="270" y1="290" x2="450" y2="290" stroke="#bdbdbd" stroke-width="2" />
              </g>

              <!-- Materials on Shelves -->
              <g>
                <!-- Cement Bags -->
                <g transform="translate(70, 100)">
                  <rect width="50" height="35" fill="#b0bec5" rx="2" ry="2" />
                  <rect x="10" y="10" width="30" height="15" fill="#90a4ae" rx="2" ry="2" />
                  <text x="25" y="21" font-family="Arial" font-size="8" fill="white" text-anchor="middle">CEMENT</text>
                </g>

                <g transform="translate(130, 100)">
                  <rect width="50" height="35" fill="#b0bec5" rx="2" ry="2" />
                  <rect x="10" y="10" width="30" height="15" fill="#90a4ae" rx="2" ry="2" />
                  <text x="25" y="21" font-family="Arial" font-size="8" fill="white" text-anchor="middle">CEMENT</text>
                </g>

                <!-- Paint Buckets -->
                <g transform="translate(290, 100)">
                  <circle cx="25" cy="20" r="18" fill="#42a5f5" stroke="#1e88e5" stroke-width="2" />
                  <rect x="20" y="0" width="10" height="5" fill="#1e88e5" />
                  <text x="25" y="24" font-family="Arial" font-size="8" fill="white" text-anchor="middle">PAINT</text>
                </g>

                <g transform="translate(350, 100)">
                  <circle cx="25" cy="20" r="18" fill="#ef5350" stroke="#e53935" stroke-width="2" />
                  <rect x="20" y="0" width="10" height="5" fill="#e53935" />
                  <text x="25" y="24" font-family="Arial" font-size="8" fill="white" text-anchor="middle">PAINT</text>
                </g>

                <g transform="translate(410, 100)">
                  <circle cx="25" cy="20" r="18" fill="#66bb6a" stroke="#43a047" stroke-width="2" />
                  <rect x="20" y="0" width="10" height="5" fill="#43a047" />
                  <text x="25" y="24" font-family="Arial" font-size="8" fill="white" text-anchor="middle">PAINT</text>
                </g>

                <!-- Brick Stacks -->
                <g transform="translate(70, 160)">
                  <g>
                    <rect width="15" height="8" fill="#d84315" />
                    <rect x="15" width="15" height="8" fill="#e64a19" />
                    <rect x="30" width="15" height="8" fill="#d84315" />
                    <rect y="8" width="15" height="8" fill="#e64a19" />
                    <rect x="15" y="8" width="15" height="8" fill="#d84315" />
                    <rect x="30" y="8" width="15" height="8" fill="#e64a19" />
                    <rect y="16" width="15" height="8" fill="#d84315" />
                    <rect x="15" y="16" width="15" height="8" fill="#e64a19" />
                    <rect x="30" y="16" width="15" height="8" fill="#d84315" />
                    <rect y="24" width="15" height="8" fill="#e64a19" />
                    <rect x="15" y="24" width="15" height="8" fill="#d84315" />
                    <rect x="30" y="24" width="15" height="8" fill="#e64a19" />
                  </g>
                </g>

                <g transform="translate(130, 160)">
                  <g>
                    <rect width="15" height="8" fill="#d84315" />
                    <rect x="15" width="15" height="8" fill="#e64a19" />
                    <rect x="30" width="15" height="8" fill="#d84315" />
                    <rect y="8" width="15" height="8" fill="#e64a19" />
                    <rect x="15" y="8" width="15" height="8" fill="#d84315" />
                    <rect x="30" y="8" width="15" height="8" fill="#e64a19" />
                    <rect y="16" width="15" height="8" fill="#d84315" />
                    <rect x="15" y="16" width="15" height="8" fill="#e64a19" />
                    <rect x="30" y="16" width="15" height="8" fill="#d84315" />
                    <rect y="24" width="15" height="8" fill="#e64a19" />
                    <rect x="15" y="24" width="15" height="8" fill="#d84315" />
                    <rect x="30" y="24" width="15" height="8" fill="#e64a19" />
                  </g>
                </g>

                <!-- Pipes -->
                <g transform="translate(290, 170)">
                  <rect width="50" height="10" fill="#90a4ae" rx="5" ry="5" />
                  <rect width="50" height="5" fill="#b0bec5" rx="2.5" ry="2.5" />
                </g>

                <g transform="translate(290, 185)">
                  <rect width="50" height="10" fill="#90a4ae" rx="5" ry="5" />
                  <rect width="50" height="5" fill="#b0bec5" rx="2.5" ry="2.5" />
                </g>

                <g transform="translate(380, 170)">
                  <rect width="50" height="15" fill="#ffb74d" rx="2" ry="2" />
                  <rect width="50" height="5" fill="#ffa726" rx="2" ry="2" />
                </g>

                <g transform="translate(380, 190)">
                  <rect width="50" height="15" fill="#ffb74d" rx="2" ry="2" />
                  <rect width="50" height="5" fill="#ffa726" rx="2" ry="2" />
                </g>

                <!-- Tile Samples -->
                <g transform="translate(70, 230)">
                  <rect width="45" height="45" fill="#eeeeee" stroke="#e0e0e0" stroke-width="1" />
                  <line x1="15" y1="0" x2="15" y2="45" stroke="#e0e0e0" stroke-width="1" />
                  <line x1="30" y1="0" x2="30" y2="45" stroke="#e0e0e0" stroke-width="1" />
                  <line x1="0" y1="15" x2="45" y2="15" stroke="#e0e0e0" stroke-width="1" />
                  <line x1="0" y1="30" x2="45" y2="30" stroke="#e0e0e0" stroke-width="1" />
                </g>

                <g transform="translate(130, 230)">
                  <rect width="45" height="45" fill="#bcaaa4" stroke="#a1887f" stroke-width="1" />
                  <line x1="15" y1="0" x2="15" y2="45" stroke="#a1887f" stroke-width="1" />
                  <line x1="30" y1="0" x2="30" y2="45" stroke="#a1887f" stroke-width="1" />
                  <line x1="0" y1="15" x2="45" y2="15" stroke="#a1887f" stroke-width="1" />
                  <line x1="0" y1="30" x2="45" y2="30" stroke="#a1887f" stroke-width="1" />
                </g>

                <!-- Hardware Items -->
                <g transform="translate(290, 230)">
                  <circle cx="10" cy="10" r="5" fill="#9e9e9e" stroke="#757575" stroke-width="1" />
                  <circle cx="25" cy="10" r="5" fill="#9e9e9e" stroke="#757575" stroke-width="1" />
                  <circle cx="40" cy="10" r="5" fill="#9e9e9e" stroke="#757575" stroke-width="1" />

                  <rect x="5" y="25" width="40" height="5" fill="#9e9e9e" stroke="#757575" stroke-width="1" />
                  <rect x="5" y="35" width="40" height="5" fill="#9e9e9e" stroke="#757575" stroke-width="1" />
                  <rect x="5" y="45" width="40" height="5" fill="#9e9e9e" stroke="#757575" stroke-width="1" />
                </g>

                <g transform="translate(380, 230)">
                  <g transform="rotate(45, 25, 10)">
                    <rect x="20" y="0" width="10" height="20" fill="#9e9e9e" stroke="#757575" stroke-width="1" />
                  </g>

                  <g transform="translate(0, 25)">
                    <rect x="5" width="40" height="5" fill="#9e9e9e" stroke="#757575" stroke-width="1" />
                    <rect x="20" y="0" width="10" height="15" fill="#9e9e9e" stroke="#757575" stroke-width="1" />
                  </g>
                </g>
              </g>
            </g>

            <!-- Foreground Elements -->
            <g>
              <!-- Floor -->
              <rect x="0" y="360" width="500" height="40" fill="#e0e0e0" />
              <g stroke="#bdbdbd" stroke-width="1">
                <line x1="0" y1="360" x2="500" y2="360" />
                <line x1="50" y1="360" x2="50" y2="400" />
                <line x1="100" y1="360" x2="100" y2="400" />
                <line x1="150" y1="360" x2="150" y2="400" />
                <line x1="200" y1="360" x2="200" y2="400" />
                <line x1="250" y1="360" x2="250" y2="400" />
                <line x1="300" y1="360" x2="300" y2="400" />
                <line x1="350" y1="360" x2="350" y2="400" />
                <line x1="400" y1="360" x2="400" y2="400" />
                <line x1="450" y1="360" x2="450" y2="400" />
              </g>
            </g>

            <!-- Title Banner -->
            <g>
              <rect x="150" y="20" width="200" height="40" fill="#1565c0" rx="5" ry="5" />
              <text x="250" y="45" font-family="Arial" font-size="18" fill="white" text-anchor="middle" font-weight="bold">MATERIAL SHOWCASE</text>
            </g>
          </svg>
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 400">
            <!-- Background -->
            <rect width="500" height="400" fill="#f5f7fa" />

            <!-- Office Environment -->
            <g>
              <!-- Office Walls -->
              <rect x="50" y="80" width="400" height="280" fill="#eceff1" stroke="#cfd8dc" stroke-width="2" />

              <!-- Windows -->
              <rect x="80" y="100" width="100" height="120" fill="#bbdefb" stroke="#90caf9" stroke-width="2" />
              <line x1="130" y1="100" x2="130" y2="220" stroke="#90caf9" stroke-width="2" />
              <line x1="80" y1="160" x2="180" y2="160" stroke="#90caf9" stroke-width="2" />

              <rect x="320" y="100" width="100" height="120" fill="#bbdefb" stroke="#90caf9" stroke-width="2" />
              <line x1="370" y1="100" x2="370" y2="220" stroke="#90caf9" stroke-width="2" />
              <line x1="320" y1="160" x2="420" y2="160" stroke="#90caf9" stroke-width="2" />

              <!-- Floor -->
              <rect x="50" y="320" width="400" height="40" fill="#d7ccc8" stroke="#bcaaa4" stroke-width="1" />
              <g stroke="#bcaaa4" stroke-width="0.5" stroke-dasharray="2,2">
                <line x1="150" y1="320" x2="150" y2="360" />
                <line x1="250" y1="320" x2="250" y2="360" />
                <line x1="350" y1="320" x2="350" y2="360" />
              </g>
            </g>

            <!-- Office Furniture -->
            <g>
              <!-- Customer Service Desk -->
              <rect x="180" y="250" width="140" height="70" fill="#5d4037" rx="3" ry="3" />
              <rect x="180" y="270" width="140" height="10" fill="#4e342e" />
              <rect x="190" y="250" width="120" height="5" fill="#8d6e63" />

              <!-- Computer on Desk -->
              <rect x="200" y="230" width="30" height="20" fill="#212121" rx="2" ry="2" />
              <rect x="210" y="225" width="10" height="5" fill="#424242" />

              <!-- Office Chair Behind Desk -->
              <rect x="235" y="280" width="30" height="10" fill="#212121" rx="2" ry="2" />
              <rect x="245" y="290" width="10" height="15" fill="#424242" />
              <circle cx="240" cy="305" r="3" fill="#212121" />
              <circle cx="260" cy="305" r="3" fill="#212121" />

              <!-- Customer Chairs -->
              <g transform="translate(160, 300)">
                <rect width="20" height="20" fill="#5d4037" rx="2" ry="2" />
                <rect x="5" y="20" width="10" height="15" fill="#4e342e" />
                <circle cx="5" cy="35" r="2" fill="#3e2723" />
                <circle cx="15" cy="35" r="2" fill="#3e2723" />
              </g>

              <g transform="translate(320, 300)">
                <rect width="20" height="20" fill="#5d4037" rx="2" ry="2" />
                <rect x="5" y="20" width="10" height="15" fill="#4e342e" />
                <circle cx="5" cy="35" r="2" fill="#3e2723" />
                <circle cx="15" cy="35" r="2" fill="#3e2723" />
              </g>
            </g>

            <!-- People -->
            <g>
              <!-- Customer Service Representative -->
              <g transform="translate(245, 260)">
                <circle cx="0" cy="-15" r="10" fill="#ffb74d" />
                <rect x="-5" y="-5" width="10" height="15" fill="#1565c0" />
                <rect x="-5" y="10" width="4" height="10" fill="#0d47a1" />
                <rect x="1" y="10" width="4" height="10" fill="#0d47a1" />
              </g>

              <!-- Customer 1 -->
              <g transform="translate(170, 280)">
                <circle cx="0" cy="-15" r="8" fill="#ffb74d" />
                <rect x="-5" y="-5" width="10" height="15" fill="#5d4037" />
                <rect x="-5" y="10" width="4" height="8" fill="#4e342e" />
                <rect x="1" y="10" width="4" height="8" fill="#4e342e" />
              </g>

              <!-- Customer 2 -->
              <g transform="translate(330, 280)">
                <circle cx="0" cy="-15" r="8" fill="#ffb74d" />
                <rect x="-5" y="-5" width="10" height="15" fill="#5d4037" />
                <rect x="-5" y="10" width="4" height="8" fill="#4e342e" />
                <rect x="1" y="10" width="4" height="8" fill="#4e342e" />
              </g>
            </g>

            <!-- Decorative Elements -->
            <g>
              <!-- Company Logo on Wall -->
              <rect x="220" y="120" width="60" height="40" fill="#1565c0" rx="5" ry="5" />
              <text x="250" y="145" font-family="Arial" font-size="14" fill="white" text-anchor="middle" font-weight="bold">BS</text>

              <!-- Plants -->
              <g transform="translate(80, 280)">
                <rect x="5" y="10" width="10" height="30" fill="#5d4037" />
                <circle cx="10" cy="5" r="10" fill="#66bb6a" />
                <circle cx="5" cy="10" r="8" fill="#4caf50" />
                <circle cx="15" cy="10" r="8" fill="#4caf50" />
              </g>

              <g transform="translate(400, 280)">
                <rect x="5" y="10" width="10" height="30" fill="#5d4037" />
                <circle cx="10" cy="5" r="10" fill="#66bb6a" />
                <circle cx="5" cy="10" r="8" fill="#4caf50" />
                <circle cx="15" cy="10" r="8" fill="#4caf50" />
              </g>
            </g>

            <!-- Title Banner -->
            <g>
              <rect x="150" y="30" width="200" height="40" fill="#1565c0" rx="5" ry="5" />
              <text x="250" y="55" font-family="Arial" font-size="16" fill="white" text-anchor="middle" font-weight="bold">CUSTOMER SERVICE</text>
            </g>
          </svg>
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 400">
            <!-- Background -->
            <rect width="500" height="400" fill="#f5f7fa" />

            <!-- Map of Pakistan (Simplified) -->
            <g fill="#e3f2fd" stroke="#1565c0" stroke-width="2">
              <path d="M100,100 L150,80 L180,100 L220,90 L250,120 L300,110 L330,150 L370,140 L400,180 L380,220 L400,250 L370,280 L390,310 L340,320 L300,280 L250,300 L220,280 L180,300 L150,280 L120,290 L100,260 L80,240 L90,210 L70,190 L90,150 L100,100 Z" />
            </g>

            <!-- Major Cities -->
            <g>
              <!-- Karachi -->
              <circle cx="180" cy="280" r="8" fill="#1565c0" />
              <text x="180" y="300" font-family="Arial" font-size="10" fill="#01579b" text-anchor="middle" font-weight="bold">Karachi</text>

              <!-- Lahore -->
              <circle cx="250" cy="120" r="8" fill="#1565c0" />
              <text x="250" y="140" font-family="Arial" font-size="10" fill="#01579b" text-anchor="middle" font-weight="bold">Lahore</text>

              <!-- Islamabad -->
              <circle cx="220" cy="90" r="8" fill="#1565c0" />
              <text x="220" y="110" font-family="Arial" font-size="10" fill="#01579b" text-anchor="middle" font-weight="bold">Islamabad</text>

              <!-- Peshawar -->
              <circle cx="180" cy="100" r="8" fill="#1565c0" />
              <text x="180" y="120" font-family="Arial" font-size="10" fill="#01579b" text-anchor="middle" font-weight="bold">Peshawar</text>

              <!-- Quetta -->
              <circle cx="120" cy="180" r="8" fill="#1565c0" />
              <text x="120" y="200" font-family="Arial" font-size="10" fill="#01579b" text-anchor="middle" font-weight="bold">Quetta</text>

              <!-- Multan -->
              <circle cx="220" cy="180" r="8" fill="#1565c0" />
              <text x="220" y="200" font-family="Arial" font-size="10" fill="#01579b" text-anchor="middle" font-weight="bold">Multan</text>

              <!-- Hyderabad -->
              <circle cx="220" cy="250" r="8" fill="#1565c0" />
              <text x="220" y="270" font-family="Arial" font-size="10" fill="#01579b" text-anchor="middle" font-weight="bold">Hyderabad</text>

              <!-- Faisalabad -->
              <circle cx="280" cy="140" r="8" fill="#1565c0" />
              <text x="280" y="160" font-family="Arial" font-size="10" fill="#01579b" text-anchor="middle" font-weight="bold">Faisalabad</text>
            </g>

            <!-- Distribution Network -->
            <g>
              <!-- Routes (Main Headquarters in Karachi) -->
              <g stroke="#f44336" stroke-width="3" stroke-dasharray="0" fill="none">
                <path d="M180,280 L220,250" />
                <path d="M180,280 L220,180" />
                <path d="M220,180 L250,120" />
                <path d="M220,180 L280,140" />
                <path d="M250,120 L220,90" />
                <path d="M180,100 L220,90" />
                <path d="M220,250 L120,180" />
              </g>

              <!-- Secondary Routes -->
              <g stroke="#f44336" stroke-width="1.5" stroke-dasharray="5,3" fill="none">
                <path d="M180,280 L120,180" />
                <path d="M250,120 L280,140" />
                <path d="M220,90 L280,140" />
              </g>

              <!-- Distribution Hubs (Warehouses) -->
              <g>
                <rect x="175" y="275" width="10" height="10" fill="#ff5722" stroke="#e64a19" stroke-width="1" />
                <rect x="245" y="115" width="10" height="10" fill="#ff5722" stroke="#e64a19" stroke-width="1" />
                <rect x="215" y="175" width="10" height="10" fill="#ff5722" stroke="#e64a19" stroke-width="1" />
              </g>

              <!-- Trucks on Routes -->
              <g>
                <g transform="translate(200, 260) scale(0.6)">
                  <rect x="0" y="0" width="25" height="12" fill="#1565c0" rx="2" ry="2" />
                  <rect x="25" y="3" width="12" height="9" fill="#1565c0" />
                  <circle cx="7" cy="12" r="3" fill="#424242" />
                  <circle cx="30" cy="12" r="3" fill="#424242" />
                  <text x="12" y="8" font-family="Arial" font-size="6" fill="white" text-anchor="middle">BS</text>
                </g>

                <g transform="translate(200, 150) scale(0.6)">
                  <rect x="0" y="0" width="25" height="12" fill="#1565c0" rx="2" ry="2" />
                  <rect x="25" y="3" width="12" height="9" fill="#1565c0" />
                  <circle cx="7" cy="12" r="3" fill="#424242" />
                  <circle cx="30" cy="12" r="3" fill="#424242" />
                  <text x="12" y="8" font-family="Arial" font-size="6" fill="white" text-anchor="middle">BS</text>
                </g>

                <g transform="translate(235, 105) scale(0.6)">
                  <rect x="0" y="0" width="25" height="12" fill="#1565c0" rx="2" ry="2" />
                  <rect x="25" y="3" width="12" height="9" fill="#1565c0" />
                  <circle cx="7" cy="12" r="3" fill="#424242" />
                  <circle cx="30" cy="12" r="3" fill="#424242" />
                  <text x="12" y="8" font-family="Arial" font-size="6" fill="white" text-anchor="middle">BS</text>
                </g>
              </g>
            </g>

            <!-- Legend -->
            <g transform="translate(380, 280)">
              <rect width="100" height="100" fill="white" stroke="#bdbdbd" stroke-width="1" rx="5" ry="5" />

              <text x="50" y="20" font-family="Arial" font-size="10" fill="#424242" text-anchor="middle" font-weight="bold">LEGEND</text>

              <circle cx="15" cy="35" r="5" fill="#1565c0" />
              <text x="25" y="38" font-family="Arial" font-size="8" fill="#424242">Major City</text>

              <rect x="10" y="45" width="10" height="10" fill="#ff5722" stroke="#e64a19" stroke-width="1" />
              <text x="25" y="53" font-family="Arial" font-size="8" fill="#424242">Distribution Hub</text>

              <line x1="10" y1="65" x2="20" y2="65" stroke="#f44336" stroke-width="2" />
              <text x="25" y="68" font-family="Arial" font-size="8" fill="#424242">Main Route</text>

              <line x1="10" y1="80" x2="20" y2="80" stroke="#f44336" stroke-width="1.5" stroke-dasharray="3,2" />
              <text x="25" y="83" font-family="Arial" font-size="8" fill="#424242">Secondary Route</text>
            </g>

            <!-- Title Banner -->
            <g>
              <rect x="150" y="30" width="200" height="40" fill="#1565c0" rx="5" ry="5" />
              <text x="250" y="55" font-family="Arial" font-size="16" fill="white" text-anchor="middle" font-weight="bold">DISTRIBUTION NETWORK</text>
            </g>
          </svg>
        </div>
      </div>
    </div>
  </section>

  <!-- Contact Section -->
  <section id="contact" class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-900">Contact Us</h2>
        <p class="mt-4 text-lg text-gray-600 max-w-3xl mx-auto">
          Have questions about our products or services? Get in touch with our team.
        </p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Contact Information -->
        <div class="bg-white rounded-lg shadow-sm p-8">
          <h3 class="text-xl font-bold text-gray-900 mb-6">Contact Information</h3>
          <div class="space-y-4">
            <div class="flex items-start">
              <div class="flex-shrink-0">
                <i class="fas fa-map-marker-alt text-primary-600 mt-1"></i>
              </div>
              <div class="ml-4">
                <p class="text-gray-700 font-medium">Address</p>
                <p class="text-gray-600">123 Business Avenue, Commercial Area, Lahore, Pakistan</p>
              </div>
            </div>
            <div class="flex items-start">
              <div class="flex-shrink-0">
                <i class="fas fa-phone text-primary-600 mt-1"></i>
              </div>
              <div class="ml-4">
                <p class="text-gray-700 font-medium">Phone</p>
                <p class="text-gray-600">+92 300 1234567</p>
                <p class="text-gray-600">+92 42 35678901</p>
              </div>
            </div>
            <div class="flex items-start">
              <div class="flex-shrink-0">
                <i class="fas fa-envelope text-primary-600 mt-1"></i>
              </div>
              <div class="ml-4">
                <p class="text-gray-700 font-medium">Email</p>
                <p class="text-gray-600">info@bstraders.com</p>
                <p class="text-gray-600">sales@bstraders.com</p>
              </div>
            </div>
            <div class="flex items-start">
              <div class="flex-shrink-0">
                <i class="fas fa-clock text-primary-600 mt-1"></i>
              </div>
              <div class="ml-4">
                <p class="text-gray-700 font-medium">Business Hours</p>
                <p class="text-gray-600">Monday - Saturday: 9:00 AM - 6:00 PM</p>
                <p class="text-gray-600">Sunday: Closed</p>
              </div>
            </div>
          </div>

          <div class="mt-8">
            <h4 class="text-lg font-medium text-gray-900 mb-4">Follow Us</h4>
            <div class="flex space-x-4">
              <a href="#" class="text-gray-600 hover:text-primary-600">
                <i class="fab fa-facebook-f text-2xl"></i>
              </a>
              <a href="#" class="text-gray-600 hover:text-primary-600">
                <i class="fab fa-twitter text-2xl"></i>
              </a>
              <a href="#" class="text-gray-600 hover:text-primary-600">
                <i class="fab fa-instagram text-2xl"></i>
              </a>
              <a href="#" class="text-gray-600 hover:text-primary-600">
                <i class="fab fa-linkedin-in text-2xl"></i>
              </a>
            </div>
          </div>
        </div>

        <!-- Contact Form -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm p-8">
          <h3 class="text-xl font-bold text-gray-900 mb-6">Send Us a Message</h3>
          <form action="#" method="POST">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
              <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Your Name</label>
                <input type="text" name="name" id="name" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
              </div>
              <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input type="email" name="email" id="email" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
              </div>
              <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                <input type="text" name="phone" id="phone" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
              </div>
              <div>
                <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                <input type="text" name="subject" id="subject" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
              </div>
              <div class="sm:col-span-2">
                <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                <textarea rows="4" name="message" id="message" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
              </div>
              <div class="sm:col-span-2">
                <button type="submit" class="w-full inline-flex justify-center py-3 px-6 border border-transparent shadow-sm text-base font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                  Send Message
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>

  <?php include 'includes/footer.php' ?>

  <!-- JavaScript -->
  <script src="assets/js/script.js"></script>
</body>

</html>
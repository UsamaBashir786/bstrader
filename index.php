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
  <?php include 'includes/navbar-main.php' ?>

  <!-- Hero Section -->
  <section class="bg-gradient-to-r from-primary-700 to-primary-900 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
        <div>
          <h1 class="text-4xl font-bold tracking-tight sm:text-5xl md:text-6xl">
            Premium Building Supplies for Your Projects
          </h1>
          <p class="mt-6 text-xl max-w-3xl">
            From construction materials to hardware, we provide quality products and reliable service for contractors and homeowners alike.
          </p>
          <div class="mt-10 flex flex-col sm:flex-row sm:space-x-4 space-y-4 sm:space-y-0">
            <a href="#products" class="bg-white text-primary-700 font-bold px-8 py-3 rounded-md text-center hover:bg-gray-100 shadow-md">
              Explore Products
            </a>
            <a href="#contact" class="bg-transparent border-2 border-white text-white font-bold px-8 py-3 rounded-md text-center hover:bg-white hover:text-primary-700">
              Contact Us
            </a>
          </div>
        </div>
        <div class="hidden md:block">
          <img src="images/hero-building.jpg" alt="Building Construction" class="rounded-lg shadow-2xl object-cover h-96 w-full">
        </div>
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
            <img src="images/customer1.jpg" alt="Customer" class="h-12 w-12 rounded-full object-cover">
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
            <img src="images/customer2.jpg" alt="Customer" class="h-12 w-12 rounded-full object-cover">
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
            <img src="images/customer3.jpg" alt="Customer" class="h-12 w-12 rounded-full object-cover">
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
          <img src="images/about1.jpg" alt="Company Image" class="rounded-lg shadow-md">
          <img src="images/about2.jpg" alt="Company Image" class="rounded-lg shadow-md mt-8">
          <img src="images/about3.jpg" alt="Company Image" class="rounded-lg shadow-md">
          <img src="images/about4.jpg" alt="Company Image" class="rounded-lg shadow-md mt-8">
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

  <!-- Map Section -->
  <section class="bg-white">
    <div class="max-w-7xl mx-auto">
      <div class="h-96 w-full">
        <!-- Replace with an actual map or embed Google Maps here -->
        <div class="h-full w-full bg-gray-200 flex items-center justify-center">
          <p class="text-gray-500 text-lg">Google Maps Embed Would Go Here</p>
        </div>
      </div>
    </div>
  </section>
  <div class="bg-primary-500 text-white p-4">
    This is a primary color background
  </div>
  <div class="bg-red-500 p-4">Test Red</div>

  <?php include 'includes/footer-main.php' ?>

  <!-- JavaScript -->
  <script src="assets/js/script.js"></script>
</body>

</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DocBook - Healthcare Appointments</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-900">
    <!-- Navbar -->
    <nav class="fixed top-0 left-0 w-full bg-white shadow-md z-50 py-4">
        <div class="container mx-auto flex justify-between items-center px-6">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-600 mr-3" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2}
                        d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                </svg>
                <span class="text-2xl font-bold text-blue-600">DocBook</span>
            </div>
            <div class="space-x-6 hidden md:flex">
                <a href="#services" class="text-gray-600 hover:text-blue-600 transition">Services</a>
                <a href="#doctors" class="text-gray-600 hover:text-blue-600 transition">Doctors</a>
                <a href="#about" class="text-gray-600 hover:text-blue-600 transition">About</a>
                @php
                    $user = Auth::user();
                    if ($user) {
                        echo '<a href="/admin" class="bg-blue-600 text-white px-4 py-2 rounded-full hover:bg-blue-700 transition">Dashboard</a>';
                    } else {
                        echo '<a href="/admin/login" class="bg-blue-600 text-white px-4 py-2 rounded-full hover:bg-blue-700 transition">Login</a>';
                    }
                @endphp
            </div>
            <div class="flex md:hidden">
                <button class="text-gray-600 hover:text-blue-600 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16m-7 6h7" />
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="relative pt-20 pb-32 bg-gradient-to-br from-blue-600 to-indigo-700 text-white"
        style="background-image: url('https://www.icoderzsolutions.com/blog/wp-content/uploads/2018/11/Doctor-Appointment-Booking-App-blog-post.jpg'); background-size: cover; background-position: center;">
        <div class="absolute inset-0 bg-black opacity-40"></div> <!-- Overlay for transparency -->
        <div class="container mx-auto px-6 text-center relative z-10">
            <h1 class="text-4xl md:text-5xl font-bold mb-6 leading-tight">Healthcare at Your Fingertips</h1>
            <p class="text-lg md:text-xl mb-10 max-w-2xl mx-auto opacity-90">Seamless doctor appointments, personalized
                care, and
                convenient consultations - all in one platform.</p>
            <div class="flex justify-center space-x-4">
                <a href="#services"
                    class="bg-white text-blue-600 px-8 py-3 rounded-full font-semibold hover:bg-gray-100 transition">Our
                    Services</a>
                <a href="#doctors"
                    class="border-2 border-white text-white px-8 py-3 rounded-full hover:bg-white hover:text-blue-600 transition">Meet
                    Doctors</a>
            </div>
        </div>
    </header>

    <!-- Services Section -->
    <section id="services" class="py-20 bg-white">
        <div class="container mx-auto text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-12 text-gray-800">Our Services</h2>
            <div class="grid sm:grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-gray-100 p-8 rounded-xl shadow-md hover:shadow-xl transition">
                    <div class="text-4xl md:text-5xl text-blue-600 mb-6">🏥</div>
                    <h3 class="text-xl md:text-2xl font-semibold mb-4 text-gray-800">General Consultation</h3>
                    <p class="text-gray-600">Comprehensive health checkups and consultations with expert physicians.</p>
                </div>
                <div class="bg-gray-100 p-8 rounded-xl shadow-md hover:shadow-xl transition">
                    <div class="text-4xl md:text-5xl text-blue-600 mb-6">💻</div>
                    <h3 class="text-xl md:text-2xl font-semibold mb-4 text-gray-800">Telemedicine</h3>
                    <p class="text-gray-600">Virtual consultations with top medical professionals from anywhere.</p>
                </div>
                <div class="bg-gray-100 p-8 rounded-xl shadow-md hover:shadow-xl transition">
                    <div class="text-4xl md:text-5xl text-blue-600 mb-6">🩺</div>
                    <h3 class="text-xl md:text-2xl font-semibold mb-4 text-gray-800">Specialized Care</h3>
                    <p class="text-gray-600">Expert consultations in various specialized medical fields.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Doctors Section -->
    <section id="doctors" class="py-20 bg-gray-100">
        <div class="container mx-auto text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-12 text-gray-800">Our Medical Experts</h2>
            <div class="grid sm:grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-xl shadow-md hover:shadow-xl transition">
                    <img src="https://png.pngtree.com/png-vector/20230928/ourmid/pngtree-young-afro-professional-doctor-png-image_10148632.png"
                        alt="Dr. Emily Carter"
                        class="w-32 h-32 md:w-64 md:h-64 object-cover rounded-full mx-auto mb-6 border-4 border-blue-100">
                    <h3 class="text-xl md:text-2xl font-semibold text-gray-800">Dr. Emily Carter</h3>
                    <p class="text-blue-600 font-medium">Cardiology Specialist</p>
                </div>
                <div class="bg-white p-8 rounded-xl shadow-md hover:shadow-xl transition">
                    <img src="https://png.pngtree.com/png-vector/20230928/ourmid/pngtree-young-afro-professional-doctor-png-image_10148632.png"
                        alt="Dr. Michael Wong"
                        class="w-32 h-32 md:w-64 md:h-64 object-cover rounded-full mx-auto mb-6 border-4 border-blue-100">
                    <h3 class="text-xl md:text-2xl font-semibold text-gray-800">Dr. Michael Wong</h3>
                    <p class="text-blue-600 font-medium">Dermatology Expert</p>
                </div>
                <div class="bg-white p-8 rounded-xl shadow-md hover:shadow-xl transition">
                    <img src="https://png.pngtree.com/png-vector/20230928/ourmid/pngtree-young-afro-professional-doctor-png-image_10148632.png"
                        alt="Dr. Sarah Rodriguez"
                        class="w-32 h-32 md:w-64 md:h-64 object-cover rounded-full mx-auto mb-6 border-4 border-blue-100">
                    <h3 class="text-xl md:text-2xl font-semibold text-gray-800">Dr. Sarah Rodriguez</h3>
                    <p class="text-blue-600 font-medium">Pediatric Specialist</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-20 bg-white">
        <div class="container mx-auto grid sm:grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-3xl md:text-4xl font-bold mb-6 text-gray-800">About DocBook</h2>
                <p class="text-lg md:text-xl text-gray-600 leading-relaxed mb-6">We are revolutionizing healthcare
                    access by providing a
                    seamless, user-friendly platform that connects patients with top-tier medical professionals. Our
                    mission is to make quality healthcare convenient, accessible, and personalized.</p>
                <a href="#"
                    class="bg-blue-600 text-white px-6 py-3 rounded-full hover:bg-blue-700 transition">Learn More</a>
            </div>
            <div>
                <img src="https://docpulse.com/wp-content/uploads/2020/01/in-patient-1024x549.gif"
                    alt="DocBook Healthcare" class="rounded-xl shadow-lg">
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-blue-800 text-white py-12">
        <div class="container mx-auto text-center">
            <div class="flex justify-center space-x-6 mb-8">
                <a href="#" class="hover:text-blue-200 transition">Privacy Policy</a>
                <a href="#" class="hover:text-blue-200 transition">Terms of Service</a>
                <a href="#" class="hover:text-blue-200 transition">Contact</a>
            </div>
            <p class="text-sm opacity-75">© 2024 DocBook. All Rights Reserved.</p>
        </div>
    </footer>
</body>

</html>

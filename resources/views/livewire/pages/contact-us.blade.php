<div class="min-h-screen bg-gradient-to-br from-blue-50 to-emerald-50 py-12 px-4 sm:px-6 lg:px-8"> <!-- Updated gradient for vibrancy -->
    <!-- Hero Section -->
    <section class="text-center mb-16 animate__animated animate__fadeIn">
        <h1 class="text-4xl font-bold text-blue-800 mb-4 sm:text-5xl">
            <i class="fas fa-envelope mr-2 text-emerald-600 animate__animated animate__pulse animate__infinite"></i> Contact Us <!-- Added pulse for engagement -->
        </h1>
        <p class="text-xl text-gray-700 max-w-3xl mx-auto">
            Get in touch with BootKode. We're here to help empower Africa's youth with digital skills, mentorship, and careers.
        </p>
    </section>

    <!-- Contact Details Section -->
    <section class="mb-16">
        <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-lg animate__animated animate__fadeInUp">
            <h2 class="text-3xl font-semibold text-blue-800 mb-6 flex items-center">
                <i class="fas fa-info-circle mr-3 text-emerald-600"></i> Our Contact Information
            </h2>
            <div class="grid md:grid-cols-2 gap-8">
                <!-- Founder Contact -->
                <div class="flex flex-col items-center text-center">
                    <img src="https://placehold.co/150x150/blue/white?text=Founder&font=roboto" alt="Oyetoke Adedayo Ebenezer" class="w-32 h-32 rounded-full mb-4 shadow-md hover:scale-105 transition duration-150 hover:shadow-lg" loading="lazy" decoding="async"> <!-- Updated src to placehold.co (reliable alternative); added hover animation, lazy loading for UX/perf -->
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Oyetoke Adedayo Ebenezer</h3>
                    <p class="text-gray-600 mb-2">Founder, Wireless Computer Services (BN 3757124)</p>
                    <p class="text-gray-700 flex items-center mb-1"><i class="fas fa-envelope mr-2 text-emerald-600"></i> oyetoke.ebenezer@gmail.com</p>
                    <p class="text-gray-700 flex items-center"><i class="fas fa-phone mr-2 text-emerald-600"></i> +234 903 003 6438</p>
                </div>
                <!-- Map Placeholder -->
                <div class="flex justify-center items-center">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3963.952912260219!2d3.379205614770757!3d6.5276316452784755!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x103b8b2ae68280c1%3A0xdc9e87a367c3d9cb!2sLagos!5e0!3m2!1sen!2sng!4v1623653659635!5m2!1sen!2sng" width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy" aria-label="BootKode Location Map"></iframe>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form Section -->
    <section class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-lg animate__animated animate__fadeInUp">
        <h2 class="text-3xl font-semibold text-blue-800 mb-6 flex items-center">
            <i class="fas fa-paper-plane mr-3 text-emerald-600"></i> Send Us a Message
        </h2>
        @if (session('success'))
            <div class="mb-6 p-4 bg-emerald-100 text-emerald-800 rounded-lg animate__animated animate__fadeIn">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-6 p-4 bg-red-100 text-red-800 rounded-lg animate__animated animate__fadeIn">
                {{ session('error') }}
            </div>
        @endif
        <form wire:submit.prevent="submit" class="space-y-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name <span class="text-red-500">*</span></label>
                <input wire:model.blur="name" id="name" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 hover:border-emerald-300 transition duration-150" placeholder="Your Full Name" aria-required="true"> <!-- Added blur for real-time, hover for UX -->
                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
                <input wire:model.blur="email" id="email" type="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 hover:border-emerald-300 transition duration-150" placeholder="your.email@example.com" aria-required="true">
                @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="subject" class="block text-sm font-medium text-gray-700">Subject <span class="text-red-500">*</span></label>
                <input wire:model.blur="subject" id="subject" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 hover:border-emerald-300 transition duration-150" placeholder="What is this about?" aria-required="true">
                @error('subject') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="message" class="block text-sm font-medium text-gray-700">Message <span class="text-red-500">*</span></label>
                <textarea wire:model.blur="message" id="message" rows="5" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 hover:border-emerald-300 transition duration-150" placeholder="Tell us how we can help..." aria-required="true"></textarea>
                @error('message') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <button type="submit" class="w-full bg-emerald-600 text-white font-bold py-3 px-4 rounded-md hover:bg-emerald-700 transition duration-300 flex items-center justify-center" wire:loading.attr="disabled"> <!-- Loading disable -->
                <span wire:loading.remove><i class="fas fa-paper-plane mr-2"></i> Send Message</span> <!-- Icon change for consistency -->
                <span wire:loading class="flex items-center"><i class="fas fa-spinner fa-spin mr-2"></i> Sending...</span> <!-- Spinner for UX feedback -->
            </button>
        </form>
    </section>
</div>
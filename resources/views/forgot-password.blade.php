<x-layout>
    <div class="min-h-screen bg-gray-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-white pt-20">
        <div class="max-w-md w-full space-y-8">
            <!-- Header with Logo -->
            <div class="text-start">
                <div class="flex justify-start mb-6">
                    <img src="{{ asset('imgs/icons-color/logo-color-word.svg') }}" alt="وزارة الثقافة والرياضة والشباب"
                        class="h-11 w-auto">
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">
                    نسيت كلمة المرور
                </h1>
                <p class="text-sm text-gray-600 mb-6">
                    يرجى إدخال البريد الإلكتروني لإعادة تعيين كلمة المرور
                </p>
            </div>

            <!-- Forgot Password Form -->
            <div class="space-y-6">
                <form class="space-y-6" action="#" method="POST">
                    @csrf

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 text-right mb-2">
                            البريد الإلكتروني :
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                            <input id="email" name="email" type="email" required
                                class="block w-full pr-10 pl-3 py-3 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#6161ab] focus:border-[#6161ab] text-right"
                                placeholder="mail@abc.com" dir="ltr">
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit"
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-[#f87171] hover:bg-[#dc2626] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#f87171] transition-colors duration-200">
                            تعيين كلمة مرور
                        </button>
                    </div>

                    <!-- Back to Login Link -->
                    <div class="text-center">
                        <p class="text-sm text-gray-600">
                            تذكرت كلمة المرور؟
                            <a href="/login" class="font-medium text-[#6161ab] hover:text-[#5555a0] transition-colors">
                                العودة لتسجيل الدخول
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>

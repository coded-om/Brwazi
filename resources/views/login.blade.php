<x-layout>
    <div class="min-h-screen bg-gray-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header with Logo -->
            <div class="text-center">
                <div class="flex justify-center mb-6">
                    <img src="{{ asset('imgs/icons-color/logo-w-word.svg') }}" alt="وزارة الثقافة والرياضة والشباب"
                        class="h-16 w-auto">
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">
                    تسجيل الدخول إلى حسابك
                </h1>
                <p class="text-sm text-gray-600">
                    اكتشف ما يحدث في عالم الفن من خلال معرض <span class="font-bold text-[#141640]">برواز</span>
                </p>
            </div>

            <!-- Login Form -->
            <div class="space-y-6">
                <form class="space-y-6" action="{{ route('login.process') }}" method="POST">
                    @csrf

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 text-right mb-2">
                            البريد الإلكتروني :
                        </label>
                        <input id="email" name="email" type="email" required
                            class="block w-full px-3 py-3 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#6161ab] focus:border-[#6161ab] text-right"
                            placeholder="mail@example.com" dir="ltr">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 text-right mb-2">
                            الرقم السري :
                        </label>
                        <input id="password" name="password" type="password" required
                            class="block w-full px-3 py-3 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#6161ab] focus:border-[#6161ab] text-right"
                            placeholder="••••••••••••••••">
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <div class="text-sm">
                            <a href="/forgot-password"
                                class="font-medium text-[#6161ab] hover:text-[#5555a0] transition-colors">
                                هل نسيت كلمة المرور؟
                            </a>
                        </div>
                        <div class="flex items-center">
                            <input id="remember-me" name="remember" type="checkbox"
                                class="h-4 w-4 text-[#6161ab] focus:ring-[#6161ab] border-gray-300 rounded">
                            <label for="remember-me" class="mr-2 block text-sm text-gray-900">
                                تذكرني
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit"
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-[#6161ab] hover:bg-[#5555a0] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#6161ab] transition-colors duration-200">
                            تسجيل الدخول
                        </button>
                    </div>

                    <!-- Sign Up Link -->
                    <div class="text-center">
                        <p class="text-sm text-gray-600">
                            ليس لديك حساب؟
                            <a href="/register"
                                class="font-medium text-[#6161ab] hover:text-[#5555a0] transition-colors">
                                إنشاء حساب
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>
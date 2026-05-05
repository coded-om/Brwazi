<x-layout>
    <div class="min-h-screen bg-gray-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <div class="flex justify-center mb-6">
                    <img src="{{ asset('imgs/icons-color/logo-w-word.svg') }}" alt="وزارة الثقافة والرياضة والشباب"
                        class="h-16 w-auto">
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">
                    إنشاء حساب جديد
                </h1>
            </div>

            <!-- Registration Form -->
            <div class="space-y-6">
                <form class="space-y-6" action="{{ route('register.process') }}" method="POST">
                    @csrf

                    <!-- Name Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- First Name -->
                        <div>
                            <label for="fname"
                                class="block text-sm font-medium text-gray-700 text-right mb-2 flex items-center gap-2">
                                <i class="fas fa-user text-gray-500"></i>
                                الاسم الأول :
                            </label>
                            <input id="fname" name="fname" type="text" required
                                class="block w-full px-3 py-3 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#6161ab] focus:border-[#6161ab] text-right"
                                placeholder="الاسم الأول">
                            @error('fname')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- Last Name -->
                        <div>
                            <label for="lname"
                                class="block text-sm font-medium text-gray-700 text-right mb-2 flex items-center gap-2">
                                <i class="fas fa-user text-gray-500"></i>
                                الاسم الأخير :
                            </label>
                            <input id="lname" name="lname" type="text" required
                                class="block w-full px-3 py-3 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#6161ab] focus:border-[#6161ab] text-right"
                                placeholder="الاسم الأخير">
                            @error('lname')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label for="email"
                            class="block text-sm font-medium text-gray-700 text-right mb-2 flex items-center gap-2">
                            <i class="fas fa-envelope text-gray-500"></i>
                            البريد الإلكتروني :
                        </label>
                        <input id="email" name="email" type="email" required
                            class="block w-full px-3 py-3 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#6161ab] focus:border-[#6161ab] text-right"
                            placeholder="mail@example.com" dir="ltr">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone Number Field -->
                    <div>
                        <label for="phone_number"
                            class="block text-sm font-medium text-gray-700 text-right mb-2 flex items-center gap-2">
                            <i class="fas fa-phone text-gray-500"></i>
                            رقم الهاتف (اختياري) :
                        </label>
                        <input id="phone_number" name="phone_number" type="tel"
                            class="block w-full px-3 py-3 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#6161ab] focus:border-[#6161ab] text-right"
                            placeholder="+968 12345678" dir="ltr">
                        @error('phone_number')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tagline Field -->
                    <div>
                        <label for="tagline"
                            class="block text-sm font-medium text-gray-700 text-right mb-2 flex items-center gap-2">
                            <i class="fas fa-sparkles text-gray-500"></i>
                            التخصص / المجال (اختياري) :
                        </label>
                        <select id="tagline" name="tagline"
                            class="block w-full px-3 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#6161ab] focus:border-[#6161ab] text-right">
                            <option value="">اختر التخصص</option>
                            @foreach(\App\Models\User::getTaglineOptions() as $option)
                                <option value="{{ $option }}">{{ $option }}</option>
                            @endforeach
                        </select>
                        @error('tagline')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Country & Birthday Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Country -->
                        <div>
                            <label for="country"
                                class="block text-sm font-medium text-gray-700 text-right mb-2 flex items-center gap-2">
                                <i class="fas fa-globe text-gray-500"></i>
                                البلد (اختياري) :
                            </label>
                            <select id="country" name="country"
                                class="block w-full px-3 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#6161ab] focus:border-[#6161ab] text-right">
                                <option value="">اختر البلد</option>
                                @foreach($countries as $country)
                                    <option value="{{ is_array($country) ? $country['name'] : $country }}">
                                        {{ is_array($country) ? $country['name'] : $country }}
                                    </option>
                                @endforeach
                            </select>
                            @error('country')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Birthday -->
                        <div>
                            <label for="birthday"
                                class="block text-sm font-medium text-gray-700 text-right mb-2 flex items-center gap-2">
                                <i class="fas fa-calendar text-gray-500"></i>
                                تاريخ الميلاد (اختياري) :
                            </label>
                            <input id="birthday" name="birthday" type="date"
                                class="block w-full px-3 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#6161ab] focus:border-[#6161ab] text-right">
                            @error('birthday')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Bio Field -->
                    <div>
                        <label for="bio"
                            class="block text-sm font-medium text-gray-700 text-right mb-2 flex items-center gap-2">
                            <i class="fas fa-quote-left text-gray-500"></i>
                            نبذة شخصية (اختياري) :
                        </label>
                        <textarea id="bio" name="bio" rows="3"
                            class="block w-full px-3 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#6161ab] focus:border-[#6161ab] text-right resize-none"
                            placeholder="اكتب نبذة موجزة عن نفسك (حتى 500 حرف)"></textarea>
                        @error('bio')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Password -->
                        <div>
                            <label for="password"
                                class="block text-sm font-medium text-gray-700 text-right mb-2 flex items-center gap-2">
                                <i class="fas fa-lock text-gray-500"></i>
                                كلمة المرور :
                            </label>
                            <input id="password" name="password" type="password" required
                                class="block w-full px-3 py-3 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#6161ab] focus:border-[#6161ab] text-right"
                                placeholder="••••••••••••••••">
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation"
                                class="block text-sm font-medium text-gray-700 text-right mb-2 flex items-center gap-2">
                                <i class="fas fa-lock text-gray-500"></i>
                                تأكيد كلمة المرور :
                            </label>
                            <input id="password_confirmation" name="password_confirmation" type="password" required
                                class="block w-full px-3 py-3 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#6161ab] focus:border-[#6161ab] text-right"
                                placeholder="••••••••••••••••">
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit"
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-brwazi-purple hover:bg-brwazi-purpleDark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brwazi-purple transition-colors duration-200">
                            إنشاء حساب
                        </button>
                    </div>

                    <!-- Login Link -->
                    <div class="text-center">
                        <p class="text-sm text-gray-600">
                            لديك حساب بالفعل؟
                            <a href="/login" class="font-medium text-[#6161ab] hover:text-[#5555a0] transition-colors">
                                تسجيل الدخول
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>
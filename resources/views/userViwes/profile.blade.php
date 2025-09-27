@php($user = auth()->user())
<x-layout>
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-md p-8">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">تعديل الملف الشخصي</h1>
                    <p class="text-gray-600">قم بتحديث معلوماتك الشخصية والنبذة التعريفية</p>
                </div>

                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            <p class="text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('user.profile.update') }}" enctype="multipart/form-data"
                    class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- الصورة الشخصية -->
                    <div class="flex items-start gap-6 p-6 bg-gray-50 rounded-xl">
                        <div
                            class="h-24 w-24 rounded-xl overflow-hidden bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center">
                            @if($user->ProfileImage)
                                <img src="{{ asset('storage/' . $user->ProfileImage) }}" alt="profile"
                                    class="h-full w-full object-cover" />
                            @else
                                <i class="fas fa-user text-white text-3xl"></i>
                            @endif
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">الصورة الشخصية</label>
                            <input type="file" name="profile_image" accept="image/*"
                                class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <p class="text-xs text-gray-500 mt-1">اختر صورة بصيغة JPG أو PNG (الحد الأقصى 2MB)</p>
                            @error('profile_image')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- الاسم -->
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">الاسم الأول *</label>
                            <input type="text" name="fname" value="{{ old('fname', $user->fname) }}" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            @error('fname')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">الاسم الأخير *</label>
                            <input type="text" name="lname" value="{{ old('lname', $user->lname) }}" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            @error('lname')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- البريد الإلكتروني -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني *</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- النبذة الشخصية -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">النبذة الشخصية</label>
                        <textarea name="bio" rows="4" placeholder="اكتب نبذة موجزة عن نفسك..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none">{{ old('bio', $user->bio) }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">حتى 500 حرف</p>
                        @error('bio')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- التخصص / المجال -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">التخصص / المجال</label>
                        <select name="tagline"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">اختر التخصص</option>
                            @foreach(\App\Models\User::getTaglineOptions() as $option)
                                <option value="{{ $option }}" {{ old('tagline', $user->tagline) == $option ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                        @error('tagline')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- بيانات إضافية -->
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">رقم الجوال</label>
                            <input type="tel" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            @error('phone_number')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">الدولة</label>
                            <input type="text" name="country" value="{{ old('country', $user->country) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            @error('country')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- تاريخ الميلاد -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ الميلاد</label>
                        <input type="date" name="birthday" value="{{ old('birthday', $user->birthday) }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('birthday')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- الأزرار -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <a href="{{ route('user.dashboard') }}"
                            class="text-gray-600 hover:text-gray-800 flex items-center gap-2">
                            <i class="fas fa-arrow-right"></i>
                            العودة للوحة التحكم
                        </a>
                        <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-lg font-medium transition flex items-center gap-2">
                            <i class="fas fa-save"></i>
                            حفظ التغييرات
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>
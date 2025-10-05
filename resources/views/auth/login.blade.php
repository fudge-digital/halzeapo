@extends('layouts.app')

@section('content')
<div class="min-h-screen flex flex-col lg:flex-row">
    {{-- Left side --}}
    <div class="w-full lg:w-1/2 bg-yellow-100 flex flex-col items-center justify-center p-6">
        <img src="{{ Storage::url('images/HALZEA-LOGO.png') }}" alt="Banner" class="max-w-md mb-4">
        <div class="text-center lg:text-center">
            <!-- <h2 class="text-3xl font-bold text-gray-800 mb-2">HALZEA</h2> -->
            <p class="text-xs text-gray-600">POMS V.01 <br>&copy; Fudge Digital - 2025</p>
        </div>
    </div>

    {{-- Right side (Form) --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-white">
        <div class="max-w-md w-full">
            <h2 class="invisible sm:visible text-3xl font-bold text-gray-800">Login</h2>
            <p class="text-sm mb-6">Silahkan login menggunakan akses yang sudah diberikan</p>

            @if (session('status'))
                <div class="mb-4 text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Password</label>
                    <input id="password" type="password" name="password" required
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center">
                    <input id="remember_me" type="checkbox" name="remember"
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="remember_me" class="ml-2 block text-sm text-gray-900">Ingat saya</label>
                </div>

                {{-- Submit --}}
                <div>
                    <button type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none">
                        Masuk
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

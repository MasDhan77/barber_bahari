@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-900 px-6 py-12">
    <div class="max-w-md w-full space-y-8 bg-gray-800 p-10 rounded-3xl border border-gray-700 shadow-2xl">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-white">
                Selamat Datang di <span class="text-amber-500">Barber Bahari</span>
            </h2>
            <p class="mt-2 text-center text-sm text-gray-400">
                Silakan masuk untuk mengelola booking Anda
            </p>
        </div>

        <form class="mt-8 space-y-6" action="{{ route('login') }}" method="POST">
            @csrf
            <div class="rounded-md shadow-sm -space-y-px">
                <div class="mb-4">
                    <label for="email" class="text-gray-400 text-sm font-bold mb-1 block">Alamat Email</label>
                    <input id="email" name="email" type="email" required 
                        class="appearance-none rounded-xl relative block w-full px-4 py-3 border border-gray-700 bg-gray-900 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition" 
                        placeholder="aril@example.com">
                </div>
                <div>
                    <label for="password" class="text-gray-400 text-sm font-bold mb-1 block">Kata Sandi</label>
                    <input id="password" name="password" type="password" required 
                        class="appearance-none rounded-xl relative block w-full px-4 py-3 border border-gray-700 bg-gray-900 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition" 
                        placeholder="••••••••">
                </div>
            </div>

            <div class="flex items-center justify-between text-sm">
                <div class="flex items-center">
                    <input id="remember_me" name="remember" type="checkbox" class="h-4 w-4 text-amber-500 focus:ring-amber-500 border-gray-700 rounded bg-gray-900">
                    <label for="remember_me" class="ml-2 block text-gray-400">Ingat saya</label>
                </div>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="font-medium text-amber-500 hover:text-amber-400">Lupa password?</a>
                @endif
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition duration-300 shadow-lg shadow-amber-600/20">
                    Masuk Sekarang
                </button>
            </div>
        </form>

        <div class="text-center">
            <p class="text-sm text-gray-400">
                Belum punya akun? 
                <a href="{{ route('register') }}" class="font-medium text-amber-500 hover:text-amber-400">Daftar sekarang</a>
            </p>
        </div>
    </div>
</div>
@endsection
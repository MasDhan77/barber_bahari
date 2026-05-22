@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-900 px-6 py-12">
    <div class="max-w-md w-full space-y-8 bg-gray-800 p-10 rounded-3xl border border-gray-700 shadow-2xl">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-white">
                Gabung <span class="text-amber-500">Member</span>
            </h2>
            <p class="mt-2 text-center text-sm text-gray-400">
                Lengkapi data diri Anda untuk kemudahan booking
            </p>
        </div>

        <form class="mt-8 space-y-4" action="{{ route('register') }}" method="POST">
            @csrf
            <div>
                <label for="name" class="text-gray-400 text-sm font-bold mb-1 block">Nama Lengkap</label>
                <input id="name" name="name" type="text" required 
                    class="appearance-none rounded-xl relative block w-full px-4 py-3 border border-gray-700 bg-gray-900 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition" 
                    placeholder="Masukkan nama lengkap">
            </div>

            <div>
                <label for="email" class="text-gray-400 text-sm font-bold mb-1 block">Alamat Email</label>
                <input id="email" name="email" type="email" required 
                    class="appearance-none rounded-xl relative block w-full px-4 py-3 border border-gray-700 bg-gray-900 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition" 
                    placeholder="email@example.com">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="password" class="text-gray-400 text-sm font-bold mb-1 block">Password</label>
                    <input id="password" name="password" type="password" required 
                        class="appearance-none rounded-xl relative block w-full px-4 py-3 border border-gray-700 bg-gray-900 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition" 
                        placeholder="••••••••">
                </div>
                <div>
                    <label for="password_confirmation" class="text-gray-400 text-sm font-bold mb-1 block">Konfirmasi</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required 
                        class="appearance-none rounded-xl relative block w-full px-4 py-3 border border-gray-700 bg-gray-900 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition" 
                        placeholder="••••••••">
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition duration-300 shadow-lg shadow-amber-600/20">
                    Daftar Member
                </button>
            </div>
        </form>

        <div class="text-center">
            <p class="text-sm text-gray-400">
                Sudah punya akun? 
                <a href="{{ route('login') }}" class="font-medium text-amber-500 hover:text-amber-400">Masuk di sini</a>
            </p>
        </div>
    </div>
</div>
@endsection
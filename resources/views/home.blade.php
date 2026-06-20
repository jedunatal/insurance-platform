@extends('layouts.app')

@section('content')

<div class="flex flex-col items-center justify-center py-20 text-center">

    <h1 class="text-4xl font-bold mb-4" style="color:#295384;">

        Plataforma de Consultoria e Corretagem de Seguros

    </h1>

    <p class="text-lg text-gray-500 mb-8">

        Sistema em desenvolvimento.

    </p>

    <div class="bg-white rounded-xl shadow-sm p-8 max-w-2xl">

        <p class="text-gray-600 mb-6">

            Bem-vindo à plataforma de gestão de seguros.

            Em breve todas as funcionalidades estarão disponíveis.

        </p>

        <a href="{{ route('dashboard') }}"
            class="inline-block px-6 py-3 rounded-lg text-white font-semibold"
            style="background:#295384;">

            Acessar Dashboard

        </a>

    </div>

</div>

@endsection
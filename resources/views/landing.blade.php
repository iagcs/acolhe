<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Acolhe — Seus pacientes param de faltar.</title>
    <meta name="description" content="O Acolhe cuida da sua agenda, envia lembretes humanizados pelo WhatsApp e prepara um prontuário inicial com IA antes da primeira sessão.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root { --apple-ease: cubic-bezier(0.25, 0.46, 0.45, 0.94); }

        [data-animate] {
            opacity: 0;
            transform: translateY(40px);
            transition: opacity 0.9s var(--apple-ease), transform 0.9s var(--apple-ease);
        }
        [data-animate].is-visible {
            opacity: 1;
            transform: translateY(0);
        }
        [data-animate][data-delay="1"] { transition-delay: 0.12s; }
        [data-animate][data-delay="2"] { transition-delay: 0.24s; }
        [data-animate][data-delay="3"] { transition-delay: 0.36s; }
        [data-animate][data-delay="4"] { transition-delay: 0.48s; }
        [data-animate][data-delay="5"] { transition-delay: 0.6s; }

        @media (prefers-reduced-motion: reduce) {
            [data-animate] { opacity: 1; transform: none; transition: none; }
        }

        /* Sticky headline parallax feel */
        .hero-headline {
            background: linear-gradient(180deg, #1C1917 0%, #2D6A4F 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="bg-white text-[#1d1d1f] antialiased" style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;">

    {{-- ── NAVIGATION ── --}}
    <nav id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-500">
        <div class="mx-auto flex h-12 max-w-[980px] items-center justify-between px-6">
            <a href="/" class="text-[15px] font-semibold tracking-tight text-[#1d1d1f] transition-opacity hover:opacity-70">
                Acolhe
            </a>

            <div class="hidden items-center gap-7 md:flex">
                <a href="#funcionalidades" class="text-xs text-[#424245] transition-opacity hover:opacity-70">Funcionalidades</a>
                <a href="#precos" class="text-xs text-[#424245] transition-opacity hover:opacity-70">Preços</a>
                <a href="#faq" class="text-xs text-[#424245] transition-opacity hover:opacity-70">FAQ</a>
                <a href="#comecar" class="cursor-pointer rounded-full bg-[#0D9488] px-4 py-1.5 text-xs font-medium text-white transition-all hover:bg-[#0F766E] focus:outline-none focus:ring-2 focus:ring-[#0D9488] focus:ring-offset-2">
                    Começar grátis
                </a>
            </div>

            <button id="mobile-menu-btn" class="cursor-pointer p-1 text-[#1d1d1f] md:hidden focus:outline-none" aria-label="Menu">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
            </button>
        </div>

        <div id="mobile-menu" class="hidden border-t border-[#d2d2d7]/40 bg-white/95 px-6 pb-5 pt-3 backdrop-blur-2xl md:hidden">
            <a href="#funcionalidades" class="block py-2 text-sm text-[#424245]">Funcionalidades</a>
            <a href="#precos" class="block py-2 text-sm text-[#424245]">Preços</a>
            <a href="#faq" class="block py-2 text-sm text-[#424245]">FAQ</a>
            <a href="#comecar" class="mt-3 block rounded-full bg-[#0D9488] py-2.5 text-center text-sm font-medium text-white">Começar grátis</a>
        </div>
    </nav>

    {{-- ── HERO ── --}}
    <section class="overflow-hidden px-6 pt-32 pb-8 lg:pt-44 lg:pb-16">
        <div class="mx-auto max-w-[980px] text-center">
            <p data-animate class="mb-4 text-sm font-medium tracking-wide text-[#0D9488] sm:text-base">Acolhe</p>

            <h1 data-animate data-delay="1" class="hero-headline mx-auto mb-5 max-w-3xl text-[40px] font-bold leading-[1.05] tracking-[-0.035em] sm:text-[56px] lg:text-[72px]">
                Seus pacientes param de faltar.
            </h1>

            <p data-animate data-delay="2" class="mx-auto mb-8 max-w-xl text-base leading-relaxed text-[#86868b] sm:text-lg lg:text-xl">
                Agenda inteligente. Lembretes humanizados pelo WhatsApp. Triagem com IA antes da primeira sessão. Tudo para você focar no paciente.
            </p>

            <div data-animate data-delay="3" class="flex flex-col items-center justify-center gap-4 sm:flex-row">
                <a href="#comecar" class="inline-flex cursor-pointer items-center rounded-full bg-[#0D9488] px-7 py-3 text-[15px] font-medium text-white transition-all hover:bg-[#0F766E] focus:outline-none focus:ring-2 focus:ring-[#0D9488] focus:ring-offset-2">
                    Testar 14 dias grátis
                </a>
                <a href="#funcionalidades" class="inline-flex cursor-pointer items-center gap-1 text-[15px] font-medium text-[#0D9488] transition-opacity hover:opacity-70">
                    Saiba mais
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                </a>
            </div>
        </div>
    </section>

    {{-- ── HERO VISUAL — WhatsApp Mockup ── --}}
    <section class="px-6 pt-8 pb-24 lg:pt-12 lg:pb-32">
        <div data-animate data-delay="4" class="mx-auto max-w-[360px]">
            <div class="overflow-hidden rounded-[28px] bg-white shadow-[0_20px_80px_rgba(0,0,0,0.12)]">
                <div class="flex items-center gap-3 bg-[#075E54] px-4 py-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-[#128C7E] text-xs font-bold text-white">A</div>
                    <div>
                        <div class="text-[13px] font-semibold text-white">Acolhe</div>
                        <div class="text-[10px] text-emerald-200/80">online</div>
                    </div>
                </div>
                <div class="space-y-1.5 bg-[#ECE5DD] p-3" style="min-height: 280px;">
                    <div class="mb-2.5 text-center">
                        <span class="inline-block rounded-md bg-[#E1E8ED] px-3 py-0.5 text-[10px] text-[#86868b]">Hoje</span>
                    </div>
                    {{-- Bot --}}
                    <div class="flex justify-start">
                        <div class="max-w-[78%] rounded-xl rounded-tl-sm bg-white px-3 py-2">
                            <p class="text-[12px] leading-[1.45] text-[#1d1d1f]">Oi Ana, te espero amanha as 15h &#x1F499;<br>Precisa remarcar? Me avise.</p>
                            <div class="mt-0.5 flex items-center justify-end gap-1">
                                <span class="text-[9px] text-[#86868b]">09:00</span>
                                <span class="text-[10px] font-bold text-[#53BDEB]">&#10003;&#10003;</span>
                            </div>
                        </div>
                    </div>
                    {{-- User --}}
                    <div class="flex justify-end">
                        <div class="max-w-[78%] rounded-xl rounded-tr-sm bg-[#DCF8C6] px-3 py-2">
                            <p class="text-[12px] leading-[1.45] text-[#1d1d1f]">Obrigada! Estarei la &#x1F60A;</p>
                            <div class="mt-0.5 flex justify-end">
                                <span class="text-[9px] text-[#86868b]">09:15</span>
                            </div>
                        </div>
                    </div>
                    {{-- Bot 2 --}}
                    <div class="flex justify-start">
                        <div class="max-w-[78%] rounded-xl rounded-tl-sm bg-white px-3 py-2">
                            <p class="text-[12px] leading-[1.45] text-[#1d1d1f]">Sua sessão é daqui a 2h, as 15:00.<br>Te espero! &#x1F499;</p>
                            <div class="mt-0.5 flex items-center justify-end gap-1">
                                <span class="text-[9px] text-[#86868b]">13:00</span>
                                <span class="text-[10px] font-bold text-[#53BDEB]">&#10003;&#10003;</span>
                            </div>
                        </div>
                    </div>
                    {{-- Buttons --}}
                    <div class="flex gap-1.5 pt-1.5">
                        <div class="flex-1 cursor-pointer rounded-lg border border-[#128C7E]/60 bg-white py-2 text-center text-[11px] font-semibold text-[#128C7E] transition-colors hover:bg-[#128C7E] hover:text-white">Confirmar</div>
                        <div class="flex-1 cursor-pointer rounded-lg border border-[#d2d2d7] bg-white py-2 text-center text-[11px] font-semibold text-[#86868b] transition-colors hover:bg-[#f5f5f7]">Remarcar</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── STATS STRIP ── --}}
    <section class="border-y border-[#d2d2d7]/40 bg-[#fbfbfd]">
        <div class="mx-auto grid max-w-[980px] grid-cols-3 divide-x divide-[#d2d2d7]/40">
            @foreach([
                ['val' => '547k+', 'label' => 'psicólogos no Brasil'],
                ['val' => '70%', 'label' => 'menos faltas'],
                ['val' => '10 min', 'label' => 'para configurar'],
            ] as $i => $stat)
                <div data-animate data-delay="{{ $i + 1 }}" class="py-10 text-center lg:py-14">
                    <div class="text-3xl font-bold tracking-tight text-[#1d1d1f] lg:text-4xl">{{ $stat['val'] }}</div>
                    <div class="mt-1 text-xs text-[#86868b] lg:text-sm">{{ $stat['label'] }}</div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ── PAIN SECTION ── --}}
    <section class="px-6 py-24 lg:py-32">
        <div class="mx-auto max-w-[980px] text-center">
            <h2 data-animate class="mx-auto max-w-2xl text-[32px] font-bold leading-[1.1] tracking-[-0.025em] sm:text-[40px] lg:text-[48px]">
                Você e psicólogo,<br>não secretario.
            </h2>
            <p data-animate data-delay="1" class="mx-auto mt-5 mb-16 max-w-lg text-base leading-relaxed text-[#86868b] lg:text-lg">
                Confirmar sessão. Remarcar. Lembrar. Anotar. Cobrar. Repetir.<br>Seu tempo deveria ser gasto ouvindo.
            </p>

            <div class="grid gap-5 sm:grid-cols-3">
                @php
                    $pains = [
                        ['stat' => '3h/sem', 'desc' => 'gastas confirmando sessoes', 'icon' => '<svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'],
                        ['stat' => 'R$ 800+', 'desc' => 'perdidos com faltas/mês', 'icon' => '<svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'],
                        ['stat' => '40%', 'desc' => 'relatam burnout administrativo', 'icon' => '<svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 00.495-7.467 5.99 5.99 0 00-1.925 3.546 5.974 5.974 0 01-2.133-1A3.75 3.75 0 0012 18z" /></svg>'],
                    ];
                @endphp

                @foreach($pains as $i => $pain)
                    <div data-animate data-delay="{{ $i + 1 }}" class="rounded-2xl bg-[#f5f5f7] p-8 text-center">
                        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-white text-[#1d1d1f] shadow-sm">
                            {!! $pain['icon'] !!}
                        </div>
                        <div class="text-3xl font-bold tracking-tight text-[#1d1d1f]">{{ $pain['stat'] }}</div>
                        <div class="mt-1 text-sm text-[#86868b]">{{ $pain['desc'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── FEATURES — Full-width cards, Apple-style ── --}}
    <section id="funcionalidades" class="px-6 pb-8">
        <div class="mx-auto max-w-[980px]">
            <div class="mb-20 text-center lg:mb-24">
                <p data-animate class="text-sm font-medium tracking-wide text-[#0D9488] sm:text-base">Funcionalidades</p>
                <h2 data-animate data-delay="1" class="mt-3 text-[32px] font-bold leading-[1.1] tracking-[-0.025em] sm:text-[40px] lg:text-[48px]">
                    Tudo que você precisa.<br>Nada que não precisa.
                </h2>
            </div>
        </div>
    </section>

    {{-- Feature 1: Agenda --}}
    <section class="px-6 pb-24 lg:pb-32">
        <div data-animate class="mx-auto max-w-[980px] overflow-hidden rounded-3xl bg-[#f5f5f7]">
            <div class="grid items-center lg:grid-cols-2">
                <div class="p-10 lg:p-16">
                    <p class="mb-2 text-sm font-semibold text-[#0D9488]">Agenda Inteligente</p>
                    <h3 class="mb-4 text-[28px] font-bold leading-[1.1] tracking-[-0.02em] lg:text-[34px]">
                        Agenda que entende sua rotina.
                    </h3>
                    <p class="mb-6 text-[15px] leading-relaxed text-[#86868b]">
                        Sessões de 50 minutos com 10 de intervalo &mdash; o padrão da psicologia. Recorrência automatica, link de agendamento público. Seu paciente agenda sozinho.
                    </p>
                    <ul class="space-y-2.5">
                        @foreach(['Sessões recorrentes automáticas', 'Link público de agendamento', 'Visão semanal e mensal', 'Detecção de conflitos'] as $h)
                            <li class="flex items-center gap-2.5 text-sm text-[#1d1d1f]">
                                <svg class="h-4 w-4 flex-shrink-0 text-[#0D9488]" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                {{ $h }}
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="flex items-center justify-center p-10 lg:p-16">
                    <div class="flex h-40 w-40 items-center justify-center rounded-3xl bg-white shadow-sm lg:h-52 lg:w-52">
                        <svg class="h-20 w-20 text-[#0D9488] lg:h-24 lg:w-24" fill="none" viewBox="0 0 24 24" stroke-width="0.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Feature 2: Lembretes --}}
    <section class="px-6 pb-24 lg:pb-32">
        <div data-animate class="mx-auto max-w-[980px] overflow-hidden rounded-3xl bg-[#1d1d1f] text-white">
            <div class="grid items-center lg:grid-cols-2">
                <div class="flex items-center justify-center p-10 lg:order-1 lg:p-16">
                    <div class="flex h-40 w-40 items-center justify-center rounded-3xl bg-[#2d2d2f] lg:h-52 lg:w-52">
                        <svg class="h-20 w-20 text-[#2DD4BF] lg:h-24 lg:w-24" fill="none" viewBox="0 0 24 24" stroke-width="0.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 01.778-.332 48.294 48.294 0 005.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" /></svg>
                    </div>
                </div>
                <div class="p-10 lg:order-2 lg:p-16">
                    <p class="mb-2 text-sm font-semibold text-[#2DD4BF]">Anti-Falta</p>
                    <h3 class="mb-4 text-[28px] font-bold leading-[1.1] tracking-[-0.02em] lg:text-[34px]">
                        Lembretes que funcionam de verdade.
                    </h3>
                    <p class="mb-6 text-[15px] leading-relaxed text-[#a1a1a6]">
                        WhatsApp automático 24h e 2h antes. Mensagens humanizadas com o seu nome, no tom certo. Seus pacientes respondem e confirmam.
                    </p>
                    <ul class="space-y-2.5">
                        @foreach(['WhatsApp nativo, não email', 'Tom humanizado e editável', 'Status: enviado, entregue, lido', 'Confirmação interativa com botões'] as $h)
                            <li class="flex items-center gap-2.5 text-sm text-[#f5f5f7]">
                                <svg class="h-4 w-4 flex-shrink-0 text-[#2DD4BF]" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                {{ $h }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- Feature 3: IA — Hero card --}}
    <section class="px-6 pb-12 lg:pb-16">
        <div data-animate class="mx-auto max-w-[980px] overflow-hidden rounded-3xl" style="background: linear-gradient(135deg, #fbfbfd 0%, #f0f0ff 100%);">
            <div class="grid items-center lg:grid-cols-2">
                <div class="p-10 lg:p-16">
                    <p class="mb-2 text-sm font-semibold text-[#7C3AED]">Inteligência Artificial</p>
                    <h3 class="mb-4 text-[28px] font-bold leading-[1.1] tracking-[-0.02em] lg:text-[34px]">
                        IA que fala a sua língua terapêutica.
                    </h3>
                    <p class="mb-6 text-[15px] leading-relaxed text-[#86868b]">
                        Da triagem pré-consulta ao resumo pos-sessão, a IA trabalha no vocabulário da sua abordagem. Você ganha tempo, contexto e clareza &mdash; sem perder o controle clínico.
                    </p>
                    <ul class="space-y-2.5">
                        @foreach([
                            'Triagem pré-consulta via WhatsApp',
                            'Resumo clínico estruturado automático',
                            'Resumo inteligente de consultas online',
                            'Configurável por abordagem terapêutica',
                            'Detecção de crise com encaminhamento ao CVV',
                        ] as $h)
                            <li class="flex items-center gap-2.5 text-sm text-[#1d1d1f]">
                                <svg class="h-4 w-4 flex-shrink-0 text-[#7C3AED]" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                {{ $h }}
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="flex items-center justify-center p-10 lg:p-16">
                    <div class="flex h-40 w-40 items-center justify-center rounded-3xl bg-white shadow-sm lg:h-52 lg:w-52">
                        <svg class="h-20 w-20 text-[#7C3AED] lg:h-24 lg:w-24" fill="none" viewBox="0 0 24 24" stroke-width="0.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 00-2.455 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" /></svg>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── IA DEEP DIVE — 3 sub-cards ── --}}
    <section class="px-6 pb-12 lg:pb-16">
        <div class="mx-auto grid max-w-[980px] gap-5 lg:grid-cols-3">

            {{-- IA Card 1: Triagem --}}
            <div data-animate data-delay="1" class="rounded-2xl bg-[#f5f5f7] p-8">
                <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-xl bg-[#7C3AED]/10 text-[#7C3AED]">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" /></svg>
                </div>
                <h4 class="mb-2 text-base font-semibold text-[#1d1d1f]">Triagem pré-consulta</h4>
                <p class="mb-4 text-sm leading-relaxed text-[#86868b]">
                    Antes da primeira sessão, a IA conduz uma conversa guiada via WhatsApp. Coleta queixa principal, histórico relevante e expectativas &mdash; tudo no tom da sua abordagem.
                </p>
                <p class="text-sm leading-relaxed text-[#86868b]">
                    Ao finalizar, gera um <span class="font-medium text-[#1d1d1f]">prontuário inicial estruturado</span> com resumo clínico, pontos de atenção e sugestoes de exploração. Você le em 2 minutos antes de atender.
                </p>
            </div>

            {{-- IA Card 2: Resumo de consultas --}}
            <div data-animate data-delay="2" class="rounded-2xl bg-[#f5f5f7] p-8">
                <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-xl bg-[#7C3AED]/10 text-[#7C3AED]">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                </div>
                <h4 class="mb-2 text-base font-semibold text-[#1d1d1f]">Resumo de consultas online</h4>
                <p class="mb-4 text-sm leading-relaxed text-[#86868b]">
                    Após cada sessão online, a IA gera um <span class="font-medium text-[#1d1d1f]">resumo automático</span> dos pontos abordados, evoluções observadas e temas para acompanhamento.
                </p>
                <p class="text-sm leading-relaxed text-[#86868b]">
                    O resumo usa o vocabulário da sua abordagem &mdash; pensamentos automáticos para TCC, transferência para Psicanálise, awareness para Gestalt. Tudo criptografado e editável.
                </p>
            </div>

            {{-- IA Card 3: Seguranca --}}
            <div data-animate data-delay="3" class="rounded-2xl bg-[#f5f5f7] p-8">
                <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-xl bg-[#7C3AED]/10 text-[#7C3AED]">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
                </div>
                <h4 class="mb-2 text-base font-semibold text-[#1d1d1f]">Ética e segurança</h4>
                <p class="mb-4 text-sm leading-relaxed text-[#86868b]">
                    O paciente <span class="font-medium text-[#1d1d1f]">sempre sabe que está falando com uma IA</span>. Consentimento explícito e obrigatório antes de qualquer interação.
                </p>
                <p class="text-sm leading-relaxed text-[#86868b]">
                    Se detectar menção a ideação suicida ou automutilação, a IA encerra imediatamente e direciona ao <span class="font-medium text-[#1d1d1f]">CVV (188)</span>. O psicólogo é alertado na hora.
                </p>
            </div>
        </div>
    </section>

    {{-- ── IA — 5 Abordagens terapêuticas ── --}}
    <section class="px-6 pb-24 lg:pb-32">
        <div data-animate class="mx-auto max-w-[980px] overflow-hidden rounded-3xl bg-[#1d1d1f] p-10 text-white lg:p-16">
            <div class="mb-10 max-w-lg">
                <p class="mb-2 text-sm font-semibold text-[#7C3AED]">Sua abordagem, sua línguagem</p>
                <h3 class="mb-4 text-[28px] font-bold leading-[1.1] tracking-[-0.02em] lg:text-[34px]">
                    5 abordagens.<br>5 vocabulários.
                </h3>
                <p class="text-[15px] leading-relaxed text-[#a1a1a6]">
                    A IA adapta tom, perguntas e vocabulário técnico a sua linha terapêutica. Você configura as perguntas, edita as instruções e personaliza tudo.
                </p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                @php
                    $approaches = [
                        ['name' => 'TCC', 'tone' => 'Direto e estruturado', 'vocab' => 'Pensamentos automáticos, crenças centrais, distorções cognitivas'],
                        ['name' => 'Psicanálise', 'tone' => 'Contemplativo e aberto', 'vocab' => 'Inconsciente, história de vida, relações primárias, transferência'],
                        ['name' => 'Humanista', 'tone' => 'Caloroso e empático', 'vocab' => 'Autenticidade, potencial, experiência vivida, aceitação'],
                        ['name' => 'Sistêmica', 'tone' => 'Contextual e relacional', 'vocab' => 'Sistema familiar, dinâmica relacional, papéis, fronteiras'],
                        ['name' => 'Gestalt', 'tone' => 'Sensorial e presente', 'vocab' => 'Awareness, aqui-e-agora, experiência corporal, contato'],
                    ];
                @endphp

                @foreach($approaches as $i => $a)
                    <div data-animate data-delay="{{ $i + 1 }}" class="rounded-xl bg-[#2d2d2f] p-5">
                        <div class="mb-3 text-sm font-semibold text-white">{{ $a['name'] }}</div>
                        <div class="mb-2 text-xs text-[#a1a1a6]">
                            <span class="text-[#7C3AED]">Tom:</span> {{ $a['tone'] }}
                        </div>
                        <div class="text-xs leading-relaxed text-[#86868b]">{{ $a['vocab'] }}</div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 flex flex-wrap gap-x-8 gap-y-3">
                @foreach([
                    'Perguntas editáveis pelo psicólogo',
                    'Instruções personalizadas por profissional',
                    'Máximo de 15 mensagens por triagem',
                    'Custo incluso nos planos Pro e Consultório',
                ] as $i => $note)
                    <div class="flex items-center gap-2 text-sm text-[#a1a1a6]">
                        <svg class="h-3.5 w-3.5 flex-shrink-0 text-[#7C3AED]" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                        {{ $note }}
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── EXTRA FEATURES — Minimal grid ── --}}
    <section class="border-t border-[#d2d2d7]/40 px-6 py-24 lg:py-32">
        <div class="mx-auto max-w-[980px]">
            <h2 data-animate class="mb-14 text-center text-[32px] font-bold leading-[1.1] tracking-[-0.025em] sm:text-[40px]">
                E tem mais.
            </h2>

            <div class="grid gap-x-8 gap-y-10 sm:grid-cols-2 lg:grid-cols-3">
                @php
                    $extras = [
                        ['title' => 'Lista de Espera', 'desc' => 'Cancelou? O sistema oferece a vaga para o proximo da fila automaticamente.', 'icon' => '<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" /></svg>'],
                        ['title' => 'Score de Risco', 'desc' => 'Cada paciente tem um score de 0 a 100. Pacientes de risco recebem lembretes extras.', 'icon' => '<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg>'],
                        ['title' => 'Cobrança via WhatsApp', 'desc' => 'Link de pagamento PIX ou cartão enviado automaticamente. Você monitora, o sistema cobra.', 'icon' => '<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" /></svg>'],
                        ['title' => 'Recibos Automáticos', 'desc' => 'Após cada sessão, o recibo em PDF é gerado e enviado por WhatsApp.', 'icon' => '<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>'],
                        ['title' => 'Relatório Mensal', 'desc' => 'No dia 1 de cada mês, você recebe um PDF com todas as métricas do período.', 'icon' => '<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z" /></svg>'],
                        ['title' => 'Criptografia AES-256', 'desc' => 'Notas privadas criptografadas de ponta a ponta. Compliance total com LGPD.', 'icon' => '<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>'],
                    ];
                @endphp

                @foreach($extras as $i => $extra)
                    <div data-animate data-delay="{{ ($i % 3) + 1 }}" class="group">
                        <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-[#f5f5f7] text-[#1d1d1f] transition-colors group-hover:bg-[#0D9488] group-hover:text-white">
                            {!! $extra['icon'] !!}
                        </div>
                        <h3 class="mb-1 text-[15px] font-semibold text-[#1d1d1f]">{{ $extra['title'] }}</h3>
                        <p class="text-sm leading-relaxed text-[#86868b]">{{ $extra['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── TESTIMONIALS ── --}}
    <section class="bg-[#f5f5f7] px-6 py-24 lg:py-32">
        <div class="mx-auto max-w-[980px]">
            <h2 data-animate class="mb-14 text-center text-[32px] font-bold leading-[1.1] tracking-[-0.025em] sm:text-[40px]">
                Quem usa, recomenda.
            </h2>

            <div class="grid gap-5 sm:grid-cols-3">
                @php
                    $testimonials = [
                        ['name' => 'Dra. Camila R.', 'crp' => 'CRP 06', 'approach' => 'TCC', 'initials' => 'CR', 'text' => 'Reduzi minhas faltas em 70% no primeiro mês. Só os lembretes de WhatsApp já pagam o sistema.'],
                        ['name' => 'Dr. Marcos L.', 'crp' => 'CRP 04', 'approach' => 'Psicanálise', 'initials' => 'ML', 'text' => 'A triagem por IA mudou meu atendimento. Chego na primeira sessão já sabendo o contexto. Ganho 20 minutos.'],
                        ['name' => 'Dra. Ana Paula S.', 'crp' => 'CRP 05', 'approach' => 'Gestalt', 'initials' => 'AS', 'text' => 'Finalmente um sistema que não parece prontuário de hospital. Simples, bonito e funciona.'],
                    ];
                @endphp

                @foreach($testimonials as $i => $t)
                    <div data-animate data-delay="{{ $i + 1 }}" class="rounded-2xl bg-white p-8">
                        <p class="mb-6 text-[15px] leading-relaxed text-[#1d1d1f]">&ldquo;{{ $t['text'] }}&rdquo;</p>
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-[#2D6A4F] text-xs font-semibold text-white">{{ $t['initials'] }}</div>
                            <div>
                                <div class="text-sm font-semibold text-[#1d1d1f]">{{ $t['name'] }}</div>
                                <div class="text-xs text-[#86868b]">{{ $t['crp'] }} &middot; {{ $t['approach'] }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── PRICING ── --}}
    <section id="precos" class="px-6 py-24 lg:py-32">
        <div class="mx-auto max-w-[980px] text-center">
            <p data-animate class="text-sm font-medium tracking-wide text-[#0D9488] sm:text-base">Preços</p>
            <h2 data-animate data-delay="1" class="mt-3 text-[32px] font-bold leading-[1.1] tracking-[-0.025em] sm:text-[40px] lg:text-[48px]">
                Se evita uma falta,<br>já se pagou.
            </h2>
            <p data-animate data-delay="2" class="mt-4 mb-14 text-base text-[#86868b]">
                14 dias grátis. Sem cartão. Cancele quando quiser.
            </p>

            <div class="grid gap-5 sm:grid-cols-3">
                @php
                    $plans = [
                        ['name' => 'Solo', 'price' => '69', 'desc' => 'Para psicólogos autônomos', 'popular' => false, 'features' => ['1 profissional', 'Agenda inteligente', 'Lembretes WhatsApp ilimitados', 'Link de agendamento', 'Até 40 pacientes ativos', 'Notas criptografadas']],
                        ['name' => 'Profissional', 'price' => '99', 'desc' => 'O diferencial da IA', 'popular' => true, 'features' => ['Tudo do Solo +', 'Pacientes ilimitados', 'IA de triagem pré-consulta', 'Configuração por abordagem', 'Documentos e anexos', 'Relatório de faltas mensal']],
                        ['name' => 'Consultório', 'price' => '179', 'desc' => 'Para 2 a 5 profissionais', 'popular' => false, 'features' => ['Tudo do Profissional +', 'Até 5 psicólogos', 'Painel administrativo', 'Relatórios consolidados', 'Agenda compartilhada', 'Suporte prioritário']],
                    ];
                @endphp

                @foreach($plans as $i => $plan)
                    <div data-animate data-delay="{{ $i + 1 }}" class="relative rounded-2xl {{ $plan['popular'] ? 'bg-[#1d1d1f] text-white' : 'bg-[#f5f5f7]' }} p-8 text-left">
                        @if($plan['popular'])
                            <div class="absolute -top-3 left-1/2 -translate-x-1/2 rounded-full bg-[#0D9488] px-3.5 py-1 text-[11px] font-semibold text-white">
                                Mais popular
                            </div>
                        @endif

                        <div class="mb-1 text-lg font-semibold">{{ $plan['name'] }}</div>
                        <div class="mb-6 text-sm {{ $plan['popular'] ? 'text-[#a1a1a6]' : 'text-[#86868b]' }}">{{ $plan['desc'] }}</div>

                        <div class="mb-8 flex items-baseline gap-0.5">
                            <span class="text-sm {{ $plan['popular'] ? 'text-[#a1a1a6]' : 'text-[#86868b]' }}">R$</span>
                            <span class="text-[44px] font-bold leading-none tracking-tight">{{ $plan['price'] }}</span>
                            <span class="text-sm {{ $plan['popular'] ? 'text-[#a1a1a6]' : 'text-[#86868b]' }}">/mês</span>
                        </div>

                        <div class="mb-8 space-y-3">
                            @foreach($plan['features'] as $f)
                                <div class="flex items-center gap-2.5 text-sm {{ $plan['popular'] ? 'text-[#f5f5f7]' : 'text-[#1d1d1f]' }}">
                                    <svg class="h-4 w-4 flex-shrink-0 {{ $plan['popular'] ? 'text-[#0D9488]' : 'text-[#0D9488]' }}" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                    {{ $f }}
                                </div>
                            @endforeach
                        </div>

                        <a href="#comecar" class="flex w-full cursor-pointer items-center justify-center rounded-full py-3 text-sm font-semibold transition-all focus:outline-none focus:ring-2 focus:ring-[#0D9488] focus:ring-offset-2
                            {{ $plan['popular']
                                ? 'bg-[#0D9488] text-white hover:bg-[#0F766E]'
                                : 'bg-[#2D6A4F] text-white hover:bg-[#1B4332]' }}">
                            Começar grátis
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── FAQ ── --}}
    <section id="faq" class="border-t border-[#d2d2d7]/40 px-6 py-24 lg:py-32">
        <div class="mx-auto max-w-[680px]">
            <h2 data-animate class="mb-12 text-center text-[32px] font-bold leading-[1.1] tracking-[-0.025em] sm:text-[40px]">
                Perguntas frequentes
            </h2>

            @php
                $faqs = [
                    ['q' => 'Preciso instalar alguma coisa?', 'a' => 'Não. O Acolhe funciona 100% no navegador, no celular ou no computador. Sem instalação, sem app.'],
                    ['q' => 'Como funciona a integração com WhatsApp?', 'a' => 'Conectamos direto ao WhatsApp do seu consultório. Os lembretes são enviados automaticamente com seu nome, no tom humanizado que você configurar. Leva menos de 5 minutos.'],
                    ['q' => 'A IA de triagem substitui o psicólogo?', 'a' => 'De forma alguma. A IA apenas coleta informações iniciais antes da primeira sessão &mdash; como uma anamnese guiada. O paciente sempre sabe que está falando com uma assistente virtual. Você recebe o prontuário pronto.'],
                    ['q' => 'É seguro? E a LGPD?', 'a' => 'Todos os dados são criptografados. Notas privadas usam AES-256. Seguimos a LGPD com consentimento explícito, direito de exclusão e exportação de dados.'],
                    ['q' => 'Posso cancelar quando quiser?', 'a' => 'Sim, sem multa e sem burocracia. É mensal. Seus dados ficam disponíveis para exportação por 90 dias apos o cancelamento.'],
                    ['q' => 'Funciona para consultório com vários psicólogos?', 'a' => 'Sim. O plano Consultório permite ate 5 profissionais com agendas independentes e painel administrativo compartilhado.'],
                ];
            @endphp

            <div class="divide-y divide-[#d2d2d7]/40">
                @foreach($faqs as $i => $faq)
                    <div data-animate class="faq-item">
                        <button class="faq-trigger flex w-full cursor-pointer items-center justify-between py-5 text-left focus:outline-none" aria-expanded="false">
                            <span class="pr-6 text-[15px] font-semibold text-[#1d1d1f]">{!! $faq['q'] !!}</span>
                            <svg class="faq-icon h-4 w-4 flex-shrink-0 text-[#86868b] transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        </button>
                        <div class="faq-content hidden pb-5">
                            <p class="text-sm leading-relaxed text-[#86868b]">{!! $faq['a'] !!}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── CTA FINAL ── --}}
    <section id="comecar" class="bg-[#f5f5f7] px-6 py-24 lg:py-32">
        <div class="mx-auto max-w-xl text-center">
            <h2 data-animate class="text-[32px] font-bold leading-[1.1] tracking-[-0.025em] sm:text-[40px] lg:text-[48px]">
                Sua agenda merece<br>tanto cuidado quanto<br>seus pacientes.
            </h2>
            <p data-animate data-delay="1" class="mt-5 mb-8 text-base leading-relaxed text-[#86868b] lg:text-lg">
                14 dias grátis. Sem cartão. Configure em 10 minutos.
            </p>

            <form data-animate data-delay="2" class="mx-auto flex max-w-sm flex-col gap-3 sm:flex-row" onsubmit="return false;">
                <label for="email-cta" class="sr-only">Seu melhor email</label>
                <input
                    id="email-cta"
                    type="email"
                    placeholder="Seu email"
                    required
                    class="flex-1 rounded-full border border-[#d2d2d7] bg-white px-5 py-3 text-sm outline-none transition-all placeholder:text-[#86868b] focus:border-[#0D9488] focus:ring-4 focus:ring-[#0D9488]/10"
                >
                <button type="submit" class="cursor-pointer whitespace-nowrap rounded-full bg-[#0D9488] px-6 py-3 text-sm font-semibold text-white transition-all hover:bg-[#0F766E] focus:outline-none focus:ring-2 focus:ring-[#0D9488] focus:ring-offset-2">
                    Começar grátis
                </button>
            </form>

            <p data-animate data-delay="3" class="mt-4 text-xs text-[#86868b]">
                Sem spam. Sem compromisso. Só organizacao.
            </p>
        </div>
    </section>

    {{-- ── FOOTER ── --}}
    <footer class="border-t border-[#d2d2d7]/40 px-6 py-8">
        <div class="mx-auto flex max-w-[980px] flex-col items-center justify-between gap-4 sm:flex-row">
            <span class="text-xs text-[#86868b]">&copy; 2026 Acolhe. Feito para psicólogos</span>
            <div class="flex gap-6 text-xs text-[#424245]">
                <a href="#" class="transition-opacity hover:opacity-70">Termos de uso</a>
                <a href="#" class="transition-opacity hover:opacity-70">Privacidade</a>
                <a href="#" class="transition-opacity hover:opacity-70">Contato</a>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Scroll-triggered animations
            const observer = new IntersectionObserver(
                (entries) => entries.forEach((e) => { if (e.isIntersecting) e.target.classList.add('is-visible'); }),
                { threshold: 0.08, rootMargin: '0px 0px -40px 0px' }
            );
            document.querySelectorAll('[data-animate]').forEach((el) => observer.observe(el));

            // Navbar — frosted glass on scroll
            const nav = document.getElementById('navbar');
            const onScroll = () => {
                if (window.scrollY > 8) {
                    nav.style.background = 'rgba(255,255,255,0.72)';
                    nav.style.backdropFilter = 'saturate(180%) blur(20px)';
                    nav.style.webkitBackdropFilter = 'saturate(180%) blur(20px)';
                    nav.style.borderBottom = '0.5px solid rgba(0,0,0,0.08)';
                } else {
                    nav.style.background = 'transparent';
                    nav.style.backdropFilter = 'none';
                    nav.style.webkitBackdropFilter = 'none';
                    nav.style.borderBottom = 'none';
                }
            };
            window.addEventListener('scroll', onScroll, { passive: true });
            onScroll();

            // Mobile menu
            const menuBtn = document.getElementById('mobile-menu-btn');
            const menu = document.getElementById('mobile-menu');
            menuBtn.addEventListener('click', () => menu.classList.toggle('hidden'));
            menu.querySelectorAll('a').forEach((l) => l.addEventListener('click', () => menu.classList.add('hidden')));

            // FAQ accordion
            document.querySelectorAll('.faq-trigger').forEach((trigger) => {
                trigger.addEventListener('click', () => {
                    const item = trigger.closest('.faq-item');
                    const content = item.querySelector('.faq-content');
                    const icon = item.querySelector('.faq-icon');
                    const open = trigger.getAttribute('aria-expanded') === 'true';

                    // Close all
                    document.querySelectorAll('.faq-item').forEach((o) => {
                        if (o !== item) {
                            o.querySelector('.faq-content').classList.add('hidden');
                            o.querySelector('.faq-trigger').setAttribute('aria-expanded', 'false');
                            o.querySelector('.faq-icon').style.transform = '';
                        }
                    });

                    content.classList.toggle('hidden', open);
                    trigger.setAttribute('aria-expanded', !open);
                    icon.style.transform = open ? '' : 'rotate(45deg)';
                });
            });
        });
    </script>
</body>
</html>

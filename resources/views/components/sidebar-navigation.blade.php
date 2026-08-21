<ul role="list" class="flex flex-1 flex-col gap-y-1.5">
    @php
        $navItems = [
            ['name' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0 0 20.25 18V6A2.25 2.25 0 0 0 18 3.75H6A2.25 2.25 0 0 0 3.75 6v12A2.25 2.25 0 0 0 6 20.25Z'],
            ['name' => 'Clientes em potencial', 'route' => 'leads.index', 'icon' => 'M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z'],
            ['name' => 'Cotações & Propostas', 'route' => 'quotes.index', 'icon' => 'M15.75 15.75V18a2.25 2.25 0 0 1-2.25 2.25h-6A2.25 2.25 0 0 1 5.25 18v-2.25m10.5 0V9a2.25 2.25 0 0 0-2.25-2.25h-6A2.25 2.25 0 0 0 5.25 9v6.75m10.5 0h1.5a2.25 2.25 0 0 0 2.25-2.25v-3a2.25 2.25 0 0 0-2.25-2.25h-1.5m-10.5 7.5H3.75A2.25 2.25 0 0 1 1.5 15v-3a2.25 2.25 0 0 1 2.25-2.25H5.25'],
            ['name' => 'Segurados', 'route' => 'insureds.index', 'icon' => 'M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z'],
            ['name' => 'Apólices', 'route' => 'policies.index', 'icon' => 'm18.375 12.739-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13'],
            ['name' => 'Esteira de Renovações', 'route' => 'renewals.index', 'icon' => 'M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99'],
            ['name' => 'Sinistros', 'route' => 'claims.index', 'icon' => 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z'],
            ['name' => 'Financeiro', 'route' => 'financial.index', 'icon' => 'M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z'],
        ];
    @endphp

    @foreach($navItems as $item)
        @php 
            $isCurrent = $item['route'] !== '#' && (request()->routeIs($item['route']) || request()->routeIs(explode('.', $item['route'])[0] . '.*')); 
        @endphp
        <li>
            <a href="{{ $item['route'] !== '#' ? route($item['route']) : '#' }}"
               class="group relative flex items-center gap-x-3 rounded-2xl px-3 py-2.5 text-xs font-semibold transition-all duration-200 
                      {{ $isCurrent 
                         ? 'bg-[#295384] text-white shadow-md border border-blue-600/20 dark:bg-[#295384] dark:text-white dark:shadow-blue-900/30 dark:border-blue-400/20' 
                         : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-800/50' }}"
               :title="sidebarCollapsed ? '{{ $item['name'] }}' : ''"
            >
                <svg class="h-5 w-5 shrink-0 transition-transform group-hover:scale-110 {{ $isCurrent ? 'text-white' : 'text-slate-500 group-hover:text-slate-800 dark:text-slate-400 dark:group-hover:text-slate-200' }}" 
                     fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                </svg>

                <span x-show="!sidebarCollapsed" class="truncate tracking-wide">{{ $item['name'] }}</span>

                @if($isCurrent)
                    <span x-show="!sidebarCollapsed" class="ml-auto w-1.5 h-1.5 rounded-full bg-white dark:bg-[#B99B6C] animate-pulse shrink-0"></span>
                @endif
            </a>
        </li>
    @endforeach
</ul>
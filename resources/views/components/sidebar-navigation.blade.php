<ul role="list" class="flex flex-1 flex-col gap-y-7">
    <li>
        <ul role="list" class="-mx-2 space-y-1">
            @php
                $navItems = [
                    ['name' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25'],
                    ['name' => 'Leads', 'route' => 'leads.index', 'icon' => 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z'],
                    ['name' => 'Clientes', 'route' => '#', 'icon' => 'M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72'],
                    ['name' => 'Apólices', 'route' => '#', 'icon' => 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5A3.375 3.375 0 0010.125 2.25H3.75A1.125 1.125 0 002.625 3.375v17.25c0 .621.504 1.125 1.125 1.125h16.5a1.125 1.125 0 001.125-1.125v-6a3.375 3.375 0 00-3.375-3.375z'],
                    ['name' => 'Sinistros', 'route' => '#', 'icon' => 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z'],
                    ['name' => 'Financeiro', 'route' => '#', 'icon' => 'M12 6v12m-3-2.818l.214.016a3.25 3.25 0 003.111-2.422L12 9.75c0-.962-.835-1.742-1.875-1.742H9.75M12 13.5h.562c1.04 0 1.874-.79 1.874-1.742V10.5c0-.962-.834-1.742-1.874-1.742H12'],
                    ['name' => 'Relatórios', 'route' => '#', 'icon' => 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v5.625c0 .621-.504 1.125-1.125 1.125h-2.25A1.125 1.125 0 013 18.75v-5.625zm6.75-3.75m-3-3'],
                    ['name' => 'Configurações', 'route' => '#', 'icon' => 'M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94']
                ];
            @endphp

            @foreach($navItems as $item)
                @php $isCurrent = request()->routeIs($item['route']); @endphp
                <li>
                    <a href="{{ $item['route'] !== '#' ? route($item['route']) : '#' }}"
                       class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold transition-colors {{ $isCurrent ? 'bg-gray-50 text-[#295384]' : 'text-gray-700 hover:text-[#295384] hover:bg-gray-50' }}"
                       :title="sidebarCollapsed ? '{{ $item['name'] }}' : ''"
                    >
                        <svg class="h-6 w-6 shrink-0 text-gray-400 group-hover:text-[#295384]" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                        </svg>
                        <span x-show="!sidebarCollapsed" x-transition:enter="transition ease-out duration-100" class="truncate">{{ $item['name'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </li>
</ul>
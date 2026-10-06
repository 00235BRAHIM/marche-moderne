<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title ?? 'Marche Moderne' }}</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap"
        rel="stylesheet"
    >

    <style>
        body {
            font-family: Inter, sans-serif;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900">

<header class="sticky top-0 z-40 bg-white/95 backdrop-blur-xl border-b border-slate-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 h-16 flex items-center gap-3">

        <button
            type="button"
            id="mobileMenuButton"
            class="md:hidden w-10 h-10 flex items-center justify-center rounded-xl bg-slate-100 hover:bg-slate-200 transition text-xl"
            aria-label="Ouvrir le menu"
        >
            ☰
        </button>

        <a href="{{ route('home') }}" class="absolute left-1/2 -translate-x-1/2 md:static md:translate-x-0 font-extrabold text-xl sm:text-2xl whitespace-nowrap">
            <span class="text-indigo-600">Marche</span> Moderne
        </a>

        <form
            action="{{ route('products') }}"
            class="hidden md:flex flex-1 max-w-xl mx-auto"
        >
            <input
                name="q"
                value="{{ request('q') }}"
                placeholder="Rechercher..."
                class="w-full border border-slate-200 rounded-l-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-indigo-500"
            >

            <button class="bg-slate-900 text-white px-5 rounded-r-xl hover:bg-slate-800 transition">
                🔎
            </button>
        </form>

        <div class="ml-auto flex items-center gap-2">

            @auth

                @if(auth()->user()->isVendor())
                    <div class="relative md:hidden" id="mobileVendorNotificationWrapper">
                        <button
                            type="button"
                            id="mobileVendorNotificationButton"
                            class="relative w-10 h-10 flex items-center justify-center rounded-xl hover:bg-slate-100 transition"
                            title="Notifications"
                        >
                            <span class="text-xl">🔔</span>

                            <span
                                id="mobileVendorNotificationCount"
                                class="hidden absolute -top-1 -right-1 min-w-[20px] h-5 px-1 rounded-full bg-red-600 text-white text-[11px] font-bold items-center justify-center"
                            >0</span>
                        </button>

                        <div
                            id="mobileVendorNotificationPanel"
                            class="hidden fixed left-1/2 -translate-x-1/2 top-20 w-[calc(100vw-24px)] max-w-[360px] bg-white border border-slate-200 rounded-2xl shadow-2xl overflow-hidden z-50"
                        >
                            <div class="px-4 py-3 border-b flex items-center justify-between">
                                <div>
                                    <h3 class="font-bold text-slate-900">
                                        Notifications
                                    </h3>
                                    <p class="text-xs text-slate-500">
                                        Nouvelles activités
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    id="mobileVendorNotificationReadAll"
                                    class="text-xs font-semibold text-indigo-600 hover:text-indigo-800"
                                >
                                    Tout lire
                                </button>
                            </div>

                            <div
                                id="mobileVendorNotificationList"
                                class="max-h-[420px] overflow-y-auto"
                            >
                                <div class="p-6 text-center text-sm text-slate-500">
                                    Chargement...
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if(auth()->user()->role === 'customer')
                    <div class="relative md:hidden" id="mobileCustomerNotificationWrapper">
                        <button
                            type="button"
                            id="mobileCustomerNotificationButton"
                            class="relative w-10 h-10 flex items-center justify-center rounded-xl hover:bg-slate-100 transition"
                            title="Notifications"
                        >
                            <span class="text-xl">🔔</span>

                            <span
                                id="mobileCustomerNotificationCount"
                                class="hidden absolute -top-1 -right-1 min-w-[20px] h-5 px-1 rounded-full bg-red-600 text-white text-[11px] font-bold items-center justify-center"
                            >0</span>
                        </button>

                        <div
                            id="mobileCustomerNotificationPanel"
                            class="hidden fixed left-1/2 -translate-x-1/2 top-20 w-[calc(100vw-24px)] max-w-[360px] bg-white border border-slate-200 rounded-2xl shadow-2xl overflow-hidden z-50"
                        >
                            <div class="px-4 py-3 border-b flex items-center justify-between">
                                <div>
                                    <h3 class="font-bold text-slate-900">
                                        Notifications
                                    </h3>
                                    <p class="text-xs text-slate-500">
                                        Suivi de vos commandes
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    id="mobileCustomerNotificationReadAll"
                                    class="text-xs font-semibold text-indigo-600 hover:text-indigo-800"
                                >
                                    Tout lire
                                </button>
                            </div>

                            <div
                                id="mobileCustomerNotificationList"
                                class="max-h-[420px] overflow-y-auto"
                            >
                                <div class="p-6 text-center text-sm text-slate-500">
                                    Chargement...
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <a
                    href="{{ route('cart') }}"
                    class="relative w-10 h-10 flex items-center justify-center rounded-xl hover:bg-slate-100 transition"
                    title="Panier"
                >
                    <span class="text-xl">🛒</span>

                    @php
                        $cartCount = auth()->user()->cart?->items->sum('quantity') ?? 0;
                    @endphp

                    @if($cartCount > 0)
                        <span class="absolute -top-1 -right-1 min-w-[20px] h-5 px-1 rounded-full bg-red-500 text-white text-[11px] font-bold flex items-center justify-center">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>
            @endauth

        </div>

        <nav class="hidden md:flex ml-auto gap-4 items-center text-sm">

            <a
                href="{{ route('shops.index') }}"
                class="font-medium hover:text-indigo-600 transition"
            >
                🏪 Nos boutiques
            </a>

            @if(session('public_shop_slug'))
                <a
                    href="{{ route('shop.public', session('public_shop_slug')) }}"
                    class="font-medium hover:text-indigo-600 transition"
                >
                    {{ session('public_shop_name') ?: 'Ma boutique' }}
                </a>
            @else
                <a
                    href="{{ route('products') }}"
                    class="font-medium hover:text-indigo-600 transition"
                >
                    Boutique
                </a>
            @endif

            @auth



                @if(auth()->user()->isVendor())
                    <div class="relative" id="vendorNotificationWrapper">
                        <button
                            type="button"
                            id="vendorNotificationButton"
                            class="relative w-10 h-10 flex items-center justify-center rounded-xl hover:bg-slate-100 transition"
                            title="Notifications"
                        >
                            <span class="text-xl">🔔</span>

                            <span
                                id="vendorNotificationCount"
                                class="hidden absolute -top-1 -right-1 min-w-[20px] h-5 px-1 rounded-full bg-red-600 text-white text-[11px] font-bold items-center justify-center"
                            >0</span>
                        </button>

                        <div
                            id="vendorNotificationPanel"
                            class="hidden absolute right-0 top-12 w-[380px] max-w-[90vw] bg-white border border-slate-200 rounded-2xl shadow-2xl overflow-hidden z-50"
                        >
                            <div class="px-4 py-3 border-b flex items-center justify-between">
                                <div>
                                    <h3 class="font-bold text-slate-900">
                                        Notifications
                                    </h3>
                                    <p class="text-xs text-slate-500">
                                        Nouvelles commandes
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    id="vendorNotificationReadAll"
                                    class="text-xs font-semibold text-indigo-600 hover:text-indigo-800"
                                >
                                    Tout lire
                                </button>
                            </div>

                            <div
                                id="vendorNotificationList"
                                class="max-h-[420px] overflow-y-auto"
                            >
                                <div class="p-6 text-center text-sm text-slate-500">
                                    Chargement...
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if(auth()->user()->role === 'customer')
                    <div class="relative" id="customerNotificationWrapper">
                        <button
                            type="button"
                            id="customerNotificationButton"
                            class="relative w-10 h-10 flex items-center justify-center rounded-xl hover:bg-slate-100 transition"
                            title="Notifications"
                        >
                            <span class="text-xl">🔔</span>

                            <span
                                id="customerNotificationCount"
                                class="hidden absolute -top-1 -right-1 min-w-[20px] h-5 px-1 rounded-full bg-red-600 text-white text-[11px] font-bold items-center justify-center"
                            >0</span>
                        </button>

                        <div
                            id="customerNotificationPanel"
                            class="hidden absolute right-0 top-12 w-[390px] max-w-[90vw] bg-white border border-slate-200 rounded-2xl shadow-2xl overflow-hidden z-50"
                        >
                            <div class="px-4 py-3 border-b flex items-center justify-between">
                                <div>
                                    <h3 class="font-bold text-slate-900">
                                        Notifications
                                    </h3>
                                    <p class="text-xs text-slate-500">
                                        Suivi de vos commandes
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    id="customerNotificationReadAll"
                                    class="text-xs font-semibold text-indigo-600 hover:text-indigo-800"
                                >
                                    Tout lire
                                </button>
                            </div>

                            <div
                                id="customerNotificationList"
                                class="max-h-[420px] overflow-y-auto"
                            >
                                <div class="p-6 text-center text-sm text-slate-500">
                                    Chargement...
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <a href="{{ route('dashboard') }}">
                    Dashboard
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button>
                        Déconnecter
                    </button>
                </form>

            @else

                <a href="{{ route('login') }}">
                    Connexion
                </a>

                <a
                    class="bg-indigo-600 text-white px-4 py-2 rounded-xl"
                    href="{{ route('register') }}"
                >
                    Créer un compte
                </a>

            @endauth

        </nav>
    </div>
</header>
<!-- Menu mobile -->
<div
    id="mobileMenu"
    class="hidden md:hidden fixed inset-0 z-50"
>
    <div
        id="mobileMenuOverlay"
        class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"
    ></div>

    <aside
        class="absolute left-0 top-0 bottom-0 w-[85%] max-w-sm bg-white shadow-2xl overflow-y-auto"
    >
        <div class="h-16 px-4 flex items-center justify-between border-b">
            <a
                href="{{ route('home') }}"
                class="font-extrabold text-xl"
            >
                <span class="text-indigo-600">Marche</span> Moderne
            </a>

            <button
                type="button"
                id="mobileMenuClose"
                class="w-10 h-10 rounded-xl bg-slate-100 text-xl"
                aria-label="Fermer le menu"
            >
                ✕
            </button>
        </div>

        <div class="p-4 space-y-2">

            <a
                href="{{ route('home') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-50 hover:text-indigo-600 transition font-semibold"
            >
                🏠
                <span>Accueil</span>
            </a>

            <a
                href="{{ route('shops.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-50 hover:text-indigo-600 transition font-semibold"
            >
                🏪
                <span>Nos boutiques</span>
            </a>

            @if(session('public_shop_slug'))
                <a
                    href="{{ route('shop.public', session('public_shop_slug')) }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-50 hover:text-indigo-600 transition font-semibold"
                >
                    🏬
                    <span>{{ session('public_shop_name') ?: 'Ma boutique' }}</span>
                </a>
            @else
                <a
                    href="{{ route('products') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-50 hover:text-indigo-600 transition font-semibold"
                >
                    🛍️
                    <span>Boutique</span>
                </a>
            @endif

            @auth
                @php
                    $mobileCartCount = auth()->user()->cart?->items->sum('quantity') ?? 0;
                @endphp

                <a
                    href="{{ route('cart') }}"
                    class="flex items-center justify-between px-4 py-3 rounded-xl hover:bg-indigo-50 hover:text-indigo-600 transition font-semibold"
                >
                    <span class="flex items-center gap-3">
                        🛒
                        <span>Panier</span>
                    </span>

                    @if($mobileCartCount > 0)
                        <span class="min-w-[24px] h-6 px-1.5 rounded-full bg-red-500 text-white text-xs font-bold flex items-center justify-center">
                            {{ $mobileCartCount }}
                        </span>
                    @endif
                </a>

                <a
                    href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-50 hover:text-indigo-600 transition font-semibold"
                >
                    📊
                    <span>Dashboard</span>
                </a>

                <div class="my-3 border-t"></div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button
                        type="submit"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-red-600 hover:bg-red-50 transition font-semibold text-left"
                    >
                        🚪
                        <span>Déconnecter</span>
                    </button>
                </form>
            @else

                <div class="my-3 border-t"></div>

                <a
                    href="{{ route('login') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-50 hover:text-indigo-600 transition font-semibold"
                >
                    🔐
                    <span>Connexion</span>
                </a>

                <a
                    href="{{ route('register') }}"
                    class="flex items-center justify-center gap-2 bg-indigo-600 text-white px-4 py-3 rounded-xl font-bold hover:bg-indigo-700 transition"
                >
                    ✨
                    <span>Créer un compte</span>
                </a>

            @endauth
        </div>
    </aside>
</div>



@if(session('warning'))
    <div class="max-w-7xl mx-auto px-4 pt-4">
        <div class="bg-amber-50 text-amber-800 p-3 rounded-xl">
            {{ session('warning') }}
        </div>
    </div>
@endif


@if(session('success'))
    <div class="max-w-7xl mx-auto px-4 pt-4">
        <div class="bg-emerald-50 text-emerald-700 p-3 rounded-xl">
            {{ session('success') }}
        </div>
    </div>
@endif


@if($errors->any())
    <div class="max-w-7xl mx-auto px-4 pt-4">
        <div class="bg-red-50 text-red-700 p-3 rounded-xl">
            {{ $errors->first() }}
        </div>
    </div>
@endif


@yield('content')


<footer class="mt-20 border-t bg-white">
    <div class="max-w-7xl mx-auto px-4 py-10 flex justify-between">

        <b>
            <span class="text-indigo-600">Marche</span> Moderne
        </b>

        <span class="text-slate-400">
            © {{ date('Y') }}
        </span>

    </div>
</footer>


@if(auth()->check() && auth()->user()->isVendor())
<script>
document.addEventListener('DOMContentLoaded', function () {

    const elements = {
        desktop: {
            button: document.getElementById('vendorNotificationButton'),
            panel: document.getElementById('vendorNotificationPanel'),
            wrapper: document.getElementById('vendorNotificationWrapper'),
            count: document.getElementById('vendorNotificationCount'),
            list: document.getElementById('vendorNotificationList'),
            readAll: document.getElementById('vendorNotificationReadAll')
        },
        mobile: {
            button: document.getElementById('mobileVendorNotificationButton'),
            panel: document.getElementById('mobileVendorNotificationPanel'),
            wrapper: document.getElementById('mobileVendorNotificationWrapper'),
            count: document.getElementById('mobileVendorNotificationCount'),
            list: document.getElementById('mobileVendorNotificationList'),
            readAll: document.getElementById('mobileVendorNotificationReadAll')
        }
    };

    if (!elements.desktop.button && !elements.mobile.button) {
        return;
    }

    function updateCount(total) {
        total = Number(total || 0);

        [elements.desktop.count, elements.mobile.count].forEach(function (badge) {
            if (!badge) return;

            badge.textContent = total > 99 ? '99+' : total;

            if (total > 0) {
                badge.classList.remove('hidden');
                badge.classList.add('flex');
            } else {
                badge.classList.add('hidden');
                badge.classList.remove('flex');
            }
        });
    }

    function updateLists(html) {
        if (elements.desktop.list) {
            elements.desktop.list.innerHTML = html;
        }

        if (elements.mobile.list) {
            elements.mobile.list.innerHTML = html;
        }
    }

    function closePanels() {
        [elements.desktop.panel, elements.mobile.panel].forEach(function (panel) {
            if (panel) {
                panel.classList.add('hidden');
            }
        });
    }

    function togglePanel(panel) {
        if (!panel) return;

        const isHidden = panel.classList.contains('hidden');

        closePanels();

        if (isHidden) {
            panel.classList.remove('hidden');
        }
    }

    async function loadVendorNotifications() {
        try {
            const response = await fetch('{{ route('vendor.notifications') }}', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error('Erreur HTTP ' + response.status);
            }

            const data = await response.json();

            updateCount(data.count);

            if (!data.notifications || data.notifications.length === 0) {

                updateLists(`
                    <div class="p-10 text-center">
                        <div class="w-14 h-14 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-2xl mb-3">
                            🔕
                        </div>

                        <p class="font-semibold text-slate-700">
                            Aucune nouvelle notification
                        </p>

                        <p class="text-xs text-slate-400 mt-1">
                            Les nouvelles activités apparaîtront ici.
                        </p>
                    </div>
                `);

                return;
            }

            const html = data.notifications.map(function (notification) {

                const total = new Intl.NumberFormat('fr-FR').format(
                    Number(notification.total || 0)
                );

                const isPayment =
                    notification.type === 'vendor_payment_proof';

                const isSubscription =
                    notification.type === 'vendor_subscription_approved';

                const isAdminActivity =
                    notification.type === 'vendor_admin_activity';

                let icon = '🔔';
                let title = notification.title || 'Notification';
                let message = notification.message || '';

                if (isPayment) {
                    icon = '💳';
                    title = notification.title || 'Paiement reçu';
                } else if (isSubscription) {
                    icon = '🎉';
                    title = notification.title || 'Abonnement validé';
                } else if (isAdminActivity) {
                    icon = notification.icon || '📢';
                } else if (notification.type === 'new_vendor_order') {
                    icon = '🛒';
                    title = notification.title || 'Nouvelle commande';
                }

                if (notification.total && !message) {
                    message = 'Montant : ' + total + ' FCFA';
                }

                const url = notification.url || '#';

                return `
                    <a
                        href="${url}"
                        data-vendor-notification-link
                        data-id="${notification.id}"
                        class="block px-4 py-4 border-b border-slate-100 hover:bg-slate-50 transition"
                    >
                        <div class="flex gap-3">

                            <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-xl shrink-0">
                                ${icon}
                            </div>

                            <div class="min-w-0 flex-1">

                                <p class="font-bold text-slate-900 text-sm">
                                    ${title}
                                </p>

                                <p class="text-xs text-slate-500 mt-1 leading-5">
                                    ${message}
                                </p>

                                ${
                                    notification.created_at
                                    ? `
                                        <p class="text-[11px] text-slate-400 mt-2">
                                            ${notification.created_at}
                                        </p>
                                      `
                                    : ''
                                }

                            </div>
                        </div>
                    </a>
                `;
            }).join('');

            updateLists(html);

            document.querySelectorAll('[data-vendor-notification-link]')
                .forEach(link => {
                    link.addEventListener('click', async function (event) {
                        event.preventDefault();

                        const notificationId = this.dataset.id;
                        const targetUrl = this.href;

                        try {
                            await deleteVendorNotification(notificationId);
                            window.location.href = targetUrl;
                        } catch (error) {
                            console.error('Suppression notification vendeur:', error);
                            window.location.href = targetUrl;
                        }
                    });
                });



        } catch (error) {

            console.error(
                'Erreur lors du chargement des notifications vendeur :',
                error
            );

            updateLists(`
                <div class="p-8 text-center">
                    <div class="text-2xl mb-2">⚠️</div>
                    <p class="text-sm font-semibold text-red-500">
                        Impossible de charger les notifications.
                    </p>
                </div>
            `);
        }
    }

    async function deleteVendorNotification(id) {
        const response = await fetch(
            '{{ url('/vendor/notifications') }}/' + id + '/read',
            {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }
        );

        if (!response.ok) {
            throw new Error('Impossible de supprimer la notification.');
        }

        updateCount(0);
        await loadVendorNotifications();
    }

    async function markAllAsRead() {
        try {

            const response = await fetch(
                '{{ route('vendor.notifications.read-all') }}',
                {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN':
                            document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                }
            );

            if (!response.ok) {
                throw new Error('Erreur HTTP ' + response.status);
            }

            updateCount(0);

            await loadVendorNotifications();

        } catch (error) {

            console.error(
                'Erreur lors du marquage des notifications :',
                error
            );
        }
    }

    if (elements.desktop.button) {
        elements.desktop.button.addEventListener('click', function (event) {
            event.stopPropagation();

            togglePanel(elements.desktop.panel);

            if (!elements.desktop.panel.classList.contains('hidden')) {
                loadVendorNotifications();
            }
        });
    }

    if (elements.mobile.button) {
        elements.mobile.button.addEventListener('click', function (event) {
            event.stopPropagation();

            togglePanel(elements.mobile.panel);

            if (!elements.mobile.panel.classList.contains('hidden')) {
                loadVendorNotifications();
            }
        });
    }

    if (elements.desktop.readAll) {
        elements.desktop.readAll.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            markAllAsRead();
        });
    }

    if (elements.mobile.readAll) {
        elements.mobile.readAll.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            markAllAsRead();
        });
    }

    document.addEventListener('click', function (event) {

        const insideDesktop =
            elements.desktop.wrapper &&
            elements.desktop.wrapper.contains(event.target);

        const insideMobile =
            elements.mobile.wrapper &&
            elements.mobile.wrapper.contains(event.target);

        if (!insideDesktop && !insideMobile) {
            closePanels();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closePanels();
        }
    });

    loadVendorNotifications();

    setInterval(function () {
        loadVendorNotifications();
    }, 30000);

});
</script>
@endif


@if(auth()->check() && auth()->user()->role === 'customer')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const button = document.getElementById('customerNotificationButton');
    const panel = document.getElementById('customerNotificationPanel');
    const wrapper = document.getElementById('customerNotificationWrapper');
    const count = document.getElementById('customerNotificationCount');
    const list = document.getElementById('customerNotificationList');
    const readAllButton = document.getElementById('customerNotificationReadAll');

    const mobileButton = document.getElementById('mobileCustomerNotificationButton');
    const mobilePanel = document.getElementById('mobileCustomerNotificationPanel');
    const mobileWrapper = document.getElementById('mobileCustomerNotificationWrapper');
    const mobileCount = document.getElementById('mobileCustomerNotificationCount');
    const mobileList = document.getElementById('mobileCustomerNotificationList');
    const mobileReadAllButton = document.getElementById('mobileCustomerNotificationReadAll');

    if (!button && !mobileButton) {
        return;
    }

    function updateCustomerCount(total) {
        total = Number(total || 0);

        [count, mobileCount].forEach(function (badge) {
            if (!badge) return;

            badge.textContent = total > 99 ? '99+' : total;

            if (total > 0) {
                badge.classList.remove('hidden');
                badge.classList.add('flex');
            } else {
                badge.classList.add('hidden');
                badge.classList.remove('flex');
            }
        });
    }

    function updateCustomerLists(html) {
        if (list) {
            list.innerHTML = html;
        }

        if (mobileList) {
            mobileList.innerHTML = html;
        }
    }

    function closeCustomerPanels() {
        [panel, mobilePanel].forEach(function (element) {
            if (element) {
                element.classList.add('hidden');
            }
        });
    }

    function toggleCustomerPanel(targetPanel) {
        if (!targetPanel) return;

        const isHidden = targetPanel.classList.contains('hidden');

        closeCustomerPanels();

        if (isHidden) {
            targetPanel.classList.remove('hidden');
        }
    }

    async function loadCustomerNotifications() {
        try {
            const response = await fetch('{{ route('notifications') }}', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error('Erreur HTTP ' + response.status);
            }

            const data = await response.json();

            if (data.count > 0) {
                updateCustomerCount(data.count);
            } else {
                updateCustomerCount(0);
            }

            if (!data.notifications || data.notifications.length === 0) {
                updateCustomerLists(`
                    <div class="p-10 text-center">
                        <div class="w-14 h-14 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-2xl mb-3">
                            🔕
                        </div>
                        <p class="font-semibold text-slate-700">Aucune notification</p>
                        <p class="text-xs text-slate-400 mt-1">
                            Vous serez informé des mises à jour de vos commandes.
                        </p>
                    </div>
                `);
                return;
            }

            const notificationsHtml = data.notifications.map(notification => {
                const total = new Intl.NumberFormat('fr-FR').format(
                    Number(notification.total || 0)
                );

                let iconBg = 'bg-indigo-100';
                let iconText = 'text-indigo-700';

                if (notification.event === 'payment_approved') {
                    iconBg = 'bg-emerald-100';
                    iconText = 'text-emerald-700';
                } else if (notification.event === 'payment_rejected') {
                    iconBg = 'bg-red-100';
                    iconText = 'text-red-700';
                } else if (notification.event === 'delivered') {
                    iconBg = 'bg-blue-100';
                    iconText = 'text-blue-700';
                }

                return `
                    <div
                        class="group border-b border-slate-100 last:border-b-0
                               ${notification.read ? 'bg-white' : 'bg-indigo-50/70'}
                               hover:bg-slate-50 transition"
                    >
                        <a
                            href="${notification.url}"
                            class="block p-4"
                            data-customer-notification-link
                            data-id="${notification.id}"
                        >
                            <div class="flex gap-3">
                                <div class="w-11 h-11 rounded-2xl ${iconBg} ${iconText}
                                            flex items-center justify-center text-xl
                                            flex-shrink-0 shadow-sm">
                                    ${notification.icon || '🔔'}
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-2">
                                        <div>
                                            <p class="font-bold text-sm text-slate-900">
                                                ${notification.title || 'Mise à jour'}
                                            </p>

                                            <p class="text-xs text-slate-500 mt-1">
                                                Commande
                                                <span class="font-semibold text-slate-700">
                                                    ${notification.order_number || '-'}
                                                </span>
                                            </p>
                                        </div>

                                        ${
                                            !notification.read
                                            ? '<span class="w-2.5 h-2.5 rounded-full bg-red-500 flex-shrink-0 mt-1.5 shadow-sm"></span>'
                                            : ''
                                        }
                                    </div>

                                    ${
                                        notification.message
                                        ? `<p class="text-xs text-slate-600 mt-2 leading-5">
                                            ${notification.message}
                                           </p>`
                                        : ''
                                    }

                                    <div class="flex flex-wrap items-center gap-3 mt-3">
                                        <span class="text-sm font-bold text-indigo-600">
                                            ${total} FCFA
                                        </span>

                                        <span class="text-[11px] text-slate-400">
                                            📅 ${notification.created_at}
                                        </span>

                                        <span class="text-[11px] text-slate-400">
                                            🕐 ${notification.time}
                                        </span>
                                    </div>

                                    <div class="mt-3 inline-flex items-center gap-1
                                                text-xs font-bold text-indigo-600
                                                group-hover:text-indigo-800">
                                        Voir la commande
                                        <span class="transition-transform group-hover:translate-x-1">→</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                `;
            }).join('');

            updateCustomerLists(notificationsHtml);

            document.querySelectorAll('[data-customer-notification-link]')
                .forEach(link => {
                    link.addEventListener('click', async function (event) {
                        event.preventDefault();

                        const notificationId = this.dataset.id;
                        const targetUrl = this.href;

                        try {
                            await deleteNotification(notificationId, this);
                            window.location.href = targetUrl;
                        } catch (error) {
                            console.error('Suppression notification:', error);
                        }
                    });
                });

        } catch (error) {
            console.error('Notifications client:', error);

            updateCustomerLists(`
                <div class="p-8 text-center">
                    <div class="text-2xl mb-2">⚠️</div>
                    <p class="text-sm text-red-500">
                        Impossible de charger les notifications.
                    </p>
                </div>
            `);
        }
    }

    async function deleteNotification(id, element) {
        const response = await fetch(
            '{{ url('/notifications') }}/' + id + '/read',
            {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }
        );

        if (!response.ok) {
            throw new Error('Impossible de supprimer la notification.');
        }

        const data = await response.json();

        if (element) {
            const notificationItem = element.closest('[data-customer-notification-link]')?.parentElement;

            if (notificationItem) {
                notificationItem.remove();
            }
        }

        updateCustomerCount(data.count ?? 0);
    }

    if (button) {
        button.addEventListener('click', function (event) {
            event.stopPropagation();

            toggleCustomerPanel(panel);

            if (panel && !panel.classList.contains('hidden')) {
                loadCustomerNotifications();
            }
        });
    }

    if (mobileButton) {
        mobileButton.addEventListener('click', function (event) {
            event.stopPropagation();

            toggleCustomerPanel(mobilePanel);

            if (mobilePanel && !mobilePanel.classList.contains('hidden')) {
                loadCustomerNotifications();
            }
        });
    }

    if (mobileReadAllButton) {
        mobileReadAllButton.addEventListener('click', async function (event) {
            event.preventDefault();
            event.stopPropagation();

            if (readAllButton) {
                readAllButton.click();
            }
        });
    }

    document.addEventListener('click', function (event) {
        const insideDesktop =
            wrapper && wrapper.contains(event.target);

        const insideMobile =
            mobileWrapper && mobileWrapper.contains(event.target);

        if (!insideDesktop && !insideMobile) {
            closeCustomerPanels();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeCustomerPanels();
        }
    });

    if (readAllButton) {
        readAllButton.addEventListener('click', async function () {
            try {
                const response = await fetch(
                    '{{ route('notifications.read-all') }}',
                    {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    }
                );

                if (!response.ok) {
                    throw new Error('Impossible de supprimer les notifications.');
                }

                await loadCustomerNotifications();

            } catch (error) {
                console.error('Suppression notifications:', error);
            }
        });
    }

    button.addEventListener('click', function (event) {
        event.stopPropagation();
        panel.classList.toggle('hidden');

        if (!panel.classList.contains('hidden')) {
            loadCustomerNotifications();
        }
    });

    document.addEventListener('click', function (event) {
        if (!wrapper.contains(event.target)) {
            panel.classList.add('hidden');
        }
    });

    loadCustomerNotifications();

    setInterval(loadCustomerNotifications, 10000);
});
</script>
@endif
</body>
</html>



<script>
document.addEventListener('DOMContentLoaded', function () {
    const menu = document.getElementById('mobileMenu');
    const openButton = document.getElementById('mobileMenuButton');
    const closeButton = document.getElementById('mobileMenuClose');
    const overlay = document.getElementById('mobileMenuOverlay');

    if (!menu || !openButton) return;

    function openMobileMenu() {
        menu.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeMobileMenu() {
        menu.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    openButton.addEventListener('click', openMobileMenu);

    if (closeButton) {
        closeButton.addEventListener('click', closeMobileMenu);
    }

    if (overlay) {
        overlay.addEventListener('click', closeMobileMenu);
    }

    menu.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', closeMobileMenu);
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeMobileMenu();
        }
    });
});
</script>

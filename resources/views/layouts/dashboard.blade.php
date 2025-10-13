<!DOCTYPE html>
<html lang="en" x-data="{ open: false }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <script src="//unpkg.com/alpinejs" defer></script>
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex h-screen bg-gray-100" style="font-family: 'Barlow', sans-serif;" x-data>

    <!-- Overlay (mobile only) -->
    <div 
        class="fixed inset-0 bg-black bg-opacity-50 z-30 md:hidden"
        x-show="open"
        x-transition.opacity
        @click="open = false">
    </div>

    <!-- Sidebar -->
    <aside 
        class="fixed inset-y-0 left-0 w-64 bg-white shadow-lg z-40 transform -translate-x-full md:translate-x-0 md:relative md:flex md:flex-col transition-transform duration-300"
        :class="{ 'translate-x-0': open }">
        
        <!-- Header -->
        <div class="p-6 border-b flex justify-between items-center">
            <h1 class="text-xl font-bold uppercase text-gray-800">Dashboard POMS</h1>
            <button class="md:hidden text-gray-600" @click="open = false">✕</button>
        </div>

        <!-- User Info -->
        <div class="flex align-center justify-center p-3 border-b">
            @auth
                <img src="https://www.gravatar.com/avatar/{{ md5(strtolower(trim(Auth::user()->email))) }}?s=80&d=identicon" alt="User Avatar" class="w-16 h-16 rounded-full mr-4">
                <div>
                    <p class="text-gray-800 font-semibold">{{ Auth::user()->name }}</p>
                    <p class="text-sm text-gray-500 capitalize">{{ Auth::user()->role }}</p>
                </div>
            @endauth
        </div>

        <!-- Navigation -->
        <nav class="flex-1 p-4">
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('dashboard') }}" class="relative bg-gray-200 block px-4 py-2 rounded-lg text-xs font-bold tracking-wider uppercase hover:bg-black hover:text-white">Home</a>
                </li>
                <li>
                    <a href="#" class="relative bg-gray-200 block px-4 py-2 rounded-lg text-xs font-bold tracking-wider uppercase hover:bg-black hover:text-white">Customer</a>
                </li>
                @php
                    use Illuminate\Support\Facades\Auth;
                    use App\Models\PurchaseOrder;

                    $user = Auth::user();
                    $badgeCount = 0;

                    if ($user->role === 'FINANCE') {
                        // Finance → PO menunggu approval finance
                        $badgeCount = PurchaseOrder::where('status', 'PENDING_FINANCE')->count();
                    }

                    if ($user->role === 'PRODUKSI') {
                        // Produksi → PO sudah approved finance
                        $badgeCount = PurchaseOrder::where('status', 'QUEUE_FINANCE')->count();
                    }

                    if ($user->role === 'SHIPPER') {
                        // Shipper → PO sudah siap dikirim
                        $badgeCount = PurchaseOrder::where('shipping_status', 'READY_TO_SHIP')->count();
                    }
                @endphp
                <li>
                    <a href="{{ route('purchase-orders.index') }}" class="relative bg-gray-200 block px-4 py-2 rounded-lg text-xs font-bold tracking-wider uppercase hover:bg-black hover:text-white">Purchase Order
                        @if($badgeCount > 0)
                        <span class="absolute top-0.5 right-0.5 grid min-h-[24px] min-w-[24px] translate-x-2/4 -translate-y-2/4 place-items-center rounded-full bg-red-600 py-1 px-1 text-xs text-white">{{ $badgeCount }}</span>
                        @endif
                    </a>
                </li>
                <li>
                    <a href="#"class="relative bg-gray-200 block px-4 py-2 rounded-lg text-xs font-bold tracking-wider uppercase hover:bg-black hover:text-white">Settings
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Logout -->
        <div class="p-6 border-t">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                        class="w-full text-left px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                    Logout
                </button>
            </form>
            <p class="text-xs text-gray-400 mt-4">&copy; 2025 - fudge</p>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
        <!-- Topbar (Mobile) -->
        <header class="md:hidden flex items-center justify-between bg-white shadow px-4 py-3">
            <div class="font-bold">Dashboard</div>
            <button @click="open = true" class="p-2 rounded-md bg-gray-100">
                ☰
            </button>
        </header>

        <main class="p-6 flex-1 overflow-y-auto">
            @yield('content')
        </main>

        <!-- Modal Preview Desain Approve -->
        <div x-data
        x-show="$store.imageModal.open"
        @keydown.escape.window="$store.imageModal.close()"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/70"
        x-cloak>
        <div class="bg-white rounded-xl p-4 max-w-3xl w-full mx-4 relative">
            <button @click="$store.imageModal.close()" class="absolute top-2 right-2 text-center text-gray-500 hover:text-gray-700">✕</button>
            <img :src="$store.imageModal.imageUrl" class="rounded-lg mx-auto max-h-[80vh] object-contain">
        </div>
    </div>

    </div>
    {{-- Toastr calls --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
        @if(session('success'))
            toastr.success(@json(session('success')));
        @endif

        @if(session('error'))
            toastr.error(@json(session('error')));
        @endif

        @if($errors->any())
            @foreach ($errors->all() as $error)
            toastr.error(@json($error));
            @endforeach
        @endif
        });
    </script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('imageModal', {
                open: false,
                imageUrl: '',
                show(url) {
                    this.imageUrl = url;
                    this.open = true;
                },
                close() {
                    this.open = false;
                    this.imageUrl = '';
                }
            });
        });
    </script>
</body>
</html>

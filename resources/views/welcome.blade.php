<!DOCTYPE html>
<html lang="th" data-theme="deepdive">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ฟาร์มอัจฉริยะ - Tech Polish</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite('resources/css/app.css')

    <style>
        body {
            font-family: 'Kanit', sans-serif;
        }
        .glow-filter {
            filter: drop-shadow(0 0 5px rgba(52, 211, 153, 0.7));
        }
    </style>
</head>
<body class="text-base-content bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-green-900/20 via-slate-950 to-black">

    <!-- Fixed Navbar -->
    <header class="navbar bg-slate-950/30 backdrop-blur-md fixed top-0 z-50 border-b border-white/5">
        <div class="container mx-auto max-w-7xl">
            <div class="navbar-start">
                <a class="btn btn-ghost text-xl text-accent">🌿 ฟาร์มอัจฉリยะ</a>
            </div>
            <div class="navbar-end">
                <a href="{{ route('login') }}" class="btn btn-ghost">เข้าสู่ระบบ</a>
                <a href="{{ route('register') }}" class="btn bg-gradient-to-r from-primary to-accent text-white rounded-full shadow-[0_0_15px_rgba(34,197,94,0.4)] hover:shadow-[0_0_25px_rgba(34,197,94,0.6)] transition-all duration-300 hover:scale-105">ทดลองใช้งานฟรี</a>
            </div>
        </div>
    </header>

    <main class="container mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="h-24"></div>

        <!-- Hero Section -->
        <section class="hero text-center my-12">
            <div class="max-w-3xl">
                <h1 class="mb-5 text-5xl md:text-7xl font-bold">อนาคตของการเกษตร<br/>อยู่ในมือคุณ</h1>
                <p class="mb-8 text-lg text-base-content/70">แพลตฟอร์มจัดการฟาร์มที่ผสานความเรียบง่ายเข้ากับข้อมูลเชิงลึก เพื่อเกษตรกรไทยยุคใหม่</p>
            </div>
        </section>

        <!-- Features Section -->
        <section class="pb-12 pt-4 sm:pb-20">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Temperature Card -->
                <div class="bg-white/5 border border-white/10 backdrop-blur-md rounded-2xl p-6 hover:-translate-y-2 transition-transform duration-300 shadow-xl">
                    <div class="flex justify-between items-start mb-4">
                         <div class="flex items-center gap-2">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                            </span>
                            <span class="text-base-content/70 text-sm">อุณหภูมิ</span>
                        </div>
                        <div class="p-3 bg-orange-500/20 rounded-lg text-4xl text-orange-400 drop-shadow-[0_0_8px_rgba(251,146,60,0.5)]">🌡️</div>
                    </div>
                    <div class="text-5xl font-bold mb-1">32.5 <span class="text-3xl font-light text-base-content/50">°C</span></div>
                </div>
                <!-- Humidity Card -->
                <div class="bg-white/5 border border-white/10 backdrop-blur-md rounded-2xl p-6 hover:-translate-y-2 transition-transform duration-300 shadow-xl">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center gap-2">
                            <span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span></span>
                            <span class="text-base-content/70 text-sm">ความชื้น</span>
                        </div>
                        <div class="p-3 bg-blue-500/20 rounded-lg text-4xl text-blue-400 drop-shadow-[0_0_8px_rgba(96,165,250,0.5)]">💧</div>
                    </div>
                    <div class="text-5xl font-bold mb-1">75 <span class="text-3xl font-light text-base-content/50">%</span></div>
                </div>
                <!-- Soil pH Card -->
                <div class="bg-white/5 border border-white/10 backdrop-blur-md rounded-2xl p-6 hover:-translate-y-2 transition-transform duration-300 shadow-xl">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center gap-2">
                            <span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span></span>
                            <span class="text-base-content/70 text-sm">ค่ากรด-ด่างดิน</span>
                        </div>
                        <div class="p-3 bg-green-500/20 rounded-lg text-4xl text-green-400 drop-shadow-[0_0_8px_rgba(52,211,153,0.5)]">🌿</div>
                    </div>
                    <div class="text-5xl font-bold mb-1">6.8 <span class="text-3xl font-light text-base-content/50">pH</span></div>
                </div>
            </div>
        </section>

        <!-- Enhanced Chart Section -->
        <section class="pb-12 sm:pb-20">
            <div class="bg-white/5 border border-white/10 backdrop-blur-md rounded-2xl p-6 shadow-xl">
                 <div class="flex justify-between items-center mb-4">
                     <h3 class="font-semibold text-base-content/80">ประวัติความชื้นในดิน (7 วัน)</h3>
                     <div class="flex items-center gap-4 text-sm text-base-content/60">
                        <span>อัปเดตล่าสุด: ไม่กี่วินาทีที่แล้ว</span>
                        <button class="btn btn-ghost btn-sm"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0011.664 0l3.18-3.185m-3.18-3.182l-3.182-3.182a8.25 8.25 0 00-11.664 0l-3.18 3.185" /></svg></button>
                     </div>
                 </div>
                 <div class="w-full h-64 -ml-4 -mr-4">
                     <svg width="100%" height="100%" viewBox="0 0 400 150" preserveAspectRatio="none">
                         <defs>
                            <linearGradient id="line-gradient" x1="0%" y1="0%" x2="0%" y2="100%">
                                <stop offset="0%" style="stop-color:rgba(52, 211, 153, 0.2)" />
                                <stop offset="100%" style="stop-color:rgba(52, 211, 153, 0)" />
                            </linearGradient>
                        </defs>
                         <path d="M 20 130 C 80 50, 150 110, 220 80, 290 50, 350 100, 380 60 L 380 130 L 20 130 Z" fill="url(#line-gradient)"/>
                         <path d="M 20 130 C 80 50, 150 110, 220 80, 290 50, 350 100, 380 60" stroke="#34D399" fill="none" stroke-width="2" class="glow-filter"/>
                     </svg>
                 </div>
            </div>
        </section>
    </main>

    <!-- Enhanced Footer -->
    <footer class="footer footer-center p-10 mt-12 pt-10 border-t border-white/5 text-base-content/70">
        <nav class="grid grid-flow-col gap-4">
            <a class="link link-hover">เกี่ยวกับเรา</a>
            <a class="link link-hover">ติดต่อ</a>
        </nav> 
        <nav>
            <div class="grid grid-flow-col gap-4">
                <a><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="fill-current"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616v.064c0 2.298 1.634 4.214 3.791 4.649-.69.188-1.432.253-2.188.213.628 1.953 2.445 3.377 4.604 3.417-1.73 1.354-3.91 2.165-6.28 2.165-.41 0-.814-.024-1.21-.07C3.905 21.34 6.318 22.5 8.98 22.5c10.58 0 16.36-8.776 16.36-16.36 0-.25-.005-.5-.015-.749.995-.718 1.858-1.62 2.543-2.65z"></path></svg></a>
                <a><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="fill-current"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"></path></svg></a>
                <a><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="fill-current"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"></path></svg></a>
            </div>
        </nav> 
        <aside>
            <p>Copyright © 2024 - ฟาร์มอัจฉริยะ. สงวนลิขสิทธิ์.</p>
        </aside>
    </footer>

</body>
</html>


<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="deepdive">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - ฟาร์มอัจฉริยะ</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite('resources/css/app.css')

    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background-color: #0c0c0c; 
            background-image: radial-gradient(ellipse at top, var(--tw-gradient-stops));
            --tw-gradient-stops: theme('colors.green.900/20'), theme('colors.slate.950'), theme('colors.black');
        }
        .glow-filter {
            filter: drop-shadow(0 0 5px rgba(52, 211, 153, 0.7));
        }
         @keyframes float_orb_1 {
            0% { transform: translate(0, 0); }
            50% { transform: translate(40px, 60px); }
            100% { transform: translate(0, 0); }
        }
        @keyframes float_orb_2 {
            0% { transform: translate(0, 0); }
            50% { transform: translate(-50px, -70px); }
            100% { transform: translate(0, 0); }
        }
        .orb1 { animation: float_orb_1 15s ease-in-out infinite; }
        .orb2 { animation: float_orb_2 18s ease-in-out infinite; }
    </style>
</head>
<body class="text-base-content relative overflow-x-hidden">

    <!-- Background Glow Orbs -->
    <div class="absolute top-0 left-0 w-full h-full -z-10 overflow-hidden">
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-green-500/10 rounded-full blur-[120px] orb1"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-primary/10 rounded-full blur-[120px] orb2"></div>
    </div>

    <nav class="bg-slate-950/30 backdrop-blur-md sticky top-0 z-50 border-b border-white/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="#" class="text-xl font-bold text-accent">🌿 Dashboard ฟาร์ม</a>
                </div>
                <div class="flex items-center">
                     <div id="token-timer" class="text-sm text-base-content/60 mr-4"></div>
                    <button id="logout-button" class="btn btn-sm bg-red-600/80 text-white hover:bg-red-500">Logout</button>
                </div>
            </div>
        </div>
    </nav>

    <main class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-baseline mb-8">
            <h2 class="text-3xl font-bold leading-tight">ภาพรวม, <span id="user-name" class="text-accent">ผู้ใช้</span>!</h2>
             <div id="last-updated" class="text-sm text-base-content/60 flex items-center gap-2">
                <span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span></span>
                <span>กำลังโหลดข้อมูล...</span>
             </div>
        </div>
        
        <section class="pb-12 pt-4 sm:pb-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white/5 border border-white/10 backdrop-blur-md rounded-2xl p-6 hover:-translate-y-2 transition-transform duration-300 shadow-xl">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-base-content/70 text-sm">อุณหภูมิ</span>
                        <div class="p-3 bg-orange-500/20 rounded-lg text-4xl text-orange-400 drop-shadow-[0_0_8px_rgba(251,146,60,0.5)]">🌡️</div>
                    </div>
                    <div id="temp-value" class="text-5xl font-bold mb-1">-- <span class="text-3xl font-light text-base-content/50">°C</span></div>
                </div>
                <div class="bg-white/5 border border-white/10 backdrop-blur-md rounded-2xl p-6 hover:-translate-y-2 transition-transform duration-300 shadow-xl">
                    <div class="flex justify-between items-start mb-4">
                       <span class="text-base-content/70 text-sm">ความชื้น</span>
                        <div class="p-3 bg-blue-500/20 rounded-lg text-4xl text-blue-400 drop-shadow-[0_0_8px_rgba(96,165,250,0.5)]">💧</div>
                    </div>
                    <div id="humidity-value" class="text-5xl font-bold mb-1">-- <span class="text-3xl font-light text-base-content/50">%</span></div>
                </div>
                <div class="bg-white/5 border border-white/10 backdrop-blur-md rounded-2xl p-6 hover:-translate-y-2 transition-transform duration-300 shadow-xl">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-base-content/70 text-sm">ค่ากรด-ด่างดิน</span>
                        <div class="p-3 bg-green-500/20 rounded-lg text-4xl text-green-400 drop-shadow-[0_0_8px_rgba(52,211,153,0.5)]">🌿</div>
                    </div>
                    <div id="ph-value" class="text-5xl font-bold mb-1">-- <span class="text-3xl font-light text-base-content/50">pH</span></div>
                </div>
            </div>
        </section>

        <section class="pb-12 sm:pb-20">
            <div class="bg-white/5 border border-white/10 backdrop-blur-md rounded-2xl p-6 shadow-xl">
                 <h3 class="font-semibold text-base-content/80 mb-4">ประวัติข้อมูลความชื้น (จำลอง 7 วัน)</h3>
                 <div class="w-full h-64 -ml-4 -mr-4">
                     <svg width="100%" height="100%" viewBox="0 0 400 150" preserveAspectRatio="none">
                         <defs><linearGradient id="line-gradient" x1="0%" y1="0%" x2="0%" y2="100%"><stop offset="0%" style="stop-color:rgba(52, 211, 153, 0.2)" /><stop offset="100%" style="stop-color:rgba(52, 211, 153, 0)" /></linearGradient></defs>
                         <path d="M 20 130 C 80 50, 150 110, 220 80, 290 50, 350 100, 380 60 L 380 130 L 20 130 Z" fill="url(#line-gradient)"/>
                         <path d="M 20 130 C 80 50, 150 110, 220 80, 290 50, 350 100, 380 60" stroke="#34D399" fill="none" stroke-width="2" class="glow-filter"/>
                     </svg>
                 </div>
            </div>
        </section>
    </main>

    <footer class="pb-8 pt-4 text-center">
        <p class="text-sm text-base-content/50">© 2024 Smart Farm. All rights reserved.</p>
    </footer>

    @vite('resources/js/app.js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const token = localStorage.getItem('access_token');
            const expiryTime = localStorage.getItem('token_expiry');

            if (!token || !expiryTime || new Date().getTime() > expiryTime) {
                window.location.href = '{{ route("login") }}';
                return;
            }
            
            const userString = localStorage.getItem('user');
            if (userString && userString !== 'undefined' && userString !== 'null') {
                try {
                    const user = JSON.parse(userString);
                    if (user && user.username) {
                        document.getElementById('user-name').textContent = user.username;
                    } else {
                        document.getElementById('user-name').textContent = 'ผู้ใช้';
                    }
                } catch (e) {
                    console.error("Could not parse user data from localStorage:", e);
                    localStorage.removeItem('user'); 
                    document.getElementById('user-name').textContent = 'ผู้ใช้';
                }
            } else {
                document.getElementById('user-name').textContent = 'ผู้ใช้';
            }

            const logoutButton = document.getElementById('logout-button');
            logoutButton.addEventListener('click', async () => {
                try {
                     await fetch('/api/auth/logout', {
                        method: 'POST',
                        headers: {'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json'}
                    });
                } finally {
                    localStorage.clear();
                    window.location.href = '{{ route("login") }}';
                }
            });
            
            const tokenTimer = document.getElementById('token-timer');
            let countdownInterval = setInterval(() => {
                const distance = expiryTime - new Date().getTime();
                if (distance < 0) {
                    clearInterval(countdownInterval);
                    logoutButton.click();
                    return;
                }
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                tokenTimer.innerHTML = `Session: <span class="font-bold text-white">${minutes}m ${seconds}s</span>`;
            }, 1000);

            async function fetchSensorData() {
                try {
                    const response = await fetch('/api/sensor-data', {
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        if (response.status === 401) logoutButton.click();
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const result = await response.json();
                    
                    if (result.status === 'success' && result.data && result.data.data.length > 0) {
                        const sensorData = result.data.data;
                        
                        const latestTemp = sensorData.find(d => d.sensor_type?.name === 'temperature');
                        const latestHumidity = sensorData.find(d => d.sensor_type?.name === 'humidity');
                        const latestPh = sensorData.find(d => d.sensor_type?.name === 'ph');

                        document.getElementById('temp-value').innerHTML = `${parseFloat(latestTemp?.val || 0).toFixed(1)} <span class="text-3xl font-light text-base-content/50">°C</span>`;
                        document.getElementById('humidity-value').innerHTML = `${parseFloat(latestHumidity?.val || 0).toFixed(0)} <span class="text-3xl font-light text-base-content/50">%</span>`;
                        document.getElementById('ph-value').innerHTML = `${parseFloat(latestPh?.val || 0).toFixed(1)} <span class="text-3xl font-light text-base-content/50">pH</span>`;

                        document.querySelector('#last-updated span:last-child').textContent = `อัปเดตล่าสุด: ${new Date().toLocaleTimeString()}`;

                    } else {
                         document.querySelector('#last-updated span:last-child').textContent = 'ไม่พบข้อมูลเซ็นเซอร์';
                    }

                } catch (error) {
                    console.error("Fetch sensor data error:", error);
                    document.querySelector('#last-updated span:last-child').textContent = 'เกิดข้อผิดพลาดในการโหลดข้อมูล';
                }
            }

            fetchSensorData();
            setInterval(fetchSensorData, 15000);
        });
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Smart Farm</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100" style="background-image: url('https://www.transparenttextures.com/patterns/subtle-white-feathers.png');">

    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full bg-white rounded-lg shadow-2xl overflow-hidden" style="box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
            <div class="p-8">
                <div class="text-center mb-8">
                    <h1 class="text-4xl font-extrabold text-gray-800 tracking-tight">Welcome Back!</h1>
                    <p class="text-gray-500 mt-2">Login to manage your Smart Farm</p>
                </div>

                <form id="login-form" class="space-y-6">
                    <div>
                        <label for="email" class="text-sm font-bold text-gray-600 block">Email Address</label>
                        <input type="email" id="email" name="email" required
                               class="w-full p-3 mt-1 text-gray-700 bg-gray-50 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-shadow">
                    </div>

                    <div>
                        <label for="password" class="text-sm font-bold text-gray-600 block">Password</label>
                        <input type="password" id="password" name="password" required
                               class="w-full p-3 mt-1 text-gray-700 bg-gray-50 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-shadow">
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember_me" name="remember_me" type="checkbox" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                            <label for="remember_me" class="ml-2 block text-sm text-gray-900">
                                Remember me
                            </label>
                        </div>
                        <div class="text-sm">
                            <a href="#" class="font-medium text-green-600 hover:text-green-500">
                                Forgot your password?
                            </a>
                        </div>
                    </div>

                    <div>
                        <button type="submit"
                                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-lg text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transform hover:scale-105 transition-transform">
                            Sign in
                        </button>
                    </div>
                </form>

                <div id="error-message" class="mt-4 text-center text-red-500 font-medium hidden"></div>
                <div id="token-timer" class="mt-4 text-center text-gray-500 font-medium hidden"></div>


                <p class="mt-8 text-center text-sm text-gray-500">
                    Not a member?
                    <a href="{{ route('register') }}" class="font-medium text-green-600 hover:text-green-500">
                        Sign up now
                    </a>
                </p>
            </div>
        </div>
    </div>

    @vite('resources/js/app.js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const loginForm = document.getElementById('login-form');
            const errorMessage = document.getElementById('error-message');
            const tokenTimer = document.getElementById('token-timer');
            let countdownInterval;

            // Check if a token is already stored and valid
            const storedExpiry = localStorage.getItem('token_expiry');
            if (storedExpiry && new Date().getTime() < storedExpiry) {
                // Automatically redirect to dashboard if session is still active
                window.location.href = '/dashboard';
                return; // Stop further execution
            }

            loginForm.addEventListener('submit', async function (e) {
                e.preventDefault();
                errorMessage.classList.add('hidden');
                clearInterval(countdownInterval);

                const email = document.getElementById('email').value;
                const password = document.getElementById('password').value;

                try {
                    const response = await fetch('/api/auth/login', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ email, password })
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.error || 'Login failed. Please check your credentials.');
                    }

                    // Store token and user info
                    localStorage.setItem('access_token', data.access_token);
                    localStorage.setItem('user', JSON.stringify(data.user));

                    // Handle the token expiration timer
                    const expiresIn = data.expires_in; // in seconds
                    const expiryTime = new Date().getTime() + (expiresIn * 1000);
                    localStorage.setItem('token_expiry', expiryTime);

                    // Redirect to dashboard
                    window.location.href = '/dashboard';

                } catch (error) {
                    errorMessage.textContent = error.message;
                    errorMessage.classList.remove('hidden');
                }
            });
        });
    </script>
</body>
</html>

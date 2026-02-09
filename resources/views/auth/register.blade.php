<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - Smart Farm</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100" style="background-image: url('https://www.transparenttextures.com/patterns/subtle-white-feathers.png');">

    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full bg-white rounded-lg shadow-2xl overflow-hidden" style="box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
            <div class="p-8">
                <div class="text-center mb-8">
                    <h1 class="text-4xl font-extrabold text-gray-800 tracking-tight">Create Your Account</h1>
                    <p class="text-gray-500 mt-2">Join us to manage your Smart Farm</p>
                </div>

                <form id="register-form" class="space-y-6">
                    <div>
                        <label for="name" class="text-sm font-bold text-gray-600 block">Full Name</label>
                        <input type="text" id="name" name="name" required
                               class="w-full p-3 mt-1 text-gray-700 bg-gray-50 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-shadow">
                    </div>

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
                    
                    <div>
                        <label for="password_confirmation" class="text-sm font-bold text-gray-600 block">Confirm Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                               class="w-full p-3 mt-1 text-gray-700 bg-gray-50 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-shadow">
                    </div>

                    <div>
                        <button type="submit"
                                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-lg text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transform hover:scale-105 transition-transform">
                            Create Account
                        </button>
                    </div>
                </form>

                <div id="success-message" class="mt-4 text-center text-green-500 font-medium hidden"></div>
                <div id="error-message" class="mt-4 text-center text-red-500 font-medium hidden"></div>

                <p class="mt-8 text-center text-sm text-gray-500">
                    Already a member?
                    <a href="{{ route('login') }}" class="font-medium text-green-600 hover:text-green-500">
                        Sign in
                    </a>
                </p>
            </div>
        </div>
    </div>

    @vite('resources/js/app.js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const registerForm = document.getElementById('register-form');
            const successMessage = document.getElementById('success-message');
            const errorMessage = document.getElementById('error-message');

            registerForm.addEventListener('submit', async function (e) {
                e.preventDefault();
                successMessage.classList.add('hidden');
                errorMessage.classList.add('hidden');

                const name = document.getElementById('name').value;
                const email = document.getElementById('email').value;
                const password = document.getElementById('password').value;
                const password_confirmation = document.getElementById('password_confirmation').value;
                
                if (password !== password_confirmation) {
                    errorMessage.textContent = 'Passwords do not match.';
                    errorMessage.classList.remove('hidden');
                    return;
                }

                try {
                    const response = await fetch('/api/auth/register', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ name, email, password, password_confirmation })
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        // Create a formatted error message
                        let errorText = data.message || 'Registration failed.';
                        if (data.errors) {
                            errorText += '<ul>';
                            for (const key in data.errors) {
                                errorText += `<li>${data.errors[key][0]}</li>`;
                            }
                            errorText += '</ul>';
                        }
                        throw new Error(errorText);
                    }

                    successMessage.textContent = 'Registration successful! You can now log in.';
                    successMessage.classList.remove('hidden');
                    
                    // Optional: Redirect to login page after a delay
                    setTimeout(() => {
                        window.location.href = '{{ route("login") }}';
                    }, 3000);


                } catch (error) {
                    errorMessage.innerHTML = error.message;
                    errorMessage.classList.remove('hidden');
                }
            });
        });
    </script>
</body>
</html>

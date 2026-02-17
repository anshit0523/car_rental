@extends('layouts.adminlayout')

@section('content')

<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-80 py-4 px-3 sm:px-5 lg:px-6">
    <div class="max-w-4xl mx-auto">
        
        <!-- Header -->
        <div class="mb-4">
            <h1 class="text-4xl font-bold text-slate-900 mb-2">Payment Settings</h1>
            <p class="text-lg text-slate-600">Manage your payment processor configuration</p>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-slate-200">
            
            <!-- Card Header -->
            <div class="bg-gradient-to-r from-slate-900 to-slate-800 px-6 py-3 border-b-4 border-blue-600">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-sm">
                        💳
                    </div>
                    <h2 class="text-lg font-bold text-white">PayPal Configuration</h2>
                </div>
            </div>

            <!-- Alerts Section -->
            @if (session('success'))
                <div class="px-6 py-3 bg-green-50 border-l-4 border-green-500">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">✓</span>
                        <div>
                            <h3 class="font-semibold text-green-900 text-sm">Success!</h3>
                            <p class="text-green-700 text-xs">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="px-6 py-3 bg-red-50 border-l-4 border-red-500">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-lg">⚠</span>
                        <h3 class="font-semibold text-red-900 text-sm">Please fix the following errors:</h3>
                    </div>
                    <ul class="space-y-1 ml-7">
                        @foreach ($errors->all() as $error)
                            <li class="text-red-700 text-xs flex items-center gap-1">
                                <span class="w-1 h-1 bg-red-500 rounded-full"></span>
                                {{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form -->
            <form action="{{ route('admin.payment-settings.update') }}" method="POST" class="divide-y divide-slate-200">
                @csrf
                @method('PUT')

                <!-- Business Email -->
                <div class="px-6 py-3 hover:bg-slate-50 transition-colors">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex-1">
                            <label class="block text-sm font-semibold text-slate-900 mb-1">
                                Business Email
                            </label>
                            <p class="text-sm text-slate-500">Your PayPal business account email</p>
                        </div>
                        <div class="flex-1">
                            <input type="email" name="business_email" 
                                   value="{{ $setting->business_email ?? '' }}" 
                                   placeholder="business@example.com"
                                   required
                                   class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-slate-900 placeholder-slate-400 transition-all">
                            @error('business_email')
                                <p class="mt-1 text-red-600 text-xs font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Client ID -->
                <div class="px-6 py-3 hover:bg-slate-50 transition-colors">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex-1">
                            <label class="block text-sm font-semibold text-slate-900 mb-1">
                                Client ID
                            </label>
                            <p class="text-sm text-slate-500">Your PayPal application client ID</p>
                        </div>
                        <div class="flex-1">
                            <input type="text" name="client_id" 
                                   value="{{ $setting->client_id ?? '' }}" 
                                   placeholder="AQd..."
                                   required
                                   class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-slate-900 placeholder-slate-400 font-mono text-sm transition-all">
                            @error('client_id')
                                <p class="mt-1 text-red-600 text-xs font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Client Secret -->
                <div class="px-6 py-3 hover:bg-slate-50 transition-colors">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex-1">
                            <label class="block text-sm font-semibold text-slate-900 mb-1">
                                Client Secret
                            </label>
                            <p class="text-sm text-slate-500">Keep this confidential</p>
                        </div>
                        <div class="flex-1">
                            <input type="password" name="client_secret" 
                                   value="{{ $setting->client_secret ?? '' }}" 
                                   placeholder="••••••••••••••••"
                                   required
                                   class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-slate-900 placeholder-slate-400 font-mono text-sm transition-all">
                            @error('client_secret')
                                <p class="mt-1 text-red-600 text-xs font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Mode / Environment -->
                <div class="px-6 py-3 hover:bg-slate-50 transition-colors">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex-1">
                            <label class="block text-sm font-semibold text-slate-900 mb-1">
                                Mode
                            </label>
                            <p class="text-sm text-slate-500">Choose sandbox or live environment</p>
                        </div>
                        <div class="flex-1 flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer p-3 rounded-lg border-2 border-slate-300 hover:border-blue-500 transition-colors" 
                                   :class="{'border-blue-500 bg-blue-50': $setting->environment === 'sandbox'}">
                                <input type="radio" name="environment" value="sandbox" 
                                       {{ $setting->environment === 'sandbox' ? 'checked' : '' }}
                                       required
                                       class="w-4 h-3 text-blue-600 cursor-pointer">
                                <span class="text-sm font-medium text-slate-900">Sandbox</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer p-3 rounded-lg border-2 border-slate-300 hover:border-blue-500 transition-colors"
                                   :class="{'border-blue-500 bg-blue-50': $setting->environment === 'live'}">
                                <input type="radio" name="environment" value="live" 
                                       {{ $setting->environment === 'live' ? 'checked' : '' }}
                                       required
                                       class="w-4 h-3 text-blue-600 cursor-pointer">
                                <span class="text-sm font-medium text-slate-900">Live</span>
                            </label>
                        </div>
                    </div>
                    @error('environment')
                        <p class="mt-2 text-red-600 text-xs font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status / Active -->
                <div class="px-6 py-3 hover:bg-slate-50 transition-colors">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex-1">
                            <label class="block text-sm font-semibold text-slate-900 mb-1">
                                Status
                            </label>
                            <p class="text-sm text-slate-500">Enable or disable PayPal payments</p>
                        </div>
                        <div class="flex-1 flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer p-3 rounded-lg border-2 border-slate-300 hover:border-green-500 transition-colors"
                                   :class="{'border-green-500 bg-green-50': $setting->active}">
                                <input type="radio" name="active" value="1" 
                                       {{ $setting->active ? 'checked' : '' }}
                                       required
                                       class="w-4 h-3 text-green-600 cursor-pointer">
                                <span class="text-sm font-medium text-slate-900">Enabled</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer p-3 rounded-lg border-2 border-slate-300 hover:border-red-500 transition-colors"
                                   :class="{'border-red-500 bg-red-50': !$setting->active}">
                                <input type="radio" name="active" value="0" 
                                       {{ !$setting->active ? 'checked' : '' }}
                                       required
                                       class="w-4 h-3 text-red-600 cursor-pointer">
                                <span class="text-sm font-medium text-slate-900">Disabled</span>
                            </label>
                        </div>
                    </div>
                    @error('active')
                        <p class="mt-2 text-red-600 text-xs font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="px-6 py-3 bg-slate-50 flex justify-end gap-4">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="px-6 py-2.5 border-2 border-slate-300 text-slate-700 font-semibold rounded-lg hover:bg-slate-100 hover:border-slate-400 transition-all">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-8 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 active:bg-blue-800 transition-all shadow-md hover:shadow-lg">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

      
</div>

<script>
    // Optional: Add real-time validation feedback
    document.querySelectorAll('input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function() {
            // Smooth transition when radio changes
            this.parentElement.classList.add('transition-colors');
        });
    });
</script>

@endsection
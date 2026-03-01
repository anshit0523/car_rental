@extends('layouts.userlayout')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-gray-50 to-white">
  <div class="max-w-5xl mx-auto px-4 py-10">

    {{-- Header --}}
    <div class="flex flex-col gap-2 mb-6">
      <h1 class="text-2xl font-bold text-gray-900 tracking-tight">My Profile</h1>
      <p class="text-sm text-gray-600">Manage your account details and update your password.</p>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
      <div class="mb-4 flex items-start gap-3 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-green-900">
        <span class="mt-0.5">✅</span>
        <div class="text-sm">{{ session('success') }}</div>
      </div>
    @endif

    @if(session('success_password'))
      <div class="mb-4 flex items-start gap-3 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-green-900">
        <span class="mt-0.5">✅</span>
        <div class="text-sm">{{ session('success_password') }}</div>
      </div>
    @endif

    @if($errors->any())
      <div class="mb-4 flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-900">
        <span class="mt-0.5">⚠️</span>
        <div class="text-sm">
          <ul class="list-disc pl-5 space-y-1">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      </div>
    @endif

    {{-- Main Container --}}
    <div class="rounded-3xl bg-white border border-gray-200 shadow-sm p-4 sm:p-6">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Card: Update Info --}}
        <div class="rounded-2xl border border-gray-200 overflow-hidden">
          <div class="px-5 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-sm font-semibold text-gray-900">Profile Information</h2>
            <p class="text-xs text-gray-500 mt-1">Update your name and contact number.</p>
          </div>

          <form method="POST" action="{{ route('user.profile.update') }}" class="p-5 space-y-4">
            @csrf

            {{-- Email (read-only for professionalism) --}}
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
              <input
                type="email"
                value="{{ $user->email }}"
                disabled
                class="w-full rounded-xl border border-gray-200 bg-gray-100 px-4 py-3 text-sm text-gray-700 cursor-not-allowed"
              />
              <p class="text-[11px] text-gray-500 mt-1">Email can’t be changed here.</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
              <input
                type="text"
                name="name"
                value="{{ old('name', $user->name) }}"
                required
                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900
                       placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500"
                placeholder="Enter your full name"
              />
              @error('name')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
              <input
                type="text"
                name="phone"
                value="{{ old('phone', $user->phone) }}"
                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900
                       placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500"
                placeholder="Optional"
              />
              @error('phone')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
              @enderror
            </div>

            <button
              type="submit"
              class="w-full rounded-xl bg-orange-600 px-4 py-3 text-sm font-semibold text-white
                     hover:bg-orange-700 active:scale-[0.99] transition shadow-sm"
            >
              Save Changes
            </button>
          </form>
        </div>

        {{-- Card: Update Password --}}
        <div class="rounded-2xl border border-gray-200 overflow-hidden">
          <div class="px-5 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-sm font-semibold text-gray-900">Security</h2>
            <p class="text-xs text-gray-500 mt-1">Change your password to keep your account secure.</p>
          </div>

          <form method="POST" action="{{ route('user.profile.password') }}" class="p-5 space-y-4">
            @csrf

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
              <input
                type="password"
                name="current_password"
                required
                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900
                       placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500"
                placeholder="Enter current password"
              />
              @error('current_password')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
              <input
                type="password"
                name="password"
                required
                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900
                       placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500"
                placeholder="Create new password"
              />
              @error('password')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
              <input
                type="password"
                name="password_confirmation"
                required
                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900
                       placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500"
                placeholder="Confirm new password"
              />
            </div>

            <button
              type="submit"
              class="w-full rounded-xl bg-gray-900 px-4 py-3 text-sm font-semibold text-white
                     hover:bg-black active:scale-[0.99] transition shadow-sm"
            >
              Update Password
            </button>
          </form>
        </div>

      </div>
    </div>

  </div>
</div>
@endsection
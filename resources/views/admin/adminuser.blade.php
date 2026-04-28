@extends('layouts.adminlayout')

@section('content')
    <div class="flex h-screen overflow-hidden">
        <div class="flex-1 overflow-y-auto bg-gradient-to-br from-slate-50 to-slate-100">
            <div class="flex-1 p-6 lg:p-8 w-full lg:ml-0">

                <div class="mb-8 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                    <div>
                        <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-2">User Management</h1>
                        <p class="text-gray-600">Manage user accounts</p>
                    </div>

                    <button onclick="resetAndOpenAddModal()"
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition flex items-center gap-2 w-full lg:w-auto justify-center">
                        <i class="fas fa-user-plus"></i>
                        Add User
                    </button>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <form id="filterForm" method="GET" action="{{ route('admin.users.index') }}">
                        <div class="flex flex-col lg:flex-row gap-4">
                            <input
                                id="userSearch"
                                name="search"
                                type="text"
                                value="{{ request('search') }}"
                                placeholder="Search users by name or email..."
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            >

                            <select
                                id="roleFilter"
                                name="role_id"
                                class="w-full lg:w-48 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            >
                                <option value="">All Roles</option>
                                <option value="1" {{ request('role_id') == '1' ? 'selected' : '' }}>Admin</option>
                                <option value="2" {{ request('role_id') == '2' ? 'selected' : '' }}>Customer</option>
                                <option value="3" {{ request('role_id') == '3' ? 'selected' : '' }}>Staff</option>
                                <option value="4" {{ request('role_id') == '4' ? 'selected' : '' }}>Manager</option>
                            </select>

                            
                                href="{{ route('admin.users.index') }}"
                                class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-5 py-2 rounded-lg font-medium text-center"
                            >
                                Reset
                            </a>
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Role</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Joined</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Action</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-200">
                                @forelse($users as $user)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                            {{ $user->name }}
                                        </td>

                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            {{ $user->email }}
                                        </td>

                                        <td class="px-6 py-4 text-sm">
                                            @if($user->role && $user->role->name === 'Admin')
                                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700 flex items-center gap-1 w-fit">
                                                    <i class="fas fa-shield-alt"></i> Admin
                                                </span>
                                            @elseif($user->role && $user->role->name === 'Manager')
                                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 flex items-center gap-1 w-fit">
                                                    <i class="fas fa-user-tie"></i> Manager
                                                </span>
                                            @elseif($user->role && $user->role->name === 'Staff')
                                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700 flex items-center gap-1 w-fit">
                                                    <i class="fas fa-briefcase"></i> Staff
                                                </span>
                                            @else
                                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 flex items-center gap-1 w-fit">
                                                    <i class="fas fa-user"></i> Customer
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            {{ $user->created_at->format('M d, Y') }}
                                        </td>

                                        <td class="px-6 py-4 text-sm">
                                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Active
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 text-sm">
                                            <div class="flex items-center gap-3">
                                                <button type="button"
                                                    onclick='openEditModal(@json($user))'
                                                    class="text-amber-600 hover:text-amber-800 font-medium">
                                                    <i class="fas fa-edit"></i>
                                                </button>

                                                <button type="button"
                                                    onclick="openDeleteModal({{ $user->id }})"
                                                    class="text-red-600 hover:text-red-800 font-medium">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                            No users found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-6 flex justify-center">
                    {{ $users->links() }}
                </div>
            </div>

            <!-- User Add/Edit Modal -->
            <div id="userModal"
                class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 transition-opacity duration-300 opacity-0">

                <div id="modalContent"
                    class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md transform scale-90 transition-all duration-300">

                    <h2 id="modalTitle" class="text-2xl font-bold mb-4">Add User</h2>

                    <form id="userForm"
                        method="POST"
                        action="{{ route('admin.users.store') }}"
                        data-store-route="{{ route('admin.users.store') }}"
                        data-update-route="{{ route('admin.users.update', ':id') }}">
                        @csrf
                        <input type="hidden" id="formMethod" name="_method" value="POST">

                        <input
                            type="text"
                            id="name"
                            name="name"
                            placeholder="Name"
                            class="border p-2 w-full mb-4 rounded-lg"
                            value="{{ old('name') }}"
                        >
                        @error('name')
                            <p class="text-red-500 text-sm -mt-2 mb-3">{{ $message }}</p>
                        @enderror

                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="Email"
                            class="border p-2 w-full mb-4 rounded-lg"
                            value="{{ old('email') }}"
                        >
                        @error('email')
                            <p class="text-red-500 text-sm -mt-2 mb-3">{{ $message }}</p>
                        @enderror

                        <!-- Password Field -->
                        <div class="relative mb-4">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Password"
                                class="border p-2 pr-10 w-full rounded-lg"
                            >

                            <button
                                type="button"
                                class="password-toggle absolute inset-y-0 right-3 flex items-center text-gray-500 hover:text-green-600 transition"
                                data-target="password"
                                aria-label="Toggle password visibility"
                            >
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-500 text-sm -mt-2 mb-3">{{ $message }}</p>
                        @enderror

                        <!-- Password Confirmation Field -->
                        <div class="relative mb-4">
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                placeholder="Confirm Password"
                                class="border p-2 pr-10 w-full rounded-lg"
                            >

                            <button
                                type="button"
                                class="password-toggle absolute inset-y-0 right-3 flex items-center text-gray-500 hover:text-green-600 transition"
                                data-target="password_confirmation"
                                aria-label="Toggle password visibility"
                            >
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>

                        <select id="role" name="role_id" class="border p-2 w-full mb-4 rounded-lg">
                            <option value="" disabled {{ old('role_id') ? '' : 'selected' }}>Select Role</option>
                            <option value="1" {{ old('role_id') == '1' ? 'selected' : '' }}>Admin</option>
                            <option value="2" {{ old('role_id') == '2' ? 'selected' : '' }}>Customer</option>
                            <option value="3" {{ old('role_id') == '3' ? 'selected' : '' }}>Staff</option>
                            <option value="4" {{ old('role_id') == '4' ? 'selected' : '' }}>Manager</option>
                        </select>
                        @error('role_id')
                            <p class="text-red-500 text-sm -mt-2 mb-3">{{ $message }}</p>
                        @enderror

                        <p class="text-xs text-gray-500 mb-4">
                            In edit mode, leave the password fields blank to keep the current password.
                        </p>

                        <div class="flex justify-end">
                            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg">
                                Save
                            </button>
                            <button type="button" onclick="closeModal()"
                                class="bg-gray-300 px-4 py-2 rounded-lg ml-2">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Success Modal -->
            <div id="successModal"
                class="hidden fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50 transition-opacity duration-300 opacity-0">

                <div id="successContent"
                    class="bg-green-600 text-white px-6 py-4 rounded-lg shadow-xl transform scale-90 transition-all duration-300 flex items-center gap-3">
                    <i class="fas fa-check-circle text-2xl"></i>
                    <span id="successMessage" class="text-lg font-medium">Success!</span>
                </div>
            </div>

            <!-- Error Modal -->
            <div id="errorModal"
                class="hidden fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50 opacity-0 transition-opacity duration-300">

                <div id="errorContent"
                    class="bg-red-600 text-white px-6 py-4 rounded-lg shadow-xl transform scale-90 transition-all duration-300 flex items-center gap-3">
                    <i class="fas fa-times-circle text-2xl"></i>
                    <span id="errorMessage" class="text-lg font-medium">Something went wrong.</span>
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div id="deleteModal"
                class="hidden fixed inset-0 bg-black bg-opacity-40 z-50 flex items-center justify-center opacity-0 transition-opacity duration-300">

                <div id="deleteContent"
                    class="bg-white p-6 rounded-xl w-full max-w-md transform scale-90 transition-all duration-300">

                    <h2 class="text-xl font-bold mb-3 text-red-600">
                        <i class="fas fa-exclamation-triangle"></i> Confirm Delete
                    </h2>

                    <p class="text-gray-700 mb-6">
                        Are you sure you want to delete this user? This action cannot be undone.
                    </p>

                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')

                        <div class="flex justify-end gap-3">
                            <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-300 rounded-lg">
                                Cancel
                            </button>

                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg">
                                Delete
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/admin/adminuser.js') }}"></script>

    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                showSuccess(@json(session('success')));
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                showError(@json(session('error')));
                openAddModal();
            });
        </script>
    @endif

    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                openAddModal();
            });
        </script>
    @endif
@endsection
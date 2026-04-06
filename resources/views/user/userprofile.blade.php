@extends('layouts.userlayout')

@section('custom-styles')
<style>
    .profile-page {
        min-height: 100vh;
        background: #f8fafc;
        font-family: 'Outfit', sans-serif;
    }

    .profile-shell {
        max-width: 1200px;
        margin: 0 auto;
        padding: 24px 18px 48px;
    }

    .profile-header {
        margin-bottom: 22px;
    }

    .profile-header h1 {
        margin: 0 0 6px;
        font-size: 30px;
        line-height: 1.1;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.02em;
    }

    .profile-header p {
        margin: 0;
        font-size: 14px;
        color: #64748b;
        font-weight: 500;
    }

    .alert-box {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        border-radius: 18px;
        padding: 14px 16px;
        margin-bottom: 14px;
        font-size: 14px;
        font-weight: 500;
    }

    .alert-box.success {
        background: #ecfdf5;
        border: 1px solid #bbf7d0;
        color: #166534;
    }

    .alert-box.error {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
    }

    .profile-grid-wrap {
        background: #ffffff;
        border: 1px solid #eef2f7;
        border-radius: 24px;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
        padding: 18px;
    }

    .profile-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .profile-card {
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        overflow: hidden;
        background: #fff;
    }

    .profile-card-head {
        padding: 18px 20px;
        background: #f8fafc;
        border-bottom: 1px solid #e5e7eb;
    }

    .profile-card-head h2 {
        margin: 0 0 4px;
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
    }

    .profile-card-head p {
        margin: 0;
        font-size: 12px;
        color: #64748b;
        font-weight: 500;
    }

    .profile-card-body {
        padding: 20px;
    }

    .profile-form {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .form-group-block {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .form-label {
        font-size: 12px;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .form-input {
        width: 100%;
        min-height: 48px;
        border: 1px solid #dbe2ea;
        border-radius: 14px;
        padding: 0 14px;
        font-size: 14px;
        font-weight: 600;
        color: #0f172a;
        background: #ffffff;
        outline: none;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .form-input:focus {
        border-color: #ff5a1f;
        box-shadow: 0 0 0 4px rgba(255, 90, 31, 0.08);
    }

    .form-input[disabled] {
        background: #f1f5f9;
        color: #64748b;
        cursor: not-allowed;
    }

    .helper-text {
        font-size: 11px;
        color: #64748b;
        font-weight: 500;
        margin-top: 2px;
    }

    .field-error {
        font-size: 12px;
        color: #dc2626;
        font-weight: 600;
        margin-top: 2px;
    }

    .btn-primary-orange,
    .btn-primary-dark {
        width: 100%;
        min-height: 48px;
        border: none;
        border-radius: 14px;
        font-size: 14px;
        font-weight: 800;
        transition: all 0.25s ease;
    }

    .btn-primary-orange {
        background: #ff5a1f;
        color: #fff;
        box-shadow: 0 10px 20px rgba(255, 90, 31, 0.18);
    }

    .btn-primary-orange:hover {
        background: #ea580c;
    }

    .btn-primary-dark {
        background: #0f172a;
        color: #fff;
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.14);
    }

    .btn-primary-dark:hover {
        background: #020617;
    }

    .error-list {
        margin: 0;
        padding-left: 18px;
    }

    .error-list li {
        margin-bottom: 4px;
    }

    @media (max-width: 900px) {
        .profile-shell {
            padding: 18px 14px 40px;
        }

        .profile-header h1 {
            font-size: 28px;
        }

        .profile-grid {
            grid-template-columns: 1fr;
        }

        .profile-grid-wrap {
            padding: 14px;
        }

        .profile-card-body {
            padding: 18px;
        }
    }
</style>
@endsection

@section('content')
<div class="profile-page">
    <div class="profile-shell">

        <div class="profile-header">
            <h1>My Profile</h1>
            <p>Manage your account details and update your password.</p>
        </div>

        @if(session('success'))
            <div class="alert-box success">
                <span>✅</span>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if(session('success_password'))
            <div class="alert-box success">
                <span>✅</span>
                <div>{{ session('success_password') }}</div>
            </div>
        @endif

        @if($errors->any())
            <div class="alert-box error">
                <span>⚠️</span>
                <div>
                    <ul class="error-list">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="profile-grid-wrap">
            <div class="profile-grid">

                <!-- Profile Information -->
                <div class="profile-card">
                    <div class="profile-card-head">
                        <h2>Profile Information</h2>
                        <p>Update your name and contact number.</p>
                    </div>

                    <div class="profile-card-body">
                        <form method="POST" action="{{ route('user.profile.update') }}" class="profile-form">
                            @csrf

                            <div class="form-group-block">
                                <label class="form-label">Email Address</label>
                                <input
                                    type="email"
                                    value="{{ $user->email }}"
                                    disabled
                                    class="form-input"
                                >
                                <div class="helper-text">Email can’t be changed here.</div>
                            </div>

                            <div class="form-group-block">
                                <label class="form-label">Full Name</label>
                                <input
                                    type="text"
                                    name="name"
                                    value="{{ old('name', $user->name) }}"
                                    required
                                    class="form-input"
                                    placeholder="Enter your full name"
                                >
                                @error('name')
                                    <div class="field-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group-block">
                                <label class="form-label">Phone Number</label>
                                <input
                                    type="text"
                                    name="phone"
                                    value="{{ old('phone', $user->phone) }}"
                                    class="form-input"
                                    placeholder="Optional"
                                >
                                @error('phone')
                                    <div class="field-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn-primary-orange">
                                Save Changes
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Security -->
                <div class="profile-card">
                    <div class="profile-card-head">
                        <h2>Security</h2>
                        <p>Change your password to keep your account secure.</p>
                    </div>

                    <div class="profile-card-body">
                        <form method="POST" action="{{ route('user.profile.password') }}" class="profile-form">
                            @csrf

                            <div class="form-group-block">
                                <label class="form-label">Current Password</label>
                                <input
                                    type="password"
                                    name="current_password"
                                    required
                                    class="form-input"
                                    placeholder="Enter current password"
                                >
                                @error('current_password')
                                    <div class="field-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group-block">
                                <label class="form-label">New Password</label>
                                <input
                                    type="password"
                                    name="password"
                                    required
                                    class="form-input"
                                    placeholder="Create new password"
                                >
                                @error('password')
                                    <div class="field-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group-block">
                                <label class="form-label">Confirm New Password</label>
                                <input
                                    type="password"
                                    name="password_confirmation"
                                    required
                                    class="form-input"
                                    placeholder="Confirm new password"
                                >
                            </div>

                            <button type="submit" class="btn-primary-dark">
                                Update Password
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection
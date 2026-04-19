@extends('layouts.adminlayout')

@section('content')
<div class="h-screen overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8 pb-8">

        <!-- Header Section -->
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-col gap-4 sm:gap-6">
                <!-- Title -->
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">
                        Damage & Return Reports
                    </h1>
                    <p class="text-sm sm:text-base text-gray-600 mt-1">
                        Manage and track all reported vehicle damages and issues
                    </p>
                </div>

                <!-- Filters -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                    <form method="GET" action="{{ route('admin.return-issues.index') }}" class="flex flex-col sm:flex-row gap-3">
                        <div class="flex-1">
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">Filter by Status</label>
                            <select name="issue_status_id" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                <option value="">All Statuses</option>
                                @foreach($issueStatuses as $status)
                                    <option value="{{ $status->id }}" {{ (string) request('issue_status_id') === (string) $status->id ? 'selected' : '' }}>
                                        {{ $status->label ?? ucfirst($status->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex gap-2 sm:items-end sm:pt-5">
                            <button type="submit" class="flex-1 sm:flex-none bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors shadow-sm hover:shadow">
                                <span class="hidden sm:inline">Apply Filter</span>
                                <span class="sm:hidden">Filter</span>
                            </button>

                            <a href="{{ route('admin.return-issues.index') }}" class="flex-1 sm:flex-none bg-gray-100 hover:bg-gray-200 active:bg-gray-300 text-gray-700 px-5 py-2.5 rounded-lg text-sm font-medium transition-colors text-center">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="mb-6 rounded-xl bg-green-50 border border-green-200 p-4 shadow-sm animate-fade-in">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm text-green-800 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4 shadow-sm animate-fade-in">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <ul class="list-disc pl-5 text-sm text-red-800 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @php
            $issueStatusClasses = [
                'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                'reviewing' => 'bg-blue-100 text-blue-800 border-blue-200',
                'awaiting_payment' => 'bg-orange-100 text-orange-800 border-orange-200',
                'resolved' => 'bg-green-100 text-green-800 border-green-200',
                'rejected' => 'bg-red-100 text-red-800 border-red-200',
            ];

            $bookingStatusClasses = [
                'Checkup' => 'bg-orange-100 text-orange-800 border-orange-200',
                'Damage' => 'bg-red-100 text-red-800 border-red-200',
                'Needs Repair' => 'bg-purple-100 text-purple-800 border-purple-200',
            ];

            $reviewingStatus = $issueStatuses->firstWhere('name', 'reviewing');
            $reviewingStatusId = $reviewingStatus?->id;
        @endphp

        <!-- Reports List -->
        <div class="space-y-4 sm:space-y-6">
            @forelse($returnIssues as $issue)
                @php
                    $currentIssueStatusName = optional($issue->issueStatus)->name;
                    $currentIssueStatusLabel = optional($issue->issueStatus)->label ?? ucfirst($currentIssueStatusName ?? 'No Status');
                    $photoUrls = $issue->photos
                        ? $issue->photos->map(fn ($photo) => asset('storage/' . $photo->photo_path))->values()->toArray()
                        : [];
                @endphp

                <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">

                    <!-- Card Header -->
                    <div class="p-4 sm:p-6 bg-gradient-to-r from-gray-50 to-white border-b border-gray-100">
                        <div class="flex flex-col gap-3 sm:gap-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <h2 class="text-lg sm:text-xl font-semibold text-gray-900 truncate">
                                        {{ $issue->title }}
                                    </h2>
                                    <div class="flex items-center gap-2 mt-1.5">
                                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <p class="text-xs sm:text-sm text-gray-500">
                                            {{ $issue->reported_at ? \Carbon\Carbon::parse($issue->reported_at)->format('M d, Y h:i A') : 'Not reported yet' }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Mobile: Issue Status Badge -->
                                <div class="sm:hidden">
                                    <span
                                        data-role="issue-status-badge"
                                        data-issue-id="{{ $issue->id }}"
                                        data-status="{{ $currentIssueStatusName }}"
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $issueStatusClasses[$currentIssueStatusName] ?? 'bg-gray-100 text-gray-800 border-gray-200' }}"
                                    >
                                        <span data-role="issue-status-text" data-issue-id="{{ $issue->id }}">
                                            {{ $currentIssueStatusLabel }}
                                        </span>
                                    </span>
                                </div>
                            </div>

                            <!-- Desktop: Status Badges -->
                            <div class="hidden sm:flex flex-wrap gap-2">
                                <span
                                    data-role="issue-status-badge"
                                    data-issue-id="{{ $issue->id }}"
                                    data-status="{{ $currentIssueStatusName }}"
                                    class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold border {{ $issueStatusClasses[$currentIssueStatusName] ?? 'bg-gray-100 text-gray-800 border-gray-200' }}"
                                >
                                    <span class="w-2 h-2 rounded-full bg-current mr-2"></span>
                                    <span data-role="issue-status-text" data-issue-id="{{ $issue->id }}">
                                        Issue: {{ $currentIssueStatusLabel }}
                                    </span>
                                </span>

                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold border {{ $bookingStatusClasses[optional($issue->booking->status)->name] ?? 'bg-gray-100 text-gray-800 border-gray-200' }}">
                                    <span class="w-2 h-2 rounded-full bg-current mr-2"></span>
                                    Booking: {{ optional($issue->booking->status)->name ?? 'No Status' }}
                                </span>
                            </div>

                            <!-- Mobile: Booking Status Badge -->
                            <div class="sm:hidden">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $bookingStatusClasses[optional($issue->booking->status)->name] ?? 'bg-gray-100 text-gray-800 border-gray-200' }}">
                                    Booking: {{ optional($issue->booking->status)->name ?? 'No Status' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-4 sm:p-6">

                        <!-- Info Grid -->
                        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-5 sm:mb-6">
                            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg sm:rounded-xl p-3 sm:p-4 border border-gray-200">
                                <p class="text-xs uppercase tracking-wide text-gray-500 font-medium mb-1">Booking ID</p>
                                <p class="text-sm sm:text-base font-bold text-gray-900">#{{ $issue->booking_id }}</p>
                            </div>

                            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg sm:rounded-xl p-3 sm:p-4 border border-gray-200">
                                <p class="text-xs uppercase tracking-wide text-gray-500 font-medium mb-1">Customer</p>
                                <p class="text-sm sm:text-base font-bold text-gray-900 truncate" title="{{ optional($issue->booking->user)->name ?? 'N/A' }}">
                                    {{ optional($issue->booking->user)->name ?? 'N/A' }}
                                </p>
                            </div>

                            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg sm:rounded-xl p-3 sm:p-4 border border-gray-200">
                                <p class="text-xs uppercase tracking-wide text-gray-500 font-medium mb-1">Issue Type</p>
                                <p class="text-sm sm:text-base font-bold text-gray-900 truncate" title="{{ $issue->issue_type }}">
                                    {{ $issue->issue_type }}
                                </p>
                            </div>

                            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg sm:rounded-xl p-3 sm:p-4 border border-gray-200">
                                <p class="text-xs uppercase tracking-wide text-gray-500 font-medium mb-1">Reporter</p>
                                <p class="text-sm sm:text-base font-bold text-gray-900 truncate" title="{{ optional($issue->reporter)->name ?? 'N/A' }}">
                                    {{ optional($issue->reporter)->name ?? 'N/A' }}
                                </p>
                            </div>

                            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg sm:rounded-xl p-3 sm:p-4 border border-gray-200">
                                <p class="text-xs uppercase tracking-wide text-gray-500 font-medium mb-1">Vehicle</p>
                                <p class="text-sm sm:text-base font-bold text-gray-900 truncate" title="{{ optional(optional($issue->booking->car)->brand)->name ?? 'N/A' }}">
                                    {{ optional(optional($issue->booking->car)->brand)->name ?? 'N/A' }}
                                </p>
                            </div>

                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg sm:rounded-xl p-3 sm:p-4 border border-blue-200">
                                <p class="text-xs uppercase tracking-wide text-blue-600 font-medium mb-1">Est. Charge</p>
                                <p class="text-sm sm:text-base font-bold text-blue-900">
                                    ₱{{ number_format($issue->estimated_charge ?? 0, 2) }}
                                </p>
                            </div>

                            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg sm:rounded-xl p-3 sm:p-4 border border-green-200 col-span-2 lg:col-span-1">
                                <p class="text-xs uppercase tracking-wide text-green-600 font-medium mb-1">Final Charge</p>
                                <p class="text-sm sm:text-base font-bold text-green-900">
                                    ₱{{ number_format($issue->final_charge ?? 0, 2) }}
                                </p>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-5 sm:mb-6">
                            <div class="flex items-center gap-2 mb-2.5">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-sm font-semibold text-gray-700">Description</p>
                            </div>
                            <div class="bg-gray-50 border border-gray-200 rounded-lg sm:rounded-xl p-3 sm:p-4">
                                <p class="text-sm text-gray-700 leading-relaxed">
                                    {{ $issue->description ?: 'No description provided.' }}
                                </p>
                            </div>
                        </div>

                        <!-- Photos -->
                        <div class="mb-6">
                            <div class="flex items-center gap-2 mb-3">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="text-sm font-semibold text-gray-700">Photos</p>
                                @if($issue->photos && $issue->photos->count())
                                    <span class="ml-auto text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">
                                        {{ $issue->photos->count() }} {{ $issue->photos->count() === 1 ? 'photo' : 'photos' }}
                                    </span>
                                @endif
                            </div>

                            @if($issue->photos && $issue->photos->count())
                                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-2 sm:gap-3">
                                    @foreach($issue->photos as $photoIndex => $photo)
                                        <button
                                            type="button"
                                            class="group relative aspect-square block overflow-hidden rounded-lg border-2 border-gray-200 hover:border-blue-400 transition-all"
                                            data-photo-url="{{ asset('storage/' . $photo->photo_path) }}"
                                            data-photo-index="{{ $photoIndex }}"
                                            data-gallery='@json($photoUrls)'
                                            data-issue-id="{{ $issue->id }}"
                                            data-current-status="{{ $currentIssueStatusName }}"
                                            data-update-url="{{ route('admin.return-issues.update-status', $issue->id) }}"
                                            onclick="openIssuePhotoModal(this)"
                                        >
                                            <img
                                                src="{{ asset('storage/' . $photo->photo_path) }}"
                                                alt="Issue Photo"
                                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                            >
                                            <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity"></div>
                                            <div class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <svg class="w-4 h-4 text-white drop-shadow-lg" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z"/>
                                                    <path d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z"/>
                                                </svg>
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            @else
                                <div class="bg-gray-50 border border-gray-200 rounded-lg sm:rounded-xl p-8 text-center">
                                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="text-sm text-gray-500">No photos uploaded</p>
                                </div>
                            @endif
                        </div>

                        <!-- Update Status Section -->
                        <div class="border-t border-gray-200 pt-5 sm:pt-6">
                            <div class="flex items-center gap-2 mb-4">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                <h3 class="text-base sm:text-lg font-semibold text-gray-900">Update Status</h3>
                            </div>

                            <form action="{{ route('admin.return-issues.update-status', $issue->id) }}" method="POST" class="space-y-4">
                                @csrf
                                @method('PATCH')

                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                    <div>
                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                                            Issue Status
                                        </label>
                                        <select
                                            name="issue_status_id"
                                            data-role="issue-status-select"
                                            data-issue-id="{{ $issue->id }}"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                        >
                                            @foreach($issueStatuses as $status)
                                                <option value="{{ $status->id }}" {{ (string) $issue->issue_status_id === (string) $status->id ? 'selected' : '' }}>
                                                    {{ $status->label ?? ucfirst($status->name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                                            Booking Status
                                        </label>
                                        <select name="booking_status_name" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                                            <option value="">Keep Current</option>
                                            @foreach($bookingStatuses as $status)
                                                <option value="{{ $status->name }}" {{ optional($issue->booking->status)->name === $status->name ? 'selected' : '' }}>
                                                    {{ $status->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                                            Final Charge (₱)
                                        </label>
                                        <input
                                            type="number"
                                            name="final_charge"
                                            min="0"
                                            step="0.01"
                                            value="{{ $issue->final_charge ?? 0 }}"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                            placeholder="0.00"
                                        >
                                    </div>

                                    <div class="flex items-end">
                                        <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 active:from-indigo-800 active:to-indigo-900 text-white px-4 py-2.5 rounded-lg text-sm font-medium shadow-sm hover:shadow-md transition-all flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <span>Update Report</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-200 p-10 sm:p-16 text-center">
                    <div class="max-w-md mx-auto">
                        <svg class="w-16 h-16 sm:w-20 sm:h-20 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-2">No Reports Found</h3>
                        <p class="text-sm sm:text-base text-gray-500">There are no return issue reports matching your criteria.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-6 sm:mt-8">
            {{ $returnIssues->links() }}
        </div>
    </div>
</div>

<!-- Photo Modal -->
<div
    id="issuePhotoModal"
    class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/80 p-4"
>
    <div class="relative w-full max-w-6xl flex items-center justify-center">
        <button
            type="button"
            onclick="closeIssuePhotoModal()"
            class="absolute -top-12 right-0 text-white hover:text-gray-300 transition"
        >
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <button
            type="button"
            id="issuePhotoPrevBtn"
            onclick="showPreviousIssuePhoto()"
            class="absolute left-0 sm:left-3 top-1/2 -translate-y-1/2 bg-white/15 hover:bg-white/25 text-white backdrop-blur px-3 py-3 rounded-full transition"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>

        <div class="w-full flex flex-col items-center">
            <img
                id="issuePhotoModalImage"
                src=""
                alt="Issue Photo Preview"
                class="max-h-[78vh] w-auto max-w-full rounded-xl shadow-2xl object-contain"
            >

            <div class="mt-4 flex items-center justify-center gap-3 text-white">
                <span id="issuePhotoCounter" class="text-sm sm:text-base font-medium bg-white/10 px-3 py-1 rounded-full"></span>
            </div>
        </div>

        <button
            type="button"
            id="issuePhotoNextBtn"
            onclick="showNextIssuePhoto()"
            class="absolute right-0 sm:right-3 top-1/2 -translate-y-1/2 bg-white/15 hover:bg-white/25 text-white backdrop-blur px-3 py-3 rounded-full transition"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </div>
</div>

<style>
    @keyframes fade-in {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in {
        animation: fade-in 0.3s ease-out;
    }
</style>

<script>
    const reviewingStatusId = @json($reviewingStatusId);
    const csrfToken = @json(csrf_token());

    const issueStatusClassMap = {
        pending: 'bg-yellow-100 text-yellow-800 border-yellow-200',
        reviewing: 'bg-blue-100 text-blue-800 border-blue-200',
        awaiting_payment: 'bg-orange-100 text-orange-800 border-orange-200',
        resolved: 'bg-green-100 text-green-800 border-green-200',
        rejected: 'bg-red-100 text-red-800 border-red-200',
    };

    let issuePhotoGallery = [];
    let issuePhotoIndex = 0;

    function openIssuePhotoModal(button) {
        const modal = document.getElementById('issuePhotoModal');
        const gallery = JSON.parse(button.dataset.gallery || '[]');
        const startIndex = parseInt(button.dataset.photoIndex || '0', 10);

        issuePhotoGallery = gallery;
        issuePhotoIndex = startIndex;

        renderIssuePhotoModal();

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');

        const issueId = button.dataset.issueId;
        const currentStatus = button.dataset.currentStatus;
        const updateUrl = button.dataset.updateUrl;

        if (!reviewingStatusId || !updateUrl) {
            return;
        }

        if (currentStatus !== 'pending') {
            return;
        }

        autoSetIssueReviewing(issueId, updateUrl);
    }

    function renderIssuePhotoModal() {
        const modalImage = document.getElementById('issuePhotoModalImage');
        const counter = document.getElementById('issuePhotoCounter');
        const prevBtn = document.getElementById('issuePhotoPrevBtn');
        const nextBtn = document.getElementById('issuePhotoNextBtn');

        if (!issuePhotoGallery.length) {
            modalImage.src = '';
            counter.textContent = '';
            prevBtn.classList.add('hidden');
            nextBtn.classList.add('hidden');
            return;
        }

        modalImage.src = issuePhotoGallery[issuePhotoIndex];
        counter.textContent = `${issuePhotoIndex + 1} / ${issuePhotoGallery.length}`;

        if (issuePhotoGallery.length <= 1) {
            prevBtn.classList.add('hidden');
            nextBtn.classList.add('hidden');
        } else {
            prevBtn.classList.remove('hidden');
            nextBtn.classList.remove('hidden');
        }
    }

    function showPreviousIssuePhoto() {
        if (!issuePhotoGallery.length) {
            return;
        }

        issuePhotoIndex = (issuePhotoIndex - 1 + issuePhotoGallery.length) % issuePhotoGallery.length;
        renderIssuePhotoModal();
    }

    function showNextIssuePhoto() {
        if (!issuePhotoGallery.length) {
            return;
        }

        issuePhotoIndex = (issuePhotoIndex + 1) % issuePhotoGallery.length;
        renderIssuePhotoModal();
    }

    function closeIssuePhotoModal() {
        const modal = document.getElementById('issuePhotoModal');
        const modalImage = document.getElementById('issuePhotoModalImage');

        modal.classList.add('hidden');
        modal.classList.remove('flex');
        modalImage.src = '';
        document.body.classList.remove('overflow-hidden');

        issuePhotoGallery = [];
        issuePhotoIndex = 0;
    }

    async function autoSetIssueReviewing(issueId, updateUrl) {
        try {
            const formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('_method', 'PATCH');
            formData.append('issue_status_id', reviewingStatusId);

            const response = await fetch(updateUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });

            if (!response.ok) {
                throw new Error('Failed to update issue status.');
            }

            updateIssueStatusUI(issueId, 'reviewing', 'Reviewing', reviewingStatusId);
        } catch (error) {
            console.error(error);
        }
    }

    function updateIssueStatusUI(issueId, statusName, statusLabel, statusId = null) {
        document.querySelectorAll(`[data-role="issue-status-badge"][data-issue-id="${issueId}"]`).forEach((badge) => {
            badge.dataset.status = statusName;

            badge.classList.remove(
                'bg-yellow-100', 'text-yellow-800', 'border-yellow-200',
                'bg-blue-100', 'text-blue-800', 'border-blue-200',
                'bg-orange-100', 'text-orange-800', 'border-orange-200',
                'bg-green-100', 'text-green-800', 'border-green-200',
                'bg-red-100', 'text-red-800', 'border-red-200',
                'bg-gray-100', 'text-gray-800', 'border-gray-200'
            );

            const newClasses = (issueStatusClassMap[statusName] || 'bg-gray-100 text-gray-800 border-gray-200').split(' ');
            badge.classList.add(...newClasses);
        });

        document.querySelectorAll(`[data-role="issue-status-text"][data-issue-id="${issueId}"]`).forEach((textEl) => {
            const isDesktop = textEl.textContent.trim().startsWith('Issue:');
            textEl.textContent = isDesktop ? `Issue: ${statusLabel}` : statusLabel;
        });

        document.querySelectorAll(`[data-issue-id="${issueId}"][data-current-status]`).forEach((photoButton) => {
            photoButton.dataset.currentStatus = statusName;
        });

        if (statusId !== null) {
            document.querySelectorAll(`[data-role="issue-status-select"][data-issue-id="${issueId}"]`).forEach((selectEl) => {
                selectEl.value = String(statusId);
            });
        }
    }

    document.getElementById('issuePhotoModal')?.addEventListener('click', function (event) {
        if (event.target.id === 'issuePhotoModal') {
            closeIssuePhotoModal();
        }
    });

    document.addEventListener('keydown', function (event) {
        const modal = document.getElementById('issuePhotoModal');
        const isOpen = modal && !modal.classList.contains('hidden');

        if (!isOpen) {
            return;
        }

        if (event.key === 'Escape') {
            closeIssuePhotoModal();
        }

        if (event.key === 'ArrowLeft') {
            showPreviousIssuePhoto();
        }

        if (event.key === 'ArrowRight') {
            showNextIssuePhoto();
        }
    });
</script>
@endsection
@extends('layouts.userlayout')

@section('custom-styles')
<style>
    .issue-page {
        min-height: 100vh;
        background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
        padding: 16px 0 24px;
    }

    .issue-wrap {
        width: 100%;
        max-width: 760px;
        margin: 0 auto;
        padding: 0 12px;
    }

    .issue-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .issue-header {
        padding: 18px 18px 14px;
        border-bottom: 1px solid #eef2f7;
        background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
    }

    .issue-title {
        font-size: 22px;
        font-weight: 800;
        line-height: 1.2;
        color: #111827;
        margin: 0 0 6px;
    }

    .issue-booking {
        font-size: 13px;
        color: #6b7280;
        line-height: 1.5;
    }

    .issue-body {
        padding: 18px;
    }

    .issue-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 16px;
    }

    .issue-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 12px;
        border-radius: 9999px;
        font-size: 12px;
        font-weight: 700;
        border: 1px solid transparent;
    }

    .issue-badge-dot {
        width: 8px;
        height: 8px;
        border-radius: 9999px;
        background: currentColor;
    }

    .badge-pending {
        background: #fef3c7;
        color: #92400e;
        border-color: #fcd34d;
    }

    .badge-reviewing {
        background: #dbeafe;
        color: #1d4ed8;
        border-color: #93c5fd;
    }

    .badge-awaiting-payment {
        background: #ffedd5;
        color: #c2410c;
        border-color: #fdba74;
    }

    .badge-resolved {
        background: #dcfce7;
        color: #166534;
        border-color: #86efac;
    }

    .badge-rejected {
        background: #fee2e2;
        color: #b91c1c;
        border-color: #fca5a5;
    }

    .status-note {
        border-radius: 14px;
        padding: 14px 15px;
        margin-bottom: 18px;
        border: 1px solid #e5e7eb;
    }

    .status-note h3 {
        margin: 0 0 6px;
        font-size: 14px;
        font-weight: 800;
    }

    .status-note p {
        margin: 0;
        font-size: 13px;
        line-height: 1.55;
    }

    .note-pending {
        background: #fffbeb;
        border-color: #fde68a;
        color: #92400e;
    }

    .note-reviewing {
        background: #eff6ff;
        border-color: #bfdbfe;
        color: #1d4ed8;
    }

    .note-awaiting-payment {
        background: #fff7ed;
        border-color: #fdba74;
        color: #c2410c;
    }

    .note-resolved {
        background: #f0fdf4;
        border-color: #86efac;
        color: #166534;
    }

    .note-rejected {
        background: #fef2f2;
        border-color: #fca5a5;
        color: #b91c1c;
    }

    .issue-lines {
        margin-bottom: 18px;
        border-top: 1px solid #f1f5f9;
    }

    .issue-line {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .issue-line-label {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #6b7280;
    }

    .issue-line-value {
        font-size: 14px;
        font-weight: 700;
        color: #111827;
        text-align: right;
        line-height: 1.4;
        word-break: break-word;
    }

    .issue-section {
        margin-bottom: 18px;
    }

    .issue-section:last-child {
        margin-bottom: 0;
    }

    .issue-section-title {
        font-size: 14px;
        font-weight: 800;
        color: #111827;
        margin-bottom: 8px;
    }

    .issue-description {
        font-size: 13px;
        line-height: 1.7;
        color: #374151;
        margin: 0;
    }

    .issue-photos {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
    }

    .issue-photo-box {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        background: #f9fafb;
        cursor: pointer;
        position: relative;
    }

    .issue-photo {
        width: 100%;
        height: 110px;
        object-fit: cover;
        display: block;
        transition: transform 0.25s ease;
    }

    .issue-photo-box:hover .issue-photo {
        transform: scale(1.04);
    }

    .issue-empty {
        font-size: 13px;
        color: #6b7280;
    }

    .issue-footer {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 20px;
    }

    .issue-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 13px;
        font-weight: 700;
        transition: 0.2s ease;
        border: 1px solid transparent;
    }

    .issue-btn-primary {
        background: #4f46e5;
        color: #fff !important;
    }

    .issue-btn-primary:hover {
        background: #4338ca;
        color: #fff !important;
    }

    .issue-btn-secondary {
        background: #fff;
        color: #374151 !important;
        border-color: #d1d5db;
    }

    .issue-btn-secondary:hover {
        background: #f9fafb;
        color: #111827 !important;
    }

    .photo-modal {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.82);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        padding: 16px;
    }

    .photo-modal.show {
        display: flex;
    }

    .photo-modal-content {
        position: relative;
        width: 100%;
        max-width: 980px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .photo-modal-image {
        display: block;
        max-width: 92vw;
        max-height: 82vh;
        width: auto;
        height: auto;
        border-radius: 14px;
        box-shadow: 0 16px 40px rgba(0,0,0,0.35);
        background: #fff;
    }

    .photo-modal-close {
        position: absolute;
        top: -12px;
        right: -12px;
        width: 38px;
        height: 38px;
        border: none;
        border-radius: 9999px;
        background: #fff;
        color: #111827;
        font-size: 22px;
        font-weight: 700;
        line-height: 1;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0,0,0,0.25);
    }

    .photo-modal-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 44px;
        height: 44px;
        border: none;
        border-radius: 9999px;
        background: rgba(255,255,255,0.16);
        color: #fff;
        font-size: 24px;
        font-weight: 700;
        cursor: pointer;
        backdrop-filter: blur(6px);
        transition: 0.2s ease;
    }

    .photo-modal-nav:hover {
        background: rgba(255,255,255,0.25);
    }

    .photo-modal-prev {
        left: 8px;
    }

    .photo-modal-next {
        right: 8px;
    }

    .photo-modal-counter {
        position: absolute;
        bottom: -42px;
        left: 50%;
        transform: translateX(-50%);
        padding: 7px 12px;
        border-radius: 9999px;
        background: rgba(255,255,255,0.12);
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        backdrop-filter: blur(6px);
    }

    @media (max-width: 640px) {
        .issue-wrap {
            max-width: 100%;
            padding: 0 8px;
        }

        .issue-header,
        .issue-body {
            padding: 14px;
        }

        .issue-title {
            font-size: 18px;
        }

        .issue-photos {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .issue-photo {
            height: 92px;
        }

        .issue-line {
            flex-direction: column;
            gap: 4px;
        }

        .issue-line-value {
            text-align: left;
        }

        .photo-modal-nav {
            width: 40px;
            height: 40px;
        }

        .photo-modal-counter {
            bottom: -36px;
        }
    }
</style>
@endsection

@section('content')
@php
    $issueStatusName = optional($returnIssue->issueStatus)->name ?? $returnIssue->status ?? 'pending';
    $issueStatusLabel = optional($returnIssue->issueStatus)->label ?? ucfirst(str_replace('_', ' ', $issueStatusName));

    $issueStatusClassMap = [
        'pending' => 'badge-pending',
        'reviewing' => 'badge-reviewing',
        'awaiting_payment' => 'badge-awaiting-payment',
        'resolved' => 'badge-resolved',
        'rejected' => 'badge-rejected',
    ];

    $statusNoteClassMap = [
        'pending' => 'note-pending',
        'reviewing' => 'note-reviewing',
        'awaiting_payment' => 'note-awaiting-payment',
        'resolved' => 'note-resolved',
        'rejected' => 'note-rejected',
    ];

    $statusMessages = [
        'pending' => [
            'title' => 'Issue Report Submitted',
            'message' => 'A return issue has been reported for this rental and is currently waiting for review.'
        ],
        'reviewing' => [
            'title' => 'Issue Under Review',
            'message' => 'Our staff is checking the uploaded photos, vehicle condition, and possible charges for this issue.'
        ],
        'awaiting_payment' => [
            'title' => 'Payment Needed',
            'message' => 'The issue has been confirmed and the final charge has been set. You can now proceed with payment.'
        ],
        'resolved' => [
            'title' => 'Issue Resolved',
            'message' => 'This return issue has already been settled and no further action is currently required.'
        ],
        'rejected' => [
            'title' => 'Issue Rejected',
            'message' => 'This reported issue was reviewed and no charge was applied.'
        ],
    ];

    $statusBadgeClass = $issueStatusClassMap[$issueStatusName] ?? 'badge-pending';
    $statusNoteClass = $statusNoteClassMap[$issueStatusName] ?? 'note-pending';
    $statusContent = $statusMessages[$issueStatusName] ?? $statusMessages['pending'];

    $photoUrls = $returnIssue->photos
        ? $returnIssue->photos->map(fn ($photo) => asset('storage/' . $photo->photo_path))->values()->toArray()
        : [];

    $finalCharge = (float) ($returnIssue->final_charge ?? 0);
    $estimatedCharge = (float) ($returnIssue->estimated_charge ?? 0);
@endphp

<div class="issue-page">
    <div class="issue-wrap">
        <div class="issue-card">
            <div class="issue-header">
                <h1 class="issue-title">Return Issue Details</h1>
                <div class="issue-booking">
                    Booking #{{ $returnIssue->booking->id }} —
                    {{ $returnIssue->booking->car->brand->name ?? 'Car' }}
                    {{ $returnIssue->booking->car->model ?? '' }}
                </div>
            </div>

            <div class="issue-body">
                <div class="issue-badges">
                    <span class="issue-badge {{ $statusBadgeClass }}">
                        <span class="issue-badge-dot"></span>
                        {{ $issueStatusLabel }}
                    </span>

                    <span class="issue-badge" style="background:#f3f4f6;color:#374151;border-color:#d1d5db;">
                        {{ ucwords(str_replace('_', ' ', $returnIssue->issue_type)) }}
                    </span>
                </div>

                <div class="status-note {{ $statusNoteClass }}">
                    <h3>{{ $statusContent['title'] }}</h3>
                    <p>{{ $statusContent['message'] }}</p>
                </div>

                <div class="issue-lines">
                    <div class="issue-line">
                        <span class="issue-line-label">Reported At</span>
                        <span class="issue-line-value">
                            {{ $returnIssue->reported_at ? \Carbon\Carbon::parse($returnIssue->reported_at)->format('M d, Y h:i A') : 'N/A' }}
                        </span>
                    </div>

                    <div class="issue-line">
                        <span class="issue-line-label">Estimated Charge</span>
                        <span class="issue-line-value">₱{{ number_format($estimatedCharge, 2) }}</span>
                    </div>

                    <div class="issue-line">
                        <span class="issue-line-label">Final Charge</span>
                        <span class="issue-line-value">
                            {{ $finalCharge > 0 ? '₱' . number_format($finalCharge, 2) : 'Not yet finalized' }}
                        </span>
                    </div>

                    <div class="issue-line">
                        <span class="issue-line-label">Reference</span>
                        <span class="issue-line-value">{{ $returnIssue->title }}</span>
                    </div>
                </div>

                <div class="issue-section">
                    <div class="issue-section-title">Description</div>
                    <p class="issue-description">
                        {{ $returnIssue->description ?: 'No additional description provided.' }}
                    </p>
                </div>

                <div class="issue-section">
                    <div class="issue-section-title">Photos</div>

                    @if($returnIssue->photos->count())
                        <div class="issue-photos">
                            @foreach($returnIssue->photos as $photoIndex => $photo)
                                <div
                                    class="issue-photo-box"
                                    data-gallery='@json($photoUrls)'
                                    data-photo-index="{{ $photoIndex }}"
                                    onclick="openUserIssuePhotoModal(this)"
                                >
                                    <img
                                        src="{{ asset('storage/' . $photo->photo_path) }}"
                                        alt="Issue Photo"
                                        class="issue-photo"
                                    >
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="issue-empty">No photos uploaded.</div>
                    @endif
                </div>

                <div class="issue-footer">
                    @if($issueStatusName === 'awaiting_payment')
                        <a href="{{ route('user.payments', ['booking_id' => $returnIssue->booking->id]) }}" class="issue-btn issue-btn-primary">
                            Pay Now
                        </a>
                    @endif

                    <a href="{{ route('user.rentals.index') }}" class="issue-btn issue-btn-secondary">
                        Back to My Rentals
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="userIssuePhotoModal" class="photo-modal">
    <div class="photo-modal-content">
        <button type="button" class="photo-modal-close" onclick="closeUserIssuePhotoModal()">&times;</button>

        <button type="button" id="userIssuePhotoPrev" class="photo-modal-nav photo-modal-prev" onclick="showPreviousUserIssuePhoto()">
            &#8249;
        </button>

        <img id="userIssuePhotoImage" src="" alt="Preview" class="photo-modal-image">

        <button type="button" id="userIssuePhotoNext" class="photo-modal-nav photo-modal-next" onclick="showNextUserIssuePhoto()">
            &#8250;
        </button>

        <div id="userIssuePhotoCounter" class="photo-modal-counter"></div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('userIssuePhotoModal');

    modal?.addEventListener('click', function (e) {
        if (e.target === modal) {
            closeUserIssuePhotoModal();
        }
    });

    document.addEventListener('keydown', function (e) {
        const isOpen = modal && modal.classList.contains('show');

        if (!isOpen) {
            return;
        }

        if (e.key === 'Escape') {
            closeUserIssuePhotoModal();
        }

        if (e.key === 'ArrowLeft') {
            showPreviousUserIssuePhoto();
        }

        if (e.key === 'ArrowRight') {
            showNextUserIssuePhoto();
        }
    });
});

let userIssueGallery = [];
let userIssuePhotoIndex = 0;

function openUserIssuePhotoModal(element) {
    userIssueGallery = JSON.parse(element.dataset.gallery || '[]');
    userIssuePhotoIndex = parseInt(element.dataset.photoIndex || '0', 10);

    renderUserIssuePhotoModal();

    const modal = document.getElementById('userIssuePhotoModal');
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
}

function renderUserIssuePhotoModal() {
    const image = document.getElementById('userIssuePhotoImage');
    const counter = document.getElementById('userIssuePhotoCounter');
    const prevBtn = document.getElementById('userIssuePhotoPrev');
    const nextBtn = document.getElementById('userIssuePhotoNext');

    if (!userIssueGallery.length) {
        image.src = '';
        counter.textContent = '';
        prevBtn.style.display = 'none';
        nextBtn.style.display = 'none';
        return;
    }

    image.src = userIssueGallery[userIssuePhotoIndex];
    counter.textContent = `${userIssuePhotoIndex + 1} / ${userIssueGallery.length}`;

    if (userIssueGallery.length <= 1) {
        prevBtn.style.display = 'none';
        nextBtn.style.display = 'none';
    } else {
        prevBtn.style.display = 'block';
        nextBtn.style.display = 'block';
    }
}

function showPreviousUserIssuePhoto() {
    if (!userIssueGallery.length) return;
    userIssuePhotoIndex = (userIssuePhotoIndex - 1 + userIssueGallery.length) % userIssueGallery.length;
    renderUserIssuePhotoModal();
}

function showNextUserIssuePhoto() {
    if (!userIssueGallery.length) return;
    userIssuePhotoIndex = (userIssuePhotoIndex + 1) % userIssueGallery.length;
    renderUserIssuePhotoModal();
}

function closeUserIssuePhotoModal() {
    const modal = document.getElementById('userIssuePhotoModal');
    const image = document.getElementById('userIssuePhotoImage');

    modal.classList.remove('show');
    image.src = '';
    document.body.style.overflow = '';

    userIssueGallery = [];
    userIssuePhotoIndex = 0;
}
</script>
@endsection
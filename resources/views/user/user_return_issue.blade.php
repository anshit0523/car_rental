@extends('layouts.userlayout')

@section('custom-styles')
<style>
    .mini-issue-page {
        min-height: 100vh;
        background: #f9fafb;
        padding: 8px 0;
    }

    .mini-issue-wrap {
        width: 100%;
        max-width: 520px;
        margin: 0 auto;
        padding: 0 4px;
    }

    .mini-issue-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        padding: 10px;
    }

    .mini-issue-title {
        font-size: 14px !important;
        font-weight: 700 !important;
        line-height: 1.2 !important;
        color: #111827 !important;
        text-align: center;
        margin-bottom: 8px;
    }

    .mini-issue-booking {
        font-size: 11px !important;
        color: #6b7280 !important;
        line-height: 1.4;
        margin-bottom: 8px;
    }

    .mini-issue-lines {
        margin-bottom: 10px;
    }

    .mini-issue-line {
        display: flex;
        justify-content: space-between;
        gap: 8px;
        padding: 5px 0;
        border-bottom: 1px solid #f3f4f6;
    }

    .mini-issue-line:last-child {
        border-bottom: none;
    }

    .mini-issue-label {
        font-size: 11px !important;
        color: #6b7280 !important;
    }

    .mini-issue-value {
        font-size: 11px !important;
        color: #111827 !important;
        font-weight: 600 !important;
        text-align: right;
    }

    .mini-issue-subtitle {
        font-size: 11px !important;
        font-weight: 700 !important;
        color: #111827 !important;
        margin-bottom: 4px;
        line-height: 1.2;
    }

    .mini-issue-description {
        font-size: 11px !important;
        color: #374151 !important;
        line-height: 1.4;
        margin-bottom: 10px;
    }

    .mini-issue-photos-title {
        font-size: 11px !important;
        font-weight: 700 !important;
        color: #111827 !important;
        margin-bottom: 6px;
    }

    .mini-issue-photos {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 6px;
        margin-bottom: 10px;
    }

    .mini-issue-photo-box {
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        overflow: hidden;
        background: #f9fafb;
        cursor: pointer;
    }

    .mini-issue-photo {
        width: 100%;
        height: 68px;
        object-fit: cover;
        display: block;
        transition: transform 0.2s ease;
    }

    .mini-issue-photo:hover {
        transform: scale(1.03);
    }

    .mini-issue-empty {
        font-size: 11px !important;
        color: #6b7280 !important;
        margin-bottom: 10px;
    }

    .mini-issue-btn {
        display: inline-block;
        background: #4f46e5;
        color: #fff !important;
        text-decoration: none;
        font-size: 11px !important;
        font-weight: 600 !important;
        padding: 6px 10px;
        border-radius: 6px;
        line-height: 1.2;
    }

    .mini-issue-btn:hover {
        background: #4338ca;
        color: #fff !important;
    }

    .photo-modal {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.78);
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
        max-width: 95vw;
        max-height: 90vh;
    }

    .photo-modal-image {
        display: block;
        max-width: 95vw;
        max-height: 90vh;
        width: auto;
        height: auto;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.35);
        background: #fff;
    }

    .photo-modal-close {
        position: absolute;
        top: -12px;
        right: -12px;
        width: 34px;
        height: 34px;
        border: none;
        border-radius: 9999px;
        background: #fff;
        color: #111827;
        font-size: 20px;
        font-weight: 700;
        line-height: 1;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0,0,0,0.25);
    }

    @media (max-width: 580px) {
        .mini-issue-wrap {
            max-width: 280px;
        }

        .mini-issue-photo {
            height: 60px;
        }
    }
</style>
@endsection

@section('content')
<div class="mini-issue-page">
    <div class="mini-issue-wrap">
        <div class="mini-issue-card">

            <div class="mini-issue-title">Return Issue Details</div>

            <div class="mini-issue-booking">
                Booking #{{ $returnIssue->booking->id }} -
                {{ $returnIssue->booking->car->brand->name ?? 'Car' }}
                {{ $returnIssue->booking->car->model ?? '' }}
            </div>

            <div class="mini-issue-lines">
                <div class="mini-issue-line">
                    <span class="mini-issue-label">Issue Type</span>
                    <span class="mini-issue-value">
                        {{ ucwords(str_replace('_', ' ', $returnIssue->issue_type)) }}
                    </span>
                </div>

                <div class="mini-issue-line">
                    <span class="mini-issue-label">Status</span>
                    <span class="mini-issue-value">{{ ucfirst($returnIssue->status) }}</span>
                </div>

                <div class="mini-issue-line">
                    <span class="mini-issue-label">Estimated Charge</span>
                    <span class="mini-issue-value">₱{{ number_format($returnIssue->estimated_charge, 2) }}</span>
                </div>

                <div class="mini-issue-line">
                    <span class="mini-issue-label">Reported At</span>
                    <span class="mini-issue-value">
                        {{ optional($returnIssue->reported_at)->format('M d, Y h:i A') }}
                    </span>
                </div>
            </div>

            <div class="mini-issue-subtitle">{{ $returnIssue->title }}</div>
            <div class="mini-issue-description">
                {{ $returnIssue->description ?: 'No additional description provided.' }}
            </div>

            <div class="mini-issue-photos-title">Photos</div>

            @if($returnIssue->photos->count())
                <div class="mini-issue-photos">
                    @foreach($returnIssue->photos as $photo)
                        <div class="mini-issue-photo-box">
                            <img
                                src="{{ asset('storage/' . $photo->photo_path) }}"
                                alt="Issue Photo"
                                class="mini-issue-photo previewIssuePhoto"
                            >
                        </div>
                    @endforeach
                </div>
            @else
                <div class="mini-issue-empty">No photos uploaded.</div>
            @endif

            <a href="{{ route('user.rentals.index') }}" class="mini-issue-btn">
                Back to My Rentals
            </a>

        </div>
    </div>
</div>

<div id="photoPreviewModal" class="photo-modal">
    <div class="photo-modal-content">
        <button type="button" id="closePhotoPreview" class="photo-modal-close">&times;</button>
        <img id="photoPreviewImage" src="" alt="Preview" class="photo-modal-image">
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('photoPreviewModal');
    const previewImage = document.getElementById('photoPreviewImage');
    const closeBtn = document.getElementById('closePhotoPreview');
    const photos = document.querySelectorAll('.previewIssuePhoto');

    photos.forEach(photo => {
        photo.addEventListener('click', function () {
            previewImage.src = this.src;
            modal.classList.add('show');
        });
    });

    closeBtn?.addEventListener('click', function () {
        modal.classList.remove('show');
        previewImage.src = '';
    });

    modal?.addEventListener('click', function (e) {
        if (e.target === modal) {
            modal.classList.remove('show');
            previewImage.src = '';
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            modal.classList.remove('show');
            previewImage.src = '';
        }
    });
});
</script>
@endsection
@extends('layouts.app')
@section('title', $article->title)
@section('meta_desc', Str::limit(strip_tags($article->content), 160))

@push('styles')
<style>
    .detail-wrap { max-width:1200px; margin:0 auto; padding:56px 24px; display:grid; grid-template-columns:1fr 340px; gap:48px; align-items:start; }

    /* ===== ARTICLE MAIN ===== */
    .article-main {}
    .article-cover {
        width:100%; height:400px; border-radius:var(--radius-lg); overflow:hidden;
        background:linear-gradient(135deg,var(--teal-50),#e0f2fe);
        display:flex; align-items:center; justify-content:center;
        font-size:96px; color:var(--teal); margin-bottom:36px;
    }
    .article-cover img { width:100%; height:100%; object-fit:cover; }
    .article-cover .placeholder-icon { opacity:.25; }

    .article-header { margin-bottom:28px; }
    .article-cats { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:16px; }
    .article-cat {
        display:inline-flex; align-items:center; gap:6px;
        background:var(--teal-pale); color:var(--teal-dark);
        padding:5px 12px; border-radius:20px; font-size:12px; font-weight:700;
        text-transform:uppercase; letter-spacing:.5px;
    }
    .article-main-title { font-size:34px; font-weight:800; color:var(--gray-800); line-height:1.25; margin-bottom:20px; }
    .article-byline {
        display:flex; align-items:center; gap:16px; flex-wrap:wrap;
        padding:16px 20px; background:var(--gray-50); border-radius:14px;
        border:1px solid var(--gray-200);
    }
    .byline-author { display:flex; align-items:center; gap:10px; }
    .byline-ava {
        width:40px; height:40px; border-radius:12px; background:var(--teal-pale);
        display:flex; align-items:center; justify-content:center; color:var(--teal); font-size:18px;
    }
    .byline-name { font-size:14px; font-weight:700; color:var(--gray-800); }
    .byline-role { font-size:12px; color:var(--gray-400); }
    .byline-sep { width:1px; height:32px; background:var(--gray-200); }
    .byline-meta { font-size:13px; color:var(--gray-500); display:flex; align-items:center; gap:6px; }

    /* ===== ARTICLE CONTENT ===== */
    .article-content {
        font-size:16px; color:var(--gray-700); line-height:1.85;
        border-top:1px solid var(--gray-200); padding-top:28px; margin-top:28px;
    }
    .article-content p { margin-bottom:18px; }
    .article-content h2 { font-size:22px; font-weight:800; color:var(--gray-800); margin:32px 0 14px; }
    .article-content h3 { font-size:18px; font-weight:700; color:var(--gray-800); margin:24px 0 10px; }
    .article-content ul, .article-content ol { padding-left:24px; margin-bottom:18px; }
    .article-content li { margin-bottom:8px; }
    .article-content strong { color:var(--gray-800); }
    .article-content blockquote {
        border-left:4px solid var(--teal); padding:16px 20px; margin:24px 0;
        background:var(--teal-50); border-radius:0 12px 12px 0;
        font-style:italic; color:var(--gray-600);
    }
    .article-content img { border-radius:12px; margin:20px 0; max-width:100%; }

    /* ===== SHARE ===== */
    .article-share {
        display:flex; align-items:center; gap:12px; flex-wrap:wrap;
        padding:20px 24px; background:var(--gray-50); border-radius:14px;
        border:1px solid var(--gray-200); margin-top:36px;
    }
    .share-label { font-size:14px; font-weight:700; color:var(--gray-700); }
    .share-btn {
        display:inline-flex; align-items:center; gap:7px; padding:9px 16px;
        border-radius:10px; font-size:13px; font-weight:600; transition:all .2s;
    }
    .share-btn.wa { background:#25d366; color:#fff; }
    .share-btn.wa:hover { background:#1ebe5d; transform:translateY(-1px); }
    .share-btn.copy { background:var(--gray-100); color:var(--gray-700); border:1.5px solid var(--gray-200); cursor:pointer; font-family:inherit; }
    .share-btn.copy:hover { background:var(--gray-200); }

    /* ===== BACK LINK ===== */
    .back-link {
        display:inline-flex; align-items:center; gap:8px;
        color:var(--gray-500); font-size:14px; font-weight:600;
        margin-bottom:28px; transition:color .2s;
    }
    .back-link:hover { color:var(--teal); }

    /* ===== SIDEBAR ===== */
    .sidebar { position:sticky; top:88px; }
    .sidebar-card {
        background:#fff; border-radius:var(--radius-lg); border:1px solid var(--gray-200);
        overflow:hidden; margin-bottom:24px; box-shadow:var(--shadow);
    }
    .sidebar-card-head {
        padding:16px 20px; border-bottom:1px solid var(--gray-100);
        font-size:13px; font-weight:800; color:var(--gray-700);
        text-transform:uppercase; letter-spacing:1px;
        display:flex; align-items:center; gap:8px;
    }
    .sidebar-card-head i { color:var(--teal); }

    /* author card */
    .author-card-body { padding:20px; text-align:center; }
    .author-big-ava {
        width:64px; height:64px; border-radius:18px; background:var(--teal-pale);
        display:flex; align-items:center; justify-content:center;
        color:var(--teal); font-size:28px; margin:0 auto 12px;
    }
    .author-card-name { font-size:16px; font-weight:800; color:var(--gray-800); margin-bottom:4px; }
    .author-card-role { font-size:13px; color:var(--teal); font-weight:600; margin-bottom:10px; }
    .author-card-desc { font-size:13px; color:var(--gray-500); line-height:1.65; }

    /* related articles */
    .related-item {
        display:flex; gap:12px; padding:14px 16px;
        border-bottom:1px solid var(--gray-100); transition:background .2s;
    }
    .related-item:last-child { border-bottom:none; }
    .related-item:hover { background:var(--gray-50); }
    .related-thumb {
        width:60px; height:60px; border-radius:10px; flex-shrink:0; overflow:hidden;
        background:linear-gradient(135deg,var(--teal-50),#e0f2fe);
        display:flex; align-items:center; justify-content:center; font-size:24px; color:var(--teal);
    }
    .related-thumb img { width:100%; height:100%; object-fit:cover; }
    .related-thumb .ph { opacity:.3; }
    .related-title { font-size:13px; font-weight:700; color:var(--gray-800); line-height:1.4; margin-bottom:4px; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
    .related-date { font-size:11px; color:var(--gray-400); }

    /* cta sidebar */
    .cta-sidebar { padding:20px; text-align:center; }
    .cta-sidebar p { font-size:13px; color:var(--gray-500); line-height:1.65; margin-bottom:14px; }

    @media(max-width:1024px) {
        .detail-wrap { grid-template-columns:1fr; }
        .sidebar { position:static; }
        .article-main-title { font-size:26px; }
        .article-cover { height:260px; }
    }
    @media(max-width:640px) {
        .detail-wrap { padding:32px 16px; }
        .article-main-title { font-size:22px; }
        .article-cover { height:200px; font-size:64px; }
    }
</style>
@endpush

@section('content')

<div class="detail-wrap">

    <!-- ===== MAIN ARTICLE ===== -->
    <article class="article-main">

        <a href="{{ route('artikel') }}" class="back-link">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Semua Artikel
        </a>

        <!-- Cover Image -->
        <div class="article-cover">
            @if($article->image_url)
                <img src="{{ $article->image_url }}" alt="{{ $article->title }}">
            @else
                <i class="fa-solid fa-paw placeholder-icon"></i>
            @endif
        </div>

        <!-- Header -->
        <div class="article-header">
            <div class="article-cats">
                <span class="article-cat"><i class="fa-solid fa-tag"></i> Kesehatan Hewan</span>
            </div>
            <h1 class="article-main-title">{{ $article->title }}</h1>
            <div class="article-byline">
                <div class="byline-author">
                    <div class="byline-ava"><i class="fa-solid fa-user-doctor"></i></div>
                    <div>
                        <div class="byline-name">{{ $article->author->name ?? 'Tim Vetra' }}</div>
                        <div class="byline-role">Dokter Hewan Vetra</div>
                    </div>
                </div>
                <div class="byline-sep"></div>
                <div class="byline-meta"><i class="fa-regular fa-calendar"></i> {{ $article->created_at->translatedFormat('d F Y') }}</div>
                <div class="byline-meta"><i class="fa-regular fa-clock"></i> {{ max(1, (int)(str_word_count(strip_tags($article->content)) / 200)) }} menit baca</div>
            </div>
        </div>

        <!-- Content -->
        <div class="article-content">
            {!! nl2br(e($article->content)) !!}
        </div>

        <!-- Share -->
        <div class="article-share">
            <span class="share-label"><i class="fa-solid fa-share-nodes" style="color:var(--teal)"></i> Bagikan:</span>
            <a href="https://wa.me/?text={{ urlencode($article->title . ' - ' . url()->current()) }}"
               target="_blank" class="share-btn wa">
                <i class="fa-brands fa-whatsapp"></i> WhatsApp
            </a>
            <button class="share-btn copy" onclick="copyLink(this)">
                <i class="fa-solid fa-link"></i> Salin Link
            </button>
        </div>

    </article>

    <!-- ===== SIDEBAR ===== -->
    <aside class="sidebar">

        <!-- Author -->
        <div class="sidebar-card">
            <div class="sidebar-card-head"><i class="fa-solid fa-user-doctor"></i> Tentang Penulis</div>
            <div class="author-card-body">
                <div class="author-big-ava"><i class="fa-solid fa-user-doctor"></i></div>
                <div class="author-card-name">{{ $article->author->name ?? 'Tim Vetra' }}</div>
                <div class="author-card-role">Dokter Hewan Vetra</div>
                <p class="author-card-desc">Dokter hewan berlisensi yang berdedikasi memberikan informasi kesehatan hewan yang akurat dan terpercaya.</p>
            </div>
        </div>

        <!-- Related Articles -->
        @if($related->count() > 0)
        <div class="sidebar-card">
            <div class="sidebar-card-head"><i class="fa-solid fa-newspaper"></i> Artikel Terkait</div>
            @foreach($related as $rel)
            <a href="{{ route('artikel.detail', $rel) }}" class="related-item" style="text-decoration:none;display:flex;">
                <div class="related-thumb">
                    @if($rel->image_url)
                        <img src="{{ $rel->image_url }}" alt="{{ $rel->title }}">
                    @else
                        <i class="fa-solid fa-paw ph"></i>
                    @endif
                </div>
                <div>
                    <div class="related-title">{{ $rel->title }}</div>
                    <div class="related-date"><i class="fa-regular fa-calendar"></i> {{ $rel->created_at->format('d M Y') }}</div>
                </div>
            </a>
            @endforeach
        </div>
        @endif

        <!-- CTA -->
        <div class="sidebar-card">
            <div class="sidebar-card-head"><i class="fa-solid fa-paw"></i> Konsultasi Dokter</div>
            <div class="cta-sidebar">
                <p>Punya pertanyaan tentang kesehatan hewan peliharaanmu? Konsultasikan langsung dengan dokter hewan kami.</p>
                <a href="{{ route('register') }}" class="btn-primary" style="width:100%;justify-content:center;">
                    <i class="fa-solid fa-comments"></i> Konsultasi Sekarang
                </a>
            </div>
        </div>

    </aside>

</div>

@endsection

@push('scripts')
<script>
function copyLink(btn) {
    navigator.clipboard.writeText(window.location.href).then(() => {
        const orig = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-check"></i> Tersalin!';
        btn.style.background = 'var(--teal-pale)';
        btn.style.color = 'var(--teal)';
        setTimeout(() => {
            btn.innerHTML = orig;
            btn.style.background = '';
            btn.style.color = '';
        }, 2000);
    });
}
</script>
@endpush

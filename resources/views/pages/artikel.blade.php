@extends('layouts.app')
@section('title', 'Artikel Kesehatan Hewan')
@section('meta_desc', 'Baca artikel kesehatan hewan dari dokter berlisensi. Tips perawatan, nutrisi, dan pencegahan penyakit hewan peliharaan.')

@push('styles')
<style>
    /* FILTER BAR */
    .filter-bar {
        background:#fff; border-bottom:1px solid var(--gray-200);
        padding:16px 24px; position:sticky; top:68px; z-index:90;
        box-shadow:0 2px 8px rgba(0,0,0,.04);
    }
    .filter-inner { max-width:1200px; margin:0 auto; display:flex; align-items:center; gap:12px; flex-wrap:wrap; }
    .search-box {
        flex:1; min-width:220px; display:flex; align-items:center; gap:10px;
        background:var(--gray-50); border:1.5px solid var(--gray-200); border-radius:12px;
        padding:10px 16px; transition:all .2s;
    }
    .search-box:focus-within { border-color:var(--teal); background:#fff; box-shadow:0 0 0 3px rgba(13,148,136,.1); }
    .search-box i { color:var(--gray-400); font-size:14px; }
    .search-box input { border:none; background:transparent; outline:none; font-size:14px; font-family:inherit; color:var(--gray-800); width:100%; }
    .result-count { font-size:13px; color:var(--gray-500); font-weight:500; white-space:nowrap; }
    /* ARTICLES GRID */
    .articles-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:28px; }
    .article-card {
        border-radius:var(--radius-lg); border:1px solid var(--gray-200);
        overflow:hidden; transition:all .3s; background:#fff; display:flex; flex-direction:column;
    }
    .article-card:hover { transform:translateY(-5px); box-shadow:var(--shadow-lg); border-color:var(--teal-light); }
    .article-img {
        height:200px; background:linear-gradient(135deg,var(--teal-50),#e0f2fe);
        display:flex; align-items:center; justify-content:center;
        font-size:64px; color:var(--teal); overflow:hidden; position:relative; flex-shrink:0;
    }
    .article-img img { width:100%; height:100%; object-fit:cover; }
    .article-img .placeholder-icon { opacity:.3; }
    .article-body { padding:22px; flex:1; display:flex; flex-direction:column; }
    .article-meta { display:flex; align-items:center; gap:12px; margin-bottom:12px; flex-wrap:wrap; }
    .article-author { font-size:12px; color:var(--teal); font-weight:600; display:flex; align-items:center; gap:5px; }
    .article-date { font-size:12px; color:var(--gray-400); display:flex; align-items:center; gap:4px; }
    .article-title { font-size:17px; font-weight:700; color:var(--gray-800); margin-bottom:10px; line-height:1.4; }
    .article-excerpt { font-size:13px; color:var(--gray-500); line-height:1.75; flex:1; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden; }
    .article-footer { padding:0 22px 22px; }
    .btn-read { display:inline-flex; align-items:center; gap:6px; color:var(--teal); font-size:13px; font-weight:700; transition:gap .2s; }
    .btn-read:hover { gap:10px; }
    /* FEATURED ARTICLE */
    .featured-article {
        background:#fff; border-radius:var(--radius-lg); border:1px solid var(--gray-200);
        overflow:hidden; display:grid; grid-template-columns:1fr 1fr; margin-bottom:48px;
        box-shadow:var(--shadow); transition:all .3s;
    }
    .featured-article:hover { box-shadow:var(--shadow-lg); border-color:var(--teal-light); }
    .featured-img {
        min-height:280px; background:linear-gradient(135deg,var(--teal-50),#e0f2fe);
        display:flex; align-items:center; justify-content:center; font-size:80px; color:var(--teal);
        overflow:hidden;
    }
    .featured-img img { width:100%; height:100%; object-fit:cover; }
    .featured-img .placeholder-icon { opacity:.3; }
    .featured-body { padding:36px; display:flex; flex-direction:column; justify-content:center; }
    .featured-tag {
        display:inline-flex; align-items:center; gap:6px;
        background:var(--teal-pale); color:var(--teal-dark);
        padding:5px 12px; border-radius:20px; font-size:11px; font-weight:700;
        text-transform:uppercase; letter-spacing:.5px; margin-bottom:16px;
    }
    .featured-title { font-size:24px; font-weight:800; color:var(--gray-800); margin-bottom:12px; line-height:1.35; }
    .featured-excerpt { font-size:14px; color:var(--gray-500); line-height:1.75; margin-bottom:20px; }
    .featured-meta { display:flex; align-items:center; gap:14px; margin-bottom:20px; }
    /* PAGINATION */
    .pagination { display:flex; align-items:center; justify-content:center; gap:6px; margin-top:48px; }
    .page-btn {
        width:40px; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center;
        font-size:14px; font-weight:600; border:1.5px solid var(--gray-200); color:var(--gray-600);
        cursor:pointer; transition:all .2s; background:#fff;
    }
    .page-btn:hover, .page-btn.active { background:var(--teal); color:#fff; border-color:var(--teal); }
    .page-btn.disabled { opacity:.4; cursor:not-allowed; }
    @media(max-width:1024px) { .articles-grid{grid-template-columns:repeat(2,1fr);} }
    @media(max-width:768px) {
        .articles-grid{grid-template-columns:1fr;}
        .featured-article{grid-template-columns:1fr;}
        .featured-img{min-height:200px;}
    }
</style>
@endpush

@section('content')

<!-- PAGE HEADER -->
<div class="page-header">
    <div class="page-header-inner">
        <div class="page-header-tag"><i class="fa-solid fa-newspaper"></i> Edukasi Kesehatan</div>
        <h1>Artikel Kesehatan Hewan</h1>
        <p>Tips, panduan, dan informasi kesehatan hewan dari dokter hewan berpengalaman. Selalu update untuk hewan peliharaanmu.</p>
    </div>
</div>

<!-- FILTER BAR -->
<div class="filter-bar">
    <div class="filter-inner">
        <div class="search-box">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="searchInput" placeholder="Cari artikel..." oninput="filterArticles()">
        </div>
        <span class="result-count" id="resultCount">{{ $articles->total() }} artikel</span>
    </div>
</div>

<section style="padding:56px 24px;">
    <div class="container">

        @if($articles->count() > 0)

        {{-- FEATURED: artikel pertama --}}
        @php $featured = $articles->first(); @endphp
        @if($articles->currentPage() == 1)
        <div class="featured-article">
            <div class="featured-img">
                @if($featured->image_url)
                    <img src="{{ $featured->image_url }}" alt="{{ $featured->title }}">
                @else
                    <i class="fa-solid fa-paw placeholder-icon"></i>
                @endif
            </div>
            <div class="featured-body">
                <div class="featured-tag"><i class="fa-solid fa-star"></i> Artikel Terbaru</div>
                <div class="featured-title">{{ $featured->title }}</div>
                <div class="featured-meta">
                    <span class="article-author"><i class="fa-solid fa-user-doctor"></i> {{ $featured->author->name ?? 'Tim Vetra' }}</span>
                    <span class="article-date"><i class="fa-regular fa-calendar"></i> {{ $featured->created_at->format('d M Y') }}</span>
                </div>
                <p class="featured-excerpt">{{ Str::limit(strip_tags($featured->content), 200) }}</p>
                <a href="{{ route('artikel.detail', $featured) }}" class="btn-primary" style="align-self:flex-start;">Baca Selengkapnya <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>
        @endif

        {{-- GRID ARTIKEL --}}
        <div class="articles-grid" id="articlesGrid">
            @foreach($articles->skip($articles->currentPage() == 1 ? 1 : 0) as $article)
            <div class="article-card" data-title="{{ strtolower($article->title) }}">
                <div class="article-img">
                    @if($article->image_url)
                        <img src="{{ $article->image_url }}" alt="{{ $article->title }}">
                    @else
                        <i class="fa-solid fa-paw placeholder-icon"></i>
                    @endif
                </div>
                <div class="article-body">
                    <div class="article-meta">
                        <span class="article-author"><i class="fa-solid fa-user-doctor"></i> {{ $article->author->name ?? 'Tim Vetra' }}</span>
                        <span class="article-date"><i class="fa-regular fa-calendar"></i> {{ $article->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="article-title">{{ $article->title }}</div>
                    <div class="article-excerpt">{{ Str::limit(strip_tags($article->content), 150) }}</div>
                </div>
                <div class="article-footer">
                    <a href="{{ route('artikel.detail', $article) }}" class="btn-read">Baca Selengkapnya <i class="fa-solid fa-arrow-right"></i></a>
                </div>
            </div>
            @endforeach
        </div>

        {{-- PAGINATION --}}
        @if($articles->hasPages())
        <div class="pagination">
            @if($articles->onFirstPage())
                <span class="page-btn disabled"><i class="fa-solid fa-chevron-left"></i></span>
            @else
                <a href="{{ $articles->previousPageUrl() }}" class="page-btn"><i class="fa-solid fa-chevron-left"></i></a>
            @endif
            @foreach($articles->getUrlRange(1, $articles->lastPage()) as $page => $url)
                <a href="{{ $url }}" class="page-btn {{ $articles->currentPage() == $page ? 'active' : '' }}">{{ $page }}</a>
            @endforeach
            @if($articles->hasMorePages())
                <a href="{{ $articles->nextPageUrl() }}" class="page-btn"><i class="fa-solid fa-chevron-right"></i></a>
            @else
                <span class="page-btn disabled"><i class="fa-solid fa-chevron-right"></i></span>
            @endif
        </div>
        @endif

        @else
        <div class="empty-state">
            <i class="fa-solid fa-newspaper"></i>
            <p>Belum ada artikel yang dipublikasikan. Pantau terus untuk update terbaru!</p>
        </div>
        @endif

    </div>
</section>

@endsection

@push('scripts')
<script>
function filterArticles() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    const cards = document.querySelectorAll('#articlesGrid .article-card');
    let visible = 0;
    cards.forEach(c => {
        const show = c.dataset.title.includes(q);
        c.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    document.getElementById('resultCount').textContent = visible + ' artikel';
}
</script>
@endpush

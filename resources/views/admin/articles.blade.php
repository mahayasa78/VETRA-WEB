@extends('layouts.app')
@section('title', 'Kelola Artikel')

@push('styles')
<style>
    .admin-articles { max-width: 1400px; margin: 0 auto; padding: 40px 24px; }
    .page-header { margin-bottom: 32px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
    .page-header h1 { font-size: 32px; font-weight: 800; color: var(--gray-800); }
    
    .btn-primary {
        padding: 12px 24px; background: var(--teal); color: #fff;
        border: none; border-radius: 12px; font-size: 14px; font-weight: 700;
        cursor: pointer; transition: all .2s; display: flex; align-items: center; gap: 8px;
    }
    .btn-primary:hover { background: var(--teal-dark); transform: translateY(-1px); }
    
    .filter-tabs {
        display: flex; gap: 8px; background: #fff; padding: 6px; border-radius: 12px;
        border: 1px solid var(--gray-200); margin-bottom: 24px; flex-wrap: wrap;
    }
    .filter-tab {
        padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600;
        border: none; background: transparent; color: var(--gray-600); cursor: pointer;
        transition: all .2s;
    }
    .filter-tab:hover { background: var(--gray-100); color: var(--gray-800); }
    .filter-tab.active { background: var(--teal); color: #fff; }
    
    .articles-grid {
        display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 24px;
    }
    
    .article-card {
        background: #fff; border-radius: 16px; overflow: hidden;
        border: 1px solid var(--gray-200); box-shadow: var(--shadow);
        transition: all .2s; cursor: pointer;
    }
    .article-card:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(0,0,0,.1); }
    
    .article-img {
        width: 100%; height: 180px; object-fit: cover; background: var(--gray-100);
    }
    
    .article-body {
        padding: 20px;
    }
    
    .article-title {
        font-size: 18px; font-weight: 700; color: var(--gray-800);
        margin-bottom: 8px; line-height: 1.4;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .article-desc {
        font-size: 14px; color: var(--gray-600); line-height: 1.6;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
        overflow: hidden; margin-bottom: 16px;
    }
    
    .article-meta {
        display: flex; justify-content: space-between; align-items: center;
        font-size: 13px; color: var(--gray-500); margin-bottom: 12px;
    }
    
    .status-badge {
        display: inline-block; padding: 4px 12px; border-radius: 8px;
        font-size: 12px; font-weight: 600;
    }
    .status-badge.published { background: #d1fae5; color: #10b981; }
    .status-badge.draft { background: #fef3c7; color: #f59e0b; }
    
    .article-actions {
        display: flex; gap: 8px; padding-top: 12px; border-top: 1px solid var(--gray-100);
    }
    
    .btn-edit, .btn-delete {
        flex: 1; padding: 8px; border-radius: 8px; font-size: 13px; font-weight: 600;
        border: none; cursor: pointer; transition: all .2s; display: flex;
        align-items: center; justify-content: center; gap: 6px;
    }
    
    .btn-edit { background: #dbeafe; color: #3b82f6; }
    .btn-edit:hover { background: #3b82f6; color: #fff; }
    
    .btn-delete { background: #fee2e2; color: #dc2626; }
    .btn-delete:hover { background: #dc2626; color: #fff; }
    
    .empty-state {
        text-align: center; padding: 80px 24px; color: var(--gray-400);
        background: #fff; border-radius: 16px; border: 1px solid var(--gray-200);
        grid-column: 1 / -1;
    }
    .empty-state i { font-size: 48px; margin-bottom: 16px; }
    
    /* Modal */
    .modal-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,.5); backdrop-filter: blur(4px);
        display: none; align-items: center; justify-content: center; z-index: 1000; padding: 20px;
        overflow-y: auto;
    }
    .modal-overlay.open { display: flex; }
    .modal-box {
        background: #fff; border-radius: 20px; padding: 32px; width: 100%; max-width: 700px;
        box-shadow: 0 20px 60px rgba(0,0,0,.3); margin: 20px;
    }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .modal-header h3 { font-size: 20px; font-weight: 800; color: var(--gray-800); }
    .modal-close {
        width: 32px; height: 32px; border-radius: 50%; background: var(--gray-100);
        border: none; cursor: pointer; display: flex; align-items: center; justify-content: center;
        color: var(--gray-600); transition: all .2s;
    }
    .modal-close:hover { background: var(--gray-200); }
    
    .form-group { margin-bottom: 18px; }
    .form-group label { display: block; font-size: 13px; font-weight: 700; color: var(--gray-700); margin-bottom: 8px; }
    .form-group input, .form-group textarea, .form-group select {
        width: 100%; padding: 11px 14px; border: 1.5px solid var(--gray-200);
        border-radius: 10px; font-size: 14px; font-family: inherit; transition: all .2s;
    }
    .form-group textarea {
        min-height: 120px; resize: vertical;
    }
    .form-group input:focus, .form-group textarea:focus, .form-group select:focus {
        outline: none; border-color: var(--teal); box-shadow: 0 0 0 3px rgba(13,148,136,.1);
    }
    
    .modal-actions { display: flex; gap: 10px; margin-top: 24px; }
    .btn-cancel {
        flex: 1; padding: 12px; border-radius: 10px; font-size: 14px; font-weight: 600;
        background: var(--gray-100); color: var(--gray-700); border: none; cursor: pointer;
    }
    .btn-submit {
        flex: 1; padding: 12px; border-radius: 10px; font-size: 14px; font-weight: 700;
        background: var(--teal); color: #fff; border: none; cursor: pointer;
    }
    .btn-submit:hover { background: var(--teal-dark); }
    
    .alert {
        padding: 14px 18px; border-radius: 12px; margin-bottom: 20px;
        display: none; align-items: center; gap: 10px;
    }
    .alert.success { background: #f0fdf4; border: 1px solid #86efac; color: #16a34a; }
    .alert.error { background: #fef2f2; border: 1px solid #fca5a5; color: #dc2626; }
    
    .char-count {
        text-align: right; font-size: 12px; color: var(--gray-500); margin-top: 4px;
    }
</style>
@endpush

@section('content')
<div style="padding-top:68px;background:var(--gray-50);min-height:100vh;">
    <div class="admin-articles">
        <div class="alert success" id="successAlert">
            <i class="fa-solid fa-circle-check"></i>
            <span id="successMessage"></span>
        </div>
        
        <div class="page-header">
            <h1>📰 Kelola Artikel</h1>
            <button class="btn-primary" onclick="openCreateModal()">
                <i class="fa-solid fa-plus"></i> Buat Artikel Baru
            </button>
        </div>

        <div class="filter-tabs">
            <button class="filter-tab active" onclick="filterArticles('all')">Semua</button>
            <button class="filter-tab" onclick="filterArticles('published')">Published</button>
            <button class="filter-tab" onclick="filterArticles('draft')">Draft</button>
        </div>

        <div class="articles-grid" id="articlesGrid">
            <div class="empty-state">
                <i class="fa-solid fa-spinner fa-spin"></i>
                <p>Memuat artikel...</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Create/Edit -->
<div class="modal-overlay" id="articleModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3 id="modalTitle">Buat Artikel Baru</h3>
            <button class="modal-close" onclick="closeModal()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form id="articleForm">
            <input type="hidden" id="articleId">
            <div class="form-group">
                <label for="articleTitle">Judul Artikel</label>
                <input type="text" id="articleTitle" required maxlength="255">
                <div class="char-count"><span id="titleCount">0</span>/255</div>
            </div>
            <div class="form-group">
                <label for="articleDesc">Deskripsi Singkat</label>
                <textarea id="articleDesc" required rows="3" maxlength="300"></textarea>
                <div class="char-count"><span id="descCount">0</span>/300</div>
            </div>
            <div class="form-group">
                <label for="articleContent">Konten Artikel</label>
                <textarea id="articleContent" required rows="8"></textarea>
            </div>
            <div class="form-group">
                <label for="articleImage">URL Gambar</label>
                <input type="url" id="articleImage" placeholder="https://example.com/image.jpg">
            </div>
            <div class="form-group">
                <label for="articleTags">Tags (pisahkan dengan koma)</label>
                <input type="text" id="articleTags" placeholder="kesehatan, hewan peliharaan, tips">
            </div>
            <div class="form-group">
                <label for="articleStatus">Status</label>
                <select id="articleStatus" required>
                    <option value="1">Published</option>
                    <option value="0">Draft</option>
                </select>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn-submit" id="submitBtn">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    const token = localStorage.getItem('vetra_token');
    const user = JSON.parse(localStorage.getItem('vetra_user'));

    if (!token || !user || user.role !== 'admin') {
        window.location.href = '/login';
    }

    let articles = [];
    let currentFilter = 'all';
    let editMode = false;
    let isLoading = false; // Prevent multiple simultaneous loads

    // Character counters
    document.getElementById('articleTitle').addEventListener('input', (e) => {
        document.getElementById('titleCount').textContent = e.target.value.length;
    });
    document.getElementById('articleDesc').addEventListener('input', (e) => {
        document.getElementById('descCount').textContent = e.target.value.length;
    });

    async function loadArticles() {
        if (isLoading) {
            console.log('Already loading, skipping...');
            return;
        }
        
        isLoading = true;
        const container = document.getElementById('articlesGrid');
        container.innerHTML = `
            <div class="empty-state">
                <i class="fa-solid fa-spinner fa-spin"></i>
                <p>Memuat artikel...</p>
            </div>`;
        
        try {
            const url = '/api/admin/articles' + (currentFilter !== 'all' ? `?status=${currentFilter}` : '');
            console.log('Loading articles from:', url);
            
            const res = await fetch(url, {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            console.log('Response status:', res.status);

            if (res.ok) {
                const data = await res.json();
                console.log('Articles loaded:', data);
                articles = data.data || data;
                renderArticles();
            } else if (res.status === 401) {
                localStorage.clear();
                window.location.href = '/login';
            } else {
                console.error('Error response:', res.status);
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fa-solid fa-exclamation-triangle"></i>
                        <p>Gagal memuat artikel. Status: ${res.status}</p>
                    </div>`;
            }
        } catch (error) {
            console.error('Error loading articles:', error);
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fa-solid fa-exclamation-triangle"></i>
                    <p>Gagal terhubung ke server: ${error.message}</p>
                </div>`;
        } finally {
            isLoading = false;
        }
    }

    function renderArticles() {
        const container = document.getElementById('articlesGrid');

        if (articles.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fa-solid fa-newspaper"></i>
                    <p>Belum ada artikel untuk filter ini</p>
                </div>`;
            return;
        }

        container.innerHTML = articles.map(a => {
            const imgUrl = a.image_url && a.image_url.startsWith('http') 
                ? a.image_url 
                : 'https://via.placeholder.com/400x200?text=No+Image';
            
            return `
            <div class="article-card">
                <img src="${imgUrl}" 
                     alt="${a.title || 'Article'}" class="article-img" 
                     onerror="if(this.src!=='https://via.placeholder.com/400x200?text=No+Image')this.src='https://via.placeholder.com/400x200?text=No+Image'">
                <div class="article-body">
                    <div class="article-meta">
                        <span>${new Date(a.created_at).toLocaleDateString('id-ID')}</span>
                        <span class="status-badge ${a.is_published ? 'published' : 'draft'}">
                            ${a.is_published ? 'Published' : 'Draft'}
                        </span>
                    </div>
                    <h3 class="article-title">${a.title || 'Untitled'}</h3>
                    <p class="article-desc">${a.description || ''}</p>
                    <div class="article-actions">
                        <button class="btn-edit" onclick="openEditModal(${a.id})">
                            <i class="fa-solid fa-pen"></i> Edit
                        </button>
                        <button class="btn-delete" onclick="deleteArticle(${a.id})">
                            <i class="fa-solid fa-trash"></i> Hapus
                        </button>
                    </div>
                </div>
            </div>
        `;
        }).join('');
    }

    function filterArticles(status) {
        currentFilter = status;
        document.querySelectorAll('.filter-tab').forEach(tab => tab.classList.remove('active'));
        event.target.classList.add('active');
        loadArticles();
    }

    function openCreateModal() {
        editMode = false;
        document.getElementById('modalTitle').textContent = 'Buat Artikel Baru';
        document.getElementById('articleForm').reset();
        document.getElementById('articleId').value = '';
        document.getElementById('titleCount').textContent = '0';
        document.getElementById('descCount').textContent = '0';
        document.getElementById('articleModal').classList.add('open');
    }

    function openEditModal(id) {
        editMode = true;
        const article = articles.find(a => a.id === id);
        if (!article) return;

        document.getElementById('modalTitle').textContent = 'Edit Artikel';
        document.getElementById('articleId').value = article.id;
        document.getElementById('articleTitle').value = article.title;
        document.getElementById('articleDesc').value = article.description;
        document.getElementById('articleContent').value = article.content;
        document.getElementById('articleImage').value = article.image_url || '';
        document.getElementById('articleTags').value = article.tags || '';
        document.getElementById('articleStatus').value = article.is_published ? '1' : '0';
        
        document.getElementById('titleCount').textContent = article.title.length;
        document.getElementById('descCount').textContent = article.description.length;
        
        document.getElementById('articleModal').classList.add('open');
    }

    function closeModal() {
        document.getElementById('articleModal').classList.remove('open');
    }

    document.getElementById('articleForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const id = document.getElementById('articleId').value;
        const data = {
            title: document.getElementById('articleTitle').value,
            description: document.getElementById('articleDesc').value,
            content: document.getElementById('articleContent').value,
            image_url: document.getElementById('articleImage').value,
            tags: document.getElementById('articleTags').value,
            is_published: document.getElementById('articleStatus').value === '1'
        };

        const btn = document.getElementById('submitBtn');
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';
        btn.disabled = true;

        try {
            const url = editMode ? `/api/admin/articles/${id}` : '/api/admin/articles';
            const method = editMode ? 'PUT' : 'POST';

            const res = await fetch(url, {
                method: method,
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            if (res.ok) {
                showSuccess(editMode ? 'Artikel berhasil diupdate!' : 'Artikel berhasil dibuat!');
                closeModal();
                loadArticles();
            } else {
                const error = await res.json();
                alert('Error: ' + (error.message || 'Gagal menyimpan artikel'));
            }
        } catch (error) {
            alert('Gagal terhubung ke server');
        } finally {
            btn.innerHTML = 'Simpan';
            btn.disabled = false;
        }
    });

    async function deleteArticle(id) {
        const article = articles.find(a => a.id === id);
        if (!confirm(`Yakin ingin menghapus artikel "${article.title}"?`)) return;

        try {
            const res = await fetch(`/api/admin/articles/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            if (res.ok) {
                showSuccess('Artikel berhasil dihapus!');
                loadArticles();
            } else {
                alert('Gagal menghapus artikel');
            }
        } catch (error) {
            alert('Gagal terhubung ke server');
        }
    }

    function showSuccess(message) {
        const alert = document.getElementById('successAlert');
        document.getElementById('successMessage').textContent = message;
        alert.style.display = 'flex';
        setTimeout(() => alert.style.display = 'none', 5000);
    }

    loadArticles();
</script>
@endsection

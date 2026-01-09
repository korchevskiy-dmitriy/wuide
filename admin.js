document.addEventListener('DOMContentLoaded', () => {
    // 1. Проверяем, админ ли мы, сразу при загрузке
    checkAdminRole();

    // 2. Инициализируем элементы
    const adminBtn = document.getElementById('admin-btn');
    const adminModal = document.getElementById('admin-modal');
    const closeAdminBtn = document.getElementById('close-admin-modal');

    // 3. Открытие окна
    if (adminBtn) {
        adminBtn.addEventListener('click', () => {
            if (adminModal) {
                adminModal.classList.add('open');
                document.body.style.overflow = 'hidden'; // Блокируем фон
                loadPendingReviews(); // Загружаем отзывы
            }
        });
    }

    // 4. Закрытие (крестик)
    if (closeAdminBtn) {
        closeAdminBtn.addEventListener('click', closeModal);
    }

    // 5. Закрытие (клик по фону)
    window.addEventListener('click', (e) => {
        if (adminModal && e.target === adminModal) {
            closeModal();
        }
    });

    function closeModal() {
        if (adminModal) adminModal.classList.remove('open');
        document.body.style.overflow = '';
    }
});

// --- ГЛОБАЛЬНЫЕ ФУНКЦИИ ---

const API_BASE = 'http://localhost:8888/wuide/api.php';

// Функция проверки роли
async function checkAdminRole() {
    const token = localStorage.getItem('authToken');
    const btn = document.getElementById('admin-btn');
    if (!token || !btn) return;

    try {
        const res = await fetch(`${API_BASE}/auth/profile`, {
            headers: { 'Authorization': 'Bearer ' + token }
        });
        
        if (res.ok) {
            const data = await res.json();
            // Если роль admin — показываем кнопку
            if (data.user && data.user.role === 'admin') {
                btn.classList.remove('hidden');
                btn.style.display = 'block'; // На всякий случай
            }
        }
    } catch (e) {
        console.error("Auth check failed", e);
    }
}

// Загрузка списка
async function loadPendingReviews() {
    const container = document.getElementById('admin-reviews-list');
    if (!container) return;
    
    container.innerHTML = '<p style="text-align:center; color:#888;">Loading pending reviews...</p>';
    const token = localStorage.getItem('authToken');

    try {
        const res = await fetch(`${API_BASE}/reviews/pending`, {
            headers: { 'Authorization': 'Bearer ' + token }
        });

        if (!res.ok) throw new Error("Failed to fetch");

        const reviews = await res.json();
        
        if (reviews.length === 0) {
            container.innerHTML = '<p style="text-align:center; padding:20px;">No reviews to moderate. Great job! 🎉</p>';
            return;
        }

        let html = '';
        reviews.forEach(r => {
            html += `
                <div class="admin-review-card" id="card-${r.id}">
                    <div class="review-meta">
                        <strong>${r.user_name}</strong> • ${r.date}
                    </div>
                    <div class="review-text">"${r.text}"</div>
                    <div class="admin-actions">
                        <button class="btn-adm btn-reject" onclick="handleModeration(${r.id}, 'reject')">Reject</button>
                        <button class="btn-adm btn-approve" onclick="handleModeration(${r.id}, 'approve')">Approve</button>
                    </div>
                </div>
            `;
        });
        container.innerHTML = html;

    } catch (e) {
        console.error(e);
        container.innerHTML = '<p style="text-align:center; color:red;">Error loading reviews.</p>';
    }
}

window.handleModeration = async function(id, action) {
    const token = localStorage.getItem('authToken');
    if (!confirm(`Are you sure you want to ${action}?`)) return;

    try {
        const res = await fetch(`${API_BASE}/reviews/${id}/moderate`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + token
            },
            body: JSON.stringify({ action: action })
        });

        if (res.ok) {
            const card = document.getElementById(`card-${id}`);
            if (card) {
                card.style.opacity = '0';
                setTimeout(() => {
                    card.remove();
                    const container = document.getElementById('admin-reviews-list');
                    if (container && container.children.length === 0) {
                        container.innerHTML = '<p style="text-align:center; padding:20px;">No reviews to moderate. Great job! 🎉</p>';
                    }
                }, 300);
            }
        } else {
            alert("Error processing request");
        }
    } catch (e) {
        console.error(e);
        alert("Server error");
    }
}
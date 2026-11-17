/**
 * Script Interaktif Sistem Koperasi Kampus STMIK IKMI
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. AJAX Tambah ke Keranjang
    const addToCartForms = document.querySelectorAll('.ajax-add-cart');
    addToCartForms.forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menambahkan...';

            const formData = new FormData(form);

            fetch(BASE_URL + '/shop/addToCart', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;

                if (data.success) {
                    // Update badge cart jika ada
                    let badge = document.querySelector('.navbar .badge');
                    if (!badge) {
                        const cartLink = document.querySelector('a[href*="/shop/cart"]');
                        if (cartLink) {
                            badge = document.createElement('span');
                            badge.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark border border-white';
                            cartLink.appendChild(badge);
                        }
                    }
                    if (badge) {
                        badge.innerText = data.total_cart_items;
                    }

                    // Tampilkan toast alert sederhana
                    showToast(data.message, 'success');
                } else {
                    showToast(data.message || 'Gagal menambahkan produk.', 'danger');
                }
            })
            .catch(err => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                console.error(err);
                // Fallback submit biasa jika AJAX gagal
                form.submit();
            });
        });
    });

    // 2. AI Explainer Modal Trigger
    const explainerButtons = document.querySelectorAll('.btn-ask-ai-explainer');
    const explainerModalElem = document.getElementById('aiExplainerModal');
    let explainerModal = null;
    if (explainerModalElem) {
        explainerModal = new bootstrap.Modal(explainerModalElem);
    }

    explainerButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const questionText = this.getAttribute('data-question') || '';
            const topic = this.getAttribute('data-topic') || '';
            const course = this.getAttribute('data-course') || '';

            if (explainerModal) {
                const modalBody = document.getElementById('aiExplainerModalBody');
                modalBody.innerHTML = `
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Memuat penjelasan AI...</span>
                        </div>
                        <h6 class="mt-3 fw-bold text-dark">AI Core Engine Sedang Bekerja...</h6>
                        <p class="text-muted small">Membedah konsep, aturan teori, dan rumus terkait soal ini.</p>
                    </div>
                `;
                explainerModal.show();

                const formData = new FormData();
                formData.append('question', questionText);
                formData.append('topic', topic);
                formData.append('course', course);

                fetch(BASE_URL + '/aiEdu/explain', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success && res.data) {
                        const d = res.data;
                        let stepsHtml = '';
                        if (d.step_by_step_analysis && Array.isArray(d.step_by_step_analysis)) {
                            stepsHtml = '<ul class="list-group list-group-flush mb-3">';
                            d.step_by_step_analysis.forEach(step => {
                                stepsHtml += `<li class="list-group-item bg-transparent px-0 py-2"><i class="bi bi-arrow-right-circle-fill text-primary me-2"></i>${step}</li>`;
                            });
                            stepsHtml += '</ul>';
                        }

                        modalBody.innerHTML = `
                            <div class="badge bg-primary-subtle text-primary mb-2">${d.course} &bull; ${d.topic}</div>
                            <h4 class="fw-bold text-dark mb-3">${d.concept_title}</h4>
                            
                            <div class="alert alert-light border border-primary-subtle p-3 rounded-3 mb-3">
                                <h6 class="fw-bold text-primary mb-1"><i class="bi bi-book-half me-1"></i> Teori & Konsep Fundamental:</h6>
                                <p class="mb-0 text-secondary">${d.fundamental_theory}</p>
                            </div>

                            ${d.formula_or_code ? `
                                <div class="mb-3">
                                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-calculator me-1"></i> Formulasi / Rumus Teori Terkait:</h6>
                                    <pre class="code-box"><code>${escapeHtml(d.formula_or_code)}</code></pre>
                                </div>
                            ` : ''}

                            <div class="mb-3">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-list-check me-1"></i> Tahapan Pemahaman Sistematis:</h6>
                                ${stepsHtml}
                            </div>

                            <div class="p-3 bg-light rounded-3 border d-flex align-items-center gap-2">
                                <i class="bi bi-info-circle-fill text-info fs-5"></i>
                                <small class="text-muted mb-0">${d.academic_note}</small>
                            </div>
                        `;
                    } else {
                        modalBody.innerHTML = `<div class="alert alert-danger">Gagal memuat penjelasan dari AI Core Engine.</div>`;
                    }
                })
                .catch(err => {
                    modalBody.innerHTML = `<div class="alert alert-danger">Terjadi kesalahan jaringan saat menghubungi AI Engine.</div>`;
                });
            }
        });
    });

    // ==============================================================
    // 3. FLOATING ACTION BUTTON (FAB) & CHAT POPUP WIDGET
    // ==============================================================
    const btnToggleFloatingChat = document.getElementById('btnToggleFloatingChat');
    const chatPopupWindow = document.getElementById('chatPopupWindow');
    const chatPopupCloseBtn = document.getElementById('chatPopupCloseBtn');
    const floatingChatIcon = document.getElementById('floatingChatIcon');
    const floatingChatPulse = document.getElementById('floatingChatPulse');
    const chatPopupForm = document.getElementById('chatPopupForm');
    const chatPopupInput = document.getElementById('chatPopupInput');
    const chatPopupStream = document.getElementById('chatPopupStream');
    const chatPopupSendBtn = document.getElementById('chatPopupSendBtn');

    // Sembunyikan FAB otomatis jika sedang di halaman Chatbot utama (/chatbot)
    if (document.getElementById('chatStream')) {
        const fabContainer = document.getElementById('floatingChatContainer');
        if (fabContainer) fabContainer.style.display = 'none';
    }

    if (btnToggleFloatingChat && chatPopupWindow) {
        btnToggleFloatingChat.addEventListener('click', function () {
            toggleFloatingChat();
        });

        if (chatPopupCloseBtn) {
            chatPopupCloseBtn.addEventListener('click', function () {
                closeFloatingChat();
            });
        }

        function toggleFloatingChat() {
            const isHidden = chatPopupWindow.classList.contains('d-none');
            if (isHidden) {
                openFloatingChat();
            } else {
                closeFloatingChat();
            }
        }

        function openFloatingChat() {
            chatPopupWindow.classList.remove('d-none');
            floatingChatIcon.className = 'bi bi-x-lg fs-4';
            if (floatingChatPulse) floatingChatPulse.style.display = 'none';
            if (chatPopupInput) {
                setTimeout(() => chatPopupInput.focus(), 150);
            }
            scrollFloatingChatBottom();
        }

        function closeFloatingChat() {
            chatPopupWindow.classList.add('d-none');
            floatingChatIcon.className = 'bi bi-chat-dots-fill fs-4';
            if (floatingChatPulse) floatingChatPulse.style.display = 'block';
        }

        // Quick Ask Chip
        window.quickAskFloating = function (query) {
            if (chatPopupInput) {
                chatPopupInput.value = query;
                if (chatPopupForm) {
                    chatPopupForm.dispatchEvent(new Event('submit'));
                }
            }
        };

        // Form Submit Kirim Pesan Asinkron (AJAX)
        if (chatPopupForm) {
            chatPopupForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const msg = chatPopupInput.value.trim();
                if (!msg) return;

                appendFloatingUserMsg(msg);
                chatPopupInput.value = '';
                chatPopupInput.disabled = true;
                if (chatPopupSendBtn) chatPopupSendBtn.disabled = true;

                const loadingId = appendFloatingLoading();

                const formData = new FormData();
                formData.append('message', msg);

                fetch(BASE_URL + '/chatbot/ask', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(res => {
                    removeFloatingLoading(loadingId);
                    chatPopupInput.disabled = false;
                    if (chatPopupSendBtn) chatPopupSendBtn.disabled = false;
                    chatPopupInput.focus();

                    if (res.success) {
                        appendFloatingBotMsg(res.reply);
                    } else {
                        appendFloatingBotMsg(res.message || 'Maaf, terjadi kendala saat membaca database.');
                    }
                })
                .catch(err => {
                    removeFloatingLoading(loadingId);
                    chatPopupInput.disabled = false;
                    if (chatPopupSendBtn) chatPopupSendBtn.disabled = false;
                    appendFloatingBotMsg('Gagal terhubung ke server lokal.');
                });
            });
        }

        function appendFloatingUserMsg(text) {
            const div = document.createElement('div');
            div.className = 'd-flex justify-content-end mb-2';
            div.innerHTML = `
                <div class="bg-ikmi-primary text-white p-2 px-3 rounded-3 small shadow-2xs" style="max-width: 80%; font-size: 0.82rem;">
                    ${escapeHtml(text)}
                </div>
            `;
            chatPopupStream.appendChild(div);
            scrollFloatingChatBottom();
        }

        function appendFloatingBotMsg(text) {
            const div = document.createElement('div');
            div.className = 'd-flex gap-2 mb-2';
            div.innerHTML = `
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 26px; height: 26px;">
                    <i class="bi bi-robot fs-xs"></i>
                </div>
                <div class="bg-white p-2 px-3 rounded-3 border shadow-2xs text-dark small lh-sm" style="max-width: 85%; font-size: 0.82rem;">
                    ${formatFloatingBotMsg(text)}
                </div>
            `;
            chatPopupStream.appendChild(div);
            scrollFloatingChatBottom();
        }

        function appendFloatingLoading() {
            const id = 'fl_loading_' + Date.now();
            const div = document.createElement('div');
            div.id = id;
            div.className = 'd-flex gap-2 mb-2';
            div.innerHTML = `
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 26px; height: 26px;">
                    <i class="bi bi-robot fs-xs"></i>
                </div>
                <div class="bg-white p-2 px-3 rounded-3 border d-flex align-items-center gap-2 small text-muted" style="font-size: 0.8rem;">
                    <div class="spinner-border spinner-border-sm text-primary" style="width: 0.85rem; height: 0.85rem;"></div>
                    <span>Mengecek data...</span>
                </div>
            `;
            chatPopupStream.appendChild(div);
            scrollFloatingChatBottom();
            return id;
        }

        function removeFloatingLoading(id) {
            const el = document.getElementById(id);
            if (el) el.remove();
        }

        function scrollFloatingChatBottom() {
            if (chatPopupStream) {
                chatPopupStream.scrollTop = chatPopupStream.scrollHeight;
            }
        }

        function formatFloatingBotMsg(md) {
            let formatted = md.replace(/\*\*(.*?)\*\*/gim, '<strong>$1</strong>');
            formatted = formatted.replace(/^\- (.*$)/gim, '<div class="py-0 my-0">• $1</div>');
            formatted = formatted.replace(/`([^`]+)`/gim, '<code class="bg-light px-1 text-danger">$1</code>');
            formatted = formatted.replace(/\n/gim, '<br>');
            return formatted;
        }
    }

    // Helper Toast
    function showToast(message, type = 'success') {
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            container.style.zIndex = '1100';
            document.body.appendChild(container);
        }

        const toastEl = document.createElement('div');
        toastEl.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0 shadow`;
        toastEl.setAttribute('role', 'alert');
        toastEl.setAttribute('aria-live', 'assertive');
        toastEl.setAttribute('aria-atomic', 'true');
        toastEl.innerHTML = `
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-circle'}-fill fs-5"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;
        container.appendChild(toastEl);
        const bsToast = new bootstrap.Toast(toastEl, { delay: 3500 });
        bsToast.show();
        toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
    }

    function escapeHtml(text) {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
});


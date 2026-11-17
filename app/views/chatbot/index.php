<div class="container py-4 py-lg-5">
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-11">
            
            <!-- Chat Card Container (Modern Minimalist Dashboard Style) -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white" style="height: 720px; display: flex; flex-direction: column;">
                
                <!-- Chat Header -->
                <div class="card-header bg-ikmi-primary text-white p-3 px-4 d-flex align-items-center justify-content-between border-0">
                    <div class="d-flex align-items-center gap-3">
                        <div class="position-relative">
                            <div class="bg-warning text-dark p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                <i class="bi bi-robot fs-4"></i>
                            </div>
                            <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle p-1" style="width: 12px; height: 12px;"></span>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h5 class="fw-bold mb-0 text-white">AI Database Chatbot</h5>
                                <span class="badge bg-white bg-opacity-20 text-warning px-2 py-0 fs-xs">RAG Engine</span>
                            </div>
                            <small class="text-white-50">Terhubung langsung ke Database Operasional <?= INSTITUTION_NAME ?></small>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success-subtle text-success border border-success-subtle d-none d-sm-inline-block">
                            <i class="bi bi-database-check me-1"></i> Live Database Connected
                        </span>
                        <a href="<?= BASE_URL ?>/shop" class="btn btn-outline-light btn-sm rounded-pill px-3">
                            <i class="bi bi-cart3 me-1"></i> Katalog
                        </a>
                    </div>
                </div>

                <!-- Chat Body (Messages Stream) -->
                <div class="card-body p-4 overflow-y-auto flex-grow-1 bg-light" id="chatStream" style="scroll-behavior: smooth;">
                    
                    <!-- Sambutan Awal dari Bot -->
                    <div class="d-flex gap-3 mb-4">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;">
                            <i class="bi bi-robot"></i>
                        </div>
                        <div class="bg-white p-3 rounded-4 shadow-2xs border" style="max-width: 85%;">
                            <h6 class="fw-bold text-dark mb-1">AI Assistant Koperasi IKMI</h6>
                            <p class="text-secondary mb-2 small lh-base">
                                Halo! Saya adalah asisten cerdas yang memiliki akses langsung untuk membaca data tabel <code>products</code>, <code>orders</code>, dan <code>quizzes</code> di database kampus secara riil.
                            </p>
                            <p class="text-secondary mb-3 small lh-base">
                                Anda dapat menanyakan ketersediaan stok perlengkapan kuliah, rekapitulasi transaksi pesanan, hingga ringkasan materi kuis akademik.
                            </p>
                            
                            <!-- Prompt Suggestion Chips -->
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill py-1 px-3 fs-xs prompt-chip" onclick="fillPrompt('Stok produk apa aja')">
                                    <i class="bi bi-box-seam me-1"></i> Stok Produk Apa Aja
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill py-1 px-3 fs-xs prompt-chip" onclick="fillPrompt('Cek stok jas almamater STMIK IKMI')">
                                    <i class="bi bi-search me-1"></i> Cek Stok Jas Almamater
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill py-1 px-3 fs-xs prompt-chip" onclick="fillPrompt('Rekap transaksi koperasi dan total omzet')">
                                    <i class="bi bi-receipt me-1"></i> Rekap Transaksi
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill py-1 px-3 fs-xs prompt-chip" onclick="fillPrompt('Tabel apa saja yang ada di database')">
                                    <i class="bi bi-database me-1"></i> Cek Tabel Database
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill py-1 px-3 fs-xs prompt-chip" onclick="fillPrompt('Tampilkan daftar buku kuliah yang tersedia')">
                                    <i class="bi bi-book me-1"></i> Daftar Buku Kuliah
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Chat Footer (Input Area) -->
                <div class="card-footer bg-white p-3 border-top">
                    <form id="chatForm" class="d-flex gap-2">
                        <div class="input-group">
                            <input type="text" id="chatInput" class="form-control rounded-pill-start border-end-0 py-2 px-3" 
                                   placeholder="Ketik pertanyaan seputar stok produk, rekap pesanan, atau materi kuis..." required autocomplete="off">
                            <button type="submit" id="chatSendBtn" class="btn btn-warning text-dark fw-bold rounded-pill-end px-4">
                                <i class="bi bi-send-fill me-1"></i> Kirim
                            </button>
                        </div>
                    </form>
                    <div class="d-flex justify-content-between align-items-center mt-2 px-2">
                        <small class="text-muted fs-xs">
                            <i class="bi bi-shield-lock-fill text-success me-1"></i> Dilengkapi Text-to-SQL & RAG Grounding Keamanan Data
                        </small>
                        <small class="text-muted fs-xs">Tekan <code>Enter</code> untuk mengirim</small>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const chatForm = document.getElementById('chatForm');
    const chatInput = document.getElementById('chatInput');
    const chatStream = document.getElementById('chatStream');
    const chatSendBtn = document.getElementById('chatSendBtn');

    window.fillPrompt = function(text) {
        chatInput.value = text;
        chatInput.focus();
    };

    chatForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const msg = chatInput.value.trim();
        if (!msg) return;

        // 1. Tampilkan pesan user
        appendUserMessage(msg);
        chatInput.value = '';
        chatInput.disabled = true;
        chatSendBtn.disabled = true;

        // 2. Tampilkan indikator loading AI
        const loadingId = appendLoadingIndicator();

        // 3. Kirim via AJAX ke ChatbotController::ask
        const formData = new FormData();
        formData.append('message', msg);

        fetch(BASE_URL + '/chatbot/ask', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(res => {
            removeLoadingIndicator(loadingId);
            chatInput.disabled = false;
            chatSendBtn.disabled = false;
            chatInput.focus();

            if (res.success) {
                appendBotMessage(res.reply, res.source, res.context_tables);
            } else {
                appendBotMessage('Maaf, ' + (res.message || 'terjadi kendala saat membaca database.'), 'System');
            }
        })
        .catch(err => {
            removeLoadingIndicator(loadingId);
            chatInput.disabled = false;
            chatSendBtn.disabled = false;
            appendBotMessage('Terjadi kesalahan jaringan atau server lokal tidak merespons.', 'Error');
        });
    });

    function appendUserMessage(text) {
        const div = document.createElement('div');
        div.className = 'd-flex justify-content-end mb-4';
        div.innerHTML = `
            <div class="bg-ikmi-primary text-white p-3 rounded-4 shadow-sm" style="max-width: 80%;">
                <p class="mb-0 small lh-base">${escapeHtml(text)}</p>
                <div class="text-end mt-1">
                    <small class="text-white-50 fs-xs">${formatTime()}</small>
                </div>
            </div>
        `;
        chatStream.appendChild(div);
        scrollToBottom();
    }

    function appendBotMessage(markdownText, source, tables) {
        const div = document.createElement('div');
        div.className = 'd-flex gap-3 mb-4';
        
        let tableBadges = '';
        if (tables && Array.isArray(tables) && tables.length > 0) {
            tableBadges = tables.map(t => `<span class="badge bg-secondary-subtle text-secondary border me-1">${t}</span>`).join('');
        }

        div.innerHTML = `
            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;">
                <i class="bi bi-robot"></i>
            </div>
            <div class="bg-white p-3 rounded-4 shadow-2xs border" style="max-width: 85%;">
                <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom">
                    <span class="badge bg-primary text-white fs-xs">${escapeHtml(source || 'AI RAG Engine')}</span>
                    <div>${tableBadges}</div>
                </div>
                <div class="chat-content text-dark small lh-base">${renderSimpleMarkdown(markdownText)}</div>
                <div class="text-end mt-2">
                    <small class="text-muted fs-xs">${formatTime()}</small>
                </div>
            </div>
        `;
        chatStream.appendChild(div);
        scrollToBottom();
    }

    function appendLoadingIndicator() {
        const id = 'loading_' + Date.now();
        const div = document.createElement('div');
        div.id = id;
        div.className = 'd-flex gap-3 mb-4';
        div.innerHTML = `
            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;">
                <i class="bi bi-robot"></i>
            </div>
            <div class="bg-white p-3 rounded-4 border d-flex align-items-center gap-2">
                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                <small class="text-muted">Membaca data database & menganalisis...</small>
            </div>
        `;
        chatStream.appendChild(div);
        scrollToBottom();
        return id;
    }

    function removeLoadingIndicator(id) {
        const el = document.getElementById(id);
        if (el) el.remove();
    }

    function scrollToBottom() {
        chatStream.scrollTop = chatStream.scrollHeight;
    }

    function formatTime() {
        const d = new Date();
        return d.getHours().toString().padStart(2, '0') + ':' + d.getMinutes().toString().padStart(2, '0');
    }

    function escapeHtml(text) {
        var map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    // Markdown Parser Sederhana untuk Chat
    function renderSimpleMarkdown(md) {
        // Headers
        let html = md.replace(/^### (.*$)/gim, '<h6 class="fw-bold text-dark mt-2 mb-2">$1</h6>')
                     .replace(/^#### (.*$)/gim, '<h6 class="fw-semibold text-secondary mt-2 mb-1">$1</h6>');
        
        // Bold & Italic
        html = html.replace(/\*\*(.*?)\*\*/gim, '<strong>$1</strong>')
                   .replace(/\*(.*?)\*/gim, '<em>$1</em>')
                   .replace(/`([^`]+)`/gim, '<code class="bg-light px-1 py-0 rounded text-danger">$1</code>');

        // Lists
        html = html.replace(/^\- (.*$)/gim, '<li class="mb-1">$1</li>');

        // Markdown Table Parser
        if (html.includes('|')) {
            const lines = html.split('\n');
            let inTable = false;
            let tableHtml = '<div class="table-responsive my-2"><table class="table table-sm table-bordered align-middle">';
            let newLines = [];

            for (let line of lines) {
                if (line.trim().startsWith('|') && line.trim().endsWith('|')) {
                    if (!inTable) {
                        inTable = true;
                        tableHtml = '<div class="table-responsive my-2"><table class="table table-sm table-bordered align-middle bg-white">';
                    }
                    if (line.includes('---')) continue; // skip delimiter

                    const cells = line.split('|').filter((_, idx, arr) => idx > 0 && idx < arr.length - 1);
                    const isHeader = !tableHtml.includes('<tbody>');

                    if (isHeader) {
                        tableHtml += '<thead class="table-light"><tr>';
                        cells.forEach(c => tableHtml += `<th>${c.trim()}</th>`);
                        tableHtml += '</tr></thead><tbody>';
                    } else {
                        tableHtml += '<tr>';
                        cells.forEach(c => tableHtml += `<td>${c.trim()}</td>`);
                        tableHtml += '</tr>';
                    }
                } else {
                    if (inTable) {
                        inTable = false;
                        tableHtml += '</tbody></table></div>';
                        newLines.push(tableHtml);
                    }
                    newLines.push(line);
                }
            }
            if (inTable) {
                tableHtml += '</tbody></table></div>';
                newLines.push(tableHtml);
            }
            html = newLines.join('\n');
        }

        // Newlines to <br> (hanya di luar blok tabel)
        html = html.replace(/\n/gim, '<br>');
        html = html.replace(/<br>\s*(<div class="table-responsive)/gi, '$1')
                   .replace(/(<\/div>)\s*<br>/gi, '$1');
        return html;
    }
});
</script>


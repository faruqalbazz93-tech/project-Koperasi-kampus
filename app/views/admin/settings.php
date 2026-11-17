<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="badge bg-dark text-warning fw-bold px-3 py-1 rounded-pill mb-2"><i class="bi bi-gear-fill me-1"></i> Admin Settings</span>
            <h2 class="fw-bold mb-0">Pengaturan API Key & Gateway</h2>
            <small class="text-muted"><?= INSTITUTION_NAME ?> &bull; Konfigurasi Dinamis Tersimpan di Database</small>
        </div>
        <a href="<?= BASE_URL ?>/admin" class="btn btn-outline-secondary rounded-pill btn-sm px-3">
            <i class="bi bi-arrow-left me-1"></i> Dashboard Admin
        </a>
    </div>

    <div class="row g-4">
        <!-- Form Konfigurasi Utama -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-sliders text-primary me-2"></i>Konfigurasi API Gateway</h5>
                    <small class="text-muted">Perubahan nilai akan langsung tersimpan dan aktif di seluruh sistem backend.</small>
                </div>
                <div class="card-body p-4 p-lg-5">
                    <form action="<?= BASE_URL ?>/admin/saveSettings" method="POST">
                        
                        <!-- 1. Fonnte WhatsApp Gateway -->
                        <div class="p-3 bg-light rounded-3 border mb-4">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="badge bg-success p-2 rounded-circle"><i class="bi bi-whatsapp fs-5 text-white"></i></span>
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark">WhatsApp Gateway (Fonnte API)</h6>
                                    <small class="text-muted">Digunakan untuk kirim OTP Registrasi & Notifikasi Pesanan Otomatis</small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Fonnte API Token</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-key-fill text-muted"></i></span>
                                    <input type="password" name="fonnte_token" id="fonnte_token" class="form-control font-monospace" 
                                           placeholder="Masukkan token dari dashboard Fonnte" 
                                           value="<?= htmlspecialchars($settings['fonnte_token'] ?? '') ?>">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('fonnte_token')">
                                        <i class="bi bi-eye" id="fonnte_token_icon"></i>
                                    </button>
                                </div>
                                <div class="form-text small">
                                    Dapatkan token Anda gratis di <a href="https://fonnte.com" target="_blank" class="text-decoration-none">fonnte.com</a>. Kosongkan untuk menggunakan mode simulasi offline.
                                </div>
                            </div>

                            <div>
                                <label class="form-label small fw-semibold text-secondary">No. WhatsApp Toko / Admin Koperasi</label>
                                <input type="text" name="store_phone" class="form-control" 
                                       placeholder="0812xxxxxxxx" 
                                       value="<?= htmlspecialchars($settings['store_phone'] ?? '081234567890') ?>">
                            </div>
                        </div>

                        <!-- 2. AI Engine Provider (Gemini / OpenAI) -->
                        <div class="p-3 bg-light rounded-3 border mb-4">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="badge bg-primary p-2 rounded-circle"><i class="bi bi-cpu-fill fs-5 text-white"></i></span>
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark">AI Chatbot & Quiz Provider</h6>
                                    <small class="text-muted">Untuk RAG Database Chatbot dan AI Academic Quiz Generator</small>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">Penyedia AI (Provider)</label>
                                    <select name="ai_provider" class="form-select">
                                        <option value="gemini" <?= (($settings['ai_provider'] ?? '') === 'gemini') ? 'selected' : '' ?>>Google Gemini (Recommended)</option>
                                        <option value="openai" <?= (($settings['ai_provider'] ?? '') === 'openai') ? 'selected' : '' ?>>OpenAI (GPT-4o / GPT-3.5)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">Status Integrasi</label>
                                    <input type="text" class="form-control bg-white fw-bold text-success" 
                                           value="<?= !empty($settings['ai_api_key']) ? 'Terkoneksi API Key' : 'Database RAG Mode (Aktif)' ?>" readonly>
                                </div>
                            </div>

                            <div class="mb-2">
                                <label class="form-label small fw-semibold text-secondary">AI API Key</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-shield-lock-fill text-muted"></i></span>
                                    <input type="password" name="ai_api_key" id="ai_api_key" class="form-control font-monospace" 
                                           placeholder="AIzaSy... atau sk-..." 
                                           value="<?= htmlspecialchars($settings['ai_api_key'] ?? '') ?>">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('ai_api_key')">
                                        <i class="bi bi-eye" id="ai_api_key_icon"></i>
                                    </button>
                                </div>
                                <div class="form-text small">
                                    Kunci API Google Gemini (dari <a href="https://aistudio.google.com" target="_blank">Google AI Studio</a>) atau OpenAI API Key.
                                </div>
                            </div>

                            <!-- Penampung Alert Hasil Tes Koneksi AI -->
                            <div id="aiConnectionAlert" class="mt-3 d-none"></div>
                        </div>

                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <button type="button" id="btnTestAi" class="btn btn-outline-primary rounded-pill px-4 py-2 fw-semibold shadow-2xs">
                                <i class="bi bi-lightning-charge-fill me-1"></i> <span id="btnTestAiText">Tes Koneksi AI</span>
                            </button>
                            <button type="submit" class="btn btn-warning text-dark fw-bold rounded-pill px-4 py-2 shadow-sm">
                                <i class="bi bi-save me-1"></i> Simpan Konfigurasi Dinamis
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Panel Uji Coba Langsung Fonnte -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
                <div class="card-header bg-success text-white py-3 px-4">
                    <h6 class="fw-bold mb-0"><i class="bi bi-send-check me-2"></i>Uji Coba Fonnte Gateway</h6>
                </div>
                <div class="card-body p-4">
                    <p class="small text-muted mb-3">
                        Kirim pesan uji coba langsung ke nomor WhatsApp Anda untuk memastikan token Fonnte berfungsi normal:
                    </p>
                    <form action="<?= BASE_URL ?>/admin/testFonnte" method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Nomor WhatsApp Tujuan</label>
                            <input type="text" name="target_phone" class="form-control" placeholder="08xxxxxxxxxx" required
                                   value="<?= htmlspecialchars($_SESSION['user']['phone'] ?? '') ?>">
                        </div>
                        <button type="submit" class="btn btn-outline-success w-100 rounded-pill fw-semibold">
                            <i class="bi bi-whatsapp me-1"></i> Kirim Pesan Tes
                        </button>
                    </form>
                </div>
            </div>

            <!-- Petunjuk & Status -->
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-light border">
                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-info-circle text-primary me-2"></i>Informasi Keamanan</h6>
                <p class="small text-muted mb-0">
                    Semua API Key disimpan secara terenkripsi dalam tabel <code>settings</code> basis data dan hanya dapat diakses oleh pengguna dengan hak akses <strong>Administrator</strong>.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
function togglePasswordVisibility(id) {
    const input = document.getElementById(id);
    const icon = document.getElementById(id + '_icon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const btnTestAi = document.getElementById('btnTestAi');
    const btnTestAiText = document.getElementById('btnTestAiText');
    const alertBox = document.getElementById('aiConnectionAlert');
    const providerSelect = document.querySelector('select[name="ai_provider"]');
    const apiKeyInput = document.getElementById('ai_api_key');

    function escapeHtml(str) {
        if (!str) return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return str.replace(/[&<>"']/g, m => map[m]);
    }

    if (btnTestAi) {
        btnTestAi.addEventListener('click', function () {
            const provider = providerSelect ? providerSelect.value : 'gemini';
            const apiKey = apiKeyInput ? apiKeyInput.value.trim() : '';
            const providerName = provider === 'openai' ? 'OpenAI' : 'Google Gemini';

            // Validasi Input API Key
            if (!apiKey) {
                alertBox.className = 'alert alert-warning border-0 rounded-3 d-flex align-items-center mb-0 mt-3 shadow-sm py-2 px-3';
                alertBox.innerHTML = `
                    <i class="bi bi-exclamation-triangle-fill text-warning fs-5 me-2"></i>
                    <div>
                        <strong class="d-block small text-warning">API Key Masih Kosong</strong>
                        <small class="text-dark">Silakan masukkan atau tempelkan API Key ${providerName} terlebih dahulu.</small>
                    </div>
                `;
                alertBox.classList.remove('d-none');
                apiKeyInput.focus();
                return;
            }

            // Tampilkan animasi Loading Spinner saat proses pengujian berlangsung
            btnTestAi.disabled = true;
            btnTestAiText.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menguji Koneksi...';

            alertBox.className = 'alert alert-info border-0 rounded-3 d-flex align-items-center mb-0 mt-3 shadow-sm py-2 px-3';
            alertBox.innerHTML = `
                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                <div>
                    <strong class="d-block small text-primary">Sedang Menghubungi Server ${providerName}...</strong>
                    <small class="text-secondary">Mengirim permintaan uji coba ke endpoint resmi untuk memverifikasi kevalidan API Key.</small>
                </div>
            `;
            alertBox.classList.remove('d-none');

            // Kirim request AJAX asinkron ke backend AdminController::testAiConnection
            const formData = new FormData();
            formData.append('ai_provider', provider);
            formData.append('ai_api_key', apiKey);

            const targetUrl = (typeof BASE_URL !== 'undefined' ? BASE_URL : '') + '/admin/testAiConnection';

            fetch(targetUrl, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json().then(data => ({ status: res.status, ok: res.ok, body: data })))
            .then(res => {
                btnTestAi.disabled = false;
                btnTestAiText.textContent = 'Tes Koneksi AI';

                if (res.ok && res.body.success) {
                    // Alert Sukses
                    alertBox.className = 'alert alert-success border-0 rounded-3 d-flex align-items-center mb-0 mt-3 shadow-sm py-2 px-3';
                    alertBox.innerHTML = `
                        <i class="bi bi-check-circle-fill text-success fs-5 me-2"></i>
                        <div>
                            <strong class="d-block small text-success">Koneksi Berhasil Terhubung!</strong>
                            <small class="text-dark">${escapeHtml(res.body.message)}</small>
                        </div>
                    `;
                } else {
                    // Alert Gagal dengan Pesan Error Jelas
                    alertBox.className = 'alert alert-danger border-0 rounded-3 d-flex align-items-center mb-0 mt-3 shadow-sm py-2 px-3';
                    alertBox.innerHTML = `
                        <i class="bi bi-x-circle-fill text-danger fs-5 me-2"></i>
                        <div>
                            <strong class="d-block small text-danger">Uji Koneksi Gagal (${escapeHtml(res.body.provider || providerName)})</strong>
                            <small class="text-dark">${escapeHtml(res.body.message || 'Terjadi kesalahan saat memverifikasi API Key.')}</small>
                        </div>
                    `;
                }
            })
            .catch(err => {
                btnTestAi.disabled = false;
                btnTestAiText.textContent = 'Tes Koneksi AI';

                // Alert Kesalahan Jaringan
                alertBox.className = 'alert alert-danger border-0 rounded-3 d-flex align-items-center mb-0 mt-3 shadow-sm py-2 px-3';
                alertBox.innerHTML = `
                    <i class="bi bi-wifi-off text-danger fs-5 me-2"></i>
                    <div>
                        <strong class="d-block small text-danger">Kendala Jaringan / Server</strong>
                        <small class="text-dark">Gagal terhubung ke endpoint backend lokal. Periksa status server web Anda.</small>
                    </div>
                `;
            });
        });
    }
});
</script>


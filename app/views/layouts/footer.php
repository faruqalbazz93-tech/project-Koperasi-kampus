        </main>

        <!-- Clean Minimalist Dashboard Footer -->
        <footer class="dashboard-footer bg-white border-top py-3 px-4 mt-auto">
            <div class="container-fluid px-0">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center text-md-start">
                        <small class="text-muted">
                            &copy; <?= date('Y') ?> <strong><?= INSTITUTION_NAME ?></strong> &bull; All Rights Reserved.
                        </small>
                    </div>
                    <div class="col-md-6 text-center text-md-end mt-2 mt-md-0">
                        <small class="text-muted">
                            <i class="bi bi-shield-check text-success me-1"></i> Fonnte Gateway & AI RAG Grounding Active
                        </small>
                    </div>
                </div>
            </div>
        </footer>

    </div><!-- /.main-content-wrapper -->

</div><!-- /.dashboard-layout-container -->

<!-- Global Modal: AI Explainer -->
<div class="modal fade" id="aiExplainerModal" tabindex="-1" aria-labelledby="aiExplainerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-ikmi-primary text-white p-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-warning text-dark"><i class="bi bi-cpu-fill"></i> AI Explainer</span>
                    <h5 class="modal-title fw-bold" id="aiExplainerModalLabel">Bedah Konsep & Formula Akademik</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="aiExplainerModalBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Memuat penjelasan AI...</span>
                    </div>
                    <p class="mt-3 text-muted">Sedang menganalisis struktur materi...</p>
                </div>
            </div>
            <div class="modal-footer bg-light p-3">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Global Floating Action Button (FAB) & AI Chatbot Widget -->
<div class="floating-chat-container" id="floatingChatContainer">
    
    <!-- Jendela Chat Pop-up (Interactive Window) -->
    <div class="chat-popup-window shadow-lg border-0 rounded-4 overflow-hidden d-none" id="chatPopupWindow">
        <!-- Header Pop-up -->
        <div class="chat-popup-header bg-ikmi-primary text-white p-3 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <div class="position-relative">
                    <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="bi bi-robot fs-6"></i>
                    </div>
                    <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle p-1" style="width: 8px; height: 8px;"></span>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-white fs-6">AI Chatbot Koperasi</h6>
                    <small class="text-white-50" style="font-size: 0.7rem;">Live RAG Database STMIK IKMI</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="<?= BASE_URL ?>/chatbot" class="text-white-50 text-decoration-none hover-white" title="Buka Layar Penuh">
                    <i class="bi bi-arrows-fullscreen fs-xs"></i>
                </a>
                <button type="button" class="btn-close btn-close-white" id="chatPopupCloseBtn" aria-label="Tutup"></button>
            </div>
        </div>

        <!-- Aliran Pesan (Message Stream) -->
        <div class="chat-popup-body p-3 bg-light overflow-y-auto flex-grow-1" id="chatPopupStream" style="height: 350px;">
            <div class="d-flex gap-2 mb-3">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 28px; height: 28px;">
                    <i class="bi bi-robot fs-xs"></i>
                </div>
                <div class="bg-white p-2 px-3 rounded-3 border shadow-2xs small lh-sm" style="max-width: 85%;">
                    <p class="mb-1 text-dark fw-medium">Halo! Ada yang bisa saya bantu terkait cek stok produk, harga barang, atau transaksi koperasi?</p>
                    <div class="d-flex flex-wrap gap-1 mt-2">
                        <button type="button" class="btn btn-outline-primary rounded-pill py-0 px-2" style="font-size: 0.68rem;" onclick="quickAskFloating('stok produk apa aja')">Stok Produk</button>
                        <button type="button" class="btn btn-outline-primary rounded-pill py-0 px-2" style="font-size: 0.68rem;" onclick="quickAskFloating('cek stok jas almamater')">Jas Almamater</button>
                        <button type="button" class="btn btn-outline-primary rounded-pill py-0 px-2" style="font-size: 0.68rem;" onclick="quickAskFloating('rekap transaksi')">Rekap Order</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Input Pesan -->
        <div class="chat-popup-footer bg-white p-2 border-top">
            <form id="chatPopupForm" class="d-flex gap-2 align-items-center">
                <input type="text" id="chatPopupInput" class="form-control form-control-sm rounded-pill py-1 px-3" 
                       placeholder="Tanya stok, harga, atau transaksi..." required autocomplete="off">
                <button type="submit" id="chatPopupSendBtn" class="btn btn-warning text-dark btn-sm rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px; flex-shrink: 0;">
                    <i class="bi bi-send-fill fs-xs"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Tombol Melayang (Floating Action Button) -->
    <button type="button" class="btn-floating-chat shadow-lg" id="btnToggleFloatingChat" aria-label="Buka AI Chatbot">
        <i class="bi bi-chat-dots-fill fs-4" id="floatingChatIcon"></i>
        <span class="floating-badge-pulse" id="floatingChatPulse"></span>
    </button>
</div>

<!-- Bootstrap 5.3 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Base URL JS Variable -->
<script>
    const BASE_URL = '<?= BASE_URL ?>';
</script>
<!-- Custom Application JS -->
<script src="<?= BASE_URL ?>/assets/js/app.js"></script>
</body>
</html>

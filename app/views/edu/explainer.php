<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="text-center mb-5">
                <span class="badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill mb-2"><i class="bi bi-mortarboard-fill"></i> AI Academic Engine</span>
                <h2 class="fw-bold mb-2">AI Academic Explainer & Concept Explorer</h2>
                <p class="text-muted">Eksplorasi konsep informatika, bedah rumus matematika komputasi, dan struktur algoritma kurikulum <?= INSTITUTION_NAME ?>.</p>
            </div>

            <!-- Search / Prompt Input Card -->
            <div class="card border-0 shadow-sm rounded-4 p-4 p-lg-5 bg-white mb-4">
                <form id="standaloneExplainerForm">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Mata Kuliah / Domain</label>
                            <input type="text" id="exp_course" class="form-control" value="Algoritma & Struktur Data" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Topik Utama</label>
                            <input type="text" id="exp_topic" class="form-control" value="Balanced Binary Search Tree (AVL)" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-semibold text-secondary">Pertanyaan, Rumus, atau Konsep yang Ingin Dibedah</label>
                        <textarea id="exp_question" class="form-control" rows="3" placeholder="Contoh: Mengapa operasi pencarian pada AVL Tree bernilai O(log n) dan bagaimana mekanisme rotasinya?" required>Berapakah kompleksitas waktu rata-rata (average time complexity) pada Balanced Binary Search Tree (AVL Tree) untuk operasi pencarian elemen dan rotasi?</textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <div class="small text-muted">
                            <i class="bi bi-lightning-fill text-warning me-1"></i> Didukung oleh AI Core Engine STMIK IKMI
                        </div>
                        <button type="submit" id="exp_submit_btn" class="btn btn-warning text-dark fw-bold rounded-pill px-4 py-2 shadow-sm">
                            <i class="bi bi-cpu-fill me-1"></i> Bedah Konsep Sekarang
                        </button>
                    </div>
                </form>
            </div>

            <!-- Area Hasil Penjelasan AI -->
            <div id="explainerResultArea" class="card border-0 shadow-sm rounded-4 p-4 p-lg-5 bg-white" style="display: none;">
                <!-- Dinamis diisi lewat AJAX -->
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('standaloneExplainerForm');
    const resultArea = document.getElementById('explainerResultArea');
    const submitBtn = document.getElementById('exp_submit_btn');

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const course = document.getElementById('exp_course').value;
        const topic = document.getElementById('exp_topic').value;
        const question = document.getElementById('exp_question').value;

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> AI Menganalisis...';

        resultArea.style.display = 'block';
        resultArea.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <h5 class="fw-bold text-dark mt-3">AI Core Engine Sedang Menganalisis...</h5>
                <p class="text-muted small">Merumuskan teori fundamental, formula komputasi, dan analogi pemahaman sistematis.</p>
            </div>
        `;

        const formData = new FormData();
        formData.append('course', course);
        formData.append('topic', topic);
        formData.append('question', question);

        fetch(BASE_URL + '/aiEdu/explain', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(res => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-cpu-fill me-1"></i> Bedah Konsep Sekarang';

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

                resultArea.innerHTML = `
                    <div class="badge bg-primary-subtle text-primary mb-2">${d.course} &bull; ${d.topic}</div>
                    <h3 class="fw-bold text-dark mb-4">${d.concept_title}</h3>
                    
                    <div class="alert alert-light border border-primary-subtle p-3 rounded-3 mb-4">
                        <h6 class="fw-bold text-primary mb-2"><i class="bi bi-journal-bookmark-fill me-1"></i> Teori & Konsep Fundamental:</h6>
                        <p class="mb-0 text-secondary lh-base">${d.fundamental_theory}</p>
                    </div>

                    ${d.formula_or_code ? `
                        <div class="mb-4">
                            <h6 class="fw-bold text-dark mb-2"><i class="bi bi-calculator me-1 text-primary"></i> Formulasi / Rumus Teori Terkait:</h6>
                            <pre class="code-box"><code>${escapeHtml(d.formula_or_code)}</code></pre>
                        </div>
                    ` : ''}

                    <div class="mb-4">
                        <h6 class="fw-bold text-dark mb-2"><i class="bi bi-list-check me-1 text-success"></i> Tahapan Pemahaman Sistematis:</h6>
                        ${stepsHtml}
                    </div>

                    <div class="p-3 bg-light rounded-3 border d-flex align-items-center gap-2">
                        <i class="bi bi-mortarboard-fill text-primary fs-5"></i>
                        <small class="text-muted mb-0">${d.academic_note}</small>
                    </div>
                `;
            } else {
                resultArea.innerHTML = `<div class="alert alert-danger">Gagal mendapatkan analisa dari AI.</div>`;
            }
        })
        .catch(err => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-cpu-fill me-1"></i> Bedah Konsep Sekarang';
            resultArea.innerHTML = `<div class="alert alert-danger">Terjadi kesalahan koneksi ke server.</div>`;
        });
    });

    function escapeHtml(text) {
        var map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
});
</script>


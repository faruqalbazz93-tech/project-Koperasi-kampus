<div class="container py-5">
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <span class="badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill mb-2"><i class="bi bi-cpu-fill"></i> Core AI Feature</span>
            <h2 class="fw-bold mb-1">AI Quiz Generator</h2>
            <p class="text-muted mb-0">Hasilkan bank soal latihan atau simulasi ujian akademik otomatis berbasis AI untuk mahasiswa & dosen <?= INSTITUTION_NAME ?>.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="<?= BASE_URL ?>/aiEdu/explainer" class="btn btn-outline-primary rounded-pill btn-sm px-3">
                <i class="bi bi-mortarboard me-1"></i> Buka AI Explainer Mandiri
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Generator Form Card -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
                <div class="card-header bg-ikmi-primary text-white p-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-sliders me-2 text-warning"></i>Konfigurasi Pembuatan Soal</h5>
                    <small class="text-white-50">Tentukan mata kuliah, topik, dan parameter kuis yang diinginkan</small>
                </div>
                <div class="card-body p-4 p-lg-5">
                    <form action="<?= BASE_URL ?>/aiEdu/generate" method="POST">
                        
                        <!-- Pilihan Mata Kuliah -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-secondary">
                                <i class="bi bi-book-half me-1 text-primary"></i> Mata Kuliah Kampus <span class="text-danger">*</span>
                            </label>
                            <select name="course" class="form-select" required>
                                <option value="Algoritma & Struktur Data">Algoritma & Struktur Data</option>
                                <option value="Basis Data Relasional & NoSQL">Basis Data Relasional & NoSQL</option>
                                <option value="Rekayasa Perangkat Lunak & MVC">Rekayasa Perangkat Lunak & MVC</option>
                                <option value="Jaringan Komputer & Keamanan">Jaringan Komputer & Keamanan</option>
                                <option value="Kecerdasan Buatan & Sistem Pakar">Kecerdasan Buatan & Sistem Pakar</option>
                            </select>
                        </div>

                        <!-- Topik Spesifik -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-secondary">
                                <i class="bi bi-bullseye me-1 text-danger"></i> Topik / Materi Spesifik <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="topic" class="form-control" placeholder="Contoh: Binary Search Tree, Normalisasi 3NF, atau MVC Lifecycle" required value="Binary Search Tree">
                            <div class="form-text small">Masukkan materi atau bab tertentu yang ingin diuji.</div>
                        </div>

                        <div class="row g-3 mb-4">
                            <!-- Format Soal -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary">
                                    <i class="bi bi-card-checklist me-1 text-success"></i> Tipe Soal <span class="text-danger">*</span>
                                </label>
                                <select name="type" class="form-select" required>
                                    <option value="Pilihan Ganda" selected>Pilihan Ganda (A, B, C, D)</option>
                                    <option value="Essay">Essay & Analisis Konsep</option>
                                </select>
                            </div>

                            <!-- Jumlah Soal -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary">
                                    <i class="bi bi-123 me-1 text-warning"></i> Jumlah Soal
                                </label>
                                <select name="count" class="form-select" required>
                                    <option value="3">3 Butir Soal (Cepat)</option>
                                    <option value="4" selected>4 Butir Soal (Standar)</option>
                                    <option value="5">5 Butir Soal (Komprehensif)</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning text-dark fw-bold py-3 rounded-pill shadow-sm">
                                <i class="bi bi-lightning-charge-fill me-1"></i> Generate Kuis dengan AI Core Engine
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Riwayat Kuis & Panduan -->
        <div class="col-lg-5">
            <!-- Card Riwayat -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Kuis Anda</h6>
                    <span class="badge bg-light text-secondary border"><?= count($recentQuizzes) ?> Kuis</span>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($recentQuizzes)): ?>
                        <div class="text-center py-4 px-3 text-muted small">
                            Belum ada riwayat kuis. Silakan coba generate kuis pertama Anda!
                        </div>
                    <?php else: ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach (array_slice($recentQuizzes, 0, 5) as $rq): ?>
                                <li class="list-group-item p-3 d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($rq['topic']) ?></h6>
                                        <small class="text-muted d-block"><?= htmlspecialchars($rq['course_name']) ?> &bull; <?= htmlspecialchars($rq['question_type']) ?></small>
                                        <small class="text-secondary"><?= date('d M Y, H:i', strtotime($rq['created_at'])) ?></small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-primary text-white d-block mb-1">Skor: <?= number_format($rq['score'], 0) ?></span>
                                        <a href="<?= BASE_URL ?>/aiEdu/play/<?= $rq['id'] ?>" class="btn btn-sm btn-outline-secondary rounded-pill py-0 px-2">Buka</a>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Card Petunjuk AI Explainer -->
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-ikmi-primary text-white">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-stars text-warning fs-4"></i>
                    <h6 class="fw-bold mb-0 text-white">Fitur AI Explainer Terintegrasi</h6>
                </div>
                <p class="small text-white-50 mb-0">
                    Setiap soal yang digenerate dilengkapi tombol <strong>"Bedah Konsep (AI Explainer)"</strong> untuk membantu mahasiswa memahami rumus matematika komputasi, struktur logika, maupun algoritma penyelesaian secara mendalam.
                </p>
            </div>
        </div>
    </div>
</div>


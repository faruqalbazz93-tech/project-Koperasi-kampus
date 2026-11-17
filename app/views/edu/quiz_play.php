<?php
$payload = $quiz['payload'] ?? [];
$questions = $payload['questions'] ?? [];
$isEvaluated = isset($_GET['evaluated']) || ($quiz['score'] > 0);
?>

<div class="container py-5">
    <!-- Header Kuis -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
        <div class="card-header bg-ikmi-primary text-white p-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold mb-1">Simulasi Kuis Interaktif</span>
                <h4 class="fw-bold mb-0"><?= htmlspecialchars($quiz['course_name']) ?>: <?= htmlspecialchars($quiz['topic']) ?></h4>
                <small class="text-white-50"><?= INSTITUTION_NAME ?> &bull; Tipe: <?= htmlspecialchars($quiz['question_type']) ?></small>
            </div>
            <div>
                <?php if ($isEvaluated): ?>
                    <div class="text-end">
                        <span class="badge bg-warning text-dark fs-6 px-3 py-2 rounded-pill">
                            <i class="bi bi-trophy-fill me-1"></i> Skor Anda: <?= number_format($quiz['score'], 1) ?> / 100
                        </span>
                    </div>
                <?php else: ?>
                    <span class="badge bg-light text-dark px-3 py-2 rounded-pill">
                        <i class="bi bi-patch-question-fill me-1 text-primary"></i> <?= count($questions) ?> Butir Soal
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <div class="card-body p-4 p-lg-5">
            <?php if ($isEvaluated): ?>
                <div class="alert alert-success border-0 rounded-4 p-4 mb-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="fw-bold mb-1 text-success"><i class="bi bi-check-circle-fill me-2"></i>Evaluasi Kuis Selesai!</h5>
                        <p class="mb-0 text-secondary small">Kunci jawaban dan penjelasan telah dibuka. Anda dapat mengklik tombol <strong>"Bedah Konsep (AI Explainer)"</strong> untuk pembahasan materi secara komprehensif.</p>
                    </div>
                    <a href="<?= BASE_URL ?>/aiEdu" class="btn btn-outline-success rounded-pill btn-sm px-3 flex-shrink-0">
                        <i class="bi bi-arrow-repeat me-1"></i> Buat Kuis Baru
                    </a>
                </div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>/aiEdu/submitQuiz" method="POST">
                <input type="hidden" name="quiz_id" value="<?= $quiz['id'] ?>">

                <?php foreach ($questions as $index => $q): ?>
                    <div class="card border mb-4 rounded-3 p-4 bg-light shadow-2xs">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge bg-primary text-white rounded-pill px-3 py-1">Soal #<?= $q['id'] ?? ($index + 1) ?></span>
                            
                            <!-- Tombol AI Explainer di setiap butir soal -->
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 btn-ask-ai-explainer"
                                    data-question="<?= htmlspecialchars($q['question']) ?>"
                                    data-topic="<?= htmlspecialchars($quiz['topic']) ?>"
                                    data-course="<?= htmlspecialchars($quiz['course_name']) ?>">
                                <i class="bi bi-cpu text-warning me-1"></i> Bedah Konsep (AI Explainer)
                            </button>
                        </div>

                        <h5 class="fw-bold text-dark mb-4 lh-base">
                            <?= htmlspecialchars($q['question']) ?>
                        </h5>

                        <?php if ($quiz['question_type'] === 'Pilihan Ganda' && !empty($q['options'])): ?>
                            <div class="row g-3 mb-3">
                                <?php foreach ($q['options'] as $key => $optVal): ?>
                                    <div class="col-md-6">
                                        <div class="position-relative">
                                            <input class="form-check-input visually-hidden quiz-option-radio" type="radio" 
                                                   name="answers[<?= $q['id'] ?>]" 
                                                   id="q_<?= $q['id'] ?>_<?= $key ?>" 
                                                   value="<?= $key ?>" <?= $isEvaluated ? 'disabled' : '' ?>>
                                            <label class="quiz-option-label w-100" for="q_<?= $q['id'] ?>_<?= $key ?>">
                                                <span class="badge bg-secondary-subtle text-dark border me-2 fw-bold"><?= $key ?></span>
                                                <span class="text-secondary"><?= htmlspecialchars($optVal) ?></span>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <?php if ($isEvaluated): ?>
                                <div class="mt-3 p-3 bg-white border border-success-subtle rounded-3">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge bg-success"><i class="bi bi-check-lg"></i> Kunci Jawaban Resmi: <?= $q['answer_key'] ?></span>
                                    </div>
                                    <small class="text-secondary d-block mt-2">
                                        <strong>Penjelasan Singkat:</strong> <?= htmlspecialchars($q['explanation'] ?? 'Jawaban yang tepat sesuai kurikulum STMIK IKMI.') ?>
                                    </small>
                                </div>
                            <?php endif; ?>

                        <?php else: ?>
                            <!-- Form Essay -->
                            <div class="mb-3">
                                <label class="form-label small text-muted">Jawaban Essay Anda:</label>
                                <textarea name="answers[<?= $q['id'] ?>]" class="form-control" rows="4" 
                                          placeholder="Tuliskan analisis, langkah algoritma, atau penjelasan konsep Anda di sini..." <?= $isEvaluated ? 'readonly' : '' ?>></textarea>
                            </div>

                            <?php if ($isEvaluated): ?>
                                <div class="mt-3 p-3 bg-white border border-info-subtle rounded-3">
                                    <span class="badge bg-info text-dark mb-2"><i class="bi bi-lightbulb"></i> Kunci Jawaban & Panduan Penilaian</span>
                                    <p class="small text-dark mb-2"><strong>Referensi Jawaban:</strong> <?= htmlspecialchars($q['answer_key']) ?></p>
                                    <small class="text-muted d-block"><strong>Rubrik Penilaian:</strong> <?= htmlspecialchars($q['rubric'] ?? '') ?></small>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                    </div>
                <?php endforeach; ?>

                <?php if (!$isEvaluated): ?>
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-warning text-dark fw-bold py-3 rounded-pill shadow-sm fs-6">
                            <i class="bi bi-send-check-fill me-1"></i> Submit Jawaban & Evaluasi Nilai Kuis
                        </button>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>


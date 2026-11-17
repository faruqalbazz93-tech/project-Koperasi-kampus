<?php

class AiService {
    /**
     * Generator Kuis Berbasis AI
     */
    public function generateQuiz(string $course, string $topic, string $type = 'Pilihan Ganda', int $count = 5): array {
        $count = max(1, min($count, 10)); // Batasi 1 - 10 soal
        $questions = [];

        $knowledgeBank = $this->getCurriculumKnowledgeBank();
        $matchedCourse = null;

        foreach ($knowledgeBank as $cKey => $cData) {
            if (stripos($course, $cKey) !== false || stripos($cKey, $course) !== false) {
                $matchedCourse = $cData;
                break;
            }
        }

        if ($type === 'Pilihan Ganda') {
            $pool = $matchedCourse['pilihan_ganda'] ?? $this->generateAlgorithmicMcq($course, $topic);
            shuffle($pool);
            $selected = array_slice($pool, 0, $count);

            // Re-index ID
            foreach ($selected as $idx => $q) {
                $q['id'] = $idx + 1;
                $questions[] = $q;
            }
        } else {
            // Essay
            $pool = $matchedCourse['essay'] ?? $this->generateAlgorithmicEssay($course, $topic);
            shuffle($pool);
            $selected = array_slice($pool, 0, $count);

            foreach ($selected as $idx => $q) {
                $q['id'] = $idx + 1;
                $questions[] = $q;
            }
        }

        return [
            'institution' => INSTITUTION_NAME,
            'course' => $course,
            'topic' => $topic,
            'type' => $type,
            'total_questions' => count($questions),
            'generated_at' => date('Y-m-d H:i:s'),
            'questions' => $questions
        ];
    }

    /**
     * AI Explainer: Bedah konsep & rumus mendalam
     */
    public function explainQuestion(string $questionText, string $topic, string $course): array {
        // Analisis topik dan berikan penjelasan mendalam
        $breakdown = $this->analyzeAcademicTopic($topic, $questionText);

        return [
            'institution' => INSTITUTION_NAME,
            'topic' => $topic,
            'course' => $course,
            'question_snippet' => $questionText,
            'concept_title' => $breakdown['title'],
            'fundamental_theory' => $breakdown['theory'],
            'formula_or_code' => $breakdown['formula_or_code'],
            'step_by_step_analysis' => $breakdown['steps'],
            'academic_note' => "Materi referensi standar kurikulum teknologi informasi STMIK IKMI.",
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * Bank Pengetahuan Kurikulum STMIK IKMI
     */
    private function getCurriculumKnowledgeBank(): array {
        return [
            'Algoritma' => [
                'pilihan_ganda' => [
                    [
                        'question' => 'Berapakah kompleksitas waktu rata-rata (average time complexity) pada Balanced Binary Search Tree (AVL Tree) untuk operasi pencarian elemen?',
                        'options' => [
                            'A' => 'O(1)',
                            'B' => 'O(log n)',
                            'C' => 'O(n)',
                            'D' => 'O(n log n)'
                        ],
                        'answer_key' => 'B',
                        'explanation' => 'Pada AVL Tree atau Red-Black Tree, pohon dijaga agar selalu seimbang dengan faktor selisih tinggi maksimal 1. Karena tinggi pohon h = floor(log2(n)), operasi pencarian selalu bernilai O(log n).'
                    ],
                    [
                        'question' => 'Struktur data manakah yang menerapkan prinsip First-In-First-Out (FIFO)?',
                        'options' => [
                            'A' => 'Stack',
                            'B' => 'Queue (Antrean)',
                            'C' => 'Binary Tree',
                            'D' => 'Max Heap'
                        ],
                        'answer_key' => 'B',
                        'explanation' => 'Queue mengadopsi mekanisme FIFO di mana elemen yang pertama kali masuk (enqueue) akan menjadi elemen yang pertama kali keluar (dequeue).'
                    ],
                    [
                        'question' => 'Pada algoritma Quicksort, teknik partitioning Lomuto memilih elemen pivot di posisi:',
                        'options' => [
                            'A' => 'Elemen paling tengah',
                            'B' => 'Elemen paling akhir (rightmost)',
                            'C' => 'Elemen acak menggunakan RNG',
                            'D' => 'Median dari tiga elemen pertama'
                        ],
                        'answer_key' => 'B',
                        'explanation' => 'Skema partisi Lomuto secara konseptual menetapkan elemen pivot pada index terakhir array, lalu menempatkan semua elemen yang lebih kecil ke sisi kiri.'
                    ],
                    [
                        'question' => 'Manakah dari struktur data berikut yang paling optimal untuk implementasi antrean prioritas (Priority Queue)?',
                        'options' => [
                            'A' => 'Singly Linked List',
                            'B' => 'Binary Heap (Min-Heap / Max-Heap)',
                            'C' => 'Circular Queue',
                            'D' => 'Double-ended Queue'
                        ],
                        'answer_key' => 'B',
                        'explanation' => 'Binary Heap memungkinkan operasi penambahan (insert) dan ekstraksi nilai prioritas tertinggi (extract-min/max) berjalan dalam efisiensi waktu O(log n).'
                    ]
                ],
                'essay' => [
                    [
                        'question' => 'Jelaskan perbedaan mendasar antara Algoritma Dijkstra dan Algoritma Bellman-Ford dalam pencarian rute terpendek (Shortest Path)!',
                        'answer_key' => 'Dijkstra menggunakan pendekatan greedy dan hanya bekerja pada graf dengan bobot sisi (edge) non-negatif dengan efisiensi O((V + E) log V). Sedangkan Bellman-Ford menggunakan pemrograman dinamis, mampu menangani graf dengan bobot sisi negatif, serta dapat mendeteksi adanya negative weight cycle dengan kompleksitas O(V * E).',
                        'rubric' => '1) Penjelasan pendekatan Greedy vs Dynamic Programming (bobot 30%). 2) Penanganan bobot negatif (bobot 40%). 3) Kompleksitas waktu (bobot 30%).'
                    ],
                    [
                        'question' => 'Uraikan bagaimana arsitektur Call Stack bekerja pada pemanggilan fungsi rekursif dan apa yang memicu terjadinya Stack Overflow!',
                        'answer_key' => 'Setiap kali fungsi rekursif dipanggil, sebuah stack frame (menyimpan parameter, variabel lokal, dan return address) didorong (pushed) ke dalam call stack memori. Jika kondisi terminasi (base case) tidak tercapai atau kedalaman rekursi melampaui batas alokasi stack sistem, maka akan terjadi exception Stack Overflow.',
                        'rubric' => '1) Konsep Stack Frame & Call Stack (bobot 40%). 2) Peran Base Case (bobot 30%). 3) Mekanisme Stack Overflow (bobot 30%).'
                    ]
                ]
            ],
            'Basis Data' => [
                'pilihan_ganda' => [
                    [
                        'question' => 'Normalisasi data tahap ketiga (Third Normal Form / 3NF) mensyaratkan relasi telah memenuhi 2NF serta:',
                        'options' => [
                            'A' => 'Tidak memiliki multivalue attribute',
                            'B' => 'Tidak ada ketergantungan transitif (transitive dependency)',
                            'C' => 'Setiap kolom adalah Foreign Key',
                            'D' => 'Semua tabel digabungkan menjadi satu tabel denormalisasi'
                        ],
                        'answer_key' => 'B',
                        'explanation' => '3NF mensyaratkan tabel berada dalam 2NF dan tidak mengandung ketergantungan transitif (atribut non-primary key tidak boleh bergantung pada atribut non-primary key lainnya).'
                    ],
                    [
                        'question' => 'Prinsip ACID pada sistem transaksi basis data relational, huruf "I" merepresentasikan:',
                        'options' => [
                            'A' => 'Integrity',
                            'B' => 'Isolation',
                            'C' => 'Indexing',
                            'D' => 'Iteration'
                        ],
                        'answer_key' => 'B',
                        'explanation' => 'Isolation (Isolasi) menjamin bahwa eksekusi transaksi yang berjalan secara konkuren tidak saling mengganggu dan memberikan hasil yang sama seolah dieksekusi secara sekuensial.'
                    ],
                    [
                        'question' => 'Jenis indeks apakah yang secara fisik mengurutkan baris data pada media penyimpanan tabel di MySQL InnoDB?',
                        'options' => [
                            'A' => 'Secondary Index',
                            'B' => 'Fulltext Index',
                            'C' => 'Clustered Index (Primary Key)',
                            'D' => 'Spatial Index'
                        ],
                        'answer_key' => 'C',
                        'explanation' => 'Pada InnoDB, Clustered Index mengorganisasi penyimpanan data fisik tabel berdasarkan urutan kunci primer (B+ Tree Leaf node menyimpan seluruh baris data).'
                    ]
                ],
                'essay' => [
                    [
                        'question' => 'Jelaskan perbedaan mendasar antara database relasional (SQL) dan basis data NoSQL (Document Store seperti MongoDB) dalam hal skalabilitas dan fleksibilitas skema!',
                        'answer_key' => 'RDBMS (SQL) mengadopsi skema kaku (structured schema) dengan integritas referensial dan transaksi ACID kuat, umumnya diskalakan secara vertikal. Database NoSQL Document-based mengadopsi skema dinamis (schema-less JSON/BSON) yang memudahkan perubahan struktur dokumen dan dirancang untuk skalabilitas horizontal (sharding) pada klaster server.',
                        'rubric' => '1) Analisis fleksibilitas skema (bobot 40%). 2) Skalabilitas Vertikal vs Horizontal (bobot 40%). 3) Kasus penggunaan nyata (bobot 20%).'
                    ]
                ]
            ],
            'Rekayasa Perangkat Lunak' => [
                'pilihan_ganda' => [
                    [
                        'question' => 'Pada pola arsitektur Model-View-Controller (MVC), komponen manakah yang bertanggung jawab mengelola logika bisnis, manipulasi data, dan aturan validasi?',
                        'options' => [
                            'A' => 'View',
                            'B' => 'Controller',
                            'C' => 'Model',
                            'D' => 'Router'
                        ],
                        'answer_key' => 'C',
                        'explanation' => 'Model merepresentasikan struktur data, entitas bisnis, aturan validasi, dan komunikasi ke persistence layer (database).'
                    ],
                    [
                        'question' => 'Dalam prinsip SOLID programming, huruf "L" merujuk pada:',
                        'options' => [
                            'A' => 'Layered Architecture Principle',
                            'B' => 'Liskov Substitution Principle',
                            'C' => 'Loose Coupling Principle',
                            'D' => 'Lazy Loading Principle'
                        ],
                        'answer_key' => 'B',
                        'explanation' => 'Liskov Substitution Principle (LSP) menyatakan bahwa objek dari subclass harus dapat menggantikan objek superclass tanpa merusak kebenaran program.'
                    ]
                ],
                'essay' => [
                    [
                        'question' => 'Uraikan alur siklus hidup request (Request Lifecycle) pada aplikasi web berbasis arsitektur MVC dari saat browser mengirim HTTP Request hingga halaman ditampilkan kembali!',
                        'answer_key' => '1) Client mengirim HTTP request ke web server. 2) Entry point (index.php) menerima request dan meneruskannya ke Router. 3) Router mem-parsing URL dan memanggil Controller & Action yang sesuai. 4) Controller memanggil Model untuk mengambil atau memproses data bisnis. 5) Model berinteraksi dengan database dan mengembalikan dataset ke Controller. 6) Controller meneruskan data ke View template. 7) View merender HTML/CSS dan dikirimkan sebagai HTTP Response kembali ke browser klien.',
                        'rubric' => '1) Peran Router & Entry Point (bobot 25%). 2) Interaksi Controller & Model (bobot 35%). 3) Rendering View & HTTP Response (bobot 40%).'
                    ]
                ]
            ]
        ];
    }

    /**
     * Fallback AI Algorithmic MCQ Generator untuk mata kuliah kustom
     */
    private function generateAlgorithmicMcq(string $course, string $topic): array {
        return [
            [
                'question' => "Dalam konteks mata kuliah {$course} dengan fokus bahasan '{$topic}', apakah tujuan utama dari penerapan standardisasi proses?",
                'options' => [
                    'A' => 'Menghindari dokumentasi teknis',
                    'B' => 'Meningkatkan konsistensi, efisiensi, dan meminimalkan anomali operasional',
                    'C' => 'Menghilangkan kebutuhan akan pengujian sistem',
                    'D' => 'Membatasi skalabilitas perangkat lunak'
                ],
                'answer_key' => 'B',
                'explanation' => "Pada kajian {$topic}, standardisasi dirancang secara sistemik untuk menjamin konsistensi keluaran serta efisiensi beban komputasi."
            ],
            [
                'question' => "Faktor krusial apakah yang paling memengaruhi keandalan (reliability) dalam implementasi '{$topic}' pada skala kampus?",
                'options' => [
                    'A' => 'Validasi input yang ketat dan penanganan exception yang terstruktur',
                    'B' => 'Pengabaian struktur autentikasi',
                    'C' => 'Penyimpanan seluruh data di sisi frontend',
                    'D' => 'Penggunaan protokol tanpa enkripsi'
                ],
                'answer_key' => 'A',
                'explanation' => "Keandalan sistem pada topik {$topic} berpijak pada verifikasi data multi-lapis dan penanganan error yang elegan."
            ]
        ];
    }

    /**
     * Fallback AI Algorithmic Essay Generator
     */
    private function generateAlgorithmicEssay(string $course, string $topic): array {
        return [
            [
                'question' => "Analisis secara kritis bagaimana integrasi '{$topic}' pada mata kuliah {$course} dapat mengoptimalkan efisiensi sistem informasi terintegrasi di STMIK IKMI!",
                'answer_key' => "Penerapan {$topic} memperkuat interoperabilitas antar-modul, mengurangi redundansi data, serta mempercepat waktu pemrosesan transaksi akademik dan operasional kampus.",
                'rubric' => '1) Pemahaman konsep teoritis (bobot 40%). 2) Analisis dampak efisiensi (bobot 35%). 3) Relevansi studi kasus kampus (bobot 25%).'
            ]
        ];
    }

    /**
     * Engine Analisis Akademik AI Explainer
     */
    private function analyzeAcademicTopic(string $topic, string $questionSnippet): array {
        $cleanTopic = strtolower($topic . ' ' . $questionSnippet);

        if (strpos($cleanTopic, 'avl') !== false || strpos($cleanTopic, 'tree') !== false || strpos($cleanTopic, 'pencarian') !== false) {
            return [
                'title' => 'Bedah Konsep: Self-Balancing Binary Search Tree & Kompleksitas O(log n)',
                'theory' => 'Binary Search Tree (BST) biasa dapat mengalami degenerasi menjadi linked list jika data dimasukkan secara terurut (skewed), dengan kompleksitas O(n). Algoritma AVL Tree mencegah hal ini dengan memelihara "Balance Factor" di setiap simpul: BF = height(left) - height(right) dengan nilai di antara {-1, 0, 1}.',
                'formula_or_code' => "BF(v) = h(v.left) - h(v.right) \nRotasi: LL, RR, LR, RL\nTinggi h \leq 1.44 \log_2(n) \implies T(n) = O(\log n)",
                'steps' => [
                    'Langkah 1: Setiap operasi insert atau delete di BST diperiksa keseimbangannya dari leaf ke root.',
                    'Langkah 2: Jika ditemukan simpul dengan |BF| > 1, rotasi tunggal (Single Rotation) atau rotasi ganda (Double Rotation) segera dieksekusi.',
                    'Langkah 3: Menjaga tinggi pohon tetap proporsional terhadap log2(n), menjamin operasi Find, Min, Max tetap O(log n).'
                ]
            ];
        }

        if (strpos($cleanTopic, 'mvc') !== false || strpos($cleanTopic, 'model') !== false || strpos($cleanTopic, 'controller') !== false) {
            return [
                'title' => 'Bedah Konsep: Separation of Concerns dalam Pola Arsitektur MVC',
                'theory' => 'Model-View-Controller adalah pola arsitektural yang memisahkan representasi informasi internal (Model), antarmuka grafis yang dilihat pengguna (View), dan logika kontrol alur permintaan (Controller).',
                'formula_or_code' => "Browser Request \to Router \to Controller::action() \n\to Model::query() \to Database \n\to Controller renders View(data) \to HTTP Response",
                'steps' => [
                    'Model: Menjaga integritas data, validasi, dan transaksi persistensi database.',
                    'View: Bertanggung jawab murni atas penyajian antarmuka (UI/UX) tanpa logika bisnis berat.',
                    'Controller: Orkestrator yang menerima input HTTP, memvalidasi otentikasi/role, memanggil service/model, dan memilih view yang sesuai.'
                ]
            ];
        }

        if (strpos($cleanTopic, '3nf') !== false || strpos($cleanTopic, 'normalisasi') !== false || strpos($cleanTopic, 'database') !== false) {
            return [
                'title' => 'Bedah Konsep: Ketergantungan Transitif & Normalisasi 3NF',
                'theory' => 'Third Normal Form (3NF) bertujuan menghilangkan anomali insert, update, dan delete dengan memastikan bahwa setiap atribut non-kunci hanya bergantung secara langsung pada primary key (ketergantungan fungsional penuh tanpa perantara).',
                'formula_or_code' => "Jika X \to Y dan Y \to Z (dengan Y bukan superkey), \nmaka X \to Z adalah Ketergantungan Transitif.\nSolusi 3NF: Pisahkan Y dan Z ke dalam relasi/tabel baru.",
                'steps' => [
                    'Identifikasi Functional Dependency: Tentukan atribut mana yang menentukan atribut lain.',
                    'Deteksi Anomali: Jika pembaruan data satu entitas mengharuskan pembaruan banyak baris berulang, terdapat transitif dependency.',
                    'Dekomposisi Tanpa Kehilangan Informasi (Lossless Join): Pisahkan tabel agar tidak ada redundansi.'
                ]
            ];
        }

        // General fallback explainer
        return [
            'title' => "Bedah Konsep Mendalam: " . htmlspecialchars($topic),
            'theory' => "Konsep {$topic} berfokus pada efisiensi operasional dan ketahanan logika komputasi. Prinsip dasarnya mengutamakan arsitektur yang modular, kemudahan pemeliharaan (maintainability), dan keamanan data.",
            'formula_or_code' => "Optimal Solution: Efficiency \propto \\frac{Useful Work}{Computational Overhead}",
            'steps' => [
                'Langkah 1: Identifikasi input, batasan komputasi, dan kondisi batas (edge cases).',
                'Langkah 2: Terapkan prinsip perancangan modular untuk mengisolasi setiap tanggung jawab fungsi.',
                'Langkah 3: Uji validasi output terhadap spesifikasi akademis yang diharapkan.'
            ]
        ];
    }
}


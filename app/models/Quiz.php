<?php

class Quiz {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function saveQuiz(int $userId, string $course, string $topic, string $type, array $payload): int {
        $stmt = $this->db->prepare("
            INSERT INTO quizzes (user_id, course_name, topic, question_type, raw_payload, score)
            VALUES (:user_id, :course_name, :topic, :question_type, :raw_payload, 0)
        ");
        $stmt->execute([
            ':user_id' => $userId,
            ':course_name' => $course,
            ':topic' => $topic,
            ':question_type' => $type,
            ':raw_payload' => json_encode($payload, JSON_UNESCAPED_UNICODE)
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function getQuiz(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM quizzes WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $quiz = $stmt->fetch();

        if ($quiz) {
            $quiz['payload'] = json_decode($quiz['raw_payload'], true);
            return $quiz;
        }

        return null;
    }

    public function updateScore(int $id, float $score): bool {
        $stmt = $this->db->prepare("UPDATE quizzes SET score = :score WHERE id = :id");
        return $stmt->execute([
            ':id' => $id,
            ':score' => $score
        ]);
    }

    public function getUserQuizzes(int $userId): array {
        $stmt = $this->db->prepare("SELECT * FROM quizzes WHERE user_id = :user_id ORDER BY id DESC");
        $stmt->execute([':user_id' => $userId]);
        $rows = $stmt->fetchAll();

        foreach ($rows as &$r) {
            $r['payload'] = json_decode($r['raw_payload'], true);
        }

        return $rows;
    }
}


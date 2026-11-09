<?php

namespace App\Services;

class GradingService
{
    public static function grade(float $marks, float $full): array
    {
        $p = $full > 0 ? ($marks / $full) * 100 : 0;
        if ($p >= 80) return ['grade' => 'A+', 'point' => 5.0];
        if ($p >= 70) return ['grade' => 'A', 'point' => 4.0];
        if ($p >= 60) return ['grade' => 'A-', 'point' => 3.5];
        if ($p >= 50) return ['grade' => 'B', 'point' => 3.0];
        if ($p >= 40) return ['grade' => 'C', 'point' => 2.0];
        if ($p >= 33) return ['grade' => 'D', 'point' => 1.0];
        return ['grade' => 'F', 'point' => 0.0];
    }

    public static function gradeFromGpa(float $gpa): string
    {
        if ($gpa >= 5) return 'A+';
        if ($gpa >= 4) return 'A';
        if ($gpa >= 3.5) return 'A-';
        if ($gpa >= 3) return 'B';
        if ($gpa >= 2) return 'C';
        if ($gpa >= 1) return 'D';
        return 'F';
    }

    public static function final(array $rows): array
    {
        $main = array_values(array_filter($rows, fn ($r) => !(bool)($r['is_fourth_subject'] ?? false)));
        $fourth = array_values(array_filter($rows, fn ($r) => (bool)($r['is_fourth_subject'] ?? false)));
        if (!$main) return ['gpa' => 0.0, 'grade' => 'F', 'status' => 'Fail'];
        foreach ($main as $r) {
            if (($r['grade'] ?? 'F') === 'F' || (float)($r['grade_point'] ?? 0) <= 0) {
                return ['gpa' => 0.0, 'grade' => 'F', 'status' => 'Fail'];
            }
        }
        $sum = array_sum(array_map(fn ($r) => (float)$r['grade_point'], $main));
        foreach ($fourth as $r) if ((float)$r['grade_point'] > 2) $sum += (float)$r['grade_point'] - 2;
        $gpa = min(5, max(0, round($sum / count($main), 2)));
        return ['gpa' => $gpa, 'grade' => self::gradeFromGpa($gpa), 'status' => 'Pass'];
    }

    public static function process($subjects, array $entries): array
    {
        $byId = collect($subjects)->keyBy('id');
        $seen = [];
        $rows = [];
        foreach ($entries as $entry) {
            $id = (int)($entry['subject_id'] ?? 0);
            $subject = $byId->get($id);
            if (!$subject) throw new \InvalidArgumentException('Invalid subject found.');
            if (isset($seen[$id])) throw new \InvalidArgumentException($subject->subject_name.' is entered more than once.');
            $seen[$id] = true;
            $raw = $entry['marks'] ?? null;
            if ($raw === '' || $raw === null || !is_numeric($raw)) throw new \InvalidArgumentException('Invalid marks for '.$subject->subject_name.'.');
            $marks = (float)$raw;
            if ($marks < 0 || $marks > (float)$subject->full_marks) throw new \InvalidArgumentException('Invalid marks for '.$subject->subject_name.'.');
            $g = self::grade($marks, (float)$subject->full_marks);
            $rows[] = [
                'subject_id' => $id, 'marks' => $marks, 'grade' => $g['grade'],
                'grade_point' => $g['point'], 'is_fourth_subject' => $subject->subject_type === 'fourth'
            ];
        }
        return ['rows' => $rows, 'final' => self::final($rows)];
    }
}

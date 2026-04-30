<?php
require_once 'session.php';

// Calculate overtime pay (1.5x rate) [cite: 15]
function calculateOTPay($hours, $wage) {
    return $hours * ($wage * 1.5);
}

// Statistics: Total Overtime Payout [cite: 18]
function getTotalPayout($employees) {
    $total = 0;
    foreach ($employees as $emp) {
        $totalHours = array_sum($emp['overtime_logs']);
        $total += calculateOTPay($totalHours, $emp['wage']);
    }
    return $total;
}

// Statistics: Average Overtime Pay [cite: 17]
function getAverageOT($employees) {
    if (empty($employees)) return 0;
    return getTotalPayout($employees) / count($employees);
}

// Statistics: Employee with most overtime [cite: 19]
function getTopEmployee($employees) {
    if (empty($employees)) return "None";
    $max = -1;
    $winner = "";
    foreach ($employees as $name => $data) {
        $sum = array_sum($data['overtime_logs']);
        if ($sum > $max) {
            $max = $sum;
            $winner = $name;
        }
    }
    return $winner;
}

// Search Filter using array_filter() [cite: 24, 25]
function filterEmployeesByName($employees, $searchTerm) {
    if (empty($searchTerm)) return $employees;
    return array_filter($employees, function($name) use ($searchTerm) {
        return str_contains(strtolower($name), strtolower($searchTerm));
    }, ARRAY_FILTER_USE_KEY);
}
?>
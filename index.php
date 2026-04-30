<?php
require_once 'functions.php';

// Logic: Add Employee [cite: 13]
if (isset($_POST['add_emp'])) {
    $name = htmlspecialchars($_POST['name']);
    $wage = (float)$_POST['wage'];
    $reg_hours = (float)$_POST['reg_hours'];

    if ($wage >= 10 && $reg_hours >= 0 && $reg_hours <= 60) {
        $_SESSION['employees'][$name] = [
            'wage' => $wage,
            'reg_hours' => $reg_hours,
            'overtime_logs' => [] // Array for multiple logs 
        ];
    }
}

// Logic: Log Overtime [cite: 14]
if (isset($_POST['log_ot'])) {
    $name = $_POST['target_name'];
    $hours = (float)$_POST['ot_val'];
    if ($hours >= 0 && $hours <= 20 && isset($_SESSION['employees'][$name])) {
        $_SESSION['employees'][$name]['overtime_logs'][] = $hours;
    }
}

$search = $_GET['search'] ?? '';
$list = filterEmployeesByName($_SESSION['employees'], $search);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Overtime Tracker</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>⏱ Employee Overtime Tracker</h1>
        
        <div class="grid">
            <div class="card">
                <h3>👤 Add Employee</h3>
                <form method="POST">
                    <input type="text" name="name" placeholder="Full Name" required>
                    <input type="number" name="wage" min="10" placeholder="Wage (Min $10)" required>
                    <input type="number" name="reg_hours" min="0" max="60" placeholder="Reg Hours (0-60)" required>
                    <button type="submit" name="add_emp" class="btn blue">Add Employee</button>
                </form>
            </div>

            <div class="card">
                <h3>⏰ Log Overtime</h3>
                <form method="POST">
                    <select name="target_name" required>
                        <option value="">-- Choose Employee --</option>
                        <?php foreach($_SESSION['employees'] as $name => $v): ?>
                            <option value="<?= $name ?>"><?= $name ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="number" name="ot_val" min="0" max="20" placeholder="Hours (0-20)">
                    <button type="submit" name="log_ot" class="btn blue">Add Overtime</button>
                </form>
            </div>

            <div class="card">
                <h3>🛠 Actions</h3>
                <form method="POST">
                    <button type="submit" name="reset_data" class="btn red">Reset All Data</button>
                </form>
                <form method="GET" style="margin-top:10px;">
                    <input type="text" name="search" placeholder="Filter by name..." value="<?= $search ?>">
                    <button type="submit" class="btn gray">Search</button>
                </form>
            </div>
        </div>

        <div class="stats">
            <div class="s-card"><h4><?= count($_SESSION['employees']) ?></h4><p>Total Employees</p></div>
            <div class="s-card"><h4><?= getTopEmployee($_SESSION['employees']) ?></h4><p>Most Overtime</p></div>
            <div class="s-card"><h4>$<?= number_format(getAverageOT($_SESSION['employees']), 2) ?></h4><p>Avg OT Pay</p></div>
            <div class="s-card"><h4>$<?= number_format(getTotalPayout($_SESSION['employees']), 2) ?></h4><p>Total Payout</p></div>
        </div>

        <div class="card">
            <h3>📋 Summary</h3>
            <table>
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Reg Hours</th>
                        <th>Wage</th>
                        <th>OT Hours</th>
                        <th>OT Pay</th>
                        <th>Total Wages</th>
                        <th>Logs</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($list as $name => $d): 
                        $totalOT = array_sum($d['overtime_logs']);
                        $otPay = calculateOTPay($totalOT, $d['wage']);
                        $totalWage = ($d['reg_hours'] * $d['wage']) + $otPay;
                    ?>
                    <tr>
                        <td><?= $name ?></td>
                        <td><?= $d['reg_hours'] ?></td>
                        <td>$<?= $d['wage'] ?></td>
                        <td><?= $totalOT ?></td>
                        <td>$<?= number_format($otPay, 2) ?></td>
                        <td>$<?= number_format($totalWage, 2) ?></td>
                        <td><?= implode(', ', $d['overtime_logs']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
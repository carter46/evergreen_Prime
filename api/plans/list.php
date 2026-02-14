<?php
/**
 * Bloombit - Plans List API
 * GET /api/plans/list.php
 */

header('Content-Type: application/json');

$plans = [
    ['id' => 1, 'name' => 'Starter', 'min' => 100, 'max' => 2500, 'yield_min' => 0.5, 'yield_max' => 1.2],
    ['id' => 2, 'name' => 'Pro', 'min' => 2500, 'max' => 25000, 'yield_min' => 1.0, 'yield_max' => 2.5],
    ['id' => 3, 'name' => 'Institutional', 'min' => 25000, 'max' => null, 'yield_min' => 1.5, 'yield_max' => 3.0]
];

echo json_encode(['success' => true, 'data' => $plans]);

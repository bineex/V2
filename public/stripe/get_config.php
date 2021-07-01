<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'config.php';

//echo json_encode(['publicKey' => $config['stripe_publishable_key'], 'basicPlan' => $config['basic_plan_id'], 'proPlan' => $config['proPlan']]);
echo json_encode(['publicKey' => $confStripe['stripe_publishable_key']]);
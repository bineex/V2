<?php
require_once 'config.php';

echo json_encode(['publicKey' => $config['stripe_publishable_key'], 'basicPlan' => $config['basic_plan_id'], 'proPlan' => $config['proPlan']]);

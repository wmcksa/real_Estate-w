<?php

return [
  'all' => [
      'title' => 'All Investments',
      'investment_view' => 'admin.transaction.investLog',
  ],
    'running' => [
        'title' => 'Running Investments',
        'investment_view' => 'admin.property.runningInvestmentList',
    ],
    'due' => [
        'title' => 'Due Investments',
        'investment_view' => 'admin.property.dueInvestmentList',
    ],
    'expired' => [
        'title' => 'Expired Investments',
        'investment_view' => 'admin.property.expiredInvestmentList',
    ],
    'completed' => [
        'title' => 'Completed Investments',
        'investment_view' => 'admin.property.completedInvestmentList',
    ],
];

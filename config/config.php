<?php

return [
    // route alias
    'route_alias' => [
        'api' => env('API_ALIAS', 'api/v1'),
        'frontend' => env('FRONTEND_ALIAS', '/'),
        'backend' => env('BACKEND_ALIAS', 'admin'),
        'agent' => env('AGENT_ALIAS', 'agent'),
    ],

    // SBO agent
    'sbo_agent' => env('AGENT_ACCOUNT', 'noci88'),

    // sbo api
    'sbo_api' => [
        'resend_order' => env('SBO_API_URL') . '/web-root/restricted/seamless-wallet/resend-order',
        'check_transaction_status' => env('SBO_API_URL') . '/web-root/restricted/player/check-transaction-status.aspx',
        'agent_bet_setting' => env('SBO_API_URL') . '/web-root/restricted/agent/update-agent-preset-bet-settings.aspx',
        'register_player' => env('SBO_API_URL') . '/web-root/restricted/player/register-player.aspx',
        'login' => env('SBO_API_URL') . '/web-root/restricted/player/login.aspx',
        'deposit' => env('SBO_API_URL') . '/web-root/restricted/player/deposit.aspx',
        'withdraw' => env('SBO_API_URL') . '/web-root/restricted/player/withdraw.aspx',
        'update_bet_setting' => env('SBO_API_URL') . '/web-root/restricted/player/update-player-bet-settings.aspx',
        'get_bet_payload' => env('SBO_API_URL') . '/web-root/restricted/report/get-bet-payload.aspx',
    ],

    'sbo_swmd_api' => [
        'bet_detail' => env('SBO_SWMD_URL') . '/Report/BetDetail',
        'pull_report_wager_ids' => env('SBO_SWMD_URL') . '/Seamless/PullReportByWagerIDs',        

    ],

    // system currency
    'currency' => [
        'zh_cn' => 'CNY',
        'zh_hk' => 'HKD',
        'en' => 'USD',
        'vi' => 'VND',
        'th' => 'HB',
    ],

    // custom per page
    'custom_per_page' => [
        100 => 100,
        500 => 500,
        1000 => 1000,
    ],

    // Eeziepay
    'eeziepay' => [
        'service_version' => '3.1',
        'remarks_prefix' => 'remarks',
        'currency' => [
            'vi' => 'VND',
            'th' => 'THB',
            'en' => 'USD',
        ],
        'bank_code' => [
            'TCB.VN' => 'Ngân hàng TMCP Kỹ Thương Việt Nam - Techcombank',
            'SCM.VN' => 'Ngân hàng TMCP Sài Gòn Thương Tín - Sacombank',
            'VCB.VN' => 'Ngân hàng TMCP Ngoại thương Việt Nam - Vietcombank',
            'ACB.VN' => 'Ngân hàng TMCP Á Châu - ACB',
            'DAB.VN' => 'Ngân hàng TMCP Đông Á - DongA Bank',
            'VTB.VN' => 'Ngân hàng TMCP Công Thương Việt Nam - VietinBank',
            'BIDV.VN' => 'Ngân hàng TMCP Đầu tư và Phát triển Việt Nam - BIDV',
            'EXIM.VN' => 'Ngân hàng TMCP Xuất Nhập khẩu Việt Nam - Eximbank',
            'VBARD.VN' => 'Ngân hàng Nông nghiệp và Phát triển Nông thôn Việt Nam - Agribank',
        ],
        'bank_qr_code' => [
            'ACB.QR.VN' => 'Ngân hàng TMCP Á Châu - ACB',
            'BIDV.QR.VN' => 'Ngân hàng TMCP Đầu tư và Phát triển Việt Nam - BIDV',
            'VCB.QR.VN' => 'Ngân hàng TMCP Ngoại thương Việt Nam - Vietcombank',
            'VTB.QR.VN' => 'Ngân hàng TMCP Công Thương Việt Nam - VietinBank',
            'VPB.QR.VN' => 'Ngân hàng TMCP Việt Nam Thịnh Vượng - VPBank',
            'MB.QR.VN' => 'Ngân hàng Quân đội - MBBank',
            'TCB.QR.VN' => 'Ngân hàng TMCP Kỹ Thương Việt Nam - Techcombank',
        ],
    ],
];

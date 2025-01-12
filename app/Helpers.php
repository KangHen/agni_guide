<?php
if (!function_exists('bankTransfer')) {
    function bankTransfer(): object
    {
        return (object) [
            'name' => 'BRI',
            'logo' => 'logo_bri.svg',
            'code' => 'bri',
            'account_number' => '656901017379531',
            'account_name' => 'Zeny Wahyu Ningsih'
        ];
    }
}

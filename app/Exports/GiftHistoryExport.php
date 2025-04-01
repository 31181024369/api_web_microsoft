<?php

namespace App\Exports;

use App\Models\GiftHistory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class GiftHistoryExport implements FromCollection, WithHeadings, WithMapping
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function collection()
    {
        return $this->query->get();
    }

    public function headings(): array
    {
        return [
            'Tên người dùng',
            'Email',
            'Số điện thoại',
            'Tên quà',
            'Điểm sử dụng',
            'Điểm còn lại',
            'Thời gian đổi quà',
            'Địa chỉ',
            'Trạng thái',
            'Thời gian xác nhận'
        ];
    }

    public function map($giftHistory): array
    {
        return [
            $giftHistory->member->username,
            $giftHistory->member->email,
            $giftHistory->numberPhone,
            $giftHistory->gift->title,
            $giftHistory->points_used,
            $giftHistory->remaining_points ?? 0,
            $giftHistory->redeemed_at->format('d/m/Y H:i:s'),
            $giftHistory->streetAddress . ', ' . $giftHistory->wardAddress . ', ' . $giftHistory->districtAddress . ', ' . $giftHistory->cityAddress,
            $giftHistory->is_confirmed ? 'Đã xác nhận' : 'Chờ xác nhận',
            $giftHistory->confirmed_at ? $giftHistory->confirmed_at->format('d/m/Y H:i:s') : ''
        ];
    }
}
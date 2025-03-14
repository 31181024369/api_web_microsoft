<!DOCTYPE html>
<html>
<head>
    <title>Thông báo nhận quà</title>
</head>
<body>
    <h2>Xin chào {{ $recipientName }},</h2>

    <p>Cảm ơn bạn đã tham gia phần thi E-learning tại website của chúng tôi!</p>

    <h3>Thông tin nhận quà:</h3>
    <ul>
        <li><strong>Tên quà tặng:</strong> {{ $giftName }}</li>
        <li><strong>Mô tả:</strong> {{ $giftDescription }}</li>
        <li><strong>Thời gian đổi quà:</strong> {{ $redeemTime }}</li>
        <li><strong>Giá trị điểm thưởng:</strong> {{ $rewardPoints }} điểm</li>
    </ul>

    <h3>Hướng dẫn nhận quà:</h3>
    <p>{{ $deliveryInfo }}</p>

    <p>Nếu bạn có bất kỳ thắc mắc nào, vui lòng liên hệ với chúng tôi.</p>

    <p>Trân trọng,<br>
    Đội ngũ hỗ trợ</p>
</body>
</html>
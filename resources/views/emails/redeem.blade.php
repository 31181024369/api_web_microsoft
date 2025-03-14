<!DOCTYPE html>
<html>

<head>
    <title>Thông báo nhận quà</title>
    <style>
        .logo-container {
            text-align: center;
            margin: 20px 0;
        }

        .logo {
            max-width: 200px;
            height: auto;
        }
    </style>
</head>

<body>
    <div class="logo-container">
        <img src="http://edu.vitinhnguyenkim.com.vn/uploads/logoMicrosoft.jpg" alt="Logo" class="logo">
    </div>
    <h2>Xin chào {{ $recipientName }},</h2>

    <p>Cảm ơn bạn đã tham gia phần thi E-learning tại website của chúng tôi!</p>

    <h3>Thông tin nhận quà:</h3>
    <ul>
        <li><strong>Tên quà tặng:</strong> {{ $giftName }}</li>
        <li><strong>Mô tả:</strong> {{ $giftDescription }}</li>
        <li><strong>Thời gian đổi quà:</strong> {{ $redeemTime }}</li>
        <li><strong>Giá trị điểm thưởng:</strong> {{ $rewardPoints }} điểm</li>
        <li><strong>Địa chỉ:</strong> {{ $address }}</li>
        <li><strong>Số điện thoại:</strong> {{ $phoneNumber }}</li>
    </ul>

    <h3>Hướng dẫn nhận quà:</h3>
    <p>{{ $deliveryInfo }}</p>

    <p>Nếu cần hỗ trợ hoặc có bất kỳ thắc mắc nào, vui lòng liên hệ bộ phận hỗ trợ của chúng tôi qua email
        <b>baobq@nguyenkimvn.vn</b> hoặc <b>Bùi Quang Bảo - 0912246137</b>.
    </p>

    <p>Trân trọng,<br>
        Đội ngũ Quản trị Hệ thống Nguyên Kim & Microsoft </p>
</body>

</html>
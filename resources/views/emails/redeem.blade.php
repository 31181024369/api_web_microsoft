<!DOCTYPE html>
<html>

<head>
    <title>Thông báo xin lỗi và xác nhận thông tin nhận quà</title>
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

    <p>Chúng tôi xin chân thành xin lỗi vì sự cố hệ thống đã xảy ra. Theo quy định, mỗi khách hàng chỉ được nhận quà một lần. Tuy nhiên, do lỗi hệ thống, bạn có thể nhận được 2 món quà khác nhau. Chúng tôi rất tiếc về sự bất tiện này và cam kết sẽ khắc phục lỗi trong thời gian sớm nhất.</p>
    <p>Chúng tôi sẽ gửi bạn món quà bạn quy đổi có giá trị cao nhất.</p>
    <p>Thông tin quà tặng bạn sẽ nhận được như sau:</p>
    <ul>
        <li><strong>Tên quà tặng:</strong> {{ $giftName }}</li>
        <li><strong>Mô tả:</strong> {{ $giftDescription }}</li>
        <li><strong>Thời gian đổi quà:</strong> {{ $redeemTime }}</li>
        <li><strong>Giá trị điểm thưởng:</strong> {{ $rewardPoints }} điểm</li>
        <li><strong>Địa chỉ:</strong> {{ $address }}</li>
        <li><strong>Số điện thoại:</strong> {{ $phoneNumber }}</li>
        <li><strong>Quà sẽ được giao đến khách hàng từ ngày 1/4/ 2025 đến ngày 14/04/2025</strong></li>
    </ul>

    <p>Nếu cần hỗ trợ hoặc có bất kỳ thắc mắc nào, vui lòng liên hệ bộ phận hỗ trợ của chúng tôi qua email
        <b>baobq@nguyenkimvn.vn</b> hoặc <b>Bùi Quang Bảo - 0912246137</b>.
    </p>
    <p>Chúng tôi cảm ơn sự thông cảm và hỗ trợ của bạn.</p>
    <p>Trân trọng,<br>
        Đội ngũ Quản trị Hệ thống Nguyên Kim & Microsoft </p>
</body>

</html>
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GiftRedeemMail extends Mailable
{
    use Queueable, SerializesModels;

    public $recipientName;
    public $giftName;
    public $giftDescription;
    public $redeemTime;
    public $rewardPoints;
    public $deliveryInfo;
    public $address;
    public $phoneNumber;

    public function __construct($data)
    {
        $this->recipientName = $data['recipientName'];
        $this->giftName = $data['giftName'];
        $this->giftDescription = $data['giftDescription'];
        $this->redeemTime = $data['redeemTime'];
        $this->rewardPoints = $data['rewardPoints'];
        $this->deliveryInfo = $data['deliveryInfo'];
        $this->address = $data['address'];
        $this->phoneNumber = $data['phoneNumber'];
    }

    public function build()
    {
        return $this->subject('Thông báo đổi quà thành công')
            ->view('emails.redeem');
    }
}

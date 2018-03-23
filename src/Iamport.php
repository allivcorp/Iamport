<?php
namespace Alliv\Iamport;

use Exception;

class Iamport
{
    /** @var ApiClient */
    private $client;

    public function __construct(array $config = [])
    {
        $this->client = new ApiClient($config);
    }

    /**
     * 아임포트 고유번호로 결제내역을 확인합니다
     *
     * @param $impUid
     * @return Result
     */
    public function getPayment($impUid)
    {
        try {
            $response = $this->client->authRequest('GET', '/payments/' . $impUid);
            $payment = new Payment($response);
            return new Result(true, $payment);
        } catch (Exception $e) {
            return new Result(false, null, $e);
        }
    }

    /**
     * 승인된 결제를 취소합니다
     *
     * @param $impUid
     * @param $amount
     * @param $reason
     * @return Result
     */
    public function cancelPayment($impUid, $amount, $reason)
    {
        try {
            $response = $this->client->authRequest('POST', '/payments/cancel', [
                'imp_uid' => $impUid,
                'amount' => $amount,
                'reason' => $reason
            ]);
            $payment = new Payment($response);
            return new Result(true, $payment);
        } catch (Exception $e) {
            return new Result(false, null, $e);
        }
    }

    /**
     * 저장된 빌링키로 재결제를 하는 경우 사용
     *
     * @param $customerUid
     * @param $merchantUid
     * @param $amount
     * @param $orderName
     * @param null $buyerName
     * @param null $buyerEmail
     * @param null $buyerTel
     * @param null $buyerAddress
     * @param null $buyerPostcode
     * @return Result
     */
    public function subscribeAgain($customerUid, $merchantUid, $amount, $orderName, $buyerName = null, $buyerEmail = null, $buyerTel = null, $buyerAddress = null, $buyerPostcode = null)
    {
        $params = [
            'customer_uid' => $customerUid,
            'merchant_uid' => $merchantUid,
            'amount' => $amount,
            'name' => $orderName
        ];
        if (!empty($buyerName)) {
            $params['buyer_name'] = $buyerName;
        }
        if (!empty($buyerEmail)) {
            $params['buyer_email'] = $buyerEmail;
        }
        if (!empty($buyerTel)) {
            $params['buyer_tel'] = $buyerTel;
        }
        if (!empty($buyerAddress)) {
            $params['buyer_addr'] = $buyerAddress;
        }
        if (!empty($buyerPostcode)) {
            $params['buyer_postcode'] = $buyerPostcode;
        }

        try {
            $response = $this->client->authRequest('POST', '/subscribe/payments/again', $params);
            $payment = new Payment($response);
            return new Result(true, $payment);
        } catch (Exception $e) {
            return new Result(false, null, $e);
        }
    }

    /**
     * 구매자에 대해 빌링키 발급 및 저장
     *
     * @param $customerUid
     * @param $cardNumber
     * @param $expiry
     * @param $birth
     * @param null $pwd2Digit
     * @param null $customerName
     * @param null $customerTel
     * @param null $customerEmail
     * @param null $customerAddress
     * @param null $customerPostcode
     * @return Result
     */
    public function addSubscribeCustomer($customerUid, $cardNumber, $expiry, $birth, $pwd2Digit = null, $customerName = null, $customerTel = null, $customerEmail = null, $customerAddress = null, $customerPostcode = null)
    {
        $params = [
            'customer_uid' => $customerUid,
            'card_number' => $cardNumber,
            'expiry' => $expiry,
            'birth' => $birth
        ];
        if (!empty($pwd2Digit)) {
            $params['pwd_2digit'] = $pwd2Digit;
        }
        if (!empty($customerName)) {
            $params['customer_name'] = $customerName;
        }
        if (!empty($customerEmail)) {
            $params['customer_email'] = $customerEmail;
        }
        if (!empty($customerTel)) {
            $params['customer_tel'] = $customerTel;
        }
        if (!empty($customerAddress)) {
            $params['customer_addr'] = $customerAddress;
        }
        if (!empty($customerPostcode)) {
            $params['customer_postcode'] = $customerPostcode;
        }

        try {
            $response = $this->client->authRequest('POST', '/subscribe/customers/' . $customerUid, $params);
            return new Result(true, $response);
        } catch (Exception $e) {
            return new Result(false, null, $e);
        }
    }

    /**
     * 구매자의 빌링키 정보 삭제
     *
     * @param $customerUid
     * @return Result
     */
    public function removeSubscribeCustomer($customerUid)
    {
        try {
            $response = $this->client->authRequest('DELETE', '/subscribe/customers/' . $customerUid, [
                'customer_uid' => $customerUid
            ]);
            return new Result(true, $response);
        } catch (Exception $e) {
            return new Result(false, null, $e);
        }
    }
}

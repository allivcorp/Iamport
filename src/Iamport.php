<?php
namespace Alliv\Iamport;

use Exception;

class Iamport
{
    const PAYMENT_STATUS_ALL = 'all';               // 전체
    const PAYMENT_STATUS_READY = 'ready';           // 미결제
    const PAYMENT_STATUS_PAID = 'paid';             // 결제완료
    const PAYMENT_STATUS_CANCELLED = 'cancelled';   // 결제취소
    const PAYMENT_STATUS_FAILED = 'failed';         // 결제실패

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
     * 상태별 결제 목록을 확인합니다.
     *
     * @param string $status
     * @param int $page
     * @param int $limit
     * @return Result
     */
    public function getPayments($status = 'all', $page = 1, $limit = 20)
    {
        try {
            $response = $this->client->authRequest('GET', '/payments/status/' . $status . '?page=' . $page . '&limit=' . $limit);
            // TODO: 유닛테스트를 위한 임시 API
            return new Result(true, $response);
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
     * 비인증 결제요청을 미리 예약해두면 아임포트가 자동으로 해당 시간에 맞춰 결제를 진행하는 방식
     *
     * @param string $customerUid
     * @param array $schedules
     * @param int|null $checkingAmount
     * @param string|null $cardNumber
     * @param string|null $expiry
     * @param string|null $birth
     * @param string|null $pwd2Digit
     * @param string|null $pg
     * @return Result
     */
    public function subscribeSchedule($customerUid, $schedules, $checkingAmount = null, $cardNumber = null, $expiry = null, $birth = null, $pwd2Digit = null, $pg = null)
    {
        $params = [
            'customer_uid' => $customerUid,
            'schedules' => $schedules,
        ];
        if (!empty($checkingAmount)) {
            $params['checking_amount'] = $checkingAmount;
        }
        if (!empty($cardNumber)) {
            $params['card_number'] = $cardNumber;
        }
        if (!empty($expiry)) {
            $params['expiry'] = $expiry;
        }
        if (!empty($birth)) {
            $params['birth'] = $birth;
        }
        if (!empty($pwd2Digit)) {
            $params['pwd_2digit'] = $pwd2Digit;
        }
        if (!empty($pg)) {
            $params['pg'] = $pg;
        }

        try {
            $response = $this->client->authRequest('POST', '/subscribe/payments/schedule', $params);
            $schedules = Schedule::fromArray($response);
            return new Result(true, $schedules);
        } catch (Exception $e) {
            return new Result(false, null, $e);
        }
    }

    /**
     * 예약 거래주문번호(merchant_uid)로 결제예약정보를 조회
     *
     * @param string $mercahntUid
     * @return Result
     */
    public function getSubscribeSchedule($mercahntUid)
    {
        try {
            $response = $this->client->authRequest('GET', '/subscribe/payments/schedule/' . $mercahntUid);
            $schedule = new Schedule($response);
            return new Result(true, $schedule);
        } catch (Exception $e) {
            return new Result(false, null, $e);
        }
    }

    /**
     * @param string $customerUid
     * @param string[]|null $merchantUid
     * @return Result
     */
    public function subscribeUnschedule($customerUid, $merchantUid = null)
    {
        $params = [
            'customer_uid' => $customerUid,
        ];
        if (!empty($merchantUid)) {
            $params['merchant_uid'] = $merchantUid;
        }

        try {
            $response = $this->client->authRequest('POST', '/subscribe/payments/unschedule', $params);
            $schedules = Schedule::fromArray($response);
            return new Result(true, $schedules);
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

    public function getSubscribeCustomer($customerUid)
    {
        try {
            $response = $this->client->authRequest('GET', '/subscribe/customers/' . $customerUid);
            return new Result(true, $response);
        } catch (Exception $e) {
            return new Result(false, null, $e);
        }
    }

    public function deleteSubscribeCustomer($customerUid)
    {
        try {
            $response = $this->client->authRequest('DELETE', '/subscribe/customers/' . $customerUid);
            return new Result(true, $response);
        } catch (Exception $e) {
            return new Result(false, null, $e);
        }
    }
}

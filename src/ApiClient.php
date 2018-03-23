<?php
namespace Alliv\Iamport;

use Alliv\Iamport\Exceptions\AuthException;
use Alliv\Iamport\Exceptions\RequestException;
use Exception;

class ApiClient
{
    const BASE_URI = 'https://api.iamport.kr';

    private $apiKey;
    private $apiSecret;

    private $accessToken = null;
    private $accessTokenExpiredAt = null;

    public function __construct(array $config = [])
    {
        $this->apiKey = $config['apiKey'];
        $this->apiSecret = $config['apiSecret'];
    }

    /**
     * @param $method
     * @param $uri
     * @param array $params
     * @param array $headers
     * @return mixed
     * @throws AuthException
     * @throws Exception
     * @throws RequestException
     */
    public function authRequest($method, $uri, $params = [], $headers = [])
    {
        $accessToken = $this->getAccessToken();
        $headers[] = 'Authorization: ' . $accessToken;

        return $this->request($method, $uri, $params, $headers);
    }

    /**
     * @param $method
     * @param $uri
     * @param array $params
     * @param array $headers
     * @return mixed
     * @throws RequestException
     * @throws Exception
     */
    public function request($method, $uri, $params = [], $headers = [])
    {
        $method = strtoupper($method);

        $headers[] = 'Content-Type: application/json';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, static::BASE_URI . $uri);

        if ($method == 'GET') {
            curl_setopt($ch, CURLOPT_POST, false);
        } else if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            $postFields = json_encode($params);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

            $headers[] = 'Content-Length: ' . strlen($postFields);
        } else if ($method == 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $body = curl_exec($ch);
        $errorCode = curl_errno($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $result = json_decode(trim($body));
        curl_close($ch);

        if ($errorCode > 0) {
            throw new Exception('Request Error (HTTP Status: ' . $statusCode . ')', $errorCode);
        }
        if (empty($result)) {
            throw new Exception('API 서버로부터의 응답이 올바르지 않습니다.' . $body, 1);
        }
        if ($result->code !== 0) {
            throw new RequestException($result);
        }

        return $result->response;
    }

    /**
     * @return string
     * @throws AuthException
     */
    private function getAccessToken()
    {
        try {
            $now = time();
            if ($now < $this->accessTokenExpiredAt && !empty($this->accessToken)) {
                return $this->accessToken;
            }

            $this->accessToken = null;
            $this->accessTokenExpiredAt = null;

            $response = $this->request('POST', '/users/getToken', [
                'imp_key' => $this->apiKey,
                'imp_secret' => $this->apiSecret
            ]);

            $offset = $response->expired_at - $response->now;
            $this->accessTokenExpiredAt = time() + $offset;
            $this->accessToken = $response->access_token;

            return $response->access_token;
        } catch (Exception $e) {
            throw new AuthException('[API 인증 오류] ' . $e->getMessage(), $e->getCode());
        }
    }
}

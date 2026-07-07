<?

require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');

use PhpOffice\PhpSpreadsheet\IOFactory;

class TrendyolController {

    private $supplierId = '6697845';
    private $storeId    = '423452';
    private $apiKey     = '1Bm6xrnh3g4nQFjuq513';
    private $apiSecret  = 'loRt45ZZnziCAtIaDAc8';
    private $userAgent  = '6697845 - SelfIntegration';
    private $baseUrl    = 'https://api.tgoapis.com'; // PROD veya STAGE

    public function __construct($supplierId = null, $storeId = null, $apiKey = null, $apiSecret = null, $userAgent = null){
        if ($supplierId) $this->supplierId = $supplierId;
        if ($storeId)    $this->storeId    = $storeId;
        if ($apiKey)     $this->apiKey     = $apiKey;
        if ($apiSecret)  $this->apiSecret  = $apiSecret;
        if ($userAgent)  $this->userAgent  = $userAgent;
    }

    private function getRequest($endpoint, $extraHeaders = []){
        $url = $this->baseUrl . $endpoint;
        $token = base64_encode($this->apiKey . ':' . $this->apiSecret);

        $headers = array_merge([
            'User-Agent: ' . $this->userAgent,
            'Content-Type: application/json',
            'Authorization: Basic ' . $token
        ], $extraHeaders);

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER     => $headers
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return ['status' => $httpCode, 'response' => $response];
    }

    public function getUrunler($storeId = null){
        $storeId = $storeId ?? $this->storeId;
        $endpoint = "/integrator/product/meal/suppliers/{$this->supplierId}/stores/{$storeId}/products";
        return $this->getRequest($endpoint);
    }

    public function getSiparisler(){
        $endpoint = "/integrator/order/meal/suppliers/{$this->supplierId}/packages";
        return $this->getRequest($endpoint, ['x-agentname: Özgür','x-executor-user: ozgur11.htp@gmail.com']);
    }

    public function getTumSiparisler($size = 50){
        $page = 0;
        $tumSiparisler = array();

        do {
            $endpoint = "/integrator/order/meal/suppliers/{$this->supplierId}/packages?page={$page}&size={$size}";
            
            $result = $this->getRequest(
                $endpoint,
                [
                    'x-agentname: Özgür',
                    'x-executor-user: ozgur11.htp@gmail.com'
                ]
            );

            $data = json_decode($result['response']);

            if (!empty($data->content)) {
                $tumSiparisler = array_merge($tumSiparisler, $data->content);
            }

            $page++;
            $totalPages = $data->totalPages ?? 0;

        } while ($page < $totalPages);

        return $tumSiparisler;
    }

}
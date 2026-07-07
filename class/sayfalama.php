<?

class Sayfalama {
    private $gecerliSayfa;
    private $toplamVeri;
    private $sayfaBasinaVeri;
    private $url;

    public function __construct($toplamVeri, $sayfaBasinaVeri, $gecerliSayfa, $url) {
        $this->toplamVeri      = $toplamVeri;
        $this->sayfaBasinaVeri = $sayfaBasinaVeri;
        $this->gecerliSayfa    = max(1, $gecerliSayfa);
        $this->url             = $url;
    }

    public function getLimitOffset() {
        $offset = ($this->gecerliSayfa - 1) * $this->sayfaBasinaVeri;
        return " LIMIT $offset, $this->sayfaBasinaVeri";
    }

    public function sayfalamaOlustur() {
        $toplamSayfa = ceil($this->toplamVeri / $this->sayfaBasinaVeri);
        if ($toplamSayfa <= 1) return '';

        $sayfalama = '
        <nav aria-label="Sayfa navigasyonu">
            <ul class="pagination pagination-primary">';

        $sayfalama .= $this->ilkSayfa();
        $sayfalama .= $this->oncekiSayfa();
        $sayfalama .= $this->sayfaLinkleri($toplamSayfa);
        $sayfalama .= $this->sonrakiSayfa($toplamSayfa);
        $sayfalama .= $this->sonSayfa($toplamSayfa);
        $sayfalama .= '</ul></nav>';

        return $sayfalama;
    }

    private function ilkSayfa() {
        $engelli = $this->gecerliSayfa == 1 ? 'disabled' : '';
        return '<li class="page-item first ' . $engelli . '">
                    <a class="page-link waves-effect" href="' . $this->sayfaUrl(1) . '">
                        <i class="tf-icon ri-skip-back-mini-line ri-20px"></i>
                    </a>
                </li>';
    }

    private function oncekiSayfa() {
        $engelli = $this->gecerliSayfa == 1 ? 'disabled' : '';
        $onceki = max(1, $this->gecerliSayfa - 1);
        return '<li class="page-item prev ' . $engelli . '">
                    <a class="page-link waves-effect" href="' . $this->sayfaUrl($onceki) . '">
                        <i class="tf-icon ri-arrow-left-s-line ri-20px"></i>
                    </a>
                </li>';
    }
    
    private function sayfaLinkleri($toplamSayfa) {
        $linkler = '';
        $aralik = 2;
        $baslangic = max(1, $this->gecerliSayfa - $aralik);
        $bitis = min($toplamSayfa, $this->gecerliSayfa + $aralik);

        if ($baslangic > 1) {
            $linkler .= '<li class="page-item">
                            <a class="page-link waves-effect" href="' . $this->sayfaUrl(1) . '">1</a>
                         </li>';
            if ($baslangic > 2) {
                $linkler .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        for ($sayfa = $baslangic; $sayfa <= $bitis; $sayfa++) {
            $aktif = $this->gecerliSayfa == $sayfa ? 'active' : '';
            $linkler .= '<li class="page-item ' . $aktif . '">
                            <a class="page-link waves-effect" href="' . $this->sayfaUrl($sayfa) . '">' . $sayfa . '</a>
                        </li>';
        }

        if ($bitis < $toplamSayfa) {
            if ($bitis < $toplamSayfa - 1) {
                $linkler .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            $linkler .= '<li class="page-item">
                            <a class="page-link waves-effect" href="' . $this->sayfaUrl($toplamSayfa) . '">' . $toplamSayfa . '</a>
                        </li>';
        }

        return $linkler;
    }

    private function sonrakiSayfa($toplamSayfa) {
        $engelli = $this->gecerliSayfa == $toplamSayfa ? 'disabled' : '';
        $sonraki = min($toplamSayfa, $this->gecerliSayfa + 1);
        return '<li class="page-item next ' . $engelli . '">
                    <a class="page-link waves-effect" href="' . $this->sayfaUrl($sonraki) . '">
                        <i class="tf-icon ri-arrow-right-s-line ri-20px"></i>
                    </a>
                </li>';
    }

    private function sonSayfa($toplamSayfa) {
        $engelli = $this->gecerliSayfa == $toplamSayfa ? 'disabled' : '';
        return '<li class="page-item last ' . $engelli . '">
                    <a class="page-link waves-effect" href="' . $this->sayfaUrl($toplamSayfa) . '">
                        <i class="tf-icon ri-skip-forward-mini-line ri-20px"></i>
                    </a>
                </li>';
    }

    private function sayfaUrl($sayfa) {
        $parsedUrl = parse_url($this->url);
        parse_str($parsedUrl['query'] ?? '', $queryParams);
        $queryParams['page'] = $sayfa;
        return ($parsedUrl['path'] ?? '') . '?' . http_build_query($queryParams);
    }

    public function getPagination($toplamVeri, $sayfaBasinaVeri, $gecerliSayfa, $url) {
        $pagination = new Sayfalama($toplamVeri, $sayfaBasinaVeri, $gecerliSayfa, $url);
        return $pagination->sayfalamaOlustur();
    }

    public function getGorunumAraligi() {
        $baslangic = ($this->gecerliSayfa - 1) * $this->sayfaBasinaVeri + 1;
        $bitis = min($this->gecerliSayfa * $this->sayfaBasinaVeri, $this->toplamVeri);
        return "({$this->toplamVeri} Sonuç içinde $baslangic - $bitis arası sonuçlar)";
    }
}

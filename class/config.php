<?
	//error_reporting();
	error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
	@session_start();

	require_once($_SERVER['DOCUMENT_ROOT'] . '/class/functions.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/class/db.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/class/sorgu.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/class/theme.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/class/kayit.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/class/select.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/class/format.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/class/row.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/class/mail.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/class/resim.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/class/sayfalama.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/class/excel_sayfasi.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/class/resim_yol.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/class/UrunMaliyetService.php');

	require_once($_SERVER['DOCUMENT_ROOT'] . '/controllers/kullaniciController.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/controllers/menuController.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/controllers/urunController.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/controllers/subeController.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/controllers/kategoriController.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/controllers/bolgeController.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/controllers/kampanyaController.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/controllers/cariController.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/controllers/alerjenController.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/controllers/siteController.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/controllers/blogController.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/controllers/malzemeAlisController.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/controllers/raporController.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/controllers/siparisController.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/controllers/gelirGiderController.php');

	// Trendyol
	require_once($_SERVER['DOCUMENT_ROOT'] . '/controllers/trendyolController.php');


	$DB = new DB();
	define('HOST', '104.247.160.211');
	define('DB', 'ozgurhtr_ozgurwaffle');
	define('USER', 'ozgurhtr_ozgurwaffle');
	define('PASS', 'H@mL5eHK.M2bDzlw.');
	$cDB = $DB->baglan(HOST, DB, USER, PASS);

	$cSorgu = new Sorgu2();

	$row_site = $cSorgu->getSite();
	$row_kullanici = $cSorgu->getKullanici();
	$rows_anamenuler = $cSorgu->getAnaMenuler();
	$rows_menuler = $cSorgu->getMenuler();

	$cKayit 			= new Kayit2();
	$cSelect2 			= new Select2();
	$cMail 				= new Mail();
	$cResim 			= new ResimYukle();
	$cTheme 			= new Theme($row_site, $row_kullanici, $rows_anamenuler, $rows_menuler);
	$cResimYol 			= new ResimYol($row_site);

	$cKullanici 		= new KullaniciController($cSelect2, $row_site);
	$cMenu 				= new MenuController($cSelect2, $row_site);
	$cUrun 				= new UrunController($cSelect2, $row_site);
	$cSube 				= new SubeController($cSelect2, $row_site);
	$cKategori 			= new KategoriController($cSelect2, $row_site);
	$cBolge 			= new BolgeController($cSelect2, $row_site);
	$cKampanya 			= new KampanyaController($cSelect2, $row_site);
	$cCari 				= new CariController($cSelect2, $row_site);
	$cAlerjen 			= new AlerjenController($cSelect2, $row_site);
	$cBlog 				= new BlogController($cSelect2, $row_site);
	$cSite 				= new SiteController($cSelect2);
	$cMalzemeAlis 		= new MalzemeAlisController($cSelect2, $row_site);
	$cRapor 			= new RaporController($cSelect2, $row_site);
	$cSiparis 			= new SiparisController($cSelect2, $row_site);
	$cGelirGider 		= new GelirGiderController($cSelect2, $row_site);

	// Trendyol
	$cTrendyol 			= new TrendyolController();

	date_default_timezone_set('Europe/Istanbul');

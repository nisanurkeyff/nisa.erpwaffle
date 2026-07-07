<?
	require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');

	class Bootstrap{

		private $site;
		private $kullanici;
		private $anamenu;
		private $menu;

		function __construct($row_site = "", $row_kullanici = "", $row_anamenu = "", $row_menu = ""){
			$this->site 		= $row_site;
			$this->kullanici 	= $row_kullanici;
			$this->anamenu 		= $row_anamenu;
			$this->menu 		= $row_menu;
		}

		public function getHeader(){			
			if($_SESSION['kullanici_id'] > 0){
				?>
				<header class="page-header" role="banner">
	                <div class="page-logo">
	                    <a href="#" class="page-logo-link press-scale-down d-flex align-items-center position-relative" data-toggle="modal" data-target="#modal-shortcut">
	                        <img src="/admin/smartadmin/img/logo.png" alt="logo">
	                        <span class="page-logo-text mr-1"> <?=$this->site->BASLIK?> </span>
	                        <span class="position-absolute text-white opacity-50 small pos-top pos-right mr-2 mt-n2"></span>
	                        <i class="fal fa-angle-down d-inline-block ml-1 fs-lg color-primary-300"></i>
	                    </a>
	                </div>

	                <div class="hidden-md-down dropdown-icon-menu position-relative">
	                    <a href="#" class="header-btn btn js-waves-off" data-action="toggle" data-class="nav-function-hidden" title="Yönlendirme"> <i class="ni ni-menu"></i> </a>
	                    <ul>
	                        <li> <a href="#" class="btn js-waves-off" data-action="toggle" data-class="nav-function-minify" title="Minify Navigation"> <i class="ni ni-minify-nav"></i> </a> </li>
	                        <li> <a href="#" class="btn js-waves-off" data-action="toggle" data-class="nav-function-fixed" title="Lock Navigation"> <i class="ni ni-lock-nav"></i> </a> </li>
	                    </ul>
	                </div>
	                <div class="hidden-lg-up">
	                    <a href="#" class="header-btn btn press-scale-down" data-action="toggle" data-class="mobile-nav-on"> <i class="ni ni-menu"></i> </a>
	                </div>
	                <div class="search" style="flex: none;">
	                    <form name="formSearch" id="formSearch" class="app-forms hidden-md-down" role="search" action="/talep/hasar_dosya_takip.do?route=talep/hasar_dosya_takip&filtre=1" autocomplete="off">
	                    	<div class="input-group" style="width: 250px">
	                    		<input type="hidden" name="route" value="talep/hasar_dosya_takip"/>
	                    		<input type="hidden" name="filtre" value="1"/>
	                    		<input type="text" name="arama_q" id="arama_q" value="<?=$_REQUEST['arama_q']?>" placeholder="Dosya No" class="form-control border-right-0" tabindex="1" >
	                    		<div class="input-group-append"><span class="input-group-text btn bg-transparent"><i class="fal fa-search" onclick="$('#formSearch').submit()"></i></span></div>
	                    		<a href="#" onclick="return false;" class="btn-danger btn-search-close js-waves-off d-none" data-action="toggle" data-class="mobile-search-on"><i class="fal fa-times"></i> </a>
	                    	</div>
	                    </form>
	                </div>	                
	                <div class="ml-auto d-flex">
	                    <div>
	                        <a href="#" data-toggle="dropdown" class="header-icon d-flex align-items-center justify-content-center ml-2">
	                        	<img src="/admin/img/user.jpg" class="profile-image rounded-circle" alt="">
	                        </a>
	                        <div class="dropdown-menu dropdown-menu-animated dropdown-lg">
	                            <div class="dropdown-header bg-trans-gradient d-flex flex-row py-4 rounded-top">
	                                <div class="d-flex flex-row align-items-center mt-1 mb-1 color-white">
	                                    <span class="mr-2">
		                                    <img src="/admin/img/user.jpg" class="rounded-circle profile-image" alt="">
	                                    </span>
	                                    <div class="info-card-text">
	                                        <div class="fs-lg text-truncate text-truncate-lg"><?=$this->kullanici->UNVAN?></div>
	                                        <span class="text-truncate text-truncate-md opacity-80"><?=$this->kullanici->YETKI?></span>
	                                    </div>
	                                </div>
	                            </div>
	                            <div class="dropdown-divider m-0"></div>
	                            <a href="/admin/kullanici/hesabim.php" class="dropdown-item">
	                                <span data-i18n="drpdwn.settings">Kullanıcı Ayarları</span>
	                            </a>
	                            <div class="dropdown-divider m-0"></div>
	                            <a class="dropdown-item fw-500 pt-3 pb-3" href="/admin/giris.php">
	                                <span> Çıkış </span>
	                            </a>
	                        </div>
	                    </div>
	                </div>
	            </header>
				<?
			}
		}

		public function getMenu(){						
			if($_SESSION['kullanici_id'] > 0) {
				?>
				<aside class="page-sidebar">
                    <div class="page-logo" style="height: 115px;padding-left: 25px; padding-top: 20px; background-color: #ffffff; background-image: none;">
                        <a href="<?=$this->kullanici->INDEX_URL?>" class="page-logo-link press-scale-down align-items-center position-relative text-center">
			        		<img src="<?=fncImgPath($this->site->LOGO)?>" alt="logo" aria-roledescription="logo" style="height: 150px; border-radius: 100px;">
                            <span class="position-absolute text-white opacity-50 small pos-top pos-right mr-2 mt-n2"></span>
                        </a>
                    </div>
                    <nav id="js-primary-nav" class="primary-nav" role="navigation">
                        <div class="nav-filter">
                            <div class="position-relative">
                                <input type="text" id="nav_filter_input" placeholder="Filter menu" class="form-control" tabindex="0">
                                <a href="#" onclick="return false;" class="btn-primary btn-search-close js-waves-off" data-action="toggle" data-class="list-filter-active" data-target=".page-sidebar">
                                    <i class="fal fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>
                        <div class="info-card px-2">
                            <img src="/admin/img/user.jpg" class="profile-image rounded-circle" alt="">
                            <div class="info-card-text">                                
                                <span class="text-truncate text-truncate-md d-inline-block"><?=$this->kullanici->UNVAN?></span><br>
                                <span class="d-inline-block text-truncate text-truncate-sm"><?=$this->kullanici->YETKI?></span>
                            </div>
                            <img src="/admin/img/kart.png" class="cover" alt="cover">
                            <a href="#" onclick="return false;" class="pull-trigger-btn" data-action="toggle" data-class="list-filter-active" data-target=".page-sidebar" data-focus="nav_filter_input">
                                <i class="fal fa-angle-down"></i>
                            </a>
                        </div>
                        <ul id="js-nav-menu" class="nav-menu">
                           <?foreach($this->anamenu as $key => $row_anamenu){
                           		$row_anamenu->CLASS = str_replace('fa fa', 'fal fa', $row_anamenu->CLASS);
                           		?>
						        <?if(count2($this->menu[$row_anamenu->ROUTE]) > 0){?>
						        <li class="<?=routeActive($row_anamenu->ROUTE)?>">
						          	<a href="#"  class="waves-effect waves-themed text-white">
							            <i class="<?=$row_anamenu->CLASS?> <?=$this->getTextColor()?>"></i>
							            <span class="nav-link-text" data-i18n="nav.application_intel"> <?=$row_anamenu->ANAMENU?> </span>
						          	</a>
						          	<ul>
						          	<?foreach($this->menu[$row_anamenu->ROUTE] as $key2 => $row){?>
							            <li class="<?=routeActive($row->ROUTE)?> text-white" title="<?=$row->TITLE?>"><a href="<?='/admin' . $row->LINK?>" data-filter-tags="<?=$row->FILTRE?>" style="padding-left: 30px;"><i class="fal"></i> <?=$row->MENU?> </a></li>
						          	<?}?>
						          	</ul>
						        </li>
					        	<?}?>
					        <?}?>
                        </ul>
                        <div class="filter-message js-filter-message bg-success-600"></div>
                    </nav>
                </aside>
		  	<?}
		}

		public function getHeaderPopup(){
			?>			
			<header class="page-header" role="banner">
				<a href="/index.do" class="logo">
			  		<span class="logo-mini"><b><?=$this->rSite->BASLIK?></b></span>
				</a>
            </header>
            <?
		}

		public function getFooter(){
			?>
			<footer class="page-footer" role="contentinfo">
                <div class="d-flex align-items-center flex-1 text-muted">
                    <span class="hidden-md-down fw-700"> <?=$this->rSite->ALTYAZI?> </span>&nbsp;
                </div>
                <div class="float-right hidden-xs"> <span class="js-get-date"></span> | <b>Version</b> 2.0.0 </div>
            </footer>
		  	<?
		}

		public function getTemaCss(){
			?>
			<link id="mytheme" rel="stylesheet" href="../smartadmin/css/themes/cust-theme-10.css"/>
			<link rel="icon" type="image/png" sizes="32x32" href="../img/favicon20.png"/>
			<link rel="stylesheet" href="../smartadmin/plugin/1.css?<?=CSSVERSION?>"/>
			<?	
		}

		public function getTemaCssIndex(){
			?>
			<link id="mytheme" rel="stylesheet" href="./smartadmin/css/themes/cust-theme-10.css"/>
			<link rel="icon" type="image/png" sizes="32x32" href="./img/favicon20.png"/>
			<link rel="stylesheet" href="./smartadmin/plugin/1.css?<?=CSSVERSION?>"/>
			<?	
		}		

		public function getFontBoyut(){
			return $this->kullanici->FONT_BOYUT_CLASS;
		}

		public function getBody(){ //nav-function-fixed
			switch($this->kullanici->MENU_ID){
				case 1 : $menu = "nav-function-fixed"; break;
				case 2 : $menu = "nav-function-minify"; break;
				case 3 : $menu = "nav-function-hidden"; break;
				default : $menu = "nav-function-fixed"; break;
			}
			return "mod-bg-1 $menu";
		}


		public function getTema(){
			if(is_null($this->kullanici->ID)){
				return "hold-transition fixed skin-yellow sidebar-collapse sidebar-collapse";
			} else {
				return "hold-transition fixed " . $this->kullanici->TEMA . ($_COOKIE['menu']=="0" ? " sidebar-collapse" : ""); // sidebar-collapse sidebar-mini skin-green fixed layout-boxed control-sidebar-open
			}
		}

		public function getTextColor(){
			return "text-white";
			return $this->kullanici->TEXT_COLOR;

		}

		public function getTemaArkaPlan(){
			return $this->kullanici->ARKA_PLAN;

		}

	}

?>
<?
	require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');

	class Theme{

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

		public function Header(){
			if($_SESSION['kullanici_id'] > 0){
				?>
				<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
				    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
				        <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)"><i class="ri-menu-fill ri-22px"></i></a>
				    </div>
				    <div class="navbar-nav-right d-flex align-items-center" id="arama">
				        <div class="navbar-nav align-items-center">
				            <div class="nav-item navbar-search-wrapper mb-0">
				                <a class="nav-item nav-link search-toggler fw-normal px-0" href="javascript:void(0);">
					                <i class="ri-search-line ri-22px scaleX-n1-rtl me-3"></i>
					                <span class="d-none d-md-inline-block text-muted">Arama (Ctrl+/)</span>
				                </a>
				            </div>
				        </div>
				        <ul class="navbar-nav flex-row align-items-center ms-auto">
				        	<li class="nav-item dropdown-language dropdown me-sm-2 me-xl-0">
                        		<a href="javascript:history.back()" class="nav-link btn btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow" style="cursor: pointer" title="Geri"><i class="ri-22px ri-arrow-left-line"></i></a>
							</li>
				            <li class="nav-item dropdown-style-switcher dropdown me-1 me-xl-0">
				                <a class="nav-link btn btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown"><i class="ri-22px"></i></a>
				                <ul class="dropdown-menu dropdown-menu-end dropdown-styles">
				                    <li>
				                        <a class="dropdown-item" href="javascript:void(0);" data-theme="light"><span class="align-middle"><i class="ri-sun-line ri-22px me-3"></i>Light</span></a>
				                    </li>
				                    <li>
				                        <a class="dropdown-item" href="javascript:void(0);" data-theme="dark"><span class="align-middle"><i class="ri-moon-clear-line ri-22px me-3"></i>Dark</span></a>
				                    </li>
				                </ul>
				            </li>
				            <li class="nav-item navbar-dropdown dropdown-user dropdown">
				                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
				                    <div class="avatar avatar-online">
				                        <img src="<?=$this->kullanici->AVATAR?>" alt class="rounded-circle" />
				                    </div>
				                </a>
				                <ul class="dropdown-menu dropdown-menu-end">
				                    <li>
				                        <div class="dropdown-item">
				                            <div class="d-flex">
				                                <div class="flex-shrink-0 me-2">
				                                    <div class="avatar avatar-online">
				                                        <img src="<?=$this->kullanici->AVATAR?>" alt class="rounded-circle" />
				                                    </div>
				                                </div>
				                                <div class="flex-grow-1">
				                                    <span class="fw-medium d-block small"><?=$this->kullanici->AD?> <?=$this->kullanici->SOYAD?></span>
				                                    <small class="text-muted"><?=$this->kullanici->YETKI?></small>
				                                </div>
				                            </div>
				                        </div>
				                    </li>
				                    <li>
				                        <div class="dropdown-divider"></div>
				                    </li>
				                    <li>
				                        <a class="dropdown-item" href="/views/kullanici/duzenle.php?id=<?=$this->kullanici->ID?>}&token=<?=$this->kullanici->TOKEN?>"><i class="ri-user-3-line ri-22px me-3"></i><span class="align-middle">Hesabım</span></a>
				                    </li>
				                    <li>
				                        <div class="dropdown-divider"></div>
				                    </li>
				                    <li>
				                        <div class="d-grid px-4 pt-2 pb-1">
				                            <a class="btn btn-sm btn-danger d-flex" href="/views/giris.php"><small class="align-middle">Çıkış Yap</small><i class="ri-logout-box-r-line ms-2 ri-16px"></i></a>
				                        </div>
				                    </li>
				                </ul>
				            </li>
				        </ul>
				    </div>
				    <div class="navbar-search-wrapper search-input-wrapper d-none">
				        <input type="text" class="form-control search-input container-xxl border-0" placeholder="Arama..." aria-label="Arama..." />
				        <i class="ri-close-fill search-toggler cursor-pointer"></i>
				    </div>
				</nav>
				<?
			}
		}
		
		public function isActiveMenu($link) {
		    return strpos($_REQUEST['route'], $link) !== false ? 'active' : '';
		}

		public function isOpenMenu($submenu) {
		    foreach ($submenu as $row_menu) {
		        if ($_REQUEST['route'] === $row_menu->ROUTE) {
		            return 'open';
		        }
		    }
		    return '';
		}

		public function Menu() {
		?>
		    <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
		        <div class="app-brand demo justify-content-center" style="height: 120px !important;">
		            <a href="/views<?=$this->kullanici->INDEX_URL?>" class="app-brand-link">
		            	<?if($this->site->ID > 0){?>
		            		<img src="<?=$this->site->LOGO?>" class="auth-cover-illustration w-100" alt="Menü Yönetim" height="100" width="100" style="border-radius: 15px;"/>
		            	<?}else{?>
		            		<img src="/img/giris.png" class="auth-cover-illustration w-100" alt="Menü Yönetim" height="100" width="100"/>
		            	<?}?>
		            </a>
		        </div>
		        <div class="menu-inner-shadow"></div>
		        <ul class="menu-inner py-1">
		            <?foreach($this->anamenu as $key => $row_anamenu){ 
		                if (isset($this->menu[$row_anamenu->ROUTE]) AND !empty($this->menu[$row_anamenu->ROUTE])){?>
		                    <li class="menu-item <?= $this->isOpenMenu($this->menu[$row_anamenu->ROUTE]) ?>">
		                        <a href="javascript:void(0);" class="menu-link menu-toggle">
		                            <i class="menu-icon tf-icons <?=$row_anamenu->ICON?>"></i>
		                            <div data-i18n="<?=$row_anamenu->ANAMENU?>"><?=$row_anamenu->ANAMENU?></div>
		                        </a>
		                        <ul class="menu-sub">
		                            <?foreach($this->menu[$row_anamenu->ROUTE] as $key2 => $row_menu){?>
		                                <li class="menu-item <?=$this->isActiveMenu($row_menu->ROUTE)?>">
		                                    <a href="<?=$row_menu->LINK?>" class="menu-link">
		                                        <div data-i18n="<?=$row_menu->MENU?>"><?=$row_menu->MENU?></div>
		                                    </a>
		                                </li>
		                            <?}?>
		                        </ul>
		                    </li>
		                <?}?>
		            <?}?>
		        </ul>
		    </aside>
		<?
		}

		public function Footer(){
			?>
			<footer class="content-footer footer bg-footer-theme">
			    <div class="container-xxl">
			        <div
			            class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
			            <div class="text-body mb-2 mb-md-0">
			                © 2025 <a href="https://sharksbites.com/" target="_blank" class="footer-link">Sharks Bite Soft</a>
			            </div>
			            <div class="d-none d-lg-inline-block">
			            </div>
			        </div>
			    </div>
			</footer>
		  	<?
		}

		public function Linkler(){
			?>
			<link rel="icon" type="image/x-icon" href="<?=$this->site->FAVICON?>" />
			<link rel="preconnect" href="https://fonts.googleapis.com" />
			<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
			<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap" rel="stylesheet" />
			<link rel="stylesheet" href="/assets/vendor/fonts/remixicon/remixicon.css" />
			<link rel="stylesheet" href="/assets/vendor/fonts/flag-icons.css" />
			<link rel="stylesheet" href="/assets/vendor/libs/node-waves/node-waves.css" />
			<link rel="stylesheet" href="/assets/vendor/css/rtl/core.css" class="template-customizer-core-css" />
			<link rel="stylesheet" href="/assets/vendor/css/rtl/theme-default.css" class="template-customizer-theme-css" />
			<link rel="stylesheet" href="/assets/css/demo.css" />
			<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css" />
			<link rel="stylesheet" href="/assets/css/notyf.min.css" />
			<link rel="stylesheet" href="/assets/css/jquery-ui.css" />
			<link rel="stylesheet" href="/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
			<link rel="stylesheet" href="/assets/vendor/libs/typeahead-js/typeahead.css" />
			<link rel="stylesheet" href="/assets/vendor/libs/bootstrap-maxlength/bootstrap-maxlength.css" />
			<link rel="stylesheet" href="/assets/vendor/libs/apex-charts/apex-charts.css" />
			<link rel="stylesheet" href="/assets/vendor/libs/swiper/swiper.css" />
			<link rel="stylesheet" href="/assets/vendor/css/pages/cards-statistics.css" />
			<link rel="stylesheet" href="/assets/vendor/css/pages/cards-analytics.css" />
			<link rel="stylesheet" href="/assets/vendor/css/pages/page-auth.css" />
		    <link rel="stylesheet" href="/assets/vendor/libs/flatpickr/flatpickr.css" />
		    <link rel="stylesheet" href="/assets/vendor/libs/select2/select2.css" />
		    <link rel="stylesheet" href="/assets/vendor/libs/tagify/tagify.css" />
		    <link rel="stylesheet" href="/assets/vendor/libs/@form-validation/form-validation.css" />
		    <link rel="stylesheet" href="/assets/vendor/libs/animate-css/animate.css" />
		    <link rel="stylesheet" href="/assets/vendor/libs/sweetalert2/sweetalert2.css" />
	    	<link rel="stylesheet" href="/assets/vendor/libs/bs-stepper/bs-stepper.css" />
	    	<link rel="stylesheet" href="/assets/vendor/libs/bootstrap-select/bootstrap-select.css" />
	    	<link rel="stylesheet" href="/assets/vendor/libs/toastr/toastr.css" />
	    	<link rel="stylesheet" href="/assets/vendor/css/pages/page-misc.css" />
    		<link rel="stylesheet" href="/assets/vendor/libs/jkanban/jkanban.css" />
    		<link rel="stylesheet" href="/assets/vendor/libs/quill/typography.css" />
    		<link rel="stylesheet" href="/assets/vendor/libs/quill/katex.css" />
    		<link rel="stylesheet" href="/assets/vendor/libs/quill/editor.css" />
    		<link rel="stylesheet" href="/assets/vendor/css/pages/app-kanban.css" />
    		<link rel="stylesheet" href="/assets/vendor/css/pages/app-calendar.css" />
		    <link rel="stylesheet" href="/assets/vendor/libs/fullcalendar/fullcalendar.css" />
		    <link rel="stylesheet" href="/assets/vendor/libs/dropzone/dropzone.css" />
		    <link rel="stylesheet" href="/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css" />
		    <link rel="stylesheet" href="/assets/vendor/libs/spinkit/spinkit.css" />
		    <link rel="stylesheet" href="/assets/vendor/libs/spinkit/spinkit.css" />

		    <script src="/assets/vendor/js/helpers.js"></script>
		    <script src="/assets/vendor/js/template-customizer.js"></script>
		    <script src="/assets/js/config.js"></script>
			<?
		}

		public function Scriptler(){
		    ?>

		    <script src="/assets/vendor/libs/jquery/jquery.js"></script>
		    <script src="/assets/vendor/libs/popper/popper.js"></script>
		    <script src="/assets/vendor/js/bootstrap.js"></script>
		    <script src="/assets/vendor/libs/node-waves/node-waves.js"></script>
		    <script src="/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
		    <script src="/assets/vendor/libs/hammer/hammer.js"></script>
		    <script src="/assets/vendor/libs/i18n/i18n.js"></script>
		    <script src="/assets/vendor/libs/typeahead-js/typeahead.js"></script>
		    <script src="/assets/vendor/js/menu.js"></script>
		    <script src="/assets/vendor/libs/cleavejs/cleave.js"></script>
		    <script src="/assets/vendor/libs/cleavejs/cleave-phone.js"></script>
		    <script src="/assets/vendor/libs/autosize/autosize.js"></script>
		    <script src="/assets/vendor/libs/bootstrap-maxlength/bootstrap-maxlength.js"></script>
		    <script src="/assets/vendor/libs/jquery-repeater/jquery-repeater.js"></script>
		    <script src="/assets/vendor/libs/apex-charts/apexcharts.js"></script>
		    <script src="/assets/vendor/libs/swiper/swiper.js"></script>
		    <script src="/assets/vendor/libs/moment/moment.js"></script>
		    <script src="/assets/vendor/libs/flatpickr/flatpickr.js"></script>
		    <script src="/assets/vendor/libs/select2/select2.js"></script>
		    <script src="/assets/vendor/libs/sweetalert2/sweetalert2.js"></script>
		    <script src="/assets/vendor/libs/bootstrap-select/bootstrap-select.js"></script>
    		<script src="/assets/vendor/libs/toastr/toastr.js"></script>
		    <script src="/assets/vendor/libs/bs-stepper/bs-stepper.js"></script>
		    <script src="/assets/vendor/libs/@form-validation/popular.js"></script>
		    <script src="/assets/vendor/libs/@form-validation/bootstrap5.js"></script>
		    <script src="/assets/vendor/libs/@form-validation/auto-focus.js"></script>
		    <script src="/assets/vendor/libs/jkanban/jkanban.js"></script>
		    <script src="/assets/vendor/libs/quill/katex.js"></script>
		    <script src="/assets/vendor/libs/quill/quill.js"></script>
		    <script src="/assets/vendor/libs/tagify/tagify.js"></script>
		    <script src="/assets/vendor/libs/bloodhound/bloodhound.js"></script>
		    <script src="/assets/vendor/libs/fullcalendar/fullcalendar.js"></script>
		    <script src="/assets/vendor/libs/chartjs/chartjs.js"></script>
		    <script src="/assets/vendor/libs/dropzone/dropzone.js"></script>
		    <script src="/assets/vendor/libs/block-ui/block-ui.js"></script>
		    <script src="/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js"></script>

		    <script src="/assets/js/main.js"></script>
		    <script src="/assets/js/form-layouts.js"></script>
		    <script src="/assets/js/form-wizard-icons.js"></script>
    		<script src="/assets/js/ui-toasts.js"></script>
		    <script src="/assets/js/modal-edit-user.js"></script>
		    <script src="/assets/js/forms-extras.js"></script>
		    <script src="/assets/js/forms-selects.js"></script>
		    <script src="/assets/js/notyf.min.js"></script>
		    <script src="/assets/js/forms-file-upload.js"></script>
		    <script src="/assets/js/input-mask.js"></script>
		    <script src="/assets/js/jquery-ui.min.js"></script>
		    <script src="/assets/js/fancybox.umd.js"></script>
		    <script src="/assets/js/jspdf.umd.min.js"></script>

		    <script src="/assets/js/script.js"></script>
		    <?
		}
	}

?>
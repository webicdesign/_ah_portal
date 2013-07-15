<?php
/*
 * @author atabak.h@gmail.com
 * ah_framework
 * Copyright 2010-2013 gbl group
 * atabak hosein nia production
 * supported by webicdesign.net
 * support@webicdesign.net
 * login class
 * no direct access
 */

class _ah_login
{
	# database class object link
	private $link;

	# user name from post or ajax
	private $user_name;

	# password from post or ajax
	private $user_pass;

	# user id from query
	private $user_id;

	# user first name from query
	private $user_first;

	# last name from query
	private $user_last;

	# user table name
	private $user_table 		= '_ah_portal_users';

	# save pass table name
	private $save_pass_table 	= '_ah_portal_save_pass';

	# user id field
	private $user_id_field 		= '_user_id';

	# user name field
	private $user_name_field 	= '_user_name';

	# password field
	private $password_field 	= '_user_pass';

	# active field
	private $active_field 		= '_active';

	# block field
	private $block_field 		= '_block';

	# active field
	private $melli_code_field 	= '_melli_code';

	# block field
	private $email_field 		= '_email';

	# error
	private $error;

	# login try
	private $login_try 			= 5;


	# autoload class function
	function _ah_login ($request = 'normal_login' , $user = NULL, $pass = NULL) {

		# login try total
		if(isset($_SESSION["_login_try_count"]) && $_SESSION["_login_try_count"] >= $this->login_try ) {

			_block_list_add ('many try');

			# redirect to 404 page
			_ah_404('blocked user');

		}

		# count not enoph
		else {
			// check request

			# normal request
			if ($request == 'normal_login') {

				# submit login form
				if (isset($_POST['user']) && isset($_POST['pass'])) {

					# define internal value
					$this->user_name = $_POST['user'];
					$this->user_pass = $_POST['pass'];

					# check user and pass
					if (self::_query()) {

						# set loggined user session
						self::_set_session();

						# redirect to current adress
						_ah_reload();
					}

					# show login form for wrong user and pass
					else {
						self::_normal_login_form($this->error);
					}
				}
				# submit register form
				if (isset($_POST['email']) && isset($_POST['passi'])) {

					# register proccess
					self::_register();
				}
				# only form
				else {
					self::_normal_login_form();
				}
			}

			# ajax request
			if ($request == 'ajax_login') {
				# submit form
				if (isset($user) && isset($pass)) {

					# define internal value
					$this->user_name = $user;
					$this->user_pass = $pass;

					# check user and pass
					if (self::_query()) {

						# set loggined user session
						self::_set_session();

						# return true for login
						return TRUE;
					}

					# show login form for wrong user and pass
					else {
						self::_ajax_login_form($this->error);
					}
				}

				# only form
				else {
					self::_ajax_login_form();
				}
			}

			# logout request
			elseif ($request == 'logout') {

				# logout
				self::_logout();

				# redirect to first page
				_ah_rdr(_portal_first_page());

			}

			# activation email
			elseif ($request == 'activation') {

				# activation page
				//self::_logout();

				# redirect to user first page
				_ah_rdr(_user_area_adress());

			}

			# reset password
			elseif ($request == 'reset_pass') {

				# reset pass page
				//self::_logout();

				# redirect to user first page
				//_ah_rdr(_user_area_adress());

			}

		}
	}

	# reset password page
	private function _reset_pass_request() {

		# check code again
	}

	# register new user [use post]
	private function _register () {

		# create database object
		self::_connect();

		# check for user name and email adress and mellicode
		$res = $this->link->result(
			array('*'),
			$this->user_table,
			array(
				array('',$this->user_name_field,'=','','OR'),
				array('',$this->email_field,'=','','OR'),
				array('',$this->melli_code_field,'=','','')
			)
		);

		# if information not duplicate
		if (!$res) {

			$uiid = $this->link->insert(
						$this->user_table,
						array('','','','','','','','','',''),
						array(
								array('','','','','','','','','','')
						)
					);

			# save user
			if ($uiid) {

				# send activation email
				$activation_code = _ah_hash($uiid,'_asportal_activation_code');
				// email(email = 'email_adress', type="active account", activation_code = $activation_code);

				# activation message
				$this->error = 'کاربری شما با موفقیت ثبت شد، برای استفاده از امکانات تارنما و فعال کردن حساب کاربری، لطفا به ایمیل خود مراجعه کرده و از طریق لینک فعال سازی اقدام کنید ';

				#
			}

			# not save [have error]
			else {

				# error message
				$this->error = 'خطایی در ثبت اطلاعات رخ داده ، لطفا لحظاتی دیگر مجدد اقدام کنید';
				return FALSE;
			}
		}

		# if information blong to other
		else {

			# message
			$this->error = 'برخی مشخصات تکراری است';
			# border change

			# reset password

			return FALSE;
		}

		# kill database object
		self::_disconnect();

	}

	# logiut user and set to guest
	private function _logout () {

		# delete user session
		$_SESSION['_ah_user'] 	= NULL;
		$_SESSION['_ah_first'] 	= NULL;
		$_SESSION['_ah_last'] 	= NULL;
		unset($_SESSION['_ah_user'], $_SESSION['_ah_first'], $_SESSION['_ah_last']);

		# define guest session
		$_SESSION['_ah_guest'] 	= 1;

		# regenerate session
		session_regenerate_id();

		return TRUE;
	}

	# set session for login user
	private function _set_session () {

		# delete guest session
		$_SESSION['ah_guest'] = NULL;
		unset($_SESSION['ah_guest']);

		# regenerate session
		session_regenerate_id();

		$_SESSION['_ah_user'] 	= $this->user_id;
		$_SESSION['_ah_first'] 	= $this->user_first;
		$_SESSION['_ah_last'] 	= $this->user_last;

		return TRUE;

	}

	# login form for ajax request
	private function _ajax_login_form () {

	}

	# login form show for normal login
	private function _normal_login_form ($error = NULL) {
		//include AH_CLASS.'_html.php';
		$_ah_html = new _ah_html();
		?>
		<!DOCTYPE html>
		<html lang="fa">
		    <head>
		        <meta charset="utf-8">
		        <title>ورود کاربران</title>
		        <meta name="viewport" content="width=device-width, initial-scale=1.0">
		        <meta name="description" content="user login page">
		        <meta name="author" content="user login page">
		        <!--[if lt IE 9]><script src="/inc/js/html5.js"></script><![endif]-->
		        <?php
		        echo $_ah_html->_js(array('js/jquery.js','js/migrate.js','js/plugin/supersized.3.2.7.min.js','js/default.js'));
		        echo $_ah_html->_css('/css/default.css');
		        ?>
		    </head>
		    <?php
		    echo $_ah_html->_css_compress('
					body{line-height:1;background:#111;height:100%;font-family:tahoma;text-align:center;color:#fff}
					.lf{width:320px;height:240px;display:none}
					form{position:relative;width:305px;margin:0 auto 0 auto;text-align:center}
					input[type=text],input[type=password],textarea{width:274px;height:42px;margin-top:10px;padding:0 15px;background:#2d2d2d;background:rgba(45,45,45,.15);border:1px solid #3d3d3d;border:1px solid rgba(255,255,255,.15);-moz-box-shadow:0 2px 3px 0 rgba(0,0,0,.1) inset;-webkit-box-shadow:0 2px 3px 0 rgba(0,0,0,.1) inset;box-shadow:0 2px 3px 0 rgba(0,0,0,.1) inset;font:12px tahoma;color:#fff;text-shadow:0 1px 2px rgba(0,0,0,.1);-o-transition:all .2s;-moz-transition:all .2s;-webkit-transition:all .2s;-ms-transition:all .2s}
					.error{display:none;position:absolute;top:27px;right:-55px;width:40px;height:40px;background:#2d2d2d;background:rgba(45,45,45,.25);-moz-border-radius:8px;-webkit-border-radius:8px;border-radius:8px}
					.error span{display:inline-block;margin:-3px 6px;font-size:40px;font-weight:700;text-shadow:0 1px 2px rgba(255,255,255,.5);-o-transform:rotate(45deg);-moz-transform:rotate(45deg);-webkit-transform:rotate(45deg);-ms-transform:rotate(45deg)}
					.connect{width:305px;margin:35px auto 0 auto;font-size:18px;font-weight:700;text-shadow:0 1px 3px rgba(0,0,0,.2)}
					.connect a{display:inline-block;width:32px;height:35px;margin-top:15px;-o-transition:all .2s;-moz-transition:all .2s;-webkit-transition:all .2s;-ms-transition:all .2s}
					.connect a:hover{background-position:center bottom}
					#supersized-loader{position:absolute;top:50%;left:50%;z-index:0;width:60px;height:60px;margin:-30px 0 0 -30px;text-indent:-999em;background:url(/inc/login/progress.gif) no-repeat center center;}
					#supersized{ display:block;position:fixed;left:0;top:0;overflow:hidden;z-index:-999;height:100%;width:100%}
					#supersized img{width:auto;height:auto;position:relative;display:none;outline:none;border:none}
					#supersized.speed img{-ms-interpolation-mode:nearest-neighbor;image-rendering:-moz-crisp-edges}
					#supersized.quality img{-ms-interpolation-mode:bicubic;image-rendering:optimizeQuality}
					#supersized li{display:block;list-style:none;z-index:-30;position:fixed;overflow:hidden;top:0;left:0;width:100%;height:100%;background:#111}
					#supersized a{width:100%;height:100%;display:block}
					#supersized li.prevslide{z-index:-20}
					#supersized li.activeslide{z-index:-10}
					#supersized li.image-loading{background:#111 url(/inc/img/progress.gif) no-repeat center center;width:100%;height:100%}
					#supersized li.image-loading img{visibility:hidden}
					#supersized li.prevslide img, #supersized li.activeslide img{display:inline}
					#supersized img{max-width:none !important}
					#ufp{color:#ffffff;margin-top:5px;text-shadow: 0px 2px 1px rgba(150, 150, 150, 0.59);}
					#ufp:HOVER{color:#bdc3c7;text-shadow: 0px 2px 1px rgba(0, 0, 0, 0.59)}
		    	');
			$_ah_html = NULL;
		    ?>
		    <script>
		    jQuery(document).ready(function(){
		    	$("#register").hide();
		    	$("#forget").hide();
		    	$(".lf").center();
		    	$(".lf").fadeTo(1000,1);
		        $('.lf form').submit(function(){
		            var username=$('#user').val();
		            var password=$('#pass').val();
		            if(username==''){
		                $(this).find(".error").fadeOut("fast",function(){$(this).css("top","27px")});
		                $(this).find(".error").fadeIn("fast",function(){$(this).parent().find(".username").focus()});
		                return false;
		            }
		            if(password==''){
		                $(this).find(".error").fadeOut("fast",function(){$(this).css("top","96px")});
		                $(this).find(".error").fadeIn("fast",function(){$(this).parent().find(".password").focus()});
		                return false;
		            }
		        });
		        $('.lf form .username, .lf form .password').keyup(function(){$(this).parent().find('.error').fadeOut('fast')})
		        $("#rbtn").click(function(){
			        $("#login").fadeTo(200,0,function(){$("#login").hide(function(){$(".lf").css({height:'640px'});$(".lf").center();$("#register").fadeTo(500,1)})});
			    });
		        $("#bbtn").click(function(){
		        	$("#register").fadeTo(200,0,function(){$("#register").hide(function(){$(".lf").css({height:'240px'});$(".lf").center();$("#login").fadeTo(500,1)})});
			    });
			    $("#ufp").click(function(){
			    	$("#login").fadeTo(200,0,function(){$("#login").hide(function(){$(".lf").css({height:'120px'});$(".lf").center();$("#forget").fadeTo(500,1)})});
				});
				$("#frbtn").click(function(){
					$("#forget").fadeTo(200,0,function(){$("#forget").hide(function(){$(".lf").css({height:'240px'});$(".lf").center();$("#login").fadeTo(500,1)})});
				});
		    });
		    jQuery(function($){$.supersized({slide_interval:10000,transition:1,transition_speed:2000,performance:2,min_width:0,min_height:0,vertical_center:1,horizontal_center:1,fit_always:0,fit_portrait:1,fit_landscape:0,slide_links:'blank',slides:[{image:'/inc/img/backgrounds/1.jpg'},{image:'/inc/img/backgrounds/2.jpg'},{image:'/inc/img/backgrounds/3.jpg'},{image:'/inc/img/backgrounds/5.jpg'},{image:'/inc/img/backgrounds/6.jpg'},{image:'/inc/img/backgrounds/7.jpg'},{image:'/inc/img/backgrounds/8.jpg'},{image:'/inc/img/backgrounds/9.jpg'},{image:'/inc/img/backgrounds/10.jpg'}]})});
		    </script>
		    <body>
		        <div class="lf">
		            <form action="" method="post" id="login">
		                <input type="text" class="ltr" id="user" name="user" placeholder="Username Or Email" autocomplete="off">
		                <input type="password" class="ltr" id="pass" name="pass" placeholder="Password" autocomplete="off" style="margin-bottom:10px">
		                <input type="submit" class="btn btn-primary btn-block" value="ورود">
		                <input type="button" class="btn btn-success btn-block" value="ثبت نام" id="rbtn">
		                <a href="#" id="ufp" class="fr fw tr">فراموش کردن کلمه عبور</a>
		                <div class="error"><span>+</span></div>
		            </form>
		            <form action="" method="post" id="register">
		            	<table class="rtl fw">
		            		<tr><td><input type="text" class="ltr" id="email" name="email" placeholder="Email" autocomplete="off"></td></tr>
		            		<tr><td><input type="text" class="ltr" id="passi" name="passi" placeholder="Password" autocomplete="off"></td></tr>
		            		<tr><td><input type="text" class="ltr" id="passii" name="passii" placeholder="Password Confirm" autocomplete="off"></td></tr>
		            		<tr><td><input type="text" class="rtl" id="first" name="first" placeholder="نام" autocomplete="off"></td></tr>
		            		<tr><td><input type="text" class="rtl" id="last" name="last" placeholder="نام خانوادگی" autocomplete="off"></td></tr>
		            		<tr><td><input type="text" class="rtl" id="company" name="company" placeholder="نام شرکت" autocomplete="off"></td></tr>
		            		<tr><td><input type="text" class="rtl" id="melli" name="melli" placeholder="کد ملی" autocomplete="off"></td></tr>
		            		<tr><td><input type="text" class="rtl" id="phone" name="phone" placeholder="تلفن تماس" autocomplete="off"></td></tr>
		            		<tr><td><input type="text" class="rtl" id="cell" name="cell" placeholder="موبایل" autocomplete="off"></td></tr>
		            		<tr><td><textarea rows="3" cols="44" placeholder="توضیحات و درخواستهای ثبت نام"></textarea></td></tr>
		            	</table>
		            	<input type="button" class="btn btn-success btn-block" value="ثبت نام" id="rnubtn" style="margin-top:5px">
		            	<input type="button" class="btn btn-primary btn-block" value="بازگشت" id="bbtn">
		            </form>
		            <form action="" method="post" id="forget">
		                <input type="text" class="ltr" id="forget_email" name="forget_email" placeholder="Email Adress" autocomplete="off">
		                <input type="submit" class="btn btn-primary btn-block" value="ارسال رمز" style="margin-top:5px">
		                <input type="button" class="btn btn-success btn-block" value="بازگشت" id="frbtn">
		            </form>
		            <div style="width:100%;float:right;tet-align:center;margin-top:10px;font:12px tahoma;color:red"><?=$this->error?></div>
		        </div>
		    </body>
		</html>
		<?php
	}

	# search databse for user
	private function _query ($type = TRUE) {

		# create database object
		self::_connect();

		# normal login
		if ($type) {

			# serch user table for username and password
			$res = $this->link->result(
				array($this->user_id, $this->user_first, $this->user_last, $this->active_field, $this->block_field),
				$this->user_table,
				array(
					array('', $this->user_name_field, '=', $this->user_name, 'AND'),
					array('', $this->password_field , '=', _ah_hash($this->user_pass), '')
				)
			);

			# username and password correct
			if ($res) {

				# active user
				if ($res[3]) {

					# user is block
					if ($res[4]) {

						# define error
						$this->error = 'user is block';

						# define session login try counter to max
						$_SESSION['_login_try_count'] = $this->login_try;

						# return false for active
						return FALSE;
					}
					# user not block
					else {

						# set variable
						$this->user_id 		= $res[0];
						$this->user_first 	= $res[1];
						$this->user_last 	= $res[3];

						# delete error
						$this->error = NULL;

						# return true for login
						return TRUE;
					}
				}

				# unactive user
				else {

					# define error
					$this->error = 'user not active';

					# define session login try counter to max
					$_SESSION['_login_try_count'] = $this->login_try;

					# return false for active
					return FALSE;
				}
			}

			# username or password wrong
			else {

				# set counter in session
				if (!isset($_SESSION['_login_try_count'])) {
					$_SESSION['_login_try_count'] = 0;
				}

				$_SESSION['_login_try_count']++;

				# define error
				$this->error = 'username or password is wrong';

				# return false for active
				return FALSE;

			}
		}

		# cookie login
		else {

			# search save password table for user name ad pass word
			return FALSE;# now
		}

		# kill database object
		self::_connect();
	}

	# get cookie info for saved password
	private function _get_cookie () {
		return FALSE;# now
	}

	# set cookie for saved password
	private function _set_cookie () {
		return TRUE;# now
	}

	# connect to database
	private function _connect() {

		# create object if not current exist
		if (!isset($this->link)) {
			include_once AH_DB;
			$this->link = new _ah_mysqli();
		}
		return TRUE;
	}

	# disconnect from database
	private function _disconnect() {

		# kill object if exist
		if (isset($this->link)) {
			$this->link = NULL;
			unset($this->link);
		}
		return TRUE;
	}
}


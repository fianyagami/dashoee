<?php defined('BASEPATH') or exit('No direct script access allowed');

function do_alert($title, $type = 'info')
{
	$ico = ['info' => 'info-circle', 'success' => 'check', 'warning' => 'warning', 'danger' => 'times-circle'];

	$out = '<div class="alert alert-' . $type . '">
						<strong><i class="fa fa-' . $ico[$type] . '"></i></strong> ' . $title . '
					</div>';
	return $out;
}

function do_tg($botToken = '834476277:AAEsO8KldpD03wttHVX_3IITZlxDCPnVQMM', $params = array())
{
	$website = "https://api.telegram.org/bot" . $botToken;

	if (!empty($params) && !empty($botToken)) {
		foreach ($params as $value) {
			$type = isset($value['photo']) ? '/sendPhoto' : '/sendMessage';
			$ch   = curl_init($website . $type);

			if (isset($value['photo'])) {
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					"Content-Type:multipart/form-data"
				));
			}

			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, (http_build_query($value)));
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$result = curl_exec($ch);
			curl_close($ch);
		}
		return $result;
	}
	return false;
}

function do_pr($array = array(), $info = '')
{
	echo '<pre>';
	print_r([$array, $info]);
	echo '</pre>';
}

function do_date($date = '', $format = '')
{
	if (empty($date)) {
		return false;
	}

	$format = empty($format) ? 'd M Y' : $format;
	return date($format, strtotime($date));
}

function do_config_type($config = '', $value = array())
{
	if (!empty($config)) {
		switch ($config) {
			case 'public':
				$arr = array(
					'public_title' => array(
						'label' => 'Title',
						'type' => 'text',
						'text' => 'Masukkan Judul Selamat Datang',
						'value' => empty($value['public_title']) ? '' : $value['public_title']
						// 'addon' => 'per page'
					),
					'img_logo'			=> array(
						'label' => 'Logo',
						'type' => 'file',
						'extra' => '',
						'value' => empty($value['img_logo']) ? '' : $value['img_logo']
					),
					'img_bg'				=> array(
						'label' => 'Background',
						'extra' => '',
						'type' => 'file',
						'value' => empty($value['img_bg']) ? '' : $value['img_bg']
					),
					'send_tg'				=> array(
						'label'        => 'Kirim Telegram',
						'extra'        => '',
						'type'         => 'radio',
						'option'       => array(0, 1),
						'option_label' => array('Nonaktif', 'Aktif'),
						'value'        => empty($value['send_tg']) ? 0 : $value['send_tg']
					),
					'circular_note' => array(
						'label' => 'Catatan Surat Edaran',
						'type' => 'text',
						'text' => 'Masukkan Catatan Surat Edaran',
						'value' => empty($value['circular_note']) ? '' : $value['circular_note']
					),
					'instruction_note' => array(
						'label' => 'Catatan Instruksi',
						'type' => 'text',
						'text' => 'Masukkan Catatan Instruksi',
						'value' => empty($value['instruction_note']) ? '' : $value['instruction_note']
					),
					'online_ip' => array(
						'label' => 'IP ONLINE',
						'type' => 'text',
						'text' => 'Masukkan IP dan port online',
						'value' => empty($value['online_ip']) ? '' : $value['online_ip']
					)
				);
				break;

			case 'footer':
				$arr = array(
					'footer_text' => array(
						'label' => 'footer text',
						'type' => 'text',
						'text' => 'Masukkan Text Untuk Footer',
						'value' => empty($value['footer_text']) ? '' : $value['footer_text']
					)
				);
				break;

			case 'member':
				$arr = array(
					'color_1' => array(
						'label' => 'Mix Theme 1',
						'type' => 'color',
						'text' => 'Warna Mix 1',
						'value' => empty($value['color_1']) ? '' : $value['color_1']
					),
					'color_2' => array(
						'label' => 'Mix Theme 2',
						'type' => 'color',
						'text' => 'Warna Mix 2',
						'value' => empty($value['color_2']) ? '' : $value['color_2']
					),
					'color_3' => array(
						'label' => 'Mix Theme 3',
						'type' => 'color',
						'text' => 'Warna Mix 3',
						'value' => empty($value['color_3']) ? '' : $value['color_3']
					),

				);
				break;
			default:
				$arr = [];
				break;
		}

		return do_config($arr, $config);
	}
}

function do_config($config_type = array(), $config_name = '')
{
	if (!empty($config_type) && !empty($config_name)) {
		$n 	= 1;
		$form = '<form action="" method="POST"  enctype="multipart/form-data">';
		foreach ($config_type as $key => $value) {
			$addtips = isset($value['tips']) ? '<p class="help-block">' . $value['tips'] . '</p>' : '';
			switch ($value['type']) {
				case 'number':
				case 'email':
				case 'color':
				case 'password':
				case 'text':

					$theme = '';
					if ($value['type'] == 'color') {
						$theme = 'change_theme theme_' . $n;
						$n++;
					}
					$addon_wrapper = isset($value['addon']) ? '<div class="input-group">' : '';
					$addon = isset($value['addon']) ? '<div class="input-group-addon">' . $value['addon'] . '</div></div>' : '';
					$form .= '<div class="form-group">
														<label>' . $value['label'] . '</label>
														' . $addon_wrapper . '
															<input type="' . $value['type'] . '" class="form-control ' . $theme . '" name="' . $key . '" placeholder="' . $value['text'] . '" autocomplete="off" value="' . $value['value'] . '" required>
															' . $addtips . '
														' . $addon . '
													</div>';
					break;

				case 'checkbox':
					$checked = !empty($value['value']) ? 'Checked' : '';
					$form .= '<div class="form-group">
									<label>' . $value['label'] . '</label>
									<div class="checkbox">
								    <label>
								      <input type="checkbox" ' . $checked . ' name="' . $key . '"> ' . $value['label_info'] . '
								    </label>
								  </div>
									' . $addtips . '
								 </div>';
					break;

				case 'textarea':
					$form .= '<div class="form-group">
									<label>' . $value['label'] . '</label>
									<textarea name="' . $key . '" class="form-control" rows="' . $value['rows'] . '" placeholder="' . $value['text'] . '" required>' . $value['value'] . '</textarea>
									' . $addtips . '
								</div>';
					break;

				case 'radio':
					$form .= '<div class="form-group">
									<label>' . $value['label'] . '</label>';
					foreach ($value['option'] as $opt => $val) {
						$sels = $value['value'] == $opt ? 'checked' : '';
						$form .= '<div class="radio">
											  <label>
											    <input type="radio" ' . $sels . ' name="' . $key . '"  value="' . $val . '">
											    ' . $value['option_label'][$opt] . '
											  </label>
											</div>';
					}
					$form .= $addtips . ' </div>';

					break;

				case 'file':
					if (!empty($value['value'])) {
						$form .= '<div class="form-group">
												<img src="' . base_url() . 'Assets/images/config/' . $config_name . '/' . $value['value'] . '" class="img img-responsive" width="150" height="150">
											</div>';
					}
					$form .= '<div class="form-group">
									<label>' . $value['label'] . '</label>
									<input type="file" class="form-control" ' . @$value['extra'] . ' name="' . $key . '">
									' . $addtips . '
								</div>';
					break;
				default:
					# code...
					break;
			}
		}

		$form .= 		'<div class="form-group">
									<a href="' . site_url('validate/config') . '" class="btn btn-default"><i class="fa fa-chevron-left"></i></a>
									<button type="submit" name="update_config" value="1" class="btn btn-primary"><i class="fa fa-floppy-o"></i> Save</button>
									<button type="reset" name="reset" value="1" class="btn btn-warning"><i class="fa fa-refresh"></i> Reset</button>
								</div>
							</form>';

		return $form;
	} else {
		if ($config_name == 'slider') {
			echo '<script>window.location="' . site_url('validate/config_slider') . '"</script>';
		}
		return do_alert('Something\'s Went Wrong!!', 'danger');
	}
}

function do_paginate($r_config = array())
{
	$config['uri_segment'] 	= 3;
	$config['per_page'] 		= 2;
	$config['base_url'] 		= base_url(); //site url
	$config['total_rows'] 	= $r_config['total_rows'];
	$config['num_links'] 		= 5;
	$config['last_link'] 		= ceil($config['total_rows'] / $config['per_page']) > 5 ? '.... Last' : 'Last';

	$config['full_tag_open'] 		= "<ul class='pagination'>";
	$config['full_tag_close'] 	= "</ul>";
	$config['num_tag_open'] 		= '<li>';
	$config['num_tag_close'] 		= '</li>';
	$config['cur_tag_open'] 		= "<li class='disabled'><li class='active'><a href='#'>";
	$config['cur_tag_close'] 		= "<span class='sr-only'></span></a></li>";
	$config['next_tag_open'] 		= "<li>";
	$config['next_tagl_close'] 	= "</li>";
	$config['prev_tag_open'] 		= "<li>";
	$config['prev_tagl_close'] 	= "</li>";
	$config['first_tag_open'] 	= "<li>";
	$config['first_tagl_close'] = "</li>";
	$config['last_tag_open'] 		= "<li>";
	$config['last_tagl_close'] 	= "</li>";


	if (!empty($r_config) && is_array($r_config)) {
		foreach ($r_config as $key => $value) {
			$config[$key] = $value;
		}
	}

	return $config;
}

function do_alertsweet($title = '', $msg = '', $type = 'success')
{
	echo '<script type="text/javascript">';
	echo 'setTimeout(function () { swal("' . $title . '","' . $msg . '","' . $type . '");';
	echo '}, 500);</script>';
}


function do_build_navbar($r_menu = array())
{
	$out = '';
	if (!empty($r_menu)) {
		foreach ($r_menu as $key => $value) {
			// do_pr($r_menu);die();
			if (empty($value['submenu'])) {
				$out .= '<li><a href="' . $value['url'] . '">' . $value['name'] . '</a></li>';
			} else {
				$out .= '  <li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Manajemen <span class="caret"></span></a>
				<ul class="dropdown-menu">
				' . do_build_navbar($value['submenu']) . '
				</ul>
				</li>';
			}
		}
		return $out;
	}
}

function do_encrypt($data, $key = 'bRuD5WYw5wd0rdHR9yLlM6wt2vteuiniQBqE70nAuhU=')
{
	if (!empty($data)) {
		// Remove the base64 encoding from our key
		$encryption_key = base64_decode($key);
		// Generate an initialization vector
		$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
		// Encrypt the data using AES 256 encryption in CBC mode using our encryption key and initialization vector.
		$encrypted = openssl_encrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);
		// The $iv is just as important as the key for decrypting, so save it with our encrypted data using a unique separator (::)
		return base64_encode($encrypted . '::' . $iv);
	}
}



function do_decrypt($data, $key = 'bRuD5WYw5wd0rdHR9yLlM6wt2vteuiniQBqE70nAuhU=')
{
	if (!empty($data)) {
		// Remove the base64 encoding from our key
		$encryption_key = base64_decode($key);
		// To decrypt, split the encrypted data from our IV - our unique separator used was "::"
		list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
		return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
	}
}

function do_download($path, $name, $mime)
{
	// make sure it's a file before doing anything!
	if (is_file($path)) {
		// Build the headers to push out the file properly.
		header('Pragma: public');     // required
		header('Expires: 0');         // no cache
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT');
		header('Cache-Control: private', false);
		header('Content-Type: ' . $mime);  // Add the mime type from Code igniter.
		header('Content-Disposition: attachment; filename="' . basename($name) . '"');  // Add the file name
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: ' . filesize($path)); // provide file size
		header('Connection: close');
		readfile($path); // push it out
		exit();
	}
}

function do_formal_date($date = '', $delimiter = '', $is_day = false)
{
	if (empty($date)) {
		$date = date('d M Y');
	}

	if (!empty($date)) {
		$day = '';
		if ($is_day) {
			$days = [
				'Saturday' => 'Sabtu',
				'Sunday' 	=> 'Minggu',
				'Monday' 	=> 'Senin',
				'Tuesday'  => 'Selasa',
				'Wednesday' => 'Rabu',
				'Thursday' => 'Kamis',
				'Friday' 	=> 'Jum\'at'
			];
			$day = $days[date('l', strtotime($date))] . ', ';
		}
		$months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
		$d = date('d', strtotime($date));
		$m = date('m', strtotime($date));
		$y = date('Y', strtotime($date));

		$delimiter = !empty($delimiter) ? $delimiter : ' ';
		return $day . $d . $delimiter . $months[intval($m)] . $delimiter . $y;
	}
}

function download_notes($type = '', $fold = '', $filename = '')
{
	$file = FCPATH . 'Assets/' . $type . '/' . $fold . '/' . $filename;
	if (is_file($file)) {
		$msg = file_get_contents($file);
		$msg_encrypted = do_decrypt($msg, 'bRuD5WYw5wd0rdHR9yLlM6wt2vteuiniQBqE70nAuhU=');


		// $msg_encrypted = do_encrypt($msg, 'bRuD5WYw5wd0rdHR9yLlM6wt2vteuiniQBqE70nAuhU=');
		// $file = fopen(FCPATH.'Assets/prepare.xlsx', 'wb');
		// fwrite($file, $msg_encrypted);
		// fclose($file);


		// $ext = get_mime_by_extension($file);
		// echo $ext;die();
		$CI = &get_instance();
		$CI->load->helper('download');
		force_download($filename, $msg_encrypted);
	}
}

function do_downpath($file = '', $filename = '')
{
	if (is_file($file)) {
		$msg = file_get_contents($file);
		$msg_encrypted = do_decrypt($msg, 'bRuD5WYw5wd0rdHR9yLlM6wt2vteuiniQBqE70nAuhU=');

		$CI = &get_instance();
		$CI->load->helper('download');
		force_download($filename, $msg_encrypted);
	}
}

function do_sendemail($email = '', $r_data = array(), $key = 1)
{
	if (!empty($email)) {
		require APPPATH . '/third_party/PHPMailer/PHPMailerAutoload.php';

		if ($key > 1) {
			$mail = new PHPMailer;
			$mail->SmtpClose();
		}

		$mail = new PHPMailer;

		$mail->isSMTP();                                   // Set mailer to use SMTP
		$mail->Host       = 'smtp.gmail.com';                    // Specify main and backup SMTP servers
		$mail->SMTPAuth   = true;                            // Enable SMTP authentication
		$mail->Username   = 'gajelicious@gmail.com';          // SMTP username
		$mail->Password   = 'semuanya1231'; // SMTP password
		$mail->SMTPSecure = 'tls';                         // Enable TLS encryption, `ssl` also accepted
		$mail->Port       = 587;                                 // TCP port to connect to

		$mail->setFrom('gajelicious@gmail.com', 'Smart Secretary KSH');
		$mail->addReplyTo('gajelicious@gmail.com', 'Smart Secretary KSH');
		$mail->addAddress($email);   // Add a recipient

		$mail->isHTML(true);  // Set email format to HTML

		$mail->Subject = $r_data['title'];
		$mail->Body    = $r_data['description'];



		if (!$mail->send()) {
			return false;
		} else {
			return true;
		}
	}
}

function do_slashes($str = '')
{
	if (!empty($str)) {
		if (is_array($str)) {
			foreach ($str as $id => $value) {
				$str[$id] = addslashes_mssql($value);
			}
		} else {
			$str = str_replace("'", "''", $str);
		}
	}
	return $str;
}

function do_stripslashes($str = '')
{
	if (!empty($str)) {
		if (is_array($str)) {
			foreach ($str as $id => $value) {
				$str[$id] = stripslashes_mssql($value);
			}
		} else {
			$str = str_replace("''", "'", $str);
		}
	}
	return $str;
}

/*for 2019*/
function do_lastNote()
{
	$last  = [
		'ASSET'      => 0,
		'BKPSI'      => 0,
		'CM'         => 0,
		'CORPO'      => 0,
		'Deputy'     => 0,
		'DIR'        => 0,
		'DM'         => 0,
		'EDP'        => 0,
		'FRM'        => 0,
		'GIZI'       => 0,
		'HD'         => 0,
		'HRD'        => 0,
		'HUMAS'      => 0,
		'K3'         => 0,
		'KEU'        => 0,
		'KLINIK'     => 0,
		'KOMED'      => 0,
		'KOMKEP'     => 0,
		'KOMTIK'     => 0,
		'KOMTKPL'    => 0,
		'KPRS'       => 0,
		'LAB'        => 0,
		'LEGAL'      => 0,
		'MANJANGMED' => 0,
		'MCU'        => 0,
		'MDGs'       => 0,
		'MP'         => 0,
		'PAN'        => 0,
		'PMKP'       => 0,
		'PPI'        => 0,
		'REHABMEDIK' => 0,
		'RI'         => 0,
		'RJ'         => 0,
		'RK'         => 0,
		'RM'         => 0,
		'RO-ND'      => 0,
		'RS23'       => 0,
		'SARPRAS'    => 0,
		'SEC'        => 0,
		'SEKR'       => 0,
		'SIM'        => 0,
		'SJSN'       => 0,
		'SPI'        => 0
	];
	return $last;
}

function do_lastNotebox()
{
	$last = [0, 65];
	return $last;
}

function do_lastIns()
{
	return 105;
}

function do_outbox_unit()
{
	$last  = [
		[
			'ASSET'      => 0,
			'BKPSI'      => 0,
			'CM'         => 0,
			'CORPO'      => 0,
			'Deputy'     => 0,
			'DIR'        => 0,
			'DM'         => 0,
			'EDP'        => 0,
			'FRM'        => 0,
			'GIZI'       => 0,
			'HD'         => 0,
			'HRD'        => 0,
			'HUMAS'      => 0,
			'K3'         => 0,
			'KEU'        => 0,
			'KLINIK'     => 0,
			'KOMED'      => 0,
			'KOMKEP'     => 0,
			'KOMTIK'     => 0,
			'KOMTKPL'    => 0,
			'KPRS'       => 0,
			'LAB'        => 0,
			'LEGAL'      => 0,
			'MANJANGMED' => 0,
			'MCU'        => 0,
			'MDGs'       => 0,
			'MP'         => 0,
			'PAN'        => 0,
			'PMKP'       => 0,
			'PPI'        => 0,
			'REHABMEDIK' => 0,
			'RI'         => 0,
			'RJ'         => 0,
			'RK'         => 0,
			'RM'         => 0,
			'RO-ND'      => 0,
			'RS23'       => 0,
			'SARPRAS'    => 0,
			'SEC'        => 0,
			'SEKR'       => 0,
			'SIM'        => 0,
			'SJSN'       => 0,
			'SPI'        => 0
		],
		[
			'ASSET'      => 0,
			'BKPSI'      => 0,
			'CM'         => 0,
			'CORPO'      => 0,
			'Deputy'     => 0,
			'DIR'        => 0,
			'DM'         => 0,
			'EDP'        => 0,
			'FRM'        => 0,
			'GIZI'       => 0,
			'HD'         => 0,
			'HRD'        => 0,
			'HUMAS'      => 0,
			'K3'         => 0,
			'KEU'        => 0,
			'KLINIK'     => 0,
			'KOMED'      => 0,
			'KOMKEP'     => 0,
			'KOMTIK'     => 0,
			'KOMTKPL'    => 0,
			'KPRS'       => 0,
			'LAB'        => 0,
			'LEGAL'      => 0,
			'MANJANGMED' => 0,
			'MCU'        => 0,
			'MDGs'       => 0,
			'MP'         => 0,
			'PAN'        => 0,
			'PMKP'       => 0,
			'PPI'        => 0,
			'REHABMEDIK' => 0,
			'RI'         => 0,
			'RJ'         => 0,
			'RK'         => 0,
			'RM'         => 0,
			'RO-ND'      => 0,
			'RS23'       => 0,
			'SARPRAS'    => 0,
			'SEC'        => 0,
			'SEKR'       => 0,
			'SIM'        => 0,
			'SJSN'       => 0,
			'SPI'        => 0
		],

	];
	return $last;
}

function do_outbox()
{
	$last  = [
		'ASSET'      => 0,
		'BKPSI'      => 0,
		'CM'         => 0,
		'CORPO'      => 0,
		'Deputy'     => 0,
		'DIR'        => 0,
		'DM'         => 0,
		'EDP'        => 0,
		'FRM'        => 0,
		'GIZI'       => 0,
		'HD'         => 0,
		'HRD'        => 0,
		'HUMAS'      => 0,
		'K3'         => 0,
		'KEU'        => 0,
		'KLINIK'     => 0,
		'KOMED'      => 0,
		'KOMKEP'     => 0,
		'KOMTIK'     => 0,
		'KOMTKPL'    => 0,
		'KPRS'       => 0,
		'LAB'        => 0,
		'LEGAL'      => 0,
		'MANJANGMED' => 0,
		'MCU'        => 0,
		'MDGs'       => 0,
		'MP'         => 0,
		'PAN'        => 0,
		'PMKP'       => 0,
		'PPI'        => 0,
		'REHABMEDIK' => 0,
		'RI'         => 0,
		'RJ'         => 0,
		'RK'         => 0,
		'RM'         => 0,
		'RO-ND'      => 0,
		'RS23'       => 0,
		'SARPRAS'    => 0,
		'SEC'        => 0,
		'SEKR'       => 0,
		'SIM'        => 0,
		'SJSN'       => 0,
		'SPI'        => 0
	];
	return $last;
}

// function do_eksternal()
// {
// 	return 0;
// }

function do_lastMemo()
{
	$last  = [
		'ASSET'      => 0,
		'BKPSI'      => 0,
		'CM'         => 0,
		'CORPO'      => 0,
		'Deputy'     => 0,
		'DIR'        => 0,
		'DM'         => 0,
		'EDP'        => 0,
		'FRM'        => 0,
		'GIZI'       => 0,
		'HD'         => 0,
		'HRD'        => 0,
		'HUMAS'      => 0,
		'K3'         => 0,
		'KEU'        => 0,
		'KLINIK'     => 0,
		'KOMED'      => 0,
		'KOMKEP'     => 0,
		'KOMTIK'     => 0,
		'KOMTKPL'    => 0,
		'KPRS'       => 0,
		'LAB'        => 0,
		'LEGAL'      => 0,
		'MANJANGMED' => 0,
		'MCU'        => 0,
		'MDGs'       => 0,
		'MP'         => 0,
		'PAN'        => 0,
		'PMKP'       => 0,
		'PPI'        => 0,
		'REHABMEDIK' => 0,
		'RI'         => 0,
		'RJ'         => 0,
		'RK'         => 0,
		'RM'         => 0,
		'RO-ND'      => 0,
		'RS23'       => 0,
		'SARPRAS'    => 0,
		'SEC'        => 0,
		'SEKR'       => 0,
		'SIM'        => 0,
		'SJSN'       => 0,
		'SPI'        => 0
	];
	return $last;
}

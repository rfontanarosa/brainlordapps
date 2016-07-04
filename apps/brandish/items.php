<?php

	header('Content-Type: text/html; charset=utf-8');

	require_once 'config.inc.php';

?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title><?php echo TITLE; ?>&nbsp;-&nbsp;Translation Tool</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="./images/favicon.ico" rel="shortcut icon" type="image/x-icon" />
		<link href="./images/favicon.ico" rel="apple-touch-icon" />
		<link rel="stylesheet" href="../../assets/libs/bootstrap/dist/css/bootstrap.min.css" type="text/css" />
		<link rel="stylesheet" href="./css/bootstrap.custom.css" type="text/css" />
		<script type="text/javascript" src="../../assets/libs/jquery/dist/jquery.min.js"></script>
		<script type="text/javascript" src="../../assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>
	</head>
	<body style="background-image: url(./images/bg2.png)">

	<?php
		$items = array();
		if (($handle = fopen('../../resources/dump/brandish/items.csv', 'r')) !== false) {
			while (($data = fgetcsv($handle, 1000, ',')) !== false) {
				array_push($items, $data);
			}
			fclose($handle);
		}
		$items_t = array();
		if (($handle = fopen('../../resources/csv/brandish/items-clomax.csv', 'r')) !== false) {
			while (($data = fgetcsv($handle, 1000, ',')) !== false) {
				array_push($items_t, $data);
			}
			fclose($handle);
		}
	?>

	<ul class="nav nav-tabs">
		<li class="active"><a href="#original" data-toggle="tab">Original</a></li>
		<li><a href="#translated" data-toggle="tab">Translated</a></li>
	</ul>

	<div class="tab-content">

		<div class="tab-pane fade in active" id="original">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th style="width: 5%">#</th>
						<th style="width: 5%">Pointer Address</th>
						<th style="width: 20%">Pointer</th>
						<th style="width: 10%">Text Address</th>
						<th style="width: 15%">Text</th>
						<th style="width: 5%">Text Length</th>
						<th style="width: 5%">LPadding Address</th>
						<th style="width: 5%">LPadding</th>
						<th style="width: 40%">Preview</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<?php
							$length_total = 0;
							$k = 0;
							foreach ($items as $item) {
								$text = $item[3];
								$padding = $item[5];
								$length = strlen($text);
								echo '<tr>';
								echo '<td>' . $k .'</td>';
								echo '<td>' . $item[0] .'</td>';
								echo '<td>' . $item[1] .'</td>';
								echo '<td>' . $item[2] .'</td>';
								echo '<td>' . $text .'</td>';
								echo '<td>' . $length .'</td>';
								echo '<td>' . $item[4] .'</td>';
								echo '<td>' . $padding .'</td>';
								if ($padding != '') {
									$color = ($padding < ((20-$padding)-$length)) ? 'red' : '#081873'; 
									echo '<td><table class="table table-bordered" style="background-color: ' . $color . '; margin-bottom: 0px;"><tbody><tr>';
									for ($i=0; $i<$padding; $i++) echo '<td style="width: 5%">&nbsp;</td>';
									for ($i=0; $i<$length; $i++) echo '<td style="width: 5%">' . $text[$i] . '</td>';
									for ($i=0; $i<(20-$padding)-$length; $i++) echo '<td style="width: 5%">&nbsp;</td>';
									echo '</tbody></tr></table></td>';
								}
								else {
									echo "<td>$text</td>";
								}
								echo '</tr>';
								$k++;
								$length_total+=$length;
							}
						?>
					</tr>
				</tbody>
			</table>
			<?php echo $length_total; ?>
		</div>

		<div class="tab-pane fade in" id="translated">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th style="width: 5%">#</th>
						<th style="width: 15%">Translated Text</th>
						<th style="width: 15%">Translated Text Address</th>
						<th style="width: 5%">Translated LPadding</th>
						<th style="width: 40%">Preview</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<?php
							$length_total = 0;
							$k = 0;
							foreach ($items_t as $item) {
								$text = $item[0];
								$padding = $item[1];
								$length = strlen($text);
								$address_s = $items[0][2];
								$address = hexdec($address_s) + $length_total + $k;
								$address = '0x' . dechex($address);
								echo '<tr>';
								echo '<td>' . $k .'</td>';
								echo '<td>' . $item[0] .'</td>';
								$color = ($address != $items[$k][2]) ? 'green' : 'transparent'; 
								echo '<td style="background-color:' . $color .'">' . $address .'</td>';
								echo '<td>' . $item[1] .'</td>';
								if ($padding != '') {
									$color = ($padding < ((20-$padding)-$length)) ? 'red' : '#081873';
									echo '<td><table class="table table-bordered" style="background-color: ' . $color . '; margin-bottom: 0px;"><tbody><tr>';
									for ($i=0; $i<$padding; $i++) echo '<td style="width: 5%">&nbsp;</td>';
									for ($i=0; $i<$length; $i++) echo '<td style="width: 5%">' . $text[$i] . '</td>';
									for ($i=0; $i<(20-$padding)-$length; $i++) echo '<td style="width: 5%">&nbsp;</td>';
									echo '</tbody></tr></table></td>';
								}
								else {
									echo "<td>$text</td>";
								}
								echo '</tr>';
								$k++;
								$length_total+=$length;
							}
						?>
					</tr>
				</tbody>
			</table>
			<?php echo $length_total; ?>
		</div>
	</div>

	</body>
</html>
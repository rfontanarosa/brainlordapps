<?php

	session_start();

	header('Content-Type: text/html; charset=utf-8');

	require_once 'config.inc.php';

	/** LOGOUT */
	if (isset($_POST['logout'])) {
		UserManager::logout();
	}

	/** LOGIN */
	if (isset($_POST['uname'])) {
		UserManager::login($_POST['uname'], $_POST['pass']);
	}

?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title><?php echo TITLE; ?>&nbsp;-&nbsp;Translation Tool</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link href="./images/favicon.ico" rel="shortcut icon" type="image/x-icon" />
		<link href="./images/favicon.ico" rel="apple-touch-icon" />
		<link rel="stylesheet" href="../../assets/libs/bootstrap/dist/css/bootstrap.min.css" type="text/css" />
		<link rel="stylesheet" href="../../assets/css/bootstrap.custom.css" type="text/css" />
		<link rel="stylesheet" href="./css/bootstrap.custom.css" type="text/css" />
		<script type="text/javascript" src="../../assets/libs/jquery/dist/jquery.min.js"></script>
		<script type="text/javascript" src="../../assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>
	</head>
	<body>

	<?php
		$max_id = LAST_ENTRY;
		$id = isset($_GET['id']) ? $_GET['id'] : 1;
		if (!is_numeric($id)) {
			exit('<div class="container">ERROR!!! Index is not a number!</div></body></html>');
		}
		if ($id < 1 || $id > $max_id) {
			exit('<div class="container">ERROR!!! Index out of range!</div></body></html>');
		}
	?>

	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="index.php"><?php echo TITLE; ?></a>
			</div>
			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				<?php if (UserManager::isLogged()): ?>
					<form method="post" class="navbar-form navbar-right" role="navigation">
						<input type="hidden" name="logout" value="1" />
						<button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-log-out"></span>&nbsp;Logout</button>
					</form>
					<ul class="nav navbar-nav navbar-right">
						<li><a href="#"><span class="glyphicon glyphicon-user"></span>&nbsp;<?php echo UserManager::getUsername(); ?></a></li>
					</ul>
				<?php else: ?>
					<form method="post" class="navbar-form navbar-right" role="navogation">
						<div class="form-group">
							<input type="text" name="uname" class="form-control" placeholder="Username">
						</div>
						<div class="form-group">
							<input type="password" name="pass" class="form-control" placeholder="Password">
						</div>
						<button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-log-in"></span>&nbsp;Login</button>
					</form>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<div class="container text-center ">
		<img src="./images/logo.png" class="img-responsive" alt="<?php echo TITLE; ?>" />
	</div>

	<?php if (UserManager::isLogged() && UserManager::hasRole(APPLICATION_ID)): ?>

		<?php $uname = UserManager::getUsername(); ?>

		<div class="container">

			<!-- PROGRESS BAR -->
			<div class="panel panel-default">
				<div class="panel-body">
					<?php
						$db = new SQLite3(SQLITE_FILENAME);
						$partially = DbManager::countByUserAndStatus($db, $uname, 1);
						$done = DbManager::countByUserAndStatus($db, $uname, 2);
						$undone = LAST_ENTRY - ($done + $partially);
						$db->close();
						unset($db);
						$done100 = number_format(round(($done/$max_id)*100, 3), 1);
						$partially100 = number_format(round(($partially/$max_id)*100, 3), 1);
						$undone100 = number_format(100 - $done100 - $partially100, 1);
					?>
					<div class="progress">
						<div class="progress-bar progress-bar-success" style="width: <?php echo $done100; ?>%">
							<span><?php echo $done100; ?>% Done</span>
						</div>
						<div class="progress-bar progress-bar-warning" style="width: <?php echo $partially100; ?>%">
							<span><?php echo $partially100; ?>% Partially Done</span>
						</div>
						<div class="progress-bar progress-bar-danger" style="width: <?php echo $undone100; ?>%">
							<span><?php echo $undone100; ?>% Undone</span>
						</div>
					</div>
				</div>
			</div>
			<!-- PAGINATION -->
			<div class="panel panel-default">
				<div class="panel-body text-center">
					<?php
						$db = new SQLite3(SQLITE_FILENAME);
						$next_id = DbManager::getNextIdByUserAndId($db, $uname, $id);
						$prev_id = DbManager::getPrevIdByUserAndId($db, $uname, $id);
						$db->close();
						unset($db);
					?>
					<div class="btn-toolbar" role="toolbar">
						<div class="btn-group" role="group">
							<a class="btn btn-default <?php if ($id == 1) echo 'disabled'; ?>" href="?id=1">&larr;&nbsp;First</a>
							<a class="btn btn-default <?php if ($id == 1) echo 'disabled'; ?>" href="?id=<?php if ($id > 1) echo ($id - 1); ?>">&laquo;&nbsp;Prev</a>
							<a class="btn btn-default <?php if ($id == $max_id) echo 'disabled'; ?>" href="?id=<?php if ($id < $max_id) echo ($id + 1); ?>">Next&nbsp;&raquo;</a>
							<a class="btn btn-default <?php if ($id == $max_id) echo 'disabled'; ?>" href="?id=<?php echo $max_id; ?>">Last&nbsp;&rarr;</a>
						</div>
						<div class="btn-group" role="group">
							<a class="btn btn-default"><?php echo sprintf('#%04d', $id); ?></a>
						</div>
						<div class="btn-group" role="group">
							<a class="btn btn-default <?php if ($prev_id == 0) echo 'disabled'; ?>" href="?id=<?php if (isset($prev_id)) echo $prev_id; ?>">&lsaquo;&nbsp;Prev (TODO)</a>
							<a class="btn btn-default <?php if ($next_id == 0) echo 'disabled'; ?>" href="?id=<?php if (isset($next_id)) echo $next_id; ?>">Next (TODO)&nbsp;&rsaquo;</a>
						</div>
					</div>
				</div>
			</div>
			<!-- MAIN -->
			<div class="row">
				<!-- ORIGINAL TEXT BOX -->
				<div class="col-xs-6 col-md-6 col-lg-6">
					<div class="panel panel-default">
						<div class="panel-body">
							<?php
								try {
									$db = new SQLite3(SQLITE_FILENAME);
									if ($row = DbManager::getOriginalById($db, $id)) {
										$text = $row['text_encoded'];
										$size = $row['size'];
										$block = $row['block'];
										if (defined('NEWLINE_REPLACE') && NEWLINE_REPLACE && defined('NEWLINECHAR')) {
											$text = str_replace(NEWLINECHAR, '&#13;&#10;', $text);
										}
										echo '<textarea id="original_text" rows="10" style="background: white;" class="form-control" disabled>', $text, '</textarea>';
									}
									$db->close();
									unset($db);
								}
								catch (Exception $e) {
									print_r($e);
								}
							?>
						</div>
						<div class="panel-footer">
							<button type="submit" class="btn btn-default" id="preview-original-btn"><span class="glyphicon glyphicon-search"></span>&nbsp;Preview</button>
						</div>
						<div class="panel-footer">
							<div class="row">
								<div class="col-xs-3 col-md-3 col-lg-3">Size:&nbsp;<?php echo $size; ?></div>
								<div class="col-xs-3 col-md-3 col-lg-3">Block:&nbsp;<?php echo $block; ?></div>
							</div>
						</div>
					</div>
				</div>
				<!-- NEW TEXT BOX -->
				<div class="col-xs-6 col-md-6 col-lg-6">
					<div class="panel panel-default">
						<div class="panel-body">
							<?php
								// modified text
								$db = new SQLite3(SQLITE_FILENAME);
								if ($row = DbManager::getTranslationByUserAndOriginalId($db, $uname, $id)) {
									$text = $row['new_text'];
									$comment = $row['comment'];
									if (defined('NEWLINE_REPLACE') && NEWLINE_REPLACE && defined('NEWLINECHAR')) {
										$text = str_replace(NEWLINECHAR, '&#13;&#10;', $text);
									}
									$status = $row['status'];
									$date = $row['date'];
								}
								if (!isset($status)) $status = 0;
								$db->close();
								unset($db);
							?>
							<form method="post" id="form1">
								<input type="hidden" name="id_text" value="<?php echo $id; ?>" />
								<?php
									switch ($status) {
										case '0':
											$class = 'label-danger';
											break;
										case '':
											$class = 'label-danger';
											break;
										case '1':
											$class = 'label-warning';
											break;
										case '2':
											$class = 'label-success';
											break;
									}
								?>
								<div class="form-group">
									<textarea rows="10" class="form-control <?php echo $class; ?>" id="new_text" name="new_text"><?php if (isset($text)) echo $text; else echo $otext; ?></textarea>
								</div>
								<div class="form-group" style="margin-bottom: 0px;">
									<textarea rows="2" class="form-control" name="comment"><?php if (isset($comment)) echo $comment; ?></textarea>
								</div>
							</form>
						</div>
						<div class="panel-footer">
							<button type="submit" class="btn btn-default text-right" id="preview-new-btn"><span class="glyphicon glyphicon-search"></span>&nbsp;Preview</button>
							<button type="submit" class="btn btn-danger submit-btn" value="0"><span class="glyphicon glyphicon-floppy-save"></span>&nbsp;UNDONE</button>
							<button type="submit" class="btn btn-warning submit-btn" value="1"><span class="glyphicon glyphicon-floppy-save"></span>&nbsp;PARTIALLY DONE</button>
							<button type="submit" class="btn btn-success submit-btn" value="2"><span class="glyphicon glyphicon-floppy-save"></span>&nbsp;DONE</button>
						</div>
						<div class="panel-footer">
							Last update:&nbsp;
							<span id="lastUpdate">
								<?php
									if (isset($date)) {
										echo @date('d/m/Y, G:i', $date);
									} else {
										echo 'Never been updated!';
									}
								?>
							</span>
						</div>
					</div>
				</div>
			</div>
			<!-- TIPS -->
			<div class="row">
				<div class="col-xs-12 col-md-12 col-lg-12">
					<div class="panel panel-default">
						<div class="panel-heading">Tips</div>
						<div class="panel-body">
							<ul>
								<li><a href="https://strategywiki.org/wiki/Brain_Lord/Town_of_Arcs">STRATEGYWIKI</a></li>
								<li><a href="https://tcrf.net/Brain_Lord">The Cutting Room Floor</a></li>
								<li><a href="http://starboy91.blogspot.it/2016/07/brain-lord-sfcsnes-review.html">Starblog</a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>

	<?php else: ?>

		<div class="container">ACCESS DENIED!!! You are not authorized to access this page!</div>

	<?php endif; ?>

	<div class="modal fade bs-example-modal-sm" id="myModal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-body"><span class="label"></span></div>
			</div>
		</div>
	</div>

	<div id="preview-modal" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
						<h4 class="modal-title" id="mySmallModalLabel" style="color:black;"><span class="glyphicon glyphicon-search"></span>&nbsp;Preview</h4>
				</div>
				<div class="modal-body"></div>
			</div>
		</div>
	</div>

	<script type="text/javascript">

	$(document).ready(function() {

		$('#form1').on('submit', function(e) {
			e.stopPropagation();
			e.preventDefault();
		});

		$('.submit-btn').click(function(e) {
			var id_text = $('input[name="id_text"]').val();
			var new_text = $('textarea[name="new_text"]').val();
			var comment = $('textarea[name="comment"]').val();
			var status = $(this).val();
			$.ajax({
				type: 'POST',
				url: 'ajax_submit.php',
				data: {
					id_text : id_text,
					new_text : new_text,
					status : status,
					comment : comment
				}
			}).done(function(data, textStatus, jqXHR) {
				var data = $.parseJSON(data);
				var textareaNewText = $('textarea[name=new_text]', '#form1');
				switch (status) {
					case '0':
						textareaNewText.removeClass('label-success label-warning').addClass('label-danger');
						break;
					case '1':
						textareaNewText.removeClass('label-danger label-success').addClass('label-warning');
						break;
					case '2':
						textareaNewText.removeClass('label-danger label-warning').addClass('label-success');
						break;
				}
				$('#lastUpdate').text(data.updateDate);
				$('#myModal > .modal-dialog > .modal-content > .modal-body > span').text('The text has been updated with success!').removeClass('label-danger').addClass('label-success');
			}).fail(function(jqXHR, textStatus, errorThrown) {
				console.log(errorThrown);
				$('#myModal > .modal-dialog > .modal-content > .modal-body > span').text('An error has occurred!').removeClass('label-success').addClass('label-danger');
			}).always(function(a, textStatus, b) {
				$('#myModal').modal();
			});
		});

		$('#preview-original-btn, #preview-new-btn').click(function(e) {
			e.stopPropagation();
			e.preventDefault();
			var id_text = $('input[name="id_text"]').val();
			var originalOrNew = ($(this).attr('id').indexOf('new') !== -1) ? 'new' : 'original';
			var text = (originalOrNew == 'new') ? $('#new_text').val() : $('#original_text').val();
			$.ajax({
				async: false,
				type: 'POST',
				url: 'preview.php',
				data: {
					id_text: id_text,
					type: originalOrNew,
					text : text
				}
			}).done(function(data, textStatus, jqXHR) {
				$('#preview-modal .modal-body').html(data);
				$('#preview-modal').modal('show');
			});
		});

	});

	</script>

	</body>
</html>
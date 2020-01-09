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
		<script type="text/javascript" src="../../node_modules/jquery/dist/jquery.min.js"></script>
		<script type="text/javascript" src="../../node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
		<script type="text/javascript"src="../../node_modules/popper.js/dist/umd/popper.min.js"></script>
		<script type="text/javascript" src="../../node_modules/@fortawesome/fontawesome-free/js/all.min.js"></script>
		<link rel="stylesheet" href="../../node_modules/bootstrap/dist/css/bootstrap.min.css" />
		<link rel="stylesheet" href="../../node_modules/@fortawesome/fontawesome-free/css/all.min.css" />
		<link rel="stylesheet" href="./css/bootstrap.custom.css" type="text/css" />
		<link rel="stylesheet" href="./css/style-preview.css" type="text/css" />
		<script type="text/javascript" src="./js/preview.js" charset="UTF-8"></script>
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

	<!-- NAVBAR -->
	<nav class="navbar fixed-top navbar-expand-lg navbar-dark brain-navbar">
		<span class="navbar-brand mb-0 h1"><?php echo TITLE; ?></span>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarSupportedContent">
			<?php if (UserManager::isLogged()): ?>
				<span class="navbar-text ml-auto pr-2">
					<i class="fas fa-user"></i>&nbsp;<?php echo UserManager::getUsername(); ?>&nbsp;-&nbsp;<?php echo UserManager::getRole(APPLICATION_ID); ?>
				</span>
				<form method="post" class="form-inline">
					<input type="hidden" name="logout" value="1" />
					<button type="submit" class="btn btn-light btn-sm my-2 my-sm-0"><i class="fas fa-sign-out-alt"></i>&nbsp;Logout</button>
				</form>
			<?php else: ?>
				<form method="post" class="form-inline ml-auto">
					<input type="text" name="uname" class="form-control form-control-sm mr-sm-2 my-2 my-sm-0" placeholder="Username" aria-label="Username" />
					<input type="password" name="pass" class="form-control form-control-sm mr-sm-2 my-2 my-sm-0" placeholder="Password" aria-label="Password" />
					<button type="submit" class="btn btn-light btn-sm my-2 my-sm-0"><i class="fas fa-sign-in-alt"></i>&nbsp;Login</button>
				</form>
			<?php endif; ?>
		</div>
	</nav>

	<?php if (UserManager::isLogged() && UserManager::getRole(APPLICATION_ID) == 'user'): ?>

		<?php $uname = UserManager::getUsername(); ?>

		<!-- PAGINATION -->
		<?php
			$db = new SQLite3(SQLITE_FILENAME);
			$next_id = DbManager::getNextIdByUserAndId($db, $uname, $id);
			$prev_id = DbManager::getPrevIdByUserAndId($db, $uname, $id);
			$db->close();
			unset($db);
		?>
		<div class="container-fluid mb-3 mt-3">
			<div class="card brain-card pt-3">
				<div class="btn-toolbar d-flex justify-content-center" role="toolbar" aria-label="Toolbar with button groups">
					<div class="btn-group mb-3 mr-3" role="group" aria-label="First group">
						<a class="btn btn-light <?php if ($id == 1) echo 'disabled'; ?>" href="?id=1">&larr;&nbsp;First</a>
						<a class="btn btn-light <?php if ($id == 1) echo 'disabled'; ?>" href="?id=<?php if ($id > 1) echo ($id - 1); ?>">&lsaquo;&nbsp;Prev</a>
						<a class="btn btn-light <?php if ($id == $max_id) echo 'disabled'; ?>" href="?id=<?php if ($id < $max_id) echo ($id + 1); ?>">Next&nbsp;&rsaquo;</a>
						<a class="btn btn-light <?php if ($id == $max_id) echo 'disabled'; ?>" href="?id=<?php echo $max_id; ?>">Last&nbsp;&rarr;</a>
					</div>
					<div class="btn-group mb-3 mr-3" role="group" aria-label="Second group">
						<a class="btn btn-light disabled" href=""><?php echo sprintf('#%04d', $id); ?></a>
					</div>
					<div class="btn-group mb-3" role="group" aria-label="Third group">
						<a class="btn btn-light <?php if (!isset($prev_id)) echo 'disabled'; ?>" href="?id=<?php if (isset($prev_id)) echo $prev_id; ?>">&laquo;&nbsp;Prev (TODO)</a>
						<a class="btn btn-light <?php if (!isset($next_id)) echo 'disabled'; ?>" href="?id=<?php if (isset($next_id)) echo $next_id; ?>">Next (TODO)&nbsp;&raquo;</a>
					</div>
				</div>
			</div>
		</div>

		<!-- BOXES -->
		<div class="container-fluid">
			<ul class="nav nav-pills brain-nav" id="pills-tab" role="tablist">
				<li class="nav-item">
					<a class="nav-link" id="pills-original-tab" data-toggle="pill" href="#pills-original" role="tab" aria-controls="pills-original" aria-selected="true"><i class="fas fa-file-alt"></i>&nbsp;ORIGINAL</a>
				</li>
				<li class="nav-item">
					<a class="nav-link active" id="pills-translation-tab" data-toggle="pill" href="#pills-translation" role="tab" aria-controls="pills-translation" aria-selected="false"><i class="fas fa-language"></i>&nbsp;TRANSLATION</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="pills-search-tab" data-toggle="pill" href="#pills-search" role="tab" aria-controls="pills-search" aria-selected="false"><i class="fas fa-search"></i>&nbsp;SEARCH</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="pills-stats-tab" data-toggle="pill" href="#pills-stats" role="tab" aria-controls="pills-stats" aria-selected="false"><i class="fas fa-chart-bar"></i>&nbsp;STATS</a>
				</li>
			</ul>
			<div class="row">
				<div class="col-md-7 col-lg-7">
					<div class="tab-content" id="pills-tabContent">
						<div class="tab-pane fade" id="pills-original" role="tabpanel" aria-labelledby="pills-original-tab">
							<?php
								try {
									$db = new SQLite3(SQLITE_FILENAME);
									if ($row = DbManager::getOriginalById($db, $id)) {
										$text = $row['text_encoded'];
										$size = $row['size'];
										$block = $row['block'];
										$id2 = $row['id2'];
										if (defined('NEWLINE_REPLACE') && NEWLINE_REPLACE && defined('NEWLINECHAR')) {
											$text = str_replace(NEWLINECHAR, '&#13;&#10;', $text);
										}
									}
									$db->close();
									unset($db);
								}
								catch (Exception $e) {
									print_r($e);
								}
							?>
							<!-- ORIGINAL - BOX -->
							<div class="card brain-card mb-3">
								<div class="card-header d-flex justify-content-between align-items-center">
									<span>ORIGINAL</span>
									<button type="submit" class="btn btn-light preview-btn" id="preview-original-btn"><i class="fas fa-eye"></i>&nbsp;PREVIEW</button>
								</div>
								<div class="card-body">
									<div class="form-group mb-0">
										<textarea rows="10" class="form-control" id="original_text" name="original_text" disabled><?php echo $text; ?></textarea>
									</div>
								</div>
								<div class="card-footer d-flex justify-content-between">
									<small>ID2:&nbsp;<?php echo $id2; ?></small>
									<small>Size:&nbsp;<?php echo $size; ?></small>
								</div>
							</div>
						</div>
						<div class="tab-pane fade show active" id="pills-translation" role="tabpanel" aria-labelledby="pills-translation-tab">
							<?php
								$db = new SQLite3(SQLITE_FILENAME);
								if ($row = DbManager::getTranslationByUserAndOriginalId($db, $uname, $id)) {
									$new_text = $row['new_text'];
									$comment = $row['comment'];
									if (defined('NEWLINE_REPLACE') && NEWLINE_REPLACE && defined('NEWLINECHAR')) {
										$new_text = str_replace(NEWLINECHAR, '&#13;&#10;', $new_text);
									}
									$status = $row['status'];
									$date = $row['date'];
								}
								$db->close();
								unset($db);
								$new_text = (isset($new_text)) ? $new_text : $text;
								$comment = (isset($comment)) ? $comment : '';
								$status = (isset($status)) ? $status : 0;
								$date = (isset($date)) ? @date('d/m/Y, G:i', $date) : 'Never been updated!';
							?>
							<!-- TRANSLATION - BOX -->
							<div class="card brain-card mb-3">
								<div class="card-header d-flex justify-content-between">
									<span>TRANSLATION</span>
									<button type="submit" class="btn btn-light preview-btn" id="preview-new-btn"><i class="fas fa-eye"></i>&nbsp;PREVIEW</button>
								</div>
								<div class="card-body">
									<form method="post" id="form1">
										<input type="hidden" name="id_text" value="<?php echo $id; ?>" />
										<?php
											switch ($status) {
												case '0':
													$class = 'btn-danger';
													break;
												case '':
													$class = 'btn-danger';
													break;
												case '1':
													$class = 'btn-warning';
													break;
												case '2':
													$class = 'btn-success';
													break;
											}
										?>
										<div class="form-group">
											<textarea rows="10" class="form-control <?php echo $class; ?>" id="new_text" name="new_text"><?php echo $new_text; ?></textarea>
										</div>
										<div class="form-group mb-0">
											<textarea rows="2" class="form-control" name="comment"><?php echo $comment; ?></textarea>
										</div>
									</form>
								</div>
								<div class="card-footer d-flex justify-content-between">
									<button type="submit" class="btn btn-danger btn-sm submit-btn" value="0"><i class="far fa-save"></i>&nbsp;UNDONE</button>
									<button type="submit" class="btn btn-warning btn-sm submit-btn" value="1"><i class="far fa-save"></i>&nbsp;ALMOST</button>
									<button type="submit" class="btn btn-success btn-sm submit-btn" value="2"><i class="far fa-save"></i>&nbsp;DONE</button>
								</div>
								<div class="card-footer">
									<small>
										Last update:&nbsp;
										<span id="lastUpdate"><?php echo $date; ?></span>
									</small>
								</div>
							</div>
						</div>
						<div class="tab-pane fade" id="pills-search" role="tabpanel" aria-labelledby="pills-search-tab">
							<!-- SEARCH BOX -->
							<div class="card brain-card mb-3">
								<div class="card-header">SEARCH</div>
								<div class="card-body">
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text" id="basic-addon0">#</span>
										</div>
										<input type="text" class="form-control" id="goto1" placeholder="Go to..." />
										<div class="input-group-append">
											<button class="btn btn-outline-light" type="button" id="go-to-btn"><i class="fas fa-external-link-alt"></i>&nbsp;Go</button>
										</div>
									</div>
								</div>
								<div class="card-body pt-0">
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text" id="basic-addon1">Original</span>
										</div>
										<input type="search" class="form-control" id="search1" placeholder="Search for..." />
										<div class="input-group-append">
											<button class="btn btn-outline-light" type="button" id="search-original-btn"><i class="fas fa-search"></i>&nbsp;Search</button>
										</div>
									</div>
								</div>
								<div class="card-body pt-0">
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text" id="basic-addon2">Translated</span>
										</div>
										<input type="search" class="form-control" id="search2" placeholder="Search for..." />
										<div class="input-group-append">
											<button class="btn btn-outline-light" type="button" id="search-new-btn"><i class="fas fa-search"></i>&nbsp;Search</button>
										</div>
									</div>
								</div>
								<div class="card-footer"id="search-result" style="display: none;"></div>
							</div>
						</div>
						<div class="tab-pane fade" id="pills-stats" role="tabpanel" aria-labelledby="pills-stats-tab">
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
							<!-- STATS BOX -->
							<div class="card brain-card mb-3">
								<div class="card-header">STATS</div>
								<ul class="list-group list-group-flush">
									<li class="list-group-item list-group-item-success d-flex justify-content-between align-items-center">
										<?php echo $done100 ?>%
										<span class="badge badge-primary badge-pill"><?php echo $done ?></span>
									</li>
									<li class="list-group-item list-group-item-warning d-flex justify-content-between align-items-center">
										<?php echo $partially100 ?>%
										<span class="badge badge-primary badge-pill"><?php echo $partially ?></span>
									</li>
									<li class="list-group-item list-group-item-danger d-flex justify-content-between align-items-center">
										<?php echo $undone100 ?>%
										<span class="badge badge-primary badge-pill"><?php echo $undone ?></span>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-5 col-lg-5">
					<!-- PEVIEW BOX -->
					<div class="card brain-card mb-3">
						<div class="card-header">PREVIEW</div>
						<div class="card-body overflow-auto" style="height: 20rem;">
							<div id="dialog-container" class="panel-body"></div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- TIPS -->
		<div class="container-fluid mb-3">
			<a class="btn btn-light btn-sm" href="http://mana.wikia.com/wiki/Seiken_Densetsu_3" target="_blank">Wiki of Mana&nbsp;<i class="fas fa-external-link-alt"></i></a>
		</div>

		<?php else: ?>

		<div class="container-fluid mt-3 bg-light">ACCESS DENIED!!! You are not authorized to access this page!</div>

	<?php endif; ?>

	<!-- TOASTS -->
	<div class="toast fixed-top" role="alert" aria-live="assertive" aria-atomic="true" id="myToast">
		<div class="toast-header">
			<strong class="mr-auto">Brainlordapps</strong>
			<button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="toast-body"></div>
	</div>

	<!-- MODALS -->
	<div class="modal" tabindex="-1" role="dialog" id="myModal">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content">
				<div class="modal-body">
					<span class="label"></span>
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">

	$(document).on('load', function() {
		sd3Cache();
	});

	$(document).ready(function() {

		$('.submit-btn').click(function(e) {
			const id_text = $('input[name="id_text"]').val();
			const new_text = $('textarea[name="new_text"]').val();
			const comment = $('textarea[name="comment"]').val();
			const status = $(this).val();
			$.ajax({
				type: 'POST',
				url: 'ajax_submit.php',
				data: {
					id_text,
					new_text,
					status,
					comment,
				}
			}).done(function(data, textStatus, jqXHR) {
				const json_data = $.parseJSON(data);
				const textarea = $('textarea[name=new_text]', '#form1');
				textarea.removeClass('btn-warning btn-danger btn-success');
				switch (status) {
					case '0':
						textarea.addClass('btn-danger');
						break;
					case '1':
						textarea.addClass('btn-warning');
						break;
					case '2':
						textarea.addClass('btn-success');
						break;
				}
				$('#lastUpdate').text(json_data.updateDate);
				$('#myToast .toast-body').text('The text has been updated with success!').removeClass('bg-danger').addClass('bg-success');
			}).fail(function(jqXHR, textStatus, errorThrown) {
				console.log(errorThrown);
				$('#myToast .toast-body').text('An error has occurred!').removeClass('bg-success').addClass('bg-danger');;
			}).always(function(a, textStatus, b) {
				//$('#myModal').modal();
				$('#myToast').toast({
					delay: 1500,
				}).toast('show');
			});
		});

		$('#form1').on('submit', function(e) {
			e.stopPropagation();
			e.preventDefault();
		});

		$('#preview-original-btn').click(function(e) {
			e.stopPropagation();
			e.preventDefault();
			$('#original_text').keyup();
		});

		$('#preview-new-btn').click(function(e) {
			e.stopPropagation();
			e.preventDefault();
			$('#new_text').keyup();
		});

		$('textarea#new_text, textarea#original_text').keyup(function(e) {
			e.stopPropagation();
			e.preventDefault();
			const text = $(this).val();
			sd3Preview('dialog-container', text);
		});

		$('#search1').keypress(function(e) {
			if (e.keyCode == '13') {
				$('#search-original-btn').click();
			}
		});

		$('#search2').keypress(function(e) {
			if (e.keyCode == '13') {
				$('#search-new-btn').click();
			}
		});

		$('#goto1').keypress(function(e) {
			if (e.keyCode == '13') {
				$('#go-to-btn').click();
			}
		});

		$('#go-to-btn').click(function(e) {
			e.stopPropagation();
			e.preventDefault();
			const id = $('#goto1').val();
			if (id && id > 0 && id < <?php echo $max_id ?>) {
				window.open(`?id=${id}`, '_blank').focus();
			} else {
				$('#myToast .toast-body').text('Index out of range!')
				$('#myToast').toast({
					delay: 1500,
				}).toast('show');
			}
		});

		$('#search-original-btn, #search-new-btn').click(function(e) {
			e.stopPropagation();
			e.preventDefault();
			const originalOrNew = ($(this).attr('id').indexOf('new') !== -1) ? 'new' : 'original';
			const text_to_search = (originalOrNew == 'new') ? $('#search2').val() : $('#search1').val();
			if (text_to_search.length > 1) {
				$.ajax({
					async: false,
					type: 'POST',
					url: 'ajax_search.php',
					data: {
						type: originalOrNew,
						text_to_search
					}
				}).done(function(data, textStatus, jqXHR) {
					$('#search-result').empty();
					const array = JSON.parse(data);
					if (array.length != 0) {
						$.each(array, function(index, value) {
							const {id, status} = value;
							const item = $('<a />').addClass('btn btn-sm mr-1 mb-1').text(id).attr('href', `?id=${id}`).attr('target', '_blank');
							if (id == <?php echo $id ?>) {
								item.addClass('disabled').removeAttr('href').removeAttr('_blank');
							} 
							(status == 2) ? item.addClass('btn-success') : (status == 1) ? item.addClass('btn-warning') : item.addClass('btn-danger');
							$('#search-result').append(item);
						});
					} else {
						$('#search-result').text('No results found!');
					}
				}).fail(function(jqXHR, textStatus, errorThrown) {
					console.log(errorThrown);
				}).always(function(a, textStatus, b) {
					$('#search-result').show();
				});
			} else {
				//
			}
		});

	});

	</script>

	</body>
</html>

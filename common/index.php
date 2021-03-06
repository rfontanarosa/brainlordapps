<?php

	header('Content-Type: text/html; charset=utf-8');

	session_start();

	require_once './config.inc.php';

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
		<link href="<?php echo APPLICATION_PATH ?>/images/favicon.ico" rel="shortcut icon" type="image/x-icon" />
		<link href="<?php echo APPLICATION_PATH ?>/images/favicon.ico" rel="apple-touch-icon" />
		<script type="text/javascript" src="/node_modules/jquery/dist/jquery.min.js"></script>
		<script type="text/javascript" src="/node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
		<script type="text/javascript"src="/node_modules/popper.js/dist/umd/popper.min.js"></script>
		<script type="text/javascript" src="/node_modules/@fortawesome/fontawesome-free/js/all.min.js"></script>
		<link rel="stylesheet" href="/node_modules/bootstrap/dist/css/bootstrap.min.css" />
		<link rel="stylesheet" href="/node_modules/@fortawesome/fontawesome-free/css/all.min.css" />
		<link rel="stylesheet" href="<?php echo APPLICATION_PATH ?>/css/bootstrap4.custom.css" type="text/css" />
		<link rel="stylesheet" href="<?php echo APPLICATION_PATH ?>/previewer/css/previewer.css" type="text/css" />
		<script type="text/javascript" src="<?php echo APPLICATION_PATH ?>/previewer/js/previewer.js" charset="UTF-8"></script>
	</head>
	<body>

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

	<?php
		$max_id = LAST_ENTRY;
		$id = isset($_GET['id']) ? $_GET['id'] : 1;
		if (!is_numeric($id)) {
			exit('<div class="m-3 p-3 bg-light">ERROR! Index is not a number!</div></body></html>');
		}
		if ($id < 1 || $id > $max_id) {
			exit('<div class="m-3 p-3 bg-light">ERROR! Index out of range!</div></body></html>');
		}
		$max_date = 0;
		$more_recent_translation = false;
	?>

	<?php if (UserManager::isLogged() && UserManager::getRole(APPLICATION_ID) == 'user'): ?>

		<?php $uname = UserManager::getUsername(); ?>

		<?php
			try {
				$db = new SQLite3(SQLITE_FILENAME);
				// PAGINATION
				$next_id = DbManager::getNextIdByUserAndId($db, $uname, $id);
				$prev_id = DbManager::getPrevIdByUserAndId($db, $uname, $id);
				// STATS
				$stats = DbManager::countByUserGroupByStatus($db, $uname);
				$partially = $stats[1];
				$done = $stats[2];
				$undone = LAST_ENTRY - ($done + $partially);
				$done100 = number_format(round(($done/$max_id)*100, 3), 1);
				$partially100 = number_format(round(($partially/$max_id)*100, 3), 1);
				$undone100 = number_format(100 - $done100 - $partially100, 1);
				// ORIGINAL
				if ($row = DbManager::getOriginalById($db, $id)) {
					$text = $row['text_decoded'];
					$size = $row['size'];
					$block = $row['block'];
					$ref = isset($row['ref']) && $row['ref'] != '' ? $row['ref'] : 'N/D';
					$text_offset = isset($row['address']) ? dechex($row['address']) : 'N/D';
					$pointers_offsets = isset($row['pointers_offsets']) ? $row['pointers_offsets'] : 'N/D';
					$other_text = $row['text'];
					if (defined('NEWLINE_REPLACE') && NEWLINE_REPLACE && defined('NEWLINECHAR')) {
						$text = str_replace(NEWLINECHAR, '&#13;&#10;', $text);
					}
				}
				// TRANSLATION
				if ($row = DbManager::getTranslationByUserAndOriginalId($db, $uname, $id)) {
					$translation = $row['translation'];
					$comment = $row['comment'];
					if (defined('NEWLINE_REPLACE') && NEWLINE_REPLACE && defined('NEWLINECHAR')) {
						$translation = str_replace(NEWLINECHAR, '&#13;&#10;', $translation);
					}
					$status = $row['status'];
					$date = $row['date'];
				}
				$translation = (isset($translation)) ? $translation : $text;
				$comment = (isset($comment)) ? $comment : '';
				$status = (isset($status)) ? $status : 0;
				$formatted_date = (isset($date)) ? @date('d/m/Y, G:i', $date) : 'Never been updated!';
				$max_date = (isset($date)) ? $date : 0;
				// DUPLICATES
				$duplicates = DbManager::countDucplicatesById($db, $id);
				// OTHERS
				$others = DbManager::getOtherTranslationByOriginalId($db, $uname, $id);
				$others_count = count($others);
				//
				$db->close();
				unset($db);
			} catch (Exception $e) {
				print_r($e);
			}
		?>

<div class="container-fluid h-100">
<div class="row h-100">

	<!-- SIDEBAR -->
	<div class="pt-3 pb-3 sidebar">
		<!-- PAGINATION -->
		<nav class="flex-column text-center mb-3">
			<a class="btn btn-light btn-sm disabled" href="?id=1">
				<?php echo sprintf('#%04d', $id); ?>
			</a>
			<a class="btn btn-light btn-sm <?php if ($id == 1) echo 'disabled'; ?>" href="?id=1">
				<i class="fas fa-fast-backward"></i>
				<small>FIRST</small>
			</a>
			<a class="btn btn-light btn-sm <?php if ($id == 1) echo 'disabled'; ?>" href="?id=<?php echo ($id > 1) ? ($id - 1) : 1; ?>" id="prev-btn">
				<i class="fas fa-step-backward"></i>
				<small>PREV</small>
				<small>Ctrl + P</small>
			</a>
			<a class="btn btn-light btn-sm <?php if ($id == $max_id) echo 'disabled'; ?>" href="?id=<?php echo ($id < $max_id) ? ($id + 1) : $max_id; ?>" id="next-btn">
				<i class="fas fa-step-forward"></i>
				<small>NEXT</small>
				<small>Ctrl + N</small>
			</a>
			<a class="btn btn-light btn-sm <?php if ($id == $max_id) echo 'disabled'; ?>" href="?id=<?php echo $max_id; ?>">
				<i class="fas fa-fast-forward"></i>
				<small>LAST</small>
			</a>
			<a class="btn btn-light btn-sm <?php if (!isset($prev_id)) echo 'disabled'; ?>" href="?id=<?php if (isset($prev_id)) echo $prev_id; ?>">
				<i class="fas fa-backward"></i>
				<small>P_TODO</small>
			</a>
			<a class="btn btn-light btn-sm <?php if (!isset($next_id)) echo 'disabled'; ?>" href="?id=<?php if (isset($next_id)) echo $next_id; ?>">
				<i class="fas fa-forward"></i>
				<small>N_TODO</small>
			</a>
		</nav>
	</div>

	<!--  -->
	<div style="width: calc(100% - 5rem);">

		<!-- BOXES -->
		<div class="container-fluid mb-3 mt-3">
			<div class="row">
				<div class="col-md-12 col-lg-8">
					<ul class="nav nav-pills brain-nav" id="pills-tab" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" id="pills-translation-tab" data-toggle="pill" href="#pills-translation" role="tab" aria-controls="pills-translation" aria-selected="false">
								<i class="fas fa-language"></i>&nbsp;TRANSLATION
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="pills-search-tab" data-toggle="pill" href="#pills-search" role="tab" aria-controls="pills-search" aria-selected="false">
								<i class="fas fa-search"></i>&nbsp;SEARCH
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="pills-others-tab" data-toggle="pill" href="#pills-others" role="tab" aria-controls="pills-others" aria-selected="false">
								<i class="fas fa-users"></i>&nbsp;OTHERS&nbsp;<span class="badge badge-primary"><?php echo $others_count ?></span>
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="pills-tools-tab" data-toggle="pill" href="#pills-tools" role="tab" aria-controls="pills-tools" aria-selected="false">
								<i class="fas fa-tools"></i>&nbsp;TOOLS
							</a>
						</li>
					</ul>
				</div>
				<div class="col-md-12 col-lg-4">
					<div class="card brain-card mb-1">
						<div class="card-body d-flex justify-content-around">
							<small><i class="fas fa-chart-bar"></i>&nbsp;STATS</small>
							<span class="badge badge-light"><i class="fas fa-globe"></i>&nbsp;<?php echo LAST_ENTRY; ?></span>
							<span class="badge badge-danger"><i class="fas fa-times"></i>&nbsp;<?php echo $undone; ?></span>
							<span class="badge badge-warning"><i class="fas fa-exclamation-triangle"></i>&nbsp;<?php echo $partially; ?></span>
							<span class="badge badge-success"><i class="fas fa-check"></i>&nbsp;<?php echo $done; ?></span>
						</div>
					</div>
				</div>
			</div>
			<div class="tab-content" id="pills-tabContent">
				<div class="tab-pane fade show active" id="pills-translation" role="tabpanel" aria-labelledby="pills-translation-tab">
					<div class="row">
						<div class="col-md-12 col-lg-6">
							<!-- TRANSLATION - BOX -->
							<div class="card brain-card mb-3">
								<div class="card-header d-flex justify-content-between">
									<span style="line-height: 1.75;">TRANSLATION</span>
									<div class="d-flex justify-content-end">
										<button type="submit" class="btn btn-light" id="paste-new-btn"><i class="fas fa-paste"></i>&nbsp;PASTE</button>
										<button type="submit" class="btn btn-light preview-btn" id="preview-new-btn" data-source-id="translation" data-dialog-container-id="dialog-container" data-id="<?php echo $id; ?>">
											<i class="fas fa-eye"></i>&nbsp;PREVIEW
										</button>
									</div>
								</div>
								<div class="card-body">
									<input type="hidden" name="id-text" value="<?php echo $id; ?>" />
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
										<textarea rows="8" class="form-control <?php echo $class; ?>" id="translation" name="translation"><?php echo $translation; ?></textarea>
									</div>
									<div class="form-group mb-0">
										<textarea rows="1" class="form-control" name="comment"><?php echo $comment; ?></textarea>
									</div>
								</div>
								<div class="card-footer d-flex justify-content-between">
									<small>
										<i class="fas fa-clock"></i>
										<span id="last-update" style="line-height: 1.5rem;"><?php echo $formatted_date; ?></span>
									</small>
									<div class="form-group mb-0">
										<div class="form-check">
											<input class="form-check-input" type="checkbox" id="extends-to-duplicates" <?php if ($duplicates > 0) echo 'checked'; ?> />
											<label class="form-check-label" for="extends-to-duplicates">Extends to <?php echo $duplicates; ?> duplicates</label>
										</div>
									</div>
								</div>
								<div class="card-footer d-flex justify-content-between">
									<input type="hidden" name="status" value="0" />
									<button type="submit" class="btn btn-danger btn-sm submit-btn" value="0"><i class="fas fa-times"></i>&nbsp;UNDONE</button>
									<button type="submit" class="btn btn-warning btn-sm submit-btn" value="1" id="partially-btn"><i class="fas fa-exclamation-triangle"></i>&nbsp;PARTIALLY<br /><small>Ctrl + A</small></button>
									<button type="submit" class="btn btn-success btn-sm submit-btn" value="2" id="done-btn"><i class="fas fa-check"></i>&nbsp;DONE<br /><small>Ctrl + D</small></button>
								</div>
							</div>
						</div>
						<div class="col-md-12 col-lg-6">
							<!-- ORIGINAL - BOX -->
							<div class="card brain-card mb-3">
								<div class="card-header d-flex justify-content-between">
									<span style="line-height: 1.75;">ORIGINAL</span>
									<div class="d-flex justify-content-end">
										<button type="submit" class="btn btn-light copy-btn" data-source-id="original-text"><i class="fas fa-copy"></i>&nbsp;COPY</button>
										<button type="submit" class="btn btn-light preview-btn" id="preview-original-btn" data-source-id="original-text" data-dialog-container-id="dialog-container" data-id="<?php echo $id; ?>">
											<i class="fas fa-eye"></i>&nbsp;PREVIEW
										</button>
									</div>
								</div>
								<div class="card-body">
									<div class="form-group mb-0">
										<textarea rows="8" class="form-control" id="original-text" name="original-text" disabled><?php echo $text; ?></textarea>
									</div>
								</div>
								<div class="card-footer d-flex justify-content-between">
									<small>Ref:&nbsp;<?php echo $ref; ?></small>
									<small>Size:&nbsp;<?php echo $size; ?></small>
									<small>Block:&nbsp;<?php echo $block; ?></small>
								</div>
								<div class="card-footer d-flex justify-content-between">
									<small>Text Offset:&nbsp;<?php echo $text_offset; ?></small>
									<small>Pointers Offsets:&nbsp;<?php echo $pointers_offsets; ?></small>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-12">
							<!-- PEVIEW BOX -->
							<div class="card brain-card">
								<div class="card-body">
									<div id="dialog-container" class="d-flex flex-row flex-wrap">PREVIEW</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="tab-pane fade" id="pills-others" role="tabpanel" aria-labelledby="pills-others-tab">
					<div class="row">
						<div class="col-md-12 col-lg-6">
							<?php
								foreach ($others as $row) {
									$author = $row['author'];
									$translation = $row['translation'];
									$comment = $row['comment'];
									if (defined('NEWLINE_REPLACE') && NEWLINE_REPLACE && defined('NEWLINECHAR')) {
										$translation = str_replace(NEWLINECHAR, '&#13;&#10;', $translation);
									}
									$status = $row['status'];
									$date = $row['date'];
									$translation = (isset($translation)) ? $translation : $text;
									$comment = (isset($comment)) ? $comment : '';
									$status = (isset($status)) ? $status : 0;
									$formatted_date = (isset($date)) ? @date('d/m/Y, G:i', $date) : 'Never been updated!';
									if ($max_date < $date) {
										$max_date = $date;
										$more_recent_translation = true;
									}
							?>
							<!-- USER - BOX -->
							<div class="card brain-card mb-3">
								<div class="card-header d-flex justify-content-between">
									<span style="line-height: 1.75;"><i class="fas fa-user"></i>&nbsp;<?php echo $author; ?></span>
									<div class="d-flex justify-content-end">
										<button type="submit" class="btn btn-light copy-btn" data-source-id="<?php echo $author; ?>_text"><i class="fas fa-copy"></i>&nbsp;COPY</button>
										<button type="submit" class="btn btn-light preview-btn" data-source-id="<?php echo $author; ?>_text" data-dialog-container-id="dialog-container1" data-id="<?php echo $id; ?>">
											<i class="fas fa-eye"></i>&nbsp;PREVIEW
										</button>
									</div>
								</div>
								<div class="card-body">
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
										<textarea rows="8" class="form-control <?php echo $class; ?>" id="<?php echo $author; ?>_text" name="<?php echo $author; ?>_text" disabled><?php echo $translation; ?></textarea>
									</div>
									<div class="form-group mb-0">
										<textarea rows="1" class="form-control" name="<?php echo $author; ?>_comment" disabled><?php echo $comment; ?></textarea>
									</div>
								</div>
								<div class="card-footer">
									<small>
										<i class="fas fa-clock"></i>
										<span><?php echo $formatted_date; ?></span>
									</small>
								</div>
							</div>
							<?php
									}
							?>
						</div>
						<div class="col-md-12 col-lg-6">
							<!-- PEVIEW BOX -->
							<div class="card brain-card">
								<div class="card-body">
									<div id="dialog-container1" class="d-flex flex-row flex-wrap">PREVIEW</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="tab-pane fade" id="pills-search" role="tabpanel" aria-labelledby="pills-search-tab">
					<div class="row">
						<div class="col-md-12 col-lg-6">
							<!-- SEARCH BOX -->
							<div class="card brain-card">
								<div class="card-header">SEARCH</div>
								<div class="card-body">
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text" id="basic-addon-id">Go to (ID)</span>
										</div>
										<input type="text" class="form-control" id="go-to" placeholder="Go to..." />
										<div class="input-group-append">
											<button class="btn btn-outline-light" type="button" id="go-to-btn"><i class="fas fa-external-link-alt"></i>&nbsp;Go</button>
										</div>
									</div>
									<div class="input-group pt-3">
										<div class="input-group-prepend">
											<span class="input-group-text" id="basic-addon0">Ref</span>
										</div>
										<input type="search" class="form-control search-input" id="search-ref" placeholder="Search for..." data-button-id="search-ref-btn" />
										<div class="input-group-append">
											<button class="btn btn-outline-light" type="button" id="search-ref-btn" data-type="ref"><i class="fas fa-search"></i>&nbsp;Search</button>
										</div>
									</div>
									<div class="form-group pt-3 mb-0">
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text" id="basic-addon1">Original</span>
											</div>
											<input type="search" class="form-control search-input" id="search-original" placeholder="Search for..." data-button-id="search-original-btn" />
											<div class="input-group-append">
												<button class="btn btn-outline-light" type="button" id="search-original-btn" data-type="original"><i class="fas fa-search"></i>&nbsp;Search</button>
											</div>
										</div>
										<div class="form-check">
											<input class="form-check-input" type="checkbox" value="" id="search-original-wwo" />
											<label class="form-check-label" for="search-original-word">Whole word only</label>
										</div>
									</div>
									<div class="form-group pt-3 mb-0">
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text" id="basic-addon2">Translated</span>
											</div>
											<input type="search" class="form-control search-input" id="search-new" placeholder="Search for..." data-button-id="search-new-btn" />
											<div class="input-group-append">
												<button class="btn btn-outline-light" type="button" id="search-new-btn" data-type="new"><i class="fas fa-search"></i>&nbsp;Search</button>
											</div>
										</div>
										<div class="form-check">
											<input class="form-check-input" type="checkbox" value="" id="search-new-wwo" />
											<label class="form-check-label" for="search-new-word">Whole word only</label>
										</div>
									</div>
									<div class="input-group pt-3">
										<div class="input-group-prepend">
											<span class="input-group-text" id="basic-addon3">Comment</span>
										</div>
										<input type="search" class="form-control search-input" id="search-comment" placeholder="Search for..." data-button-id="search-comment-btn" />
										<div class="input-group-append">
											<button class="btn btn-outline-light" type="button" id="search-comment-btn" data-type="comment"><i class="fas fa-search"></i>&nbsp;Search</button>
										</div>
									</div>
									<div class="input-group pt-3">
										<div class="input-group-prepend">
											<span class="input-group-text" id="basic-addon4">Duplicates (ID)</span>
										</div>
										<input type="search" class="form-control search-input" id="search-duplicates" placeholder="Search duplicates..." data-button-id="search-duplicates-btn" value="<?php echo $id; ?>" />
										<div class="input-group-append">
											<button class="btn btn-outline-light" type="button" id="search-duplicates-btn" data-type="duplicates"><i class="fas fa-search"></i>&nbsp;Search</button>
										</div>
									</div>
								</div>
								<div class="card-footer">
									<button class="btn btn-outline-light" type="button" id="search-personal_all-btn" data-type="personal_all"><i class="fas fa-search"></i>&nbsp;Search Personal ALL</button>
								</div>
								<div class="card-footer">
									<button class="btn btn-outline-light" type="button" id="search-global_untranslated-btn" data-type="global_untranslated"><i class="fas fa-search"></i>&nbsp;Search Global Untranslated</button>
								</div>
							</div>
						</div>
						<div class="col-md-12 col-lg-6">
							<div class="card brain-card">
								<div class="card-body" id="search-result">SEARCH RESULTS</div>
							</div>
						</div>
					</div>
				</div>
				<div class="tab-pane fade" id="pills-tools" role="tabpanel" aria-labelledby="pills-tools-tab">
					<?php if (file_exists('tools.php')): ?>
						<?php require_once("tools.php"); ?>
					<?php else: ?>
						<div class="p-3 bg-light">No tools.</div>
					<?php endif; ?>
				</div>
			</div>
		</div>

	</div>

</div>
</div>

	<?php else: ?>

		<div class="m-3 p-3 bg-light">ACCESS DENIED! You are not authorized to access this page!</div>

	<?php endif; ?>

	<!-- TOASTS -->
	<div class="toast fixed-top" role="alert" aria-live="assertive" aria-atomic="true" id="my-toast">
		<div class="toast-header">
			<strong class="mr-auto">Brainlordapps</strong>
			<button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="toast-body"></div>
	</div>

	<!-- MODALS -->
	<div class="modal" tabindex="-1" role="dialog" id="confirm-modal">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-body">
					<span>A more recent translation exists. Do you want to overwrite it?</span>
				</div>
				<div class="modal-footer p-1">
					<button type="button" class="btn btn-primary btn-sm" id="modal-confirm-btn">Save changes</button>
					<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>

	<span id="app-vars" data-max-id="<?php echo $max_id ?>" data-current-id="<?php echo $id ?>" data-more-recent-translation="<?php echo $more_recent_translation ?>" style="display: hidden;"></span>
	<script type="text/javascript" src="/common/js/app.js" charset="UTF-8"></script>

	</body>
</html>

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

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo TITLE; ?>&nbsp;-&nbsp;Translation Tool</title>
    <link href="<?php echo APPLICATION_PATH ?>/images/favicon.ico" rel="shortcut icon" type="image/x-icon">
    <link href="<?php echo APPLICATION_PATH ?>/images/favicon.ico" rel="apple-touch-icon">
    <script type="text/javascript" src="/node_modules/jquery/dist/jquery.min.js"></script>
    <script src="/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="/node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/node_modules/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo APPLICATION_PATH ?>/css/bootstrap.custom.css">
    <link rel="stylesheet" href="/node_modules/mumble-previewer/dist/css/styles.css">
    <script type="text/javascript" src="/node_modules/mumble-previewer/dist/bundle.js" charset="UTF-8"></script>
  </head>
  <body>

  <!-- NAVBAR -->
  <nav class="navbar sticky-top navbar-expand-lg brain-navbar">
    <div class="container-fluid">
      <a class="navbar-brand" href="/"><i class="bi bi-house"></i></a>
      <a class="navbar-brand" href="<?php echo APPLICATION_PATH ?>"><?php echo TITLE; ?></a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <?php if (UserManager::isLogged()): ?>
          <span class="navbar-text d-block ms-auto me-3">
            <i class="bi bi-person-circle"></i>&nbsp;<?php echo UserManager::getUsername(); ?>&nbsp;-&nbsp;<?php echo UserManager::getRole(APPLICATION_ID); ?>
          </span>
          <form method="post">
            <input name="logout" type="hidden" value="1" />
            <button class="btn btn-light btn-sm" type="submit"><i class="bi bi-box-arrow-right"></i>&nbsp;Logout</button>
          </form>
        <?php else: ?>
          <form class="d-flex ms-auto" method="post">
            <div class="me-2">
              <input class="form-control form-control-sm" name="uname" type="text" placeholder="Username" aria-label="Username" />
            </div>
            <div class="me-2">
              <input class="form-control form-control-sm" name="pass" type="password" placeholder="Password" aria-label="Password" />
            </div>
            <div>
              <button class="btn btn-light btn-sm" type="submit"><i class="bi bi-box-arrow-left"></i>&nbsp;Login</button>
            </div>
          </form>
        <?php endif; ?>
      </div>
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
					$text_offset = isset($row['address']) ? dechex((int)$row['address']) : 'N/D';
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
	<div class="py-3 px-0 sidebar">
		<!-- PAGINATION -->
		<nav class="flex-column text-center mb-3">
			<a class="btn btn-light btn-sm disabled" href="?id=1">
				<?php echo sprintf('#%04d', $id); ?>
			</a>
			<a class="btn btn-light btn-sm <?php if ($id == 1) echo 'disabled'; ?>" href="?id=1">
				<i class="bi bi-skip-backward-fill"></i>
				<small>FIRST</small>
			</a>
			<a class="btn btn-light btn-sm <?php if ($id == 1) echo 'disabled'; ?>" href="?id=<?php echo ($id > 1) ? ($id - 1) : 1; ?>" id="prev-btn">
				<i class="bi bi-arrow-left-circle-fill"></i>
				<small>PREV</small>
				<small>Ctrl + P</small>
			</a>
			<a class="btn btn-light btn-sm <?php if ($id == $max_id) echo 'disabled'; ?>" href="?id=<?php echo ($id < $max_id) ? ($id + 1) : $max_id; ?>" id="next-btn">
				<i class="bi bi-arrow-right-circle-fill"></i>
				<small>NEXT</small>
				<small>Ctrl + N</small>
			</a>
			<a class="btn btn-light btn-sm <?php if ($id == $max_id) echo 'disabled'; ?>" href="?id=<?php echo $max_id; ?>">
				<i class="bi bi-skip-forward-fill"></i>
				<small>LAST</small>
			</a>
			<a class="btn btn-light btn-sm <?php if (!isset($prev_id)) echo 'disabled'; ?>" href="?id=<?php if (isset($prev_id)) echo $prev_id; ?>">
				<i class="bi bi-skip-start-fill"></i>
				<small>P_TODO</small>
			</a>
			<a class="btn btn-light btn-sm <?php if (!isset($next_id)) echo 'disabled'; ?>" href="?id=<?php if (isset($next_id)) echo $next_id; ?>">
				<i class="bi bi-skip-end-fill"></i>
				<small>N_TODO</small>
			</a>
		</nav>
	</div>

	<!-- MAIN -->
	<div class="px-0" style="width: calc(100% - 5rem);">

		<!-- BOXES -->
		<div class="container-fluid mb-3 mt-3">
			<div class="row gx-2">
				<div class="col-md-12 col-lg-8">
          <ul class="nav nav-pills brain-nav" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="pills-translation-tab" data-bs-toggle="pill" data-bs-target="#pills-translation" type="button" role="tab" aria-controls="pills-translation" aria-selected="true"><i class="bi bi-translate"></i>&nbsp;TRANSLATION</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="pills-search-tab" data-bs-toggle="pill" data-bs-target="#pills-search" type="button" role="tab" aria-controls="pills-search" aria-selected="false"><i class="bi bi-search"></i>&nbsp;SEARCH</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="pills-others-tab" data-bs-toggle="pill" data-bs-target="#pills-others" type="button" role="tab" aria-controls="pills-others" aria-selected="false"><i class="bi bi-people-fill"></i>&nbsp;OTHERS&nbsp;<span class="badge text-bg-primary"><?php echo $others_count ?></span></button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="pills-tools-tab" data-bs-toggle="pill" data-bs-target="#pills-tools" type="button" role="tab" aria-controls="pills-tools" aria-selected="false"><i class="bi bi-tools"></i>&nbsp;TOOLS</button>
            </li>
          </ul>
				</div>
				<!-- STATS -->
				<div class="col-md-12 col-lg-4">
					<div class="card brain-card mb-1">
						<div class="card-body d-flex justify-content-around">
							<small><i class="bi bi-bar-chart-line-fill"></i>&nbsp;STATS</small>
							<span class="badge text-bg-light"><i class="bi bi-check2-all"></i>&nbsp;<?php echo LAST_ENTRY; ?></span>
							<span class="badge text-bg-danger"><i class="bi bi-x-lg"></i>&nbsp;<?php echo $undone; ?></span>
							<span class="badge text-bg-warning"><i class="bi bi-exclamation-triangle-fill"></i>&nbsp;<?php echo $partially; ?></span>
							<span class="badge text-bg-success"><i class="bi bi-check-lg"></i>&nbsp;<?php echo $done; ?></span>
						</div>
					</div>
				</div>
			</div>
			<div class="tab-content" id="pills-tabContent">
				<div class="tab-pane fade show active" id="pills-translation" role="tabpanel" aria-labelledby="pills-translation-tab" tabindex="0">
					<div class="row gx-2">
						<!-- ORIGINAL COLUMN -->
						<div class="col-md-12 col-lg-4">
							<div class="card brain-card mb-3">
								<div class="card-header d-flex justify-content-between align-items-center">
									<div>ORIGINAL</div>
									<div class="d-flex justify-content-end">
										<button type="submit" class="btn btn-light copy-btn" data-source-id="original-text"><i class="bi bi-clipboard-fill"></i>&nbsp;COPY</button>
										<button
											type="submit"
											class="btn btn-light preview-btn"
											id="preview-original-btn"
											data-source-id="original-text"
											data-preview-container-id="preview-container"
											data-game-id="<?php echo APPLICATION_ID; ?>"
											data-id="<?php echo $id; ?>"
										>
											<i class="bi bi-eye-fill"></i>&nbsp;PREVIEW
										</button>
									</div>
								</div>
								<div class="card-body">
									<textarea rows="14" class="form-control" id="original-text" name="original-text" disabled><?php echo $text; ?></textarea>
								</div>
								<div class="card-footer d-flex justify-content-between">
									<small>Ref:&nbsp;<?php echo htmlentities($ref); ?></small>
									<small>Size:&nbsp;<?php echo $size; ?></small>
									<small>Block:&nbsp;<?php echo $block; ?></small>
								</div>
								<div class="card-footer d-flex justify-content-between">
									<small>Text Offset:&nbsp;<?php echo $text_offset; ?></small>
									<small>Pointers Offsets:&nbsp;<?php echo $pointers_offsets; ?></small>
								</div>
							</div>
						</div>
						<!-- TRANSLATION COLUMN -->
						<div class="col-md-12 col-lg-4">
							<div class="card brain-card mb-3">
								<div class="card-header d-flex justify-content-between align-items-center">
									<div>TRANSLATION</div>
									<div class="d-flex justify-content-end">
										<button type="submit" class="btn btn-light" id="paste-new-btn"><i class="bi bi-clipboard"></i>&nbsp;PASTE</button>
										<button
											type="submit"
											class="btn btn-light preview-btn"
											id="preview-new-btn"
											data-source-id="translation"
											data-preview-container-id="preview-container"
											data-game-id="<?php echo APPLICATION_ID; ?>"
											data-id="<?php echo $id; ?>"
										>
											<i class="bi bi-eye-fill"></i>&nbsp;PREVIEW
										</button>
									</div>
								</div>
								<div class="card-body">
									<?php
										switch ($status) {
											case '0':
												$class = 'text-bg-danger';
												break;
											case '':
												$class = 'text-bg-danger';
												break;
											case '1':
												$class = 'text-bg-warning';
												break;
											case '2':
												$class = 'text-bg-success';
												break;
										}
									?>
									<input type="hidden" name="id-text" value="<?php echo $id; ?>" />
									<textarea rows="14" class="form-control mb-3 <?php echo $class; ?>" id="translation" name="translation"><?php echo $translation; ?></textarea>
									<textarea rows="1" class="form-control" name="comment"><?php echo $comment; ?></textarea>
								</div>
								<div class="card-footer d-flex justify-content-between align-items-center">
									<small>
										<i class="bi bi-clock-fill"></i>
										<span id="last-update"><?php echo $formatted_date; ?></span>
									</small>
									<div class="form-check">
										<input class="form-check-input" type="checkbox" id="extends-to-duplicates" <?php if ($duplicates > 0) echo 'checked'; ?> />
										<label class="form-check-label" for="extends-to-duplicates">Extends to <?php echo $duplicates; ?> duplicates</label>
									</div>
								</div>
								<div class="card-footer d-flex justify-content-between">
									<input type="hidden" name="status" value="0" />
									<button type="submit" class="btn btn-danger btn-sm submit-btn" value="0"><i class="bi bi-x-lg"></i>&nbsp;UNDONE</button>
									<button type="submit" class="btn btn-warning btn-sm submit-btn" value="1" id="partially-btn"><i class="bi bi-exclamation-triangle-fill"></i>&nbsp;PARTIALLY<br /><small>Ctrl + A</small></button>
									<button type="submit" class="btn btn-success btn-sm submit-btn" value="2" id="done-btn"><i class="bi bi-check-lg"></i>&nbsp;DONE<br /><small>Ctrl + D</small></button>
								</div>
							</div>
						</div>
						<div class="col-md-12 col-lg-4">
							<!-- PEVIEW - BOX -->
							<div class="card brain-card">
								<div class="card-header">PREVIEW</div>
								<div class="card-body">
									<div id="preview-container" class="d-flex flex-row flex-wrap justify-content-center"></div>
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
							<!-- USER - BOXES -->
							<div class="card brain-card mb-3">
								<div class="card-header d-flex justify-content-between align-items-center">
									<div><i class="bi bi-person-fill"></i>&nbsp;<?php echo $author; ?></div>
									<div class="d-flex justify-content-end">
										<button type="submit" class="btn btn-light copy-btn" data-source-id="<?php echo $author; ?>_text"><i class="bi bi-clipboard-fill"></i>&nbsp;COPY</button>
										<button
											type="submit"
											class="btn btn-light preview-btn"
											data-source-id="<?php echo $author; ?>_text"
											data-preview-container-id="preview-container-1"
											data-game-id="<?php echo APPLICATION_ID; ?>"
											data-id="<?php echo $id; ?>"
										>
											<i class="bi bi-eye-fill"></i>&nbsp;PREVIEW
										</button>
									</div>
								</div>
								<div class="card-body">
									<?php
										switch ($status) {
											case '0':
												$class = 'text-bg-danger';
												break;
											case '':
												$class = 'text-bg-danger';
												break;
											case '1':
												$class = 'text-bg-warning';
												break;
											case '2':
												$class = 'text-bg-success';
												break;
										}
									?>
									<textarea rows="8" class="form-control mb-3 <?php echo $class; ?>" id="<?php echo $author; ?>_text" name="<?php echo $author; ?>_text" disabled><?php echo $translation; ?></textarea>
									<textarea rows="1" class="form-control" name="<?php echo $author; ?>_comment" disabled><?php echo $comment; ?></textarea>
								</div>
								<div class="card-footer">
									<small>
										<i class="bi bi-clock-fill"></i>
										<span><?php echo $formatted_date; ?></span>
									</small>
								</div>
							</div>
							<?php
								}
							?>
						</div>
						<!-- PEVIEW - BOX -->
						<div class="col-md-12 col-lg-6">
							<div class="card brain-card">
								<div class="card-header">PREVIEW</div>
								<div class="card-body">
									<div id="preview-container-1" class="d-flex flex-row flex-wrap justify-content-center"></div>
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
									<div class="input-group mb-3">
										<span class="input-group-text">Go to (ID)</span>
										<input type="text" class="form-control" id="go-to" placeholder="Go to..." />
										<button class="btn btn-outline-light" type="button" id="go-to-btn"><i class="bi bi-box-arrow-in-right"></i>&nbsp;Go</button>
									</div>
									<div class="input-group mb-3">
										<span class="input-group-text">Ref</span>
										<input type="search" class="form-control search-input" id="search-ref" placeholder="Search for..." data-button-id="search-ref-btn" />
										<button class="btn btn-outline-light" type="button" id="search-ref-btn" data-type="ref"><i class="bi bi-search"></i>&nbsp;Search</button>
									</div>
									<div class="form-group mb-3">
										<div class="input-group">
											<span class="input-group-text">Original</span>
											<input type="search" class="form-control search-input" id="search-original" placeholder="Search for..." data-button-id="search-original-btn" />
											<button class="btn btn-outline-light" type="button" id="search-original-btn" data-type="original"><i class="bi bi-search"></i>&nbsp;Search</button>
										</div>
										<div class="form-check">
											<input class="form-check-input" type="checkbox" value="" id="search-original-wwo" />
											<label class="form-check-label" for="search-original-word">Whole word only</label>
										</div>
									</div>
									<div class="form-group mb-3">
										<div class="input-group">
											<span class="input-group-text">Translated</span>
											<input type="search" class="form-control search-input" id="search-new" placeholder="Search for..." data-button-id="search-new-btn" />
											<button class="btn btn-outline-light" type="button" id="search-new-btn" data-type="new"><i class="bi bi-search"></i>&nbsp;Search</button>
										</div>
										<div class="form-check">
											<input class="form-check-input" type="checkbox" value="" id="search-new-wwo" />
											<label class="form-check-label" for="search-new-word">Whole word only</label>
										</div>
									</div>
									<div class="input-group mb-3">
										<span class="input-group-text">Comment</span>
										<input type="search" class="form-control search-input" id="search-comment" placeholder="Search for..." data-button-id="search-comment-btn" />
										<button class="btn btn-outline-light" type="button" id="search-comment-btn" data-type="comment"><i class="bi bi-search"></i>&nbsp;Search</button>
									</div>
									<div class="input-group">
										<span class="input-group-text">Duplicates (ID)</span>
										<input type="search" class="form-control search-input" id="search-duplicates" placeholder="Search duplicates..." data-button-id="search-duplicates-btn" value="<?php echo $id; ?>" />
										<button class="btn btn-outline-light" type="button" id="search-duplicates-btn" data-type="duplicates"><i class="bi bi-search"></i>&nbsp;Search</button>
									</div>
								</div>
								<div class="card-footer">
									<button class="btn btn-outline-light" type="button" id="search-personal_all-btn" data-type="personal_all"><i class="bi bi-search"></i>&nbsp;Search Personal ALL</button>
								</div>
								<div class="card-footer">
									<button class="btn btn-outline-light" type="button" id="search-global_untranslated-btn" data-type="global_untranslated"><i class="bi bi-search"></i>&nbsp;Search Global Untranslated</button>
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
	<div class="toast-container position-fixed bottom-0 end-0 p-3">
		<div class="toast" role="alert" aria-live="assertive" aria-atomic="true" id="my-toast">
			<div class="toast-header">
				<strong class="me-auto">Brainlordapps</strong>
				<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
			</div>
			<div class="toast-body"></div>
		</div>
	</div>

	<!-- MODALS -->
	<div class="modal" tabindex="-1" id="confirm-modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<p>A more recent translation exists. Do you want to overwrite it?</p>
				</div>
				<div class="modal-footer p-1">
					<button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary btn-sm" id="modal-confirm-btn">Save changes</button>
				</div>
			</div>
		</div>
	</div>

	<span id="app-vars" data-max-id="<?php echo $max_id ?>" data-current-id="<?php echo $id ?>" data-more-recent-translation="<?php echo $more_recent_translation ?>" style="display: hidden;"></span>
	<script type="text/javascript" src="/common/js/app.js" charset="UTF-8"></script>

	</body>
</html>

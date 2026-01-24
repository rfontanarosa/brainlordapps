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
    <title><?php echo TITLE; ?>&nbsp;-&nbsp;Translation Tool</title>
    <link href="<?php echo APPLICATION_PATH ?>/images/favicon.ico" rel="shortcut icon" type="image/x-icon">
    <link href="<?php echo APPLICATION_PATH ?>/images/favicon.ico" rel="apple-touch-icon">
    <script src="/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="/node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/node_modules/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/bootstrap.custom.css">
    <link rel="stylesheet" href="/node_modules/mumble-previewer/dist/css/styles.css">
    <script src="/node_modules/mumble-previewer/dist/bundle.js"></script>
    <script src="/common/js/app.js"></script>
  </head>
  <body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-dark sticky-top navbar-expand-lg">
      <div class="container-fluid">
        <a class="navbar-brand" href="/"><i class="bi bi-house"></i></a>
        <a class="navbar-brand" href="<?php echo APPLICATION_PATH ?>"><?php echo TITLE; ?></a>
        <span class="navbar-brand"><img src="<?php echo APPLICATION_PATH ?>/images/favicon.png" /></span>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <?php if (UserManager::isLogged()): ?>
            <ul class="navbar-nav me-auto mb-2 mb-lg-0" id="pills-tab" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" id="pills-translation-tab" data-bs-toggle="pill" data-bs-target="#pills-translation" type="button" role="tab" aria-controls="pills-translation" aria-selected="true">
                  <i class="bi bi-translate"></i>&nbsp;TRANSLATION
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="pills-search-tab" data-bs-toggle="pill" data-bs-target="#pills-search" type="button" role="tab" aria-controls="pills-search" aria-selected="false"><i class="bi bi-search"></i>&nbsp;SEARCH</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="pills-tools-tab" data-bs-toggle="pill" data-bs-target="#pills-tools" type="button" role="tab" aria-controls="pills-tools" aria-selected="false"><i class="bi bi-tools"></i>&nbsp;TOOLS</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="pills-help-tab" data-bs-toggle="pill" data-bs-target="#pills-help" type="button" role="tab" aria-controls="pills-help" aria-selected="false"><i class="bi bi-question-lg"></i>&nbsp;HELP</a>
              </li>
            </ul>
            <span class="navbar-text d-block ms-auto me-3">
              <i class="bi bi-person-circle"></i>&nbsp;<?php echo UserManager::getUsername(); ?>&nbsp;-&nbsp;<?php echo UserManager::getRole(APPLICATION_ID); ?>
            </span>
            <form method="post">
              <input name="logout" type="hidden" value="1" />
              <button class="btn btn-primary btn-sm" type="submit"><i class="bi bi-box-arrow-right"></i>&nbsp;Logout</button>
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
                <button class="btn btn-primary btn-sm" type="submit"><i class="bi bi-box-arrow-left"></i>&nbsp;Login</button>
              </div>
            </form>
          <?php endif; ?>
        </div>
      </div>
    </nav>

    <?php if (UserManager::isLogged() && UserManager::getRole(APPLICATION_ID) == 'user'): ?>

      <?php
        $uname = UserManager::getUsername();
        $max_id = LAST_ENTRY;
        $id = isset($_GET['id']) ? $_GET['id'] : 1;
        if (!is_numeric($id)) {
          exit('<div class="m-3">ERROR! Index is not a number!</div></body></html>');
        }
        if ($id < 1 || $id > $max_id) {
          exit('<div class="m-3">ERROR! Index out of range!</div></body></html>');
        }
        $max_date = 0;
        $more_recent_translation = false;
        try {
          $db = new SQLite3(SQLITE_FILENAME);
          // PAGINATION
          $next_id = DbManager::getNextIdByUserAndId($db, $uname, $id);
          $prev_id = DbManager::getPrevIdByUserAndId($db, $uname, $id);
          // STATS
          $stats = DbManager::countByUserGroupByStatus($db, $uname);
          $in_progress = $stats[1];
          $done = $stats[2];
          $todo = LAST_ENTRY - ($done + $in_progress);
          $done100 = number_format(($done / $max_id) * 100, 1);
          $in_progress100 = number_format(($in_progress / $max_id) * 100, 1);
          $todo100 = number_format(100 - $done100 - $in_progress100, 1);
          // ORIGINAL
          if ($row = DbManager::getOriginalById($db, $id)) {
            $text = $row['text_decoded'];
            $size = $row['size'];
            // $block = $row['block'];
            $ref = isset($row['ref']) && $row['ref'] != '' ? $row['ref'] : 'N/D';
            $text_offset = isset($row['address']) ? dechex((int)$row['address']) : 'N/D';
            $pointers_offsets = isset($row['pointers_offsets']) ? $row['pointers_offsets'] : 'N/D';
            $other_text = $row['text'];
            if (defined('NEWLINE_REPLACE') && NEWLINE_REPLACE && defined('NEWLINECHAR')) {
              $text = str_replace(NEWLINECHAR, '&#13;&#10;', $text);
            }
          }
          $translations = [];
          // TRANSLATION
          if ($row = DbManager::getTranslationByUserAndOriginalId($db, $uname, $id)) {
            $translation = $row['translation'];
            $comment = $row['comment'];
            $status = $row['status'];
            $date = $row['date'];
            if (defined('NEWLINE_REPLACE') && NEWLINE_REPLACE && defined('NEWLINECHAR')) {
              $translation = str_replace(NEWLINECHAR, '&#13;&#10;', $translation);
            }
          }
          $translations[$uname] = [
            'translation' => (isset($translation)) ? $translation : $text,
            'comment' => (isset($comment)) ? $comment : '',
            'status'=> (isset($status)) ? $status : 0,
            'formatted_date' => (isset($date)) ? @date('d/m/Y, G:i', $date) : 'Never been updated!'
          ];
          $max_date = (isset($date)) ? $date : 0;
          // DUPLICATES
          $duplicates = DbManager::countDucplicatesById($db, $id);
          // OTHERS
          $others = DbManager::getOtherTranslationByOriginalId($db, $uname, $id);
          foreach ($others as $row) {
            $author = $row['author'];
            $translation = $row['translation'];
            $comment = $row['comment'];
            $status = $row['status'];
            $date = $row['date'];
            if (defined('NEWLINE_REPLACE') && NEWLINE_REPLACE && defined('NEWLINECHAR')) {
              $translation = str_replace(NEWLINECHAR, '&#13;&#10;', $translation);
            }
            $translations[$author] = [
              'translation' => (isset($translation)) ? $translation : $text,
              'comment' => (isset($comment)) ? $comment : '',
              'status'=> (isset($status)) ? $status : 0,
              'formatted_date' => (isset($date)) ? @date('d/m/Y, G:i', $date) : 'Never been updated!'
            ];
            if ($max_date < $date) {
              $max_date = $date;
              $more_recent_translation = true;
            }
          }
          $others_count = count($others);
          //
          $db->close();
          unset($db);
        } catch (Exception $e) {
          print_r($e);
        }
      ?>

      <!-- MAIN -->
      <div class="container-fluid h-100">
        <div class="row h-100">
          <div class="px-0">
            <div class="container-fluid mb-2 mt-2">
              <div class="row mb-2 gx-2">
                <!-- PAGINATION -->
                <div class="col-md-12 col-lg-4 brain-paginator">
                  <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip" data-bs-title="Go to FIRST entry">
                    <a class="btn btn-primary btn-sm <?php if ($id == 1) echo 'disabled'; ?>" href="?id=1">
                      <i class="bi bi-skip-backward-fill"></i>
                    </a>
                  </span>
                  <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip" data-bs-title="Go to PREVIOUS TODO entry">
                    <a class="btn btn-primary btn-sm <?php if (!isset($prev_id)) echo 'disabled'; ?>" href="?id=<?php if (isset($prev_id)) echo $prev_id; ?>">
                      <i class="bi bi-skip-start-fill"></i>
                    </a>
                  </span>
                  <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip" data-bs-title="Go to PREVIOUS entry">
                    <a class="btn btn-primary btn-sm <?php if ($id == 1) echo 'disabled'; ?>" href="?id=<?php echo ($id > 1) ? ($id - 1) : 1; ?>" id="prev-btn">
                      <i class="bi bi-arrow-left-circle-fill"></i>
                    </a>
                  </span>
                  <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip" data-bs-title="Go to NEXT entry">
                    <a class="btn btn-primary btn-sm <?php if ($id == $max_id) echo 'disabled'; ?>" href="?id=<?php echo ($id < $max_id) ? ($id + 1) : $max_id; ?>" id="next-btn">
                      <i class="bi bi-arrow-right-circle-fill"></i>
                    </a>
                  </span>
                  <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip" data-bs-title="Go to NEXT TODO entry">
                    <a class="btn btn-primary btn-sm <?php if (!isset($next_id)) echo 'disabled'; ?>" href="?id=<?php if (isset($next_id)) echo $next_id; ?>">
                      <i class="bi bi-skip-end-fill"></i>
                    </a>
                  </span>
                  <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip" data-bs-title="Go to LAST entry">
                    <a class="btn btn-primary btn-sm <?php if ($id == $max_id) echo 'disabled'; ?>" href="?id=<?php echo $max_id; ?>">
                      <i class="bi bi-skip-forward-fill"></i>
                    </a>
                  </span>
                </div>
                <!-- TRANSLATION ID -->
                <div class="col-md-12 col-lg-4 brain-translation-id">
                  <span><?php echo sprintf('#%04d', $id); ?></span>
                </div>
                <!-- STATS -->
                <div class="col-md-12 col-lg-4 brain-stats">
                  <small><i class="bi bi-bar-chart-line-fill"></i>&nbsp;STATS</small>
                  <span class="badge"><?php echo LAST_ENTRY; ?></span>
                  <span class="badge"><i class="bi-x-circle-fill text-danger"></i>&nbsp;<?php echo $todo . ' - ' . $todo100 . '%'; ?></span>
                  <span class="badge"><i class="bi-exclamation-diamond-fill text-warning"></i>&nbsp;<?php echo $in_progress . ' - ' . $in_progress100 . '%'; ?></span>
                  <span class="badge"><i class="bi-check-square-fill text-success"></i>&nbsp;<?php echo $done . ' - ' . $done100 . '%'; ?></span>
                </div>
              </div>
              <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-translation" role="tabpanel" aria-labelledby="pills-translation-tab" tabindex="0">
                  <div class="row gx-2">
                    <!-- ORIGINAL COLUMN -->
                    <div class="col-md-12 col-lg-4">
                      <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center gap-3">
                          <div>ORIGINAL</div>
                          <div class="d-flex gap-3">
                            <span tabindex="0" data-bs-toggle="tooltip" data-bs-title="Copy to clipboard">
                              <button class="btn btn-primary" id="copy-btn-original" type="submit">
                                <i class="bi bi-clipboard-fill"></i>
                              </button>
                            </span>
                            <span tabindex="0" data-bs-toggle="tooltip" data-bs-title="Show preview">
                              <button class="btn btn-primary preview-btn" id="preview-btn-original" type="submit">
                                <i class="bi bi-eye-fill"></i>
                              </button>
                            </span>
                          </div>
                        </div>
                        <div class="card-body">
                          <textarea rows="14" class="form-control" id="original-text" disabled><?php echo "\n" . $text; ?></textarea>
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                          <small>Ref:&nbsp;<?php echo htmlentities($ref); ?></small>
                          <small>Size:&nbsp;<?php echo $size; ?></small>
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                          <small>Text Offset:&nbsp;<?php echo $text_offset; ?></small>
                          <small>Pointers Offsets:&nbsp;<?php echo $pointers_offsets; ?></small>
                        </div>
                      </div>
                    </div>
                    <!-- TRANSLATION COLUMN -->
                    <div class="col-md-12 col-lg-4">
                      <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center gap-3">
                          <div>TRANSLATION (<?php echo $others_count ?>)</div>
                          <select id="select-translator" class="form-select form-select-sm" style="flex: 1;">
                            <?php foreach ($translations as $author => $translation): ?>
                              <option value="<?php echo $author; ?>"><?php echo $author; ?></option>;
                            <?php endforeach; ?>
                          </select>
                          <div class="d-flex gap-3">
                            <span tabindex="0" data-bs-toggle="tooltip" data-bs-title="Copy to clipboard">
                              <button class="btn btn-primary" id="copy-btn" type="submit">
                                <i class="bi bi-clipboard-fill"></i>
                              </button>
                            </span>
                            <span tabindex="0" data-bs-toggle="tooltip" data-bs-title="Paste from clipboard">
                              <button class="btn" id="paste-btn" type="submit">
                                <i class="bi bi-clipboard-plus-fill"></i>
                              </button>
                            </span>
                            <span tabindex="0" data-bs-toggle="tooltip" data-bs-title="Show preview">
                              <button class="btn btn-primary" id="preview-btn" type="submit">
                                <i class="bi bi-eye-fill"></i>
                              </button>
                            </span>
                          </div>
                        </div>
                        <?php foreach ($translations as $author => $translation): ?>
                          <?php $class = ($author == $uname) ? '' : ' d-none'; ?>
                          <span class="card-block card-block-<?php echo $author . $class; ?>">
                            <div class="card-body">
                              <?php
                                switch ($translation['status']) {
                                  case '1':
                                    $classes = 'bi-exclamation-diamond-fill text-warning';
                                    break;
                                  case '2':
                                    $classes = 'bi-check-square-fill text-success';
                                    break;
                                  default:
                                    $classes = 'bi-x-circle-fill text-danger';
                                }
                              ?>
                              <?php if ($author == $uname): ?>
                                <input type="hidden" name="id-text" value="<?php echo $id; ?>" />
                                <textarea rows="14" class="form-control mb-3" name="translation" id="translation"><?php echo "\n" . $translation['translation']; ?></textarea>
                                <textarea rows="1" class="form-control" name="comment"><?php echo $translation['comment']; ?></textarea>
                              <?php else: ?>
                                <textarea rows="14" class="form-control mb-3" name="translation" disabled><?php echo "\n" . $translation['translation']; ?></textarea>
                                <textarea rows="1" class="form-control" name="comment" disabled><?php echo $translation['comment']; ?></textarea>
                              <?php endif; ?>
                            </div>
                            <div class="card-footer d-flex justify-content-between align-items-center">
                              <small>
                                <i class="bi bi-clock-fill"></i>
                                <span id="last-update"><?php echo $translation['formatted_date']; ?></span>
                              </small>
                              <small>
                                Status:&nbsp;
                                <i id="translation-status" class="bi <?php echo $classes; ?>"></i>
                              </small>
                            </div>
                            <?php if ($author == $uname): ?>
                              <div class="card-footer d-flex justify-content-between align-items-center">
                                <input type="hidden" name="status" value="0" />
                                <button type="submit" class="btn btn-primary btn-sm submit-btn" value="0" style="width: 30%"><i class="bi-x-circle-fill text-danger"></i>&nbsp;TODO</button>
                                <button type="submit" class="btn btn-primary btn-sm submit-btn" value="1" style="width: 30%" id="in-progress-btn"><i class="bi-exclamation-diamond-fill text-warning"></i>&nbsp;IN PROGRESS</button>
                                <button type="submit" class="btn btn-primary btn-sm submit-btn" value="2" style="width: 30%" id="done-btn"><i class="bi-check-square-fill text-success"></i>&nbsp;DONE</button>
                              </div>
                              <div class="card-footer d-flex justify-content-between align-items-center">
                                <div class="form-check">
                                  <input class="form-check-input" type="checkbox" id="extends-to-duplicates" <?php if ($duplicates > 0) echo 'checked'; ?> />
                                  <label class="form-check-label" for="extends-to-duplicates">Extends to <?php echo $duplicates; ?> duplicates</label>
                                </div>
                              </div>
                            <?php endif; ?>
                          </span>
                        <?php endforeach; ?>
                      </div>
                    </div>
                    <!-- PREVIEW COLUMN -->
                    <div class="col-md-12 col-lg-4">
                      <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center gap-3">
                          <div>PREVIEW</div>
                          <select class="form-select form-select-sm" style="flex: 1;">
                            <option value="">Default</option>
                          </select>
                        </div>
                        <div class="card-body">
                          <div id="preview-container" class="d-flex flex-row flex-wrap justify-content-center gap-3"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="tab-pane fade" id="pills-search" role="tabpanel" aria-labelledby="pills-search-tab">
                  <div class="row">
                    <!-- SEARCH COLUMN -->
                    <div class="col-md-12 col-lg-6">
                      <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">SEARCH</div>
                        <div class="card-body">
                          <div class="input-group mb-3">
                            <span class="input-group-text">Go to (ID)</span>
                            <input type="search" class="form-control search-input" id="go-to" placeholder="Go to..." data-button-id="go-to-btn" />
                            <button class="btn btn-primary" id="go-to-btn" type="button">
                              <i class="bi bi-box-arrow-in-right"></i>&nbsp;Go
                            </button>
                          </div>
                          <div class="input-group mb-3">
                            <span class="input-group-text">Ref</span>
                            <input type="search" class="form-control search-input" id="search-ref" placeholder="Search for..." data-button-id="search-ref-btn" />
                            <button class="btn btn-primary search-btn" id="search-ref-btn" data-type="ref" type="button">
                              <i class="bi bi-search"></i>&nbsp;Search
                            </button>
                          </div>
                          <div class="form-group mb-3">
                            <div class="input-group mb-1">
                              <span class="input-group-text">Original</span>
                              <input type="search" class="form-control search-input" id="search-original" placeholder="Search for..." data-button-id="search-original-btn" />
                              <button class="btn btn-primary search-btn" id="search-original-btn" data-type="original" type="button">
                                <i class="bi bi-search"></i>&nbsp;Search
                              </button>
                            </div>
                            <div class="form-check">
                              <input class="form-check-input" type="checkbox" value="" id="search-original-wwo" />
                              <label class="form-check-label" for="search-original-word">Whole word only</label>
                            </div>
                          </div>
                          <div class="form-group mb-3">
                            <div class="input-group mb-1">
                              <span class="input-group-text">Translated</span>
                              <input type="search" class="form-control search-input" id="search-new" placeholder="Search for..." data-button-id="search-new-btn" />
                              <button class="btn btn-primary search-btn" id="search-new-btn" data-type="new" type="button">
                                <i class="bi bi-search"></i>&nbsp;Search
                              </button>
                            </div>
                            <div class="form-check">
                              <input class="form-check-input" type="checkbox" value="" id="search-new-wwo" />
                              <label class="form-check-label" for="search-new-word">Whole word only</label>
                            </div>
                          </div>
                          <div class="input-group mb-3">
                            <span class="input-group-text">Comment</span>
                            <input type="search" class="form-control search-input" id="search-comment" placeholder="Search for..." data-button-id="search-comment-btn" />
                            <button class="btn btn-primary search-btn" id="search-comment-btn" data-type="comment" type="button">
                              <i class="bi bi-search"></i>&nbsp;Search
                            </button>
                          </div>
                          <div class="input-group">
                            <span class="input-group-text">Duplicates (ID)</span>
                            <input type="search" class="form-control search-input" id="search-duplicates" placeholder="Search duplicates..." data-button-id="search-duplicates-btn" value="<?php echo $id; ?>" />
                            <button class="btn btn-primary search-btn" id="search-duplicates-btn" data-type="duplicates" type="button">
                              <i class="bi bi-search"></i>&nbsp;Search
                            </button>
                          </div>
                        </div>
                        <div class="card-footer d-flex align-items-center gap-3">
                          <div style="flex: 1;"><i class="bi bi-search"></i>&nbsp;Search Personal</div>
                          <button class="btn btn-primary search-btn" data-type="personal_all" type="button">
                            <i class="bi bi-globe"></i>&nbsp;ALL
                          </button>
                          <button class="btn btn-primary search-btn" data-type="personal_todo" type="button">
                            <i class="bi bi-x-circle-fill text-danger"></i>&nbsp;TODO
                          </button>
                          <button class="btn btn-primary search-btn" data-type="personal_in_progress" type="button">
                            <i class="bi bi-exclamation-diamond-fill text-warning"></i>&nbsp;IN PROGRESS
                          </button>
                          <button class="btn btn-primary search-btn" data-type="personal_done" type="button">
                            <i class="bi bi-check-square-fill text-success"></i>&nbsp;DONE
                          </button>
                        </div>
                        <div class="card-footer card-footer d-flex align-items-center gap-3">
                        <div style="flex: 1;"><i class="bi bi-search"></i>&nbsp;Search Global</div>
                          <button class="btn btn-primary search-btn" data-type="global_untranslated" type="button">
                            <i class="bi bi-search"></i>&nbsp;Untranslated
                          </button>
                        </div>
                      </div>
                    </div>
                    <!-- SEARCH RESULTS COLUMN -->
                    <div class="col-md-12 col-lg-6">
                      <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">SEARCH RESULTS</div>
                        <div class="card-body d-flex flex-wrap" id="search-results"></div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="tab-pane fade" id="pills-tools" role="tabpanel" aria-labelledby="pills-tools-tab">
                  <?php if (file_exists('tools.php')): ?>
                    <?php require_once("tools.php"); ?>
                  <?php else: ?>
                    <div class="p-3">No tools.</div>
                  <?php endif; ?>
                </div>
                <div class="tab-pane fade" id="pills-help" role="tabpanel" aria-labelledby="pills-help-tab">
                  <div class="row">
                    <div class="col-md-12 col-lg-6">
                      <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">SHORTCUTS</div>
                        <div class="card-body">
                        <ul class="list-group">
                          <li class="list-group-item">Ctrl + P - Previous</li>
                          <li class="list-group-item">Ctrl + N - Next</li>
                          <li class="list-group-item">Ctrl + A - In progress</li>
                          <li class="list-group-item">Ctrl + D - Done</li>
                        </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <span
        id="app-vars"
        data-current-id="<?php echo $id ?>"
        data-max-id="<?php echo $max_id ?>"
        data-more-recent-translation="<?php echo $more_recent_translation ?>"
        data-username="<?php echo $uname; ?>"
        data-game-id="<?php echo defined('PREVIEWER_ID') ? PREVIEWER_ID : APPLICATION_ID; ?>"
        style="display: hidden;">
      </span>

    <?php else: ?>
      <div class="m-3">ACCESS DENIED! You are not authorized to access this page!</div>
    <?php endif; ?>

    <!-- TOAST -->
    <div aria-live="polite" aria-atomic="true" class="position-relative">
      <div class="toast-container bottom-0 end-0 p-3">
        <div class="toast" id="my-toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-theme="dark">
          <div class="toast-header">
            <strong class="me-auto">Brainlordapps</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
          </div>
          <div class="toast-body"></div>
        </div>
      </div>
    </div>

    <!-- MODAL -->
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

    <script>
      document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('preview-btn').click();
      });
    </script>

  </body>
</html>

<div class="row">
	<div class="col-md-12 col-lg-6">
		<form action="export_dump.php" method="post" class="card brain-card">
			<div class="card-header d-flex justify-content-between align-items-center gap-3">
				<div>EXPORT</div>
				<select id="export-filename" name="filename" class="form-select form-select-sm" style="flex: 1;">
					<?php foreach ($files as $file): ?>
						<option value="<?php echo htmlspecialchars($file['filename']); ?>"><?php echo htmlspecialchars($file['filename']); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="card-body">
				<button class="btn btn-primary" type="submit" name="type" value="1"><i class="fas fa-file-export"></i>&nbsp;Export ORIGINAL dump</button>
			</div>
			<div class="card-footer">
				<button class="btn btn-primary" type="submit" name="type" value="2"><i class="fas fa-file-export"></i>&nbsp;Export YOUR translation</button>
			</div>
			<div class="card-footer">
				<button class="btn btn-primary" type="submit" name="type" value="3"><i class="fas fa-file-export"></i>&nbsp;Export MORE RECENT translation</button>
			</div>
		</form>
	</div>
</div>

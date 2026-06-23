<div class="row">
	<div class="col-md-12 col-lg-6">
		<div class="card brain-card">
			<div class="card-header d-flex justify-content-between align-items-center">TOOLS</div>
			<form action="export_dump.php" method="post">
				<input type="hidden" name="block" value="0" />
				<div class="card-body">
					<label for="export-filename" class="form-label">Filename</label>
					<select class="form-select mb-3" id="export-filename" name="filename">
						<?php foreach ($files as $file): ?>
							<option value="<?php echo htmlspecialchars($file['filename']); ?>"><?php echo htmlspecialchars($file['filename']); ?></option>
						<?php endforeach; ?>
					</select>
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
	<div class="col-md-12 col-lg-6">
		<div class="card brain-card">
			<div class="card-body" id="tool-result">TOOL RESULTS</div>
		</div>
	</div>
</div>

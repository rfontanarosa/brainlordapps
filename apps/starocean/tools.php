<div class="row">
	<div class="col-md-12 col-lg-6">
		<div class="card brain-card">
			<div class="card-header">TOOLS</div>
			<div class="card-body">
				<form action="tool-export-dump.php" method="post">
					<input type="hidden" name="type" value="1" />
					<input type="hidden" name="block" value="0" />
					<button class="btn btn-outline-light" type="submit"><i class="fas fa-file-export"></i>&nbsp;Export ORIGINAL dump</button>
				</form>
			</div>
			<div class="card-footer">
				<form action="tool-export-dump.php" method="post">
					<input type="hidden" name="type" value="2" />
					<input type="hidden" name="block" value="0" />
					<button class="btn btn-outline-light" type="submit"><i class="fas fa-file-export"></i>&nbsp;Export YOUR translation</button>
				</form>
			</div>
			<div class="card-footer">
				<form action="tool-export-dump.php" method="post">
					<input type="hidden" name="type" value="3" />
					<input type="hidden" name="block" value="0" />
					<button class="btn btn-outline-light" type="submit"><i class="fas fa-file-export"></i>&nbsp;Export MORE RECENT translation</button>
				</form>
			</div>
		</div>
	</div>
	<div class="col-md-12 col-lg-6">
		<div class="card brain-card">
			<div class="card-body" id="tool-result">TOOL RESULTS</div>
		</div>
	</div>
</div>

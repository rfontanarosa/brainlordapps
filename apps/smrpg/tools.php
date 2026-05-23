<div class="row">
	<div class="col-md-12 col-lg-6">
		<div class="card brain-card">
			<div class="card-header d-flex justify-content-between align-items-center">TOOLS</div>
			<div class="card-body">
				<form action="tool-export-dialogues.php" method="post">
					<input type="hidden" name="block" value="1" />
					<button class="btn btn-outline-light" type="submit"><i class="fas fa-file-export"></i>&nbsp;Export dialogues.txt</button>
				</form>
			</div>
			<div class="card-footer">
				<form action="tool-export-dialogues.php" method="post">
					<input type="hidden" name="block" value="2" />
					<button class="btn btn-outline-light" type="submit"><i class="fas fa-file-export"></i>&nbsp;Export battleDialogues.txt</button>
				</form>
			</div>
			<div class="card-footer">
				<form action="tool-export-dialogues.php" method="post">
					<input type="hidden" name="block" value="1" />
					<input type="hidden" name="type" value="2" />
					<button class="btn btn-outline-light" type="submit"><i class="fas fa-file-export"></i>&nbsp;Export MORE RECENT dialogues.txt</button>
				</form>
			</div>
			<div class="card-footer">
				<form action="tool-export-dialogues.php" method="post">
					<input type="hidden" name="block" value="2" />
					<input type="hidden" name="type" value="2" />
					<button class="btn btn-outline-light" type="submit"><i class="fas fa-file-export"></i>&nbsp;Export MORE RECENT battleDialogues.txt</button>
				</form>
			</div>
		</div>
	</div>
</div>
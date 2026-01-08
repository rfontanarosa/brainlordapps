<div class="row">
	<div class="col-md-12 col-lg-6">
		<div class="card brain-card">
			<div class="card-header d-flex justify-content-between align-items-center">TOOLS</div>
			<div class="card-body">
				<button class="btn btn-outline-light" type="button" id="find-invalid-text-btn"><i class="fas fa-bug"></i>&nbsp;Find too long texts</button>
			</div>
			<div class="card-footer">
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
	<div class="col-md-12 col-lg-6">
		<div class="card brain-card">
			<div class="card-body" id="tool-result">TOOL RESULTS</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function() {
		$('#find-invalid-text-btn').click(function(e) {
			e.stopPropagation();
			e.preventDefault();
			$.ajax({
				async: false,
				type: 'GET',
				url: 'tool-find-invalid-text.php',
			}).done(function(data, textStatus, jqXHR) {
				console.log(data);
				const array = JSON.parse(data);
				$('#tool-result').empty();
				if (array.length != 0) {
					$('#tool-result').append($('<div />').addClass('mb-3').text(`Results found: ${array.length}`));
					const template = $('<a />').addClass('btn btn-sm btn-primary mr-1 mb-1').attr('target', '_blank');
					const items = array.map(value => {
						const {id} = value;
						const item = template.clone().text(id).attr('href', `?id=${id}`);
						return item;
					});
					$('#tool-result').append(items);
				} else {
					$('#tool-result').text('No results found!');
				}
			}).fail(function(jqXHR, textStatus, errorThrown) {
				console.log(errorThrown);
				$('#tool-result').empty();
				$('#tool-result').text('An error has occurred!');
			}).always(function(a, textStatus, b) {
				$('#tool-result').show();
			});
		});
	});
</script>
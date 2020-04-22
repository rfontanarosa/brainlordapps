function renderPreview(previewContainerSelector, text) {
  $.ajax({
    async: false,
    type: 'POST',
    url: 'preview.php',
    data: {
      text,
    }
  }).done(function(data, textStatus, jqXHR) {
    $('#dialog-container').empty();
    $('#dialog-container').html(data);
  });
};

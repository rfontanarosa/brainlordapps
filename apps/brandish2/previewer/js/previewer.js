function renderPreview(previewContainerSelector, text, id, type) {
  console.log(id, text);
  $.ajax({
    async: false,
    type: 'POST',
    url: 'preview.php',
    data: {
      text,
      id,
      type,
    }
  }).done(function(data, textStatus, jqXHR) {
    $('#' + previewContainerSelector).empty();
    $('#' + previewContainerSelector).html(data);
  });
};

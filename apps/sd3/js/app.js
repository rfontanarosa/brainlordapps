$(document).ready(function() {

  const max_id = parseInt($('#app-vars').attr('data-max-id'));
  const current_id = parseInt($('#app-vars').attr('data-current-id'));
  let more_recent_translation = $('#app-vars').attr('data-more-recent-translation') === '1';

  const submit = () => {
    const id_text = $('input[name="id_text"]').val();
    const new_text = $('textarea[name="new_text"]').val();
    const comment = $('textarea[name="comment"]').val();
    const status = $('input[name="status"]').val();
    const extends_to_duplicates = $('#extendsToDuplicates').is(':checked');
    $.ajax({
      type: 'POST',
      url: 'ajax_submit.php',
      data: {
        id_text,
        new_text,
        status,
        comment,
        extends_to_duplicates,
      },
    }).done(function(data, textStatus, jqXHR) {
      const json_data = JSON.parse(data);
      const textarea = $('textarea[name=new_text]');
      textarea.removeClass('btn-warning btn-danger btn-success');
      switch (status) {
        case '0':
          textarea.addClass('btn-danger');
          break;
        case '1':
          textarea.addClass('btn-warning');
          break;
        case '2':
          textarea.addClass('btn-success');
          break;
      }
      $('#lastUpdate').text(json_data.updateDate);
      more_recent_translation = false;
      $('#myToast .toast-body').text('The text has been updated with success!').removeClass('bg-danger').addClass('bg-success');
    }).fail(function(jqXHR, textStatus, errorThrown) {
      console.log(errorThrown);
      $('#myToast .toast-body').text('An error has occurred!').removeClass('bg-success').addClass('bg-danger');;
    }).always(function(a, textStatus, b) {
      $('#confirm-modal').modal('hide');
      $('#myToast').toast({
        delay: 1500,
      }).toast('show');
    });
  }; 

  $('#modal-confirm-btn').click(function(e) {
    submit();
  });

  $('.submit-btn').click(function(e) {
    const status = $(this).val();
    $('input[name="status"]').val(status);
    more_recent_translation ? $('#confirm-modal').modal() : submit();
  });

  $('.preview-btn').click(function(e) {
    e.stopPropagation();
    e.preventDefault();
    if (typeof renderPreview === 'function') {
      const sourceId = e.target.getAttribute('data-source-id');
      const dialogContainerId = e.target.getAttribute('data-dialog-container-id');
      const text = document.getElementById(sourceId).value;
      renderPreview(dialogContainerId, text);
    } else {
      console.log('renderPreview is not defined!');
    }
  });

  $('.copy-btn').click(function(e) {
    e.stopPropagation();
    e.preventDefault();
    const sourceId = e.target.getAttribute('data-source-id');
    const text = document.getElementById(sourceId).value;
    navigator.clipboard.writeText(text).then(function() {
      /* clipboard successfully set */
    }, function() {
      /* clipboard write failed */
    });
  });

  $('#paste-new-btn').click(function(e) {
    e.stopPropagation();
    e.preventDefault();
    navigator.clipboard.readText().then(clipText => {
      document.getElementById("new_text").value = clipText;
      $('#new_text').keyup();
    });
  });

  $('textarea#new_text').keyup(function(e) {
    e.stopPropagation();
    e.preventDefault();
    $('#preview-new-btn').click();
  });

  $('.search-btn').keypress(function(e) {
    if (e.keyCode === 13) {
      const buttonId = $(this).attr('data-btn-id');
      $('#' + buttonId).click();
    }
  });

  $('#go-to').keypress(function(e) {
    if (e.keyCode === 13) {
      $('#go-to-btn').click();
    }
  });

  $('#go-to-btn').click(function(e) {
    e.stopPropagation();
    e.preventDefault();
    const id = parseInt(document.getElementById('go-to').value);
    if (!isNaN(id) && id > 0 && id < max_id) {
      window.open(`?id=${id}`, '_blank').focus();
    } else {
      $('#myToast .toast-body').text('Index out of range!')
      $('#myToast').toast({
        delay: 1500,
      }).toast('show');
    }
  });

  $('#search-id2-btn, #search-original-btn, #search-new-btn, #search-comment-btn, #search-duplicates-btn, #search-personal_all-btn, #search-global_untranslated-btn').click(function(e) {
    e.stopPropagation();
    e.preventDefault();
    const type = $(this).attr('data-type');
    let text_to_search = undefined;
    switch (type) {
      case 'id2':
        text_to_search = $('#search-id2').val();
        break;
      case 'original':
        text_to_search = $('#search-original').val();
        break;
      case 'new':
        text_to_search = $('#search-new').val();
        break;
      case 'comment':
        text_to_search = $('#search-comment').val();
        break;
      case 'duplicates':
        text_to_search = $('#search-duplicates').val();
        break;
    }
    if (text_to_search === undefined || text_to_search.length > 1) {
      $.ajax({
        async: false,
        type: 'POST',
        url: 'ajax_search.php',
        data: {
          type,
          text_to_search,
        },
      }).done(function(data, textStatus, jqXHR) {
        const array = JSON.parse(data);
        $('#search-result').empty();
        if (array.length != 0) {
          $('#search-result').append($('<div />').addClass('mb-3').text(`Results found: ${array.length}`));
          const template = $('<a />').addClass('btn btn-sm mr-1 mb-1').attr('target', '_blank');
          const items = array.map(value => {
            const {id, status} = value;
            const item = template.clone().text(id).attr('href', `?id=${id}`);
            if (id === current_id) {
              item.addClass('disabled').removeAttr('href').removeAttr('_blank');
            }
            (status === 2) ? item.addClass('btn-success') : (status === 1) ? item.addClass('btn-warning') : item.addClass('btn-danger');
            return item;
          });
          $('#search-result').append(items);
        } else {
          $('#search-result').text('No results found!');
        }
      }).fail(function(jqXHR, textStatus, errorThrown) {
        console.log(errorThrown);
        $('#search-result').empty();
        $('#search-result').text('An error has occurred!');
      }).always(function(a, textStatus, b) {
        $('#search-result').show();
      });
    } else {
      $('#search-result').empty();
      $('#search-result').text('Invalid or empty input!');
      $('#search-result').show();
    }
  });

});

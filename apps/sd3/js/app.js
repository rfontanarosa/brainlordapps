$(document).ready(function() {

  const max_id = parseInt($('#app-vars').attr('data-max-id'));
  const current_id = parseInt($('#app-vars').attr('data-current-id'));

  $('.submit-btn').click(function(e) {
    const id_text = $('input[name="id_text"]').val();
    const new_text = $('textarea[name="new_text"]').val();
    const comment = $('textarea[name="comment"]').val();
    const status = $(this).val();
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
      const json_data = $.parseJSON(data);
      const textarea = $('textarea[name=new_text]', '#form1');
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
      $('#myToast .toast-body').text('The text has been updated with success!').removeClass('bg-danger').addClass('bg-success');
    }).fail(function(jqXHR, textStatus, errorThrown) {
      console.log(errorThrown);
      $('#myToast .toast-body').text('An error has occurred!').removeClass('bg-success').addClass('bg-danger');;
    }).always(function(a, textStatus, b) {
      //$('#myModal').modal();
      $('#myToast').toast({
        delay: 1500,
      }).toast('show');
    });
  });

  $('#form1').on('submit', function(e) {
    e.stopPropagation();
    e.preventDefault();
  });

  $('#preview-original-btn').click(function(e) {
    e.stopPropagation();
    e.preventDefault();
    $('#original_text').keyup();
  });

  $('#preview-new-btn').click(function(e) {
    e.stopPropagation();
    e.preventDefault();
    $('#new_text').keyup();
  });

  $('textarea#new_text, textarea#original_text').keyup(function(e) {
    e.stopPropagation();
    e.preventDefault();
    if (typeof renderPreview === 'function') {
      const text = $(this).val();
      renderPreview('dialog-container', text);
    } else {
      console.log('renderPreview is not defined!');
    }
  });

  $('#search1').keypress(function(e) {
    if (e.keyCode == '13') {
      $('#search-original-btn').click();
    }
  });

  $('#search2').keypress(function(e) {
    if (e.keyCode == '13') {
      $('#search-new-btn').click();
    }
  });

  $('#search3').keypress(function(e) {
    if (e.keyCode == '13') {
      $('#search-comment-btn').click();
    }
  });

  $('#goto1').keypress(function(e) {
    if (e.keyCode == '13') {
      $('#go-to-btn').click();
    }
  });

  $('#go-to-btn').click(function(e) {
    e.stopPropagation();
    e.preventDefault();
    const id = $('#goto1').val();
    if (id && id > 0 && id < max_id) {
      window.open(`?id=${id}`, '_blank').focus();
    } else {
      $('#myToast .toast-body').text('Index out of range!')
      $('#myToast').toast({
        delay: 1500,
      }).toast('show');
    }
  });

  $('#search-original-btn, #search-new-btn, #search-comment-btn, #search-duplicates-btn, #search-global_untranslated-btn').click(function(e) {
    e.stopPropagation();
    e.preventDefault();
    const type = $(this).attr('data-type');
    let text_to_search = undefined;
    switch (type) {
      case 'original':
        text_to_search = $('#search1').val();
        break;
      case 'new':
        text_to_search = $('#search2').val();
        break;
      case 'comment':
        text_to_search = $('#search3').val();
        break;
      case 'duplicates':
        text_to_search = $('#search4').val();
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
          $.each(array, function(index, value) {
            const {id, status} = value;
            const item = $('<a />').addClass('btn btn-sm mr-1 mb-1').text(id).attr('href', `?id=${id}`).attr('target', '_blank');
            if (id == current_id) {
              item.addClass('disabled').removeAttr('href').removeAttr('_blank');
            } 
            (status == 2) ? item.addClass('btn-success') : (status == 1) ? item.addClass('btn-warning') : item.addClass('btn-danger');
            $('#search-result').append(item);
          });
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

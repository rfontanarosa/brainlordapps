$(function() {

  document.onkeyup = e => {
    if (e.ctrlKey && e.key.toUpperCase() === 'P') {
      document.getElementById('prev-btn').click();
    } else if (e.ctrlKey && e.key.toUpperCase() === 'N') {
      document.getElementById('next-btn').click();
    } else if (e.ctrlKey && e.key.toUpperCase() === 'D') {
      document.getElementById('done-btn').click();
    } else if (e.ctrlKey && e.key.toUpperCase() === 'A') {
      document.getElementById('partially-btn').click();
    }
  };

  const maxId = parseInt(document.getElementById('app-vars').getAttribute('data-max-id'));
  const currentId = parseInt(document.getElementById('app-vars').getAttribute('data-current-id'));
  let moreRecentTranslation = document.getElementById('app-vars').getAttribute('data-more-recent-translation') === '1';

  const submit = () => {
    const idText = document.querySelector('input[name="id-text"]').value;
    const translation = document.querySelector('textarea[name="translation"]').value;
    const comment = document.querySelector('textarea[name="comment"]').value;
    const status = parseInt(document.querySelector('input[name="status"]').value);
    const extendsToDuplicates = document.getElementById('extends-to-duplicates').checked;
    $.ajax({
      type: 'POST',
      url: 'ajax_submit.php',
      data: {
        id_text: idText,
        project: '',
        translation,
        status,
        tags: '',
        comment,
        extends_to_duplicates: extendsToDuplicates,
      },
    }).done(function(data, textStatus, jqXHR) {
      const jsonObject = JSON.parse(data);
      const textarea = $('textarea[name=translation]');
      const className = status === 0 ? 'text-bg-danger' : status === 1 ? 'text-bg-warning' : 'text-bg-success';
      textarea.removeClass('text-bg-warning text-bg-danger text-bg-success').addClass(className);
      $('#last-update').text(jsonObject.updateDate);
      moreRecentTranslation = false;
      $('#my-toast .toast-body').text('The text has been updated with success!').removeClass('bg-danger').addClass('bg-success');
    }).fail(function(jqXHR, textStatus, errorThrown) {
      console.log(errorThrown);
      $('#my-toast .toast-body').text('An error has occurred!').removeClass('bg-success').addClass('bg-danger');
    }).always(function(a, textStatus, b) {
      $('#confirm-modal').modal('hide');
      $('#my-toast').toast('show')
    });
  }; 

  $('#modal-confirm-btn').on('click', () => submit());

  $('.submit-btn').on('click', e => {
    const status = e.currentTarget.value;
    $('input[name="status"]').val(status);
    const myModal = new bootstrap.Modal(document.getElementById('confirm-modal'))
    moreRecentTranslation ? myModal.show() : submit();
  });

  $('.preview-btn').on('click', e => {
    e.stopPropagation();
    e.preventDefault();
    if (typeof renderPreview === 'function') {
      const sourceId = e.currentTarget.getAttribute('data-source-id');
      const containerId = e.currentTarget.getAttribute('data-preview-container-id');
      const gameId = e.currentTarget.getAttribute('data-game-id');
      const id = e.currentTarget.getAttribute('data-id');
      const text = document.getElementById(sourceId).value;
      renderPreview(containerId, text, id, gameId);
    } else {
      console.error('renderPreview is not defined!');
    }
  });

  $('.copy-btn').on('click', e => {
    e.stopPropagation();
    e.preventDefault();
    const sourceId = e.currentTarget.getAttribute('data-source-id');
    const text = document.getElementById(sourceId).value;
    navigator.clipboard.writeText(text).then(function() {
      /* clipboard successfully set */
    }, function() {
      /* clipboard write failed */
    });
  });

  $('#paste-new-btn').on('click', e => {
    e.stopPropagation();
    e.preventDefault();
    navigator.clipboard.readText().then(clipText => {
      document.getElementById('translation').value = clipText;
      $('#translation').keyup();
    });
  });

  $('#translation').on('keyup', () => document.getElementById('preview-new-btn').click());

  $('.search-input').on('keypress', e => {
    if (e.key === 'Enter') {
      const buttonId = e.currentTarget.getAttribute('data-button-id');
      document.getElementById(buttonId).click();
    }
  });

  $('#go-to').on('keypress', e => {
    if (e.key === 'Enter') {
      document.getElementById('go-to-btn').click();
    }
  });

  $('#go-to-btn').on('click', e => {
    e.stopPropagation();
    e.preventDefault();
    const id = parseInt(document.getElementById('go-to').value);
    if (!isNaN(id) && id > 0 && id < maxId) {
      window.open(`?id=${id}`, '_blank').focus();
    } else {
      $('#my-toast .toast-body').text('Index out of range!')
      $('#my-toast').toast('show')
    }
  });

  $('#search-ref-btn, #search-original-btn, #search-new-btn, #search-comment-btn, #search-duplicates-btn, #search-personal_all-btn, #search-global_untranslated-btn').on('click', e => {
    e.stopPropagation();
    e.preventDefault();
    const type = e.currentTarget.getAttribute('data-type');
    let textToSearch = undefined;
    let wholeWordOnly = false;
    switch (type) {
      case 'ref':
        textToSearch = document.getElementById('search-ref').value;
        break;
      case 'original':
        textToSearch = document.getElementById('search-original').value;
        wholeWordOnly = $('#search-original-wwo').is(':checked');
        break;
      case 'new':
        textToSearch = document.getElementById('search-new').value;
        wholeWordOnly = $('#search-new-wwo').is(':checked');
        break;
      case 'comment':
        textToSearch = document.getElementById('search-comment').value;
        break;
      case 'duplicates':
        textToSearch = document.getElementById('search-duplicates').value;
        break;
    }
    if (textToSearch === undefined || textToSearch.length > 1) {
      $.ajax({
        async: false,
        type: 'POST',
        url: 'ajax_search.php',
        data: {
          type,
          text_to_search: textToSearch,
          whole_word_only: wholeWordOnly,
        },
      }).done(function(data, textStatus, jqXHR) {
        const jsonArray = JSON.parse(data);
        $('#search-result').empty();
        if (jsonArray.length !== 0) {
          $('#search-result').append($('<div />').addClass('mb-3').text(`Results found: ${jsonArray.length}`));
          const template = $('<a />').addClass('btn btn-sm me-1 mb-1').attr('target', '_blank');
          const items = jsonArray.map(value => {
            const {id, status} = value;
            const item = template.clone().text(id).attr('href', `?id=${id}`);
            if (id === currentId) {
              item.addClass('disabled').removeAttr('href').removeAttr('_blank');
            }
            const className = status === 0 ? 'btn-danger' : status === 1 ? 'btn-warning' : 'btn-success';
            item.addClass(className);
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
      $('#search-result').text('Input must be not empty and larger than 1 character!');
      $('#search-result').show();
    }
  });

});

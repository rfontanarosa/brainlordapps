document.addEventListener('DOMContentLoaded', () => {

  document.onkeyup = e => {
    if (e.ctrlKey && e.key.toUpperCase() === 'P') {
      document.getElementById('prev-btn').click();
    } else if (e.ctrlKey && e.key.toUpperCase() === 'N') {
      document.getElementById('next-btn').click();
    } else if (e.ctrlKey && e.key.toUpperCase() === 'D') {
      document.getElementById('done-btn').click();
    } else if (e.ctrlKey && e.key.toUpperCase() === 'A') {
      document.getElementById('in-progress-btn').click();
    }
  };

  const appVars = document.getElementById('app-vars');
  const currentId = parseInt(appVars.getAttribute('data-current-id'), 10);
  const maxId = parseInt(appVars.getAttribute('data-max-id'), 10);
  const username = appVars.getAttribute('data-username');
  const gameId = appVars.getAttribute('data-game-id');
  let moreRecentTranslation = appVars.getAttribute('data-more-recent-translation') === '1';

  const modal = new bootstrap.Modal(document.getElementById('confirm-modal'));
  const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
  const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

  const modalConfirmButton = document.getElementById('modal-confirm-btn');
  const submitButtons = document.querySelectorAll('.submit-btn');
  const selectTranslator = document.getElementById('select-translator');

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
      // update translation status icon
      const colorClass = status === 1 ? 'text-warning' : status === 2 ? 'text-success' : 'text-danger';
      const iconClass = status === 1 ? 'bi-exclamation-diamond-fill' : status === 2 ? 'bi-check-square-fill' : 'bi-x-circle-fill';
      const statusIconElement = document.getElementById('translation-status');
      statusIconElement.classList.remove(...statusIconElement.classList);
      statusIconElement.classList.add('bi', colorClass, iconClass);
      // update date
      const jsonObject = JSON.parse(data);
      const lastUpdateElement = document.getElementById('last-update');
      lastUpdateElement.textContent = jsonObject.updateDate;
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

  modalConfirmButton.addEventListener('click', () => submit());

  submitButtons.forEach(submitButton => {
    submitButton.addEventListener('click', e => {
      e.preventDefault();
      const status = e.currentTarget.value;
      const statusInput = document.querySelector('input[name="status"]');
      statusInput.value = status;
      moreRecentTranslation ? modal.show() : submit();
    })
  });

  selectTranslator.addEventListener('change', function() {
    const selectedOption = selectTranslator.options[selectTranslator.selectedIndex];
    const selectedValue = selectedOption.value;
    const blocks = document.querySelectorAll('.card-block');
    blocks.forEach(block => {
      if (block.classList.contains(`card-block-${selectedValue}`)) {
        block.classList.remove('d-none');
      } else {
        block.classList.add('d-none');
      }
    });
    document.getElementById('paste-btn').disabled = selectedValue !== username;
  });

  document.getElementById('preview-btn-original').addEventListener('click', e => {
    e.preventDefault();
    const text = document.getElementById('original-text').value;
    if (MumblePreviewer && typeof MumblePreviewer.renderPreview === 'function') {
      MumblePreviewer.renderPreview('preview-container', text, gameId);
    } else {
      console.error('renderPreview is not defined!');
    }
  });

  document.getElementById('preview-btn').addEventListener('click', e => {
    e.preventDefault();
    const elements = document.querySelectorAll('[name="translation"]');
    const visibleElement = Array.from(elements).find(el => window.getComputedStyle(el.parentElement.parentElement).display !== 'none');
    const text = visibleElement.value;
    if (MumblePreviewer && typeof MumblePreviewer.renderPreview === 'function') {
      MumblePreviewer.renderPreview('preview-container', text, gameId);
    } else {
      console.error('renderPreview is not defined!');
    }
  });

  document.getElementById('copy-btn-original').addEventListener('click', e => {
    e.preventDefault();
    const text = document.getElementById('original-text').value;
    navigator.clipboard.writeText(text).then(function() {
      /* clipboard successfully set */
    }, function() {
      /* clipboard write failed */
    });
  });

  document.getElementById('copy-btn').addEventListener('click', e => {
    e.preventDefault();
    const elements = document.querySelectorAll('[name="translation"]');
    const visibleElement = Array.from(elements).find(el => window.getComputedStyle(el.parentElement.parentElement).display !== 'none');
    const text = visibleElement.value;
    navigator.clipboard.writeText(text).then(function() {
      /* clipboard successfully set */
    }, function() {
      /* clipboard write failed */
    });
  });

  document.getElementById('paste-btn').addEventListener('click', e => {
    e.preventDefault();
    navigator.clipboard.readText().then(clipText => {
      const translationElement = document.getElementById('translation');
      translationElement.value = clipText;
      translationElement.dispatchEvent(new Event('keyup')); 
    });
  });

  document.getElementById('translation').addEventListener('keyup', () => {
    document.getElementById('preview-btn').click();
  });

  $('.search-input').on('keypress', e => {
    if (e.key === 'Enter') {
      const buttonId = e.currentTarget.getAttribute('data-button-id');
      document.getElementById(buttonId).click();
    }
  });

  $('#go-to-btn').on('click', e => {
    e.preventDefault();
    const id = parseInt(document.getElementById('go-to').value);
    if (!isNaN(id) && id > 0 && id < maxId) {
      window.open(`?id=${id}`, '_blank').focus();
    } else {
      $('#my-toast .toast-body').text('Index out of range!')
      $('#my-toast').toast('show')
    }
  });

  $('.search-btn').on('click', e => {
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
    if ((textToSearch === undefined || textToSearch.length > 1) || type === 'duplicates') {
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

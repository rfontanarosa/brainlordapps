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
  const toast = document.getElementById('my-toast');
  const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toast);
  const toastBody = toast.querySelector('.toast-body');

  const modalConfirmButton = document.getElementById('modal-confirm-btn');
  const submitButtons = document.querySelectorAll('.submit-btn');
  const selectTranslator = document.getElementById('select-translator');
  const searchButtons = document.querySelectorAll('.search-btn');
  const searchInputs = document.querySelectorAll('.search-input');
  const searchResults = document.getElementById('search-results');

  const submit = () => {
    const idText = document.querySelector('input[name="id-text"]').value;
    const translation = document.querySelector('textarea[name="translation"]').value;
    const comment = document.querySelector('textarea[name="comment"]').value;
    const status = parseInt(document.querySelector('input[name="status"]').value);
    const extendsToDuplicates = document.getElementById('extends-to-duplicates').checked;
    fetch('ajax_submit.php', {
      method: 'POST',
      body: new URLSearchParams({
        id_text: idText,
        project: '',
        translation,
        status,
        tags: '',
        comment,
        extends_to_duplicates: extendsToDuplicates,
      })
    }).then(response => {
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      return response.json();
    }).then(data => {
      // update translation status icon
      const colorClass = status === 1 ? 'text-warning' : status === 2 ? 'text-success' : 'text-danger';
      const iconClass = status === 1 ? 'bi-exclamation-diamond-fill' : status === 2 ? 'bi-check-square-fill' : 'bi-x-circle-fill';
      const statusIconElement = document.getElementById('translation-status');
      statusIconElement.classList.remove(...statusIconElement.classList);
      statusIconElement.classList.add('bi', colorClass, iconClass);
      // update date
      const lastUpdateElement = document.getElementById('last-update');
      lastUpdateElement.textContent = data.updateDate;
      moreRecentTranslation = false;
      toastBody.textContent = 'The text has been updated successfully!';
    }).catch(error => {
      console.error(error);
      toastBody.textContent = 'The text has been updated successfully!';
    }).finally(() => {
      modal.hide();
      toastBootstrap.show();
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

  searchInputs.forEach(searchInput => {
    searchInput.addEventListener('keyup', e => {
      if (e.key === 'Enter') {
        const buttonId = e.currentTarget.getAttribute('data-button-id');
        document.getElementById(buttonId).click();
      }
    });
  });

  document.getElementById('go-to-btn').addEventListener('click', e => {
    e.preventDefault();
    const id = parseInt(document.getElementById('go-to').value);
    if (!isNaN(id) && id > 0 && id < maxId) {
      window.open(`?id=${id}`, '_blank').focus();
    } else {
      toastBody.textContent = 'Index out of range!';
      toastBootstrap.show();
    }
  });

  searchButtons.forEach(searchButton => {
    searchButton.addEventListener('click', e => {
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
          wholeWordOnly = document.getElementById('search-original-wwo').checked;
          break;
        case 'new':
          textToSearch = document.getElementById('search-new').value;
          wholeWordOnly = document.getElementById('search-new-wwo').checked;
          break;
        case 'comment':
          textToSearch = document.getElementById('search-comment').value;
          break;
        case 'duplicates':
          textToSearch = document.getElementById('search-duplicates').value;
          break;
      }
      if (['ref', 'original', 'new', 'comment'].includes(type) && (textToSearch === undefined || textToSearch.length < 2)) {
        searchResults.innerHTML = '';
        searchResults.textContent = 'Please enter a valid search value (at least 2 characters).';
        searchResults.style.display = 'block';
        return;
      }
      fetch('ajax_search.php', {
        method: 'POST',
        body: new URLSearchParams({
          type,
          text_to_search: textToSearch,
          whole_word_only: wholeWordOnly
        })
      }).then(response => {
        searchResults.innerHTML = '';
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      }).then(data => {
        const resultsCountElement = document.createElement('div');
        resultsCountElement.style.flexBasis = '100%';
        resultsCountElement.classList.add('mb-1');
        searchResults.appendChild(resultsCountElement);
        if (data.length !== 0) {
          resultsCountElement.textContent = `Results found: ${data.length}!`;
          const template = document.createElement('a');
          template.classList.add('btn', 'btn-sm', 'me-1', 'mb-1', 'd-flex', 'justify-content-center', 'align-items-center');
          template.target = '_blank';
          template.style.width = '50px';
          template.style.height = '50px';
          const items = [];
          data.forEach(({id, status}) => {
            const item = template.cloneNode(true);
            item.textContent = id;
            item.href = `?id=${id}`;
            if (id === currentId) {
              item.classList.add('disabled');
              item.removeAttribute('href');
              item.removeAttribute('target');
            }
            const className = status === 0 ? 'btn-danger' : status === 1 ? 'btn-warning' : 'btn-success';
            item.classList.add(className);
            searchResults.appendChild(item);
          });
        } else {
          resultsCountElement.textContent = 'No results found!';
        }
      }).catch(error => {
        console.error(error);
        searchResults.textContent = 'An error has occurred! See the console for more details.';
      }).finally(() => {
        searchResults.style.display = 'block';
      });
    });
  });

});

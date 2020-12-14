class ReleaseForm {
  constructor() {
    const self = this;

    const addAuthorToggler = document.getElementById('add-author-toggler');
    addAuthorToggler.addEventListener('click', function (event) {
      event.preventDefault();
      const newAuthor = document.getElementById('new-author');
      slideToggle(newAuthor);
    });

    const removeButtons = document.querySelectorAll('#authors-container button');
    removeButtons.forEach(function (removeButton) {
      removeButton.addEventListener('click', function (event) {
        event.preventDefault();
        const container = event.target.parentElement;
        const duration = 300;
        slideUp(container, duration);
        setTimeout(function () {
          container.remove();
        }, duration);
      });
    });

    const addAuthorButton = document.getElementById('add-author-button');
    addAuthorButton.addEventListener('click', function (event) {
      event.preventDefault();
      self.addAuthor();
    });

    const cancelAddAuthorButton = document.getElementById('cancel-add-author-button');
    cancelAddAuthorButton.addEventListener('click', function (event) {
      event.preventDefault();
      const newAuthorContainer = document.getElementById('new-author');
      const newAuthorNameField = newAuthorContainer.querySelector('input[type=text]');
      const duration = 300;
      slideUp(newAuthorContainer, duration);
      setTimeout(function () {
        newAuthorNameField.value = '';
      }, duration);
    });

    const authorSelector = document.getElementById('author-select');
    authorSelector.addEventListener('change', function () {
      self.selectAuthor();
    });

    const form = document.getElementById('ReleaseForm');
    form.addEventListener('submit', function (event) {
      if (self.hasUnaddedAuthor()) {
        event.preventDefault();
        const authorName = document.querySelector('#new-author input').value;
        alert('Please click "add" to add ' + authorName + ' to this release.');
      }
    });

    const addPartnerButton = document.getElementById('add-partner-button');
    addPartnerButton.addEventListener('click', function (event) {
      event.preventDefault();
      document.getElementById('release-partner-id').selectedIndex = 0;
      document.getElementById('choose-partner').style.display = 'none';
      document.getElementById('add-partner').style.display = 'block';
    });

    const choosePartnerButton = document.getElementById('choose-partner-button');
    choosePartnerButton.addEventListener('click', function (event) {
      event.preventDefault();
      document.getElementById('release-new-partner').value = '';
      document.getElementById('choose-partner').style.display = 'block';
      document.getElementById('add-partner').style.display = 'none';
    });

    const uploadReportNoteButton = document.getElementById('footnote-upload-reports-handle');
    uploadReportNoteButton.addEventListener('click', function (event) {
      event.preventDefault();
      const uploadNote = document.getElementById('footnote-upload-reports');
      uploadNote.style.display = uploadNote.style.display === 'none' ? 'block' : 'none';
    });

    const uploadGraphicsNoteButton = document.getElementById('footnote-upload-graphics-handle');
    uploadGraphicsNoteButton.addEventListener('click', function (event) {
      event.preventDefault();
      const uploadNote = document.getElementById('footnote-upload-graphics');
      uploadNote.style.display = uploadNote.style.display === 'none' ? 'block' : 'none';
    });

    const removeGraphicButtons = document.querySelectorAll('button.remove-graphic');
    removeGraphicButtons.forEach(function (button) {
      button.addEventListener('click', function (event) {
        event.preventDefault();
        self.removeGraphic(event.target);
      });

    });

    const addGraphicButton = document.querySelector('button.add-graphic');
    addGraphicButton.addEventListener('click', function (event) {
      event.preventDefault();
      self.addGraphic('ReleaseAddForm');
    });
  }

  hasUnaddedAuthor() {
    const input = document.querySelector('#new-author input');

    return input.style.display !== 'none' && input.value !== '';
  }

  addAuthor() {
    const authorName = document.querySelector('#new-author input');
    authorName.value.replace('"', '\'');
    if (authorName.value === '') {
      return;
    }

    const li = document.createElement('li');
    const button = document.createElement('button');
    button.innerHTML = '<i class="fas fa-times" title="Remove"></i>';
    button.className = 'btn btn-sm btn-link';
    const self = this;
    button.addEventListener('click', function (event) {
      event.preventDefault();
      self.removeNewAuthor(event.target.closest('li'));
    });
    li.innerHTML = authorName.value + '<input type="hidden" name="new_authors[]" value="' + authorName.value + '" />';
    li.appendChild(button);
    li.style.display = 'none';
    document.getElementById('authors-container').appendChild(li);
    slideDown(li);
    const newAuthor = document.getElementById('new-author');
    const duration = 300;
    slideUp(newAuthor, 300);
    setTimeout(function () {
      authorName.value = '';
    }, duration);
  }

  selectAuthor() {
    const select = document.getElementById('author-select');
    const authorId = select.value;
    const selected = select.querySelector('option:checked');
    selected.selected = false;

    if (authorId === '') {
      return;
    }

    // Do nothing if author is already selected
    const authorIsSelected = document.querySelector(`#authors-container input[value="${authorId}"]`);
    if (authorIsSelected !== null) {
      return;
    }

    const authorName = selected.innerText;
    const li = document.createElement('li');
    li.innerHTML = `${authorName}<input type="hidden" name="authors[_ids][]" value="${authorId}" />`;
    const button = document.createElement('button');
    button.innerHTML = 'X';
    button.addEventListener('click', function (event) {
      event.preventDefault();
      const container = event.target.parentElement;
      slideUp(container, 300);
      container.remove();
    });
    li.appendChild(button);
    li.style.display = 'none';
    document.getElementById('authors-container').appendChild(li);
    slideDown(li);
  }

  setupUpload(params) {
    const choose = document.getElementById('upload-reports');
    FileAPI.event.on(choose, 'change', function (evt) {
      const files = FileAPI.getFiles(evt);
      slideDown(document.getElementById('upload-reports-progress-container'));
      const progress = document.getElementById('upload-reports-progress');

      FileAPI.upload({
        url: '/releases/upload-report',
        files: {report: files},
        data: {
          _csrfToken: window.csrfToken,
          timestamp: params.time,
          token: params.token,
          overwrite: document.getElementById('overwrite-reports').checked,
        },
        progress: function (evt) {
          const progressAmount = Math.round((evt.loaded / evt.total) * 100);
          progress.style.width = progressAmount + '%';
        },
        complete: function (err, xhr) {
          if (err) {
            (new FlashMessage).insert(err, 'error', '#upload-report-results');
          } else {
            (new FlashMessage).insert('Upload successful', 'success', '#upload-report-results');
          }
          progress.style.width = '100%';
          slideUp(progress.parentElement);
        }
      });
    });
  }

  removeGraphic(button) {
    button.closest('tr').remove();
    this.updateOrderSelectors();

    // Hide table head if table body is empty
    const rows = document.querySelectorAll('table.graphics tbody tr');
    if (rows.length === 0) {
      document.querySelector('table.graphics thead').style.display = 'none';
    }
  }

  updateOrderSelectors() {
    const rowCount = document.querySelectorAll('table.graphics tbody tr').length;
    const selectElements = document.querySelectorAll('table.graphics select');
    selectElements.forEach(function (select) {
      const selected = select.querySelector('option:checked').value;
      select.innerHtml = '';
      for (let n = 1; n <= rowCount; n++) {
        const option = document.createElement('option');
        option.innerHTML = n.toString();
        option.value = (n - 1).toString();
        option.selected = selected === option.value;
        select.appendChild(option);
      }
    });
  }

  getGraphicsCount() {
    return document.querySelectorAll('table.graphics tbody tr.graphic').length;
  }

  /**
   * Create another row of input fields under 'linked graphics'
   */
  addGraphic(formId) {
    // Get and advance the key
    const body = document.querySelector('body');
    let i = this.getGraphicsCount();

    // Get the row to be copied
    const newRow = document.querySelector('table.graphics tfoot .dummy-row').cloneNode(true);
    newRow.classList.remove('dummy-row');
    newRow.classList.add('graphic');

    // Apply a unique key to each row
    newRow.querySelectorAll('input, select').forEach(function (element) {
      element.id = element.id.replace('{i}', i);
      element.name = element.name.replace('{i}', i);
      element.className = element.className.replace('{i}', i);
      element.disabled = false;
    });
    newRow.querySelectorAll('label').forEach(function (element) {
      element.htmlFor = element.htmlFor.replace('{i}', i);
    });

    // Set up the remove button
    const self = this;
    newRow.querySelectorAll('button.remove-graphic').forEach(function (button) {
      button.addEventListener('click', function (event) {
        event.preventDefault();
        self.removeGraphic(event.target);
      });
    });

    // Set up the 'find report' button
    newRow.querySelector('button.find-report').addEventListener('click', function (event) {
      event.preventDefault();
      let button = event.target;
      if (!button.classList.contains('btn')) {
        button = button.closest('button');
      }
      self.toggleReportFinder(button, i);
    });

    // Add the now-unique row
    document.querySelector('table.graphics tbody').append(newRow);

    // Reset 'order' options
    this.updateOrderSelectors();

    // Restart the validation engine so that this row is included
    // document.getElementById(`#${formId}`).validationEngine('attach');

    // Show the table head
    const thead = document.querySelector('table.graphics thead');
    if (thead.style.display === 'none') {
      thead.style.display = 'table-header-group';
    }
  }

  /* Called when a 'find report' button is clicked
   * button: the button clicked
   * i: the unique key for the corresponding 'linked graphics' row */
  toggleReportFinder(button, i) {
    const existingSelectionBox = document.getElementById(`report-choices-${i}`);

    // Open
    if (existingSelectionBox === null) {
      button.innerHTML = '<i class="fas fa-spinner fa-spin loading"></i>';
      const cell = button.closest('td');
      this.loadReportFinder(cell, i, button);

    // Close
    } else {
      existingSelectionBox.closest('tr').remove();
    }
  }

  // cell: the table cell that contains the input field to be populated
  loadReportFinder(cell, i, button) {
    const self = this;
    fetch('/releases/list-reports')
      .then(response => response.text())
      .then(function (html) {
        self.setupReportFinder(html, cell, i);
        button.innerHTML = '<i class="fas fa-search"></i>';
      });
  }

  /* html: the results of requesting /releases/list-reports
   * cell: the table cell that contains the input field to be populated
   * i: the unique key of the row in the 'add/edit linked graphics' box */
  setupReportFinder(html, cell, i) {
    const newRow = document.createElement('tr');
    newRow.innerHTML = `<td colspan="4" class="report-choices"><div id="report-choices-${i}">${html}</div></td>`;
    cell.closest('tr').after(newRow);
    newRow.querySelector('button.report').addEventListener('click', function (event) {
      event.preventDefault();
      const reportFilename = event.target.innerText.trim();
      cell.querySelector('input').value = `/reports/${reportFilename}`;
      newRow.remove();
    });
    newRow.querySelector('button.reports-cancel').addEventListener('click', function (event) {
      event.preventDefault();
      newRow.remove();
    });
    const self = this;
    newRow.querySelector('button.refresh').addEventListener('click', function (event) {
      event.preventDefault();
      const loading = event.target.querySelector('.loading');
      loading.style.display = 'inline';
      fetch('/releases/list-reports')
        .then(response => response.text())
        .then(function (html) {
          newRow.remove();
          self.setupReportFinder(html, cell, i);
        })
        .catch(function (error) {
          alert('Sorry, there was a problem reloading the list of reports.');
          loading.style.display = 'none';
          console.log(error);
        });
    });
    newRow.querySelector('.sorting-options button').addEventListener('click', function (event) {
      event.preventDefault();
      const link = event.target;
      link.classList.add('selected');
      const newest = newRow.querySelector('ul.newest');
      const alphabetic = newRow.querySelector('ul.alphabetic');
      if (link.classList.contains('newest')) {
        newRow.querySelector('.sorting-options button.alphabetic').classList.remove('selected');
        newest.style.display = 'block';
        alphabetic.style.display = 'none';
      } else if (link.classList.contains('alphabetic')) {
        newRow.querySelector('.sorting-options button.newest').classList.remove('selected');
        newest.style.display = 'none';
        alphabetic.style.display = 'block';
      }
    });
  }

  removeNewAuthor(container) {
    const duration = 300;
    slideUp(container, duration);
    setTimeout(function () {
      container.remove();
    }, duration);
  }
}

class ReleaseForm {
    constructor() {
        const self = this;

        const addAuthorToggler = document.getElementById('add_author_toggler');
        addAuthorToggler.addEventListener('click', function (event) {
            event.preventDefault();
            const newAuthor = document.getElementById('new_author');
            slideToggle(newAuthor);
        });

        const removeButtons = document.querySelectorAll('#authors_container button');
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

        const addAuthorButton = document.getElementById('add_author_button');
        addAuthorButton.addEventListener('click', function (event) {
            event.preventDefault();
            self.addAuthor();
        });

        const cancelAddAuthorButton = document.getElementById('cancel_add_author_button');
        cancelAddAuthorButton.addEventListener('click', function (event) {
            event.preventDefault();
            const newAuthorContainer = document.getElementById('new_author');
            const newAuthorNameField = document.querySelector('#new_author input[type=text]');
            const duration = 300;
            slideUp(newAuthorContainer, duration);
            setTimeout(function () {
                newAuthorNameField.value = '';
            }, duration);
        });

        /*const releaseAuthor = document.getElementById('ReleaseAuthor');
        releaseAuthor.addEventListener('click', function () {
            self.selectAuthor();
        });*/

        const form = document.getElementById('ReleaseForm');
        form.addEventListener('submit', function (event) {
            if (self.hasUnaddedAuthor()) {
                event.preventDefault();
                const authorName = document.querySelector('#new_author input').value;
                alert('Please click "add" to add ' + authorName + ' to this release.');
            }
        });

        const addPartnerButton = document.getElementById('add_partner_button');
        addPartnerButton.addEventListener('click', function (event) {
            event.preventDefault();
            document.getElementById('ReleasePartnerId').value = '';
            document.getElementById('choose_partner').style.display = 'none';
            document.getElementById('add_partner').style.display = 'block';
        });

        const choosePartnerButton = document.getElementById('choose_partner_button');
        choosePartnerButton.addEventListener('click', function (event) {
            event.preventDefault();
            document.getElementById('ReleaseNewPartner').value = '';
            document.getElementById('choose_partner').style.display = 'block';
            document.getElementById('add_partner').style.display = 'none';
        });

        const uploadReportNoteButton = document.getElementById('footnote_upload_reports_handle');
        uploadReportNoteButton.addEventListener('click', function (event) {
            event.preventDefault();
            const uploadNote = document.getElementById('footnote_upload_reports');
            uploadNote.style.display = uploadNote.style.display === 'none' ? 'block' : 'none';
        });

        const uploadGraphicsNoteButton = document.getElementById('footnote_upload_graphics_handle');
        uploadGraphicsNoteButton.addEventListener('click', function (event) {
            event.preventDefault();
            const uploadNote = document.getElementById('footnote_upload_graphics');
            uploadNote.style.display = uploadNote.style.display === 'none' ? 'block' : 'none';
        });

        const removeGraphicButtons = document.querySelectorAll('button.remove_graphic');
        removeGraphicButtons.forEach(function (button) {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                self.removeGraphic(event.target);
            });

        });

        const addGraphicButton = document.querySelector('a.add_graphic');
        addGraphicButton.addEventListener('click', function (event) {
            event.preventDefault();
            self.addGraphic('ReleaseAddForm');
        });
    }

    hasUnaddedAuthor() {
        const input = document.querySelector('#new_author input');

        return input.style.display !== 'none' && input.value !== '';
    }

    addAuthor() {
        const authorName = document.querySelector('#new_author input');
        authorName.value.replace('"', '\'');
        if (authorName.value === '') {
            return;
        }

        const li = document.createElement('li');
        const button = document.createElement('button');
        button.innerHTML = 'X';
        button.addEventListener('click', function (event) {
            event.preventDefault();
            const container = event.target.parentElement;
            const duration = 300;
            slideUp(container, duration);
            setTimeout(function () {
                container.remove();
            }, duration);
        });
        li.innerHTML = name + '<input type="hidden" name="data[new_authors][]" value="' + name + '" />';
        li.appendChild(button);
        li.style.display = 'none';
        document.getElementById('authors_container').appendChild(li);
        slideDown(li);
        const newAuthor = document.getElementById('new_author');
        const duration = 300;
        slideUp(newAuthor, 300);
        setTimeout(function () {
            authorName.value = '';
        }, duration);
    }

    selectAuthor() {
        const select = document.getElementById('ReleaseAuthor');
        const authorId = select.value;
        const selected = select.querySelector('option:checked');
        selected.selected = false;

        if (authorId === '') {
            return;
        }

        // Do nothing if author is already selected
        const authorIsSelected = document.querySelector(`#authors_container input[value=${authorId}]`);
        if (authorIsSelected !== null) {
            return;
        }

        const authorName = selected.innerHtml;
        const li = document.createElement('li');
        li.innerHTML = `${authorName}<input type="hidden" name="author[]" value="${authorId}" />`;
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
        document.getElementById('authors_container').appendChild(li);
        slideDown(li);
    }

    setupUploadify(params) {
        $('#upload_reports').uploadifive({
            uploadScript: '/releases/upload-reports',
            fileSizeLimit: params.fileSizeLimit,
            fileTypeExts: params.validExtensions,
            formData: {
                timestamp: params.time,
                token: params.token,
                overwrite: false
            },
            onUploadFile: function(file) {
                $('#upload_reports').uploadifive('settings', 'formData', {
                    overwrite: document.getElementById('overwrite_reports').checked
                });
            },
            onUploadComplete: function(file, data, response) {
                let classname = (data.indexOf('Error') === -1) ? 'success' : 'error';
                (new FlashMessage()).insert(data, classname);
            },
            onError: function(file, errorCode, errorMsg, errorString) {
                alert('There was an error uploading that file. Details are available in the browser console.');
                console.log('Upload error...');
                console.log('file: ' +file);
                console.log('errorCode: ' +errorCode);
                console.log('errorMsg: ' +errorMsg);
                console.log('errorString: ' +errorString);
            }
        });
    }

    removeGraphic(button) {
        button.closest('tr').remove();
        this.updateOrderSelectors();

        // Hide table head if table body is empty
        const rows = document.querySelector('table.graphics tbody tr');
        if (rows.length === 0) {
            document.querySelector('table.graphics thead').style.display = 'none';
        }
    }

    updateOrderSelectors() {
        const rowCount = document.querySelector('table.graphics tbody tr').length;
        const selectElements = document.querySelector('table.graphics select');
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

    /**
     * Create another row of input fields under 'linked graphics'
     */
    addGraphic(formId) {
        // Get and advance the key
        const body = document.querySelector('body');
        let i = body.dataset.graphicsIterator;
        body.dataset.graphicsIterator++;

        // Get the row to be copied
        const dummyRow = document.querySelector('table.graphics tfoot .dummy-row').cloneNode(true);
        dummyRow.classList.remove('dummy-row');

        // Apply a unique key to each row
        dummyRow.querySelectorAll('input, select').forEach(function (element) {
            element.id = element.id.replace('{i}', i);
            element.name = element.name.replace('{i}', i);
            element.className = element.className.replace('{i}', i);
            element.disabled = false;
        });

        // Set up the remove button
        const self = this;
        dummyRow.querySelectorAll('button.remove_graphic').each(function (button) {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                self.removeGraphic(event.target);
            });
        });

        // Set up the 'find report' button
        dummyRow.querySelector('a.find_report').addEventListener('click', function (event) {
            event.preventDefault();
            self.toggleReportFinder(event.target, i);
        });

        // Add the now-unique row
        document.querySelector('table.graphics tbody').append(dummyRow);

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
     * link: the link clicked
     * i: the unique key for the corresponding 'linked graphics' row */
    toggleReportFinder(link, i) {
        const existingSelectionBox = document.getElementById(`report_choices_${i}`);

        // Open
        if (existingSelectionBox === null) {
            const cell = link.closest('td');
            this.loadReportFinder(cell, i);

        // Close
        } else {
            existingSelectionBox.closest('tr').remove();
        }
    }

    /* cell: the table cell that contains the input field to be populated
     * i: the unique key of the row in the 'add/edit linked graphics' box */
    loadReportFinder(cell, i) {
        fetch(`/releases/list_reports/${i}`)
            .then(html => this.setupReportFinder(html, cell, i));
    }

    /* html: the results of requesting /releases/list_reports/$i
     * cell: the table cell that contains the input field to be populated
     * i: the unique key of the row in the 'add/edit linked graphics' box */
    setupReportFinder(html, cell, i) {
        const newRow = document.createElement('tr');
        newRow.innerHTML = `<td colspan="4" class="report_choices"><div id="report_choices_${i}">${html}</div></td>`;
        cell.closest('tbody').append(newRow);
        newRow.querySelector('a.report').addEventListener('click', function (event) {
            event.preventDefault();
            const reportFilename = event.target.innerText.trim();
            cell.querySelector('input').value = `/reports/${reportFilename}`;
            newRow.remove();
        });
        newRow.querySelector('a.close').addEventListener('click', function (event) {
            event.preventDefault();
            newRow.remove();
        });
        const self = this;
        newRow.querySelector('a.refresh').addEventListener('click', function (event) {
            event.preventDefault();
            const loading = event.target.querySelector('.loading');
            loading.style.display = 'inline';
            fetch(`/releases/list_reports/${i}`)
                .then(function(html) {
                    newRow.remove();
                    self.setupReportFinder(html, cell, i);
                })
                .catch(function (error) {
                    alert('Sorry, there was a problem reloading the list of reports.');
                    loading.style.display = 'none';
                    console.log(error);
                });
        });
        newRow.querySelector('.sorting_options a').addEventListener('click', function (event) {
            event.preventDefault();
            const link = event.target;
            link.classList.add('selected');
            const newest = newRow.querySelector('ul.newest');
            const alphabetic = newRow.querySelector('ul.alphabetic');
            if (link.classList.contains('newest')) {
                newRow.querySelector('.sorting_options a.alphabetic').classList.remove('selected');
                newest.style.display = 'block';
                alphabetic.style.display = 'none';
            } else if (link.classList.contains('alphabetic')) {
                newRow.querySelector('.sorting_options a.newest').classList.remove('selected');
                newest.style.display = 'none';
                alphabetic.style.display = 'block';
            }
        });
    }
}

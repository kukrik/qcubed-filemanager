(function ($) {
    'use strict';

    $.fn.galleryUpload = function (settings, key, value) {
        // GalleryUploadHandler renders only <div class="files"> when UseWrapper=false.
        // QCubed still calls $('#controlId').galleryUpload(...), which can be an empty selection.
        // Preserve that legacy entry point and keep instance data on the real container.
        const targets = this.length ? this : $(document.querySelector('.files'));
        targets.each(function () {
            const scope = this.closest('form') || document;
            const host = this.matches('.files') ? this : this.querySelector('.files') || scope.querySelector('.files');
            if (!host) return;
            const existing = $(host).data('galleryUpload');
            if (typeof settings === 'string') {
                if (existing && settings === 'option') {
                    if (key && typeof key === 'object') {
                        Object.keys(key).forEach(name => existing.setOption(name, key[name]));
                    } else existing.setOption(key, value);
                }
                return;
            }
            if (existing) {
                Object.keys(settings || {}).forEach(name => existing.setOption(name, settings[name]));
                return;
            }
            $(host).data('galleryUpload', createUploader(host, settings));
        });
        return this;
    };

    function createUploader(host, settings) {
        const options = $.extend({
            language: 'en', showIcons: false, acceptFileTypes: null,
            maxNumberOfFiles: null, maxFileSize: null, minFileSize: null,
            chunkUpload: true, maxChunkSize: 1024 * 1024,
            limitConcurrentUploads: 2, url: null,
            previewMaxWidth: 80, previewMaxHeight: 80,
            requestTimeout: 120000, progressHoldTime: 600, serverLimits: true
        }, settings);
                let languages = {
            en: {
                add_files_Lbl: 'Add files',
                all_start_Lbl: 'Start upload',
                all_cancel_Lbl: 'Cancel all uploads',
                maxNumberOfFiles_Lbl: 'Maximum number of files is exceeded. Up to %d files are allowed to be uploaded.',
                acceptFileTypes_Lbl: 'File type not allowed',
                maxFileSize_Lbl: 'File is too large',
                minFileSize_Lbl: 'File is too small',
                error_uploading_file_Lbl: 'Error uploading file',
                cancel_Lbl: 'Cancel',
                done_Lbl: 'Done',
                no_file_selected_Lbl: 'No file selected',
                no_file_selected_to_delete_Lbl: 'No file selected to delete',
                file_transfer_completed_successfully_Lbl: 'File transfer completed successfully',
                file_transfer_failed_partially_Lbl: 'File transfer failed partially',
                file_transfer_failed_completely_Lbl: 'File transfer failed completely',
                upload_cancelled_Lbl: 'Upload cancelled',
                remove_invalid_inputs_Lbl: 'Please remove invalid inputs!',
                remove_invalid_inputs_acceptFileTypes_Lbl: 'The following file types are allowed: %s',
                remove_invalid_inputs_maxFileSize_Lbl: 'The maximum size allowed for each file is up to %s',
                remove_invalid_inputs_minFileSize_Lbl: 'Each file must exceed the minimum size %s'
            },
            et: {
                add_files_Lbl: 'Lisa faile',
                all_start_Lbl: 'Alusta üleslaadimist',
                all_cancel_Lbl: 'Tühista kõik üleslaadimised',
                maxNumberOfFiles_Lbl: 'Maksimaalne failide arv on ületatud. Üles laadida on lubatud kuni %d faili.',
                acceptFileTypes_Lbl: 'Failitüüp pole lubatud',
                maxFileSize_Lbl: 'Fail on liiga suur',
                minFileSize_Lbl: 'Fail on liiga väike',
                error_uploading_file_Lbl: 'Viga faili üleslaadimisel',
                cancel_Lbl: 'Loobu',
                done_Lbl: 'Valmis',
                no_file_selected_Lbl: 'Fail/failid pole valitud',
                no_file_selected_to_delete_Lbl: 'Kustutamiseks pole valitud ühtegi faili',
                file_transfer_completed_successfully_Lbl: 'Failiedastus on edukalt lõpule viidud',
                file_transfer_failed_partially_Lbl: 'Failiedastus ebaõnnestus osaliselt',
                file_transfer_failed_completely_Lbl: 'Failiedastus ebaõnnestus täielikult',
                upload_cancelled_Lbl: 'Üleslaadimine tühistati',
                remove_invalid_inputs_Lbl: 'Eemaldage kehtetud sisendid!',
                remove_invalid_inputs_acceptFileTypes_Lbl: 'Lubatud on järgmised failitüübid: %s',
                remove_invalid_inputs_maxFileSize_Lbl: 'Iga faili maksimaalne lubatud suurus on kuni %s',
                remove_invalid_inputs_minFileSize_Lbl: 'Iga fail peab ületama minimaalset suurust %s'
            },
            ru: {
                add_files_Lbl: 'Добавить файлы',
                all_start_Lbl: 'Начать загрузку',
                all_cancel_Lbl: 'Отменить все загрузки',
                maxNumberOfFiles_Lbl: 'Превышено максимальное количество файлов. Разрешено загружать до %d файлов.',
                acceptFileTypes_Lbl: 'Недопустимый тип файла',
                maxFileSize_Lbl: 'Файл слишком большой',
                minFileSize_Lbl: 'Файл слишком мал',
                error_uploading_file_Lbl: 'Ошибка загрузки файла',
                cancel_Lbl: 'Отмена',
                done_Lbl: 'Сделанный',
                no_file_selected_Lbl: 'Файл не выбран',
                no_file_selected_to_delete_Lbl: 'Не выбран файл для удаления',
                file_transfer_completed_successfully_Lbl: 'Передача файла успешно завершена',
                file_transfer_failed_partially_Lbl: 'Передача файла не удалась частично',
                file_transfer_failed_completely_Lbl: 'Передача файла полностью не удалась',
                upload_cancelled_Lbl: 'Загрузка отменена',
                remove_invalid_inputs_Lbl: 'Пожалуйста, удалите неверные данные!',
                remove_invalid_inputs_acceptFileTypes_Lbl: 'Разрешены следующие типы файлов: %s',
                remove_invalid_inputs_maxFileSize_Lbl: 'Максимально допустимый размер для каждого файла составляет до %s',
                remove_invalid_inputs_minFileSize_Lbl: 'Каждый файл должен превышать минимальный размер %s'
            }
        };


        const extra = {
            en: {processing: 'Processing image…', invalid: 'Invalid server response', timeout: 'Upload timed out', empty: 'File is empty'},
            et: {processing: 'Pildi töötlemine…', invalid: 'Serveri vastus on vigane', timeout: 'Üleslaadimise aeg sai läbi', empty: 'Fail on tühi'},
            ru: {processing: 'Обработка изображения…', invalid: 'Некорректный ответ сервера', timeout: 'Время загрузки истекло', empty: 'Файл пуст'}
        };
        function language() {
            const code = String(options.language || 'en').split('-')[0];
            return languages[code] ? code : 'en';
        }
        function text(name) { return languages[language()][name] || extra[language()][name]; }
        const form = host.closest('form') || document.querySelector('form');
        const find = selector => (form || document).querySelector(selector);
        const controls = {
            picker: find('.fileinput-button'), input: find('#files'), start: find('.all-start'),
            cancel: find('.all-cancel'), back: find('.back'), donebar: find('.fileupload-donebar'),
            done: find('.done'), alert: find('#alert-wrapper'), errors: find('.alert-multi-wrapper')
        };
        const container = host.matches('.files') ? host : host.querySelector('.files') || find('.files');
        if (!container || Object.values(controls).some(element => !element)) {
            throw new Error('galleryUpload: required upload controls are missing');
        }
        const table = document.createElement('table');
        table.className = 'table table-striped';
        const columns = document.createElement('colgroup');
        [10, 55, 20, 15].forEach(width => {
            const col = document.createElement('col');
            col.style.width = width + '%';
            columns.appendChild(col);
        });
        const body = document.createElement('tbody');
        table.appendChild(columns);
        table.appendChild(body);
        container.appendChild(table);
        let jobs = [];
        let running = false;
        let nextId = 0;
        let serverChunkLimit = Infinity;
        let limitsXhr = null;

        function hidden(element, hide) { element.classList.toggle('hidden', hide); }
        function disabled(element, disable) {
            element.classList.toggle('disabled', disable);
            if (disable) element.setAttribute('disabled', 'disabled');
            else element.removeAttribute('disabled');
        }
        function alert(message, kind) {
            controls.alert.replaceChildren();
            if (!message) return;
            const box = document.createElement('div');
            box.className = 'alert alert-' + kind + ' alert-dismissible';
            box.setAttribute('role', 'alert');
            const close = document.createElement('button');
            close.type = 'button';
            close.className = 'close';
            close.textContent = '×';
            close.setAttribute('aria-label', 'Close');
            close.addEventListener('click', () => box.remove());
            box.appendChild(close);
            box.appendChild(document.createTextNode(message));
            controls.alert.appendChild(box);
        }
        function validate(file) {
            const ext = file.name.split('.').pop().toLowerCase();
            if (options.acceptFileTypes && !options.acceptFileTypes.map(v => String(v).toLowerCase()).includes(ext)) return text('acceptFileTypes_Lbl');
            if (!file.size) return text('empty');
            if (options.maxFileSize && file.size > options.maxFileSize) return text('maxFileSize_Lbl');
            if (options.minFileSize && file.size < options.minFileSize) return text('minFileSize_Lbl');
            return '';
        }
        function selectionValid() {
            return jobs.length > 0 && !jobs.some(job => job.validation) &&
                !(options.maxNumberOfFiles && jobs.length > options.maxNumberOfFiles);
        }
        function refresh() {
            disabled(controls.picker, running);
            disabled(controls.input, running);
            disabled(controls.start, running || !selectionValid() || !jobs.some(job => job.state === 'queued'));
            disabled(controls.cancel, !jobs.some(job => ['queued', 'uploading', 'processing'].includes(job.state)));
            disabled(controls.back, running || jobs.length > 0);
            hidden(controls.donebar, running || !jobs.length || jobs.some(job => job.state === 'queued'));
            disabled(controls.done, running);
            controls.errors.replaceChildren();
        }
        function selectionMessage() {
            alert('', 'info');
            if (options.maxNumberOfFiles && jobs.length > options.maxNumberOfFiles) {
                alert(text('maxNumberOfFiles_Lbl').replace('%d', options.maxNumberOfFiles), 'danger');
            } else if (jobs.some(job => job.validation)) alert(text('remove_invalid_inputs_Lbl'), 'danger');
            refresh();
        }
        function addElement(parent, tag, className, content) {
            const element = document.createElement(tag);
            element.className = className;
            if (content !== undefined) element.textContent = content;
            parent.appendChild(element);
            return element;
        }
        function addFile(file) {
            const uploadId = Date.now().toString(36) + '-' + Math.random().toString(36).slice(2) + Math.random().toString(36).slice(2);
            const job = {id: ++nextId, uploadId, file, state: 'queued', validation: validate(file), xhr: null, timer: null, percent: 0};
            const row = addElement(body, 'tr', '');
            row.dataset.uploadId = String(job.id);
            const preview = addElement(addElement(row, 'td', ''), 'span', 'preview');
            const ext = file.name.split('.').pop().toLowerCase();
            if (!options.showIcons && file.type.startsWith('image/')) {
                const image = addElement(preview, 'img', '');
                job.previewUrl = URL.createObjectURL(file);
                image.src = job.previewUrl;
                image.width = options.previewMaxWidth;
                image.height = options.previewMaxHeight;
                image.alt = '';
            } else preview.innerHTML = getFileIconExtension(ext.replace(/[^a-z0-9]/g, ''));
            const nameCell = addElement(row, 'td', '');
            addElement(nameCell, 'p', 'name', file.name);
            const track = addElement(nameCell, 'div', 'progress-bar hidden');
            track.setAttribute('role', 'progressbar');
            track.setAttribute('aria-label', file.name);
            track.setAttribute('aria-valuemin', '0');
            track.setAttribute('aria-valuemax', '100');
            const bar = addElement(track, 'div', 'progress');
            bar.style.transition = 'width 180ms linear';
            bar.style.width = '0%';
            const error = addElement(nameCell, 'strong', 'error text-error', job.validation);
            const size = addElement(row, 'td', '');
            addElement(size, 'span', 'size', readableBytes(file.size));
            const percentBox = addElement(size, 'div', 'percent-bar hidden');
            const percent = addElement(percentBox, 'span', 'percent', '0%');
            const buttons = addElement(row, 'td', '');
            const cancel = addElement(buttons, 'button', 'btn btn-warning cancel', text('cancel_Lbl'));
            cancel.type = 'button';
            const ready = addElement(buttons, 'span', 'ready hidden');
            ready.innerHTML = '<svg viewBox="0 0 56 56" class="svg-file svg-check files-svg"><path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"></path></svg>';
            const bubble = addElement(buttons, 'span', 'error error-bubble hidden');
            bubble.innerHTML = '<svg viewBox="0 0 56 56" class="svg-file svg-error files-svg"><path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z"></path></svg>';
            job.ui = {row, track, bar, error, percentBox, percent, cancel, ready, bubble};
            cancel.addEventListener('click', event => {
                event.preventDefault();
                if (!running && job.state === 'queued') {
                    release(job);
                    jobs = jobs.filter(item => item !== job);
                    selectionMessage();
                } else if (['queued', 'uploading', 'processing'].includes(job.state)) {
                    const xhr = job.xhr;
                    finish(job, 'cancelled', text('upload_cancelled_Lbl'));
                    if (xhr) xhr.abort();
                }
            });
            jobs.push(job);
        }
        function release(job) {
            clearTimeout(job.timer);
            if (job.previewUrl) URL.revokeObjectURL(job.previewUrl);
            job.ui.row.remove();
        }
        function progress(job, bytes) {
            job.percent = Math.max(job.percent, Math.min(99, Math.floor(bytes / job.file.size * 100)));
            job.ui.bar.style.width = job.percent + '%';
            job.ui.track.setAttribute('aria-valuenow', String(job.percent));
            job.ui.percent.textContent = job.state === 'processing' ? text('processing') : job.percent + '%';
        }
        function terminal(job) { return ['success', 'error', 'cancelled'].includes(job.state); }
        function finish(job, state, message) {
            if (terminal(job)) return;
            job.state = state;
            job.xhr = null;
            hidden(job.ui.cancel, true);
            hidden(job.ui.ready, state !== 'success');
            hidden(job.ui.bubble, state !== 'error');
            job.ui.error.textContent = message || '';
            if (state === 'success') {
                job.ui.bar.style.width = '100%';
                job.ui.track.setAttribute('aria-valuenow', '100');
                job.ui.percent.textContent = '100%';
                job.timer = setTimeout(() => {
                    hidden(job.ui.track, true);
                    hidden(job.ui.percentBox, true);
                }, Math.max(200, Number(options.progressHoldTime) || 600));
            } else {
                hidden(job.ui.track, true);
                hidden(job.ui.percentBox, true);
            }
            pump();
        }
        function errorMessage(response, fallback) {
            if (!response || !response.error) return fallback;
            return typeof response.error === 'string' ? response.error : response.error.message || fallback;
        }
        function send(job, start, chunkNumber) {
            if (terminal(job)) return;
            const chunked = options.chunkUpload !== false;
            const chunkSize = Math.max(1, Math.floor(Math.min(serverChunkLimit, Number(options.maxChunkSize) || 1024 * 1024)));
            const end = chunked ? Math.min(start + chunkSize, job.file.size) : job.file.size;
            const count = chunked ? Math.ceil(job.file.size / chunkSize) : 1;
            const last = end === job.file.size;
            const data = new FormData();
            data.append('chunkEnabled', chunked ? 'true' : 'false');
            data.append('files', chunked ? job.file.slice(start, end) : job.file, job.file.name);
            if (chunked) {
                data.append('chunk', chunkNumber);
                data.append('index', start);
                data.append('count', count);
                data.append('uploadId', job.uploadId);
                data.append('totalSize', job.file.size);
            }
            const xhr = new XMLHttpRequest();
            job.xhr = xhr;
            const current = () => job.xhr === xhr && !terminal(job);
            try {
                xhr.open('POST', options.url || form.getAttribute('action'), true);
                xhr.timeout = Math.max(0, Number(options.requestTimeout) || 0);
                xhr.upload.addEventListener('progress', event => {
                    if (!current() || !event.lengthComputable || !event.total) return;
                    progress(job, start + (end - start) * event.loaded / event.total);
                    if (last && event.loaded >= event.total) {
                        job.state = 'processing';
                        job.ui.percent.textContent = text('processing');
                    }
                });
                xhr.onload = () => {
                    if (!current()) return;
                    let response;
                    try { response = JSON.parse(xhr.responseText); } catch (_) {
                        finish(job, 'error', text('invalid') + ' (HTTP ' + xhr.status + ')');
                        return;
                    }
                    if (xhr.status < 200 || xhr.status >= 300) {
                        finish(job, 'error', errorMessage(response, text('error_uploading_file_Lbl')) + ' (HTTP ' + xhr.status + ')');
                        return;
                    }
                    if (!response || typeof response !== 'object' || Array.isArray(response) ||
                        !Object.prototype.hasOwnProperty.call(response, 'error')) {
                        finish(job, 'error', text('invalid'));
                        return;
                    }
                    if (response.error) { finish(job, 'error', errorMessage(response, text('error_uploading_file_Lbl'))); return; }
                    if (!last) {
                        if (response.merged !== false || Number(response.chunk) !== chunkNumber || Number(response.count) !== count) {
                            finish(job, 'error', text('invalid'));
                            return;
                        }
                        progress(job, end);
                        send(job, end, chunkNumber + 1);
                    } else {
                        // The current PHP endpoint omits merged on final success.
                        if (response.merged === false || typeof response.filename !== 'string' ||
                            !response.path || Number(response.size) !== job.file.size) {
                            finish(job, 'error', text('invalid'));
                            return;
                        }
                        finish(job, 'success');
                    }
                };
                xhr.onerror = () => { if (current()) finish(job, 'error', text('error_uploading_file_Lbl')); };
                xhr.ontimeout = () => { if (current()) finish(job, 'error', text('timeout')); };
                xhr.onabort = () => { if (current()) finish(job, 'cancelled', text('upload_cancelled_Lbl')); };
                xhr.send(data);
            } catch (_) { if (current()) finish(job, 'error', text('error_uploading_file_Lbl')); }
        }
        function pump() {
            if (!running) return;
            if (limitsXhr) { refresh(); return; }
            const active = () => jobs.filter(job => ['uploading', 'processing'].includes(job.state));
            const limit = Math.max(1, Math.floor(Number(options.limitConcurrentUploads) || 1));
            for (const job of jobs) {
                if (active().length >= limit) break;
                if (job.state !== 'queued') continue;
                // Legacy PHP keys temporary parts by basename, so identical names must run serially.
                if (active().some(other => other.file.name.toLowerCase() === job.file.name.toLowerCase())) continue;
                job.state = 'uploading';
                hidden(job.ui.track, false);
                hidden(job.ui.percentBox, false);
                progress(job, 0);
                send(job, 0, 1);
            }
            if (jobs.every(terminal)) {
                running = false;
                const failed = jobs.filter(job => job.state === 'error').length;
                const cancelled = jobs.some(job => job.state === 'cancelled');
                if (failed) alert(text(failed === jobs.length ? 'file_transfer_failed_completely_Lbl' : 'file_transfer_failed_partially_Lbl'), failed === jobs.length ? 'danger' : 'warning');
                else if (cancelled) alert(text('upload_cancelled_Lbl'), 'info');
                else alert(text('file_transfer_completed_successfully_Lbl'), 'success');
            }
            refresh();
        }
        function reset() {
            running = false;
            if (limitsXhr) { const pending = limitsXhr; limitsXhr = null; pending.abort(); }
            jobs.forEach(job => {
                const xhr = job.xhr;
                job.state = 'cancelled';
                job.xhr = null;
                if (xhr) xhr.abort();
                release(job);
            });
            jobs = [];
            controls.input.value = '';
            alert('', 'info');
            refresh();
        }
        function negotiateLimits() {
            if (!options.serverLimits) { pump(); return; }
            const xhr = new XMLHttpRequest();
            limitsXhr = xhr;
            const url = String(options.url || form.getAttribute('action')).split('#')[0];
            const current = () => running && limitsXhr === xhr;
            const fail = message => {
                if (!current()) return;
                limitsXhr = null;
                running = false;
                jobs.filter(job => !terminal(job)).forEach(job => finish(job, 'error', message));
                running = true;
                pump();
            };
            try {
                xhr.open('GET', url + (url.includes('?') ? '&' : '?') + 'uploadCapabilities=1', true);
                xhr.timeout = Math.max(1000, Number(options.requestTimeout) || 120000);
                xhr.onload = () => {
                    if (!current()) return;
                    let response;
                    try { response = JSON.parse(xhr.responseText); } catch (_) { fail(text('invalid')); return; }
                    if (xhr.status < 200 || xhr.status >= 300 || !response || response.error ||
                        response.protocol !== 2 || response.uploadsEnabled !== true ||
                        !Number.isSafeInteger(response.maxChunkSize) || response.maxChunkSize < 1) {
                        fail(errorMessage(response, text('error_uploading_file_Lbl')));
                        return;
                    }
                    serverChunkLimit = response.maxChunkSize;
                    limitsXhr = null;
                    pump();
                };
                xhr.onerror = () => fail(text('error_uploading_file_Lbl'));
                xhr.ontimeout = () => fail(text('timeout'));
                xhr.onabort = () => fail(text('upload_cancelled_Lbl'));
                refresh();
                xhr.send();
            } catch (_) { fail(text('error_uploading_file_Lbl')); }
        }
        controls.input.addEventListener('change', event => {
            if (running) return;
            Array.from(event.target.files).forEach(addFile);
            controls.input.value = '';
            selectionMessage();
        });
        controls.start.addEventListener('click', event => {
            event.preventDefault();
            if (running) return;
            if (!selectionValid()) { selectionMessage(); return; }
            if (!jobs.some(job => job.state === 'queued')) return;
            running = true;
            alert('', 'info');
            negotiateLimits();
        });
        controls.cancel.addEventListener('click', event => {
            event.preventDefault();
            if (!running) { reset(); alert(text('upload_cancelled_Lbl'), 'info'); return; }
            // Stop scheduling first: aborting one file must not start another queued file.
            running = false;
            if (limitsXhr) { const pending = limitsXhr; limitsXhr = null; pending.abort(); }
            jobs.filter(job => !terminal(job)).forEach(job => {
                const xhr = job.xhr;
                finish(job, 'cancelled', text('upload_cancelled_Lbl'));
                if (xhr) xhr.abort();
            });
            alert(text('upload_cancelled_Lbl'), 'info');
            refresh();
        });
        // Do not prevent or replace QCubed's own Done AJAX action.
        controls.done.addEventListener('click', () => { if (!running) reset(); });
        refresh();
        return {setOption(name, value) {
            if (running) return;
            options[name] = value;
            jobs.filter(job => job.state === 'queued').forEach(job => {
                job.validation = validate(job.file);
                job.ui.error.textContent = job.validation;
            });
            selectionMessage();
        }};

        function readableBytes(bytes) {
            if (!bytes) return '0 B';
            const units = ['B', 'KB', 'MB', 'GB', 'TB'];
            const index = Math.min(units.length - 1, Math.floor(Math.log(bytes) / Math.log(1024)));
            return (bytes / Math.pow(1024, index)).toFixed(2) + ' ' + units[index];
        }
                function getFileIconExtension(ext) {
            let icon;
            switch (ext) {
                case 'gif':
                case 'jpg':
                case 'jpeg':
                case 'jpc':
                case 'png':
                case 'bmp':
                    icon = '<svg class="svg-file svg-image files-svg" viewBox="0 0 56 56"><path class="svg-file-bg" d="M36.985,0H7.963C7.155,0,6.5,0.655,6.5,1.926V55c0,0.345,0.655,1,1.463,1h40.074 c0.808,0,1.463-0.655,1.463-1V12.978c0-0.696-0.093-0.92-0.257-1.085L37.607,0.257C37.442,0.093,37.218,0,36.985,0z"></path><polygon class="svg-file-flip" points="37.5,0.151 37.5,12 49.349,12"></polygon><g class="svg-file-icon"> <circle cx="18.931" cy="14.431" r="4.569" style="fill:#f3d55b"></circle> <polygon points="6.5,39 17.5,39 49.5,39 49.5,28 39.5,18.5 29,30 23.517,24.517" style="fill:#88c057"></polygon> </g> <path class="svg-file-text-bg" d="M48.037,56H7.963C7.155,56,6.5,55.345,6.5,54.537V39h43v15.537C49.5,55.345,48.845,56,48.037,56z"></path> <text class="svg-file-ext" x="28" y="51.5">' + ext + '</text> </svg>';
                    break;
                case 'pdf':
                    icon = '<svg class="svg-file svg-pdf files-svg" viewBox="0 0 56 56"> <path class="svg-file-bg" d="M36.985,0H7.963C7.155,0,6.5,0.655,6.5,1.926V55c0,0.345,0.655,1,1.463,1h40.074 c0.808,0,1.463-0.655,1.463-1V12.978c0-0.696-0.093-0.92-0.257-1.085L37.607,0.257C37.442,0.093,37.218,0,36.985,0z"></path><polygon class="svg-file-flip" points="37.5,0.151 37.5,12 49.349,12"></polygon><g class="svg-file-icon"><path d="M19.514,33.324L19.514,33.324c-0.348,0-0.682-0.113-0.967-0.326 c-1.041-0.781-1.181-1.65-1.115-2.242c0.182-1.628,2.195-3.332,5.985-5.068c1.504-3.296,2.935-7.357,3.788-10.75 c-0.998-2.172-1.968-4.99-1.261-6.643c0.248-0.579,0.557-1.023,1.134-1.215c0.228-0.076,0.804-0.172,1.016-0.172 c0.504,0,0.947,0.649,1.261,1.049c0.295,0.376,0.964,1.173-0.373,6.802c1.348,2.784,3.258,5.62,5.088,7.562 c1.311-0.237,2.439-0.358,3.358-0.358c1.566,0,2.515,0.365,2.902,1.117c0.32,0.622,0.189,1.349-0.39,2.16 c-0.557,0.779-1.325,1.191-2.22,1.191c-1.216,0-2.632-0.768-4.211-2.285c-2.837,0.593-6.15,1.651-8.828,2.822 c-0.836,1.774-1.637,3.203-2.383,4.251C21.273,32.654,20.389,33.324,19.514,33.324z M22.176,28.198 c-2.137,1.201-3.008,2.188-3.071,2.744c-0.01,0.092-0.037,0.334,0.431,0.692C19.685,31.587,20.555,31.19,22.176,28.198z M35.813,23.756c0.815,0.627,1.014,0.944,1.547,0.944c0.234,0,0.901-0.01,1.21-0.441c0.149-0.209,0.207-0.343,0.23-0.415 c-0.123-0.065-0.286-0.197-1.175-0.197C37.12,23.648,36.485,23.67,35.813,23.756z M28.343,17.174 c-0.715,2.474-1.659,5.145-2.674,7.564c2.09-0.811,4.362-1.519,6.496-2.02C30.815,21.15,29.466,19.192,28.343,17.174z M27.736,8.712c-0.098,0.033-1.33,1.757,0.096,3.216C28.781,9.813,27.779,8.698,27.736,8.712z"></path></g><path class="svg-file-text-bg" d="M48.037,56H7.963C7.155,56,6.5,55.345,6.5,54.537V39h43v15.537C49.5,55.345,48.845,56,48.037,56z"></path><text class="svg-file-ext" x="28" y="51.5">' + ext + '</text></svg>';
                    break;
                case 'docx':
                case 'doc':
                    icon = '<svg class="svg-file svg-word files-svg" viewBox="0 0 56 56"><path class="svg-file-bg" d="M36.985,0H7.963C7.155,0,6.5,0.655,6.5,1.926V55c0,0.345,0.655,1,1.463,1h40.074 c0.808,0,1.463-0.655,1.463-1V12.978c0-0.696-0.093-0.92-0.257-1.085L37.607,0.257C37.442,0.093,37.218,0,36.985,0z"></path><polygon class="svg-file-flip" points="37.5,0.151 37.5,12 49.349,12"></polygon><g class="svg-file-icon"><path d="M12.5,13h6c0.553,0,1-0.448,1-1s-0.447-1-1-1h-6c-0.553,0-1,0.448-1,1S11.947,13,12.5,13z"></path><path d="M12.5,18h9c0.553,0,1-0.448,1-1s-0.447-1-1-1h-9c-0.553,0-1,0.448-1,1S11.947,18,12.5,18z"></path><path d="M25.5,18c0.26,0,0.52-0.11,0.71-0.29c0.18-0.19,0.29-0.45,0.29-0.71c0-0.26-0.11-0.52-0.29-0.71 c-0.38-0.37-1.04-0.37-1.42,0c-0.181,0.19-0.29,0.44-0.29,0.71s0.109,0.52,0.29,0.71C24.979,17.89,25.24,18,25.5,18z"></path><path d="M29.5,18h8c0.553,0,1-0.448,1-1s-0.447-1-1-1h-8c-0.553,0-1,0.448-1,1S28.947,18,29.5,18z"></path><path d="M11.79,31.29c-0.181,0.19-0.29,0.44-0.29,0.71s0.109,0.52,0.29,0.71 C11.979,32.89,12.229,33,12.5,33c0.27,0,0.52-0.11,0.71-0.29c0.18-0.19,0.29-0.45,0.29-0.71c0-0.26-0.11-0.52-0.29-0.71 C12.84,30.92,12.16,30.92,11.79,31.29z"></path><path d="M24.5,31h-8c-0.553,0-1,0.448-1,1s0.447,1,1,1h8c0.553,0,1-0.448,1-1S25.053,31,24.5,31z"></path><path d="M41.5,18h2c0.553,0,1-0.448,1-1s-0.447-1-1-1h-2c-0.553,0-1,0.448-1,1S40.947,18,41.5,18z"></path><path d="M12.5,23h22c0.553,0,1-0.448,1-1s-0.447-1-1-1h-22c-0.553,0-1,0.448-1,1S11.947,23,12.5,23z"></path><path d="M43.5,21h-6c-0.553,0-1,0.448-1,1s0.447,1,1,1h6c0.553,0,1-0.448,1-1S44.053,21,43.5,21z"></path><path d="M12.5,28h4c0.553,0,1-0.448,1-1s-0.447-1-1-1h-4c-0.553,0-1,0.448-1,1S11.947,28,12.5,28z"></path><path d="M30.5,26h-10c-0.553,0-1,0.448-1,1s0.447,1,1,1h10c0.553,0,1-0.448,1-1S31.053,26,30.5,26z"></path><path d="M43.5,26h-9c-0.553,0-1,0.448-1,1s0.447,1,1,1h9c0.553,0,1-0.448,1-1S44.053,26,43.5,26z"></path></g><path class="svg-file-text-bg" d="M48.037,56H7.963C7.155,56,6.5,55.345,6.5,54.537V39h43v15.537C49.5,55.345,48.845,56,48.037,56z"></path><text class="svg-file-ext" x="28" y="51.5">' + ext + '</text></svg>';
                    break;
                case 'xlsx':
                case 'xls':
                    icon = '<svg viewBox="0 0 56 56" class="svg-file svg-excel files-svg"><path class="svg-file-bg" d="M36.985,0H7.963C7.155,0,6.5,0.655,6.5,1.926V55c0,0.345,0.655,1,1.463,1h40.074 c0.808,0,1.463-0.655,1.463-1V12.978c0-0.696-0.093-0.92-0.257-1.085L37.607,0.257C37.442,0.093,37.218,0,36.985,0z"></path><polygon class="svg-file-flip" points="37.5,0.151 37.5,12 49.349,12"></polygon><g class="svg-file-icon"><path style="fill:#c8bdb8" d="M23.5,16v-4h-12v4v2v2v2v2v2v2v2v4h10h2h21v-4v-2v-2v-2v-2v-2v-4H23.5z M13.5,14h8v2h-8V14z M13.5,18h8v2h-8V18z M13.5,22h8v2h-8V22z M13.5,26h8v2h-8V26z M21.5,32h-8v-2h8V32z M42.5,32h-19v-2h19V32z M42.5,28h-19v-2h19V28 z M42.5,24h-19v-2h19V24z M23.5,20v-2h19v2H23.5z"></path></g><path class="svg-file-text-bg" d="M48.037,56H7.963C7.155,56,6.5,55.345,6.5,54.537V39h43v15.537C49.5,55.345,48.845,56,48.037,56z"></path><text class="svg-file-ext" x="28" y="51.5">' + ext + '</text></svg>';
                    break;
                case 'pptx':
                case 'ppt':
                    icon = '<svg viewBox="0 0 56 56" class="svg-file svg-powerpoint files-svg"><path class="svg-file-bg" d="M36.985,0H7.963C7.155,0,6.5,0.655,6.5,1.926V55c0,0.345,0.655,1,1.463,1h40.074 c0.808,0,1.463-0.655,1.463-1V12.978c0-0.696-0.093-0.92-0.257-1.085L37.607,0.257C37.442,0.093,37.218,0,36.985,0z"></path><polygon class="svg-file-flip" points="37.5,0.151 37.5,12 49.349,12"></polygon><g class="svg-file-icon"><path style="fill:#c8bdb8" d="M39.5,30h-24V14h24V30z M17.5,28h20V16h-20V28z"></path><path style="fill:#c8bdb8" d="M20.499,35c-0.175,0-0.353-0.046-0.514-0.143c-0.474-0.284-0.627-0.898-0.343-1.372l3-5 c0.284-0.474,0.898-0.627,1.372-0.343c0.474,0.284,0.627,0.898,0.343,1.372l-3,5C21.17,34.827,20.839,35,20.499,35z"></path><path style="fill:#c8bdb8" d="M34.501,35c-0.34,0-0.671-0.173-0.858-0.485l-3-5c-0.284-0.474-0.131-1.088,0.343-1.372 c0.474-0.283,1.088-0.131,1.372,0.343l3,5c0.284,0.474,0.131,1.088-0.343,1.372C34.854,34.954,34.676,35,34.501,35z"></path><path style="fill:#c8bdb8" d="M27.5,16c-0.552,0-1-0.447-1-1v-3c0-0.553,0.448-1,1-1s1,0.447,1,1v3C28.5,15.553,28.052,16,27.5,16 z"></path><rect x="17.5" y="16" style="fill:#d3ccc9" width="20" height="12"></rect></g><path class="svg-file-text-bg" d="M48.037,56H7.963C7.155,56,6.5,55.345,6.5,54.537V39h43v15.537C49.5,55.345,48.845,56,48.037,56z"></path><text class="svg-file-ext" x="28" y="51.5">' + ext + '</text></svg>';
                    break;
                case 'mov':
                case 'mpeg':
                case 'mpg':
                case 'mp4':
                case 'm4v':
                    icon = '<svg viewBox="0 0 56 56" class="svg-file svg-video files-svg"><path class="svg-file-bg" d="M36.985,0H7.963C7.155,0,6.5,0.655,6.5,1.926V55c0,0.345,0.655,1,1.463,1h40.074 c0.808,0,1.463-0.655,1.463-1V12.978c0-0.696-0.093-0.92-0.257-1.085L37.607,0.257C37.442,0.093,37.218,0,36.985,0z"></path>\<polygon class="svg-file-flip" points="37.5,0.151 37.5,12 49.349,12"></polygon><g class="svg-file-icon"><path d="M24.5,28c-0.166,0-0.331-0.041-0.481-0.123C23.699,27.701,23.5,27.365,23.5,27V13 c0-0.365,0.199-0.701,0.519-0.877c0.321-0.175,0.71-0.162,1.019,0.033l11,7C36.325,19.34,36.5,19.658,36.5,20 s-0.175,0.66-0.463,0.844l-11,7C24.874,27.947,24.687,28,24.5,28z M25.5,14.821v10.357L33.637,20L25.5,14.821z"></path><path d="M28.5,35c-8.271,0-15-6.729-15-15s6.729-15,15-15s15,6.729,15,15S36.771,35,28.5,35z M28.5,7 c-7.168,0-13,5.832-13,13s5.832,13,13,13s13-5.832,13-13S35.668,7,28.5,7z"></path></g><path class="svg-file-text-bg" d="M48.037,56H7.963C7.155,56,6.5,55.345,6.5,54.537V39h43v15.537C49.5,55.345,48.845,56,48.037,56z"></path><text class="svg-file-ext" x="28" y="51.5">' + ext + '</text></svg>';
                    break;
                case 'wav':
                case 'mp3':
                case 'mp2':
                case 'm4a':
                case 'aac':
                    icon = '<svg viewBox="0 0 56 56" class="svg-file svg-audio files-svg"><path class="svg-file-bg" d="M36.985,0H7.963C7.155,0,6.5,0.655,6.5,1.926V55c0,0.345,0.655,1,1.463,1h40.074 c0.808,0,1.463-0.655,1.463-1V12.978c0-0.696-0.093-0.92-0.257-1.085L37.607,0.257C37.442,0.093,37.218,0,36.985,0z"></path><polygon class="svg-file-flip" points="37.5,0.151 37.5,12 49.349,12"></polygon><g class="svg-file-icon"><path d="M35.67,14.986c-0.567-0.796-1.3-1.543-2.308-2.351c-3.914-3.131-4.757-6.277-4.862-6.738V5 c0-0.553-0.447-1-1-1s-1,0.447-1,1v1v8.359v9.053h-3.706c-3.882,0-6.294,1.961-6.294,5.117c0,3.466,2.24,5.706,5.706,5.706 c3.471,0,6.294-2.823,6.294-6.294V16.468l0.298,0.243c0.34,0.336,0.861,0.72,1.521,1.205c2.318,1.709,6.2,4.567,5.224,7.793 C35.514,25.807,35.5,25.904,35.5,26c0,0.43,0.278,0.826,0.71,0.957C36.307,26.986,36.404,27,36.5,27c0.43,0,0.826-0.278,0.957-0.71 C39.084,20.915,37.035,16.9,35.67,14.986z M26.5,27.941c0,2.368-1.926,4.294-4.294,4.294c-2.355,0-3.706-1.351-3.706-3.706 c0-2.576,2.335-3.117,4.294-3.117H26.5V27.941z M31.505,16.308c-0.571-0.422-1.065-0.785-1.371-1.081l-1.634-1.34v-3.473 c0.827,1.174,1.987,2.483,3.612,3.783c0.858,0.688,1.472,1.308,1.929,1.95c0.716,1.003,1.431,2.339,1.788,3.978 C34.502,18.515,32.745,17.221,31.505,16.308z"></path></g><path class="svg-file-text-bg" d="M48.037,56H7.963C7.155,56,6.5,55.345,6.5,54.537V39h43v15.537C49.5,55.345,48.845,56,48.037,56z"></path><text class="svg-file-ext" x="28" y="51.5">' + ext + '</text></svg>';
                    break;
                case 'rtf':
                case 'txt':
                    icon = '<svg viewBox="0 0 56 56" class="svg-file svg-text files-svg"><path class="svg-file-bg" d="M36.985,0H7.963C7.155,0,6.5,0.655,6.5,1.926V55c0,0.345,0.655,1,1.463,1h40.074 c0.808,0,1.463-0.655,1.463-1V12.978c0-0.696-0.093-0.92-0.257-1.085L37.607,0.257C37.442,0.093,37.218,0,36.985,0z"></path><polygon class="svg-file-flip" points="37.5,0.151 37.5,12 49.349,12"></polygon><g class="svg-file-icon"><path d="M12.5,13h6c0.553,0,1-0.448,1-1s-0.447-1-1-1h-6c-0.553,0-1,0.448-1,1S11.947,13,12.5,13z"></path><path d="M12.5,18h9c0.553,0,1-0.448,1-1s-0.447-1-1-1h-9c-0.553,0-1,0.448-1,1S11.947,18,12.5,18z"></path><path d="M25.5,18c0.26,0,0.52-0.11,0.71-0.29c0.18-0.19,0.29-0.45,0.29-0.71c0-0.26-0.11-0.52-0.29-0.71 c-0.38-0.37-1.04-0.37-1.42,0c-0.181,0.19-0.29,0.44-0.29,0.71s0.109,0.52,0.29,0.71C24.979,17.89,25.24,18,25.5,18z"></path><path d="M29.5,18h8c0.553,0,1-0.448,1-1s-0.447-1-1-1h-8c-0.553,0-1,0.448-1,1S28.947,18,29.5,18z"></path><path d="M11.79,31.29c-0.181,0.19-0.29,0.44-0.29,0.71s0.109,0.52,0.29,0.71 C11.979,32.89,12.229,33,12.5,33c0.27,0,0.52-0.11,0.71-0.29c0.18-0.19,0.29-0.45,0.29-0.71c0-0.26-0.11-0.52-0.29-0.71 C12.84,30.92,12.16,30.92,11.79,31.29z"></path><path d="M24.5,31h-8c-0.553,0-1,0.448-1,1s0.447,1,1,1h8c0.553,0,1-0.448,1-1S25.053,31,24.5,31z"></path><path d="M41.5,18h2c0.553,0,1-0.448,1-1s-0.447-1-1-1h-2c-0.553,0-1,0.448-1,1S40.947,18,41.5,18z"></path><path d="M12.5,23h22c0.553,0,1-0.448,1-1s-0.447-1-1-1h-22c-0.553,0-1,0.448-1,1S11.947,23,12.5,23z"></path><path d="M43.5,21h-6c-0.553,0-1,0.448-1,1s0.447,1,1,1h6c0.553,0,1-0.448,1-1S44.053,21,43.5,21z"></path><path d="M12.5,28h4c0.553,0,1-0.448,1-1s-0.447-1-1-1h-4c-0.553,0-1,0.448-1,1S11.947,28,12.5,28z"></path><path d="M30.5,26h-10c-0.553,0-1,0.448-1,1s0.447,1,1,1h10c0.553,0,1-0.448,1-1S31.053,26,30.5,26z"></path><path d="M43.5,26h-9c-0.553,0-1,0.448-1,1s0.447,1,1,1h9c0.553,0,1-0.448,1-1S44.053,26,43.5,26z"></path></g><path class="svg-file-text-bg" d="M48.037,56H7.963C7.155,56,6.5,55.345,6.5,54.537V39h43v15.537C49.5,55.345,48.845,56,48.037,56z"></path><text class="svg-file-ext" x="28" y="51.5">' + ext + '</text></svg>';
                    break;
                case 'zip':
                case 'rar':
                case 'asice':
                case 'cdoc':
                    icon = '<svg viewBox="0 0 56 56" class="svg-file svg-archive files-svg"><path class="svg-file-bg" d="M36.985,0H7.963C7.155,0,6.5,0.655,6.5,1.926V55c0,0.345,0.655,1,1.463,1h40.074 c0.808,0,1.463-0.655,1.463-1V12.978c0-0.696-0.093-0.92-0.257-1.085L37.607,0.257C37.442,0.093,37.218,0,36.985,0z"></path><polygon class="svg-file-flip" points="37.5,0.151 37.5,12 49.349,12"></polygon><g class="svg-file-icon"><path d="M28.5,24v-2h2v-2h-2v-2h2v-2h-2v-2h2v-2h-2v-2h2V8h-2V6h-2v2h-2v2h2v2h-2v2h2v2h-2v2h2v2h-2v2h2v2 h-4v5c0,2.757,2.243,5,5,5s5-2.243,5-5v-5H28.5z M30.5,29c0,1.654-1.346,3-3,3s-3-1.346-3-3v-3h6V29z"></path><path d="M26.5,30h2c0.552,0,1-0.447,1-1s-0.448-1-1-1h-2c-0.552,0-1,0.447-1,1S25.948,30,26.5,30z"></path></g><path class="svg-file-text-bg" d="M48.037,56H7.963C7.155,56,6.5,55.345,6.5,54.537V39h43v15.537C49.5,55.345,48.845,56,48.037,56z"></path><text class="svg-file-ext" x="28" y="51.5">' + ext + '</text></svg>';
                    break;
                default:
                    icon = '<svg viewBox="0 0 56 56" class="svg-file svg-none files-svg"><path class="svg-file-bg" d="M36.985,0H7.963C7.155,0,6.5,0.655,6.5,1.926V55c0,0.345,0.655,1,1.463,1h40.074 c0.808,0,1.463-0.655,1.463-1V12.978c0-0.696-0.093-0.92-0.257-1.085L37.607,0.257C37.442,0.093,37.218,0,36.985,0z"></path><polygon class="svg-file-flip" points="37.5,0.151 37.5,12 49.349,12"></polygon><path class="svg-file-text-bg" d="M48.037,56H7.963C7.155,56,6.5,55.345,6.5,54.537V39h43v15.537C49.5,55.345,48.845,56,48.037,56z"></path><text class="svg-file-ext f_10" x="28" y="51.5">' + ext + '</text></svg>';
            }

            return icon
        }


    }
})(jQuery);

(function () {
    'use strict';
    if (window.VauuDownload) return;
    const labels = {
        et: {wait: 'Valmistan allalaadimist ja koostan vajadusel ZIP-arhiivi. Palun oota…', ready: 'Allalaadimine algas. Edenemist näed brauseri allalaadimiste vaates.', error: 'Allalaadimine ebaõnnestus. Palun proovi uuesti.', slow: 'Ettevalmistus võtab oodatust kauem aega. Serverilt pole veel kinnitust saabunud.'},
        en: {wait: 'Preparing your download and creating a ZIP archive if needed. Please wait…', ready: 'Download started. Follow its progress in your browser downloads.', error: 'The download failed. Please try again.', slow: 'Preparation is taking longer than expected. The server has not confirmed it yet.'},
        ru: {wait: 'Подготовка загрузки и создание ZIP-архива. Пожалуйста, подождите…', ready: 'Загрузка началась. Следите за ней в списке загрузок браузера.', error: 'Не удалось начать загрузку. Попробуйте ещё раз.', slow: 'Подготовка занимает больше времени. Подтверждение сервера ещё не получено.'}
    };
    let language = 'en', box, poll, watchdog, fade, active = false, frame, buttonId;
    function message(key) {
        if (!box) {
            box = document.createElement('div');
            box.setAttribute('role', 'status');
            box.setAttribute('aria-live', 'polite');
            // Match toastr.css without moving/reconfiguring the CMS's other toasts.
            box.className = 'toast toast-warning toast-message';
            box.style.cssText = 'position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);z-index:999999;box-sizing:border-box;width:520px;max-width:calc(100vw - 32px);max-height:calc(100vh - 32px);overflow:auto;margin:0;padding:18px 22px 18px 54px;border-radius:3px;background-color:#F89406;background-position:15px center;background-repeat:no-repeat;color:#fff;box-shadow:0 0 12px #999;opacity:1;line-height:1.5;overflow-wrap:anywhere;pointer-events:none;';
            box.style.backgroundImage = 'url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAGYSURBVEhL5ZSvTsNQFMbXZGICMYGYmJhAQIJAICYQPAACiSDB8AiICQQJT4CqQEwgJvYASAQCiZiYmJhAIBATCARJy+9rTsldd8sKu1M0+dLb057v6/lbq/2rK0mS/TRNj9cWNAKPYIJII7gIxCcQ51cvqID+GIEX8ASG4B1bK5gIZFeQfoJdEXOfgX4QAQg7kH2A65yQ87lyxb27sggkAzAuFhbbg1K2kgCkB1bVwyIR9m2L7PRPIhDUIXgGtyKw575yz3lTNs6X4JXnjV+LKM/m3MydnTbtOKIjtz6VhCBq4vSm3ncdrD2lk0VgUXSVKjVDJXJzijW1RQdsU7F77He8u68koNZTz8Oz5yGa6J3H3lZ0xYgXBK2QymlWWA+RWnYhskLBv2vmE+hBMCtbA7KX5drWyRT/2JsqZ2IvfB9Y4bWDNMFbJRFmC9E74SoS0CqulwjkC0+5bpcV1CZ8NMej4pjy0U+doDQsGyo1hzVJttIjhQ7GnBtRFN1UarUlH8F3xict+HY07rEzoUGPlWcjRFRr4/gChZgc3ZL2d8oAAAAASUVORK5CYII=")';
            document.body.appendChild(box);
        }
        box.textContent = labels[language][key];
        box.hidden = false;
    }
    function finish(success) {
        clearInterval(poll); clearTimeout(watchdog); clearTimeout(fade);
        active = false;
        const button = buttonId && document.getElementById(buttonId);
        if (button) button.disabled = false;
        message(success ? 'ready' : 'error');
        fade = setTimeout(() => { box.hidden = true; }, success ? 8000 : 15000);
    }
    function start(code, id) {
        language = String(code || 'en').split('-')[0];
        if (!labels[language]) language = 'en';
        clearInterval(poll); clearTimeout(watchdog); clearTimeout(fade);
        active = true;
        buttonId = id || buttonId;
        const button = buttonId && document.getElementById(buttonId);
        if (button) button.disabled = true;
        message('wait');
        watchdog = setTimeout(() => { if (active) message('slow'); }, 120000);
    }
    function deliver(url, token) {
        if (!active) start(language);
        if (frame) frame.remove();
        frame = document.createElement('iframe');
        frame.hidden = true;
        frame.title = 'Download';
        frame.addEventListener('load', () => {
            // Attachment responses normally do not load a document; errors do.
            try { if (active && frame.contentDocument && frame.contentDocument.body.textContent.trim()) finish(false); } catch (_) {}
        });
        poll = setInterval(() => {
            const key = 'vauu_download_started_' + token;
            const marker = document.cookie.split(';').map(value => value.trim()).find(value => value === key + '=1');
            if (marker) {
                document.cookie = key + '=; Max-Age=0; Path=/; SameSite=Lax';
                finish(true);
            }
        }, 500);
        frame.src = url;
        document.body.appendChild(frame);
    }
    // QCubed uses jQuery AJAX; transport/timeout failures must not leave an endless wait notice.
    if (window.jQuery) window.jQuery(document).ajaxError(() => { if (active) finish(false); });
    window.VauuDownload = {start, deliver, finish};
})();
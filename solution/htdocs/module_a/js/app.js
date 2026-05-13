/* ── 다국어 ──────────────────────────────────────────── */
const i18n = {
    ko: {
        tagline:       '오늘의 순간을 네 컷에 담아보세요',
        touch:         '터치하여 시작',
        frame_title:   '프레임을 선택해주세요',
        frame_sub:     '원하는 프레임을 선택하고 촬영을 시작해보세요!',
        frame1_name:   '세로 4컷',
        frame1_desc:   '클래식한 4컷 모드',
        frame1_tag:    '4컷 세로형',
        frame2_name:   '가로 4컷',
        frame2_desc:   '와이드한 가로 모드',
        frame2_tag:    '4컷 가로형',
        frame3_name:   '2x2컷',
        frame3_desc:   '여유로운 2x2 모드',
        frame3_tag:    '2x2 세로형',
        btn_start:     '시작하기',
        shoot_hint:    '촬영',
        shot_done:     '완료',
        step1_title:   '사진 배치',
        step1_desc:    '슬롯을 클릭해 프레임에 배치하세요',
        btn_next:      '다음',
        step2_title:   '필터 적용',
        filter_default:'기본',
        filter_bw:     '흑백',
        step3_title:   '스티커 적용',
        step3_desc:    '스티커 선택 후 프레임을 클릭하세요',
        sticker_size:  '스티커 크기',
        delete_sticker:'선택 스티커 삭제',
        complete:      '완성',
        result_title:  '오늘의 네 컷이<br>완성됐어요',
        download_jpg:  'JPG 다운로드',
        download_png:  'PNG 다운로드',
        btn_home:      '홈으로',
    },
    en: {
        tagline:       'Capture today in four shots',
        touch:         'Touch to start',
        frame_title:   'Choose a frame',
        frame_sub:     'Select a frame and start shooting!',
        frame1_name:   'Vertical 4-cut',
        frame1_desc:   'Classic 4-cut mode',
        frame1_tag:    '4-cut vertical',
        frame2_name:   'Horizontal 4-cut',
        frame2_desc:   'Wide horizontal mode',
        frame2_tag:    '4-cut horizontal',
        frame3_name:   '2x2 cut',
        frame3_desc:   'Spacious 2x2 mode',
        frame3_tag:    '2x2 vertical',
        btn_start:     'Start',
        shoot_hint:    'Shoot',
        shot_done:     'done',
        step1_title:   'Arrange',
        step1_desc:    'Click a slot to place in frame',
        btn_next:      'Next',
        step2_title:   'Filter',
        filter_default:'Default',
        filter_bw:     'B&W',
        step3_title:   'Sticker',
        step3_desc:    'Select a sticker then click the frame',
        sticker_size:  'Sticker size',
        delete_sticker:'Delete sticker',
        complete:      'Complete',
        result_title:  'Your four shots<br>are ready!',
        download_jpg:  'Download JPG',
        download_png:  'Download PNG',
        btn_home:      'Home',
    },
};

let currentLang = 'ko';

function applyLang(lang) {
    currentLang = lang;
    document.body.classList.toggle('lang-en', lang === 'en');

    document.querySelectorAll('[data-i18n]').forEach(el => {
        const key = el.dataset.i18n;
        if (i18n[lang][key] !== undefined) el.innerHTML = i18n[lang][key];
    });

    document.querySelectorAll('[data-lang]').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.lang === lang);
    });
}

document.addEventListener('click', e => {
    const btn = e.target.closest('[data-lang]');
    if (btn) {
        e.stopPropagation();
        applyLang(btn.dataset.lang);
    }
});

/* ── 화면 전환 ───────────────────────────────────────────── */
function goTo(screenName) {
    document.querySelectorAll('.screen').forEach(s => s.classList.remove('active'));
    document.getElementById('screen-' + screenName).classList.add('active');
    if (screenName === 'capture') initCapture();
    if (screenName === 'arrange') initArrange();
    if (screenName === 'filter')  initFilter();
    if (screenName === 'sticker') initSticker();
    if (screenName === 'result')  initResult();
}

/* ── 시작 화면 ───────────────────────────────────────────── */
document.getElementById('screen-start').addEventListener('click', e => {
    if (e.target.closest('[data-lang]')) return;
    goTo('frame');
});

/* ── 전역 상태 ───────────────────────────────────────────── */
const state = {
    selectedFrame: null,
    capturedPhotos: [],
    arrangedPhotos: [],
    filter: 'none',
    stickers: [],
    nextStickerId: 0,
};

/* ── 프레임 선택 화면 ────────────────────────────────────── */
document.querySelectorAll('.frame-card').forEach(card => {
    card.addEventListener('click', () => {
        document.querySelectorAll('.frame-card').forEach(c => c.classList.remove('selected'));
        card.classList.add('selected');
        state.selectedFrame = Number(card.dataset.frame);
        document.getElementById('btnFrameStart').disabled = false;
    });
});

document.getElementById('btnFrameStart').addEventListener('click', () => goTo('capture'));

/* ── 유틸 ────────────────────────────────────────────────── */
function getRandomSample(frameType) {
    const n = String(Math.floor(Math.random() * 8) + 1).padStart(2, '0');
    if (frameType === 3) return `media/sample_vertical_${n}.png`;
    return Math.random() < 0.5
        ? `media/sample_square_${n}.png`
        : `media/sample_horizontal_${n}.png`;
}

function drawImageCover(ctx, img, dx, dy, dw, dh) {
    const srcW = img.naturalWidth, srcH = img.naturalHeight;
    const scale = Math.max(dw / srcW, dh / srcH);
    const cropW = dw / scale;
    const cropH = dh / scale;
    const sx = (srcW - cropW) / 2;
    const sy = (srcH - cropH) / 2;
    ctx.drawImage(img, sx, sy, cropW, cropH, dx, dy, dw, dh);
}

function loadImage(src) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.onload = () => resolve(img);
        img.onerror = reject;
        img.src = src;
    });
}

/* ── 촬영 화면 ───────────────────────────────────────────── */
let countdownTimer = null;
let countdownStart = null;

function initCapture() {
    clearInterval(countdownTimer);
    state.capturedPhotos = [];
    document.querySelectorAll('.shot-slot').forEach(slot => {
        slot.classList.remove('taken');
        const img = slot.querySelector('img');
        const span = slot.querySelector('span');
        if (img) img.style.display = 'none';
        if (span) span.style.display = '';
    });
    document.getElementById('shotCount').textContent = '0';
    document.getElementById('countdownNum').textContent = '5';
    startCountdown();
}

function startCountdown() {
    clearInterval(countdownTimer);
    countdownStart = Date.now();
    countdownTimer = setInterval(() => {
        const remaining = Math.ceil((5000 - (Date.now() - countdownStart)) / 1000);
        document.getElementById('countdownNum').textContent = Math.max(remaining, 0);
        if (Date.now() - countdownStart >= 5000) takePhoto();
    }, 10);
}

function takePhoto() {
    clearInterval(countdownTimer);

    const overlay = document.getElementById('flashOverlay');
    overlay.style.transition = 'none';
    overlay.style.opacity = '1';
    setTimeout(() => {
        overlay.style.transition = 'opacity 0.4s';
        overlay.style.opacity = '0';
    }, 50);

    const src = getRandomSample(state.selectedFrame);
    const idx = state.capturedPhotos.length;
    state.capturedPhotos.push(src);

    const slot = document.querySelector(`.shot-slot[data-slot="${idx}"]`);
    const img = slot.querySelector('img');
    const span = slot.querySelector('span');
    img.src = src;
    img.style.display = 'block';
    span.style.display = 'none';
    slot.classList.add('taken');
    document.getElementById('shotCount').textContent = state.capturedPhotos.length;

    if (state.capturedPhotos.length >= 4) {
        document.getElementById('countdownNum').textContent = '';
        setTimeout(() => goTo('arrange'), 600);
    } else {
        startCountdown();
    }
}

document.getElementById('btnShutter').addEventListener('click', takePhoto);

/* ── FRAME_LAYOUTS ───────────────────────────────────────── */
const FRAME_LAYOUTS = {
    1: {
        imgW: 418, imgH: 1487,
        cells: [
            { x: 28,  y: 39,   w: 361, h: 321 },
            { x: 28,  y: 376,  w: 361, h: 322 },
            { x: 28,  y: 714,  w: 361, h: 322 },
            { x: 28,  y: 1052, w: 361, h: 321 },
        ]
    },
    2: {
        imgW: 1678, imgH: 400,
        cells: [
            { x: 33,   y: 31, w: 316, h: 339 },
            { x: 366,  y: 31, w: 316, h: 339 },
            { x: 699,  y: 31, w: 316, h: 339 },
            { x: 1032, y: 31, w: 315, h: 339 },
        ]
    },
    3: {
        imgW: 731, imgH: 1042,
        cells: [
            { x: 36,  y: 40,  w: 316, h: 418 },
            { x: 373, y: 40,  w: 320, h: 418 },
            { x: 36,  y: 475, w: 316, h: 435 },
            { x: 373, y: 475, w: 320, h: 435 },
        ]
    }
};

/* ── canvas 렌더 ─────────────────────────────────────────── */
let _renderVer = 0;

async function renderPreviewCanvas(canvasId) {
    const ver = ++_renderVer;
    const layout = FRAME_LAYOUTS[state.selectedFrame];
    const canvas = document.getElementById(canvasId);

    const MAX_W = 900, MAX_H = 750;
    const scale = Math.min(MAX_W / layout.imgW, MAX_H / layout.imgH);
    const dw = Math.round(layout.imgW * scale);
    const dh = Math.round(layout.imgH * scale);

    canvas.width  = dw;
    canvas.height = dh;
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, dw, dh);

    // ① 프레임 PNG 먼저 (셀이 불투명이라 배경으로 깔기)
    const frameImg = await loadImage(`media/frame_0${state.selectedFrame}.png`);
    if (ver !== _renderVer) return;
    ctx.drawImage(frameImg, 0, 0, dw, dh);

    // ② 사진을 셀 위에 덮기
    for (let i = 0; i < layout.cells.length; i++) {
        const src = state.arrangedPhotos[i];
        if (!src) continue;
        const c = layout.cells[i];
        if (state.filter === 'bw') ctx.filter = 'grayscale(100%)';
        const img = await loadImage(src);
        if (ver !== _renderVer) return;
        drawImageCover(ctx, img, c.x * scale, c.y * scale, c.w * scale, c.h * scale);
        ctx.filter = 'none';
    }
}

/* ── 사진 배치 화면 ──────────────────────────────────────── */
function initArrange() {
    state.arrangedPhotos = [null, null, null, null];
    document.getElementById('btnArrangeNext').disabled = true;

    // 사이드바 슬롯에 촬영 사진 채우기
    document.querySelectorAll('.arrange-slot').forEach((slot, i) => {
        const img = slot.querySelector('img');
        if (img) img.src = state.capturedPhotos[i] || '';
        slot.classList.remove('used');
    });

    // canvas 삽입
    const preview = document.getElementById('arrangePreview');
    preview.innerHTML = '<canvas id="arrangeCanvas" class="preview-canvas"></canvas>';

    const canvas = document.getElementById('arrangeCanvas');
    canvas.addEventListener('click', e => {
        const layout = FRAME_LAYOUTS[state.selectedFrame];
        const renderScale = Math.min(900 / layout.imgW, 750 / layout.imgH);
        const rect = canvas.getBoundingClientRect();
        const cx = (e.clientX - rect.left) * (canvas.width / rect.width);
        const cy = (e.clientY - rect.top) * (canvas.height / rect.height);

        for (let i = 0; i < layout.cells.length; i++) {
            const c = layout.cells[i];
            if (cx >= c.x * renderScale && cx <= (c.x + c.w) * renderScale &&
                cy >= c.y * renderScale && cy <= (c.y + c.h) * renderScale) {
                if (state.arrangedPhotos[i] !== null) {
                    state.arrangedPhotos[i] = null;
                    renderPreviewCanvas('arrangeCanvas');
                    document.getElementById('btnArrangeNext').disabled = true;
                }
                break;
            }
        }
    });

    renderPreviewCanvas('arrangeCanvas');
}

document.getElementById('arrangeSlots').addEventListener('click', e => {
    const slot = e.target.closest('.arrange-slot');
    if (!slot) return;

    const idx = Number(slot.dataset.idx);
    const src = state.capturedPhotos[idx];
    if (!src) return;

    const emptyIdx = state.arrangedPhotos.indexOf(null);
    if (emptyIdx === -1) return;

    state.arrangedPhotos[emptyIdx] = src;
    renderPreviewCanvas('arrangeCanvas');

    if (state.arrangedPhotos.every(p => p !== null)) {
        document.getElementById('btnArrangeNext').disabled = false;
    }
});

document.getElementById('btnArrangeNext').addEventListener('click', () => goTo('filter'));

/* ── 필터 화면 ───────────────────────────────────────────── */
function initFilter() {
    state.filter = 'none';
    document.querySelectorAll('.filter-item').forEach(item => {
        item.classList.toggle('selected', item.dataset.filter === 'none');
    });
    renderPreviewCanvas('canvasFilter');
}

document.querySelectorAll('.filter-item').forEach(item => {
    item.addEventListener('click', () => {
        state.filter = item.dataset.filter;
        document.querySelectorAll('.filter-item').forEach(fi => fi.classList.toggle('selected', fi === item));
        renderPreviewCanvas('canvasFilter');
    });
});

document.getElementById('btnFilterNext').addEventListener('click', () => goTo('sticker'));

/* ── 스티커 화면 ─────────────────────────────────────────── */
let selectedStickerId = null;
let selectedStickerNum = null;

function initSticker() {
    state.stickers = [];
    state.nextStickerId = 0;
    selectedStickerId = null;
    selectedStickerNum = null;

    document.querySelectorAll('.sticker-opt').forEach(opt => opt.classList.remove('selected'));
    document.getElementById('stickerSize').value = 100;
    document.getElementById('stickerSizeVal').textContent = '100';

    const container = document.getElementById('framePreviewSticker');
    container.innerHTML = `<div style="position:relative;display:inline-block;">
        <canvas id="canvasSticker" class="preview-canvas"></canvas>
        <div id="stickerOverlay" style="position:absolute;top:0;left:0;width:100%;height:100%;pointer-events:none;"></div>
    </div>`;

    renderPreviewCanvas('canvasSticker');

    document.getElementById('canvasSticker').addEventListener('click', e => {
        if (selectedStickerNum === null) return;
        const canvas = document.getElementById('canvasSticker');
        const rect = canvas.getBoundingClientRect();
        const scale = canvas.width / rect.width;
        const x = (e.clientX - rect.left) * scale;
        const y = (e.clientY - rect.top) * scale;
        const sliderVal = Number(document.getElementById('stickerSize').value);

        const sticker = {
            id: state.nextStickerId++,
            src: `media/sticker_0${selectedStickerNum}.png`,
            x, y,
            size: sliderVal * scale,
        };
        state.stickers.push(sticker);
        renderStickerOverlay();
        selectStickerOverlay(sticker.id);
    });
}

function renderStickerOverlay() {
    const overlay = document.getElementById('stickerOverlay');
    const canvas = document.getElementById('canvasSticker');
    if (!overlay || !canvas) return;
    const rect = canvas.getBoundingClientRect();
    const logicalToDisplay = rect.width / canvas.width;

    overlay.innerHTML = '';
    state.stickers.forEach(sticker => {
        const img = document.createElement('img');
        img.src = sticker.src;
        const displaySize = sticker.size * logicalToDisplay;
        Object.assign(img.style, {
            position: 'absolute',
            left: `${sticker.x / canvas.width * 100}%`,
            top: `${sticker.y / canvas.height * 100}%`,
            width: `${displaySize}px`,
            height: `${displaySize}px`,
            transform: 'translate(-50%, -50%)',
            objectFit: 'contain',
            cursor: 'pointer',
            pointerEvents: 'auto',
            outline: sticker.id === selectedStickerId ? '2px solid var(--pb-accent)' : 'none',
            borderRadius: '4px',
        });
        img.addEventListener('click', e => {
            e.stopPropagation();
            selectStickerOverlay(sticker.id);
        });
        overlay.appendChild(img);
    });
}

function selectStickerOverlay(id) {
    selectedStickerId = id;
    const sticker = state.stickers.find(s => s.id === id);
    if (sticker) {
        const canvas = document.getElementById('canvasSticker');
        const rect = canvas.getBoundingClientRect();
        const displaySize = Math.round(sticker.size * rect.width / canvas.width);
        document.getElementById('stickerSize').value = displaySize;
        document.getElementById('stickerSizeVal').textContent = displaySize;
    }
    renderStickerOverlay();
}

document.querySelectorAll('.sticker-opt').forEach(opt => {
    opt.addEventListener('click', () => {
        document.querySelectorAll('.sticker-opt').forEach(o => o.classList.remove('selected'));
        opt.classList.add('selected');
        selectedStickerNum = Number(opt.dataset.sticker);
    });
});

document.getElementById('stickerSize').addEventListener('input', e => {
    const sliderVal = Number(e.target.value);
    document.getElementById('stickerSizeVal').textContent = sliderVal;
    if (selectedStickerId === null) return;
    const sticker = state.stickers.find(s => s.id === selectedStickerId);
    if (!sticker) return;
    const canvas = document.getElementById('canvasSticker');
    const rect = canvas.getBoundingClientRect();
    sticker.size = sliderVal * (canvas.width / rect.width);
    renderStickerOverlay();
});

document.getElementById('btnDeleteSticker').addEventListener('click', () => {
    if (selectedStickerId === null) return;
    state.stickers = state.stickers.filter(s => s.id !== selectedStickerId);
    selectedStickerId = null;
    renderStickerOverlay();
});

document.getElementById('btnStickerNext').addEventListener('click', () => goTo('result'));

/* ── 결과 화면 ───────────────────────────────────────────── */
async function initResult() {
    await renderPreviewCanvas('resultCanvas');
    const canvas = document.getElementById('resultCanvas');
    const ctx = canvas.getContext('2d');
    for (const sticker of state.stickers) {
        const img = await loadImage(sticker.src);
        ctx.drawImage(img,
            sticker.x - sticker.size / 2,
            sticker.y - sticker.size / 2,
            sticker.size, sticker.size);
    }
}

function downloadCanvas(format) {
    const canvas = document.getElementById('resultCanvas');
    const mimeType = format === 'jpg' ? 'image/jpeg' : 'image/png';
    const quality  = format === 'jpg' ? 0.95 : undefined;
    const a = document.createElement('a');
    a.href = canvas.toDataURL(mimeType, quality);
    a.download = `photobooth.${format}`;
    a.click();
}

document.getElementById('btnDownloadJpg').addEventListener('click', () => downloadCanvas('jpg'));
document.getElementById('btnDownloadPng').addEventListener('click', () => downloadCanvas('png'));

document.getElementById('btnHome').addEventListener('click', () => {
    state.selectedFrame = null;
    state.capturedPhotos = [];
    state.arrangedPhotos = [];
    state.filter = 'none';
    state.stickers = [];
    state.nextStickerId = 0;
    document.querySelectorAll('.frame-card').forEach(c => c.classList.remove('selected'));
    document.getElementById('btnFrameStart').disabled = true;
    goTo('start');
});

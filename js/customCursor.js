const site_wide_cursor = document.querySelector('.custom-cursor.site-wide');
let isDrawing = false;

document.addEventListener('mouseenter', () => {
    site_wide_cursor.style.display = 'block';
});

document.addEventListener('mouseleave', () => {
    site_wide_cursor.style.display = 'none';
});

document.addEventListener('mousemove', TrackCursor);
document.addEventListener('mousedown', startInkTrail);
document.addEventListener('mouseup', stopInkTrail);

function TrackCursor(evt) {
    const scrollX = window.scrollX || window.pageXOffset;
    const scrollY = window.scrollY || window.pageYOffset;
    const w = site_wide_cursor.clientWidth;
    const h = site_wide_cursor.clientHeight;
    site_wide_cursor.style.transform = `translate(${evt.clientX + scrollX}px, ${evt.clientY + scrollY}px)`;
    
    if (isDrawing) {
        createInkSpot(evt);
    }
}

function startInkTrail() {
    isDrawing = true;
}

function stopInkTrail() {
    isDrawing = false;
    const inkSpots = document.querySelectorAll('.ink-spot');
    inkSpots.forEach(spot => spot.remove());
}

function createInkSpot(evt) {
    const scrollX = window.scrollX || window.pageXOffset;
    const scrollY = window.scrollY || window.pageYOffset;
    const inkSpot = document.createElement('div');
    inkSpot.classList.add('ink-spot');
    inkSpot.style.left = `${evt.clientX + scrollX}px`;
    inkSpot.style.top = `${evt.clientY + scrollY}px`;
    document.body.appendChild(inkSpot);
}

window.addEventListener('load', () => {
    document.body.classList.remove('loading');
});

window.addEventListener('beforeunload', () => {
    document.body.classList.add('loading');
});

document.querySelectorAll('a, button,li,ul,img').forEach(el => {
    el.addEventListener('mouseenter', () => {
        site_wide_cursor.classList.add('active');
    });
    el.addEventListener('mouseleave', () => {
        site_wide_cursor.classList.remove('active');
    });
});

document.querySelectorAll('p,table').forEach(el => {
    el.addEventListener('mouseenter', () => {
        site_wide_cursor.classList.add('text');
    });
    el.addEventListener('mouseleave', () => {
        site_wide_cursor.classList.remove('text');
    });
});

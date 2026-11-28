<div id="eh-walkthrough-root" class="eh-wt-root" hidden>
    <div id="eh-wt-spotlight" class="eh-wt-spotlight" aria-hidden="true"></div>
    <div id="eh-wt-tooltip" class="eh-wt-tooltip" role="dialog" aria-modal="true" aria-labelledby="eh-wt-title">
        <div class="eh-wt-top">
            <span class="eh-wt-badge" id="eh-wt-badge">PANDUAN</span>
            <span class="eh-wt-counter" id="eh-wt-counter">1 / 1</span>
        </div>
        <h2 id="eh-wt-title"></h2>
        <p id="eh-wt-desc"></p>
        <div class="eh-wt-dots" id="eh-wt-dots"></div>
        <div class="eh-wt-actions">
            <button type="button" class="eh-wt-skip" id="eh-wt-skip">Lewati</button>
            <div class="eh-wt-nav">
                <button type="button" class="btn outline" id="eh-wt-prev">Sebelumnya</button>
                <button type="button" class="btn primary" id="eh-wt-next">Lanjut</button>
            </div>
        </div>
    </div>
</div>

<style>
.eh-wt-root {
    position: fixed;
    inset: 0;
    z-index: 9999;
    pointer-events: none;
}
.eh-wt-root[hidden] {
    display: none;
}
.eh-wt-spotlight {
    position: fixed;
    border-radius: 12px;
    box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.72), 0 0 0 2px var(--teal, #55d6c2);
    transition: top 0.25s ease, left 0.25s ease, width 0.25s ease, height 0.25s ease;
    pointer-events: none;
}
.eh-wt-tooltip {
    position: fixed;
    width: min(380px, calc(100vw - 24px));
    background: var(--surface, #20242b);
    border: 1px solid var(--border, #39414c);
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.55);
    color: var(--text, #eee);
    pointer-events: auto;
    transition: top 0.25s ease, left 0.25s ease;
}
.eh-wt-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
}
.eh-wt-badge {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.08em;
    color: var(--teal, #55d6c2);
    background: rgba(85, 214, 194, 0.12);
    padding: 4px 10px;
    border-radius: 999px;
}
.eh-wt-counter {
    font-size: 12px;
    color: var(--muted, #a1a1aa);
}
.eh-wt-tooltip h2 {
    margin: 0 0 8px;
    font-size: 18px;
    line-height: 1.3;
}
.eh-wt-tooltip p {
    margin: 0 0 16px;
    font-size: 13px;
    line-height: 1.6;
    color: var(--muted, #a1a1aa);
}
.eh-wt-dots {
    display: flex;
    gap: 6px;
    margin-bottom: 18px;
}
.eh-wt-dot {
    width: 7px;
    height: 7px;
    border-radius: 999px;
    background: #3f3f46;
    transition: width 0.2s ease, background 0.2s ease;
}
.eh-wt-dot.is-active {
    width: 20px;
    background: var(--teal, #55d6c2);
}
.eh-wt-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}
.eh-wt-skip {
    background: transparent;
    border: 0;
    color: var(--muted, #a1a1aa);
    font-size: 13px;
    cursor: pointer;
    padding: 6px 4px;
}
.eh-wt-skip:hover {
    color: #fff;
}
.eh-wt-nav {
    display: flex;
    gap: 8px;
}
</style>

<script>
(() => {
    const storageKey = @json($storageKey);
    const activeKey = `${storageKey}_active`;
    const steps = @json($steps);
    const root = document.getElementById('eh-walkthrough-root');
    if (!root || !Array.isArray(steps) || !steps.length) return;

    const spotlight = document.getElementById('eh-wt-spotlight');
    const tooltip = document.getElementById('eh-wt-tooltip');
    const badgeEl = document.getElementById('eh-wt-badge');
    const counterEl = document.getElementById('eh-wt-counter');
    const titleEl = document.getElementById('eh-wt-title');
    const descEl = document.getElementById('eh-wt-desc');
    const dotsEl = document.getElementById('eh-wt-dots');
    const skipBtn = document.getElementById('eh-wt-skip');
    const prevBtn = document.getElementById('eh-wt-prev');
    const nextBtn = document.getElementById('eh-wt-next');

    let index = 0;

    const positionElements = () => {
        if (root.hidden) return;
        const step = steps[index];
        const target = step.selector ? document.querySelector(step.selector) : null;
        const vw = window.innerWidth;
        const vh = window.innerHeight;
        const pad = 8;

        if (target && target.getBoundingClientRect().width > 0 && target.getBoundingClientRect().height > 0) {
            const rect = target.getBoundingClientRect();
            const top = Math.max(4, rect.top - pad);
            const left = Math.max(4, rect.left - pad);
            const width = Math.min(vw - left - 4, rect.width + pad * 2);
            const height = Math.min(vh - top - 4, rect.height + pad * 2);

            spotlight.style.top = `${top}px`;
            spotlight.style.left = `${left}px`;
            spotlight.style.width = `${width}px`;
            spotlight.style.height = `${height}px`;

            const tipRect = tooltip.getBoundingClientRect();
            let tipTop = rect.bottom + 16;
            if (tipTop + tipRect.height > vh - 12) {
                tipTop = Math.max(12, rect.top - tipRect.height - 16);
            }
            let tipLeft = Math.min(Math.max(12, rect.left), vw - tipRect.width - 12);

            tooltip.style.top = `${tipTop}px`;
            tooltip.style.left = `${tipLeft}px`;
        } else {
            spotlight.style.top = '50%';
            spotlight.style.left = '50%';
            spotlight.style.width = '0px';
            spotlight.style.height = '0px';

            const tipRect = tooltip.getBoundingClientRect();
            tooltip.style.top = `${Math.max(12, (vh - tipRect.height) / 2)}px`;
            tooltip.style.left = `${Math.max(12, (vw - tipRect.width) / 2)}px`;
        }
    };

    const render = () => {
        const step = steps[index];
        if (step.url && window.location.pathname !== new URL(step.url, window.location.origin).pathname) {
            try {
                sessionStorage.setItem(activeKey, String(index));
            } catch (_) {}
            window.location.href = step.url;
            return;
        }

        badgeEl.textContent = step.badge || 'PANDUAN';
        counterEl.textContent = `${index + 1} / ${steps.length}`;
        titleEl.textContent = step.title;
        descEl.textContent = step.desc;
        dotsEl.innerHTML = steps
            .map((_, i) => `<span class="eh-wt-dot ${i === index ? 'is-active' : ''}"></span>`)
            .join('');

        prevBtn.style.visibility = index === 0 ? 'hidden' : 'visible';
        nextBtn.textContent = index === steps.length - 1 ? 'Selesai' : 'Lanjut';

        const target = step.selector ? document.querySelector(step.selector) : null;
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'nearest' });
        }

        requestAnimationFrame(() => {
            positionElements();
            setTimeout(positionElements, 280);
        });
    };

    const openGuide = (startIndex = 0) => {
        index = startIndex;
        root.hidden = false;
        render();
    };

    const closeGuide = () => {
        root.hidden = true;
        try {
            localStorage.setItem(storageKey, '1');
            sessionStorage.removeItem(activeKey);
        } catch (_) {}
    };

    skipBtn.addEventListener('click', closeGuide);
    prevBtn.addEventListener('click', () => {
        if (index > 0) {
            index--;
            render();
        }
    });
    nextBtn.addEventListener('click', () => {
        if (index < steps.length - 1) {
            index++;
            render();
        } else {
            closeGuide();
        }
    });

    window.addEventListener('resize', positionElements, { passive: true });
    window.addEventListener('scroll', positionElements, { passive: true });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !root.hidden) closeGuide();
    });

    document.querySelectorAll('[data-open-guide]').forEach((btn) => {
        btn.addEventListener('click', () => openGuide(0));
    });

    try {
        const resumeIndex = sessionStorage.getItem(activeKey);
        if (resumeIndex !== null) {
            sessionStorage.removeItem(activeKey);
            openGuide(Math.min(Number(resumeIndex) || 0, steps.length - 1));
        } else if (localStorage.getItem(storageKey) !== '1') {
            openGuide(0);
        }
    } catch (_) {
        openGuide(0);
    }
})();
</script>

<div id="eventhub-guide-modal" class="eh-guide-backdrop" hidden role="dialog" aria-modal="true" aria-labelledby="eh-guide-title">
    <div class="eh-guide-card">
        <div class="eh-guide-top">
            <span class="eh-guide-badge" id="eh-guide-badge">PANDUAN</span>
            <span class="eh-guide-counter" id="eh-guide-counter">1 / 1</span>
        </div>
        <h2 id="eh-guide-title"></h2>
        <p id="eh-guide-desc"></p>
        <div class="eh-guide-dots" id="eh-guide-dots"></div>
        <div class="eh-guide-actions">
            <button type="button" class="eh-guide-skip" id="eh-guide-skip">Lewati</button>
            <div class="eh-guide-nav">
                <button type="button" class="btn outline" id="eh-guide-prev">Sebelumnya</button>
                <button type="button" class="btn primary" id="eh-guide-next">Lanjut</button>
            </div>
        </div>
    </div>
</div>

<style>
.eh-guide-backdrop {
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    background: rgba(0, 0, 0, 0.72);
    backdrop-filter: blur(4px);
}
.eh-guide-backdrop[hidden] {
    display: none;
}
.eh-guide-card {
    width: 100%;
    max-width: 480px;
    background: var(--surface, #20242b);
    border: 1px solid var(--border, #39414c);
    border-radius: 18px;
    padding: 26px;
    box-shadow: 0 24px 60px rgba(0, 0, 0, 0.55);
    color: var(--text, #eee);
}
.eh-guide-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 14px;
}
.eh-guide-badge {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.08em;
    color: var(--teal, #55d6c2);
    background: rgba(85, 214, 194, 0.12);
    padding: 4px 10px;
    border-radius: 999px;
}
.eh-guide-counter {
    font-size: 12px;
    color: var(--muted, #a1a1aa);
}
.eh-guide-card h2 {
    margin: 0 0 10px;
    font-size: 20px;
    line-height: 1.3;
}
.eh-guide-card p {
    margin: 0 0 20px;
    font-size: 14px;
    line-height: 1.65;
    color: var(--muted, #a1a1aa);
}
.eh-guide-dots {
    display: flex;
    gap: 6px;
    margin-bottom: 22px;
}
.eh-guide-dot {
    width: 8px;
    height: 8px;
    border-radius: 999px;
    background: #3f3f46;
    transition: width 0.2s ease, background 0.2s ease;
}
.eh-guide-dot.is-active {
    width: 22px;
    background: var(--teal, #55d6c2);
}
.eh-guide-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}
.eh-guide-skip {
    background: transparent;
    border: 0;
    color: var(--muted, #a1a1aa);
    font-size: 13px;
    cursor: pointer;
    padding: 6px 4px;
}
.eh-guide-skip:hover {
    color: #fff;
}
.eh-guide-nav {
    display: flex;
    gap: 8px;
}
</style>

<script>
(() => {
    const storageKey = @json($storageKey);
    const steps = @json($steps);
    const modal = document.getElementById('eventhub-guide-modal');
    if (!modal || !Array.isArray(steps) || !steps.length) return;

    const badgeEl = document.getElementById('eh-guide-badge');
    const counterEl = document.getElementById('eh-guide-counter');
    const titleEl = document.getElementById('eh-guide-title');
    const descEl = document.getElementById('eh-guide-desc');
    const dotsEl = document.getElementById('eh-guide-dots');
    const skipBtn = document.getElementById('eh-guide-skip');
    const prevBtn = document.getElementById('eh-guide-prev');
    const nextBtn = document.getElementById('eh-guide-next');

    let index = 0;

    const render = () => {
        const step = steps[index];
        badgeEl.textContent = step.badge || 'PANDUAN';
        counterEl.textContent = `${index + 1} / ${steps.length}`;
        titleEl.textContent = step.title;
        descEl.textContent = step.desc;

        dotsEl.innerHTML = steps
            .map((_, i) => `<span class="eh-guide-dot ${i === index ? 'is-active' : ''}"></span>`)
            .join('');

        prevBtn.style.visibility = index === 0 ? 'hidden' : 'visible';
        nextBtn.textContent = index === steps.length - 1 ? 'Selesai' : 'Lanjut';
    };

    const openGuide = (startIndex = 0) => {
        index = startIndex;
        render();
        modal.hidden = false;
    };

    const closeGuide = () => {
        modal.hidden = true;
        try {
            localStorage.setItem(storageKey, '1');
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

    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeGuide();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.hidden) closeGuide();
    });

    document.querySelectorAll('[data-open-guide]').forEach((btn) => {
        btn.addEventListener('click', () => openGuide(0));
    });

    try {
        if (localStorage.getItem(storageKey) !== '1') {
            openGuide(0);
        }
    } catch (_) {
        openGuide(0);
    }
})();
</script>

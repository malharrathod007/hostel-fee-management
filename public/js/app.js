/* Hostel Fee Manager — shared layout behavior
   (sidebar, swipe gestures, mobile table cards, filters, toasts) */
(function () {

    /* ── Sidebar toggle ─────────────────────── */
    const sidebar       = document.getElementById('sidebar');
    const overlay       = document.getElementById('sidebarOverlay');
    const toggleBtn     = document.getElementById('sidebarToggle');
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');

    function openSidebar() {
        sidebar.classList.add('show');
        overlay.classList.add('show');
        document.body.style.overflow = 'hidden';
        if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'true');
    }

    function closeSidebar() {
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
        document.body.style.overflow = '';
        if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'false');
    }

    if (toggleBtn)     toggleBtn.addEventListener('click', () => sidebar.classList.contains('show') ? closeSidebar() : openSidebar());
    if (mobileMenuBtn) mobileMenuBtn.addEventListener('click', openSidebar);
    if (overlay)       overlay.addEventListener('click', closeSidebar);

    // Close sidebar when a nav link is tapped on mobile
    if (sidebar) {
        sidebar.querySelectorAll('a.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768) closeSidebar();
            });
        });
    }

    /* ── Swipe gestures ─────────────────────── */
    let touchStartX = 0, touchStartY = 0;

    document.addEventListener('touchstart', e => {
        touchStartX = e.touches[0].clientX;
        touchStartY = e.touches[0].clientY;
    }, { passive: true });

    document.addEventListener('touchend', e => {
        const dx = e.changedTouches[0].clientX - touchStartX;
        const dy = Math.abs(e.changedTouches[0].clientY - touchStartY);
        // Only horizontal swipes (dy < 60 prevents vertical scroll interference)
        if (dy > 60) return;
        // Swipe right from left edge → open
        if (touchStartX < 32 && dx > 55) openSidebar();
        // Swipe left on open sidebar → close
        if (dx < -55 && sidebar.classList.contains('show')) closeSidebar();
    }, { passive: true });

    /* ── Auto data-label for mobile table cards ── */
    document.querySelectorAll('.table-custom').forEach(function (table) {
        var headers = Array.from(table.querySelectorAll('thead th')).map(function (th) {
            return th.textContent.trim();
        });
        table.querySelectorAll('tbody tr').forEach(function (row) {
            var cells = row.querySelectorAll('td');
            // Empty-state row (has a single td with colspan)
            if (cells.length === 1 && cells[0].hasAttribute('colspan')) {
                row.classList.add('mob-empty-row');
                return;
            }
            cells.forEach(function (td, i) {
                if (headers[i]) td.setAttribute('data-label', headers[i]);
            });
        });
    });

    /* ── Collapsible filter forms on mobile ─── */
    if (window.innerWidth <= 768) {
        document.querySelectorAll('.card-custom.no-print').forEach(function (card) {
            var form = card.querySelector('form[method="GET"]');
            if (!form) return;

            // Check if any filter is currently active
            var hasActive = Array.from(form.querySelectorAll('input[type="text"], select')).some(function (el) {
                return el.value && el.value.trim() !== '';
            });

            // Build the collapsible header
            var header = document.createElement('div');
            header.className = 'mob-filter-header';
            header.innerHTML =
                '<span class="mob-filter-header-title">' +
                    '<i class="bi bi-funnel-fill" style="color:var(--primary);font-size:0.9rem;"></i>' +
                    ' Filters' +
                    (hasActive ? ' <span class="badge" style="background:var(--primary);font-size:0.6rem;padding:0.2rem 0.45rem;border-radius:20px;">Active</span>' : '') +
                '</span>' +
                '<button type="button" class="mob-filter-toggle-btn">' +
                    '<i class="bi bi-chevron-' + (hasActive ? 'up' : 'down') + '"></i>' +
                    '<span>' + (hasActive ? 'Hide' : 'Show') + '</span>' +
                '</button>';

            card.insertBefore(header, form);

            // Collapse by default if no active filter
            if (!hasActive) form.style.display = 'none';

            header.addEventListener('click', function () {
                var hidden = form.style.display === 'none';
                form.style.display = hidden ? '' : 'none';
                var icon  = header.querySelector('.mob-filter-toggle-btn i');
                var label = header.querySelector('.mob-filter-toggle-btn span');
                if (icon)  icon.className  = 'bi bi-chevron-' + (hidden ? 'up' : 'down');
                if (label) label.textContent = hidden ? 'Hide' : 'Show';
            });
        });
    }

    /* ── Flash toast auto-dismiss ───────────── */
    (function () {
        var wrap = document.getElementById('flashToastWrap');
        if (!wrap) return;

        function dismiss(toast) {
            if (!toast || toast.classList.contains('hide')) return;
            toast.classList.add('hide');
            toast.addEventListener('animationend', function () { toast.remove(); }, { once: true });
        }

        wrap.querySelectorAll('.flash-toast').forEach(function (toast) {
            var closeBtn = toast.querySelector('.flash-close');
            if (closeBtn) closeBtn.addEventListener('click', function () { dismiss(toast); });

            var isError = toast.classList.contains('flash-error');
            var delay   = isError ? 7000 : 4000;
            var progress = toast.querySelector('.flash-progress > span');
            if (progress) progress.style.animationDuration = (delay / 1000) + 's';

            var timer = setTimeout(function () { dismiss(toast); }, delay);

            toast.addEventListener('mouseenter', function () {
                clearTimeout(timer);
                if (progress) progress.style.animationPlayState = 'paused';
            });
            toast.addEventListener('mouseleave', function () {
                timer = setTimeout(function () { dismiss(toast); }, 2000);
                if (progress) progress.style.animationPlayState = 'running';
            });
        });
    })();

})();

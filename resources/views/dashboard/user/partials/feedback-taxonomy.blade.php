{{--
    Taksonomi Feedback Visual & Audio TravelKu:
    1. Acknowledge / Hover State (Kursi kosong & rute menggelap saat kursor diarahkan)
    2. Progressive Feedback (Spinner loading saat lanjut ke pembayaran & anti klik ganda)
    3. Validation & Warning (Kursi terisi berwarna abu-abu disabled, Toast merah di sudut layar untuk error, Toast hijau untuk sukses)
    4. Audio Feedback 3 Tingkat:
       - Action/Acknowledge Sound ("pop"/"klik" lembut saat pilih kursi kosong/rute)
       - Warning/Error Sound ("thud"/"bip" nada rendah saat klik kursi abu-abu atau lupa isi rute/form)
       - Success/Progressive Sound ("chime" nada naik saat lanjut ke pembayaran & validasi sukses)
--}}
<div id="travel-toast-container"
     aria-live="polite"
     role="region"
     aria-label="Notifikasi sistem"
     style="position:fixed;top:1.25rem;right:1.25rem;z-index:9999;display:flex;flex-direction:column;gap:0.625rem;max-width:24rem;width:calc(100vw - 2.5rem);pointer-events:none;">
</div>

<script>
if (!window.TravelFeedback) {
    window.TravelFeedback = {
        audioCtx: null,
        soundEnabled: true,
        initAudio() {
            if (!this.soundEnabled) return null;
            try {
                const AudioContextClass = window.AudioContext || window.webkitAudioContext;
                if (!AudioContextClass) return null;
                if (!this.audioCtx) this.audioCtx = new AudioContextClass();
                if (this.audioCtx.state === 'suspended') this.audioCtx.resume();
                return this.audioCtx;
            } catch (e) {
                return null;
            }
        },
        playAction(isDeselect = false) {
            const ctx = this.initAudio();
            if (!ctx) return;
            try {
                const now = ctx.currentTime;
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(isDeselect ? 620 : 520, now);
                osc.frequency.exponentialRampToValueAtTime(isDeselect ? 420 : 760, now + 0.07);
                gain.gain.setValueAtTime(0.001, now);
                gain.gain.exponentialRampToValueAtTime(0.18, now + 0.012);
                gain.gain.exponentialRampToValueAtTime(0.001, now + 0.075);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start(now);
                osc.stop(now + 0.08);
            } catch (e) {}
        },
        playWarning() {
            const ctx = this.initAudio();
            if (!ctx) return;
            try {
                const now = ctx.currentTime;
                [0, 0.09].forEach((offset, idx) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'triangle';
                    osc.frequency.setValueAtTime(idx === 0 ? 175 : 140, now + offset);
                    osc.frequency.linearRampToValueAtTime(idx === 0 ? 130 : 105, now + offset + 0.07);
                    gain.gain.setValueAtTime(0.001, now + offset);
                    gain.gain.exponentialRampToValueAtTime(0.24, now + offset + 0.01);
                    gain.gain.exponentialRampToValueAtTime(0.001, now + offset + 0.075);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start(now + offset);
                    osc.stop(now + offset + 0.08);
                });
            } catch (e) {}
        },
        playSuccess() {
            const ctx = this.initAudio();
            if (!ctx) return;
            try {
                const now = ctx.currentTime;
                [523.25, 659.25, 783.99, 1046.5].forEach((freq, idx, arr) => {
                    const start = now + idx * 0.065;
                    const duration = idx === arr.length - 1 ? 0.22 : 0.11;
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(freq, start);
                    gain.gain.setValueAtTime(0.001, start);
                    gain.gain.exponentialRampToValueAtTime(0.16, start + 0.015);
                    gain.gain.exponentialRampToValueAtTime(0.001, start + duration);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start(start);
                    osc.stop(start + duration + 0.01);
                });
            } catch (e) {}
        },
        toast(message, type = 'success', title = '') {
            let container = document.getElementById('travel-toast-container');
            if (!container) return;
            const isError = type === 'error' || type === 'warning';
            const heading = title || (isError ? 'Peringatan Validasi' : 'Berhasil');
            const toastEl = document.createElement('div');
            toastEl.setAttribute('role', isError ? 'alert' : 'status');
            toastEl.style.cssText = [
                'pointer-events:auto',
                'display:flex',
                'align-items:flex-start',
                'gap:0.75rem',
                'padding:0.875rem 1rem',
                'border-radius:0.75rem',
                'border:2px solid ' + (isError ? '#be123c' : '#0c5a3e'),
                'background:' + (isError ? '#fff1f2' : '#eef6f1'),
                'color:' + (isError ? '#881337' : '#0c392f'),
                'box-shadow:0 12px 28px -6px rgba(12,57,47,0.18)',
                'transform:translateY(-8px)',
                'opacity:0',
                'transition:transform 0.2s ease, opacity 0.2s ease',
                'font-family:Overpass,ui-sans-serif,system-ui,sans-serif',
            ].join(';');

            const iconSvg = isError
                ? '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#be123c" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:2px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>'
                : '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0c7950" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:2px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>';

            const contentDiv = document.createElement('div');
            contentDiv.style.cssText = 'flex:1;min-width:0;font-size:0.9rem;line-height:1.4;';
            const titleEl = document.createElement('p');
            titleEl.style.cssText = 'margin:0;font-weight:800;font-size:0.925rem;';
            titleEl.textContent = heading;
            const msgEl = document.createElement('p');
            msgEl.style.cssText = 'margin:0.15rem 0 0;font-weight:600;';
            msgEl.textContent = message;
            contentDiv.appendChild(titleEl);
            contentDiv.appendChild(msgEl);

            const closeBtn = document.createElement('button');
            closeBtn.type = 'button';
            closeBtn.setAttribute('aria-label', 'Tutup notifikasi');
            closeBtn.style.cssText = 'background:none;border:0;color:inherit;opacity:0.7;cursor:pointer;padding:2px;line-height:1;font-size:1.1rem;font-weight:800;';
            closeBtn.innerHTML = '&times;';
            closeBtn.addEventListener('click', () => this.dismissToast(toastEl));

            toastEl.insertAdjacentHTML('beforeend', iconSvg);
            toastEl.appendChild(contentDiv);
            toastEl.appendChild(closeBtn);
            container.appendChild(toastEl);

            requestAnimationFrame(() => {
                toastEl.style.transform = 'translateY(0)';
                toastEl.style.opacity = '1';
            });
            setTimeout(() => this.dismissToast(toastEl), 3800);
        },
        dismissToast(toastEl) {
            if (!toastEl || !toastEl.parentNode) return;
            toastEl.style.transform = 'translateY(-8px)';
            toastEl.style.opacity = '0';
            setTimeout(() => toastEl.remove(), 200);
        }
    };
}

document.addEventListener('DOMContentLoaded', () => {
    @if (session('error'))
        window.TravelFeedback?.playWarning();
        window.TravelFeedback?.toast(@js(session('error')), 'error', 'Peringatan Sistem');
    @elseif ($errors->any())
        window.TravelFeedback?.playWarning();
        window.TravelFeedback?.toast(@js($errors->first()), 'error', 'Validasi Gagal');
    @elseif (session('success'))
        window.TravelFeedback?.playSuccess();
        window.TravelFeedback?.toast(@js(session('success')), 'success', 'Berhasil');
    @endif
});
</script>


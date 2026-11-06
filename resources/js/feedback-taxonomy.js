/**
 * TravelKu Visual & Audio Feedback Taxonomy
 *
 * 1. Action / Acknowledge Sound (Pop / Klik lembut) -> Saat memilih kursi kosong / rute
 * 2. Warning / Error Sound (Thud / Bip nada rendah) -> Saat klik kursi abu-abu (terisi) / validasi gagal
 * 3. Success / Progressive Sound (Chime melodi naik) -> Saat lanjut ke pembayaran / validasi sukses
 * 4. Visual Toast Notification (Hijau = Sukses, Merah = Error/Warning) di sudut layar
 */

class TravelFeedbackSystem {
    constructor() {
        this.audioCtx = null;
        this.soundEnabled = true;
    }

    initAudio() {
        if (!this.soundEnabled) {
            return null;
        }
        try {
            const AudioContextClass = window.AudioContext || window.webkitAudioContext;
            if (!AudioContextClass) {
                return null;
            }
            if (!this.audioCtx) {
                this.audioCtx = new AudioContextClass();
            }
            if (this.audioCtx.state === 'suspended') {
                this.audioCtx.resume();
            }
            return this.audioCtx;
        } catch (e) {
            return null;
        }
    }

    /**
     * Level 1: Action / Acknowledge Sound ("pop" / "klik" lembut dan pendek)
     * Digunakan saat memilih/membatalkan kursi kosong atau memilih rute.
     */
    playAction(isDeselect = false) {
        const ctx = this.initAudio();
        if (!ctx) {
            return;
        }

        try {
            const now = ctx.currentTime;
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();

            osc.type = 'sine';
            if (isDeselect) {
                osc.frequency.setValueAtTime(620, now);
                osc.frequency.exponentialRampToValueAtTime(420, now + 0.065);
            } else {
                osc.frequency.setValueAtTime(520, now);
                osc.frequency.exponentialRampToValueAtTime(760, now + 0.07);
            }

            gain.gain.setValueAtTime(0.001, now);
            gain.gain.exponentialRampToValueAtTime(0.18, now + 0.012);
            gain.gain.exponentialRampToValueAtTime(0.001, now + 0.075);

            osc.connect(gain);
            gain.connect(ctx.destination);

            osc.start(now);
            osc.stop(now + 0.08);
        } catch (e) {
            // Abaikan jika autoplay diblokir browser
        }
    }

    /**
     * Level 2: Warning / Error Sound ("thud" / "bip" bernada rendah/datar)
     * Digunakan saat mengklik kursi abu-abu (sudah terisi/ditahan) atau validasi form gagal.
     */
    playWarning() {
        const ctx = this.initAudio();
        if (!ctx) {
            return;
        }

        try {
            if (ctx.state === 'suspended') {
                ctx.resume();
            }
            const now = ctx.currentTime;

            // Dua ketukan rendah tegas ("thud-thud") yang terdengar jelas di seluruh speaker laptop & HP
            [0, 0.11].forEach((offset, idx) => {
                const osc = ctx.createOscillator();
                const oscHarmonic = ctx.createOscillator();
                const filter = ctx.createBiquadFilter();
                const gain = ctx.createGain();

                osc.type = 'sawtooth';
                oscHarmonic.type = 'triangle';

                const baseFreq = idx === 0 ? 240 : 190;
                const dropFreq = idx === 0 ? 160 : 130;

                osc.frequency.setValueAtTime(baseFreq, now + offset);
                osc.frequency.exponentialRampToValueAtTime(dropFreq, now + offset + 0.08);

                oscHarmonic.frequency.setValueAtTime(baseFreq * 1.5, now + offset);
                oscHarmonic.frequency.exponentialRampToValueAtTime(dropFreq * 1.5, now + offset + 0.08);

                filter.type = 'lowpass';
                filter.frequency.setValueAtTime(650, now + offset);
                filter.frequency.linearRampToValueAtTime(320, now + offset + 0.08);

                gain.gain.setValueAtTime(0.001, now + offset);
                gain.gain.exponentialRampToValueAtTime(0.35, now + offset + 0.015);
                gain.gain.exponentialRampToValueAtTime(0.001, now + offset + 0.09);

                osc.connect(filter);
                oscHarmonic.connect(filter);
                filter.connect(gain);
                gain.connect(ctx.destination);

                osc.start(now + offset);
                oscHarmonic.start(now + offset);
                osc.stop(now + offset + 0.095);
                oscHarmonic.stop(now + offset + 0.095);
            });
        } catch (e) {
            // Abaikan jika autoplay diblokir browser
        }
    }

    /**
     * Level 3: Success / Progressive Sound ("chime" nada gemerincing pendek yang melodinya naik)
     * Digunakan saat menekan tombol "Lanjut ke Pembayaran" dan validasi berhasil.
     */
    playSuccess() {
        const ctx = this.initAudio();
        if (!ctx) {
            return;
        }

        try {
            const now = ctx.currentTime;
            const notes = [523.25, 659.25, 783.99, 1046.5]; // C5 - E5 - G5 - C6 (Rising Chime)

            notes.forEach((freq, idx) => {
                const start = now + idx * 0.065;
                const duration = idx === notes.length - 1 ? 0.22 : 0.11;

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
        } catch (e) {
            // Abaikan jika autoplay diblokir browser
        }
    }

    /**
     * Menampilkan Toast Notification di sudut kanan atas layar.
     * @param {string} message Pesan feedback
     * @param {'success'|'error'|'warning'|'info'} type Tipe status visual
     * @param {string} [title] Judul opsional
     */
    toast(message, type = 'success', title = '') {
        let container = document.getElementById('travel-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'travel-toast-container';
            container.setAttribute('aria-live', 'polite');
            container.setAttribute('role', 'region');
            container.setAttribute('aria-label', 'Notifikasi sistem');
            container.style.cssText = 'position:fixed;top:1.25rem;right:1.25rem;z-index:9999;display:flex;flex-direction:column;gap:0.625rem;max-width:24rem;width:calc(100vw - 2.5rem);pointer-events:none;';
            document.body.appendChild(container);
        }

        const isError = type === 'error' || type === 'warning';
        const defaultTitle = isError ? 'Peringatan Validasi' : 'Berhasil';
        const heading = title || defaultTitle;

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

        setTimeout(() => {
            this.dismissToast(toastEl);
        }, 3800);
    }

    dismissToast(toastEl) {
        if (!toastEl || !toastEl.parentNode) {
            return;
        }
        toastEl.style.transform = 'translateY(-8px)';
        toastEl.style.opacity = '0';
        setTimeout(() => {
            toastEl.remove();
        }, 200);
    }
}

if (!window.TravelFeedback) {
    window.TravelFeedback = new TravelFeedbackSystem();
}

export default window.TravelFeedback;


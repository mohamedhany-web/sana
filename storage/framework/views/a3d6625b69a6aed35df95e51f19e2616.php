<?php ($geoColors = config('brand.colors')); ?>
<script>
window.createGeoEngine = function(canvas, opts = {}) {
    if (!canvas) return { setPhase(){}, setStep(){}, setConnectStrength(){}, setEmailPulse(){}, triggerWave(){}, destroy(){} };

    const colors = opts.colors || ['<?php echo e($geoColors['blue']); ?>', '<?php echo e($geoColors['purple']); ?>', '<?php echo e($geoColors['yellow']); ?>'];
    const ctx = canvas.getContext('2d');
    let w = 0, h = 0, dpr = 1;
    let phase = 'idle';
    let step = 1;
    let connectStrength = 0;
    let emailPulse = 0;
    let waveEnergy = 0;
    let converge = 0;
    let mouse = { x: 0.5, y: 0.5, active: false };
    let ripples = [];
    let rings = [];
    let raf = null;
    let nodes = [];
    let particles = [];
    const NODE_COUNT = 18;

    function resize() {
        dpr = Math.min(window.devicePixelRatio || 1, 2);
        w = canvas.clientWidth;
        h = canvas.clientHeight;
        canvas.width = w * dpr;
        canvas.height = h * dpr;
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    }

    function initNodes() {
        nodes = [];
        for (let i = 0; i < NODE_COUNT; i++) {
            nodes.push({
                x: Math.random() * w,
                y: Math.random() * h,
                ox: 0, oy: 0,
                r: 4 + Math.random() * 14,
                color: colors[i % colors.length],
                vx: (Math.random() - 0.5) * 0.4,
                vy: (Math.random() - 0.5) * 0.4,
                angle: Math.random() * Math.PI * 2,
                speed: 0.003 + Math.random() * 0.006,
            });
        }
    }

    function initParticles() {
        particles = [];
        for (let i = 0; i < 40; i++) {
            particles.push({
                x: Math.random() * w,
                y: Math.random() * h,
                s: 1 + Math.random() * 2,
                a: 0.08 + Math.random() * 0.2,
                vx: (Math.random() - 0.5) * 0.15,
                vy: (Math.random() - 0.5) * 0.15,
                c: colors[i % colors.length],
            });
        }
    }

    function targetForNode(i, total) {
        const cx = w * 0.5, cy = h * 0.22;
        const R = Math.min(w, h) * 0.12;
        const a = (i / total) * Math.PI * 2 - Math.PI / 2;
        return { x: cx + Math.cos(a) * R, y: cy + Math.sin(a) * R };
    }

    function drawBlob(x, y, r, color, alpha) {
        ctx.save();
        ctx.globalAlpha = alpha;
        const g = ctx.createRadialGradient(x, y, 0, x, y, r);
        g.addColorStop(0, color + '55');
        g.addColorStop(0.5, color + '22');
        g.addColorStop(1, color + '00');
        ctx.fillStyle = g;
        ctx.beginPath();
        ctx.arc(x, y, r, 0, Math.PI * 2);
        ctx.fill();
        ctx.restore();
    }

    function tick() {
        ctx.clearRect(0, 0, w, h);

        const mx = mouse.x * w;
        const my = mouse.y * h;
        const progress = Math.max(0, Math.min(1, (step - 1) / 7));
        const assemble = phase === 'register' ? progress : 0;

        // Particles
        particles.forEach(p => {
            if (mouse.active) {
                p.x += (mx - p.x) * 0.0008;
                p.y += (my - p.y) * 0.0008;
            }
            p.x += p.vx; p.y += p.vy;
            if (p.x < 0) p.x = w; if (p.x > w) p.x = 0;
            if (p.y < 0) p.y = h; if (p.y > h) p.y = 0;
            ctx.fillStyle = p.c;
            ctx.globalAlpha = p.a * (1 - converge * 0.5);
            ctx.beginPath();
            ctx.arc(p.x, p.y, p.s, 0, Math.PI * 2);
            ctx.fill();
        });
        ctx.globalAlpha = 1;

        // Ripples (phone)
        ripples = ripples.filter(r => r.life > 0);
        ripples.forEach(r => {
            r.life -= 0.012;
            r.radius += 2.2;
            ctx.strokeStyle = colors[0];
            ctx.globalAlpha = r.life * 0.35;
            ctx.lineWidth = 1.5;
            ctx.beginPath();
            ctx.arc(r.x, r.y, r.radius, 0, Math.PI * 2);
            ctx.stroke();
        });
        ctx.globalAlpha = 1;

        // Email rings
        rings = rings.filter(r => r.life > 0);
        rings.forEach(r => {
            r.life -= 0.015;
            r.radius += 1.8;
            ctx.strokeStyle = colors[1];
            ctx.globalAlpha = r.life * 0.4;
            ctx.lineWidth = 1;
            ctx.beginPath();
            ctx.arc(r.x, r.y, r.radius, 0, Math.PI * 2);
            ctx.stroke();
        });
        ctx.globalAlpha = 1;

        if (emailPulse > 0) {
            emailPulse = Math.max(0, emailPulse - 0.02);
            drawBlob(w * 0.5, h * 0.38, 80 + emailPulse * 120, colors[1], 0.15 + emailPulse * 0.1);
        }

        waveEnergy = Math.max(0, waveEnergy - 0.02);

        // Nodes
        nodes.forEach((n, i) => {
            n.angle += n.speed;

            let tx = n.x, ty = n.y;
            if (assemble > 0) {
                const t = targetForNode(i, nodes.length);
                tx = n.x + (t.x - n.x) * assemble * 0.04;
                ty = n.y + (t.y - n.y) * assemble * 0.04;
                n.x = tx; n.y = ty;
            } else {
                n.x += n.vx + Math.sin(n.angle) * 0.08;
                n.y += n.vy + Math.cos(n.angle * 0.7) * 0.08;
            }

            if (mouse.active && phase !== 'converge') {
                const dx = mx - n.x, dy = my - n.y;
                const dist = Math.sqrt(dx * dx + dy * dy) || 1;
                const force = Math.min(60 / dist, 1.2);
                n.x -= (dx / dist) * force * 0.6;
                n.y -= (dy / dist) * force * 0.6;
            }

            if (n.x < -20) n.x = w + 20;
            if (n.x > w + 20) n.x = -20;
            if (n.y < -20) n.y = h + 20;
            if (n.y > h + 20) n.y = -20;

            if (converge > 0) {
                n.x += (w * 0.5 - n.x) * converge * 0.08;
                n.y += (h * 0.42 - n.y) * converge * 0.08;
            }

            const blobR = n.r * (2.5 + waveEnergy * 2);
            drawBlob(n.x, n.y, blobR, n.color, 0.35 + connectStrength * 0.15);

            ctx.fillStyle = n.color;
            ctx.globalAlpha = 0.55 + connectStrength * 0.2;
            ctx.beginPath();
            ctx.arc(n.x, n.y, n.r * 0.35, 0, Math.PI * 2);
            ctx.fill();
            ctx.globalAlpha = 1;
        });

        // Connect lines (name phase)
        if (connectStrength > 0.05) {
            ctx.lineWidth = 0.8 + connectStrength;
            for (let i = 0; i < nodes.length; i++) {
                for (let j = i + 1; j < nodes.length; j++) {
                    const a = nodes[i], b = nodes[j];
                    const dx = a.x - b.x, dy = a.y - b.y;
                    const dist = Math.sqrt(dx * dx + dy * dy);
                    if (dist < 120 + connectStrength * 80) {
                        ctx.strokeStyle = colors[0];
                        ctx.globalAlpha = (1 - dist / 200) * connectStrength * 0.45;
                        ctx.beginPath();
                        ctx.moveTo(a.x, a.y);
                        ctx.lineTo(b.x, b.y);
                        ctx.stroke();
                    }
                }
            }
            ctx.globalAlpha = 1;
        }

        // Constellation lines (progress)
        if (assemble > 0.15) {
            const pts = nodes.map((_, i) => targetForNode(i, nodes.length));
            ctx.lineWidth = 1;
            for (let i = 0; i < pts.length; i++) {
                const j = (i + 1) % pts.length;
                ctx.strokeStyle = colors[2];
                ctx.globalAlpha = assemble * 0.25;
                ctx.beginPath();
                ctx.moveTo(pts[i].x, pts[i].y);
                ctx.lineTo(pts[j].x, pts[j].y);
                ctx.stroke();
            }
            ctx.globalAlpha = 1;
        }

        if (converge >= 0.95) {
            drawBlob(w * 0.5, h * 0.42, 90, colors[0], 0.35);
            drawBlob(w * 0.5, h * 0.42, 50, colors[1], 0.5);
        }

        if (converge < 1) converge = Math.min(1, converge + (phase === 'converge' ? 0.025 : 0));

        raf = requestAnimationFrame(tick);
    }

    function onMove(e) {
        const rect = canvas.getBoundingClientRect();
        mouse.x = (e.clientX - rect.left) / rect.width;
        mouse.y = (e.clientY - rect.top) / rect.height;
        mouse.active = true;
    }

    resize();
    initNodes();
    initParticles();
    window.addEventListener('resize', () => { resize(); });
    window.addEventListener('mousemove', onMove);
    tick();

    return {
        setPhase(p) { phase = p; },
        setStep(s) { step = s; phase = 'register'; },
        setConnectStrength(v) { connectStrength = Math.max(0, Math.min(1, v)); },
        setEmailPulse(v) { emailPulse = Math.max(emailPulse, v || 0.5); rings.push({ x: w * 0.5, y: h * 0.38, radius: 10, life: 1 }); },
        triggerWave() { waveEnergy = 1; ripples.push({ x: w * 0.5, y: h * 0.45, radius: 20, life: 1 }); },
        startConverge() { phase = 'converge'; converge = 0; },
        waitConverge(ms) {
            this.startConverge();
            return new Promise(r => setTimeout(r, ms || 900));
        },
        destroy() {
            cancelAnimationFrame(raf);
            window.removeEventListener('resize', resize);
            window.removeEventListener('mousemove', onMove);
        },
    };
};

window.initMagnetic = function(el) {
    if (!el) return;
    el.addEventListener('mousemove', e => {
        const r = el.getBoundingClientRect();
        const x = e.clientX - r.left - r.width / 2;
        const y = e.clientY - r.top - r.height / 2;
        el.style.transform = `translate(${x * 0.12}px, ${y * 0.12}px)`;
    });
    el.addEventListener('mouseleave', () => { el.style.transform = ''; });
};
</script>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\auth\partials\geometric-engine.blade.php ENDPATH**/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="description" content="Clinovia is the modern smart clinic management system built for schools — patient records, appointments, medicines, AI assistant, and more in one platform." />
<title>Clinovia — Smart School Clinic Management System</title>
<link rel="icon" type="image/svg+xml" href="/clinovia-icon.svg">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=Poppins:wght@600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
/* ════════════════════════════════════════════════════════════════
   RESET & TOKENS
════════════════════════════════════════════════════════════════ */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --p:hsl(201,85%,39%);
  --p-l:hsl(201,85%,56%);
  --s:hsl(265,58%,54%);
  --s-l:hsl(265,58%,70%);
  --dark:#060d1b;
  --dark-2:#0d1628;
  --dark-3:#162035;
  --b-dark:rgba(255,255,255,.07);
  --b-dark-s:rgba(255,255,255,.13);
  --txt:#e8edf5;
  --mut:#7c879a;
  --mut-l:#a8b3c3;
  --grad:linear-gradient(135deg,var(--p),var(--s));
  --grad-r:linear-gradient(315deg,var(--p),var(--s));
  --grad-h:linear-gradient(90deg,var(--p),var(--s));
  --fh:'Poppins','Inter',sans-serif;
  --fb:'Inter',system-ui,sans-serif;
  --r:14px;--rl:20px;--rxl:28px;
}
html{scroll-behavior:smooth}
body{font-family:var(--fb);font-size:16px;line-height:1.6;color:#1e293b;background:#fff;overflow-x:hidden;-webkit-font-smoothing:antialiased}
::-webkit-scrollbar{width:5px}
::-webkit-scrollbar-track{background:var(--dark)}
::-webkit-scrollbar-thumb{background:var(--dark-3);border-radius:3px}
img{max-width:100%;display:block}
a{text-decoration:none}
button{font-family:var(--fb);cursor:pointer}

/* ════════════════════════════════════════════════════════════════
   UTILITIES
════════════════════════════════════════════════════════════════ */
.grad-text{background:var(--grad-r);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
.reveal{opacity:0;transform:translateY(22px);transition:opacity .65s ease,transform .65s ease}
.reveal.visible{opacity:1;transform:none}
.d1{transition-delay:.08s}.d2{transition-delay:.16s}.d3{transition-delay:.24s}.d4{transition-delay:.32s}.d5{transition-delay:.40s}

/* ════════════════════════════════════════════════════════════════
   NAVBAR
════════════════════════════════════════════════════════════════ */
.nav{position:fixed;top:0;left:0;right:0;z-index:999;padding:1.1rem 2rem;display:flex;align-items:center;gap:1.5rem;transition:all .3s}
.nav.solid{background:rgba(6,13,27,.96);border-bottom:1px solid var(--b-dark-s);padding:.75rem 2rem;backdrop-filter:blur(18px)}
.nav-brand{display:flex;align-items:center;gap:.6rem;text-decoration:none;flex-shrink:0}
.nav-icon{width:33px;height:33px;border-radius:9px;background:var(--grad);display:flex;align-items:center;justify-content:center;font-size:.85rem;color:#fff}
.nav-name{font-family:var(--fh);font-weight:700;font-size:1.05rem;color:#fff;letter-spacing:-.01em}
.nav-links{display:flex;align-items:center;gap:.15rem;list-style:none;flex:1}
.nav-links a{font-size:.85rem;font-weight:500;color:rgba(255,255,255,.6);padding:.4rem .85rem;border-radius:8px;transition:color .2s,background .2s}
.nav-links a:hover{color:#fff;background:rgba(255,255,255,.07)}
.nav-right{display:flex;align-items:center;gap:.75rem;margin-left:auto}
.nav-login{font-size:.85rem;font-weight:600;color:rgba(255,255,255,.7);padding:.45rem .95rem;border-radius:8px;transition:color .2s}
.nav-login:hover{color:#fff}
.nav-cta{font-size:.85rem;font-weight:700;color:#fff;padding:.5rem 1.15rem;border-radius:9px;background:var(--grad);transition:opacity .2s,transform .15s;box-shadow:0 4px 16px rgba(15,115,186,.3)}
.nav-cta:hover{opacity:.88;transform:translateY(-1px)}
.nav-mob{display:none;background:none;border:none;color:#fff;font-size:1.35rem;padding:.25rem;flex-shrink:0}

/* mobile drawer */
.mob-drawer{display:none;position:fixed;inset:0;background:var(--dark);z-index:1100;padding:4.5rem 1.75rem 2rem;flex-direction:column;gap:.25rem}
.mob-drawer.open{display:flex}
.mob-close{position:absolute;top:1.1rem;right:1.5rem;background:none;border:none;color:#fff;font-size:1.4rem}
.mob-drawer a{font-size:1rem;font-weight:600;color:rgba(255,255,255,.7);padding:.8rem 0;border-bottom:1px solid var(--b-dark);transition:color .2s}
.mob-drawer a:hover{color:#fff}

/* ════════════════════════════════════════════════════════════════
   HERO
════════════════════════════════════════════════════════════════ */
.hero{min-height:100vh;background:var(--dark);position:relative;overflow:hidden;display:flex;align-items:center;padding:8rem 2rem 5rem}
.hero-orb{position:absolute;border-radius:50%;pointer-events:none}
.orb-1{width:900px;height:900px;background:radial-gradient(circle,hsl(201,85%,39%,.22) 0%,transparent 68%);top:-250px;left:-300px}
.orb-2{width:700px;height:700px;background:radial-gradient(circle,hsl(265,58%,54%,.18) 0%,transparent 68%);bottom:-200px;right:5%}
.orb-3{width:450px;height:450px;background:radial-gradient(circle,hsl(201,85%,39%,.12) 0%,transparent 68%);top:25%;right:-80px}
.hero-dots{position:absolute;inset:0;background-image:radial-gradient(circle,rgba(255,255,255,.055) 1px,transparent 1px);background-size:38px 38px;pointer-events:none}
.hero-inner{max-width:1280px;margin:0 auto;width:100%;display:grid;grid-template-columns:1fr 1.05fr;gap:4rem;align-items:center;position:relative;z-index:1}
.hero-badge{display:inline-flex;align-items:center;gap:.5rem;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.11);border-radius:100px;padding:.3rem .85rem;font-size:.76rem;font-weight:600;color:rgba(255,255,255,.78);letter-spacing:.04em;margin-bottom:1.5rem;width:fit-content}
.badge-live{width:7px;height:7px;border-radius:50%;background:hsl(144,80%,50%);box-shadow:0 0 8px hsl(144,80%,50%);animation:blink 2.2s infinite}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.35}}
.hero-h1{font-family:var(--fh);font-size:clamp(2.1rem,4.2vw,3.65rem);font-weight:900;color:#fff;line-height:1.1;letter-spacing:-.035em;margin-bottom:1.25rem}
.hero-sub{font-size:1.05rem;color:var(--mut-l);line-height:1.72;max-width:510px;margin-bottom:2.25rem}
.hero-btns{display:flex;gap:.9rem;flex-wrap:wrap;align-items:center;margin-bottom:1.75rem}
.btn-pri{display:inline-flex;align-items:center;gap:.5rem;background:var(--grad);color:#fff;font-weight:700;font-size:.95rem;padding:.875rem 1.8rem;border-radius:12px;border:none;transition:transform .2s,box-shadow .2s;box-shadow:0 8px 28px hsl(201,85%,39%,.38);cursor:pointer}
.btn-pri:hover{transform:translateY(-2px);box-shadow:0 12px 36px hsl(201,85%,39%,.48)}
.btn-ghost{display:inline-flex;align-items:center;gap:.5rem;background:rgba(255,255,255,.07);color:rgba(255,255,255,.82);font-weight:600;font-size:.95rem;padding:.875rem 1.8rem;border-radius:12px;border:1px solid rgba(255,255,255,.11);transition:background .2s,border-color .2s,transform .2s}
.btn-ghost:hover{background:rgba(255,255,255,.11);border-color:rgba(255,255,255,.22);transform:translateY(-2px)}
.hero-demo{display:flex;align-items:center;gap:.6rem;flex-wrap:wrap}
.demo-label{font-size:.73rem;color:var(--mut);font-weight:500}
.demo-chip{background:rgba(255,255,255,.055);border:1px solid rgba(255,255,255,.09);border-radius:6px;padding:.18rem .6rem;font-size:.7rem;font-family:'Courier New',monospace;color:rgba(255,255,255,.55)}

/* ─── Dashboard mockup ─── */
.mockup-wrap{position:relative}
.mockup-glow{position:absolute;inset:-60px;background:radial-gradient(ellipse at 50% 60%,hsl(201,85%,39%,.14) 0%,transparent 68%);pointer-events:none;z-index:0}
.mockup-frame{position:relative;z-index:1;border-radius:15px;overflow:hidden;border:1px solid rgba(255,255,255,.11);box-shadow:0 0 0 1px rgba(255,255,255,.04),0 32px 80px rgba(0,0,0,.65),0 0 60px rgba(15,115,186,.08);transform:perspective(1100px) rotateY(-5deg) rotateX(2.5deg);transform-origin:left center;animation:mockup-float 7s ease-in-out infinite}
@keyframes mockup-float{0%,100%{transform:perspective(1100px) rotateY(-5deg) rotateX(2.5deg) translateY(0)}50%{transform:perspective(1100px) rotateY(-5deg) rotateX(2.5deg) translateY(-12px)}}
/* Titlebar */
.m-bar{background:#1a2840;padding:.6rem .95rem;display:flex;align-items:center;gap:.45rem;border-bottom:1px solid rgba(255,255,255,.06)}
.m-dot{width:9px;height:9px;border-radius:50%}
.m-red{background:#ff5f57}.m-yel{background:#febc2e}.m-grn{background:#28c840}
.m-url{flex:1;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.06);border-radius:5px;padding:.17rem .55rem;font-size:.65rem;color:rgba(255,255,255,.35);margin:0 .45rem;font-family:monospace}
/* Body */
.m-body{background:#0d1628;display:grid;grid-template-columns:155px 1fr;height:355px;overflow:hidden}
.m-side{background:#091220;padding:.65rem .45rem;border-right:1px solid rgba(255,255,255,.05);display:flex;flex-direction:column;gap:.1rem}
.m-brand{display:flex;align-items:center;gap:.4rem;padding:.25rem .4rem .65rem;border-bottom:1px solid rgba(255,255,255,.05);margin-bottom:.25rem}
.m-brand-ic{width:21px;height:21px;border-radius:6px;background:var(--grad);display:flex;align-items:center;justify-content:center;font-size:.5rem;color:#fff}
.m-brand-nm{font-size:.6rem;font-weight:700;color:rgba(255,255,255,.88);font-family:var(--fh)}
.m-nav{display:flex;align-items:center;gap:.35rem;padding:.3rem .45rem;border-radius:6px;font-size:.59rem;color:rgba(255,255,255,.4)}
.m-nav.on{background:rgba(15,115,186,.18);color:hsl(201,85%,65%)}
.m-nav i{font-size:.65rem}
/* Main */
.m-main{padding:.8rem;display:flex;flex-direction:column;gap:.55rem;overflow:hidden}
.m-top{display:flex;align-items:center;justify-content:space-between}
.m-pg-title{font-size:.68rem;font-weight:700;color:rgba(255,255,255,.9);font-family:var(--fh)}
.m-usr{display:flex;align-items:center;gap:.3rem}
.m-av{width:19px;height:19px;border-radius:50%;background:var(--grad);display:flex;align-items:center;justify-content:center;font-size:.5rem;font-weight:700;color:#fff}
.m-un{font-size:.58rem;color:rgba(255,255,255,.45)}
/* Stat row */
.m-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:.35rem}
.m-sc{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.06);border-radius:7px;padding:.4rem .45rem}
.m-sl{font-size:.5rem;color:rgba(255,255,255,.38);margin-bottom:.15rem}
.m-sv{font-size:.82rem;font-weight:700;color:rgba(255,255,255,.9);font-family:var(--fh)}
.m-ss{font-size:.45rem;color:hsl(144,60%,52%)}
/* Grid 2 */
.m-g2{display:grid;grid-template-columns:1.45fr 1fr;gap:.35rem;flex:1;min-height:0}
.m-card{background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.06);border-radius:7px;padding:.4rem .45rem;overflow:hidden}
.m-ct{font-size:.56rem;font-weight:600;color:rgba(255,255,255,.65);margin-bottom:.3rem}
.m-row{display:flex;align-items:center;gap:.3rem;padding:.18rem 0;border-bottom:1px solid rgba(255,255,255,.03)}
.m-row:last-child{border-bottom:none}
.m-rd{width:6px;height:6px;border-radius:50%;flex-shrink:0}
.m-rn{font-size:.52rem;color:rgba(255,255,255,.55);flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.mbg{font-size:.43rem;padding:.08rem .28rem;border-radius:4px;font-weight:600;flex-shrink:0}
.bg-g{background:rgba(34,197,94,.13);color:hsl(144,60%,55%)}
.bg-y{background:rgba(234,179,8,.13);color:hsl(45,90%,60%)}
.bg-b{background:rgba(15,115,186,.18);color:hsl(201,85%,65%)}
.bg-r{background:rgba(239,68,68,.13);color:#f87171}
/* Chart bars */
.m-bars{display:flex;align-items:flex-end;gap:.18rem;height:68px;margin-top:.2rem;padding:0 .1rem}
.m-bar{flex:1;border-radius:3px 3px 0 0;background:linear-gradient(180deg,hsl(201,85%,50%,.75),hsl(265,58%,54%,.55))}

/* ════════════════════════════════════════════════════════════════
   STATS STRIP
════════════════════════════════════════════════════════════════ */
.stats-strip{background:#fff;border-top:1px solid #edf0f5;border-bottom:1px solid #edf0f5;padding:3.25rem 2rem}
.stats-inner{max-width:1080px;margin:0 auto;display:grid;grid-template-columns:1fr 1px 1fr 1px 1fr 1px 1fr;gap:1.5rem;align-items:center}
.stat-div{background:#e5e9f0;height:48px;width:1px}
.stat-it{text-align:center}
.stat-num{font-family:var(--fh);font-size:2.2rem;font-weight:900;background:var(--grad);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;line-height:1;margin-bottom:.3rem}
.stat-lbl{font-size:.875rem;color:#64748b;font-weight:500}

/* ════════════════════════════════════════════════════════════════
   SECTIONS
════════════════════════════════════════════════════════════════ */
.section{padding:6rem 2rem}
.si{max-width:1200px;margin:0 auto}
.eyebrow{display:inline-block;font-size:.75rem;font-weight:700;letter-spacing:.09em;text-transform:uppercase;background:var(--grad);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;margin-bottom:.7rem}
.sec-h2{font-family:var(--fh);font-size:clamp(1.7rem,3vw,2.65rem);font-weight:800;color:#0f172a;line-height:1.18;letter-spacing:-.03em;margin-bottom:.85rem}
.sec-p{font-size:1.02rem;color:#64748b;line-height:1.72;max-width:570px}
.sh{margin-bottom:3.5rem}
.sh.c{text-align:center}.sh.c .sec-p{margin:0 auto}
.bg-sl{background:#f8fafc}
.bg-dk{background:var(--dark)}

/* ════════════════════════════════════════════════════════════════
   FEATURES — BENTO GRID
════════════════════════════════════════════════════════════════ */
.bento{display:grid;grid-template-columns:repeat(3,1fr);gap:1rem}
.bc{background:#fff;border:1px solid #e2e8f2;border-radius:var(--rl);padding:1.7rem;position:relative;overflow:hidden;transition:transform .25s,box-shadow .25s,border-color .25s}
.bc:hover{transform:translateY(-3px);box-shadow:0 18px 50px rgba(15,115,186,.08);border-color:rgba(15,115,186,.22)}
.bc::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:var(--grad);opacity:0;transition:opacity .25s}
.bc:hover::before{opacity:1}
.bc.s2{grid-column:span 2}
.bc.s3{grid-column:span 3}
/* Feature icon */
.fi{width:42px;height:42px;border-radius:11px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;margin-bottom:.9rem}
.fi-b{background:hsl(201,85%,39%,.1);color:hsl(201,85%,39%)}
.fi-p{background:hsl(265,58%,54%,.1);color:hsl(265,58%,54%)}
.fi-g{background:hsl(144,60%,40%,.1);color:hsl(144,60%,40%)}
.fi-o{background:hsl(28,90%,50%,.1);color:hsl(28,90%,50%)}
.fi-r{background:hsl(348,75%,52%,.1);color:hsl(348,75%,52%)}
.fi-t{background:hsl(174,72%,36%,.1);color:hsl(174,72%,36%)}
.fi-i{background:hsl(238,58%,55%,.1);color:hsl(238,58%,55%)}
.fi-a{background:hsl(38,90%,45%,.1);color:hsl(38,90%,45%)}
.f-lbl{font-size:.7rem;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:var(--p);margin-bottom:.3rem}
.f-title{font-family:var(--fh);font-size:1rem;font-weight:700;color:#0f172a;margin-bottom:.45rem;line-height:1.3}
.f-desc{font-size:.875rem;color:#64748b;line-height:1.62}
.f-tags{display:flex;flex-wrap:wrap;gap:.3rem;margin-top:.8rem}
.ftag{font-size:.68rem;font-weight:500;background:#f1f5f9;color:#475569;padding:.18rem .55rem;border-radius:100px;border:1px solid #e2e8f0}
/* Wide feature extras */
.bc.s2 .f-title{font-size:1.1rem}
.bc-extra{display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-top:.9rem}
.bc-mini{background:#f8fafc;border:1px solid #e8edf5;border-radius:10px;padding:.75rem .9rem;display:flex;align-items:flex-start;gap:.6rem}
.bc-mini i{color:var(--p);font-size:.9rem;margin-top:.05rem;flex-shrink:0}
.bc-mini-t{font-size:.8rem;font-weight:600;color:#334155;margin-bottom:.15rem}
.bc-mini-d{font-size:.73rem;color:#64748b}

/* ════════════════════════════════════════════════════════════════
   WALKTHROUGH
════════════════════════════════════════════════════════════════ */
.wt-section .sec-h2{color:#fff}
.wt-section .sec-p{color:var(--mut-l)}
.wt-section{position:relative;overflow:hidden}
.wt-section::before{content:'';position:absolute;width:600px;height:600px;border-radius:50%;background:radial-gradient(circle,hsl(265,58%,54%,.1) 0%,transparent 70%);top:-150px;right:-100px;pointer-events:none}
.wt-tabs{display:flex;gap:.3rem;background:rgba(255,255,255,.05);border:1px solid var(--b-dark);border-radius:12px;padding:.3rem;margin-bottom:2.25rem;flex-wrap:wrap}
.wt-tab{flex:1;min-width:90px;padding:.55rem .85rem;border-radius:9px;border:none;background:transparent;font-size:.8rem;font-weight:600;color:var(--mut);cursor:pointer;transition:background .2s,color .2s;font-family:var(--fb)}
.wt-tab.on{background:var(--grad);color:#fff}
.wt-tab:not(.on):hover{color:#fff;background:rgba(255,255,255,.07)}
.wt-panel{display:none;animation:fadeUp .3s ease}
.wt-panel.on{display:grid;grid-template-columns:1fr 1.05fr;gap:3rem;align-items:center}
@keyframes fadeUp{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:none}}
.wt-h3{font-family:var(--fh);font-size:1.55rem;font-weight:700;color:#fff;margin-bottom:.8rem;line-height:1.3}
.wt-p{color:var(--mut-l);font-size:.95rem;line-height:1.72;margin-bottom:1.2rem}
.wt-feats{display:flex;flex-direction:column;gap:.55rem}
.wt-f{display:flex;align-items:flex-start;gap:.6rem}
.wt-fi{width:21px;height:21px;border-radius:6px;background:rgba(15,115,186,.2);display:flex;align-items:center;justify-content:center;font-size:.6rem;color:hsl(201,85%,66%);flex-shrink:0;margin-top:.05rem}
.wt-ft{font-size:.875rem;color:var(--mut-l);line-height:1.55}
.wt-ft strong{color:rgba(255,255,255,.88);font-weight:600}
/* Screen */
.wt-scr{background:var(--dark-2);border:1px solid var(--b-dark-s);border-radius:var(--rl);overflow:hidden;box-shadow:0 24px 64px rgba(0,0,0,.55)}
.wt-scr-h{background:var(--dark-3);padding:.6rem 1rem;border-bottom:1px solid var(--b-dark);display:flex;align-items:center;gap:.5rem;font-size:.68rem;color:var(--mut);font-weight:600}
.wt-scr-h i{color:hsl(201,85%,56%)}
.wt-scr-b{padding:.85rem}
.scr-row{display:flex;align-items:center;gap:.7rem;padding:.5rem .55rem;border-radius:8px;border-bottom:1px solid var(--b-dark)}
.scr-row:last-child{border-bottom:none}
.scr-row:hover{background:rgba(255,255,255,.03)}
.scr-av{width:27px;height:27px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.62rem;font-weight:700;color:#fff;flex-shrink:0}
.scr-inf{flex:1}
.scr-nm{font-size:.73rem;font-weight:600;color:rgba(255,255,255,.83)}
.scr-sb{font-size:.62rem;color:var(--mut)}
.scr-bdg{font-size:.6rem;font-weight:600;padding:.13rem .4rem;border-radius:5px}
.bdg-grn{background:rgba(34,197,94,.14);color:hsl(144,60%,55%)}
.bdg-yel{background:rgba(234,179,8,.14);color:hsl(45,90%,60%)}
.bdg-blu{background:rgba(15,115,186,.18);color:hsl(201,85%,65%)}
.bdg-red{background:rgba(239,68,68,.14);color:#f87171}
.bdg-pur{background:rgba(139,92,246,.18);color:hsl(265,80%,75%)}

/* ════════════════════════════════════════════════════════════════
   BENEFITS
════════════════════════════════════════════════════════════════ */
.ben-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1.5rem}
.ben-card{padding:2rem 1.75rem;border-radius:var(--rl);border:1px solid #e2e8f2;background:#fff;transition:transform .25s,box-shadow .25s}
.ben-card:hover{transform:translateY(-4px);box-shadow:0 20px 56px rgba(0,0,0,.08)}
.ben-ic{width:50px;height:50px;border-radius:13px;background:var(--grad);display:flex;align-items:center;justify-content:center;font-size:1.25rem;color:#fff;margin-bottom:1.2rem;box-shadow:0 6px 20px hsl(201,85%,39%,.24)}
.ben-t{font-family:var(--fh);font-size:1.05rem;font-weight:700;color:#0f172a;margin-bottom:.5rem}
.ben-d{font-size:.9rem;color:#64748b;line-height:1.65}

/* ════════════════════════════════════════════════════════════════
   WHY CLINOVIA
════════════════════════════════════════════════════════════════ */
.why-grid{display:grid;grid-template-columns:1fr 1fr;gap:4rem;align-items:center}
.why-list{display:flex;flex-direction:column;gap:1.2rem}
.why-it{display:flex;gap:.95rem;align-items:flex-start}
.why-ic{width:37px;height:37px;border-radius:9px;background:var(--grad);display:flex;align-items:center;justify-content:center;font-size:.9rem;color:#fff;flex-shrink:0;box-shadow:0 4px 12px hsl(201,85%,39%,.2)}
.why-t{font-weight:700;color:#0f172a;font-size:.95rem;margin-bottom:.18rem}
.why-d{font-size:.875rem;color:#64748b;line-height:1.62}
/* Comparison card */
.why-vis{background:var(--dark);border-radius:var(--rxl);padding:2rem 1.75rem;border:1px solid var(--b-dark-s);box-shadow:0 20px 60px rgba(0,0,0,.14)}
.why-vis-h{font-family:var(--fh);font-size:.72rem;font-weight:700;color:var(--mut);text-transform:uppercase;letter-spacing:.07em;margin-bottom:1.35rem}
.cmp-row{display:grid;grid-template-columns:1fr 26px 1fr;gap:.75rem;align-items:center;margin-bottom:.7rem}
.cmp-r{font-size:.78rem;color:rgba(255,255,255,.45);text-align:right}
.cmp-r.good{color:rgba(255,255,255,.82);font-weight:600}
.cmp-ic{width:26px;height:26px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.68rem;justify-self:center}
.ic-y{background:rgba(34,197,94,.14);color:hsl(144,60%,55%)}
.ic-n{background:rgba(239,68,68,.14);color:#f87171}
.cmp-l{font-size:.78rem;color:rgba(255,255,255,.45)}
.why-vis-div{height:1px;background:var(--b-dark);margin:1rem 0}
.why-vis-note{font-size:.73rem;color:var(--mut);text-align:center}
.why-vis-note span{color:hsl(201,85%,65%);font-weight:600}

/* ════════════════════════════════════════════════════════════════
   TESTIMONIALS
════════════════════════════════════════════════════════════════ */
.test-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1.5rem}
.tc{background:#fff;border:1px solid #e2e8f2;border-radius:var(--rl);padding:1.75rem;transition:transform .25s,box-shadow .25s;position:relative}
.tc:hover{transform:translateY(-3px);box-shadow:0 16px 46px rgba(0,0,0,.08)}
.tc-stars{color:#f59e0b;font-size:.75rem;margin-bottom:.8rem}
.tc-quote{font-size:.9rem;color:#374151;line-height:1.75;margin-bottom:1.25rem;position:relative;padding-top:.85rem}
.tc-quote::before{content:'\201C';font-size:3.2rem;font-family:Georgia,serif;color:var(--p);opacity:.18;position:absolute;top:-.85rem;left:-.35rem;line-height:1}
.tc-auth{display:flex;align-items:center;gap:.75rem}
.tc-av{width:39px;height:39px;border-radius:50%;background:var(--grad);display:flex;align-items:center;justify-content:center;font-size:.88rem;font-weight:700;color:#fff;flex-shrink:0}
.tc-name{font-weight:700;font-size:.875rem;color:#0f172a}
.tc-role{font-size:.77rem;color:#9ca3af}

/* ════════════════════════════════════════════════════════════════
   FAQ
════════════════════════════════════════════════════════════════ */
.faq-wrap{max-width:760px;margin:0 auto;display:flex;flex-direction:column;gap:.7rem}
.faq-it{background:#fff;border:1px solid #e2e8f2;border-radius:var(--r);overflow:hidden;transition:border-color .2s}
.faq-it:hover{border-color:rgba(15,115,186,.25)}
.faq-q{width:100%;background:none;border:none;padding:1.2rem 1.45rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;cursor:pointer;text-align:left}
.faq-qt{font-size:.93rem;font-weight:600;color:#0f172a}
.faq-ch{width:28px;height:28px;border-radius:50%;background:#f1f5f9;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:.72rem;color:#64748b;transition:transform .3s,background .2s}
.faq-it.open .faq-ch{transform:rotate(180deg);background:var(--grad);color:#fff}
.faq-ans{max-height:0;overflow:hidden;transition:max-height .35s ease}
.faq-it.open .faq-ans{max-height:250px}
.faq-ans-in{padding:0 1.45rem 1.2rem;font-size:.9rem;color:#64748b;line-height:1.72}

/* ════════════════════════════════════════════════════════════════
   CTA SECTION
════════════════════════════════════════════════════════════════ */
.cta-sec{position:relative;overflow:hidden}
.cta-sec::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse at 20% 50%,hsl(201,85%,39%,.22) 0%,transparent 58%),radial-gradient(ellipse at 80% 50%,hsl(265,58%,54%,.2) 0%,transparent 58%);pointer-events:none}
.cta-inner{max-width:680px;margin:0 auto;text-align:center;position:relative;z-index:1}
.cta-h2{font-family:var(--fh);font-size:clamp(1.75rem,3.5vw,2.9rem);font-weight:900;color:#fff;line-height:1.18;letter-spacing:-.03em;margin-bottom:.9rem}
.cta-p{font-size:1rem;color:var(--mut-l);margin-bottom:2.25rem;line-height:1.72}
.cta-btns{display:flex;gap:.9rem;justify-content:center;flex-wrap:wrap}
.cta-note{margin-top:1.5rem;font-size:.78rem;color:var(--mut)}
.cta-note strong{color:var(--mut-l)}
.cta-creds{display:flex;gap:.75rem;justify-content:center;flex-wrap:wrap;margin-top:1rem}
.cta-demo-note{margin-top:1.25rem;font-size:.75rem;color:var(--mut);font-family:'Courier New',monospace}
.cta-demo-note strong{color:hsl(201,85%,65%)}

/* Walkthrough panel text (on dark bg) */
.wt-content h3,.wt-h3{font-family:var(--fh);font-size:1.55rem;font-weight:700;color:#fff;margin-bottom:.8rem;line-height:1.3}
.wt-content p,.wt-p{color:var(--mut-l);font-size:.95rem;line-height:1.72;margin-bottom:1.2rem}
.wt-content .wt-feats{display:flex;flex-direction:column;gap:.55rem}
.wt-content .wt-f{display:flex;align-items:flex-start;gap:.6rem}
.wt-content .wt-fi{width:21px;height:21px;border-radius:6px;background:rgba(15,115,186,.2);display:flex;align-items:center;justify-content:center;font-size:.6rem;color:hsl(201,85%,66%);flex-shrink:0;margin-top:.05rem}
.wt-content .wt-ft{font-size:.875rem;color:var(--mut-l);line-height:1.55}
.wt-content .wt-ft strong{color:rgba(255,255,255,.88);font-weight:600}

/* ════════════════════════════════════════════════════════════════
   FOOTER
════════════════════════════════════════════════════════════════ */
footer{background:var(--dark-2);border-top:1px solid var(--b-dark);padding:4.5rem 2rem 2.25rem}
.ft-inner{max-width:1200px;margin:0 auto}
.ft-top{display:grid;grid-template-columns:1.8fr 1fr 1fr 1fr;gap:3rem;padding-bottom:3rem;border-bottom:1px solid var(--b-dark)}
.ft-brand-row{display:flex;align-items:center;gap:.6rem;margin-bottom:1rem;text-decoration:none}
.ft-logo{width:34px;height:34px;border-radius:9px;background:var(--grad);display:flex;align-items:center;justify-content:center;font-size:.9rem;color:#fff}
.ft-name{font-family:var(--fh);font-weight:700;font-size:1.05rem;color:#fff}
.ft-tag{font-size:.875rem;color:var(--mut);line-height:1.65;max-width:250px;margin-bottom:1.5rem}
.ft-bdg{display:inline-flex;align-items:center;gap:.4rem;background:rgba(15,115,186,.1);border:1px solid rgba(15,115,186,.2);border-radius:8px;padding:.3rem .7rem;font-size:.7rem;color:hsl(201,85%,65%);font-weight:600}
.ft-ch{font-size:.78rem;font-weight:700;color:rgba(255,255,255,.88);text-transform:uppercase;letter-spacing:.07em;margin-bottom:1.05rem}
.ft-links{list-style:none;display:flex;flex-direction:column;gap:.55rem}
.ft-links a{font-size:.875rem;color:var(--mut);transition:color .2s}
.ft-links a:hover{color:rgba(255,255,255,.82)}
.ft-bot{display:flex;align-items:center;justify-content:space-between;padding-top:1.75rem;flex-wrap:wrap;gap:1rem}
.ft-copy{font-size:.79rem;color:var(--mut)}
.ft-copy strong{color:rgba(255,255,255,.55)}
.ft-bot-links{display:flex;gap:1.5rem}
.ft-bot-links a{font-size:.79rem;color:var(--mut);transition:color .2s}
.ft-bot-links a:hover{color:rgba(255,255,255,.65)}

/* ════════════════════════════════════════════════════════════════
   RESPONSIVE
════════════════════════════════════════════════════════════════ */
@media(max-width:1024px){
  .hero-inner{grid-template-columns:1fr}
  .mockup-wrap{display:none}
  .hero{min-height:auto;padding:7rem 2rem 4.5rem}
  .wt-panel.on{grid-template-columns:1fr}
  .why-grid{grid-template-columns:1fr}
  .why-vis{max-width:480px}
  .ft-top{grid-template-columns:1fr 1fr;gap:2rem}
  .bento{grid-template-columns:1fr 1fr}
  .bc.s2{grid-column:span 2}
  .bc.s3{grid-column:span 2}
}
@media(max-width:768px){
  .nav-links,.nav-right{display:none}
  .nav-mob{display:block}
  .stats-inner{grid-template-columns:1fr 1fr;gap:2rem}
  .stat-div{display:none}
  .ben-grid{grid-template-columns:1fr}
  .test-grid{grid-template-columns:1fr}
  .bento{grid-template-columns:1fr}
  .bc.s2,.bc.s3{grid-column:span 1}
  .bc-extra{grid-template-columns:1fr}
  .ft-top{grid-template-columns:1fr}
  .ft-bot{flex-direction:column;align-items:flex-start}
  .wt-tabs{gap:.2rem}
  .wt-tab{min-width:80px;padding:.45rem .65rem;font-size:.75rem}
  .section{padding:4rem 1.25rem}
}
@media(max-width:480px){
  .hero-btns,.cta-btns{flex-direction:column}
  .btn-pri,.btn-ghost{text-align:center;justify-content:center}
  .stats-inner{grid-template-columns:1fr 1fr}
  .stats-strip{padding:2.5rem 1.25rem}
}
</style>
</head>
<body>

<!-- ══════════════════════════════════════════════════════════════
     NAVBAR
══════════════════════════════════════════════════════════════ -->
<nav class="nav" id="navbar">
  <a href="/" class="nav-brand">
    <div class="nav-icon"><img src="/clinovia-icon.svg" alt="Clinovia" width="22" height="22" style="display:block;border-radius:5px"></div>
    <span class="nav-name">Clinovia</span>
  </a>
  <ul class="nav-links">
    <li><a href="#features">Features</a></li>
    <li><a href="#walkthrough">Walkthrough</a></li>
    <li><a href="#why">Why Clinovia</a></li>
    <li><a href="#faq">FAQ</a></li>
  </ul>
  <div class="nav-right">
    <a href="{{ route('login') }}" class="nav-login">Sign In</a>
    <a href="{{ route('login') }}" class="nav-cta">View Demo &rarr;</a>
  </div>
  <button class="nav-mob" id="navMobBtn" aria-label="Menu"><i class="bi bi-list"></i></button>
</nav>

<!-- Mobile Drawer -->
<div class="mob-drawer" id="mobDrawer">
  <button class="mob-close" id="mobClose"><i class="bi bi-x-lg"></i></button>
  <a href="#features" onclick="closeMob()">Features</a>
  <a href="#walkthrough" onclick="closeMob()">Walkthrough</a>
  <a href="#why" onclick="closeMob()">Why Clinovia</a>
  <a href="#faq" onclick="closeMob()">FAQ</a>
  <a href="{{ route('login') }}" style="margin-top:.75rem;color:rgba(255,255,255,.9)">Sign In</a>
  <a href="{{ route('login') }}" style="background:linear-gradient(135deg,hsl(201,85%,39%),hsl(265,58%,54%));border-radius:10px;padding:.85rem 1.5rem;text-align:center;font-weight:700;color:#fff;margin-top:.5rem">View Demo</a>
</div>

<!-- ══════════════════════════════════════════════════════════════
     HERO
══════════════════════════════════════════════════════════════ -->
<section class="hero" id="home">
  <div class="hero-orb orb-1"></div>
  <div class="hero-orb orb-2"></div>
  <div class="hero-orb orb-3"></div>
  <div class="hero-dots"></div>

  <div class="hero-inner">
    <!-- Left -->
    <div>
      <div class="hero-badge"><span class="badge-live"></span> Live Demo Available</div>
      <h1 class="hero-h1">
        The Smart Clinic<br>
        Management System<br>
        <span class="grad-text">Built for Schools</span>
      </h1>
      <p class="hero-sub">
        From patient records and appointments to medicine inventory, AI assistance, and real-time SMS — Clinovia gives school clinics one unified platform to deliver better care, faster.
      </p>
      <div class="hero-btns">
        <a href="{{ route('login') }}" class="btn-pri"><i class="bi bi-play-circle-fill"></i> Explore Live Demo</a>
        <a href="#features" class="btn-ghost"><i class="bi bi-grid-3x3-gap-fill"></i> See All Features</a>
      </div>
      <div class="hero-demo">
        <span class="demo-label">Quick access:</span>
        <span class="demo-chip">admin@clinovia.app / Admin@2026!</span>
        <span class="demo-chip">viewer@clinovia.app / Viewer@2026!</span>
      </div>
    </div>

    <!-- Right: Dashboard Mockup -->
    <div class="mockup-wrap">
      <div class="mockup-glow"></div>
      <div class="mockup-frame">
        <!-- Titlebar -->
        <div class="m-bar">
          <div class="m-dot m-red"></div>
          <div class="m-dot m-yel"></div>
          <div class="m-dot m-grn"></div>
          <div class="m-url">clinovia.onrender.com/dashboard</div>
        </div>
        <!-- Body -->
        <div class="m-body">
          <!-- Sidebar -->
          <div class="m-side">
            <div class="m-brand">
              <div class="m-brand-ic"><img src="/clinovia-icon.svg" alt="" width="14" height="14" style="display:block;border-radius:3px"></div>
              <span class="m-brand-nm">Clinovia</span>
            </div>
            <div class="m-nav on"><i class="bi bi-grid-fill"></i> Dashboard</div>
            <div class="m-nav"><i class="bi bi-people-fill"></i> Patients</div>
            <div class="m-nav"><i class="bi bi-calendar2-check-fill"></i> Appointments</div>
            <div class="m-nav"><i class="bi bi-clipboard2-pulse-fill"></i> Consultations</div>
            <div class="m-nav"><i class="bi bi-capsule"></i> Medicines</div>
            <div class="m-nav"><i class="bi bi-box-seam-fill"></i> Inventory</div>
            <div class="m-nav"><i class="bi bi-receipt"></i> Dispensing</div>
            <div class="m-nav"><i class="bi bi-bar-chart-fill"></i> Reports</div>
            <div class="m-nav"><i class="bi bi-stars"></i> Cobi AI</div>
          </div>
          <!-- Main -->
          <div class="m-main">
            <div class="m-top">
              <span class="m-pg-title">Dashboard</span>
              <div class="m-usr">
                <div class="m-av">A</div>
                <span class="m-un">Admin Nurse</span>
              </div>
            </div>
            <div class="m-stats">
              <div class="m-sc"><div class="m-sl">Patients</div><div class="m-sv">247</div><div class="m-ss">↑ 12 this week</div></div>
              <div class="m-sc"><div class="m-sl">Appointments</div><div class="m-sv">18</div><div class="m-ss">↑ 4 pending</div></div>
              <div class="m-sc"><div class="m-sl">Medicines</div><div class="m-sv">56</div><div class="m-ss">3 low stock</div></div>
              <div class="m-sc"><div class="m-sl">Reports</div><div class="m-sv">12</div><div class="m-ss">this month</div></div>
            </div>
            <div class="m-g2">
              <div class="m-card">
                <div class="m-ct">Recent Consultations</div>
                <div class="m-row"><div class="m-rd" style="background:hsl(201,85%,55%)"></div><div class="m-rn">Maria Santos</div><div class="mbg bg-g">Completed</div></div>
                <div class="m-row"><div class="m-rd" style="background:hsl(265,58%,65%)"></div><div class="m-rn">Juan Dela Cruz</div><div class="mbg bg-b">In Progress</div></div>
                <div class="m-row"><div class="m-rd" style="background:hsl(144,60%,50%)"></div><div class="m-rn">Ana Reyes</div><div class="mbg bg-g">Completed</div></div>
                <div class="m-row"><div class="m-rd" style="background:hsl(38,90%,55%)"></div><div class="m-rn">Carlo Bautista</div><div class="mbg bg-y">Pending</div></div>
              </div>
              <div class="m-card">
                <div class="m-ct">Monthly Visits</div>
                <div class="m-bars">
                  <div class="m-bar" style="height:40%"></div>
                  <div class="m-bar" style="height:60%"></div>
                  <div class="m-bar" style="height:50%"></div>
                  <div class="m-bar" style="height:80%"></div>
                  <div class="m-bar" style="height:65%"></div>
                  <div class="m-bar" style="height:90%"></div>
                  <div class="m-bar" style="height:75%"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════════════
     STATS STRIP
══════════════════════════════════════════════════════════════ -->
<div class="stats-strip">
  <div class="stats-inner">
    <div class="stat-it">
      <div class="stat-num" data-count="10">0+</div>
      <div class="stat-lbl">Integrated Modules</div>
    </div>
    <div class="stat-div"></div>
    <div class="stat-it">
      <div class="stat-num" data-count="100">0%</div>
      <div class="stat-lbl">Role-Based Access Control</div>
    </div>
    <div class="stat-div"></div>
    <div class="stat-it">
      <div class="stat-num" data-count="0">AI</div>
      <div class="stat-lbl">Powered Assistant (Cobi)</div>
    </div>
    <div class="stat-div"></div>
    <div class="stat-it">
      <div class="stat-num" data-count="0">SMS</div>
      <div class="stat-lbl">Real-Time Notifications</div>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════════════
     FEATURES — BENTO GRID
══════════════════════════════════════════════════════════════ -->
<section class="section bg-sl" id="features">
  <div class="si">
    <div class="sh c reveal">
      <span class="eyebrow">Features</span>
      <h2 class="sec-h2">Everything your school clinic needs</h2>
      <p class="sec-p">One platform, ten powerful modules. Clinovia handles every aspect of school clinic operations — from patient intake to AI-powered assistance.</p>
    </div>

    <div class="bento">

      <!-- Patient Records — wide -->
      <div class="bc s2 reveal d1">
        <div class="fi fi-b"><i class="bi bi-people-fill"></i></div>
        <div class="f-lbl">Core Module</div>
        <div class="f-title">Patient Health Records</div>
        <div class="f-desc">Full CRUD patient management with medical history, emergency contacts, guardian information, and activity logs — all in one organised profile.</div>
        <div class="bc-extra">
          <div class="bc-mini"><i class="bi bi-check2-circle"></i><div><div class="bc-mini-t">9 Patient Categories</div><div class="bc-mini-d">College, Senior High, Junior High, Elementary, Kinder, Daycare, Teacher, Employee, Visitor</div></div></div>
          <div class="bc-mini"><i class="bi bi-clock-history"></i><div><div class="bc-mini-t">Complete Visit Timeline</div><div class="bc-mini-d">Full history of consultations, dispensing, and appointment records per patient</div></div></div>
        </div>
        <div class="f-tags"><span class="ftag">Medical History</span><span class="ftag">Emergency Contacts</span><span class="ftag">Soft Delete + Restore</span></div>
      </div>

      <!-- Appointments -->
      <div class="bc reveal d2">
        <div class="fi fi-p"><i class="bi bi-calendar2-check-fill"></i></div>
        <div class="f-lbl">Scheduling</div>
        <div class="f-title">Appointment Scheduling</div>
        <div class="f-desc">Time-slot-based scheduling with a complete approval workflow. Approve, cancel, or mark no-shows with automatic SMS notifications.</div>
        <div class="f-tags"><span class="ftag">Time Slots</span><span class="ftag">Approval Workflow</span><span class="ftag">SMS Alerts</span></div>
      </div>

      <!-- Consultations -->
      <div class="bc reveal d1">
        <div class="fi fi-g"><i class="bi bi-clipboard2-pulse-fill"></i></div>
        <div class="f-lbl">Medical Records</div>
        <div class="f-title">Consultations</div>
        <div class="f-desc">Document every clinic visit with chief complaints, vital signs, diagnosis, treatment notes, and link to patient appointments seamlessly.</div>
        <div class="f-tags"><span class="ftag">Vital Signs</span><span class="ftag">Diagnosis Notes</span><span class="ftag">Linked Appointments</span></div>
      </div>

      <!-- Medicine Inventory — wide -->
      <div class="bc s2 reveal d2">
        <div class="fi fi-o"><i class="bi bi-capsule"></i></div>
        <div class="f-lbl">Pharmacy</div>
        <div class="f-title">Medicine Inventory & Dispensing</div>
        <div class="f-desc">Complete medicine catalog with stock tracking, expiry date monitoring, and automated low-stock alerts. Dispense medicines to patients and maintain a full ledger of all transactions.</div>
        <div class="bc-extra">
          <div class="bc-mini"><i class="bi bi-exclamation-triangle-fill"></i><div><div class="bc-mini-t">Expiry Alerts</div><div class="bc-mini-d">Get notified before medicines expire. Colour-coded stock levels for quick visual scanning</div></div></div>
          <div class="bc-mini"><i class="bi bi-prescription2"></i><div><div class="bc-mini-t">Dispensing Records</div><div class="bc-mini-d">Link dispensing to consultations. Full stock-in/stock-out transaction ledger</div></div></div>
        </div>
        <div class="f-tags"><span class="ftag">Categories</span><span class="ftag">Reorder Threshold</span><span class="ftag">Transaction History</span></div>
      </div>

      <!-- SMS -->
      <div class="bc reveal d3">
        <div class="fi fi-r"><i class="bi bi-chat-dots-fill"></i></div>
        <div class="f-lbl">Communication</div>
        <div class="f-title">SMS Notifications</div>
        <div class="f-desc">Integrated Semaphore SMS API. Send appointment confirmations and reminders directly from Clinovia with customisable message templates.</div>
        <div class="f-tags"><span class="ftag">Semaphore API</span><span class="ftag">Custom Templates</span><span class="ftag">Delivery Logs</span></div>
      </div>

      <!-- AI Assistant Cobi — wide -->
      <div class="bc s2 reveal d1" style="background:linear-gradient(135deg,hsl(201,85%,39%,.04),hsl(265,58%,54%,.04));border-color:rgba(15,115,186,.18)">
        <div class="fi" style="background:linear-gradient(135deg,hsl(201,85%,39%,.15),hsl(265,58%,54%,.15));color:hsl(265,58%,54%)"><i class="bi bi-stars"></i></div>
        <div class="f-lbl" style="-webkit-text-fill-color:unset;background:none;color:var(--s)">AI Assistant</div>
        <div class="f-title">Cobi — Your Clinic AI</div>
        <div class="f-desc">Powered by Groq's gpt-oss-120b model (120B parameters). Cobi acts as a senior software engineer, technology doctor, IT support specialist, and research assistant — answering any clinic system or medical knowledge question intelligently.</div>
        <div class="f-tags"><span class="ftag">gpt-oss-120b</span><span class="ftag">12-Turn Memory</span><span class="ftag">Prompt-Injection Guard</span><span class="ftag">Retry Logic</span></div>
      </div>

      <!-- Audit Logs -->
      <div class="bc reveal d2">
        <div class="fi fi-t"><i class="bi bi-shield-check"></i></div>
        <div class="f-lbl">Compliance</div>
        <div class="f-title">Audit Logs</div>
        <div class="f-desc">Full activity tracking across all modules. Every create, update, and delete records the user, timestamp, and before/after values for complete traceability.</div>
        <div class="f-tags"><span class="ftag">Before / After</span><span class="ftag">IP Tracking</span><span class="ftag">All Modules</span></div>
      </div>

      <!-- Reports -->
      <div class="bc reveal d3">
        <div class="fi fi-i"><i class="bi bi-bar-chart-fill"></i></div>
        <div class="f-lbl">Analytics</div>
        <div class="f-title">Reports & Analytics</div>
        <div class="f-desc">Generate daily, monthly, and annual reports. Medicine usage, inventory snapshots, appointment summaries — export to PDF or CSV.</div>
        <div class="f-tags"><span class="ftag">PDF Export</span><span class="ftag">CSV Export</span><span class="ftag">5 Report Types</span></div>
      </div>

      <!-- User Management + RBAC — wide -->
      <div class="bc s2 reveal d1">
        <div class="fi fi-a"><i class="bi bi-people-fill"></i></div>
        <div class="f-lbl">Administration</div>
        <div class="f-title">User Management & Role-Based Access</div>
        <div class="f-desc">Fine-grained permission system with four built-in roles. Administrators control who can view, create, update, or delete any resource across the entire system.</div>
        <div class="bc-extra">
          <div class="bc-mini"><i class="bi bi-person-badge-fill"></i><div><div class="bc-mini-t">4 Built-in Roles</div><div class="bc-mini-d">Administrator · Nurse · Staff · Viewer — each with carefully scoped permissions</div></div></div>
          <div class="bc-mini"><i class="bi bi-lock-fill"></i><div><div class="bc-mini-t">Granular Permissions</div><div class="bc-mini-d">30+ individual permissions per resource. Powered by Spatie Laravel-Permission</div></div></div>
        </div>
        <div class="f-tags"><span class="ftag">Spatie Permission</span><span class="ftag">Active / Inactive Users</span><span class="ftag">Admin-Only Registration</span></div>
      </div>

    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════════════
     WALKTHROUGH
══════════════════════════════════════════════════════════════ -->
<section class="section bg-dk wt-section" id="walkthrough">
  <div class="si">
    <div class="sh reveal">
      <span class="eyebrow">Product Walkthrough</span>
      <h2 class="sec-h2">See Clinovia in action</h2>
      <p class="sec-p">A guided tour through the five most-used areas of the platform.</p>
    </div>

    <div class="wt-tabs reveal d1">
      <button class="wt-tab on" data-tab="t-dash">Dashboard</button>
      <button class="wt-tab" data-tab="t-pat">Patients</button>
      <button class="wt-tab" data-tab="t-appt">Appointments</button>
      <button class="wt-tab" data-tab="t-med">Medicines</button>
      <button class="wt-tab" data-tab="t-ai">AI Assistant</button>
    </div>

    <!-- Dashboard -->
    <div class="wt-panel on" id="t-dash">
      <div class="wt-content">
        <h3>Real-Time Clinic Dashboard</h3>
        <p>The moment you log in, Clinovia shows you everything that matters — active patients, pending appointments, low-stock alerts, and today's consultations at a glance.</p>
        <div class="wt-feats">
          <div class="wt-f"><div class="wt-fi"><i class="bi bi-check2"></i></div><div class="wt-ft"><strong>Live statistics</strong> — patients, appointments, medicines, and dispensing updated in real time</div></div>
          <div class="wt-f"><div class="wt-fi"><i class="bi bi-check2"></i></div><div class="wt-ft"><strong>Low-stock warnings</strong> — medicines below reorder threshold highlighted immediately</div></div>
          <div class="wt-f"><div class="wt-fi"><i class="bi bi-check2"></i></div><div class="wt-ft"><strong>Today's schedule</strong> — pending and approved appointments visible on first load</div></div>
          <div class="wt-f"><div class="wt-fi"><i class="bi bi-check2"></i></div><div class="wt-ft"><strong>Role-aware view</strong> — nurses, staff, and admins each see only what they need</div></div>
        </div>
      </div>
      <div class="wt-scr">
        <div class="wt-scr-h"><i class="bi bi-grid-fill"></i> Dashboard</div>
        <div class="wt-scr-b">
          <div class="scr-row" style="background:rgba(15,115,186,.06);border-radius:8px;margin-bottom:.3rem"><div class="scr-av" style="background:var(--grad)">📊</div><div class="scr-inf"><div class="scr-nm">Today: 8 consultations completed</div><div class="scr-sb">3 pending · 1 no-show</div></div><div class="scr-bdg bdg-grn">Active</div></div>
          <div class="scr-row"><div class="scr-av" style="background:hsl(348,75%,52%)">!</div><div class="scr-inf"><div class="scr-nm">Paracetamol 500mg</div><div class="scr-sb">Only 8 tablets remaining</div></div><div class="scr-bdg bdg-red">Low Stock</div></div>
          <div class="scr-row"><div class="scr-av" style="background:hsl(38,90%,50%)">📅</div><div class="scr-inf"><div class="scr-nm">Juan Dela Cruz — 2:00 PM</div><div class="scr-sb">General Check-up</div></div><div class="scr-bdg bdg-yel">Pending</div></div>
          <div class="scr-row"><div class="scr-av" style="background:hsl(265,58%,54%)">📅</div><div class="scr-inf"><div class="scr-nm">Maria Santos — 3:30 PM</div><div class="scr-sb">Follow-up Consultation</div></div><div class="scr-bdg bdg-blu">Approved</div></div>
          <div class="scr-row"><div class="scr-av" style="background:hsl(144,60%,40%)">✓</div><div class="scr-inf"><div class="scr-nm">Ana Reyes — Consultation Done</div><div class="scr-sb">Amoxicillin dispensed</div></div><div class="scr-bdg bdg-grn">Done</div></div>
        </div>
      </div>
    </div>

    <!-- Patients -->
    <div class="wt-panel" id="t-pat">
      <div class="wt-content">
        <h3>Complete Patient Records</h3>
        <p>Clinovia maintains rich, searchable health records for every patient — students, teachers, employees, and visitors — with full medical history and visit timeline.</p>
        <div class="wt-feats">
          <div class="wt-f"><div class="wt-fi"><i class="bi bi-check2"></i></div><div class="wt-ft"><strong>9 patient categories</strong> — from Kinder to College, plus Teachers, Employees, and Visitors</div></div>
          <div class="wt-f"><div class="wt-fi"><i class="bi bi-check2"></i></div><div class="wt-ft"><strong>Medical profile</strong> — allergies, existing conditions, blood type, emergency contacts, and guardian info</div></div>
          <div class="wt-f"><div class="wt-fi"><i class="bi bi-check2"></i></div><div class="wt-ft"><strong>Consultation history</strong> — every visit, dispensing record, and appointment linked to the patient</div></div>
          <div class="wt-f"><div class="wt-fi"><i class="bi bi-check2"></i></div><div class="wt-ft"><strong>Soft delete & restore</strong> — safely deactivate records without permanent loss</div></div>
        </div>
      </div>
      <div class="wt-scr">
        <div class="wt-scr-h"><i class="bi bi-people-fill"></i> Patient Records</div>
        <div class="wt-scr-b">
          <div class="scr-row"><div class="scr-av" style="background:hsl(201,85%,39%)">MS</div><div class="scr-inf"><div class="scr-nm">Maria Santos</div><div class="scr-sb">College · 20 yrs · F</div></div><div class="scr-bdg bdg-blu">College</div></div>
          <div class="scr-row"><div class="scr-av" style="background:hsl(265,58%,54%)">JD</div><div class="scr-inf"><div class="scr-nm">Juan Dela Cruz</div><div class="scr-sb">Senior High · 17 yrs · M</div></div><div class="scr-bdg bdg-pur">Sr. High</div></div>
          <div class="scr-row"><div class="scr-av" style="background:hsl(144,60%,40%)">AR</div><div class="scr-inf"><div class="scr-nm">Ana Reyes</div><div class="scr-sb">Teacher · 34 yrs · F</div></div><div class="scr-bdg bdg-grn">Teacher</div></div>
          <div class="scr-row"><div class="scr-av" style="background:hsl(38,90%,50%)">CB</div><div class="scr-inf"><div class="scr-nm">Carlo Bautista</div><div class="scr-sb">Elementary · 10 yrs · M</div></div><div class="scr-bdg bdg-yel">Elem.</div></div>
          <div class="scr-row"><div class="scr-av" style="background:hsl(348,75%,52%)">LP</div><div class="scr-inf"><div class="scr-nm">Liza Palma</div><div class="scr-sb">Employee · 42 yrs · F</div></div><div class="scr-bdg bdg-grn">Employee</div></div>
        </div>
      </div>
    </div>

    <!-- Appointments -->
    <div class="wt-panel" id="t-appt">
      <div class="wt-content">
        <h3>Effortless Appointment Scheduling</h3>
        <p>Book appointments by time slot, manage approvals, and automatically send SMS confirmations. The full workflow from request to visit is handled in one place.</p>
        <div class="wt-feats">
          <div class="wt-f"><div class="wt-fi"><i class="bi bi-check2"></i></div><div class="wt-ft"><strong>Configurable time slots</strong> — set available slots and capacity per day</div></div>
          <div class="wt-f"><div class="wt-fi"><i class="bi bi-check2"></i></div><div class="wt-ft"><strong>5 appointment statuses</strong> — Pending · Approved · Cancelled · No-Show · Completed</div></div>
          <div class="wt-f"><div class="wt-fi"><i class="bi bi-check2"></i></div><div class="wt-ft"><strong>SMS at every stage</strong> — automatic notifications when status changes via Semaphore</div></div>
          <div class="wt-f"><div class="wt-fi"><i class="bi bi-check2"></i></div><div class="wt-ft"><strong>Link to consultation</strong> — completed appointments create consultation records automatically</div></div>
        </div>
      </div>
      <div class="wt-scr">
        <div class="wt-scr-h"><i class="bi bi-calendar2-check-fill"></i> Appointments — Today</div>
        <div class="wt-scr-b">
          <div class="scr-row"><div class="scr-av" style="background:hsl(201,85%,39%)">9A</div><div class="scr-inf"><div class="scr-nm">Maria Santos — 9:00 AM</div><div class="scr-sb">Check-up · SMS sent</div></div><div class="scr-bdg bdg-grn">Completed</div></div>
          <div class="scr-row"><div class="scr-av" style="background:hsl(144,60%,40%)">10A</div><div class="scr-inf"><div class="scr-nm">Juan Dela Cruz — 10:00 AM</div><div class="scr-sb">Fever · SMS sent</div></div><div class="scr-bdg bdg-blu">Approved</div></div>
          <div class="scr-row"><div class="scr-av" style="background:hsl(265,58%,54%)">2P</div><div class="scr-inf"><div class="scr-nm">Ana Reyes — 2:00 PM</div><div class="scr-sb">Follow-up · Awaiting nurse</div></div><div class="scr-bdg bdg-yel">Pending</div></div>
          <div class="scr-row"><div class="scr-av" style="background:hsl(38,90%,50%)">3P</div><div class="scr-inf"><div class="scr-nm">Carlo Bautista — 3:00 PM</div><div class="scr-sb">Wound care</div></div><div class="scr-bdg bdg-yel">Pending</div></div>
          <div class="scr-row"><div class="scr-av" style="background:hsl(348,75%,52%)">11A</div><div class="scr-inf"><div class="scr-nm">Liza Palma — 11:00 AM</div><div class="scr-sb">Did not arrive</div></div><div class="scr-bdg bdg-red">No-Show</div></div>
        </div>
      </div>
    </div>

    <!-- Medicines -->
    <div class="wt-panel" id="t-med">
      <div class="wt-content">
        <h3>Smart Medicine Inventory</h3>
        <p>Track every medicine from arrival to dispensing. Clinovia alerts you before stock runs out or before medicines expire — so the clinic is always prepared.</p>
        <div class="wt-feats">
          <div class="wt-f"><div class="wt-fi"><i class="bi bi-check2"></i></div><div class="wt-ft"><strong>Full stock lifecycle</strong> — stock-in, stock-out, and dispensing all tracked with timestamps</div></div>
          <div class="wt-f"><div class="wt-fi"><i class="bi bi-check2"></i></div><div class="wt-ft"><strong>Expiry monitoring</strong> — medicines expiring within 30/60/90 days flagged automatically</div></div>
          <div class="wt-f"><div class="wt-fi"><i class="bi bi-check2"></i></div><div class="wt-ft"><strong>Category management</strong> — organise medicines by category for easy search and reporting</div></div>
          <div class="wt-f"><div class="wt-fi"><i class="bi bi-check2"></i></div><div class="wt-ft"><strong>Dispensing linked to consults</strong> — know exactly which patient received what, when, and why</div></div>
        </div>
      </div>
      <div class="wt-scr">
        <div class="wt-scr-h"><i class="bi bi-capsule"></i> Medicine Inventory</div>
        <div class="wt-scr-b">
          <div class="scr-row"><div class="scr-av" style="background:hsl(201,85%,39%)">Rx</div><div class="scr-inf"><div class="scr-nm">Paracetamol 500mg</div><div class="scr-sb">Analgesic · 8 tabs left</div></div><div class="scr-bdg bdg-red">Low Stock</div></div>
          <div class="scr-row"><div class="scr-av" style="background:hsl(144,60%,40%)">Rx</div><div class="scr-inf"><div class="scr-nm">Amoxicillin 250mg</div><div class="scr-sb">Antibiotic · 45 caps</div></div><div class="scr-bdg bdg-grn">In Stock</div></div>
          <div class="scr-row"><div class="scr-av" style="background:hsl(38,90%,50%)">Rx</div><div class="scr-inf"><div class="scr-nm">Ibuprofen 200mg</div><div class="scr-sb">NSAID · Expires Jun 2026</div></div><div class="scr-bdg bdg-yel">Expiring</div></div>
          <div class="scr-row"><div class="scr-av" style="background:hsl(265,58%,54%)">Rx</div><div class="scr-inf"><div class="scr-nm">Cetirizine 10mg</div><div class="scr-sb">Antihistamine · 120 tabs</div></div><div class="scr-bdg bdg-grn">In Stock</div></div>
          <div class="scr-row"><div class="scr-av" style="background:hsl(174,72%,36%)">Rx</div><div class="scr-inf"><div class="scr-nm">Betadine Solution</div><div class="scr-sb">Antiseptic · 2 bottles</div></div><div class="scr-bdg bdg-yel">Low</div></div>
        </div>
      </div>
    </div>

    <!-- AI -->
    <div class="wt-panel" id="t-ai">
      <div class="wt-content">
        <h3>Cobi — Your AI Clinic Assistant</h3>
        <p>Cobi is powered by the most capable model on Groq (gpt-oss-120b, 120B parameters). Ask anything — system navigation, medical terminology, IT support, or research questions.</p>
        <div class="wt-feats">
          <div class="wt-f"><div class="wt-fi"><i class="bi bi-check2"></i></div><div class="wt-ft"><strong>Senior expert persona</strong> — Software Engineer · Tech Doctor · IT Support · Research Assistant</div></div>
          <div class="wt-f"><div class="wt-fi"><i class="bi bi-check2"></i></div><div class="wt-ft"><strong>12-turn conversation memory</strong> — Cobi remembers context within your session for coherent multi-step answers</div></div>
          <div class="wt-f"><div class="wt-fi"><i class="bi bi-check2"></i></div><div class="wt-ft"><strong>Medical knowledge</strong> — explains terminology, vitals, common medications, and clinic documentation</div></div>
          <div class="wt-f"><div class="wt-fi"><i class="bi bi-check2"></i></div><div class="wt-ft"><strong>Security hardened</strong> — prompt injection detection, rate limiting, and private per-user conversation history</div></div>
        </div>
      </div>
      <div class="wt-scr">
        <div class="wt-scr-h"><i class="bi bi-stars"></i> Cobi — AI Assistant</div>
        <div class="wt-scr-b">
          <div class="scr-row" style="flex-direction:column;align-items:flex-end;border:none;padding-bottom:.4rem">
            <div style="background:linear-gradient(135deg,hsl(201,85%,39%),hsl(265,58%,54%));border-radius:12px 12px 3px 12px;padding:.5rem .75rem;font-size:.72rem;color:#fff;max-width:80%;line-height:1.5">How do I add a new patient to the system?</div>
            <div style="font-size:.58rem;color:var(--mut);margin-top:.2rem">You · 2:14 PM</div>
          </div>
          <div class="scr-row" style="flex-direction:column;align-items:flex-start;border:none;gap:.2rem">
            <div style="display:flex;align-items:center;gap:.3rem"><div class="scr-av" style="background:var(--grad);width:22px;height:22px;font-size:.5rem"><i class="bi bi-stars"></i></div><div style="font-size:.6rem;color:var(--mut);font-weight:600">Cobi</div></div>
            <div style="background:rgba(255,255,255,.05);border:1px solid var(--b-dark);border-radius:3px 12px 12px 12px;padding:.5rem .75rem;font-size:.7rem;color:rgba(255,255,255,.82);max-width:88%;line-height:1.55">Go to <strong style="color:hsl(201,85%,65%)">Patients → Create</strong> in the sidebar. Fill in the required fields: full name, category, date of birth, and contact number. Medical history and emergency contacts are optional but recommended. Click <strong style="color:hsl(201,85%,65%)">Save Patient</strong> and the record is immediately searchable.</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════════════
     BENEFITS
══════════════════════════════════════════════════════════════ -->
<section class="section" id="benefits">
  <div class="si">
    <div class="sh c reveal">
      <span class="eyebrow">Why It Works</span>
      <h2 class="sec-h2">Built the way clinics actually operate</h2>
      <p class="sec-p">Every design decision in Clinovia came from real school clinic workflows — not textbook assumptions.</p>
    </div>
    <div class="ben-grid">
      <div class="ben-card reveal d1">
        <div class="ben-ic"><i class="bi bi-lightning-charge-fill"></i></div>
        <div class="ben-t">Fast & Efficient</div>
        <div class="ben-d">Database-cached permissions, optimised queries, and lazy-loaded views keep every page snappy — even on free-tier hosting. Clinic staff spend time on care, not waiting for screens to load.</div>
      </div>
      <div class="ben-card reveal d2">
        <div class="ben-ic"><i class="bi bi-shield-fill-check"></i></div>
        <div class="ben-t">Secure & Trustworthy</div>
        <div class="ben-d">CSP headers, HSTS, CSRF protection, role-based access, audit logs, and bcrypt passwords. Production-grade security baked in from day one — not bolted on later.</div>
      </div>
      <div class="ben-card reveal d3">
        <div class="ben-ic"><i class="bi bi-stars"></i></div>
        <div class="ben-t">Intelligent & Modern</div>
        <div class="ben-d">An AI assistant that genuinely helps — not a chatbot that just says "I don't know." Cobi provides accurate, contextual answers to system, medical, and technical questions in real time.</div>
      </div>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════════════
     WHY CLINOVIA
══════════════════════════════════════════════════════════════ -->
<section class="section bg-sl" id="why">
  <div class="si">
    <div class="why-grid">
      <div>
        <div class="reveal">
          <span class="eyebrow">Why Clinovia</span>
          <h2 class="sec-h2">Paper and generic systems can't keep up</h2>
          <p class="sec-p" style="margin-bottom:2.5rem">Clinovia was purpose-built for the realities of school clinic work — not adapted from a general EHR template.</p>
        </div>
        <div class="why-list">
          <div class="why-it reveal d1">
            <div class="why-ic"><i class="bi bi-building-check"></i></div>
            <div><div class="why-t">School-Specific Categories</div><div class="why-d">Handles all patient types in a school — from Kinder students to visiting parents — with appropriate data fields for each.</div></div>
          </div>
          <div class="why-it reveal d2">
            <div class="why-ic"><i class="bi bi-cloud-check-fill"></i></div>
            <div><div class="why-t">Zero Infrastructure Cost</div><div class="why-d">Deployed on Render free tier with SQLite — no database server to manage, no monthly hosting bill. Runs reliably in a Docker container.</div></div>
          </div>
          <div class="why-it reveal d3">
            <div class="why-ic"><i class="bi bi-phone-fill"></i></div>
            <div><div class="why-t">SMS Built In, Not Bolted On</div><div class="why-d">Semaphore SMS is woven into the appointment workflow. Parents and patients get notified automatically when their appointment status changes.</div></div>
          </div>
          <div class="why-it reveal d4">
            <div class="why-ic"><i class="bi bi-arrow-up-right-circle-fill"></i></div>
            <div><div class="why-t">Grows With Your Clinic</div><div class="why-d">Start with patient records. Add appointments, medicines, and AI assistance as you need them. The modular permission system means you can limit what each role sees.</div></div>
          </div>
        </div>
      </div>
      <div class="reveal d2">
        <div class="why-vis">
          <div class="why-vis-h">Clinovia vs. the alternatives</div>
          <div class="cmp-row"><div class="cmp-r good">Clinovia</div><div class="cmp-ic ic-y"><i class="bi bi-check2"></i></div><div class="cmp-l">Paper logbooks</div></div>
          <div class="cmp-row"><div class="cmp-r good">Real-time search</div><div class="cmp-ic ic-y"><i class="bi bi-check2"></i></div><div class="cmp-l">Manual filing</div></div>
          <div class="cmp-row"><div class="cmp-r good">Automated SMS</div><div class="cmp-ic ic-n"><i class="bi bi-x"></i></div><div class="cmp-l">Phone calls only</div></div>
          <div class="cmp-row"><div class="cmp-r good">AI-powered help</div><div class="cmp-ic ic-n"><i class="bi bi-x"></i></div><div class="cmp-l">No assistance</div></div>
          <div class="cmp-row"><div class="cmp-r good">Expiry alerts</div><div class="cmp-ic ic-n"><i class="bi bi-x"></i></div><div class="cmp-l">Manual checking</div></div>
          <div class="cmp-row"><div class="cmp-r good">Full audit trail</div><div class="cmp-ic ic-n"><i class="bi bi-x"></i></div><div class="cmp-l">No accountability log</div></div>
          <div class="cmp-row"><div class="cmp-r good">Role-based access</div><div class="cmp-ic ic-n"><i class="bi bi-x"></i></div><div class="cmp-l">Everyone sees everything</div></div>
          <div class="why-vis-div"></div>
          <div class="why-vis-note">Purpose-built for schools by <span>@DevVee</span></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════════════
     FAQ
══════════════════════════════════════════════════════════════ -->
<section class="section bg-sl" id="faq">
  <div class="si">
    <div class="sh c reveal">
      <span class="eyebrow">FAQ</span>
      <h2 class="sec-h2">Common questions</h2>
    </div>
    <div class="faq-wrap">
      <div class="faq-it reveal">
        <button class="faq-q"><span class="faq-qt">Is the live demo safe to explore with real data?</span><span class="faq-ch"><i class="bi bi-chevron-down"></i></span></button>
        <div class="faq-ans"><div class="faq-ans-in">Yes — the demo runs on Render's free tier with an ephemeral SQLite database that resets to clean sample data on every deploy. Use the provided demo credentials freely. Never enter real patient information in the live demo; it is for evaluation only.</div></div>
      </div>
      <div class="faq-it reveal d1">
        <button class="faq-q"><span class="faq-qt">What happens to data when Render restarts the server?</span><span class="faq-ch"><i class="bi bi-chevron-down"></i></span></button>
        <div class="faq-ans"><div class="faq-ans-in">The demo uses SQLite on an ephemeral container filesystem — data resets on each deploy or container restart. For a production school deployment, Clinovia can be connected to a persistent database (PostgreSQL, MySQL) so data is permanently stored. The demo is explicitly a showcase of the UI and features.</div></div>
      </div>
      <div class="faq-it reveal d2">
        <button class="faq-q"><span class="faq-qt">How does Cobi AI protect patient privacy?</span><span class="faq-ch"><i class="bi bi-chevron-down"></i></span></button>
        <div class="faq-ans"><div class="faq-ans-in">Cobi conversations are stored privately per user account — no other user can see your chat history. Conversations are hidden from API responses and JSON serialization. The system prompt instructs Cobi never to retain or reveal patient-specific information shared during a session. For production, avoid sharing real patient data in the AI chat.</div></div>
      </div>
      <div class="faq-it reveal d3">
        <button class="faq-q"><span class="faq-qt">Can I self-host Clinovia for my school?</span><span class="faq-ch"><i class="bi bi-chevron-down"></i></span></button>
        <div class="faq-ans"><div class="faq-ans-in">Yes. Clinovia ships with a production-ready Dockerfile and render.yaml. You can deploy it to Render, Railway, Fly.io, a VPS, or any Docker-capable server. Connect your own database, set your environment variables, and it is production-ready in minutes.</div></div>
      </div>
      <div class="faq-it reveal d4">
        <button class="faq-q"><span class="faq-qt">What is the difference between the Admin and Nurse roles?</span><span class="faq-ch"><i class="bi bi-chevron-down"></i></span></button>
        <div class="faq-ans"><div class="faq-ans-in">Administrators have full access including user management, role assignment, system settings, and audit logs. Nurses have full clinical access (patients, appointments, consultations, medicines, inventory, dispensing, reports, SMS, and AI assistant) but cannot manage system users or view audit logs. Staff have limited read-only access plus appointment creation. Viewers are read-only demo accounts.</div></div>
      </div>
      <div class="faq-it reveal d5">
        <button class="faq-q"><span class="faq-qt">How do I get a Groq API key for Cobi?</span><span class="faq-ch"><i class="bi bi-chevron-down"></i></span></button>
        <div class="faq-ans"><div class="faq-ans-in">Visit console.groq.com, create a free account, and generate an API key. Paste it into your Render environment variable <code>GROQ_API_KEY</code>. Groq offers a generous free tier. Clinovia defaults to the most powerful available model (openai/gpt-oss-120b) but you can change the model in Admin → Settings.</div></div>
      </div>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════════════
     CTA
══════════════════════════════════════════════════════════════ -->
<section class="section bg-dk cta-sec">
  <div class="si">
    <div class="cta-inner reveal">
      <div class="hero-badge" style="margin:0 auto 1.5rem"><span class="badge-live"></span> Free to Explore</div>
      <h2 class="cta-h2">Start managing your clinic <span class="grad-text">smarter today</span></h2>
      <p class="cta-p">No setup fee. No credit card. Just log in with the demo credentials and experience the full platform — every module, every feature, live.</p>
      <div class="cta-btns">
        <a href="{{ route('login') }}" class="btn-pri"><i class="bi bi-box-arrow-in-right"></i> Open Live Demo</a>
        <a href="#features" class="btn-ghost"><i class="bi bi-grid-3x3-gap-fill"></i> See All Features</a>
      </div>
      <div class="cta-demo-note">Demo credentials — Admin: <strong>admin@clinovia.app / Admin@2026!</strong> &nbsp;|&nbsp; Viewer: <strong>viewer@clinovia.app / Viewer@2026!</strong></div>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════════════
     FOOTER
══════════════════════════════════════════════════════════════ -->
<footer>
  <div class="ft-inner">
    <div class="ft-top">
      <div>
        <a href="/" class="ft-brand-row">
          <div class="ft-logo"><img src="/clinovia-icon.svg" alt="Clinovia" width="22" height="22" style="display:block;border-radius:6px"></div>
          <span class="ft-name">Clinovia</span>
        </a>
        <p class="ft-tag">Smart School Clinic Management System — patient records, appointments, medicines, AI assistance, and more in one platform.</p>
        <div class="ft-bdg"><i class="bi bi-lightning-charge-fill"></i> Powered by Groq AI</div>
      </div>
      <div>
        <div class="ft-ch">Platform</div>
        <ul class="ft-links">
          <li><a href="#features">Features</a></li>
          <li><a href="#walkthrough">Walkthrough</a></li>
          <li><a href="#benefits">Benefits</a></li>
          <li><a href="#why">Why Clinovia</a></li>
        </ul>
      </div>
      <div>
        <div class="ft-ch">System</div>
        <ul class="ft-links">
          <li><a href="{{ route('login') }}">Sign In</a></li>
          <li><a href="{{ route('login') }}">Demo — Admin</a></li>
          <li><a href="{{ route('login') }}">Demo — Viewer</a></li>
          <li><a href="/health">Health Status</a></li>
        </ul>
      </div>
      <div>
        <div class="ft-ch">Built With</div>
        <ul class="ft-links">
          <li><a href="https://laravel.com" target="_blank" rel="noopener">Laravel 11</a></li>
          <li><a href="https://groq.com" target="_blank" rel="noopener">Groq API</a></li>
          <li><a href="https://render.com" target="_blank" rel="noopener">Render Hosting</a></li>
          <li><a href="https://semaphore.co" target="_blank" rel="noopener">Semaphore SMS</a></li>
        </ul>
      </div>
    </div>
    <div class="ft-bot">
      <div class="ft-copy">&copy; {{ date('Y') }} <strong>Clinovia</strong> &mdash; Designed &amp; built by <strong>Prince Arvee Avena</strong></div>
      <div class="ft-bot-links">
        <a href="#home">Back to top &uarr;</a>
        <a href="/health">System Status</a>
        <a href="{{ route('login') }}">Sign In</a>
      </div>
    </div>
  </div>
</footer>

<script>
(function(){
  'use strict';

  /* Navbar scroll solidify */
  var nav = document.getElementById('navbar');
  window.addEventListener('scroll', function(){
    nav.classList.toggle('solid', window.scrollY > 40);
  }, { passive: true });

  /* Mobile drawer */
  document.getElementById('navMobBtn').addEventListener('click', function(){
    document.getElementById('mobDrawer').classList.add('open');
  });
  document.getElementById('mobClose').addEventListener('click', function(){
    document.getElementById('mobDrawer').classList.remove('open');
  });
  window.closeMob = function(){ document.getElementById('mobDrawer').classList.remove('open'); };

  /* Walkthrough tabs */
  document.querySelectorAll('.wt-tab').forEach(function(btn){
    btn.addEventListener('click', function(){
      document.querySelectorAll('.wt-tab').forEach(function(b){ b.classList.remove('on'); });
      document.querySelectorAll('.wt-panel').forEach(function(p){ p.classList.remove('on'); });
      btn.classList.add('on');
      var panel = document.getElementById(btn.dataset.tab);
      if(panel) panel.classList.add('on');
    });
  });

  /* FAQ accordion */
  document.querySelectorAll('.faq-q').forEach(function(btn){
    btn.addEventListener('click', function(){
      var item = btn.closest('.faq-it');
      var isOpen = item.classList.contains('open');
      document.querySelectorAll('.faq-it.open').forEach(function(o){ o.classList.remove('open'); });
      if(!isOpen) item.classList.add('open');
    });
  });

  /* Scroll reveal with IntersectionObserver */
  if('IntersectionObserver' in window){
    var io = new IntersectionObserver(function(entries){
      entries.forEach(function(e){
        if(e.isIntersecting){ e.target.classList.add('visible'); io.unobserve(e.target); }
      });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(function(el){ io.observe(el); });
  } else {
    document.querySelectorAll('.reveal').forEach(function(el){ el.classList.add('visible'); });
  }

  /* Smooth scroll for anchor links */
  document.querySelectorAll('a[href^="#"]').forEach(function(a){
    a.addEventListener('click', function(e){
      var id = a.getAttribute('href').slice(1);
      var el = document.getElementById(id);
      if(el){ e.preventDefault(); el.scrollIntoView({ behavior:'smooth', block:'start' }); }
    });
  });

})();
</script>
</body>
</html>

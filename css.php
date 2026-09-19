<?php
header("Content-type: text/css; charset: UTF-8");
?>
:root { --gold-primary: #D4AF37; --gold-light: #FFD700; --bg-dark: #0a0a0a; }
* { margin: 0; padding: 0; box-sizing: border-box; }
body { background-color: var(--bg-dark); background-image: radial-gradient(circle at 50% 50%, #1a1a1a 0%, #000 100%); color: #fff; font-family: 'Poppins', sans-serif; min-height: 100vh; font-size: 13px; }
.premium-font { font-family: 'Cinzel', serif; }
.gold-text { background: linear-gradient(to bottom, #FFD700 0%, #D4AF37 50%, #B8860B 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
.gold-border { border: 1px solid var(--gold-primary); box-shadow: 0 0 10px rgba(212, 175, 55, 0.1); }
.gold-button { background: linear-gradient(45deg, #B8860B, #D4AF37, #FFD700); transition: all 0.2s ease; color: #000; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; border: none; cursor: pointer; }
.gold-button:hover { transform: translateY(-1px); box-shadow: 0 3px 15px rgba(212, 175, 55, 0.4); filter: brightness(1.1); }
.card { background: rgba(20, 20, 20, 0.8); backdrop-filter: blur(10px); border: 1px solid rgba(212, 175, 55, 0.2); border-radius: 10px; }
.stat-box { background: rgba(212, 175, 55, 0.05); border: 1px solid rgba(212, 175, 55, 0.3); border-radius: 8px; padding: 6px; text-align: center; }
.stat-number { font-size: 14px; font-weight: bold; color: #FFD700; }
.stat-label { font-size: 8px; color: #D4AF37; text-transform: uppercase; letter-spacing: 1px; }
.action-button { background: rgba(212, 175, 55, 0.1); border: 1px solid var(--gold-primary); color: #FFD700; padding: 3px 8px; border-radius: 4px; cursor: pointer; transition: all 0.2s ease; font-size: 10px; font-weight: 600; min-width: 80px; }
.action-button:hover { background: rgba(212, 175, 55, 0.3); }
.copy-success { position: fixed; top: 20px; right: 20px; background: #4CAF50; color: white; padding: 8px 12px; border-radius: 4px; animation: slideIn 0.3s ease; z-index: 1000; font-size: 11px; transition: opacity 0.5s ease; }
@keyframes slideIn { from { transform: translateX(400px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
.result-item { border-bottom: 1px solid rgba(212, 175, 55, 0.1); padding: 10px 0; }
.result-item:last-child { border-bottom: none; }
.label-small { font-size: 9px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 1px; }
.value-small { font-size: 12px; color: #fff; font-weight: 500; }
input::placeholder { font-size: 11px; color: rgba(255,255,255,0.3); }
input, textarea { background: rgba(0, 0, 0, 0.4); border: 1px solid rgba(212, 175, 55, 0.2); color: #fff; padding: 8px 12px; border-radius: 6px; font-family: inherit; }
input:focus, textarea:focus { outline: none; border-color: var(--gold-light); box-shadow: 0 0 10px rgba(255, 215, 0, 0.2); }
.error { color: #FF6B6B; font-size: 10px; margin-top: 8px; }
.success { color: #4CAF50; font-size: 10px; margin-top: 8px; }
.grid { display: grid; }
.grid-cols-1 { grid-template-columns: 1fr; }
.grid-cols-2 { grid-template-columns: repeat(2, 1fr); }
.grid-cols-3 { grid-template-columns: repeat(3, 1fr); }
.gap-2 { gap: 8px; }
.gap-3 { gap: 12px; }
.mb-2 { margin-bottom: 8px; }
.mb-3 { margin-bottom: 12px; }
.mb-4 { margin-bottom: 16px; }
.mt-1 { margin-top: 4px; }
.mt-2 { margin-top: 8px; }
.mt-4 { margin-top: 16px; }
.mt-6 { margin-top: 24px; }
.mt-8 { margin-top: 32px; }
.p-2 { padding: 8px; }
.p-3 { padding: 12px; }
.p-4 { padding: 16px; }
.px-3 { padding-left: 12px; padding-right: 12px; }
.py-1 { padding-top: 4px; padding-bottom: 4px; }
.py-2 { padding-top: 8px; padding-bottom: 8px; }
.py-3 { padding-top: 12px; padding-bottom: 12px; }
.py-4 { padding-top: 16px; padding-bottom: 16px; }
.text-center { text-align: center; }
.text-sm { font-size: 12px; }
.text-xs { font-size: 10px; }
.font-bold { font-weight: bold; }
.text-gray-300 { color: #d1d5db; }
.text-gray-400 { color: #9ca3af; }
.text-gray-500 { color: #6b7280; }
.text-gray-600 { color: #4b5563; }
.text-gold-light { color: #FFD700; }
.text-green-400 { color: #4ade80; }
.text-green-500 { color: #22c55e; }
.text-red-500 { color: #ef4444; }
.bg-green-600\/10 { background-color: rgba(22, 163, 74, 0.1); }
.border-green-600\/20 { border-color: rgba(22, 163, 74, 0.2); }
.hover\:bg-green-600\/20:hover { background-color: rgba(22, 163, 74, 0.2); }
.flex { display: flex; }
.flex-col { flex-direction: column; }
.flex-row { flex-direction: row; }
.items-center { align-items: center; }
.justify-center { justify-content: center; }
.justify-between { justify-content: space-between; }
.justify-end { justify-content: flex-end; }
.rounded-full { border-radius: 9999px; }
.rounded-md { border-radius: 6px; }
.rounded-lg { border-radius: 8px; }
.max-w-2xl { max-width: 672px; }
.max-w-sm { max-width: 384px; }
.mx-auto { margin-left: auto; margin-right: auto; }
.w-3 { width: 12px; }
.w-12 { width: 48px; }
.h-0\.5 { height: 2px; }
.h-3 { height: 12px; }
.tracking-widest { letter-spacing: 0.1em; }
@media (max-width: 640px) {
    .sm\:flex-row { flex-direction: column; }
    .sm\:col-span-2 { grid-column: span 2; }
    .md\:p-4 { padding: 12px; }
    .md\:text-3xl { font-size: 24px; }
    .text-2xl { font-size: 20px; }
}
.hidden { display: none; }
.text-\[10px\] { font-size: 10px; }
.tracking-tighter { letter-spacing: -0.05em; }
.h-1\.5 { height: 6px; }
.bg-black\/40 { background-color: rgba(0, 0, 0, 0.4); }
.rounded-full { border-radius: 9999px; }
.shadow-\[0_0_10px_rgba\(212\,175\,55\,0\.5\)\] { box-shadow: 0 0 10px rgba(212, 175, 55, 0.5); }
.duration-300 { transition-duration: 300ms; }
@keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
.animate-pulse { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
@media (min-width: 641px) {
    .sm\:flex-row { flex-direction: row; }
    .sm\:col-span-2 { grid-column: span 2; }
    .md\:p-4 { padding: 16px; }
    .md\:text-3xl { font-size: 30px; }
}

@extends('layouts.app')

@section('title', 'Cobi — AI Assistant')

@push('styles')
<style>
/* ── Cobi layout ─────────────────────────────────────────── */
.cobi-layout {
    display: grid;
    grid-template-columns: 280px 1fr;
    height: calc(100vh - var(--navbar-total) - 1.5rem - 1.5rem - 48px); /* subtract content-area padding + footer */
    gap: 1rem;
    min-height: 500px;
}

/* ── History sidebar ─────────────────────────────────────── */
.cobi-history {
    display: flex;
    flex-direction: column;
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 14px;
    overflow: hidden;
}
.cobi-history-header {
    padding: 1rem 1.1rem;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}
.cobi-history-list {
    flex: 1;
    overflow-y: auto;
    padding: .5rem;
}
.cobi-history-item {
    border-radius: 10px;
    padding: .55rem .85rem;
    cursor: pointer;
    font-size: .8rem;
    color: var(--text);
    transition: background .15s;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.cobi-history-item:hover { background: hsl(201,85%,95%); color: var(--primary); }
.cobi-history-item .hi-time { font-size: .68rem; color: var(--text-muted); }

/* ── Main chat panel ─────────────────────────────────────── */
.cobi-main {
    display: flex;
    flex-direction: column;
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,.06);
}
.cobi-chat-header {
    padding: .85rem 1.25rem;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: .85rem;
    flex-shrink: 0;
    background: linear-gradient(135deg, hsla(201,85%,39%,.04), hsla(265,58%,54%,.04));
}
.cobi-avatar-lg {
    width: 44px; height: 44px;
    border-radius: 12px;
    background: linear-gradient(135deg, hsl(201,85%,39%), hsl(265,58%,54%));
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 1.25rem;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(110,60,200,.35);
}
.cobi-status-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    background: hsl(144,100%,39%);
    box-shadow: 0 0 6px hsl(144,100%,39%);
    animation: pulse-dot 2s infinite;
}

/* ── Messages ────────────────────────────────────────────── */
.cobi-messages {
    flex: 1;
    overflow-y: auto;
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    scroll-behavior: smooth;
}
.cobi-messages::-webkit-scrollbar { width: 4px; }
.cobi-messages::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }

.msg-row { display: flex; align-items: flex-end; gap: .65rem; }
.msg-row.user  { flex-direction: row-reverse; }
.msg-row.cobi  { flex-direction: row; }

.msg-avatar {
    width: 30px; height: 30px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: .75rem;
    color: #fff;
    flex-shrink: 0;
    font-weight: 700;
}
.msg-avatar.av-cobi { background: linear-gradient(135deg, hsl(201,85%,39%), hsl(265,58%,54%)); }
.msg-avatar.av-user { background: var(--gradient-primary); }

.msg-bubble {
    max-width: 84%;
    min-width: 120px;
    padding: .75rem 1.1rem;
    border-radius: 16px;
    font-size: .875rem;
    line-height: 1.6;
    word-break: break-word;
    position: relative;
    animation: msg-in .2s ease-out;
}
@keyframes msg-in {
    from { opacity: 0; transform: translateY(6px); }
    to   { opacity: 1; transform: translateY(0); }
}
.msg-row.user .msg-bubble {
    background: var(--gradient-primary);
    color: #fff;
    border-bottom-right-radius: 4px;
}
.msg-row.cobi .msg-bubble {
    background: hsl(210,17%,97%);
    color: var(--text);
    border: 1px solid var(--border);
    border-bottom-left-radius: 4px;
}
.msg-meta {
    font-size: .68rem;
    color: var(--text-muted);
    margin-top: .3rem;
    padding: 0 .5rem;
}
.msg-row.user .msg-meta { text-align: right; }

/* Markdown-like rendering in Cobi bubbles */
.msg-row.cobi .msg-bubble strong { font-weight: 700; }
.msg-row.cobi .msg-bubble code {
    background: rgba(0,0,0,.07);
    padding: .1rem .35rem;
    border-radius: 4px;
    font-size: .82rem;
    font-family: 'Courier New', monospace;
}
.msg-row.cobi .msg-bubble pre {
    background: rgba(0,0,0,.07);
    padding: .75rem 1rem;
    border-radius: 8px;
    overflow-x: auto;
    font-size: .8rem;
    margin: .5rem 0 0;
}
.msg-row.cobi .msg-bubble ul, .msg-row.cobi .msg-bubble ol {
    padding-left: 1.25rem;
    margin: .4rem 0 0;
}
.msg-row.cobi .msg-bubble li { margin-bottom: .2rem; }

/* ── Typing indicator ────────────────────────────────────── */
.typing-bubble {
    background: hsl(210,17%,97%);
    border: 1px solid var(--border);
    border-radius: 16px;
    border-bottom-left-radius: 4px;
    padding: .75rem 1rem;
    display: flex;
    gap: .35rem;
    align-items: center;
}
.typing-dot {
    width: 7px; height: 7px;
    border-radius: 50%;
    background: var(--text-muted);
    animation: typing-bounce .9s infinite;
}
.typing-dot:nth-child(2) { animation-delay: .15s; }
.typing-dot:nth-child(3) { animation-delay: .3s; }
@keyframes typing-bounce {
    0%,60%,100% { transform: translateY(0); }
    30% { transform: translateY(-5px); }
}

/* ── Input area ──────────────────────────────────────────── */
.cobi-input-area {
    padding: 1rem 1.25rem;
    border-top: 1px solid var(--border);
    background: var(--card-bg);
    flex-shrink: 0;
}
.cobi-input-wrap {
    display: flex;
    gap: .65rem;
    align-items: flex-end;
    background: hsl(210,17%,97%);
    border: 1.5px solid var(--border);
    border-radius: 14px;
    padding: .65rem .75rem;
    transition: border-color .2s, box-shadow .2s;
}
.cobi-input-wrap:focus-within {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(15,115,186,.1);
}
.cobi-textarea {
    flex: 1;
    border: none;
    background: transparent;
    resize: none;
    outline: none;
    font-size: .875rem;
    color: var(--text);
    line-height: 1.5;
    max-height: 120px;
    overflow-y: auto;
    font-family: inherit;
}
.cobi-textarea::placeholder { color: var(--text-muted); }
.cobi-send-btn {
    width: 36px; height: 36px;
    border-radius: 10px;
    background: var(--gradient-primary);
    border: none;
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: .9rem;
    cursor: pointer;
    transition: opacity .15s, transform .15s;
    flex-shrink: 0;
}
.cobi-send-btn:hover { opacity: .88; transform: scale(1.05); }
.cobi-send-btn:disabled { opacity: .4; cursor: not-allowed; transform: none; }

/* ── Quick prompts ───────────────────────────────────────── */
.cobi-quick {
    display: flex;
    flex-wrap: wrap;
    gap: .4rem;
    margin-top: .65rem;
}
.cobi-quick-btn {
    font-size: .75rem;
    padding: .3rem .75rem;
    border-radius: 20px;
    border: 1px solid var(--border);
    background: var(--card-bg);
    color: var(--text-muted);
    cursor: pointer;
    transition: all .15s;
}
.cobi-quick-btn:hover { border-color: var(--primary); color: var(--primary); background: hsla(201,85%,39%,.06); }

/* ── Empty state ─────────────────────────────────────────── */
.cobi-empty {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    color: var(--text-muted);
    padding: 2rem;
    text-align: center;
}
.cobi-empty-icon {
    width: 72px; height: 72px;
    border-radius: 20px;
    background: linear-gradient(135deg, hsl(201,85%,39%), hsl(265,58%,54%));
    display: flex; align-items: center; justify-content: center;
    font-size: 2rem; color: #fff;
    box-shadow: 0 8px 24px rgba(110,60,200,.3);
}

/* ── Mobile responsive ───────────────────────────────────── */
@media (max-width: 991px) {
    .cobi-layout { grid-template-columns: 1fr; }
    .cobi-history { display: none; }
    .cobi-layout.show-history .cobi-history { display: flex; position: fixed; inset: 0; z-index: 9999; border-radius: 0; }
}
</style>
@endpush

@section('content')

{{-- Page header --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex align-items-center gap-3">
        <div class="cobi-avatar-lg" style="width:40px;height:40px;border-radius:10px;font-size:1.1rem;">
            <i class="bi bi-stars"></i>
        </div>
        <div>
            <h4 class="fw-bold mb-0">
                Cobi
                <span class="badge text-bg-warning ms-1" style="font-size:.6rem;vertical-align:middle;">AI</span>
            </h4>
            <p class="text-muted small mb-0">Powered by Groq — Clinovia AI assistant</p>
        </div>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary btn-sm d-lg-none" id="historyToggle">
            <i class="bi bi-clock-history me-1"></i> History
        </button>
        <button class="btn btn-outline-danger btn-sm" id="clearBtn">
            <i class="bi bi-trash me-1"></i> Clear Chat
        </button>
    </div>
</div>

<div class="cobi-layout" id="cobiLayout">

    {{-- ── History sidebar ──────────────────────────────────────── --}}
    <div class="cobi-history" id="cobiHistory">
        <div class="cobi-history-header">
            <span class="fw-semibold small">
                <i class="bi bi-clock-history text-muted me-1"></i>Recent Chats
            </span>
            <span class="badge bg-secondary-subtle text-secondary-emphasis">{{ $conversations->count() }}</span>
        </div>
        <div class="cobi-history-list" id="historyList">
            @forelse($conversations as $convo)
            <div class="cobi-history-item" title="{{ $convo->message }}">
                <div>{{ Str::limit($convo->message, 42) }}</div>
                <div class="hi-time">{{ $convo->created_at->diffForHumans() }}</div>
            </div>
            @empty
            <div class="text-center text-muted small py-4">
                <i class="bi bi-chat-square d-block fs-3 opacity-30 mb-2"></i>
                No conversations yet
            </div>
            @endforelse
        </div>
    </div>

    {{-- ── Main chat panel ───────────────────────────────────────── --}}
    <div class="cobi-main">

        {{-- Chat header --}}
        <div class="cobi-chat-header">
            <div class="cobi-avatar-lg">
                <i class="bi bi-stars"></i>
            </div>
            <div class="flex-grow-1">
                <div class="fw-semibold">Cobi</div>
                <div class="d-flex align-items-center gap-2">
                    <span class="cobi-status-dot"></span>
                    <span class="text-muted" style="font-size:.73rem;">Online &bull; Groq API &bull; gpt-oss-120b</span>
                </div>
            </div>
            <div class="text-muted small d-none d-md-block">
                <i class="bi bi-shield-check text-success me-1"></i>Conversations are private
            </div>
        </div>

        {{-- Messages --}}
        <div class="cobi-messages" id="chatMessages">

            {{-- Empty / Welcome state --}}
            @if($conversations->isEmpty())
            <div class="cobi-empty" id="emptyState">
                <div class="cobi-empty-icon">
                    <i class="bi bi-stars"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1">Hi, I'm Cobi! 👋</h5>
                    <p class="mb-0 small">I'm your Clinovia AI assistant. Ask me anything about the system, clinic workflows, or general medical questions.</p>
                </div>
            </div>
            @else
            {{-- Render conversation history --}}
            @foreach($conversations->sortBy('created_at') as $convo)
            <div class="msg-row user">
                <div class="msg-avatar av-user">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div>
                    <div class="msg-bubble">{{ $convo->message }}</div>
                    <div class="msg-meta">{{ $convo->created_at->format('h:i A') }}</div>
                </div>
            </div>
            <div class="msg-row cobi">
                <div class="msg-avatar av-cobi"><i class="bi bi-stars" style="font-size:.7rem;"></i></div>
                <div>
                    <div class="msg-bubble" data-markdown>{{ $convo->response }}</div>
                    <div class="msg-meta">Cobi &bull; {{ $convo->created_at->format('h:i A') }}</div>
                </div>
            </div>
            @endforeach
            @endif

        </div>

        {{-- Input area --}}
        <div class="cobi-input-area">
            <div class="cobi-input-wrap">
                <textarea id="cobiInput"
                          class="cobi-textarea"
                          placeholder="Ask Cobi anything — clinic system, technical issues, medical terms, research…"
                          rows="1"
                          maxlength="4000"
                          autocomplete="off"></textarea>
                <button class="cobi-send-btn" id="sendBtn" disabled>
                    <i class="bi bi-send-fill" id="sendIcon"></i>
                </button>
            </div>
            <div class="cobi-quick mt-2">
                @foreach([
                    'How do I add a new patient?',
                    'How to approve an appointment?',
                    'How to dispense medicine?',
                    'What reports are available?',
                    'Explain a common medical term',
                    'Help me troubleshoot a technical issue',
                    'How do SMS notifications work?',
                    'Explain the audit log',
                ] as $prompt)
                <button class="cobi-quick-btn" data-prompt="{{ $prompt }}">{{ $prompt }}</button>
                @endforeach
            </div>
            <div class="d-flex justify-content-between mt-2">
                <span class="text-muted" style="font-size:.68rem;">
                    <i class="bi bi-lightning-charge-fill text-warning me-1"></i>Powered by Groq &bull; gpt-oss-120b
                </span>
                <span class="text-muted" style="font-size:.68rem;" id="charCount">0/4000</span>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
const chatMessages = document.getElementById('chatMessages');
const cobiInput    = document.getElementById('cobiInput');
const sendBtn      = document.getElementById('sendBtn');
const sendIcon     = document.getElementById('sendIcon');
const csrfToken    = document.querySelector('meta[name="csrf-token"]').content;
const userInitial  = '{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}';

// ── Auto-resize textarea ───────────────────────────────────
cobiInput.addEventListener('input', function () {
    this.style.height = 'auto';
    this.style.height = Math.min(this.scrollHeight, 120) + 'px';
    sendBtn.disabled  = !this.value.trim();
    document.getElementById('charCount').textContent = `${this.value.length}/4000`;
});

// ── Send on Enter (Shift+Enter = newline) ──────────────────
cobiInput.addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        if (!sendBtn.disabled) sendMessage(this.value.trim());
    }
});

// ── Scroll to bottom ───────────────────────────────────────
function scrollBottom() {
    chatMessages.scrollTo({ top: chatMessages.scrollHeight, behavior: 'smooth' });
}
scrollBottom();

// ── Simple markdown renderer ───────────────────────────────
function renderMarkdown(text) {
    return text
        // Code blocks
        .replace(/```([\s\S]*?)```/g, '<pre><code>$1</code></pre>')
        // Inline code
        .replace(/`([^`]+)`/g, '<code>$1</code>')
        // Bold
        .replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>')
        // Italic
        .replace(/\*([^*]+)\*/g, '<em>$1</em>')
        // Headers
        .replace(/^### (.+)$/gm, '<strong style="font-size:.95em;">$1</strong>')
        .replace(/^## (.+)$/gm, '<strong>$1</strong>')
        // Numbered lists
        .replace(/^\d+\. (.+)$/gm, '<li>$1</li>')
        // Bullet lists
        .replace(/^[-•] (.+)$/gm, '<li>$1</li>')
        // Wrap consecutive <li> in <ul>
        .replace(/(<li>.*<\/li>\n?)+/g, m => `<ul>${m}</ul>`)
        // Line breaks
        .replace(/\n\n/g, '</p><p>')
        .replace(/\n/g, '<br>');
}

// Apply markdown to all existing cobi bubbles
document.querySelectorAll('.msg-bubble[data-markdown]').forEach(el => {
    el.innerHTML = renderMarkdown(el.textContent);
    el.removeAttribute('data-markdown');
});

// ── Append message helper ──────────────────────────────────
function appendMessage(role, content, time) {
    const emptyState = document.getElementById('emptyState');
    if (emptyState) emptyState.remove();

    const row = document.createElement('div');
    row.className = `msg-row ${role}`;

    if (role === 'user') {
        row.innerHTML = `
            <div class="msg-avatar av-user">${userInitial}</div>
            <div>
                <div class="msg-bubble">${escapeHtml(content)}</div>
                <div class="msg-meta">${time}</div>
            </div>`;
    } else {
        row.innerHTML = `
            <div class="msg-avatar av-cobi"><i class="bi bi-stars" style="font-size:.7rem;"></i></div>
            <div>
                <div class="msg-bubble">${renderMarkdown(content)}</div>
                <div class="msg-meta">Cobi &bull; ${time}</div>
            </div>`;
    }

    chatMessages.appendChild(row);
    scrollBottom();
    return row;
}

function escapeHtml(text) {
    return text.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

// ── Typing indicator ───────────────────────────────────────
function showTyping() {
    const row = document.createElement('div');
    row.className = 'msg-row cobi';
    row.id = 'typingRow';
    row.innerHTML = `
        <div class="msg-avatar av-cobi"><i class="bi bi-stars" style="font-size:.7rem;"></i></div>
        <div class="typing-bubble">
            <span class="typing-dot"></span>
            <span class="typing-dot"></span>
            <span class="typing-dot"></span>
        </div>`;
    chatMessages.appendChild(row);
    scrollBottom();
}

function hideTyping() {
    const t = document.getElementById('typingRow');
    if (t) t.remove();
}

// ── Send message ───────────────────────────────────────────
async function sendMessage(text) {
    if (!text) return;

    cobiInput.value    = '';
    cobiInput.style.height = 'auto';
    sendBtn.disabled   = true;
    sendIcon.className = 'bi bi-hourglass-split';

    const now = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
    appendMessage('user', text, now);
    showTyping();

    try {
        const res = await fetch('{{ route("ai-assistant.chat") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept':       'application/json',
            },
            body: JSON.stringify({ message: text }),
        });

        const data = await res.json();
        hideTyping();

        if (res.ok) {
            appendMessage('cobi', data.response, now);
        } else {
            appendMessage('cobi', '⚠️ ' + (data.message || 'Something went wrong.'), now);
        }
    } catch (err) {
        hideTyping();
        appendMessage('cobi', '⚠️ Could not reach the AI service. Check your connection.', now);
    } finally {
        sendBtn.disabled   = false;
        sendIcon.className = 'bi bi-send-fill';
        cobiInput.focus();
    }
}

// ── Form submit ────────────────────────────────────────────
sendBtn.addEventListener('click', () => sendMessage(cobiInput.value.trim()));

// ── Quick prompts ──────────────────────────────────────────
document.querySelectorAll('.cobi-quick-btn').forEach(btn => {
    btn.addEventListener('click', () => sendMessage(btn.dataset.prompt));
});

// ── Clear history ──────────────────────────────────────────
document.getElementById('clearBtn').addEventListener('click', async function () {
    if (!confirm('Clear all conversation history?')) return;
    await fetch('{{ route("ai-assistant.clear") }}', {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
    });
    chatMessages.innerHTML = `
        <div class="cobi-empty" id="emptyState">
            <div class="cobi-empty-icon"><i class="bi bi-stars"></i></div>
            <div><h5 class="fw-bold mb-1">Chat cleared!</h5><p class="mb-0 small">Start a new conversation.</p></div>
        </div>`;
    document.getElementById('historyList').innerHTML = `
        <div class="text-center text-muted small py-4"><i class="bi bi-chat-square d-block fs-3 opacity-30 mb-2"></i>No conversations yet</div>`;
});

// ── Mobile history toggle ──────────────────────────────────
const historyToggle = document.getElementById('historyToggle');
if (historyToggle) {
    historyToggle.addEventListener('click', () => {
        document.getElementById('cobiLayout').classList.toggle('show-history');
    });
}
</script>
@endpush

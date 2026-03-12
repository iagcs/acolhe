import { useState, useEffect, useRef } from "react";

// ── Aesthetic: Warm clinical elegance — soft cream/sage palette, editorial typography,
// ── generous whitespace, subtle grain texture. Feels like a well-designed therapy office.

const LP = () => {
    const [plan, setPlan] = useState("solo");
    const [email, setEmail] = useState("");
    const [faqOpen, setFaqOpen] = useState(null);
    const [scrollY, setScrollY] = useState(0);
    const [visible, setVisible] = useState({});
    const refs = useRef({});

    useEffect(() => {
        const onScroll = () => setScrollY(window.scrollY);
        window.addEventListener("scroll", onScroll, { passive: true });
        return () => window.removeEventListener("scroll", onScroll);
    }, []);

    useEffect(() => {
        const obs = new IntersectionObserver(
            entries => entries.forEach(e => {
                if (e.isIntersecting) setVisible(p => ({ ...p, [e.target.id]: true }));
            }),
            { threshold: 0.15 }
        );
        document.querySelectorAll("[data-anim]").forEach(el => obs.observe(el));
        return () => obs.disconnect();
    }, []);

    const plans = {
        solo: { name: "Solo", price: "69", desc: "Para psicólogos autônomos", features: ["1 profissional", "Agenda inteligente", "Lembretes WhatsApp ilimitados", "Link de agendamento", "Até 40 pacientes ativos", "Notas criptografadas"] },
        pro: { name: "Profissional", price: "99", desc: "Para quem quer o diferencial da IA", popular: true, features: ["Tudo do Solo +", "Pacientes ilimitados", "IA de triagem pré-consulta", "Configuração por abordagem", "Documentos e anexos", "Relatório de faltas mensal"] },
        clinic: { name: "Consultório", price: "179", desc: "Para 2 a 5 profissionais", features: ["Tudo do Profissional +", "Até 5 psicólogos", "Painel administrativo", "Relatórios consolidados", "Agenda compartilhada", "Suporte prioritário"] },
    };

    const faqs = [
        { q: "Preciso instalar alguma coisa?", a: "Não. O PsiAgenda funciona 100% no navegador, no celular ou no computador. Sem instalação, sem app." },
        { q: "Como funciona a integração com WhatsApp?", a: "Conectamos direto ao WhatsApp do seu consultório. Os lembretes são enviados automaticamente com seu nome, no tom humanizado que você configurar. Leva menos de 5 minutos para configurar." },
        { q: "A IA de triagem substitui o psicólogo?", a: "De forma alguma. A IA apenas coleta informações iniciais antes da primeira sessão — como uma anamnese guiada. Ela usa o vocabulário da sua abordagem (TCC, Psicanálise, Gestalt, etc.) e o paciente sempre sabe que está falando com uma assistente virtual. Você recebe o prontuário pronto para ler antes de atender." },
        { q: "É seguro? E a LGPD?", a: "Todos os dados são criptografados. Notas privadas usam criptografia AES-256. Seguimos a LGPD com consentimento explícito, direito de exclusão e exportação de dados. Nenhum dado é compartilhado." },
        { q: "Posso cancelar quando quiser?", a: "Sim, sem multa e sem burocracia. É mensal, cancela quando quiser. Seus dados ficam disponíveis para exportação por 90 dias após o cancelamento." },
        { q: "Funciona para consultório com vários psicólogos?", a: "Sim! O plano Consultório permite até 5 profissionais com agendas independentes e painel administrativo compartilhado." },
    ];

    const testimonials = [
        { name: "Dra. Camila R.", crp: "CRP 06", text: "Reduzi minhas faltas em 70% no primeiro mês. Só os lembretes de WhatsApp já pagam o sistema.", approach: "TCC" },
        { name: "Dr. Marcos L.", crp: "CRP 04", text: "A triagem por IA mudou meu atendimento. Chego na primeira sessão já sabendo o contexto. Ganho 20 minutos.", approach: "Psicanálise" },
        { name: "Dra. Ana Paula S.", crp: "CRP 05", text: "Finalmente um sistema que não parece prontuário de hospital. Simples, bonito e funciona.", approach: "Gestalt" },
    ];

    const S = (id) => visible[id] ? "anim-in" : "anim-out";

    return (
        <div style={{ fontFamily: "'Source Serif 4', Georgia, serif", color: "#1C1917", background: "#FAFAF7" }}>
            <link href="https://fonts.googleapis.com/css2?family=Source+Serif+4:opsz,wght@8..60,400;8..60,600;8..60,700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet" />
            <style>{`
        * { box-sizing: border-box; margin: 0; padding: 0; }
        .ff { font-family: 'Outfit', system-ui, sans-serif; }
        .anim-out { opacity: 0; transform: translateY(28px); }
        .anim-in { opacity: 1; transform: translateY(0); transition: opacity 0.7s cubic-bezier(0.16,1,0.3,1), transform 0.7s cubic-bezier(0.16,1,0.3,1); }
        .d1 { transition-delay: 0.1s !important; }
        .d2 { transition-delay: 0.2s !important; }
        .d3 { transition-delay: 0.3s !important; }
        .d4 { transition-delay: 0.4s !important; }
        .grain { position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 999; opacity: 0.025; background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E"); }
        .btn-primary { background: #2D6A4F; color: #fff; border: none; padding: 14px 32px; border-radius: 10px; font-size: 16px; font-weight: 600; cursor: pointer; font-family: 'Outfit', sans-serif; transition: all 0.2s; }
        .btn-primary:hover { background: #1B4332; transform: translateY(-1px); box-shadow: 0 4px 16px rgba(45,106,79,0.25); }
        .btn-secondary { background: transparent; color: #2D6A4F; border: 2px solid #2D6A4F; padding: 12px 28px; border-radius: 10px; font-size: 15px; font-weight: 600; cursor: pointer; font-family: 'Outfit', sans-serif; transition: all 0.2s; }
        .btn-secondary:hover { background: #2D6A4F; color: #fff; }
        a { color: #2D6A4F; text-decoration: none; }
        .card-hover { transition: transform 0.25s, box-shadow 0.25s; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(0,0,0,0.08); }
        @keyframes float { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-8px); } }
        @keyframes pulse { 0%,100% { opacity: 1; } 50% { opacity: 0.6; } }
        .nav-blur { backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); }
      `}</style>
            <div className="grain" />

            {/* ── NAV ── */}
            <nav className="ff nav-blur" style={{
                position: "fixed", top: 0, left: 0, right: 0, zIndex: 100, padding: "0 40px",
                height: 64, display: "flex", alignItems: "center", justifyContent: "space-between",
                background: scrollY > 50 ? "rgba(250,250,247,0.9)" : "transparent",
                borderBottom: scrollY > 50 ? "1px solid rgba(0,0,0,0.06)" : "none",
                transition: "all 0.3s",
            }}>
                <div style={{ display: "flex", alignItems: "center", gap: 10 }}>
                    <div style={{ width: 32, height: 32, borderRadius: 8, background: "#2D6A4F", display: "flex", alignItems: "center", justifyContent: "center", color: "#fff", fontWeight: 700, fontSize: 16 }}>P</div>
                    <span style={{ fontWeight: 700, fontSize: 18, letterSpacing: "-0.02em", color: "#1C1917" }}>PsiAgenda</span>
                </div>
                <div style={{ display: "flex", alignItems: "center", gap: 32 }}>
                    {["Funcionalidades", "Preços", "FAQ"].map(l => (
                        <a key={l} href={`#${l.toLowerCase()}`} className="ff" style={{ fontSize: 14, fontWeight: 500, color: "#57534E" }}>{l}</a>
                    ))}
                    <button className="btn-primary ff" style={{ padding: "10px 24px", fontSize: 14 }}>Começar grátis</button>
                </div>
            </nav>

            {/* ── HERO ── */}
            <section style={{
                minHeight: "100vh", display: "flex", alignItems: "center", justifyContent: "center",
                padding: "120px 40px 80px", position: "relative", overflow: "hidden",
            }}>
                {/* Background orbs */}
                <div style={{ position: "absolute", top: "10%", right: "5%", width: 400, height: 400, borderRadius: "50%", background: "radial-gradient(circle, rgba(45,106,79,0.06) 0%, transparent 70%)", animation: "float 8s ease-in-out infinite" }} />
                <div style={{ position: "absolute", bottom: "15%", left: "8%", width: 300, height: 300, borderRadius: "50%", background: "radial-gradient(circle, rgba(180,83,9,0.04) 0%, transparent 70%)", animation: "float 6s ease-in-out infinite 1s" }} />

                <div style={{ maxWidth: 1100, width: "100%", display: "flex", alignItems: "center", gap: 60 }}>
                    <div style={{ flex: "1 1 55%" }}>
                        <div data-anim id="hero-badge" className={`ff ${S("hero-badge")}`} style={{
                            display: "inline-flex", alignItems: "center", gap: 8, padding: "6px 16px",
                            borderRadius: 20, background: "#F0FDF4", border: "1px solid #BBF7D0", marginBottom: 24,
                        }}>
                            <span style={{ width: 8, height: 8, borderRadius: "50%", background: "#22C55E", animation: "pulse 2s ease infinite" }} />
                            <span style={{ fontSize: 13, fontWeight: 600, color: "#166534" }}>Vagas limitadas para beta</span>
                        </div>

                        <h1 data-anim id="hero-h1" className={`${S("hero-h1")} d1`} style={{
                            fontSize: 52, fontWeight: 700, lineHeight: 1.12, letterSpacing: "-0.03em",
                            marginBottom: 20, color: "#1C1917",
                        }}>
                            Seus pacientes <span style={{ color: "#2D6A4F" }}>param de faltar.</span><br />
                            Você para de se preocupar.
                        </h1>

                        <p data-anim id="hero-p" className={`ff ${S("hero-p")} d2`} style={{
                            fontSize: 18, lineHeight: 1.65, color: "#57534E", maxWidth: 520, marginBottom: 36,
                        }}>
                            O PsiAgenda cuida da sua agenda, envia lembretes humanizados pelo WhatsApp e — com IA — prepara um prontuário inicial antes da primeira sessão. Para você focar no que importa: o paciente.
                        </p>

                        <div data-anim id="hero-cta" className={`ff ${S("hero-cta")} d3`} style={{ display: "flex", gap: 14, alignItems: "center", marginBottom: 40 }}>
                            <button className="btn-primary" style={{ fontSize: 17, padding: "16px 36px" }}>Testar 14 dias grátis</button>
                            <button className="btn-secondary">Ver como funciona</button>
                        </div>

                        <div data-anim id="hero-proof" className={`ff ${S("hero-proof")} d4`} style={{ display: "flex", gap: 32 }}>
                            {[
                                { val: "547k+", label: "psicólogos no Brasil" },
                                { val: "70%", label: "menos faltas" },
                                { val: "10 min", label: "para configurar" },
                            ].map(s => (
                                <div key={s.label}>
                                    <div style={{ fontSize: 24, fontWeight: 700, color: "#2D6A4F" }}>{s.val}</div>
                                    <div style={{ fontSize: 13, color: "#78716C" }}>{s.label}</div>
                                </div>
                            ))}
                        </div>
                    </div>

                    {/* Hero visual — mockup of WhatsApp reminder */}
                    <div data-anim id="hero-visual" className={`${S("hero-visual")} d2`} style={{ flex: "1 1 40%", display: "flex", justifyContent: "center" }}>
                        <div style={{
                            width: 320, background: "#fff", borderRadius: 24, padding: 0, overflow: "hidden",
                            boxShadow: "0 24px 64px rgba(0,0,0,0.1), 0 2px 8px rgba(0,0,0,0.05)",
                            border: "1px solid rgba(0,0,0,0.06)",
                        }}>
                            {/* Phone header */}
                            <div style={{ background: "#075E54", padding: "14px 16px", display: "flex", alignItems: "center", gap: 10 }}>
                                <div style={{ width: 36, height: 36, borderRadius: "50%", background: "#128C7E", display: "flex", alignItems: "center", justifyContent: "center", color: "#fff", fontSize: 13, fontWeight: 700 }}>P</div>
                                <div>
                                    <div style={{ color: "#fff", fontSize: 14, fontWeight: 600, fontFamily: "'Outfit', sans-serif" }}>PsiAgenda</div>
                                    <div style={{ color: "#B0D9D1", fontSize: 11, fontFamily: "'Outfit', sans-serif" }}>online</div>
                                </div>
                            </div>
                            {/* Chat */}
                            <div style={{ background: "#ECE5DD", padding: 14, minHeight: 340 }}>
                                <div style={{ textAlign: "center", margin: "6px 0 14px" }}>
                                    <span className="ff" style={{ background: "#E1E8ED", padding: "3px 12px", borderRadius: 6, fontSize: 11, color: "#57534E" }}>Hoje</span>
                                </div>
                                {[
                                    { text: "Oi Ana, te espero amanhã às 15h 💙\nPrecisa remarcar? Me avise.", time: "09:00", status: "✓✓" },
                                    { text: "Obrigada pelo lembrete! Estarei lá 😊", time: "09:15", from: "user" },
                                    { text: "Oi Ana, só lembrando: sua sessão é daqui a 2h, às 15:00.\nTe espero! 💙", time: "13:00", status: "✓✓" },
                                ].map((m, i) => (
                                    <div key={i} style={{ display: "flex", justifyContent: m.from === "user" ? "flex-end" : "flex-start", marginBottom: 8 }}>
                                        <div className="ff" style={{
                                            maxWidth: "80%", borderRadius: 10, padding: "9px 12px",
                                            background: m.from === "user" ? "#DCF8C6" : "#fff",
                                            borderTopLeftRadius: m.from !== "user" ? 3 : 10,
                                            borderTopRightRadius: m.from === "user" ? 3 : 10,
                                            boxShadow: "0 1px 2px rgba(0,0,0,0.04)",
                                        }}>
                                            <p style={{ fontSize: 13, color: "#1C1917", margin: 0, lineHeight: 1.5, whiteSpace: "pre-wrap" }}>{m.text}</p>
                                            <div style={{ display: "flex", justifyContent: "flex-end", alignItems: "center", gap: 4, marginTop: 3 }}>
                                                <span style={{ fontSize: 10.5, color: "#8696A0" }}>{m.time}</span>
                                                {m.status && <span style={{ fontSize: 12, color: "#53BDEB", fontWeight: 700 }}>{m.status}</span>}
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* ── PAIN SECTION ── */}
            <section style={{ padding: "80px 40px", background: "#F5F0EB" }}>
                <div style={{ maxWidth: 900, margin: "0 auto", textAlign: "center" }}>
                    <h2 data-anim id="pain-h" className={S("pain-h")} style={{ fontSize: 36, fontWeight: 700, letterSpacing: "-0.02em", marginBottom: 16 }}>
                        Você é psicólogo, não secretário.
                    </h2>
                    <p data-anim id="pain-p" className={`ff ${S("pain-p")} d1`} style={{ fontSize: 17, color: "#57534E", lineHeight: 1.6, maxWidth: 640, margin: "0 auto 48px" }}>
                        Confirmar sessão por WhatsApp. Remarcar. Lembrar. Anotar. Cobrar. Repetir. Seu tempo deveria ser gasto ouvindo, não digitando.
                    </p>
                    <div style={{ display: "flex", gap: 20, justifyContent: "center", flexWrap: "wrap" }}>
                        {[
                            { emoji: "📱", stat: "3h/semana", desc: "gastas confirmando sessões manualmente" },
                            { emoji: "🚫", stat: "R$ 800+", desc: "perdidos por mês com faltas de pacientes" },
                            { emoji: "😩", stat: "40%", desc: "dos psicólogos relatam burnout administrativo" },
                        ].map((p, i) => (
                            <div data-anim id={`pain-${i}`} key={i} className={`ff card-hover ${S(`pain-${i}`)} d${i + 1}`} style={{
                                flex: "1 1 260px", background: "#fff", borderRadius: 16, padding: "28px 24px",
                                textAlign: "center", border: "1px solid rgba(0,0,0,0.05)",
                            }}>
                                <div style={{ fontSize: 36, marginBottom: 12 }}>{p.emoji}</div>
                                <div style={{ fontSize: 28, fontWeight: 700, color: "#B45309", marginBottom: 6 }}>{p.stat}</div>
                                <div style={{ fontSize: 14, color: "#78716C", lineHeight: 1.5 }}>{p.desc}</div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* ── FEATURES ── */}
            <section id="funcionalidades" style={{ padding: "100px 40px", background: "#FAFAF7" }}>
                <div style={{ maxWidth: 1060, margin: "0 auto" }}>
                    <div style={{ textAlign: "center", marginBottom: 64 }}>
                        <span data-anim id="feat-tag" className={`ff ${S("feat-tag")}`} style={{ fontSize: 13, fontWeight: 600, color: "#2D6A4F", textTransform: "uppercase", letterSpacing: "0.1em" }}>Funcionalidades</span>
                        <h2 data-anim id="feat-h" className={`${S("feat-h")} d1`} style={{ fontSize: 38, fontWeight: 700, marginTop: 12, letterSpacing: "-0.02em" }}>
                            Tudo que você precisa.<br />Nada que não precisa.
                        </h2>
                    </div>

                    {[
                        {
                            tag: "Core", title: "Agenda que entende sua rotina", icon: "📅",
                            desc: "Sessões de 50 minutos com 10 de intervalo — o padrão da psicologia. Recorrência automática, drag-and-drop, link de agendamento público. Seu paciente agenda sozinho.",
                            highlights: ["Sessões recorrentes automáticas", "Link público de agendamento", "Visão semanal e mensal", "Detecção de conflitos"],
                            color: "#2D6A4F", bg: "#F0FDF4",
                        },
                        {
                            tag: "Anti-Falta", title: "Lembretes que funcionam de verdade", icon: "💬",
                            desc: "WhatsApp automático 24h e 2h antes. Mensagens humanizadas com o seu nome, no tom certo. Sem \"Prezado(a)\", sem robô. Seus pacientes respondem e confirmam.",
                            highlights: ["WhatsApp nativo (não é email)", "Tom humanizado e editável", "Status: enviado, entregue, lido", "Botão de reenvio em falhas"],
                            color: "#B45309", bg: "#FEF3C7",
                        },
                        {
                            tag: "Diferencial", title: "IA que fala a sua língua terapêutica", icon: "✨",
                            desc: "Antes da primeira sessão, a IA conduz uma triagem via WhatsApp usando o vocabulário da sua abordagem — TCC, Psicanálise, Gestalt, Sistêmica ou Humanista. Você recebe um prontuário inicial pronto.",
                            highlights: ["5 abordagens terapêuticas", "Vocabulário técnico adaptado", "Paciente sempre sabe que é IA", "Totalmente opcional"],
                            color: "#7C3AED", bg: "#F5F3FF",
                        },
                    ].map((f, i) => (
                        <div data-anim id={`feat-${i}`} key={i} className={S(`feat-${i}`)} style={{
                            display: "flex", alignItems: "center", gap: 48, marginBottom: 64,
                            flexDirection: i % 2 === 1 ? "row-reverse" : "row",
                        }}>
                            <div style={{ flex: "1 1 55%" }}>
                                <span className="ff" style={{ fontSize: 12, fontWeight: 700, color: f.color, textTransform: "uppercase", letterSpacing: "0.08em" }}>{f.tag}</span>
                                <h3 style={{ fontSize: 30, fontWeight: 700, marginTop: 8, marginBottom: 14, letterSpacing: "-0.02em" }}>{f.title}</h3>
                                <p className="ff" style={{ fontSize: 16, color: "#57534E", lineHeight: 1.65, marginBottom: 20 }}>{f.desc}</p>
                                <div style={{ display: "flex", flexWrap: "wrap", gap: 8 }}>
                                    {f.highlights.map(h => (
                                        <span key={h} className="ff" style={{
                                            padding: "6px 14px", borderRadius: 20, fontSize: 13, fontWeight: 500,
                                            background: f.bg, color: f.color, border: `1px solid ${f.color}20`,
                                        }}>✓ {h}</span>
                                    ))}
                                </div>
                            </div>
                            <div style={{
                                flex: "1 1 40%", aspectRatio: "4/3", borderRadius: 20,
                                background: f.bg, border: `1px solid ${f.color}15`,
                                display: "flex", alignItems: "center", justifyContent: "center",
                                fontSize: 80,
                            }}>
                                {f.icon}
                            </div>
                        </div>
                    ))}
                </div>
            </section>

            {/* ── SOCIAL PROOF ── */}
            <section style={{ padding: "80px 40px", background: "#F5F0EB" }}>
                <div style={{ maxWidth: 1000, margin: "0 auto", textAlign: "center" }}>
                    <h2 data-anim id="proof-h" className={S("proof-h")} style={{ fontSize: 36, fontWeight: 700, marginBottom: 48, letterSpacing: "-0.02em" }}>
                        Quem usa, recomenda.
                    </h2>
                    <div style={{ display: "flex", gap: 20, justifyContent: "center", flexWrap: "wrap" }}>
                        {testimonials.map((t, i) => (
                            <div data-anim id={`test-${i}`} key={i} className={`card-hover ${S(`test-${i}`)} d${i + 1}`} style={{
                                flex: "1 1 280px", maxWidth: 320, background: "#fff", borderRadius: 16,
                                padding: "28px 24px", textAlign: "left", border: "1px solid rgba(0,0,0,0.05)",
                            }}>
                                <div style={{ fontSize: 32, marginBottom: 12 }}>"</div>
                                <p className="ff" style={{ fontSize: 15, color: "#44403C", lineHeight: 1.6, marginBottom: 20, fontStyle: "italic" }}>
                                    {t.text}
                                </p>
                                <div style={{ display: "flex", alignItems: "center", gap: 12, borderTop: "1px solid #F0ECE8", paddingTop: 16 }}>
                                    <div style={{
                                        width: 40, height: 40, borderRadius: "50%", background: "#2D6A4F",
                                        color: "#fff", display: "flex", alignItems: "center", justifyContent: "center",
                                        fontSize: 14, fontWeight: 700, fontFamily: "'Outfit', sans-serif",
                                    }}>{t.name.split(" ").map(n => n[0]).slice(0, 2).join("")}</div>
                                    <div>
                                        <div className="ff" style={{ fontWeight: 600, color: "#1C1917", fontSize: 14 }}>{t.name}</div>
                                        <div className="ff" style={{ fontSize: 12, color: "#78716C" }}>{t.crp} · {t.approach}</div>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* ── PRICING ── */}
            <section id="preços" style={{ padding: "100px 40px", background: "#FAFAF7" }}>
                <div style={{ maxWidth: 960, margin: "0 auto", textAlign: "center" }}>
                    <span data-anim id="price-tag" className={`ff ${S("price-tag")}`} style={{ fontSize: 13, fontWeight: 600, color: "#2D6A4F", textTransform: "uppercase", letterSpacing: "0.1em" }}>Preços</span>
                    <h2 data-anim id="price-h" className={`${S("price-h")} d1`} style={{ fontSize: 38, fontWeight: 700, marginTop: 12, marginBottom: 12, letterSpacing: "-0.02em" }}>
                        Se evita 1 falta, já se pagou.
                    </h2>
                    <p data-anim id="price-sub" className={`ff ${S("price-sub")} d2`} style={{ fontSize: 16, color: "#78716C", marginBottom: 48 }}>
                        14 dias grátis. Sem cartão. Cancele quando quiser.
                    </p>

                    <div style={{ display: "flex", gap: 20, justifyContent: "center", flexWrap: "wrap" }}>
                        {Object.entries(plans).map(([key, p], i) => (
                            <div data-anim id={`plan-${i}`} key={key} className={`card-hover ${S(`plan-${i}`)} d${i + 1}`}
                                onClick={() => setPlan(key)}
                                style={{
                                    flex: "1 1 270px", maxWidth: 310, borderRadius: 20, padding: p.popular ? "4px" : 0,
                                    background: p.popular ? "linear-gradient(135deg, #2D6A4F, #166534)" : "transparent",
                                    cursor: "pointer",
                                }}>
                                <div style={{
                                    background: "#fff", borderRadius: p.popular ? 17 : 20, padding: "32px 24px",
                                    border: p.popular ? "none" : plan === key ? "2px solid #2D6A4F" : "1px solid rgba(0,0,0,0.08)",
                                    position: "relative", height: "100%",
                                }}>
                                    {p.popular && (
                                        <div className="ff" style={{
                                            position: "absolute", top: -12, left: "50%", transform: "translateX(-50%)",
                                            background: "#2D6A4F", color: "#fff", padding: "4px 16px", borderRadius: 20,
                                            fontSize: 12, fontWeight: 700,
                                        }}>Mais popular</div>
                                    )}
                                    <div className="ff" style={{ fontWeight: 700, fontSize: 18, color: "#1C1917", marginBottom: 4 }}>{p.name}</div>
                                    <div className="ff" style={{ fontSize: 13, color: "#78716C", marginBottom: 20 }}>{p.desc}</div>
                                    <div style={{ display: "flex", alignItems: "baseline", gap: 4, marginBottom: 24, justifyContent: "center" }}>
                                        <span className="ff" style={{ fontSize: 14, color: "#78716C" }}>R$</span>
                                        <span className="ff" style={{ fontSize: 48, fontWeight: 700, color: "#1C1917", lineHeight: 1 }}>{p.price}</span>
                                        <span className="ff" style={{ fontSize: 14, color: "#78716C" }}>/mês</span>
                                    </div>
                                    <div style={{ display: "flex", flexDirection: "column", gap: 10, marginBottom: 24, textAlign: "left" }}>
                                        {p.features.map(f => (
                                            <div key={f} className="ff" style={{ display: "flex", alignItems: "center", gap: 8, fontSize: 14, color: "#44403C" }}>
                                                <span style={{ color: "#2D6A4F", fontWeight: 700 }}>✓</span> {f}
                                            </div>
                                        ))}
                                    </div>
                                    <button className={p.popular ? "btn-primary" : "btn-secondary"} style={{ width: "100%", justifyContent: "center", display: "flex" }}>
                                        Começar grátis
                                    </button>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* ── FAQ ── */}
            <section id="faq" style={{ padding: "80px 40px", background: "#F5F0EB" }}>
                <div style={{ maxWidth: 720, margin: "0 auto" }}>
                    <h2 data-anim id="faq-h" className={S("faq-h")} style={{ fontSize: 36, fontWeight: 700, textAlign: "center", marginBottom: 40, letterSpacing: "-0.02em" }}>
                        Perguntas frequentes
                    </h2>
                    {faqs.map((f, i) => (
                        <div data-anim id={`faq-${i}`} key={i} className={S(`faq-${i}`)} style={{
                            background: "#fff", borderRadius: 14, marginBottom: 10,
                            border: faqOpen === i ? "1px solid #2D6A4F30" : "1px solid rgba(0,0,0,0.05)",
                            overflow: "hidden", transition: "border 0.2s",
                        }}>
                            <div onClick={() => setFaqOpen(faqOpen === i ? null : i)} style={{
                                padding: "18px 24px", cursor: "pointer", display: "flex",
                                justifyContent: "space-between", alignItems: "center",
                            }}>
                                <span className="ff" style={{ fontWeight: 600, fontSize: 15, color: "#1C1917" }}>{f.q}</span>
                                <span style={{ fontSize: 22, color: "#78716C", transition: "transform 0.2s", transform: faqOpen === i ? "rotate(45deg)" : "rotate(0)" }}>+</span>
                            </div>
                            {faqOpen === i && (
                                <div style={{ padding: "0 24px 18px" }}>
                                    <p className="ff" style={{ fontSize: 14, color: "#57534E", lineHeight: 1.65 }}>{f.a}</p>
                                </div>
                            )}
                        </div>
                    ))}
                </div>
            </section>

            {/* ── CTA FINAL ── */}
            <section style={{
                padding: "100px 40px", textAlign: "center",
                background: "linear-gradient(180deg, #FAFAF7 0%, #F0FDF4 100%)",
            }}>
                <div style={{ maxWidth: 600, margin: "0 auto" }}>
                    <h2 data-anim id="cta-h" className={S("cta-h")} style={{ fontSize: 40, fontWeight: 700, marginBottom: 16, letterSpacing: "-0.02em" }}>
                        Sua agenda merece cuidado<br />tanto quanto seus pacientes.
                    </h2>
                    <p data-anim id="cta-p" className={`ff ${S("cta-p")} d1`} style={{ fontSize: 17, color: "#57534E", lineHeight: 1.6, marginBottom: 32 }}>
                        14 dias grátis. Sem cartão de crédito. Configure em 10 minutos.
                    </p>
                    <div data-anim id="cta-form" className={`ff ${S("cta-form")} d2`} style={{ display: "flex", gap: 10, justifyContent: "center", maxWidth: 460, margin: "0 auto" }}>
                        <input
                            value={email} onChange={e => setEmail(e.target.value)}
                            placeholder="Seu melhor email"
                            style={{
                                flex: 1, padding: "14px 20px", borderRadius: 10, border: "1px solid #D6D3D1",
                                fontSize: 15, fontFamily: "'Outfit', sans-serif", outline: "none",
                                background: "#fff",
                            }}
                            onFocus={e => e.target.style.borderColor = "#2D6A4F"}
                            onBlur={e => e.target.style.borderColor = "#D6D3D1"}
                        />
                        <button className="btn-primary" style={{ fontSize: 15, whiteSpace: "nowrap" }}>Começar grátis →</button>
                    </div>
                    <p data-anim id="cta-sub" className={`ff ${S("cta-sub")} d3`} style={{ fontSize: 13, color: "#A8A29E", marginTop: 12 }}>
                        Sem spam. Sem compromisso. Só organização.
                    </p>
                </div>
            </section>

            {/* ── FOOTER ── */}
            <footer className="ff" style={{ padding: "40px 40px 32px", background: "#1C1917", color: "#A8A29E" }}>
                <div style={{ maxWidth: 1060, margin: "0 auto", display: "flex", justifyContent: "space-between", alignItems: "center", flexWrap: "wrap", gap: 20 }}>
                    <div style={{ display: "flex", alignItems: "center", gap: 10 }}>
                        <div style={{ width: 28, height: 28, borderRadius: 7, background: "#2D6A4F", display: "flex", alignItems: "center", justifyContent: "center", color: "#fff", fontWeight: 700, fontSize: 14 }}>P</div>
                        <span style={{ fontWeight: 700, fontSize: 16, color: "#E7E5E4" }}>PsiAgenda</span>
                    </div>
                    <div style={{ display: "flex", gap: 24, fontSize: 13 }}>
                        <a href="#" style={{ color: "#A8A29E" }}>Termos de uso</a>
                        <a href="#" style={{ color: "#A8A29E" }}>Privacidade</a>
                        <a href="#" style={{ color: "#A8A29E" }}>Contato</a>
                    </div>
                    <div style={{ fontSize: 13 }}>© 2026 PsiAgenda. Feito com 💙 para psicólogos.</div>
                </div>
            </footer>
        </div>
    );
};

export default LP;

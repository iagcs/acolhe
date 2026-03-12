import { useState, useEffect, useRef } from "react";

const C = {
    bg: "#F5F7FA", surface: "#FFFFFF", primary: "#3571C5", primaryLight: "#EBF1FB",
    primaryDark: "#2856A0", accent: "#1DAA7B", accentLight: "#E6F7F1",
    warning: "#E8A817", warningLight: "#FEF7E0", danger: "#D94848", dangerLight: "#FDEDED",
    text: "#151B28", textSec: "#5E6E82", textMuted: "#95A4B8", border: "#E2E8F0",
    borderLight: "#F0F3F7", sidebar: "#151B28", sidebarHover: "#1E2738", sidebarActive: "#2A3F5F",
    ai: "#7C3AED", aiLight: "#F1EBFF", aiBg: "#FAF5FF",
};

const STATUS = {
    scheduled: { bg: "#EBF1FB", text: "#3571C5", dot: "#3571C5", label: "Agendada" },
    confirmed: { bg: "#E6F7F1", text: "#178A63", dot: "#1DAA7B", label: "Confirmada" },
    cancelled: { bg: "#F0F3F7", text: "#5E6E82", dot: "#95A4B8", label: "Cancelada" },
    completed: { bg: "#E9F7E9", text: "#2B7A3E", dot: "#34A853", label: "Realizada" },
    no_show: { bg: "#FDEDED", text: "#B83232", dot: "#D94848", label: "Faltou" },
};

const MSG_STATUS = {
    read: { icon: "✓✓", color: "#1DAA7B", label: "Lido" },
    delivered: { icon: "✓✓", color: "#3571C5", label: "Entregue" },
    sent: { icon: "✓", color: "#95A4B8", label: "Enviado" },
    failed: { icon: "✕", color: "#D94848", label: "Falha" },
    pending: { icon: "⏳", color: "#E8A817", label: "Pendente" },
};

const I = ({ name, size = 20, color = "currentColor" }) => {
    const d = {
        calendar: <><rect x="3" y="4" width="18" height="18" rx="2" /><line x1="16" y1="2" x2="16" y2="6" /><line x1="8" y1="2" x2="8" y2="6" /><line x1="3" y1="10" x2="21" y2="10" /></>,
        users: <><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" /><circle cx="9" cy="7" r="4" /><path d="M23 21v-2a4 4 0 00-3-3.87" /><path d="M16 3.13a4 4 0 010 7.75" /></>,
        bell: <><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9" /><path d="M13.73 21a2 2 0 01-3.46 0" /></>,
        home: <><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" /><polyline points="9 22 9 12 15 12 15 22" /></>,
        settings: <><circle cx="12" cy="12" r="3" /><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z" /></>,
        search: <><circle cx="11" cy="11" r="8" /><line x1="21" y1="21" x2="16.65" y2="16.65" /></>,
        plus: <><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></>,
        chevLeft: <><polyline points="15 18 9 12 15 6" /></>,
        chevRight: <><polyline points="9 18 15 12 9 6" /></>,
        check: <><polyline points="20 6 9 17 4 12" /></>,
        x: <><line x1="18" y1="6" x2="6" y2="18" /><line x1="6" y1="6" x2="18" y2="18" /></>,
        clock: <><circle cx="12" cy="12" r="10" /><polyline points="12 6 12 12 16 14" /></>,
        video: <><polygon points="23 7 16 12 23 17 23 7" /><rect x="1" y="5" width="15" height="14" rx="2" /></>,
        phone: <><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.127.96.362 1.903.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.338 1.85.573 2.81.7A2 2 0 0122 16.92z" /></>,
        file: <><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" /><polyline points="14 2 14 8 20 8" /></>,
        image: <><rect x="3" y="3" width="18" height="18" rx="2" /><circle cx="8.5" cy="8.5" r="1.5" /><polyline points="21 15 16 10 5 21" /></>,
        upload: <><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4" /><polyline points="17 8 12 3 7 8" /><line x1="12" y1="3" x2="12" y2="15" /></>,
        trash: <><polyline points="3 6 5 6 21 6" /><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2" /></>,
        edit: <><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" /><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" /></>,
        msg: <><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" /></>,
        send: <><line x1="22" y1="2" x2="11" y2="13" /><polygon points="22 2 15 22 11 13 2 9 22 2" /></>,
        bot: <><rect x="3" y="11" width="18" height="10" rx="2" /><circle cx="12" cy="5" r="3" /><line x1="12" y1="8" x2="12" y2="11" /><circle cx="8" cy="16" r="1" /><circle cx="16" cy="16" r="1" /></>,
        shield: <><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" /></>,
        eye: <><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" /><circle cx="12" cy="12" r="3" /></>,
        lock: <><rect x="3" y="11" width="18" height="11" rx="2" /><path d="M7 11V7a5 5 0 0110 0v4" /></>,
        sparkle: <><path d="M12 2l2.4 7.2L22 12l-7.6 2.8L12 22l-2.4-7.2L2 12l7.6-2.8L12 2z" /></>,
        zap: <><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" /></>,
        download: <><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4" /><polyline points="7 10 12 15 17 10" /><line x1="12" y1="15" x2="12" y2="3" /></>,
        book: <><path d="M4 19.5A2.5 2.5 0 016.5 17H20" /><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z" /></>,
        brain: <><path d="M12 2a7 7 0 017 7c0 2.5-1.3 4.7-3.2 6L12 22l-3.8-7C6.3 13.7 5 11.5 5 9a7 7 0 017-7z" /><circle cx="12" cy="9" r="2.5" /></>,
        heart: <><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z" /></>,
        compass: <><circle cx="12" cy="12" r="10" /><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76" /></>,
        layers: <><polygon points="12 2 2 7 12 12 22 7 12 2" /><polyline points="2 17 12 22 22 17" /><polyline points="2 12 12 17 22 12" /></>,
        target: <><circle cx="12" cy="12" r="10" /><circle cx="12" cy="12" r="6" /><circle cx="12" cy="12" r="2" /></>,
        refresh: <><polyline points="23 4 23 10 17 10" /><path d="M20.49 15a9 9 0 11-2.12-9.36L23 10" /></>,
        info: <><circle cx="12" cy="12" r="10" /><line x1="12" y1="16" x2="12" y2="12" /><line x1="12" y1="8" x2="12.01" y2="8" /></>,
    };
    return <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">{d[name]}</svg>;
};

const WA = ({ size = 20, color = "#25D366" }) => (
    <svg width={size} height={size} viewBox="0 0 24 24" fill={color}><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" /></svg>
);

// ── Therapeutic approaches data ──
const APPROACHES = {
    tcc: {
        id: "tcc", name: "Terapia Cognitivo-Comportamental", short: "TCC",
        color: "#2563EB", bg: "#EFF6FF", icon: "brain",
        desc: "Foco em identificar e reestruturar pensamentos disfuncionais e padrões comportamentais.",
        vocabulary: ["pensamentos automáticos", "crenças centrais", "reestruturação cognitiva", "registro de pensamentos", "distorções cognitivas", "exposição gradual", "dessensibilização", "tarefas entre sessões", "padrão comportamental", "pensamento-emoção-comportamento"],
        sampleQuestions: [
            "Quando você percebe esses sentimentos, que tipo de pensamento costuma passar pela sua cabeça?",
            "Existe alguma situação recente em que você notou um padrão de pensamento que se repete?",
            "Como esses pensamentos influenciam o que você faz no dia a dia?",
            "Você consegue identificar momentos em que esses sentimentos são mais fortes?",
            "O que você espera conseguir mudar no seu jeito de pensar ou agir?",
        ],
        aiTone: "Direto, estruturado e acolhedor. Foca em ajudar o paciente a identificar conexões entre pensamentos, emoções e comportamentos desde a primeira conversa.",
        previewChat: [
            { from: "bot", text: "Oi Ana! Sou a assistente da Dra. Marina. 😊\n\nAntes da sua primeira sessão, gostaria de te conhecer um pouquinho melhor. É rápido e totalmente opcional.\n\nPosso começar? Responda SIM ou NÃO." },
            { from: "user", text: "SIM" },
            { from: "bot", text: "Ótimo! 💙\n\nO que te motivou a buscar terapia neste momento? Pode me contar o que está sentindo." },
            { from: "user", text: "Estou tendo crises de ansiedade no trabalho, fico tremendo e não consigo me concentrar." },
            { from: "bot", text: "Entendo, obrigada por compartilhar. 💙\n\nQuando essas crises acontecem, que tipo de pensamento costuma passar pela sua cabeça nesse momento? Por exemplo, pensamentos sobre o que pode dar errado, sobre como os outros te veem..." },
            { from: "user", text: "Penso que vou errar tudo, que vão me demitir, que não sou capaz." },
            { from: "bot", text: "Isso é muito importante, Ana. Esses pensamentos parecem bem intensos. 💙\n\nVocê percebe se esse padrão de pensamento — de achar que vai errar ou não é capaz — aparece em outras situações além do trabalho?" },
        ]
    },
    psicanalise: {
        id: "psicanalise", name: "Psicanálise", short: "Psicanálise",
        color: "#7C3AED", bg: "#F5F3FF", icon: "layers",
        desc: "Investigação do inconsciente, história de vida e padrões emocionais profundos.",
        vocabulary: ["inconsciente", "história de vida", "relações primárias", "padrões emocionais", "transferência", "associação livre", "sonhos", "infância", "vínculos afetivos", "conflitos internos", "elaboração", "resistência"],
        sampleQuestions: [
            "Me conte um pouco sobre a sua história — o que te traz aqui hoje?",
            "Como era o ambiente em que você cresceu? Sua relação com sua família?",
            "Você percebe algum padrão que se repete nos seus relacionamentos?",
            "Tem algum sonho ou lembrança que costuma aparecer com frequência?",
            "Como você se sente ao falar sobre essas questões agora?",
        ],
        aiTone: "Acolhedor, contemplativo e aberto. Convida o paciente a explorar sua história pessoal e sentimentos sem pressa, usando escuta ativa e perguntas amplas.",
        previewChat: [
            { from: "bot", text: "Oi Ana! Sou a assistente da Dra. Marina. 😊\n\nAntes da sua primeira sessão, gostaria de abrir um espaço para você se apresentar, no seu tempo. É totalmente opcional.\n\nPosso começar? Responda SIM ou NÃO." },
            { from: "user", text: "SIM" },
            { from: "bot", text: "Que bom! 💙\n\nMe conte um pouco sobre você e o que te trouxe até aqui. Pode falar livremente, sem pressa." },
            { from: "user", text: "Estou tendo crises de ansiedade no trabalho, fico tremendo e não consigo me concentrar." },
            { from: "bot", text: "Obrigada por compartilhar, Ana. 💙\n\nEssas crises parecem te afetar bastante. Quando elas acontecem, algo te vem à mente — alguma lembrança, sensação, ou imagem? Pode ser qualquer coisa, mesmo que pareça não ter relação." },
            { from: "user", text: "Às vezes lembro da minha mãe me cobrando muito quando eu era criança." },
            { from: "bot", text: "Que interessante você trazer isso. 💙\n\nComo era essa relação com a sua mãe? Me conta um pouco mais sobre como foi crescer nesse ambiente." },
        ]
    },
    humanista: {
        id: "humanista", name: "Abordagem Humanista", short: "Humanista",
        color: "#059669", bg: "#ECFDF5", icon: "heart",
        desc: "Centrada na pessoa, foco no potencial humano, autenticidade e autoconhecimento.",
        vocabulary: ["autenticidade", "autoconhecimento", "potencial", "experiência vivida", "aceitação incondicional", "empatia", "congruência", "crescimento pessoal", "aqui e agora", "escuta ativa", "campo experiencial"],
        sampleQuestions: [
            "O que te trouxe aqui? Fique à vontade para compartilhar o que sentir vontade.",
            "Como você se sente neste momento da sua vida?",
            "O que é mais importante para você neste momento?",
            "Se pudesse mudar algo em como você se sente hoje, o que seria?",
            "Como você se descreveria para alguém que não te conhece?",
        ],
        aiTone: "Caloroso, empático e presente. Prioriza a experiência subjetiva do paciente, sem julgamento, incentivando a auto-exploração e a expressão autêntica de sentimentos.",
        previewChat: [
            { from: "bot", text: "Oi Ana! Sou a assistente da Dra. Marina. 😊\n\nAntes da sua primeira sessão, gostaria de te conhecer melhor — quem você é, como está se sentindo. É um espaço só seu, totalmente opcional.\n\nPosso começar? Responda SIM ou NÃO." },
            { from: "user", text: "SIM" },
            { from: "bot", text: "Que bom que está aqui! 💙\n\nMe conta: como você está se sentindo neste momento da sua vida? Fique à vontade para compartilhar o que vier." },
            { from: "user", text: "Estou tendo crises de ansiedade no trabalho, fico tremendo e não consigo me concentrar." },
            { from: "bot", text: "Eu ouço você, Ana. Parece que está vivendo algo bem intenso. 💙\n\nQuando você pensa no que realmente importa para você agora — o que vem à mente? O que você sente que precisa neste momento?" },
            { from: "user", text: "Preciso me sentir bem comigo mesma de novo. Sinto que me perdi." },
            { from: "bot", text: "Isso que você disse é muito significativo — \"me perdi\". 💙\n\nSe pudesse se reencontrar, como seria? O que mudaria no seu dia a dia?" },
        ]
    },
    sistemica: {
        id: "sistemica", name: "Terapia Sistêmica", short: "Sistêmica",
        color: "#D97706", bg: "#FFFBEB", icon: "users",
        desc: "Olhar para o indivíduo dentro dos seus sistemas relacionais: família, trabalho, comunidade.",
        vocabulary: ["sistema familiar", "dinâmica relacional", "papéis", "padrões interacionais", "fronteiras", "triangulação", "ciclo de vida", "rede de apoio", "contexto relacional", "genograma", "circularidade"],
        sampleQuestions: [
            "Quem são as pessoas mais importantes na sua vida hoje?",
            "Como estão seus relacionamentos neste momento? Família, trabalho, amizades...",
            "Quando você enfrenta uma dificuldade, quem é a primeira pessoa que procura?",
            "Houve alguma mudança importante na sua família ou nos seus relacionamentos recentemente?",
            "Como você acha que as pessoas próximas percebem o que você está vivendo?",
        ],
        aiTone: "Contextual e relacional. Explora o paciente dentro de seus vínculos e sistemas, buscando entender como os relacionamentos influenciam o que está sentindo.",
        previewChat: [
            { from: "bot", text: "Oi Ana! Sou a assistente da Dra. Marina. 😊\n\nAntes da sua primeira sessão, gostaria de entender um pouquinho sobre você e as pessoas ao seu redor. É rápido e totalmente opcional.\n\nPosso começar? Responda SIM ou NÃO." },
            { from: "user", text: "SIM" },
            { from: "bot", text: "Que bom! 💙\n\nPrimeiro, me conta: o que te trouxe até aqui? O que está acontecendo na sua vida?" },
            { from: "user", text: "Estou tendo crises de ansiedade no trabalho, fico tremendo e não consigo me concentrar." },
            { from: "bot", text: "Entendo, Ana. Isso parece bem difícil. 💙\n\nEssas crises no trabalho — como elas têm afetado seus relacionamentos? A convivência com a família, amigos, colegas?" },
            { from: "user", text: "Tenho me isolado bastante. Meu namorado reclama que estou distante." },
            { from: "bot", text: "Obrigada por compartilhar. 💙\n\nQuando você está passando por um momento difícil como esse, quem é a primeira pessoa que procura? Ou você costuma lidar sozinha?" },
        ]
    },
    gestalt: {
        id: "gestalt", name: "Gestalt-terapia", short: "Gestalt",
        color: "#DC2626", bg: "#FEF2F2", icon: "compass",
        desc: "Foco na awareness, no aqui-e-agora e na experiência corporal e sensorial.",
        vocabulary: ["awareness", "aqui-e-agora", "experiência corporal", "figura e fundo", "contato", "fronteira de contato", "autorregulação", "gestalt inacabada", "campo", "presença", "fluidez", "ajustamento criativo"],
        sampleQuestions: [
            "Como você está se sentindo agora, neste exato momento?",
            "Quando essa ansiedade aparece, o que acontece no seu corpo?",
            "O que chama mais a sua atenção na sua vida neste momento?",
            "Se esse sentimento pudesse falar, o que ele diria?",
            "O que você percebe que ficou inacabado ou preso dentro de você?",
        ],
        aiTone: "Presente, sensorial e poético. Convida o paciente a perceber o que está sentindo no corpo e no momento presente, usando linguagem vivencial e concreta.",
        previewChat: [
            { from: "bot", text: "Oi Ana! Sou a assistente da Dra. Marina. 😊\n\nAntes da sua primeira sessão, quero te convidar para um momento de auto-observação — perceber como você está agora. É breve e totalmente opcional.\n\nPosso começar? Responda SIM ou NÃO." },
            { from: "user", text: "SIM" },
            { from: "bot", text: "Que bom! 💙\n\nAntes de me contar a história, quero te perguntar: como você está se sentindo agora, neste momento? Presta atenção no seu corpo — o que você percebe?" },
            { from: "user", text: "Estou tensa. Ombros pesados. Um aperto no peito." },
            { from: "bot", text: "Que bom que você consegue perceber isso. 💙\n\nEsse aperto no peito... se ele pudesse falar, o que acha que ele diria? O que ele quer expressar?" },
            { from: "user", text: "Acho que diria que estou cansada de segurar tudo sozinha." },
            { from: "bot", text: "\"Cansada de segurar tudo sozinha\" — que frase forte, Ana. 💙\n\nO que te trouxe a buscar terapia agora? O que fez esse momento ser diferente dos outros?" },
        ]
    },
};

// ── Shared components ──
const Btn = ({ children, variant = "primary", small, ...props }) => (
    <button {...props} style={{
        padding: small ? "6px 12px" : "9px 18px", borderRadius: 8, fontSize: small ? 12 : 13,
        fontWeight: 600, cursor: "pointer", display: "inline-flex", alignItems: "center", gap: 6,
        border: variant === "outline" ? `1px solid ${C.border}` : "none",
        background: variant === "primary" ? C.primary : variant === "danger" ? C.danger : variant === "ai" ? C.ai : C.surface,
        color: variant === "outline" ? C.text : "#fff", fontFamily: "inherit", ...props.style,
    }}>{children}</button>
);

const Badge = ({ children, color = C.primary, bg }) => (
    <span style={{ padding: "3px 9px", borderRadius: 6, fontSize: 11, fontWeight: 600, background: bg || color + "18", color, whiteSpace: "nowrap" }}>{children}</span>
);

const Toggle = ({ on, onToggle, label }) => (
    <div onClick={onToggle} style={{ display: "flex", alignItems: "center", gap: 10, cursor: "pointer" }}>
        <div style={{ width: 44, height: 24, borderRadius: 12, padding: 2, transition: "background 0.2s", background: on ? C.accent : C.border, position: "relative" }}>
            <div style={{ width: 20, height: 20, borderRadius: "50%", background: "#fff", transition: "transform 0.2s", transform: on ? "translateX(20px)" : "translateX(0)", boxShadow: "0 1px 3px rgba(0,0,0,0.15)" }} />
        </div>
        {label && <span style={{ fontSize: 14, color: C.text, fontWeight: 500 }}>{label}</span>}
    </div>
);

const Card = ({ children, style = {} }) => (
    <div style={{ background: C.surface, borderRadius: 14, border: `1px solid ${C.border}`, overflow: "hidden", ...style }}>{children}</div>
);

const CardHeader = ({ title, right, noBorder }) => (
    <div style={{ padding: "14px 20px", display: "flex", justifyContent: "space-between", alignItems: "center", borderBottom: noBorder ? "none" : `1px solid ${C.borderLight}` }}>
        <span style={{ fontSize: 15, fontWeight: 700, color: C.text }}>{title}</span>
        {right}
    </div>
);

// ── Sidebar ──
const Sidebar = ({ active, onChange }) => {
    const items = [
        { id: "dashboard", icon: "home", label: "Dashboard" },
        { id: "patients", icon: "users", label: "Pacientes" },
        { id: "messages", icon: "msg", label: "Mensagens", badge: 3 },
        { id: "ai", icon: "sparkle", label: "IA Assistente" },
    ];
    return (
        <div style={{ width: 224, minHeight: "100vh", background: C.sidebar, display: "flex", flexDirection: "column", fontFamily: "inherit", flexShrink: 0 }}>
            <div style={{ padding: "22px 16px", display: "flex", alignItems: "center", gap: 10 }}>
                <div style={{ width: 34, height: 34, borderRadius: 9, background: C.primary, display: "flex", alignItems: "center", justifyContent: "center", fontSize: 16, fontWeight: 700, color: "#fff" }}>P</div>
                <div>
                    <div style={{ color: "#fff", fontWeight: 700, fontSize: 16, letterSpacing: "-0.02em" }}>PsiAgenda</div>
                    <div style={{ color: C.textMuted, fontSize: 11 }}>Dra. Marina Silva</div>
                </div>
            </div>
            <div style={{ padding: "4px 10px", flex: 1 }}>
                {items.map(it => (
                    <div key={it.id} onClick={() => onChange(it.id)} style={{
                        display: "flex", alignItems: "center", gap: 11, padding: "9px 11px", borderRadius: 8,
                        cursor: "pointer", marginBottom: 1, transition: "all 0.12s",
                        background: active === it.id ? C.sidebarActive : "transparent",
                        color: active === it.id ? "#fff" : "#8899AA",
                    }}
                        onMouseEnter={e => { if (active !== it.id) e.currentTarget.style.background = C.sidebarHover; }}
                        onMouseLeave={e => { if (active !== it.id) e.currentTarget.style.background = "transparent"; }}
                    >
                        {it.id === "ai" ? <I name="sparkle" size={17} color={active === it.id ? "#E9D5FF" : "#A78BFA"} /> : <I name={it.icon} size={17} />}
                        <span style={{ fontSize: 13.5, fontWeight: active === it.id ? 600 : 400, flex: 1 }}>{it.label}</span>
                        {it.badge && <span style={{ background: C.danger, color: "#fff", fontSize: 10, fontWeight: 700, padding: "1px 6px", borderRadius: 9 }}>{it.badge}</span>}
                    </div>
                ))}
            </div>
        </div>
    );
};

// ── AI ASSISTANT VIEW (full with approach config) ──
const AIView = () => {
    const [aiEnabled, setAiEnabled] = useState(true);
    const [selectedApproach, setSelectedApproach] = useState("tcc");
    const [tab, setTab] = useState("approach");
    const [showApproachSelector, setShowApproachSelector] = useState(false);
    const [customInstructions, setCustomInstructions] = useState("");
    const [consentRequired, setConsentRequired] = useState(true);
    const [autoScreen, setAutoScreen] = useState(true);
    const [autoSummary, setAutoSummary] = useState(true);

    const approach = APPROACHES[selectedApproach];

    return (
        <div>
            <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center", marginBottom: 20 }}>
                <div>
                    <div style={{ display: "flex", alignItems: "center", gap: 10 }}>
                        <h1 style={{ fontSize: 22, fontWeight: 700, color: C.text, margin: 0 }}>IA Assistente</h1>
                        <Badge color={C.ai} bg={C.aiLight}>Beta</Badge>
                    </div>
                    <p style={{ color: C.textSec, fontSize: 13, margin: "3px 0 0" }}>Triagem inteligente alinhada à sua abordagem terapêutica</p>
                </div>
                <Toggle on={aiEnabled} onToggle={() => setAiEnabled(!aiEnabled)} label={aiEnabled ? "Ativo" : "Desativado"} />
            </div>

            {!aiEnabled ? (
                <Card style={{ padding: 48, textAlign: "center" }}>
                    <div style={{ width: 72, height: 72, borderRadius: "50%", background: C.aiLight, margin: "0 auto 16px", display: "flex", alignItems: "center", justifyContent: "center" }}>
                        <I name="sparkle" size={32} color={C.ai} />
                    </div>
                    <h2 style={{ fontSize: 20, fontWeight: 700, color: C.text, margin: "0 0 8px" }}>IA Assistente desativada</h2>
                    <p style={{ color: C.textSec, fontSize: 14, maxWidth: 460, margin: "0 auto 20px", lineHeight: 1.6 }}>
                        Quando ativada, a IA conduz uma triagem inicial via WhatsApp usando o vocabulário e a linhagem da sua abordagem terapêutica, gerando um prontuário preliminar alinhado ao seu modo de trabalho.
                    </p>
                    <Btn variant="ai" onClick={() => setAiEnabled(true)}><I name="sparkle" size={15} color="#fff" /> Ativar IA Assistente</Btn>
                </Card>
            ) : (
                <div>
                    {/* How it works */}
                    <Card style={{ marginBottom: 16, border: `1px solid ${C.ai}25` }}>
                        <div style={{ padding: "16px 22px", background: C.aiBg }}>
                            <div style={{ fontSize: 14, fontWeight: 700, color: C.ai, marginBottom: 12, display: "flex", alignItems: "center", gap: 6 }}>
                                <I name="zap" size={16} color={C.ai} /> Como funciona
                            </div>
                            <div style={{ display: "flex", gap: 14 }}>
                                {[
                                    { step: "1", title: "Configure sua abordagem", desc: "Escolha sua linha terapêutica e a IA adapta vocabulário e tom" },
                                    { step: "2", title: "Paciente agenda", desc: "Novo paciente recebe convite para triagem via WhatsApp" },
                                    { step: "3", title: "Conversa alinhada", desc: "IA faz perguntas usando conceitos e linguagem da sua abordagem" },
                                    { step: "4", title: "Prontuário pronto", desc: "Resumo estruturado disponível antes da primeira sessão" },
                                ].map(s => (
                                    <div key={s.step} style={{ flex: 1, display: "flex", gap: 9, alignItems: "flex-start" }}>
                                        <div style={{ width: 26, height: 26, borderRadius: "50%", background: C.ai, color: "#fff", display: "flex", alignItems: "center", justifyContent: "center", fontSize: 12, fontWeight: 700, flexShrink: 0 }}>{s.step}</div>
                                        <div>
                                            <div style={{ fontWeight: 600, color: C.text, fontSize: 13 }}>{s.title}</div>
                                            <div style={{ fontSize: 12, color: C.textSec, marginTop: 1, lineHeight: 1.4 }}>{s.desc}</div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </Card>

                    {/* Tabs */}
                    <div style={{ display: "flex", gap: 4, marginBottom: 16 }}>
                        {[
                            { id: "approach", label: "Abordagem Terapêutica", icon: "brain" },
                            { id: "questions", label: "Perguntas", icon: "msg" },
                            { id: "config", label: "Configurações", icon: "settings" },
                            { id: "preview", label: "Preview da conversa", icon: "eye" },
                        ].map(t => (
                            <div key={t.id} onClick={() => setTab(t.id)} style={{
                                padding: "9px 16px", borderRadius: 8, cursor: "pointer", fontSize: 13, fontWeight: 600,
                                display: "flex", alignItems: "center", gap: 6, transition: "all 0.12s",
                                background: tab === t.id ? C.aiLight : "transparent",
                                color: tab === t.id ? C.ai : C.textSec,
                            }}>
                                <I name={t.icon} size={14} color={tab === t.id ? C.ai : C.textMuted} />
                                {t.label}
                            </div>
                        ))}
                    </div>

                    {/* ── TAB: APPROACH ── */}
                    {tab === "approach" && (
                        <div style={{ display: "flex", gap: 16 }}>
                            <div style={{ flex: 1 }}>
                                {/* Approach selector */}
                                <Card style={{ marginBottom: 16 }}>
                                    <CardHeader title="Sua abordagem" right={
                                        <Btn small variant="outline" onClick={() => setShowApproachSelector(!showApproachSelector)}>
                                            {showApproachSelector ? "Fechar" : "Alterar abordagem"}
                                        </Btn>
                                    } />

                                    {showApproachSelector ? (
                                        <div style={{ padding: 16, display: "flex", flexDirection: "column", gap: 8 }}>
                                            {Object.values(APPROACHES).map(a => (
                                                <div key={a.id} onClick={() => { setSelectedApproach(a.id); setShowApproachSelector(false); }}
                                                    style={{
                                                        padding: "14px 18px", borderRadius: 10, cursor: "pointer",
                                                        border: `2px solid ${selectedApproach === a.id ? a.color : C.borderLight}`,
                                                        background: selectedApproach === a.id ? a.bg : "transparent",
                                                        display: "flex", alignItems: "center", gap: 14, transition: "all 0.15s",
                                                    }}
                                                    onMouseEnter={e => { if (selectedApproach !== a.id) e.currentTarget.style.borderColor = a.color + "60"; }}
                                                    onMouseLeave={e => { if (selectedApproach !== a.id) e.currentTarget.style.borderColor = C.borderLight; }}
                                                >
                                                    <div style={{ width: 42, height: 42, borderRadius: 10, background: a.color + "18", display: "flex", alignItems: "center", justifyContent: "center", flexShrink: 0 }}>
                                                        <I name={a.icon} size={20} color={a.color} />
                                                    </div>
                                                    <div style={{ flex: 1 }}>
                                                        <div style={{ fontWeight: 700, color: C.text, fontSize: 14, display: "flex", alignItems: "center", gap: 8 }}>
                                                            {a.name}
                                                            {selectedApproach === a.id && <I name="check" size={16} color={a.color} />}
                                                        </div>
                                                        <div style={{ fontSize: 12, color: C.textSec, marginTop: 2, lineHeight: 1.4 }}>{a.desc}</div>
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                    ) : (
                                        <div style={{ padding: "18px 20px" }}>
                                            <div style={{ display: "flex", alignItems: "center", gap: 14, marginBottom: 16 }}>
                                                <div style={{ width: 50, height: 50, borderRadius: 12, background: approach.color + "18", display: "flex", alignItems: "center", justifyContent: "center" }}>
                                                    <I name={approach.icon} size={24} color={approach.color} />
                                                </div>
                                                <div>
                                                    <div style={{ fontSize: 18, fontWeight: 700, color: C.text }}>{approach.name}</div>
                                                    <div style={{ fontSize: 13, color: C.textSec, marginTop: 2 }}>{approach.desc}</div>
                                                </div>
                                            </div>

                                            {/* AI Tone */}
                                            <div style={{ background: approach.bg, borderRadius: 10, padding: 16, marginBottom: 16, borderLeft: `3px solid ${approach.color}` }}>
                                                <div style={{ fontSize: 12, fontWeight: 700, color: approach.color, marginBottom: 6, display: "flex", alignItems: "center", gap: 5 }}>
                                                    <I name="sparkle" size={13} color={approach.color} /> TOM DA IA
                                                </div>
                                                <p style={{ fontSize: 13.5, color: C.text, margin: 0, lineHeight: 1.6 }}>{approach.aiTone}</p>
                                            </div>

                                            {/* Vocabulary */}
                                            <div style={{ marginBottom: 16 }}>
                                                <div style={{ fontSize: 13, fontWeight: 700, color: C.text, marginBottom: 10, display: "flex", alignItems: "center", gap: 6 }}>
                                                    <I name="book" size={15} color={approach.color} /> Vocabulário utilizado pela IA
                                                </div>
                                                <div style={{ display: "flex", flexWrap: "wrap", gap: 6 }}>
                                                    {approach.vocabulary.map(v => (
                                                        <span key={v} style={{
                                                            padding: "5px 12px", borderRadius: 20, fontSize: 12.5, fontWeight: 500,
                                                            background: approach.bg, color: approach.color, border: `1px solid ${approach.color}30`,
                                                        }}>{v}</span>
                                                    ))}
                                                </div>
                                            </div>
                                        </div>
                                    )}
                                </Card>

                                {/* Custom instructions */}
                                <Card>
                                    <CardHeader title="Instruções personalizadas" right={<Badge color={C.textMuted} bg={C.borderLight}>Opcional</Badge>} />
                                    <div style={{ padding: "16px 20px" }}>
                                        <p style={{ fontSize: 13, color: C.textSec, margin: "0 0 10px", lineHeight: 1.5 }}>
                                            Adicione orientações extras para a IA. Exemplo: temas a evitar, tom mais formal/informal, ou especificidades da sua prática.
                                        </p>
                                        <textarea
                                            value={customInstructions}
                                            onChange={e => setCustomInstructions(e.target.value)}
                                            placeholder="Ex: Evitar perguntas sobre religião. Ser especialmente gentil com pacientes que mencionam luto. Perguntar sobre qualidade do sono..."
                                            style={{
                                                width: "100%", minHeight: 100, padding: 14, borderRadius: 10,
                                                border: `1px solid ${C.border}`, fontSize: 13.5, color: C.text,
                                                fontFamily: "inherit", resize: "vertical", outline: "none",
                                                lineHeight: 1.6, background: C.bg,
                                            }}
                                            onFocus={e => e.target.style.borderColor = C.ai}
                                            onBlur={e => e.target.style.borderColor = C.border}
                                        />
                                        <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center", marginTop: 8 }}>
                                            <span style={{ fontSize: 12, color: C.textMuted }}>A IA combina estas instruções com a abordagem selecionada</span>
                                            <Btn small variant="ai"><I name="check" size={13} color="#fff" /> Salvar</Btn>
                                        </div>
                                    </div>
                                </Card>
                            </div>

                            {/* Right column: mini preview */}
                            <div style={{ width: 340, flexShrink: 0 }}>
                                <Card style={{ border: `1px solid ${approach.color}30` }}>
                                    <div style={{ padding: "12px 16px", background: "#075E54", display: "flex", alignItems: "center", gap: 10 }}>
                                        <div style={{ width: 32, height: 32, borderRadius: "50%", background: "#128C7E", display: "flex", alignItems: "center", justifyContent: "center" }}>
                                            <I name="bot" size={16} color="#fff" />
                                        </div>
                                        <div>
                                            <div style={{ color: "#fff", fontWeight: 600, fontSize: 13 }}>Assistente Dra. Marina</div>
                                            <div style={{ color: "#B0D9D1", fontSize: 10 }}>{approach.short} · online</div>
                                        </div>
                                    </div>
                                    <div style={{ background: "#ECE5DD", padding: 10, maxHeight: 380, overflowY: "auto" }}>
                                        {approach.previewChat.slice(0, 5).map((msg, i) => (
                                            <div key={i} style={{ display: "flex", justifyContent: msg.from === "bot" ? "flex-start" : "flex-end", marginBottom: 6 }}>
                                                <div style={{
                                                    maxWidth: "82%", borderRadius: 8, padding: "7px 10px",
                                                    background: msg.from === "bot" ? "#fff" : "#DCF8C6",
                                                    borderTopLeftRadius: msg.from === "bot" ? 2 : 8,
                                                    borderTopRightRadius: msg.from === "user" ? 2 : 8,
                                                    boxShadow: "0 1px 1px rgba(0,0,0,0.04)",
                                                }}>
                                                    {msg.from === "bot" && (
                                                        <div style={{ fontSize: 10, fontWeight: 600, color: approach.color, marginBottom: 2, display: "flex", alignItems: "center", gap: 3 }}>
                                                            <I name="sparkle" size={9} color={approach.color} /> {approach.short}
                                                        </div>
                                                    )}
                                                    <p style={{ fontSize: 12, color: C.text, margin: 0, lineHeight: 1.45, whiteSpace: "pre-wrap" }}>{msg.text}</p>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                    <div style={{ padding: "8px 12px", background: "#F0F0F0", textAlign: "center" }}>
                                        <span style={{ fontSize: 11, color: C.textMuted }}>Preview com abordagem <strong>{approach.short}</strong></span>
                                    </div>
                                </Card>

                                <Card style={{ marginTop: 12 }}>
                                    <div style={{ padding: 16 }}>
                                        <div style={{ fontSize: 13, fontWeight: 700, color: C.text, marginBottom: 10, display: "flex", alignItems: "center", gap: 6 }}>
                                            <I name="info" size={14} color={C.primary} /> O que muda com a abordagem
                                        </div>
                                        {[
                                            { label: "Tom da conversa", desc: approach.aiTone.split('.')[0] + "." },
                                            { label: "Vocabulário", desc: `Usa termos como "${approach.vocabulary[0]}", "${approach.vocabulary[1]}", "${approach.vocabulary[2]}"` },
                                            { label: "Estilo de pergunta", desc: approach.id === "tcc" ? "Focadas em pensamentos e comportamentos" : approach.id === "psicanalise" ? "Exploratórias sobre história e vínculos" : approach.id === "humanista" ? "Centradas na experiência subjetiva" : approach.id === "sistemica" ? "Contextuais sobre relacionamentos" : "Sensoriais e sobre o momento presente" },
                                        ].map((item, i) => (
                                            <div key={i} style={{ padding: "8px 0", borderBottom: i < 2 ? `1px solid ${C.borderLight}` : "none" }}>
                                                <div style={{ fontSize: 12, fontWeight: 600, color: approach.color }}>{item.label}</div>
                                                <div style={{ fontSize: 12, color: C.textSec, marginTop: 2, lineHeight: 1.4 }}>{item.desc}</div>
                                            </div>
                                        ))}
                                    </div>
                                </Card>
                            </div>
                        </div>
                    )}

                    {/* ── TAB: QUESTIONS ── */}
                    {tab === "questions" && (
                        <div>
                            <div style={{ background: approach.bg, borderRadius: 10, padding: "12px 18px", marginBottom: 16, display: "flex", alignItems: "center", gap: 10, border: `1px solid ${approach.color}25` }}>
                                <I name={approach.icon} size={18} color={approach.color} />
                                <span style={{ fontSize: 13, color: C.text }}>Perguntas calibradas para <strong style={{ color: approach.color }}>{approach.name}</strong>. A IA adapta o tom conforme as respostas do paciente.</span>
                            </div>
                            <Card>
                                <CardHeader title="Perguntas da triagem" right={<Btn small variant="outline"><I name="plus" size={13} color={C.text} /> Adicionar</Btn>} />
                                {approach.sampleQuestions.map((q, i) => (
                                    <div key={i} style={{
                                        padding: "14px 20px", display: "flex", alignItems: "center", gap: 14,
                                        borderBottom: i < approach.sampleQuestions.length - 1 ? `1px solid ${C.borderLight}` : "none",
                                    }}>
                                        <div style={{ width: 28, height: 28, borderRadius: 6, background: approach.bg, display: "flex", alignItems: "center", justifyContent: "center", fontSize: 12, fontWeight: 700, color: approach.color, flexShrink: 0 }}>{i + 1}</div>
                                        <span style={{ flex: 1, fontSize: 14, color: C.text, lineHeight: 1.4 }}>{q}</span>
                                        {i < 3 && <Badge color={approach.color} bg={approach.bg}>Obrigatória</Badge>}
                                        <div style={{ display: "flex", gap: 4 }}>
                                            <div style={{ padding: 5, borderRadius: 6, cursor: "pointer" }}><I name="edit" size={14} color={C.textMuted} /></div>
                                            <div style={{ padding: 5, borderRadius: 6, cursor: "pointer" }}><I name="trash" size={14} color={C.textMuted} /></div>
                                        </div>
                                    </div>
                                ))}
                                <div style={{ padding: 14, background: C.bg, display: "flex", alignItems: "center", gap: 8 }}>
                                    <I name="sparkle" size={14} color={C.ai} />
                                    <span style={{ fontSize: 12, color: C.textSec }}>A IA faz follow-ups naturais entre as perguntas, usando vocabulário da {approach.short}. As perguntas acima são o guia — a conversa flui organicamente.</span>
                                </div>
                            </Card>

                            {/* Vocabulary reference */}
                            <Card style={{ marginTop: 14 }}>
                                <CardHeader title={`Vocabulário ativo — ${approach.short}`} right={<Badge color={approach.color} bg={approach.bg}>{approach.vocabulary.length} termos</Badge>} />
                                <div style={{ padding: "14px 20px" }}>
                                    <p style={{ fontSize: 13, color: C.textSec, margin: "0 0 12px", lineHeight: 1.5 }}>
                                        Estes termos são incorporados naturalmente nas perguntas e follow-ups da IA. Você pode editar ou adicionar termos.
                                    </p>
                                    <div style={{ display: "flex", flexWrap: "wrap", gap: 6 }}>
                                        {approach.vocabulary.map(v => (
                                            <span key={v} style={{
                                                padding: "6px 14px", borderRadius: 20, fontSize: 13, fontWeight: 500,
                                                background: approach.bg, color: approach.color, border: `1px solid ${approach.color}30`,
                                                cursor: "pointer", transition: "all 0.12s",
                                            }}
                                                onMouseEnter={e => { e.currentTarget.style.background = approach.color; e.currentTarget.style.color = "#fff"; }}
                                                onMouseLeave={e => { e.currentTarget.style.background = approach.bg; e.currentTarget.style.color = approach.color; }}
                                            >{v}</span>
                                        ))}
                                        <span style={{
                                            padding: "6px 14px", borderRadius: 20, fontSize: 13, fontWeight: 600,
                                            background: "transparent", color: approach.color, border: `2px dashed ${approach.color}40`,
                                            cursor: "pointer",
                                        }}>+ Adicionar termo</span>
                                    </div>
                                </div>
                            </Card>
                        </div>
                    )}

                    {/* ── TAB: CONFIG ── */}
                    {tab === "config" && (
                        <div style={{ display: "flex", gap: 16 }}>
                            <div style={{ flex: 1 }}>
                                <Card>
                                    <CardHeader title="Preferências gerais" noBorder />
                                    <div style={{ padding: "0 20px 20px" }}>
                                        {[
                                            { label: "Consentimento obrigatório", desc: "Paciente precisa aceitar antes da triagem iniciar", value: consentRequired, toggle: () => setConsentRequired(!consentRequired) },
                                            { label: "Triagem automática para novos", desc: "Iniciar triagem automaticamente ao agendar", value: autoScreen, toggle: () => setAutoScreen(!autoScreen) },
                                            { label: "Gerar resumo automático", desc: "IA gera resumo consolidado das respostas", value: autoSummary, toggle: () => setAutoSummary(!autoSummary) },
                                        ].map((opt, i) => (
                                            <div key={i} style={{ display: "flex", justifyContent: "space-between", alignItems: "center", padding: "14px 0", borderBottom: i < 2 ? `1px solid ${C.borderLight}` : "none" }}>
                                                <div>
                                                    <div style={{ fontWeight: 600, color: C.text, fontSize: 14 }}>{opt.label}</div>
                                                    <div style={{ fontSize: 12, color: C.textMuted, marginTop: 2 }}>{opt.desc}</div>
                                                </div>
                                                <Toggle on={opt.value} onToggle={opt.toggle} />
                                            </div>
                                        ))}
                                    </div>
                                </Card>

                                <Card style={{ marginTop: 14 }}>
                                    <CardHeader title="Mensagem de convite" noBorder />
                                    <div style={{ padding: "0 20px 20px" }}>
                                        <div style={{ background: C.bg, borderRadius: 10, padding: 14, fontSize: 13.5, color: C.textSec, lineHeight: 1.6, border: `1px solid ${C.borderLight}` }}>
                                            Oi {"{nome}"}! Sou a assistente da Dra. Marina. 😊<br /><br />
                                            Antes da sua primeira sessão, gostaria de te fazer algumas perguntas para que a Dra. Marina possa te atender da melhor forma.<br /><br />
                                            É rápido (uns 5 minutinhos) e totalmente opcional. Suas respostas são confidenciais e apenas a Dra. Marina terá acesso.<br /><br />
                                            Posso começar? Responda <strong>SIM</strong> para iniciar ou <strong>NÃO</strong> se preferir não participar.
                                        </div>
                                        <Btn small variant="outline" style={{ marginTop: 10 }}><I name="edit" size={13} color={C.text} /> Editar</Btn>
                                    </div>
                                </Card>
                            </div>

                            <div style={{ flex: 1 }}>
                                <Card>
                                    <CardHeader title="Segurança & Ética" noBorder />
                                    <div style={{ padding: "0 20px 20px" }}>
                                        {[
                                            { icon: "shield", title: "Dados criptografados", desc: "Respostas armazenadas com criptografia AES-256" },
                                            { icon: "eye", title: "Transparência total", desc: "Paciente sabe que conversa com uma IA" },
                                            { icon: "x", title: "Recusa sem consequências", desc: "Paciente pode parar a qualquer momento" },
                                            { icon: "lock", title: "Acesso exclusivo", desc: "Apenas o psicólogo responsável visualiza" },
                                        ].map((item, i) => (
                                            <div key={i} style={{ display: "flex", gap: 12, padding: "12px 0", borderBottom: i < 3 ? `1px solid ${C.borderLight}` : "none" }}>
                                                <div style={{ width: 34, height: 34, borderRadius: 8, background: C.accentLight, display: "flex", alignItems: "center", justifyContent: "center", flexShrink: 0 }}>
                                                    <I name={item.icon} size={16} color={C.accent} />
                                                </div>
                                                <div>
                                                    <div style={{ fontWeight: 600, color: C.text, fontSize: 13.5 }}>{item.title}</div>
                                                    <div style={{ fontSize: 12, color: C.textMuted, marginTop: 1 }}>{item.desc}</div>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                </Card>
                                <Card style={{ marginTop: 14 }}>
                                    <CardHeader title="Estatísticas" noBorder />
                                    <div style={{ padding: "0 20px 20px", display: "flex", gap: 12 }}>
                                        {[
                                            { val: "3", label: "Triagens", color: C.ai },
                                            { val: "100%", label: "Conclusão", color: C.accent },
                                            { val: "4 min", label: "Tempo médio", color: C.primary },
                                        ].map(s => (
                                            <div key={s.label} style={{ flex: 1, textAlign: "center", padding: 12, background: C.bg, borderRadius: 10 }}>
                                                <div style={{ fontSize: 22, fontWeight: 700, color: s.color }}>{s.val}</div>
                                                <div style={{ fontSize: 11, color: C.textMuted, marginTop: 2 }}>{s.label}</div>
                                            </div>
                                        ))}
                                    </div>
                                </Card>
                            </div>
                        </div>
                    )}

                    {/* ── TAB: PREVIEW ── */}
                    {tab === "preview" && (
                        <div style={{ display: "flex", gap: 16 }}>
                            {/* Chat preview */}
                            <Card style={{ maxWidth: 440, flex: "0 0 440px" }}>
                                <div style={{ padding: "12px 16px", background: "#075E54", display: "flex", alignItems: "center", gap: 10 }}>
                                    <div style={{ width: 34, height: 34, borderRadius: "50%", background: "#128C7E", display: "flex", alignItems: "center", justifyContent: "center" }}>
                                        <I name="bot" size={18} color="#fff" />
                                    </div>
                                    <div style={{ flex: 1 }}>
                                        <div style={{ color: "#fff", fontWeight: 600, fontSize: 14 }}>Assistente Dra. Marina</div>
                                        <div style={{ color: "#B0D9D1", fontSize: 11 }}>{approach.short} · online</div>
                                    </div>
                                    <Badge color={approach.color} bg={approach.bg}>{approach.short}</Badge>
                                </div>
                                <div style={{ background: "#ECE5DD", padding: 12, maxHeight: 500, overflowY: "auto" }}>
                                    {approach.previewChat.map((msg, i) => (
                                        <div key={i} style={{ display: "flex", justifyContent: msg.from === "bot" ? "flex-start" : "flex-end", marginBottom: 8 }}>
                                            <div style={{
                                                maxWidth: "82%", borderRadius: 10, padding: "9px 12px",
                                                background: msg.from === "bot" ? "#fff" : "#DCF8C6",
                                                borderTopLeftRadius: msg.from === "bot" ? 3 : 10,
                                                borderTopRightRadius: msg.from === "user" ? 3 : 10,
                                                boxShadow: "0 1px 1px rgba(0,0,0,0.04)",
                                            }}>
                                                {msg.from === "bot" && (
                                                    <div style={{ fontSize: 10.5, fontWeight: 600, color: approach.color, marginBottom: 3, display: "flex", alignItems: "center", gap: 3 }}>
                                                        <I name="sparkle" size={10} color={approach.color} /> Assistente · {approach.short}
                                                    </div>
                                                )}
                                                <p style={{ fontSize: 13, color: C.text, margin: 0, lineHeight: 1.5, whiteSpace: "pre-wrap" }}>{msg.text}</p>
                                                <div style={{ textAlign: "right", marginTop: 3 }}>
                                                    <span style={{ fontSize: 10, color: "#8696A0" }}>14:{String(30 + i * 2).padStart(2, "0")}</span>
                                                    {msg.from === "bot" && <span style={{ fontSize: 12, color: C.accent, marginLeft: 4, fontWeight: 700 }}>✓✓</span>}
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                                <div style={{ padding: 10, background: "#F0F0F0", textAlign: "center" }}>
                                    <span style={{ fontSize: 12, color: C.textMuted }}>Simulação com abordagem <strong style={{ color: approach.color }}>{approach.short}</strong></span>
                                </div>
                            </Card>

                            {/* Analysis panel */}
                            <div style={{ flex: 1 }}>
                                <Card style={{ marginBottom: 14 }}>
                                    <CardHeader title="Análise da conversa" />
                                    <div style={{ padding: "16px 20px" }}>
                                        <div style={{ display: "flex", gap: 10, marginBottom: 16 }}>
                                            {Object.values(APPROACHES).map(a => (
                                                <div key={a.id} onClick={() => setSelectedApproach(a.id)}
                                                    style={{
                                                        padding: "8px 14px", borderRadius: 8, cursor: "pointer", fontSize: 12, fontWeight: 600,
                                                        background: selectedApproach === a.id ? a.bg : "transparent",
                                                        color: selectedApproach === a.id ? a.color : C.textMuted,
                                                        border: `1px solid ${selectedApproach === a.id ? a.color + "40" : C.borderLight}`,
                                                        transition: "all 0.12s",
                                                    }}>{a.short}</div>
                                            ))}
                                        </div>

                                        <div style={{ fontSize: 13, fontWeight: 700, color: C.text, marginBottom: 10 }}>Termos da abordagem usados na conversa:</div>
                                        <div style={{ display: "flex", flexWrap: "wrap", gap: 6, marginBottom: 16 }}>
                                            {approach.vocabulary.slice(0, 5).map(v => (
                                                <span key={v} style={{
                                                    padding: "5px 12px", borderRadius: 20, fontSize: 12, fontWeight: 500,
                                                    background: approach.bg, color: approach.color, border: `1px solid ${approach.color}30`,
                                                }}>✓ {v}</span>
                                            ))}
                                        </div>

                                        <div style={{ fontSize: 13, fontWeight: 700, color: C.text, marginBottom: 8 }}>Tom detectado:</div>
                                        <div style={{ background: approach.bg, borderRadius: 10, padding: 14, borderLeft: `3px solid ${approach.color}` }}>
                                            <p style={{ fontSize: 13, color: C.text, margin: 0, lineHeight: 1.6 }}>{approach.aiTone}</p>
                                        </div>
                                    </div>
                                </Card>

                                <Card>
                                    <CardHeader title="Compare abordagens" />
                                    <div style={{ padding: "12px 20px" }}>
                                        <p style={{ fontSize: 13, color: C.textSec, margin: "0 0 12px" }}>Veja como a mesma situação é abordada de formas diferentes:</p>
                                        <div style={{ fontSize: 12, fontWeight: 700, color: C.textMuted, marginBottom: 8 }}>Paciente diz: "Estou com ansiedade no trabalho"</div>
                                        {Object.values(APPROACHES).slice(0, 3).map(a => (
                                            <div key={a.id} style={{ padding: "10px 0", borderBottom: `1px solid ${C.borderLight}` }}>
                                                <div style={{ display: "flex", alignItems: "center", gap: 6, marginBottom: 4 }}>
                                                    <div style={{ width: 8, height: 8, borderRadius: "50%", background: a.color }} />
                                                    <span style={{ fontSize: 12, fontWeight: 700, color: a.color }}>{a.short}</span>
                                                </div>
                                                <p style={{ fontSize: 12.5, color: C.textSec, margin: 0, lineHeight: 1.5, fontStyle: "italic" }}>
                                                    "{a.previewChat[4]?.text.split('\n')[0]}"
                                                </p>
                                            </div>
                                        ))}
                                    </div>
                                </Card>
                            </div>
                        </div>
                    )}
                </div>
            )}
        </div>
    );
};

// ── Placeholder views ──
const DashboardView = ({ onNavigate }) => (
    <div>
        <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center", marginBottom: 22 }}>
            <div>
                <h1 style={{ fontSize: 22, fontWeight: 700, color: C.text, margin: 0 }}>Bom dia, Marina 👋</h1>
                <p style={{ color: C.textSec, fontSize: 13, margin: "3px 0 0" }}>Terça-feira, 11 de Março de 2026</p>
            </div>
        </div>
        <div style={{ display: "flex", gap: 14, marginBottom: 24, flexWrap: "wrap" }}>
            {[
                { label: "Sessões hoje", value: "3", icon: "calendar", color: C.primary },
                { label: "Taxa de presença", value: "91%", icon: "check", color: C.accent },
                { label: "Lembretes enviados", value: "34", icon: "send", color: "#25D366" },
                { label: "Triagens IA", value: "3", icon: "sparkle", color: C.ai },
            ].map(s => (
                <div key={s.label} style={{ flex: 1, minWidth: 160, background: C.surface, borderRadius: 12, border: `1px solid ${C.border}`, padding: "18px 20px" }}>
                    <div style={{ display: "flex", justifyContent: "space-between", alignItems: "flex-start" }}>
                        <div>
                            <div style={{ fontSize: 12, color: C.textSec, fontWeight: 500, marginBottom: 6 }}>{s.label}</div>
                            <div style={{ fontSize: 26, fontWeight: 700, color: C.text }}>{s.value}</div>
                        </div>
                        <div style={{ width: 38, height: 38, borderRadius: 10, background: s.color + "15", display: "flex", alignItems: "center", justifyContent: "center" }}>
                            <I name={s.icon} size={18} color={s.color} />
                        </div>
                    </div>
                </div>
            ))}
        </div>
        <Card style={{ padding: 40, textAlign: "center" }}>
            <p style={{ color: C.textSec, fontSize: 14 }}>Explore a tela de <strong style={{ color: C.ai, cursor: "pointer" }} onClick={() => onNavigate("ai")}>IA Assistente</strong> para ver a configuração de abordagem terapêutica.</p>
        </Card>
    </div>
);

const PatientsView = () => (
    <Card style={{ padding: 40, textAlign: "center" }}>
        <I name="users" size={40} color={C.borderLight} />
        <p style={{ color: C.textSec, fontSize: 15, marginTop: 12, fontWeight: 600 }}>Pacientes — conforme protótipo anterior</p>
        <p style={{ color: C.textMuted, fontSize: 13 }}>Lista de pacientes com notas, documentos e triagem IA por paciente.</p>
    </Card>
);

const MessagesView = () => (
    <Card style={{ padding: 40, textAlign: "center" }}>
        <I name="msg" size={40} color={C.borderLight} />
        <p style={{ color: C.textSec, fontSize: 15, marginTop: 12, fontWeight: 600 }}>Mensagens — conforme protótipo anterior</p>
        <p style={{ color: C.textMuted, fontSize: 13 }}>Hub de conversas WhatsApp com timeline e filtros.</p>
    </Card>
);

// ── Main ──
export default function PsiAgenda() {
    const [screen, setScreen] = useState("ai");
    const views = {
        dashboard: <DashboardView onNavigate={setScreen} />,
        patients: <PatientsView />,
        messages: <MessagesView />,
        ai: <AIView />,
    };
    return (
        <div style={{ display: "flex", minHeight: "100vh", fontFamily: "'DM Sans', sans-serif", background: C.bg }}>
            <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />
            <style>{`* { box-sizing: border-box; } ::-webkit-scrollbar { width: 5px; } ::-webkit-scrollbar-track { background: transparent; } ::-webkit-scrollbar-thumb { background: ${C.border}; border-radius: 3px; }`}</style>
            <Sidebar active={screen} onChange={setScreen} />
            <div style={{ flex: 1, padding: "24px 28px", overflowY: "auto", maxHeight: "100vh" }}>{views[screen]}</div>
        </div>
    );
}

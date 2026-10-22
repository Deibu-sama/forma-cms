import React, { useRef, useState } from 'react';
import { createRoot } from 'react-dom/client';

const boot = window.__BOOKKEEPER_DATA__ || {};
const settings = boot.settings || {};
const projectsFromServer = Array.isArray(boot.projects) ? boot.projects : [];
const isDemo = Boolean(boot.isDemo);

function Icon({ name, size = 20, className = '' }) {
  const common = { width: size, height: size, viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', strokeWidth: 1.7, strokeLinecap: 'round', strokeLinejoin: 'round', 'aria-hidden': true, className };
  const paths = {
    arrow: <><path d="M5 12h14" /><path d="m13 5 7 7-7 7" /></>,
    arrowUp: <><path d="M7 17 17 7" /><path d="M7 7h10v10" /></>,
    ledger: <><rect x="4" y="3" width="16" height="18" rx="2" /><path d="M8 7h8M8 11h8M8 15h4" /><path d="M7 3v18" /></>,
    check: <path d="m5 12 4 4L19 6" />,
    checkCircle: <><circle cx="12" cy="12" r="9" /><path d="m8 12 2.5 2.5L16 9" /></>,
    bars: <><path d="M4 19V9M10 19V5M16 19v-7M22 19H2" /></>,
    calendar: <><rect x="3" y="5" width="18" height="16" rx="2" /><path d="M16 3v4M8 3v4M3 10h18" /></>,
    shield: <><path d="M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11Z" /><path d="m9 12 2 2 4-4" /></>,
    message: <><path d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7a8.4 8.4 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.4 8.4 0 0 1 3.8-.9h.5a8.5 8.5 0 0 1 8 8Z" /></>,
    book: <><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v17H6.5A2.5 2.5 0 0 0 4 22Z" /><path d="M4 5.5v14A2.5 2.5 0 0 1 6.5 17H20M8 7h8M8 11h6" /></>,
    sparkle: <><path d="m12 3 1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8L12 3Z" /><path d="m19 16 .9 2.1L22 19l-2.1.9L19 22l-.9-2.1L16 19l2.1-.9L19 16Z" /></>,
    menu: <><path d="M4 7h16M4 12h16M4 17h16" /></>,
    close: <><path d="m6 6 12 12M18 6 6 18" /></>,
    mail: <><rect x="3" y="5" width="18" height="14" rx="2" /><path d="m3 7 9 6 9-6" /></>,
    clock: <><circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 2" /></>,
    chevron: <path d="m6 9 6 6 6-6" />,
    coins: <><ellipse cx="12" cy="5" rx="8" ry="3" /><path d="M4 5v6c0 1.7 3.6 3 8 3 .9 0 1.8-.1 2.6-.2M20 5v4M4 11v6c0 1.7 3.6 3 8 3 .8 0 1.6-.1 2.4-.2" /><circle cx="18" cy="17" r="4" /><path d="M18 15v4m-1.5-2.5h3" /></>,
    globe: <><circle cx="12" cy="12" r="9" /><path d="M3 12h18M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18" /></>,
    file: <><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" /><path d="M14 2v6h6M8 13h8M8 17h8" /></>,
  };
  return <svg {...common}>{paths[name] || paths.sparkle}</svg>;
}

function BrandMark({ compact = false }) {
  return (
    <span className="inline-flex items-center gap-3">
      <span className="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-brand-accent text-brand-primary">
        <Icon name="ledger" size={22} />
      </span>
      {!compact && (
        <span className="flex flex-col leading-tight">
          <span className="text-[15px] font-bold tracking-[-.04em] text-brand-primary">{settings.brand_name || 'Bookkeeper Pro'}</span>
          <span className="mt-1 text-[9px] font-semibold uppercase tracking-[.17em] text-brand-muted">Books • Clarity • Confidence</span>
        </span>
      )}
    </span>
  );
}

function SectionLabel({ children, light = false }) {
  return (
    <div className={'mb-4 flex items-center gap-3 text-[10px] font-bold uppercase tracking-[.2em] ' + (light ? 'text-brand-accent' : 'text-brand-muted')}>
      <span className="h-px w-7 bg-brand-accent" />
      {children}
    </div>
  );
}

function Header() {
  const [open, setOpen] = useState(false);
  const links = [
    ['Services', '#services'],
    ['Approach', '#approach'],
    ['Portfolio', '#portfolio'],
    ['FAQs', '#faqs'],
  ];
  return (
    <header className="sticky top-0 z-50 border-b border-brand-primary/10 bg-brand-background/95 backdrop-blur-xl">
      <div className="mx-auto flex max-w-7xl items-center justify-between px-5 py-3.5 sm:px-8 lg:px-10">
        <a href="#home" aria-label="Bookkeeper Pro home" className="rounded-md no-underline">
          <BrandMark />
        </a>
        <nav aria-label="Primary navigation" className="hidden items-center gap-7 lg:flex">
          {links.map(([label, href]) => <a key={label} href={href} className="text-[13px] font-medium text-brand-muted no-underline transition hover:text-brand-primary">{label}</a>)}
          <a href="#contact" className="button-lift inline-flex items-center gap-2 rounded-md bg-brand-primary px-5 py-3 text-[12px] font-semibold text-white no-underline hover:bg-brand-accent hover:text-brand-primary">
            Let’s talk <Icon name="arrowUp" size={15} />
          </a>
        </nav>
        <button type="button" onClick={() => setOpen(!open)} className="grid h-11 w-11 place-items-center rounded-lg border border-brand-primary/15 text-brand-primary lg:hidden" aria-label={open ? 'Close navigation' : 'Open navigation'} aria-expanded={open}>
          <Icon name={open ? 'close' : 'menu'} />
        </button>
      </div>
      {open && (
        <nav aria-label="Mobile navigation" className="mobile-menu-shadow border-t border-brand-primary/10 bg-brand-background px-5 py-4 lg:hidden">
          {links.map(([label, href]) => <a key={label} href={href} onClick={() => setOpen(false)} className="block border-b border-brand-primary/10 py-3 text-sm font-medium text-brand-primary no-underline">{label}</a>)}
          <a href="#contact" onClick={() => setOpen(false)} className="mt-4 flex items-center justify-between rounded-md bg-brand-primary px-4 py-3 text-sm font-semibold text-white no-underline">Start a conversation <Icon name="arrow" size={17} /></a>
        </nav>
      )}
    </header>
  );
}

function DashboardPreview() {
  const rows = [
    ['Income records', 'Organized', 'w-4/5'],
    ['Expense categories', 'In review', 'w-3/5'],
    ['Account reconciliation', 'Checklist', 'w-[72%]'],
  ];
  return (
    <div className="relative mx-auto w-full max-w-[540px]">
      <div className="absolute -right-5 -top-7 h-28 w-28 soft-ring sm:-right-8 sm:-top-8 sm:h-36 sm:w-36" />
      <div className="absolute -bottom-7 -left-6 h-28 w-28 rounded-full border border-brand-accent/20 sm:-bottom-9 sm:-left-9 sm:h-36 sm:w-36" />
      <div className="dashboard-card relative overflow-hidden rounded-2xl border border-white/10 bg-[#192f46] text-white">
        <div className="flex items-center justify-between border-b border-white/10 px-5 py-4 sm:px-6">
          <div className="flex items-center gap-3">
            <span className="grid h-9 w-9 place-items-center rounded-lg bg-brand-accent/15 text-brand-accent"><Icon name="ledger" size={18} /></span>
            <span>
              <span className="block text-xs font-semibold">Bookkeeping workspace</span>
              <span className="mt-1 block text-[10px] text-white/45">Illustrative preview · no client data</span>
            </span>
          </div>
          <span className="rounded-full border border-brand-accent/30 px-2.5 py-1 text-[9px] font-semibold uppercase tracking-wider text-brand-accent">Example</span>
        </div>
        <div className="p-5 sm:p-6">
          <div className="flex items-end justify-between gap-3">
            <div>
              <p className="mb-2 text-[10px] font-semibold uppercase tracking-[.16em] text-white/45">Month-end overview</p>
              <h3 className="text-xl font-semibold tracking-tight sm:text-2xl">A clearer picture.</h3>
            </div>
            <span className="mb-1 text-[10px] text-white/45">Workflow status</span>
          </div>
          <div className="mt-6 grid grid-cols-3 gap-2.5">
            {[
              ['01', 'Capture', 'Gather records'],
              ['02', 'Reconcile', 'Check activity'],
              ['03', 'Review', 'Prepare reports'],
            ].map(([number, title, subtitle]) => (
              <div key={number} className="rounded-xl border border-white/10 bg-white/[.04] p-3">
                <span className="text-[10px] text-brand-accent">{number}</span>
                <p className="mb-1 mt-4 text-[11px] font-semibold sm:text-xs">{title}</p>
                <p className="text-[9px] leading-4 text-white/45 sm:text-[10px]">{subtitle}</p>
              </div>
            ))}
          </div>
          <div className="mt-5 rounded-xl border border-white/10 bg-[#102338]/60 p-4">
            <div className="mb-4 flex items-center justify-between">
              <span className="text-xs font-semibold">Month-end checklist</span>
              <span className="text-[9px] uppercase tracking-wider text-white/40">Sample workflow</span>
            </div>
            <div className="space-y-4">
              {rows.map(([title, status, width]) => (
                <div key={title}>
                  <div className="mb-2 flex items-center justify-between gap-2 text-[10px]">
                    <span className="text-white/75">{title}</span><span className="text-white/40">{status}</span>
                  </div>
                  <div className="h-1.5 overflow-hidden rounded-full bg-white/10"><div className={'dashboard-bar h-full rounded-full ' + width} /></div>
                </div>
              ))}
            </div>
          </div>
        </div>
        <div className="flex items-center gap-2 border-t border-white/10 bg-white/[.025] px-5 py-3.5 text-[10px] text-white/55 sm:px-6">
          <Icon name="shield" size={14} className="text-brand-accent" />
          Organized records. Clear review points. No guesswork.
        </div>
      </div>
      <div className="absolute -left-4 top-[35%] hidden items-center gap-3 rounded-xl border border-brand-primary/10 bg-brand-surface px-4 py-3 text-brand-primary shadow-xl sm:flex">
        <span className="grid h-9 w-9 place-items-center rounded-lg bg-[#e6ecdf] text-[#48644a]"><Icon name="checkCircle" size={19} /></span>
        <span><span className="block text-[11px] font-bold">A process you can follow</span><span className="mt-1 block text-[9px] text-brand-muted">Built around clear checkpoints</span></span>
      </div>
    </div>
  );
}

const serviceItems = [
  {
    number: '01',
    icon: 'ledger',
    title: 'Monthly bookkeeping',
    text: 'Keep income, expenses, and everyday transactions organized with a consistent monthly routine.',
    items: ['Transaction categorization', 'Record organization', 'Month-end preparation'],
  },
  {
    number: '02',
    icon: 'checkCircle',
    title: 'Reconciliation support',
    text: 'Compare bookkeeping records with bank and account statements, then flag items needing review.',
    items: ['Bank statement matching', 'Discrepancy follow-up', 'Review-ready records'],
  },
  {
    number: '03',
    icon: 'bars',
    title: 'Financial reporting prep',
    text: 'Prepare clear, well-organized reports so you and your advisers can review the numbers.',
    items: ['Management report preparation', 'Expense summaries', 'Supporting schedules'],
  },
  {
    number: '04',
    icon: 'file',
    title: 'Catch-up & cleanup',
    text: 'Bring overdue or inconsistent bookkeeping records into a more orderly, reviewable shape.',
    items: ['Backlog review', 'Missing-record checklist', 'Clean handover notes'],
  },
];

const processItems = [
  ['Listen & understand', 'We discuss your business, current records, tools, and what you need help organizing.'],
  ['Agree on the scope', 'You receive a clear outline of the bookkeeping tasks, responsibilities, and working rhythm.'],
  ['Organize & reconcile', 'Records are categorized, accounts are reconciled where agreed, and open questions are flagged.'],
  ['Review & hand over', 'Receive the agreed reports and a clear list of items that may need your attention or adviser input.'],
];

const faqs = [
  {
    q: 'What types of businesses can you support?',
    a: 'The right fit depends on the volume, complexity, software, and condition of your records. Share a little about your business and current workflow so the scope can be assessed before work begins.',
  },
  {
    q: 'Do you provide tax, legal, or audit advice?',
    a: 'This portfolio describes bookkeeping and record-organization support, not regulated tax, legal, audit, or investment advice. I can help organize records and prepare supporting information for you to review with a suitably qualified professional.',
  },
  {
    q: 'Can you work with my accounting software?',
    a: 'Tell me which bookkeeping or accounting tools you already use. The available workflow and access requirements can be discussed before agreeing on a scope.',
  },
  {
    q: 'What do you need to get started?',
    a: 'Usually the first step is a conversation about your business, reporting needs, current records, and any deadlines. Access to financial records should only be shared through an agreed, secure process after the scope is confirmed.',
  },
  {
    q: 'How is pricing determined?',
    a: 'Pricing depends on the agreed scope, transaction volume, record condition, reporting needs, and working frequency. A proposal should be prepared after the initial discussion rather than assuming one price fits every business.',
  },
];

function App() {
  const initialHeadline = settings.headline || 'Clear books.\nConfident decisions.';
  const headlineLines = initialHeadline.split(/\r?\n/);
  const projectItems = projectsFromServer;
  const [openFaq, setOpenFaq] = useState(0);
  const [submitting, setSubmitting] = useState(false);
  const [submission, setSubmission] = useState({ type: 'idle', message: '' });
  const formRef = useRef(null);
  const palette = settings.custom_colors || {};
  const themeStyle = {
    '--brand-primary': palette.primary || '#14283f',
    '--brand-background': palette.background || '#f5f1e8',
    '--brand-accent': palette.accent || '#b99a61',
    '--brand-surface': palette.surface || '#fffdf8',
    '--brand-text': palette.text || palette.primary || '#192c41',
    '--brand-muted': palette.muted || '#68727b',
  };

  async function submitInquiry(event) {
    event.preventDefault();
    if (!formRef.current) return;
    setSubmitting(true);
    setSubmission({ type: 'idle', message: '' });
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    try {
      const response = await fetch('/contact', {
        method: 'POST',
        headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: new FormData(formRef.current),
      });
      const result = await response.json().catch(() => ({}));
      if (!response.ok) {
        const firstValidationError = result.errors ? Object.values(result.errors).flat()[0] : '';
        throw new Error(firstValidationError || result.message || 'Your message could not be sent right now. Please try again or use the email link.');
      }
      setSubmission({ type: 'success', message: result.message || 'Thank you. Your message has been received.' });
      formRef.current.reset();
    } catch (error) {
      setSubmission({ type: 'error', message: error.message || 'Something went wrong. Please try again.' });
    } finally {
      setSubmitting(false);
    }
  }

  const mailAddress = settings.contact_email || '';
  return (
    <div style={themeStyle} className="min-h-screen bg-brand-background text-brand-text">
      <a href="#main" className="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[100] focus:rounded-md focus:bg-white focus:px-4 focus:py-3 focus:text-brand-primary">Skip to content</a>
      <Header />

      {isDemo && (
        <div className="border-b border-brand-accent/25 bg-[#efe3c7] px-4 py-2 text-center text-[11px] font-medium text-brand-primary">
          Demonstration content — replace the sample copy and workflow examples with your own details.
        </div>
      )}

      <main id="main">
        <section id="home" className="hero-pattern relative overflow-hidden text-white">
          <div className="pointer-events-none absolute -right-28 top-16 h-80 w-80 rounded-full border border-white/[.08] sm:right-[4%] sm:top-12 sm:h-[28rem] sm:w-[28rem]" />
          <div className="pointer-events-none absolute -right-10 top-28 h-64 w-64 rounded-full border border-brand-accent/20 sm:right-[8%] sm:top-24 sm:h-[22rem] sm:w-[22rem]" />
          <div className="relative mx-auto grid max-w-7xl items-center gap-16 px-5 py-16 sm:px-8 sm:py-24 lg:grid-cols-[1.03fr_.97fr] lg:gap-14 lg:px-10 lg:py-28">
            <div className="relative z-10">
              <SectionLabel light>{settings.eyebrow || 'BOOKKEEPING · RECONCILIATION · REPORTING'}</SectionLabel>
              <h1 className="display-heading max-w-2xl text-5xl font-semibold sm:text-6xl lg:text-[4.55rem] xl:text-[5rem]">
                {headlineLines.map((line, index) => <span key={index} className={'block ' + (index === headlineLines.length - 1 && headlineLines.length > 1 ? 'mt-2 text-brand-accent' : '')}>{line}</span>)}
              </h1>
              <p className="mt-7 max-w-xl text-base leading-8 text-white/70 sm:text-lg">
                {settings.description || 'Reliable bookkeeping support that helps you keep records organized, understand what needs attention, and feel more prepared for your next business decision.'}
              </p>
              <div className="mt-9 flex flex-col gap-3 sm:flex-row sm:items-center">
                <a href="#contact" className="button-lift inline-flex min-h-13 items-center justify-center gap-3 rounded-md bg-brand-accent px-6 py-3.5 text-sm font-bold text-brand-primary no-underline hover:bg-[#d1b579]">
                  Discuss your bookkeeping <Icon name="arrow" size={17} />
                </a>
                <a href="#services" className="button-lift inline-flex min-h-13 items-center justify-center gap-2 rounded-md border border-white/20 px-6 py-3.5 text-sm font-semibold text-white no-underline hover:border-brand-accent/60 hover:bg-white/5">
                  Explore services
                </a>
              </div>
              <div className="mt-10 flex flex-wrap gap-x-6 gap-y-3 border-t border-white/15 pt-6 text-[11px] text-white/60">
                <span className="inline-flex items-center gap-2"><Icon name="checkCircle" size={15} className="text-brand-accent" /> Clear scope and communication</span>
                <span className="inline-flex items-center gap-2"><Icon name="shield" size={15} className="text-brand-accent" /> Respectful data handling</span>
              </div>
            </div>
            <DashboardPreview />
          </div>
          <div className="relative border-t border-white/10">
            <div className="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-3 px-5 py-4 text-[10px] uppercase tracking-[.14em] text-white/45 sm:px-8 lg:px-10">
              <span>Organized records</span><span className="hidden h-px flex-1 bg-white/10 sm:block" /><span>Thoughtful review</span><span className="hidden h-px flex-1 bg-white/10 sm:block" /><span>Decision-ready information</span>
            </div>
          </div>
        </section>

        <section id="services" className="bg-brand-background px-5 py-20 sm:px-8 sm:py-28 lg:px-10">
          <div className="mx-auto max-w-7xl">
            <div className="grid gap-7 lg:grid-cols-[.86fr_1.14fr] lg:items-end">
              <div>
                <SectionLabel>How I can help</SectionLabel>
                <h2 className="display-heading max-w-xl text-4xl font-semibold sm:text-5xl lg:text-[3.55rem]">Good bookkeeping makes room for <span className="text-brand-accent">better focus.</span></h2>
              </div>
              <p className="max-w-2xl pb-1 text-sm leading-7 text-brand-muted sm:text-base sm:leading-8 lg:justify-self-end">
                From keeping monthly records current to preparing organized reports, the work starts with your needs and a clearly agreed scope.
              </p>
            </div>
            <div className="mt-12 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
              {serviceItems.map((item) => (
                <article key={item.number} className="service-card flex flex-col rounded-xl border border-brand-primary/10 bg-brand-surface p-6 sm:p-7">
                  <div className="flex items-center justify-between">
                    <span className="grid h-12 w-12 place-items-center rounded-xl bg-brand-primary text-brand-accent"><Icon name={item.icon} size={22} /></span>
                    <span className="text-xs font-semibold tracking-[.14em] text-brand-accent">{item.number}</span>
                  </div>
                  <h3 className="mt-7 text-xl font-semibold tracking-[-.04em]">{item.title}</h3>
                  <p className="mt-3 text-sm leading-6 text-brand-muted">{item.text}</p>
                  <ul className="mt-5 space-y-3 border-t border-brand-primary/10 pt-5">
                    {item.items.map((feature) => <li key={feature} className="flex items-start gap-2 text-xs leading-5 text-brand-text"><Icon name="check" size={14} className="mt-0.5 shrink-0 text-brand-accent" />{feature}</li>)}
                  </ul>
                </article>
              ))}
            </div>
            <p className="mt-6 max-w-4xl text-[11px] leading-5 text-brand-muted">
              Services are examples of bookkeeping support and must be agreed in writing before work begins. Tax preparation, tax opinions, legal advice, audit opinions, and investment advice are outside this portfolio’s stated scope.
            </p>
          </div>
        </section>

        <section id="approach" className="section-grid overflow-hidden border-y border-brand-primary/10 bg-brand-surface px-5 py-20 sm:px-8 sm:py-28 lg:px-10">
          <div className="mx-auto grid max-w-7xl gap-12 lg:grid-cols-[.85fr_1.15fr] lg:gap-20">
            <div className="lg:sticky lg:top-28 lg:self-start">
              <SectionLabel>A thoughtful process</SectionLabel>
              <h2 className="display-heading max-w-xl text-4xl font-semibold sm:text-5xl lg:text-[3.6rem]">Clarity comes from a process you can <span className="text-brand-accent">understand.</span></h2>
              <p className="mt-6 max-w-lg text-sm leading-7 text-brand-muted sm:text-base sm:leading-8">
                No mystery, no assumed scope. We agree on the work, identify what is missing, and keep the open questions visible throughout the process.
              </p>
              <a href="#contact" className="mt-7 inline-flex items-center gap-3 border-b border-brand-accent pb-2 text-sm font-semibold text-brand-primary no-underline">Talk through your needs <Icon name="arrow" size={16} className="text-brand-accent" /></a>
            </div>
            <div className="divide-y divide-brand-primary/10">
              {processItems.map(([title, text], index) => (
                <div key={title} className="grid gap-4 py-7 first:pt-0 sm:grid-cols-[64px_1fr] sm:gap-6 sm:py-9">
                  <span className="flex h-12 w-12 items-center justify-center rounded-full border border-brand-accent/50 text-xs font-bold text-brand-accent">0{index + 1}</span>
                  <div>
                    <h3 className="text-xl font-semibold tracking-[-.035em] sm:text-2xl">{title}</h3>
                    <p className="mt-3 max-w-2xl text-sm leading-7 text-brand-muted">{text}</p>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </section>

        <section id="portfolio" className="bg-brand-background px-5 py-20 sm:px-8 sm:py-28 lg:px-10">
          <div className="mx-auto max-w-7xl">
            <div className="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
              <div>
                <SectionLabel>Portfolio & work samples</SectionLabel>
                <h2 className="display-heading max-w-2xl text-4xl font-semibold sm:text-5xl lg:text-[3.55rem]">A look at the work <span className="text-brand-accent">behind the books.</span></h2>
              </div>
              <p className="max-w-md text-sm leading-7 text-brand-muted">
                Examples can show how a process is organized without exposing private client records or overstating outcomes.
              </p>
            </div>
            {projectItems.length > 0 ? (
              <div className="mt-12 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                {projectItems.map((project, index) => (
                  <article key={project.id || project.slug || project.title || index} className="project-panel overflow-hidden rounded-xl border border-brand-primary/10 bg-brand-surface">
                    <div className={'relative flex min-h-56 flex-col justify-between overflow-hidden p-6 text-white ' + (index % 3 === 1 ? 'bg-[#5e6a5a]' : index % 3 === 2 ? 'bg-[#9c865d] text-brand-primary' : 'bg-[#1a334a]')}>
                      {project.image_url ? <img src={project.image_url} alt={project.image_alt || project.title || 'Portfolio example'} loading="lazy" className="absolute inset-0 h-full w-full object-cover" /> : null}
                      <div className="absolute inset-0 opacity-25 section-grid" />
                      <div className="relative flex items-start justify-between gap-4">
                        <span className="rounded-full border border-current/30 px-3 py-1 text-[9px] font-bold uppercase tracking-[.15em]">{(isDemo || project.is_demonstration === true) ? 'Sample workflow' : (project.category || 'Bookkeeping')}</span>
                        <Icon name="arrowUp" size={18} />
                      </div>
                      <div className="relative">
                        <p className="mb-2 text-[10px] font-bold uppercase tracking-[.16em] opacity-65">Work sample / {String(index + 1).padStart(2, '0')}</p>
                        <h3 className="max-w-sm text-2xl font-semibold tracking-[-.05em] sm:text-3xl">{project.art_label || project.title || 'Bookkeeping workflow'}</h3>
                      </div>
                    </div>
                    <div className="p-6">
                      <h3 className="text-lg font-semibold tracking-[-.03em]">{project.title || 'Bookkeeping example'}</h3>
                      <p className="mt-2 text-sm leading-6 text-brand-muted">{project.summary || project.description || 'An example of a thoughtful, well-documented bookkeeping process.'}</p>
                      <div className="mt-5 flex items-center gap-2 text-[10px] font-semibold uppercase tracking-wider text-brand-muted"><Icon name="file" size={14} className="text-brand-accent" /> {(isDemo || project.is_demonstration === true) ? 'Concept example · not client work' : (project.category || 'Work sample')}</div>
                    </div>
                  </article>
                ))}
              </div>
            ) : (
              <div className="mt-12 rounded-xl border border-dashed border-brand-primary/20 bg-brand-surface p-10 text-center">
                <Icon name="file" size={28} className="mx-auto text-brand-accent" />
                <h3 className="mt-4 text-xl font-semibold">Work samples are being prepared.</h3>
                <p className="mx-auto mt-2 max-w-lg text-sm leading-6 text-brand-muted">Published portfolio entries will appear here when they are added to the CMS.</p>
              </div>
            )}
            <p className="mt-5 text-[10px] leading-5 text-brand-muted">Please do not upload bank statements, account numbers, tax IDs, or other confidential financial records to public portfolio entries.</p>
          </div>
        </section>

        <section className="bg-[#eae4d7] px-5 py-16 sm:px-8 lg:px-10">
          <div className="mx-auto grid max-w-7xl gap-8 lg:grid-cols-[.8fr_1.2fr] lg:items-center">
            <div>
              <SectionLabel>Built on trust</SectionLabel>
              <h2 className="display-heading max-w-xl text-3xl font-semibold sm:text-4xl">Careful with your records. Clear about my role.</h2>
            </div>
            <div className="grid gap-4 sm:grid-cols-3">
              {[
                ['shield', 'Respectful handling', 'Access and document sharing should use an agreed secure process.'],
                ['message', 'Clear communication', 'Questions and missing information are surfaced rather than hidden.'],
                ['book', 'Well-kept records', 'The aim is organized bookkeeping, clear review points, and useful handovers.'],
              ].map(([icon, title, text]) => (
                <div key={title} className="border-l border-brand-accent pl-4">
                  <Icon name={icon} size={19} className="text-brand-primary" />
                  <h3 className="mt-3 text-sm font-bold">{title}</h3>
                  <p className="mt-2 text-xs leading-5 text-brand-muted">{text}</p>
                </div>
              ))}
            </div>
          </div>
        </section>

        <section id="faqs" className="bg-brand-surface px-5 py-20 sm:px-8 sm:py-28 lg:px-10">
          <div className="mx-auto grid max-w-7xl gap-10 lg:grid-cols-[.76fr_1.24fr] lg:gap-20">
            <div>
              <SectionLabel>Common questions</SectionLabel>
              <h2 className="display-heading max-w-lg text-4xl font-semibold sm:text-5xl">A few things you may want to <span className="text-brand-accent">know first.</span></h2>
              <p className="mt-5 max-w-md text-sm leading-7 text-brand-muted">Every business is different. These answers set expectations before a conversation, not a substitute for an agreed scope.</p>
            </div>
            <div className="divide-y divide-brand-primary/10 border-y border-brand-primary/10">
              {faqs.map((faq, index) => (
                <div key={faq.q}>
                  <button type="button" onClick={() => setOpenFaq(openFaq === index ? -1 : index)} aria-expanded={openFaq === index} className="flex w-full items-center justify-between gap-6 py-5 text-left">
                    <span className="text-sm font-semibold leading-6 sm:text-base">{faq.q}</span>
                    <span className={'grid h-8 w-8 shrink-0 place-items-center rounded-full border border-brand-primary/15 transition ' + (openFaq === index ? 'rotate-180 border-brand-accent bg-brand-accent/15' : '')}><Icon name="chevron" size={17} /></span>
                  </button>
                  {openFaq === index && <p className="max-w-2xl pb-6 pr-9 text-sm leading-7 text-brand-muted">{faq.a}</p>}
                </div>
              ))}
            </div>
          </div>
        </section>

        <section id="contact" className="hero-pattern relative overflow-hidden px-5 py-20 text-white sm:px-8 sm:py-28 lg:px-10">
          <div className="pointer-events-none absolute -right-32 -top-32 h-96 w-96 rounded-full border border-white/10" />
          <div className="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full border border-brand-accent/20" />
          <div className="relative mx-auto grid max-w-7xl gap-12 lg:grid-cols-[.8fr_1.2fr] lg:gap-20">
            <div>
              <SectionLabel light>Let’s talk about your books</SectionLabel>
              <h2 className="display-heading max-w-xl text-4xl font-semibold sm:text-5xl lg:text-[3.9rem]">{settings.contact_headline || 'Ready for a clearer bookkeeping routine?'}</h2>
              <p className="mt-6 max-w-lg text-sm leading-7 text-white/65 sm:text-base sm:leading-8">{settings.contact_text || 'Tell me where things stand today and what kind of support would make the biggest difference. We can start by discussing the scope.'}</p>
              {mailAddress && (
                <a href={'mailto:' + mailAddress} className="mt-8 inline-flex items-center gap-3 text-sm font-semibold text-brand-accent no-underline hover:underline">
                  <Icon name="mail" size={17} /> {mailAddress}
                </a>
              )}
              <div className="mt-10 flex max-w-md items-start gap-3 border-t border-white/15 pt-5 text-[11px] leading-5 text-white/50">
                <Icon name="shield" size={17} className="mt-0.5 shrink-0 text-brand-accent" />
                Please do not submit bank account numbers, passwords, tax IDs, or confidential financial documents through this form.
              </div>
            </div>
            <div className="rounded-xl bg-brand-surface p-5 text-brand-text shadow-2xl sm:p-8">
              <div className="mb-7 flex items-start justify-between gap-4">
                <div>
                  <h3 className="text-2xl font-semibold tracking-[-.045em]">Start a conversation</h3>
                  <p className="mt-2 text-xs leading-5 text-brand-muted">Share enough detail to help outline the next step.</p>
                </div>
                <span className="grid h-10 w-10 shrink-0 place-items-center rounded-lg bg-brand-background text-brand-primary"><Icon name="message" size={20} /></span>
              </div>
              {submission.type !== 'idle' && (
                <div role="status" aria-live="polite" className={'mb-5 rounded-md border px-4 py-3 text-sm leading-6 ' + (submission.type === 'success' ? 'border-green-700/20 bg-green-50 text-green-900' : 'border-red-700/20 bg-red-50 text-red-900')}>
                  {submission.message}
                </div>
              )}
              <form ref={formRef} onSubmit={submitInquiry} className="space-y-4">
                <div className="absolute -left-[10000px] top-auto h-px w-px overflow-hidden" aria-hidden="true">
                  <label htmlFor="website">Leave this field empty</label>
                  <input id="website" name="website" type="text" tabIndex="-1" autoComplete="off" />
                </div>
                <div className="grid gap-4 sm:grid-cols-2">
                  <label className="block"><span className="mb-2 block text-xs font-semibold">Your name <span className="text-red-700">*</span></span><input className="form-field text-sm" name="full_name" autoComplete="name" required maxLength={120} placeholder="Full name" /></label>
                  <label className="block"><span className="mb-2 block text-xs font-semibold">Email address <span className="text-red-700">*</span></span><input className="form-field text-sm" name="email" autoComplete="email" type="email" required maxLength={254} placeholder="you@business.com" /></label>
                </div>
                <div className="grid gap-4 sm:grid-cols-2">
                  <label className="block"><span className="mb-2 block text-xs font-semibold">Business name</span><input className="form-field text-sm" name="business_name" autoComplete="organization" maxLength={160} placeholder="Business or organization" /></label>
                  <label className="block"><span className="mb-2 block text-xs font-semibold">What support are you looking for?</span>
                    <select className="form-field text-sm" name="service_required" defaultValue="">
                      <option value="">Select an area</option>
                      <option value="Monthly bookkeeping">Monthly bookkeeping</option>
                      <option value="Reconciliation support">Reconciliation support</option>
                      <option value="Financial reporting prep">Financial reporting prep</option>
                      <option value="Catch-up and cleanup">Catch-up and cleanup</option>
                      <option value="Not sure yet">Not sure yet</option>
                    </select>
                  </label>
                </div>
                <div className="grid gap-4 sm:grid-cols-2">
                  <label className="block"><span className="mb-2 block text-xs font-semibold">Accounting software</span><input className="form-field text-sm" name="accounting_software" maxLength={100} placeholder="e.g. your current tool" /></label>
                  <label className="block"><span className="mb-2 block text-xs font-semibold">Approx. monthly transactions</span>
                    <select className="form-field text-sm" name="monthly_transaction_volume" defaultValue="">
                      <option value="">Choose a range</option>
                      <option value="Under 100">Under 100</option>
                      <option value="100–500">100–500</option>
                      <option value="501–1500">501–1,500</option>
                      <option value="1500+">1,500+</option>
                      <option value="Not sure">Not sure</option>
                    </select>
                  </label>
                </div>
                <label className="block"><span className="mb-2 block text-xs font-semibold">What would you like help with? <span className="text-red-700">*</span></span><textarea className="form-field min-h-32 resize-y text-sm" name="message" rows="4" required maxLength={3000} placeholder="A brief overview of the support you’re looking for…" /></label>
                <button type="submit" disabled={submitting} className="button-lift flex min-h-12 w-full items-center justify-center gap-3 rounded-md bg-brand-primary px-5 py-3 text-sm font-bold text-white disabled:cursor-not-allowed disabled:opacity-60">
                  {submitting ? 'Sending…' : 'Send enquiry'} <Icon name="arrow" size={17} />
                </button>
                <p className="text-[10px] leading-5 text-brand-muted">By submitting, you agree that your details may be used to respond to your enquiry. Do not include sensitive financial information. See the site owner’s privacy notice before launch.</p>
              </form>
            </div>
          </div>
        </section>
      </main>

      <footer className="bg-brand-background px-5 py-7 sm:px-8 lg:px-10">
        <div className="mx-auto flex max-w-7xl flex-col gap-5 border-t border-brand-primary/15 pt-6 sm:flex-row sm:items-center sm:justify-between">
          <a href="#home" className="inline-flex w-fit items-center gap-3 no-underline"><BrandMark /></a>
          <p className="max-w-xl text-[10px] leading-5 text-brand-muted">© {new Date().getFullYear()} {settings.site_name || 'Bookkeeper Pro Portfolio'}. Bookkeeping and record-organization support only; not tax, legal, audit, or investment advice.</p>
          <a href="#contact" className="inline-flex items-center gap-2 text-xs font-semibold text-brand-primary no-underline hover:text-brand-accent">Get in touch <Icon name="arrowUp" size={14} /></a>
        </div>
      </footer>
    </div>
  );
}

const root = document.getElementById('app');
if (root) createRoot(root).render(<App />);

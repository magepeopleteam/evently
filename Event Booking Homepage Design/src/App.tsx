import { useState, useEffect, useRef } from 'react'

/* ─── COLORS ────────────────────────────────────────────────── */
const C = {
  bg: '#FFFFFF',
  soft: '#F6F6F3',
  dark: '#0B0B0D',
  text: '#111113',
  muted: '#777773',
  border: '#E7E7E3',
  accent: '#6C5CE7',
  orange: '#FF7657',
}

/* ─── IMAGES ────────────────────────────────────────────────── */
const IMGS = {
  hero: 'https://images.unsplash.com/photo-1459749411175-04bf5292ceea?w=900&h=720&fit=crop&auto=format',
  concert: 'https://images.unsplash.com/photo-1470229722913-7c0e2dbbafd3?w=600&h=400&fit=crop&auto=format',
  festival: 'https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?w=600&h=400&fit=crop&auto=format',
  conference: 'https://images.unsplash.com/photo-1587825140708-dfaf72ae4b04?w=600&h=400&fit=crop&auto=format',
  sports: 'https://images.unsplash.com/photo-1563299796-b729d0af54a5?w=600&h=400&fit=crop&auto=format',
  food: 'https://images.unsplash.com/photo-1638132704795-6bb223151bf7?w=600&h=400&fit=crop&auto=format',
  workshop: 'https://images.unsplash.com/photo-1560831340-b9679dc9e9f0?w=600&h=400&fit=crop&auto=format',
  featured: 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?w=1440&h=620&fit=crop&auto=format',
  event1: 'https://images.unsplash.com/photo-1524368535928-5b5e00ddc76b?w=500&h=380&fit=crop&auto=format',
  event2: 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=500&h=380&fit=crop&auto=format',
  event3: 'https://images.unsplash.com/photo-1565035010268-a3816f98589a?w=500&h=380&fit=crop&auto=format',
  event4: 'https://images.unsplash.com/photo-1563841930606-67e2bce48b78?w=500&h=380&fit=crop&auto=format',
  event5: 'https://images.unsplash.com/photo-1505373877841-8d25f7d46678?w=500&h=380&fit=crop&auto=format',
  event6: 'https://images.unsplash.com/photo-1531058020387-3be344556be6?w=500&h=380&fit=crop&auto=format',
  event7: 'https://images.unsplash.com/photo-1512540452972-baac55d40ef1?w=500&h=380&fit=crop&auto=format',
  event8: 'https://images.unsplash.com/photo-1705593973313-75de7bf95b56?w=500&h=380&fit=crop&auto=format',
  journal1: 'https://images.unsplash.com/photo-1540039155733-5bb30b53aa14?w=700&h=460&fit=crop&auto=format',
  journal2: 'https://images.unsplash.com/photo-1776654983411-fcf58974aee4?w=700&h=460&fit=crop&auto=format',
  journal3: 'https://images.unsplash.com/photo-1569783721854-33a99b4c0bae?w=700&h=460&fit=crop&auto=format',
}

/* ─── EVENTS DATA ────────────────────────────────────────────── */
const EVENTS = [
  { id: 1, img: IMGS.event1, category: 'MUSIC', date: 'AUG 24', title: 'Summer Music Festival', location: 'Dhaka, Bangladesh', price: 'From $49', vibe: ['Music', 'Travel'] },
  { id: 2, img: IMGS.event2, category: 'CONFERENCE', date: 'SEP 08', title: 'Tech Innovation Summit', location: 'Dhaka, Bangladesh', price: 'From $89', vibe: ['Business', 'Learn'] },
  { id: 3, img: IMGS.event3, category: 'MUSIC', date: 'SEP 14', title: 'Jazz & Blues Night', location: 'Chittagong, Bangladesh', price: 'From $35', vibe: ['Music'] },
  { id: 4, img: IMGS.event4, category: 'FESTIVAL', date: 'OCT 03', title: 'Cultural Arts Festival', location: 'Dhaka, Bangladesh', price: 'From $20', vibe: ['Creative', 'Travel', 'Family'] },
  { id: 5, img: IMGS.event5, category: 'BUSINESS', date: 'SEP 21', title: 'Business Growth Summit', location: 'Dhaka, Bangladesh', price: 'From $129', vibe: ['Business', 'Learn'] },
  { id: 6, img: IMGS.event6, category: 'WORKSHOP', date: 'OCT 10', title: 'Photography Masterclass', location: 'Dhaka, Bangladesh', price: 'From $75', vibe: ['Creative', 'Learn'] },
  { id: 7, img: IMGS.event7, category: 'ARTS', date: 'OCT 18', title: 'Modern Art Exhibition', location: 'Sylhet, Bangladesh', price: 'From $15', vibe: ['Creative', 'Family'] },
  { id: 8, img: IMGS.event8, category: 'SPORTS', date: 'NOV 02', title: 'Premier League Night', location: 'Dhaka, Bangladesh', price: 'From $60', vibe: ['Sports', 'Travel'] },
]

const CALENDAR_EVENTS: Record<number, { title: string; location: string; price: string }[]> = {
  5: [{ title: 'Startup Networking Night', location: 'Dhaka', price: 'Free' }],
  10: [{ title: 'Jazz & Blues Night', location: 'Chittagong', price: 'From $35' }],
  14: [{ title: "Indie Folk Showcase", location: 'Dhaka', price: 'From $25' }],
  18: [{ title: 'Modern Art Exhibition', location: 'Sylhet', price: 'From $15' }],
  21: [{ title: 'Business Growth Summit', location: 'Dhaka', price: 'From $129' }],
  24: [
    { title: 'Summer Music Festival', location: 'Dhaka', price: 'From $49' },
    { title: 'Business Growth Summit', location: 'Dhaka', price: 'From $89' },
  ],
  28: [{ title: 'Food & Culture Fair', location: 'Dhaka', price: 'From $18' }],
}

/* ─── HEADER ─────────────────────────────────────────────────── */
function Header() {
  const [scrolled, setScrolled] = useState(false)
  const [mobileOpen, setMobileOpen] = useState(false)

  useEffect(() => {
    const handler = () => setScrolled(window.scrollY > 40)
    window.addEventListener('scroll', handler)
    return () => window.removeEventListener('scroll', handler)
  }, [])

  return (
    <header
      style={{
        position: 'fixed',
        top: 0,
        left: 0,
        right: 0,
        zIndex: 100,
        height: 76,
        display: 'flex',
        alignItems: 'center',
        transition: 'background 0.3s, border-color 0.3s, backdrop-filter 0.3s',
        background: scrolled ? 'rgba(255,255,255,0.92)' : 'transparent',
        backdropFilter: scrolled ? 'blur(16px)' : 'none',
        borderBottom: scrolled ? `1px solid ${C.border}` : '1px solid transparent',
      }}
    >
      <div style={{ maxWidth: 1240, width: '100%', margin: '0 auto', padding: '0 40px', display: 'flex', alignItems: 'center', gap: 40 }}>
        {/* Logo */}
        <span style={{ fontWeight: 800, fontSize: 22, letterSpacing: '-0.04em', color: C.text, flexShrink: 0 }}>
          EVENTLY
        </span>

        {/* Nav */}
        <nav style={{ display: 'flex', gap: 32, marginLeft: 8 }} className="hidden-mobile">
          {['Events', 'Categories', 'Venues', 'Organizers'].map(item => (
            <a key={item} href="#" style={{ fontSize: 15, fontWeight: 500, color: C.muted, textDecoration: 'none', transition: 'color 0.15s' }}
              onMouseEnter={e => (e.currentTarget.style.color = C.text)}
              onMouseLeave={e => (e.currentTarget.style.color = C.muted)}>
              {item}
            </a>
          ))}
        </nav>

        <div style={{ flex: 1 }} />

        {/* Right side */}
        <div style={{ display: 'flex', alignItems: 'center', gap: 16 }} className="hidden-mobile">
          <button style={{ background: 'none', border: 'none', cursor: 'pointer', padding: 8, color: C.muted }}>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
              <circle cx="11" cy="11" r="8" /><path d="m21 21-4.35-4.35" />
            </svg>
          </button>
          <a href="#" style={{ fontSize: 15, fontWeight: 500, color: C.text, textDecoration: 'none' }}>Log in</a>
          <button style={{
            background: C.dark, color: '#fff', border: 'none', borderRadius: 100, padding: '10px 22px',
            fontSize: 15, fontWeight: 600, cursor: 'pointer', transition: 'opacity 0.15s',
          }}
            onMouseEnter={e => (e.currentTarget.style.opacity = '0.85')}
            onMouseLeave={e => (e.currentTarget.style.opacity = '1')}>
            Create Event
          </button>
        </div>

        {/* Mobile menu */}
        <button
          className="show-mobile"
          onClick={() => setMobileOpen(!mobileOpen)}
          style={{ background: 'none', border: 'none', cursor: 'pointer', padding: 8, color: C.text }}>
          {mobileOpen
            ? <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M18 6 6 18M6 6l12 12" /></svg>
            : <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><line x1="3" y1="6" x2="21" y2="6" /><line x1="3" y1="12" x2="21" y2="12" /><line x1="3" y1="18" x2="21" y2="18" /></svg>
          }
        </button>
      </div>

      {/* Mobile dropdown */}
      {mobileOpen && (
        <div style={{
          position: 'absolute', top: 76, left: 0, right: 0,
          background: '#fff', borderBottom: `1px solid ${C.border}`,
          padding: '20px 20px 24px', display: 'flex', flexDirection: 'column', gap: 8,
        }}>
          {['Events', 'Categories', 'Venues', 'Organizers'].map(item => (
            <a key={item} href="#" style={{ fontSize: 16, fontWeight: 500, color: C.text, textDecoration: 'none', padding: '10px 0', borderBottom: `1px solid ${C.border}` }}>
              {item}
            </a>
          ))}
          <div style={{ display: 'flex', gap: 12, marginTop: 12 }}>
            <a href="#" style={{ fontSize: 15, fontWeight: 500, color: C.text, textDecoration: 'none', flex: 1, textAlign: 'center', padding: '12px', border: `1px solid ${C.border}`, borderRadius: 100 }}>Log in</a>
            <button style={{ background: C.dark, color: '#fff', border: 'none', borderRadius: 100, padding: '12px 20px', fontSize: 15, fontWeight: 600, cursor: 'pointer', flex: 1 }}>
              Create Event
            </button>
          </div>
        </div>
      )}
    </header>
  )
}

/* ─── HERO ───────────────────────────────────────────────────── */
function Hero() {
  return (
    <section style={{ paddingTop: 76, background: C.soft, minHeight: 720, position: 'relative' }}>
      <div style={{ maxWidth: 1240, margin: '0 auto', padding: '80px 40px 60px', display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 64, alignItems: 'center' }} className="hero-grid">
        {/* Left */}
        <div>
          <span style={{
            display: 'inline-block', fontSize: 11, fontWeight: 700, letterSpacing: '0.14em',
            color: C.accent, textTransform: 'uppercase', marginBottom: 24,
            background: `${C.accent}12`, padding: '6px 14px', borderRadius: 100,
          }}>
            Discover Your Next Experience
          </span>
          <h1 style={{ fontSize: 'clamp(44px, 6vw, 76px)', fontWeight: 800, lineHeight: 1.04, letterSpacing: '-0.03em', color: C.text, margin: '0 0 24px' }}>
            Events worth<br />remembering.
          </h1>
          <p style={{ fontSize: 18, lineHeight: 1.6, color: C.muted, margin: '0 0 40px', maxWidth: 420 }}>
            Find concerts, conferences, festivals and experiences happening around you.
          </p>
          <div style={{ display: 'flex', gap: 12, flexWrap: 'wrap' }}>
            <ArrowButton variant="primary" href="#">Explore Events</ArrowButton>
            <ArrowButton variant="secondary" href="#">Browse Categories</ArrowButton>
          </div>

          {/* Trust badges */}
          <div style={{ display: 'flex', gap: 32, marginTop: 48, flexWrap: 'wrap' }}>
            {[['10K+', 'Events'], ['250K+', 'Tickets Sold'], ['50+', 'Cities']].map(([n, l]) => (
              <div key={l}>
                <div style={{ fontSize: 22, fontWeight: 800, color: C.text, letterSpacing: '-0.03em' }}>{n}</div>
                <div style={{ fontSize: 13, color: C.muted, marginTop: 2 }}>{l}</div>
              </div>
            ))}
          </div>
        </div>

        {/* Right — image + floating card */}
        <div style={{ position: 'relative' }}>
          <div style={{
            borderRadius: 28, overflow: 'hidden', aspectRatio: '4/3',
            background: C.border,
          }}>
            <img src={IMGS.hero} alt="Concert crowd at summer music festival" style={{ width: '100%', height: '100%', objectFit: 'cover', display: 'block' }} />
          </div>

          {/* Floating event card */}
          <div style={{
            position: 'absolute', bottom: 24, left: -32,
            background: '#fff', borderRadius: 20, padding: '20px 24px',
            boxShadow: '0 20px 60px rgba(0,0,0,0.12)',
            border: `1px solid ${C.border}`,
            minWidth: 260,
          }}>
            <div style={{ fontSize: 11, fontWeight: 700, letterSpacing: '0.1em', color: C.accent, marginBottom: 8 }}>SUMMER MUSIC FESTIVAL</div>
            <div style={{ fontSize: 15, fontWeight: 700, color: C.text, marginBottom: 4 }}>Aug 24–26, 2026</div>
            <div style={{ fontSize: 13, color: C.muted, marginBottom: 14 }}>📍 Dhaka, Bangladesh</div>
            <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
              <span style={{ fontSize: 18, fontWeight: 800, color: C.text }}>From $49</span>
              <button style={{
                background: C.dark, color: '#fff', border: 'none', borderRadius: 100,
                padding: '9px 18px', fontSize: 13, fontWeight: 600, cursor: 'pointer',
              }}>
                Book Now →
              </button>
            </div>
          </div>

          {/* Top right badge */}
          <div style={{
            position: 'absolute', top: 20, right: 20,
            background: 'rgba(255,255,255,0.95)', backdropFilter: 'blur(8px)',
            borderRadius: 14, padding: '10px 16px',
            display: 'flex', alignItems: 'center', gap: 8,
            boxShadow: '0 4px 20px rgba(0,0,0,0.08)',
          }}>
            <div style={{ width: 8, height: 8, borderRadius: '50%', background: '#22C55E' }} />
            <span style={{ fontSize: 13, fontWeight: 600, color: C.text }}>2,840 tickets sold today</span>
          </div>
        </div>
      </div>

      {/* Search bar overlapping bottom */}
      <div style={{ position: 'relative', zIndex: 10 }}>
        <SearchBar />
      </div>
    </section>
  )
}

/* ─── SEARCH BAR ─────────────────────────────────────────────── */
function SearchBar() {
  return (
    <div style={{ padding: '0 40px', marginTop: 40, paddingBottom: 0 }} className="search-wrap">
      <div style={{ maxWidth: 1240, margin: '0 auto' }}>
        <div style={{
          background: '#fff', borderRadius: 24, border: `1px solid ${C.border}`,
          boxShadow: '0 16px 56px rgba(0,0,0,0.12)',
          display: 'grid', gridTemplateColumns: '1fr 1fr 1fr 1fr auto',
          overflow: 'hidden', height: 88,
        }} className="search-grid">
          {[
            { label: 'WHAT', placeholder: 'Search events, artists, venues...' },
            { label: 'WHERE', placeholder: 'Dhaka' },
            { label: 'WHEN', placeholder: 'Any date' },
            { label: 'CATEGORY', placeholder: 'All events' },
          ].map((f, i) => (
            <div key={f.label} style={{
              padding: '0 28px', display: 'flex', flexDirection: 'column', justifyContent: 'center',
              borderRight: i < 3 ? `1px solid ${C.border}` : 'none',
            }}>
              <div style={{ fontSize: 10, fontWeight: 700, letterSpacing: '0.1em', color: C.muted, marginBottom: 4 }}>{f.label}</div>
              <input
                type="text"
                placeholder={f.placeholder}
                style={{
                  border: 'none', outline: 'none', fontSize: 15, fontWeight: 500,
                  color: C.text, background: 'transparent', fontFamily: 'inherit', padding: 0,
                }}
              />
            </div>
          ))}
          <div style={{ padding: '0 20px', display: 'flex', alignItems: 'center' }}>
            <button style={{
              background: C.accent, color: '#fff', border: 'none', borderRadius: 16,
              padding: '14px 32px', fontSize: 15, fontWeight: 700, cursor: 'pointer',
              transition: 'opacity 0.15s', whiteSpace: 'nowrap',
            }}
              onMouseEnter={e => (e.currentTarget.style.opacity = '0.88')}
              onMouseLeave={e => (e.currentTarget.style.opacity = '1')}>
              Search
            </button>
          </div>
        </div>

        {/* Mobile search */}
        <div className="search-mobile" style={{ display: 'none', background: '#fff', borderRadius: 18, border: `1px solid ${C.border}`, boxShadow: '0 12px 40px rgba(0,0,0,0.10)', padding: 8, marginTop: 20 }}>
          <div style={{ display: 'flex', gap: 8 }}>
            <input type="text" placeholder="Search events..." style={{ flex: 1, border: 'none', outline: 'none', fontSize: 16, padding: '14px 16px', borderRadius: 12, background: C.soft, fontFamily: 'inherit', color: C.text }} />
            <button style={{ background: C.accent, color: '#fff', border: 'none', borderRadius: 12, padding: '14px 20px', fontSize: 15, fontWeight: 700, cursor: 'pointer' }}>Search</button>
          </div>
        </div>
      </div>
    </div>
  )
}

/* ─── CATEGORIES ─────────────────────────────────────────────── */
function Categories() {
  const cats = [
    { label: 'Concerts', img: IMGS.concert, span: 'col-span-2' },
    { label: 'Conferences', img: IMGS.conference, span: '' },
    { label: 'Sports', img: IMGS.sports, span: '' },
    { label: 'Festivals', img: IMGS.festival, span: '' },
    { label: 'Food & Dining', img: IMGS.food, span: '' },
    { label: 'Workshops', img: IMGS.workshop, span: '' },
  ]

  return (
    <section style={{ padding: '80px 40px 100px', maxWidth: 1240, margin: '0 auto' }}>
      <div style={{ display: 'flex', alignItems: 'flex-end', justifyContent: 'space-between', marginBottom: 48, flexWrap: 'wrap', gap: 16 }}>
        <div>
          <h2 style={{ fontSize: 'clamp(34px, 4vw, 48px)', fontWeight: 800, letterSpacing: '-0.03em', color: C.text, margin: '0 0 12px' }}>
            Explore by experience
          </h2>
          <p style={{ fontSize: 17, color: C.muted, margin: 0 }}>Find something you'll love.</p>
        </div>
      </div>

      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gridTemplateRows: 'repeat(2, 260px)', gap: 16 }} className="cat-grid">
        {cats.map((cat, i) => (
          <CategoryCard key={cat.label} cat={cat} wide={i === 0} />
        ))}
      </div>
    </section>
  )
}

function CategoryCard({ cat, wide }: { cat: { label: string; img: string }; wide: boolean }) {
  const [hovered, setHovered] = useState(false)
  return (
    <div
      onMouseEnter={() => setHovered(true)}
      onMouseLeave={() => setHovered(false)}
      style={{
        gridColumn: wide ? 'span 2' : 'span 1',
        borderRadius: 24, overflow: 'hidden', position: 'relative', cursor: 'pointer',
        background: C.border,
      }}
    >
      <img
        src={cat.img}
        alt={cat.label}
        style={{
          width: '100%', height: '100%', objectFit: 'cover', display: 'block',
          transition: 'transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94)',
          transform: hovered ? 'scale(1.05)' : 'scale(1)',
        }}
      />
      <div style={{
        position: 'absolute', inset: 0,
        background: 'linear-gradient(to top, rgba(0,0,0,0.72) 0%, rgba(0,0,0,0.1) 60%, transparent 100%)',
      }} />
      <div style={{
        position: 'absolute', bottom: 0, left: 0, right: 0, padding: '24px 28px',
        display: 'flex', alignItems: 'flex-end', justifyContent: 'space-between',
      }}>
        <span style={{ fontSize: 20, fontWeight: 700, color: '#fff' }}>{cat.label}</span>
        <span style={{
          fontSize: 20, color: '#fff', transition: 'transform 0.2s',
          transform: hovered ? 'translateX(5px)' : 'translateX(0)',
          display: 'inline-block',
        }}>→</span>
      </div>
    </div>
  )
}

/* ─── EVENT CARD ─────────────────────────────────────────────── */
function EventCard({ event }: { event: typeof EVENTS[0] }) {
  const [hovered, setHovered] = useState(false)
  const [liked, setLiked] = useState(false)
  return (
    <div
      onMouseEnter={() => setHovered(true)}
      onMouseLeave={() => setHovered(false)}
      style={{
        borderRadius: 20, overflow: 'hidden', background: '#fff',
        border: `1px solid ${C.border}`,
        transition: 'transform 0.22s, box-shadow 0.22s',
        transform: hovered ? 'translateY(-4px)' : 'translateY(0)',
        boxShadow: hovered ? '0 20px 48px rgba(0,0,0,0.10)' : '0 2px 8px rgba(0,0,0,0.04)',
        cursor: 'pointer',
      }}
    >
      <div style={{ position: 'relative', overflow: 'hidden', height: 220, background: C.soft }}>
        <img
          src={event.img}
          alt={event.title}
          style={{
            width: '100%', height: '100%', objectFit: 'cover',
            transition: 'transform 0.5s',
            transform: hovered ? 'scale(1.04)' : 'scale(1)',
          }}
        />
        <span style={{
          position: 'absolute', top: 14, left: 14,
          background: 'rgba(255,255,255,0.95)', backdropFilter: 'blur(8px)',
          borderRadius: 8, padding: '4px 10px',
          fontSize: 11, fontWeight: 700, letterSpacing: '0.08em', color: C.text,
        }}>
          {event.category}
        </span>
        <button
          onClick={e => { e.stopPropagation(); setLiked(!liked) }}
          style={{
            position: 'absolute', top: 14, right: 14,
            background: 'rgba(255,255,255,0.95)', backdropFilter: 'blur(8px)',
            border: 'none', borderRadius: '50%', width: 34, height: 34,
            cursor: 'pointer', display: 'flex', alignItems: 'center', justifyContent: 'center',
            fontSize: 16, transition: 'transform 0.15s',
            transform: liked ? 'scale(1.2)' : 'scale(1)',
          }}>
          {liked ? '❤️' : '🤍'}
        </button>
      </div>
      <div style={{ padding: '18px 20px 20px' }}>
        <div style={{ fontSize: 12, fontWeight: 700, color: C.accent, marginBottom: 6, letterSpacing: '0.04em' }}>{event.date}</div>
        <div style={{ fontSize: 17, fontWeight: 700, color: C.text, marginBottom: 8, lineHeight: 1.3 }}>{event.title}</div>
        <div style={{ fontSize: 13, color: C.muted, marginBottom: 14 }}>📍 {event.location}</div>
        <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
          <span style={{ fontSize: 16, fontWeight: 800, color: C.text }}>{event.price}</span>
          <button style={{
            background: C.dark, color: '#fff', border: 'none', borderRadius: 100,
            padding: '8px 16px', fontSize: 13, fontWeight: 600, cursor: 'pointer',
            display: 'flex', alignItems: 'center', gap: 4,
          }}>
            Book →
          </button>
        </div>
      </div>
    </div>
  )
}

/* ─── TRENDING EVENTS ────────────────────────────────────────── */
function TrendingEvents() {
  return (
    <section style={{ padding: '100px 40px', background: C.bg }}>
      <div style={{ maxWidth: 1240, margin: '0 auto' }}>
        <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', marginBottom: 48, flexWrap: 'wrap', gap: 16 }}>
          <h2 style={{ fontSize: 'clamp(34px, 4vw, 48px)', fontWeight: 800, letterSpacing: '-0.03em', color: C.text, margin: 0 }}>
            Trending right now
          </h2>
          <a href="#" style={{ fontSize: 15, fontWeight: 600, color: C.text, textDecoration: 'none', display: 'flex', alignItems: 'center', gap: 4 }}>
            View all →
          </a>
        </div>
        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(4, 1fr)', gap: 24 }} className="events-grid">
          {EVENTS.map(ev => <EventCard key={ev.id} event={ev} />)}
        </div>
      </div>
    </section>
  )
}

/* ─── FEATURED EVENT ─────────────────────────────────────────── */
function FeaturedEvent() {
  const [hovered, setHovered] = useState(false)
  return (
    <section style={{ position: 'relative', height: 600, overflow: 'hidden' }}
      onMouseEnter={() => setHovered(true)}
      onMouseLeave={() => setHovered(false)}>
      <img
        src={IMGS.featured}
        alt="Future Music Festival"
        style={{
          width: '100%', height: '100%', objectFit: 'cover', display: 'block',
          transition: 'transform 0.8s',
          transform: hovered ? 'scale(1.03)' : 'scale(1)',
        }}
      />
      <div style={{
        position: 'absolute', inset: 0,
        background: 'linear-gradient(to right, rgba(0,0,0,0.78) 0%, rgba(0,0,0,0.3) 60%, transparent 100%)',
      }} />
      <div style={{
        position: 'absolute', inset: 0, display: 'flex', alignItems: 'center',
        padding: '0 clamp(20px, 5vw, 80px)',
      }}>
        <div style={{ maxWidth: 600 }}>
          <div style={{ fontSize: 12, fontWeight: 700, letterSpacing: '0.14em', color: 'rgba(255,255,255,0.6)', marginBottom: 20 }}>
            FEATURED EXPERIENCE
          </div>
          <h2 style={{ fontSize: 'clamp(36px, 5vw, 64px)', fontWeight: 800, letterSpacing: '-0.03em', color: '#fff', lineHeight: 1.06, margin: '0 0 20px' }}>
            Future Music Festival
          </h2>
          <div style={{ display: 'flex', gap: 24, flexWrap: 'wrap', marginBottom: 12 }}>
            <span style={{ fontSize: 16, color: 'rgba(255,255,255,0.8)' }}>August 24–26, 2026</span>
            <span style={{ color: 'rgba(255,255,255,0.4)' }}>·</span>
            <span style={{ fontSize: 16, color: 'rgba(255,255,255,0.8)' }}>Dhaka, Bangladesh</span>
          </div>
          <div style={{ fontSize: 14, color: 'rgba(255,255,255,0.6)', marginBottom: 36 }}>20,000+ attendees expected</div>
          <button style={{
            background: '#fff', color: C.text, border: 'none', borderRadius: 100,
            padding: '16px 36px', fontSize: 16, fontWeight: 700, cursor: 'pointer',
            transition: 'transform 0.15s, box-shadow 0.15s',
          }}
            onMouseEnter={e => { e.currentTarget.style.transform = 'translateY(-2px)'; e.currentTarget.style.boxShadow = '0 12px 32px rgba(0,0,0,0.3)' }}
            onMouseLeave={e => { e.currentTarget.style.transform = 'translateY(0)'; e.currentTarget.style.boxShadow = 'none' }}>
            Explore Event →
          </button>
        </div>
      </div>
    </section>
  )
}

/* ─── CHOOSE YOUR VIBE ───────────────────────────────────────── */
function ChooseVibe() {
  const vibes = ['Music', 'Learn', 'Business', 'Creative', 'Sports', 'Family', 'Food', 'Travel']
  const [active, setActive] = useState('Music')
  const filtered = EVENTS.filter(e => e.vibe.includes(active)).slice(0, 4)

  return (
    <section style={{ padding: '100px 40px', background: C.soft }}>
      <div style={{ maxWidth: 1240, margin: '0 auto' }}>
        <div style={{ textAlign: 'center', marginBottom: 48 }}>
          <h2 style={{ fontSize: 'clamp(34px, 4vw, 48px)', fontWeight: 800, letterSpacing: '-0.03em', color: C.text, margin: '0 0 14px' }}>
            Choose your vibe
          </h2>
          <p style={{ fontSize: 17, color: C.muted, margin: 0 }}>Find an experience that matches your mood.</p>
        </div>

        <div style={{ display: 'flex', gap: 10, justifyContent: 'center', flexWrap: 'wrap', marginBottom: 48 }}>
          {vibes.map(v => (
            <button
              key={v}
              onClick={() => setActive(v)}
              style={{
                border: 'none', borderRadius: 100, padding: '11px 22px',
                fontSize: 14, fontWeight: 600, cursor: 'pointer',
                transition: 'all 0.18s',
                background: active === v ? C.dark : '#fff',
                color: active === v ? '#fff' : C.text,
                boxShadow: active === v ? 'none' : `0 0 0 1px ${C.border}`,
              }}
            >
              {v}
            </button>
          ))}
        </div>

        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(4, 1fr)', gap: 24 }} className="events-grid">
          {filtered.map(ev => <EventCard key={ev.id} event={ev} />)}
        </div>
      </div>
    </section>
  )
}

/* ─── EVENTS NEAR YOU ────────────────────────────────────────── */
function NearYou() {
  return (
    <section style={{ padding: '100px 40px', background: C.bg }}>
      <div style={{ maxWidth: 1240, margin: '0 auto' }}>
        <div style={{ display: 'flex', alignItems: 'flex-end', justifyContent: 'space-between', marginBottom: 48, flexWrap: 'wrap', gap: 16 }}>
          <div>
            <h2 style={{ fontSize: 'clamp(34px, 4vw, 48px)', fontWeight: 800, letterSpacing: '-0.03em', color: C.text, margin: '0 0 10px' }}>
              Events happening near you
            </h2>
            <div style={{ display: 'flex', alignItems: 'center', gap: 12, flexWrap: 'wrap' }}>
              <span style={{ fontSize: 16, color: C.muted }}>📍 Dhaka</span>
              <button style={{ background: 'none', border: `1px solid ${C.border}`, borderRadius: 100, padding: '7px 16px', fontSize: 13, fontWeight: 600, cursor: 'pointer', color: C.text }}>
                Use my location
              </button>
            </div>
          </div>
          <a href="#" style={{ fontSize: 15, fontWeight: 600, color: C.text, textDecoration: 'none' }}>View on map →</a>
        </div>
        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(4, 1fr)', gap: 24 }} className="events-grid">
          {EVENTS.slice(0, 4).map(ev => <EventCard key={ev.id} event={ev} />)}
        </div>
      </div>
    </section>
  )
}

/* ─── EVENT CALENDAR ─────────────────────────────────────────── */
function EventCalendar() {
  const [selected, setSelected] = useState<number | null>(24)
  const daysInMonth = 31
  const firstDay = 6 // Aug 2026 starts on Saturday (index 6)
  const days = Array.from({ length: daysInMonth }, (_, i) => i + 1)
  const blanks = Array.from({ length: firstDay }, (_, i) => i)

  return (
    <section style={{ padding: '100px 40px', background: C.soft }}>
      <div style={{ maxWidth: 1240, margin: '0 auto' }}>
        <h2 style={{ fontSize: 'clamp(34px, 4vw, 48px)', fontWeight: 800, letterSpacing: '-0.03em', color: C.text, margin: '0 0 48px', textAlign: 'center' }}>
          What's happening this month
        </h2>

        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 40, alignItems: 'start' }} className="cal-grid">
          {/* Calendar */}
          <div style={{ background: '#fff', borderRadius: 24, border: `1px solid ${C.border}`, padding: 32 }}>
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 28 }}>
              <h3 style={{ fontSize: 20, fontWeight: 700, color: C.text, margin: 0 }}>August 2026</h3>
              <div style={{ display: 'flex', gap: 8 }}>
                {['←', '→'].map(a => (
                  <button key={a} style={{ background: C.soft, border: 'none', borderRadius: 10, width: 36, height: 36, cursor: 'pointer', fontSize: 15 }}>{a}</button>
                ))}
              </div>
            </div>
            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(7, 1fr)', gap: 4, marginBottom: 8 }}>
              {['S', 'M', 'T', 'W', 'T', 'F', 'S'].map((d, i) => (
                <div key={i} style={{ fontSize: 12, fontWeight: 700, color: C.muted, textAlign: 'center', padding: '4px 0', letterSpacing: '0.04em' }}>{d}</div>
              ))}
            </div>
            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(7, 1fr)', gap: 4 }}>
              {blanks.map(b => <div key={`b${b}`} />)}
              {days.map(d => {
                const hasEvent = !!CALENDAR_EVENTS[d]
                const isSelected = selected === d
                return (
                  <button
                    key={d}
                    onClick={() => setSelected(d === selected ? null : d)}
                    style={{
                      border: 'none', borderRadius: 10, aspectRatio: '1',
                      cursor: 'pointer', display: 'flex', flexDirection: 'column',
                      alignItems: 'center', justifyContent: 'center', gap: 3,
                      fontSize: 14, fontWeight: isSelected ? 700 : 500,
                      background: isSelected ? C.dark : 'transparent',
                      color: isSelected ? '#fff' : C.text,
                      transition: 'all 0.15s',
                    }}
                    onMouseEnter={e => { if (!isSelected) e.currentTarget.style.background = C.soft }}
                    onMouseLeave={e => { if (!isSelected) e.currentTarget.style.background = 'transparent' }}
                  >
                    {d}
                    {hasEvent && (
                      <div style={{ width: 5, height: 5, borderRadius: '50%', background: isSelected ? 'rgba(255,255,255,0.7)' : C.accent }} />
                    )}
                  </button>
                )
              })}
            </div>
          </div>

          {/* Event list */}
          <div>
            {selected && CALENDAR_EVENTS[selected] ? (
              <>
                <div style={{ fontSize: 14, fontWeight: 700, letterSpacing: '0.06em', color: C.muted, marginBottom: 20 }}>
                  AUGUST {selected}
                </div>
                <div style={{ display: 'flex', flexDirection: 'column', gap: 16 }}>
                  {CALENDAR_EVENTS[selected].map((ev, i) => (
                    <div key={i} style={{
                      background: '#fff', borderRadius: 18, border: `1px solid ${C.border}`,
                      padding: '20px 24px', display: 'flex', justifyContent: 'space-between', alignItems: 'center',
                    }}>
                      <div>
                        <div style={{ fontSize: 16, fontWeight: 700, color: C.text, marginBottom: 4 }}>{ev.title}</div>
                        <div style={{ fontSize: 13, color: C.muted }}>{ev.location} · {ev.price}</div>
                      </div>
                      <button style={{
                        background: C.dark, color: '#fff', border: 'none', borderRadius: 100,
                        padding: '9px 18px', fontSize: 13, fontWeight: 600, cursor: 'pointer',
                      }}>
                        Book →
                      </button>
                    </div>
                  ))}
                </div>
              </>
            ) : (
              <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'center', height: '100%', color: C.muted, fontSize: 15 }}>
                Select a date to see events
              </div>
            )}
          </div>
        </div>
      </div>
    </section>
  )
}

/* ─── HOW IT WORKS ───────────────────────────────────────────── */
function HowItWorks() {
  return (
    <section style={{ padding: '100px 40px', background: C.bg }}>
      <div style={{ maxWidth: 1240, margin: '0 auto' }}>
        <div style={{ textAlign: 'center', marginBottom: 72 }}>
          <h2 style={{ fontSize: 'clamp(34px, 4vw, 48px)', fontWeight: 800, letterSpacing: '-0.03em', color: C.text, margin: 0 }}>
            Book experiences in three simple steps.
          </h2>
        </div>

        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: 0, position: 'relative' }} className="steps-grid">
          {/* Line */}
          <div style={{
            position: 'absolute', top: 36, left: '16%', right: '16%', height: 1,
            background: `linear-gradient(to right, ${C.border}, ${C.border})`,
          }} className="steps-line" />

          {[
            { num: '01', label: 'DISCOVER', desc: 'Find an event you love from thousands of curated experiences worldwide.' },
            { num: '02', label: 'BOOK', desc: 'Choose your ticket and pay securely. Get instant confirmation.' },
            { num: '03', label: 'ENJOY', desc: 'Receive your digital ticket and enjoy the event worry-free.' },
          ].map(step => (
            <div key={step.num} style={{ textAlign: 'center', padding: '0 32px' }}>
              <div style={{
                width: 72, height: 72, borderRadius: '50%', border: `1px solid ${C.border}`,
                display: 'flex', alignItems: 'center', justifyContent: 'center',
                margin: '0 auto 28px', background: '#fff', position: 'relative', zIndex: 1,
              }}>
                <span style={{ fontSize: 22, fontWeight: 800, color: C.text, letterSpacing: '-0.04em' }}>{step.num}</span>
              </div>
              <div style={{ fontSize: 12, fontWeight: 700, letterSpacing: '0.12em', color: C.accent, marginBottom: 12 }}>{step.label}</div>
              <p style={{ fontSize: 16, lineHeight: 1.6, color: C.muted, margin: 0 }}>{step.desc}</p>
            </div>
          ))}
        </div>
      </div>
    </section>
  )
}

/* ─── DIGITAL TICKET ─────────────────────────────────────────── */
function DigitalTicket() {
  return (
    <section style={{ padding: '100px 40px', background: C.soft }}>
      <div style={{ maxWidth: 1240, margin: '0 auto', display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 80, alignItems: 'center' }} className="ticket-grid">
        <div>
          <h2 style={{ fontSize: 'clamp(34px, 4vw, 52px)', fontWeight: 800, letterSpacing: '-0.03em', color: C.text, lineHeight: 1.1, margin: '0 0 24px' }}>
            Your ticket.<br />Your experience.
          </h2>
          <p style={{ fontSize: 17, lineHeight: 1.65, color: C.muted, margin: '0 0 36px', maxWidth: 400 }}>
            Everything you need for your next event, right in one beautiful digital ticket.
          </p>
          <ArrowButton variant="primary" href="#">View My Tickets</ArrowButton>
        </div>

        {/* Ticket UI */}
        <div style={{ position: 'relative', display: 'flex', justifyContent: 'center' }}>
          <div style={{
            background: C.dark, borderRadius: 28, padding: '36px 32px',
            width: '100%', maxWidth: 360, position: 'relative', overflow: 'hidden',
            boxShadow: '0 40px 80px rgba(0,0,0,0.2)',
          }}>
            {/* Decoration circles */}
            <div style={{ position: 'absolute', top: -40, right: -40, width: 160, height: 160, borderRadius: '50%', background: `${C.accent}20` }} />
            <div style={{ position: 'absolute', bottom: -30, left: -30, width: 120, height: 120, borderRadius: '50%', background: `${C.orange}15` }} />

            <div style={{ position: 'relative' }}>
              <div style={{ fontSize: 11, fontWeight: 700, letterSpacing: '0.14em', color: C.accent, marginBottom: 20 }}>EVENTLY</div>
              <div style={{ fontSize: 12, fontWeight: 700, letterSpacing: '0.1em', color: 'rgba(255,255,255,0.5)', marginBottom: 8 }}>SUMMER MUSIC FESTIVAL</div>
              <div style={{ fontSize: 28, fontWeight: 800, color: '#fff', letterSpacing: '-0.02em', marginBottom: 4 }}>AUG 24 · 2026</div>
              <div style={{ fontSize: 15, color: 'rgba(255,255,255,0.6)', marginBottom: 28 }}>Dhaka, Bangladesh</div>

              <div style={{ height: 1, background: 'rgba(255,255,255,0.1)', marginBottom: 24, position: 'relative' }}>
                <div style={{ position: 'absolute', left: -32, top: -10, width: 20, height: 20, borderRadius: '50%', background: C.soft }} />
                <div style={{ position: 'absolute', right: -32, top: -10, width: 20, height: 20, borderRadius: '50%', background: C.soft }} />
              </div>

              <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-end' }}>
                <div>
                  <div style={{ fontSize: 11, color: 'rgba(255,255,255,0.4)', marginBottom: 4, letterSpacing: '0.08em' }}>TICKET TYPE</div>
                  <div style={{ fontSize: 18, fontWeight: 800, color: '#fff' }}>VIP PASS</div>
                  <div style={{ fontSize: 13, color: 'rgba(255,255,255,0.5)', marginTop: 4 }}>ENTRY 06:30 PM</div>
                </div>
                {/* QR Code */}
                <div style={{ background: '#fff', borderRadius: 12, padding: 10 }}>
                  <div style={{ width: 72, height: 72, display: 'grid', gridTemplateColumns: 'repeat(7, 1fr)', gap: 1 }}>
                    {Array.from({ length: 49 }).map((_, i) => (
                      <div key={i} style={{ background: [0,1,2,7,8,9,14,6,13,20,21,28,35,42,43,44,48,47,46,41,34,27,24,25,26,17,10,11,16,23,30,37,38,31,32,33].includes(i) ? '#111' : 'transparent', borderRadius: 1 }} />
                    ))}
                  </div>
                </div>
              </div>

              <div style={{ marginTop: 20, padding: '10px 14px', background: 'rgba(255,255,255,0.06)', borderRadius: 10, fontSize: 12, color: 'rgba(255,255,255,0.4)', letterSpacing: '0.06em' }}>
                #EVT-82931
              </div>
            </div>
          </div>

          {/* Second ticket peek */}
          <div style={{
            position: 'absolute', bottom: -20, right: -20, zIndex: -1,
            background: C.accent, borderRadius: 28, width: 340, height: '90%',
            opacity: 0.3,
          }} />
        </div>
      </div>
    </section>
  )
}

/* ─── ORGANIZER SECTION ──────────────────────────────────────── */
function OrganizerSection() {
  return (
    <section style={{ background: C.dark, padding: '100px 40px' }}>
      <div style={{ maxWidth: 1240, margin: '0 auto', display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 80, alignItems: 'center' }} className="org-grid">
        <div>
          <span style={{ fontSize: 12, fontWeight: 700, letterSpacing: '0.12em', color: C.accent, display: 'block', marginBottom: 24 }}>FOR ORGANIZERS</span>
          <h2 style={{ fontSize: 'clamp(34px, 4vw, 52px)', fontWeight: 800, letterSpacing: '-0.03em', color: '#fff', lineHeight: 1.1, margin: '0 0 24px' }}>
            Turn your event into an experience.
          </h2>
          <p style={{ fontSize: 17, lineHeight: 1.65, color: 'rgba(255,255,255,0.5)', margin: '0 0 40px', maxWidth: 420 }}>
            Create events, sell tickets, manage attendees and track your performance from one powerful dashboard.
          </p>
          <div style={{ display: 'flex', gap: 12, flexWrap: 'wrap' }}>
            <button style={{
              background: '#fff', color: C.dark, border: 'none', borderRadius: 100,
              padding: '14px 30px', fontSize: 15, fontWeight: 700, cursor: 'pointer',
              transition: 'opacity 0.15s',
            }}
              onMouseEnter={e => (e.currentTarget.style.opacity = '0.9')}
              onMouseLeave={e => (e.currentTarget.style.opacity = '1')}>
              Start Selling →
            </button>
            <button style={{
              background: 'transparent', color: 'rgba(255,255,255,0.7)', border: '1px solid rgba(255,255,255,0.2)',
              borderRadius: 100, padding: '14px 30px', fontSize: 15, fontWeight: 600, cursor: 'pointer',
            }}>
              Explore Organizer Tools
            </button>
          </div>
        </div>

        {/* Dashboard mockup */}
        <div style={{ background: 'rgba(255,255,255,0.05)', borderRadius: 24, border: '1px solid rgba(255,255,255,0.1)', padding: 28 }}>
          <div style={{ fontSize: 13, fontWeight: 700, color: 'rgba(255,255,255,0.5)', marginBottom: 24, letterSpacing: '0.04em' }}>Dashboard Overview</div>

          <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16, marginBottom: 24 }}>
            {[
              { label: 'Revenue', value: '$48,290', change: '+12.4%' },
              { label: 'Tickets Sold', value: '2,840', change: '+8.1%' },
              { label: 'Upcoming Events', value: '24', change: '→' },
              { label: 'Conversion', value: '8.42%', change: '+0.3%' },
            ].map(stat => (
              <div key={stat.label} style={{ background: 'rgba(255,255,255,0.05)', borderRadius: 16, padding: '18px 20px' }}>
                <div style={{ fontSize: 12, color: 'rgba(255,255,255,0.4)', marginBottom: 8 }}>{stat.label}</div>
                <div style={{ fontSize: 22, fontWeight: 800, color: '#fff', letterSpacing: '-0.02em', marginBottom: 4 }}>{stat.value}</div>
                <div style={{ fontSize: 12, fontWeight: 600, color: '#22C55E' }}>{stat.change}</div>
              </div>
            ))}
          </div>

          {/* Mini chart */}
          <div style={{ background: 'rgba(255,255,255,0.04)', borderRadius: 16, padding: '16px 20px' }}>
            <div style={{ fontSize: 12, color: 'rgba(255,255,255,0.4)', marginBottom: 12 }}>Ticket Sales — Aug 2026</div>
            <svg viewBox="0 0 280 80" style={{ width: '100%', height: 80 }}>
              <defs>
                <linearGradient id="chartGrad" x1="0" x2="0" y1="0" y2="1">
                  <stop offset="0%" stopColor={C.accent} stopOpacity="0.4" />
                  <stop offset="100%" stopColor={C.accent} stopOpacity="0" />
                </linearGradient>
              </defs>
              <path d="M0,60 L40,45 L80,52 L120,30 L160,38 L200,15 L240,22 L280,8 L280,80 L0,80 Z" fill="url(#chartGrad)" />
              <path d="M0,60 L40,45 L80,52 L120,30 L160,38 L200,15 L240,22 L280,8" fill="none" stroke={C.accent} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
            </svg>
          </div>
        </div>
      </div>
    </section>
  )
}

/* ─── STATS ──────────────────────────────────────────────────── */
function Stats() {
  const stats = [
    { value: '10K+', label: 'Events' },
    { value: '250K+', label: 'Tickets Sold' },
    { value: '98%', label: 'Customer Satisfaction' },
    { value: '50+', label: 'Cities' },
  ]
  return (
    <section style={{ padding: '100px 40px', background: C.bg }}>
      <div style={{ maxWidth: 1240, margin: '0 auto', display: 'grid', gridTemplateColumns: 'repeat(4, 1fr)', gap: 40, textAlign: 'center' }} className="stats-grid">
        {stats.map(s => (
          <div key={s.label}>
            <div style={{ fontSize: 'clamp(40px, 5vw, 64px)', fontWeight: 800, letterSpacing: '-0.04em', color: C.text, lineHeight: 1 }}>{s.value}</div>
            <div style={{ fontSize: 15, color: C.muted, marginTop: 12, fontWeight: 500 }}>{s.label}</div>
          </div>
        ))}
      </div>
    </section>
  )
}

/* ─── TESTIMONIALS ───────────────────────────────────────────── */
function Testimonials() {
  const testimonials = [
    { stars: 5, text: "Everything from discovering the event to receiving my ticket felt effortless. This is how event booking should work.", name: 'Sarah Williams', role: 'Event Organizer', initials: 'SW', color: '#6C5CE7' },
    { stars: 5, text: "I've tried every event app out there. Evently is the only one that actually feels premium. The ticket experience is beautiful.", name: 'Marcus Chen', role: 'Festival Goer', initials: 'MC', color: '#FF7657' },
    { stars: 5, text: "Sold out our 500-person conference in 48 hours. The organizer dashboard made it simple to track everything in real time.", name: 'Priya Nair', role: 'Conference Director', initials: 'PN', color: '#22C55E' },
  ]

  return (
    <section style={{ padding: '100px 40px', background: C.soft }}>
      <div style={{ maxWidth: 1240, margin: '0 auto' }}>
        <h2 style={{ fontSize: 'clamp(34px, 4vw, 48px)', fontWeight: 800, letterSpacing: '-0.03em', color: C.text, margin: '0 0 56px', textAlign: 'center', maxWidth: 600, marginLeft: 'auto', marginRight: 'auto' }}>
          Loved by people who love great experiences.
        </h2>
        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: 24 }} className="testi-grid">
          {testimonials.map((t, i) => (
            <div key={i} style={{ background: '#fff', borderRadius: 24, border: `1px solid ${C.border}`, padding: '32px 28px' }}>
              <div style={{ fontSize: 18, color: '#F59E0B', marginBottom: 20, letterSpacing: 2 }}>{'★'.repeat(t.stars)}</div>
              <p style={{ fontSize: 16, lineHeight: 1.65, color: C.text, margin: '0 0 28px' }}>"{t.text}"</p>
              <div style={{ display: 'flex', alignItems: 'center', gap: 14 }}>
                <div style={{ width: 44, height: 44, borderRadius: '50%', background: t.color, display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: 14, fontWeight: 700, color: '#fff' }}>
                  {t.initials}
                </div>
                <div>
                  <div style={{ fontSize: 15, fontWeight: 700, color: C.text }}>{t.name}</div>
                  <div style={{ fontSize: 13, color: C.muted }}>{t.role}</div>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  )
}

/* ─── EVENT JOURNAL ──────────────────────────────────────────── */
function EventJournal() {
  const articles = [
    { img: IMGS.journal1, cat: 'EVENTS', title: 'How to plan an unforgettable event', date: 'Aug 2, 2026' },
    { img: IMGS.journal2, cat: 'FESTIVALS', title: '5 things to know before buying festival tickets', date: 'Jul 28, 2026' },
    { img: IMGS.journal3, cat: 'CULTURE', title: 'The future of live events', date: 'Jul 15, 2026' },
  ]

  return (
    <section style={{ padding: '100px 40px', background: C.bg }}>
      <div style={{ maxWidth: 1240, margin: '0 auto' }}>
        <div style={{ display: 'flex', alignItems: 'flex-end', justifyContent: 'space-between', marginBottom: 48, flexWrap: 'wrap', gap: 16 }}>
          <div>
            <h2 style={{ fontSize: 'clamp(34px, 4vw, 48px)', fontWeight: 800, letterSpacing: '-0.03em', color: C.text, margin: '0 0 10px' }}>
              Event Journal
            </h2>
            <p style={{ fontSize: 17, color: C.muted, margin: 0 }}>Ideas, inspiration and stories from the world of events.</p>
          </div>
        </div>

        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: 28 }} className="journal-grid">
          {articles.map((a, i) => {
            const [hov, setHov] = useState(false)
            return (
              <div key={i} style={{ cursor: 'pointer' }}
                onMouseEnter={() => setHov(true)}
                onMouseLeave={() => setHov(false)}>
                <div style={{ borderRadius: 20, overflow: 'hidden', marginBottom: 24, height: 280, background: C.soft }}>
                  <img src={a.img} alt={a.title} style={{ width: '100%', height: '100%', objectFit: 'cover', transition: 'transform 0.5s', transform: hov ? 'scale(1.04)' : 'scale(1)' }} />
                </div>
                <div style={{ fontSize: 11, fontWeight: 700, letterSpacing: '0.1em', color: C.accent, marginBottom: 10 }}>{a.cat}</div>
                <h3 style={{ fontSize: 20, fontWeight: 700, color: C.text, lineHeight: 1.35, margin: '0 0 14px' }}>{a.title}</h3>
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                  <span style={{ fontSize: 13, color: C.muted }}>{a.date}</span>
                  <span style={{ fontSize: 14, fontWeight: 600, color: C.text, transition: 'gap 0.15s', display: 'flex', alignItems: 'center', gap: hov ? 8 : 4 }}>
                    Read article →
                  </span>
                </div>
              </div>
            )
          })}
        </div>
      </div>
    </section>
  )
}

/* ─── FINAL CTA ──────────────────────────────────────────────── */
function FinalCTA() {
  return (
    <section style={{ padding: '120px 40px', background: C.dark, textAlign: 'center', position: 'relative', overflow: 'hidden' }}>
      {/* Background decoration */}
      <div style={{ position: 'absolute', top: '50%', left: '50%', transform: 'translate(-50%, -50%)', width: 600, height: 600, borderRadius: '50%', background: `${C.accent}10`, filter: 'blur(80px)', pointerEvents: 'none' }} />

      <div style={{ position: 'relative', maxWidth: 800, margin: '0 auto' }}>
        <h2 style={{ fontSize: 'clamp(40px, 6vw, 80px)', fontWeight: 800, letterSpacing: '-0.04em', color: '#fff', lineHeight: 1.05, margin: '0 0 24px' }}>
          Your next great experience is waiting.
        </h2>
        <p style={{ fontSize: 19, lineHeight: 1.6, color: 'rgba(255,255,255,0.5)', margin: '0 0 48px' }}>
          Discover thousands of events, experiences and unforgettable moments.
        </p>
        <button style={{
          background: '#fff', color: C.dark, border: 'none', borderRadius: 100,
          padding: '18px 44px', fontSize: 17, fontWeight: 700, cursor: 'pointer',
          transition: 'transform 0.15s, box-shadow 0.15s',
          display: 'inline-block',
        }}
          onMouseEnter={e => { e.currentTarget.style.transform = 'translateY(-3px)'; e.currentTarget.style.boxShadow = '0 20px 48px rgba(0,0,0,0.4)' }}
          onMouseLeave={e => { e.currentTarget.style.transform = 'translateY(0)'; e.currentTarget.style.boxShadow = 'none' }}>
          Explore Events →
        </button>
      </div>
    </section>
  )
}

/* ─── FOOTER ─────────────────────────────────────────────────── */
function Footer() {
  const cols = [
    { title: 'Explore', links: ['Events', 'Categories', 'Venues', 'Organizers'] },
    { title: 'Company', links: ['About', 'Contact', 'Blog', 'Careers'] },
    { title: 'Support', links: ['Help Center', 'FAQ', 'Terms', 'Privacy'] },
  ]

  return (
    <footer style={{ background: '#060608', padding: '72px 40px 40px', borderTop: '1px solid rgba(255,255,255,0.06)' }}>
      <div style={{ maxWidth: 1240, margin: '0 auto' }}>
        <div style={{ display: 'grid', gridTemplateColumns: '2fr 1fr 1fr 1fr', gap: 48, marginBottom: 64 }} className="footer-grid">
          <div>
            <div style={{ fontSize: 22, fontWeight: 800, letterSpacing: '-0.04em', color: '#fff', marginBottom: 14 }}>EVENTLY</div>
            <p style={{ fontSize: 15, lineHeight: 1.65, color: 'rgba(255,255,255,0.4)', margin: '0 0 28px', maxWidth: 240 }}>
              Discover experiences.<br />Create memories.
            </p>
            {/* Social icons */}
            <div style={{ display: 'flex', gap: 12 }}>
              {['IG', 'FB', 'X', 'YT'].map(s => (
                <a key={s} href="#" style={{
                  width: 38, height: 38, borderRadius: '50%',
                  background: 'rgba(255,255,255,0.07)', border: '1px solid rgba(255,255,255,0.1)',
                  display: 'flex', alignItems: 'center', justifyContent: 'center',
                  fontSize: 11, fontWeight: 700, color: 'rgba(255,255,255,0.5)',
                  textDecoration: 'none', transition: 'background 0.15s',
                }}
                  onMouseEnter={e => (e.currentTarget.style.background = 'rgba(255,255,255,0.12)')}
                  onMouseLeave={e => (e.currentTarget.style.background = 'rgba(255,255,255,0.07)')}>
                  {s}
                </a>
              ))}
            </div>
          </div>

          {cols.map(col => (
            <div key={col.title}>
              <div style={{ fontSize: 12, fontWeight: 700, letterSpacing: '0.1em', color: 'rgba(255,255,255,0.4)', marginBottom: 20 }}>{col.title.toUpperCase()}</div>
              <div style={{ display: 'flex', flexDirection: 'column', gap: 12 }}>
                {col.links.map(link => (
                  <a key={link} href="#" style={{ fontSize: 15, color: 'rgba(255,255,255,0.55)', textDecoration: 'none', transition: 'color 0.15s' }}
                    onMouseEnter={e => (e.currentTarget.style.color = '#fff')}
                    onMouseLeave={e => (e.currentTarget.style.color = 'rgba(255,255,255,0.55)')}>
                    {link}
                  </a>
                ))}
              </div>
            </div>
          ))}
        </div>

        <div style={{ borderTop: '1px solid rgba(255,255,255,0.08)', paddingTop: 28, display: 'flex', justifyContent: 'space-between', alignItems: 'center', flexWrap: 'wrap', gap: 16 }}>
          <span style={{ fontSize: 13, color: 'rgba(255,255,255,0.3)' }}>© 2026 Evently. All rights reserved.</span>
          <div style={{ display: 'flex', gap: 24 }}>
            {['Privacy', 'Terms'].map(l => (
              <a key={l} href="#" style={{ fontSize: 13, color: 'rgba(255,255,255,0.3)', textDecoration: 'none', transition: 'color 0.15s' }}
                onMouseEnter={e => (e.currentTarget.style.color = 'rgba(255,255,255,0.6)')}
                onMouseLeave={e => (e.currentTarget.style.color = 'rgba(255,255,255,0.3)')}>
                {l}
              </a>
            ))}
          </div>
        </div>
      </div>
    </footer>
  )
}

/* ─── ARROW BUTTON ───────────────────────────────────────────── */
function ArrowButton({ variant, href, children }: { variant: 'primary' | 'secondary'; href: string; children: React.ReactNode }) {
  const [hov, setHov] = useState(false)
  const isPrimary = variant === 'primary'
  return (
    <a
      href={href}
      onMouseEnter={() => setHov(true)}
      onMouseLeave={() => setHov(false)}
      style={{
        display: 'inline-flex', alignItems: 'center', gap: 6,
        padding: '14px 28px', borderRadius: 100, fontSize: 15, fontWeight: 700,
        textDecoration: 'none', transition: 'all 0.18s', cursor: 'pointer',
        background: isPrimary ? C.dark : 'transparent',
        color: isPrimary ? '#fff' : C.text,
        border: isPrimary ? 'none' : `1.5px solid ${C.border}`,
        boxShadow: isPrimary && hov ? '0 8px 24px rgba(0,0,0,0.18)' : 'none',
        transform: hov ? 'translateY(-1px)' : 'translateY(0)',
      }}
    >
      {children}
      <span style={{ transition: 'transform 0.18s', transform: hov ? 'translateX(4px)' : 'translateX(0)', display: 'inline-block' }}>→</span>
    </a>
  )
}

/* ─── RESPONSIVE STYLES ──────────────────────────────────────── */
const responsiveCSS = `
  .hidden-mobile { display: flex !important; }
  .show-mobile { display: none !important; }
  .search-grid { display: grid !important; }
  .search-mobile { display: none !important; }

  @media (max-width: 1000px) {
    .hero-grid { grid-template-columns: 1fr !important; gap: 40px !important; }
    .cat-grid { grid-template-columns: repeat(2, 1fr) !important; grid-template-rows: auto !important; }
    .cat-grid > div:first-child { grid-column: span 1 !important; }
    .events-grid { grid-template-columns: repeat(2, 1fr) !important; }
    .cal-grid { grid-template-columns: 1fr !important; }
    .steps-grid { grid-template-columns: 1fr !important; gap: 40px !important; }
    .steps-line { display: none !important; }
    .ticket-grid { grid-template-columns: 1fr !important; gap: 48px !important; }
    .org-grid { grid-template-columns: 1fr !important; gap: 48px !important; }
    .stats-grid { grid-template-columns: repeat(2, 1fr) !important; }
    .testi-grid { grid-template-columns: 1fr !important; gap: 20px !important; }
    .journal-grid { grid-template-columns: 1fr !important; gap: 48px !important; }
    .footer-grid { grid-template-columns: 1fr 1fr !important; gap: 36px !important; }
  }

  @media (max-width: 640px) {
    .hidden-mobile { display: none !important; }
    .show-mobile { display: flex !important; }
    .search-grid { display: none !important; }
    .search-mobile { display: flex !important; }
    .events-grid { grid-template-columns: 1fr !important; }
    .stats-grid { grid-template-columns: repeat(2, 1fr) !important; }
    .footer-grid { grid-template-columns: 1fr !important; }
  }
`

/* ─── APP ────────────────────────────────────────────────────── */
export default function App() {
  return (
    <>
      <style>{responsiveCSS}</style>
      <div style={{ fontFamily: "'Plus Jakarta Sans', sans-serif", background: C.bg }}>
        <Header />
        <Hero />
        <Categories />
        <TrendingEvents />
        <FeaturedEvent />
        <ChooseVibe />
        <NearYou />
        <EventCalendar />
        <HowItWorks />
        <DigitalTicket />
        <OrganizerSection />
        <Stats />
        <Testimonials />
        <EventJournal />
        <FinalCTA />
        <Footer />
      </div>
    </>
  )
}

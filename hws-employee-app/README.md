# Himalayan Water Science — Employee App

A field-service mobile web app for Himalayan Water Science technicians, built with **Next.js (App Router)**, **TypeScript**, and **Tailwind CSS**. Converted from the `himalayan-water-science-ui-prototype.html` design prototype into a real, running application.

## Getting started

```bash
npm install
npm run dev
```

Open http://localhost:3000 — you'll land on the login screen. Any email/password gets you in (there's no backend yet — see below).

## Screens

| Route | Screen |
|---|---|
| `/` | Login |
| `/home` | Dashboard — greeting, attendance status, task stats, today's tasks |
| `/attendance` | Check in / check out, location, weekly summary |
| `/tasks` | Full task list with search + status filters |
| `/tasks/[id]` | Task detail — stepper, work notes, before/after photos, signature, rating |
| `/survey` | Site survey / lead capture form |
| `/profile` | Technician profile, stats, logout |

## Project structure

```
app/                  Routes (App Router)
components/           Screen-level building blocks (TaskCard, Stepper, SignaturePad, ...)
components/ui/        Small design-system primitives (Button, Card, Badge, Pill, Field)
lib/data.ts           Sample task data + status/badge helpers
lib/store.tsx         Shared app state (tasks, attendance) via React Context
lib/types.ts          Shared TypeScript types
```

## Design decisions worth knowing about

This was converted from a design-tool prototype, so a few things were deliberately **not** carried over 1:1:

- **No phone-bezel frame or fake status bar.** The prototype rendered the app inside a mock iPhone graphic for presentation. A real deployed app doesn't need that — on an actual phone, the phone *is* the frame.
- **No "jump to any screen" picker.** The prototype had a numbered pill row ("1. Login, 2. Home...") for browsing screens out of order, Figma-flow style. That's a prototyping aid, not app UI, so it's gone — navigation now works the way the app actually flows (login → home → bottom nav).
- **Home screen stats are computed from real task data**, not hardcoded. The prototype's "5 Today / 3 Pending / 2 Done" numbers didn't actually match the task list shown below them; here they're derived from `lib/data.ts` so they're always consistent.
- **Login fields are empty with placeholders**, not pre-filled with a sample employee's credentials — more appropriate for a real login form.

## No backend yet

All data lives in `lib/data.ts` and `lib/store.tsx` as in-memory sample state — it resets on every page refresh, same as the original prototype. There's no persistence and no real authentication. Natural next steps, in rough order of value:

1. Replace `INITIAL_TASKS` in `lib/data.ts` with a real API call (e.g. fetched in a server component and passed down, or via a data-fetching library).
2. Wire the login form to a real auth endpoint.
3. Persist attendance check-in/out and task progress server-side instead of in React state.
4. The four profile menu rows (Attendance history, Leave requests, Expense claims, Settings) are currently visual-only, matching the prototype — they'll need real destinations.

## Tech notes

- Icons are from [lucide-react](https://lucide.dev), matched to the prototype's custom icon set by name (droplet, clock, map-pin, etc.) rather than carried over as a hand-rolled SVG sprite.
- "Space Grotesk" loads via `next/font/google` instead of the prototype's embedded base64 font files — same font, much smaller bundle.
- The topographic line pattern behind the login header (`components/ContourBackground.tsx`) is a faithful port of the prototype's generative SVG background.
